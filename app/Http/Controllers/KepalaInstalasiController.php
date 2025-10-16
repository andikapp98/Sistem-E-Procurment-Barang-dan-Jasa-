<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Controller untuk Kepala Instalasi
 * 
 * Tugas Kepala Instalasi:
 * 1. Menerima permintaan dari unit
 * 2. Mereview permintaan
 * 3. Membuat Nota Dinas untuk dikirim ke atasan (Kepala Instalasi / Direktur)
 * 4. Meneruskan ke bagian pengadaan
 */
class KepalaInstalasiController extends Controller
{
    /**
     * Dashboard Kepala Instalasi
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Hitung statistik - ambil semua permintaan dari unit kerja yang sama atau yang ditugaskan ke user ini
        $permintaans = Permintaan::with(['user', 'notaDinas'])
            ->where(function($query) use ($user) {
                if ($user->unit_kerja) {
                    $query->whereHas('user', function($q) use ($user) {
                        $q->where('unit_kerja', $user->unit_kerja);
                    })
                    ->orWhere('pic_pimpinan', $user->nama);
                } else {
                    // Jika tidak ada unit_kerja, ambil yang ditugaskan saja
                    $query->where('pic_pimpinan', $user->nama);
                }
            })
            ->get();

        $stats = [
            'total' => $permintaans->count(),
            'diajukan' => $permintaans->where('status', 'diajukan')->count(),
            'proses' => $permintaans->where('status', 'proses')->count(),
            'disetujui' => $permintaans->where('status', 'disetujui')->count(),
        ];

        // Ambil 5 permintaan terbaru
        $recentPermintaans = $permintaans
            ->sortByDesc('permintaan_id')
            ->take(5)
            ->map(function($permintaan) {
                $permintaan->tracking_status = $permintaan->trackingStatus;
                return $permintaan;
            })
            ->values();

        return Inertia::render('KepalaInstalasi/Dashboard', [
            'stats' => $stats,
            'recentPermintaans' => $recentPermintaans,
            'userLogin' => $user,
        ]);
    }

    /**
     * Tampilkan daftar permintaan yang perlu direview oleh Kepala Instalasi
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil permintaan yang berasal dari unit kerja yang sama dengan Kepala Instalasi
        // atau permintaan yang sudah dibuat nota dinas tapi masih perlu tindak lanjut
        $permintaans = Permintaan::with(['user', 'notaDinas'])
            ->where(function($query) use ($user) {
                if ($user->unit_kerja) {
                    $query->whereHas('user', function($q) use ($user) {
                        $q->where('unit_kerja', $user->unit_kerja);
                    })
                    ->orWhere('pic_pimpinan', $user->nama);
                } else {
                    // Jika tidak ada unit_kerja, ambil yang ditugaskan saja
                    $query->where('pic_pimpinan', $user->nama);
                }
            })
            ->orderByDesc('permintaan_id')
            ->get()
            ->map(function($permintaan) {
                // Tambahkan status tracking
                $permintaan->tracking_status = $permintaan->trackingStatus;
                return $permintaan;
            });

        return Inertia::render('KepalaInstalasi/Index', [
            'permintaans' => $permintaans,
            'userLogin' => $user,
        ]);
    }

    /**
     * Tampilkan detail permintaan untuk review
     */
    public function show(Permintaan $permintaan)
    {
        $permintaan->load(['user', 'notaDinas.disposisi']);
        
        return Inertia::render('KepalaInstalasi/Show', [
            'permintaan' => $permintaan,
            'trackingStatus' => $permintaan->trackingStatus,
        ]);
    }

    /**
     * Form untuk membuat Nota Dinas dari permintaan
     */
    public function createNotaDinas(Permintaan $permintaan)
    {
        $permintaan->load('user');
        
        return Inertia::render('KepalaInstalasi/CreateNotaDinas', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Store Nota Dinas
     */
    public function storeNotaDinas(Request $request, Permintaan $permintaan)
    {
        $data = $request->validate([
            'dari_unit' => 'required|string',
            'ke_jabatan' => 'required|string',
            'tanggal_nota' => 'required|date',
            'status' => 'nullable|string',
        ]);

        $data['permintaan_id'] = $permintaan->permintaan_id;
        
        // Set default status jika tidak ada
        if (!isset($data['status'])) {
            $data['status'] = 'proses';
        }

        $notaDinas = NotaDinas::create($data);

        // Update status permintaan
        $permintaan->update([
            'status' => 'proses',
            'pic_pimpinan' => $data['ke_jabatan'],
        ]);

        return redirect()
            ->route('kepala-instalasi.show', $permintaan)
            ->with('success', 'Nota Dinas berhasil dibuat dan dikirim ke ' . $data['ke_jabatan']);
    }

    /**
     * Approve permintaan (langsung tanpa nota dinas formal)
     */
    public function approve(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();

        // Update status permintaan
        $permintaan->update([
            'status' => 'disetujui',
            'pic_pimpinan' => $user->nama,
        ]);

        // Buat nota dinas otomatis
        NotaDinas::create([
            'permintaan_id' => $permintaan->permintaan_id,
            'dari_unit' => $user->unit_kerja ?? $user->jabatan,
            'ke_jabatan' => 'Bagian Pengadaan',
            'tanggal_nota' => Carbon::now(),
            'status' => 'disetujui',
        ]);

        return redirect()
            ->route('kepala-instalasi.index')
            ->with('success', 'Permintaan disetujui dan diteruskan ke Bagian Pengadaan');
    }

    /**
     * Reject permintaan
     */
    public function reject(Request $request, Permintaan $permintaan)
    {
        $data = $request->validate([
            'alasan' => 'required|string',
        ]);

        $user = Auth::user();

        // Update status permintaan
        $permintaan->update([
            'status' => 'ditolak',
            'deskripsi' => $permintaan->deskripsi . "\n\n[DITOLAK] " . $data['alasan'],
        ]);

        // Buat nota dinas penolakan
        NotaDinas::create([
            'permintaan_id' => $permintaan->permintaan_id,
            'dari_unit' => $user->unit_kerja ?? $user->jabatan,
            'ke_jabatan' => $permintaan->user->jabatan ?? 'Unit Pemohon',
            'tanggal_nota' => Carbon::now(),
            'status' => 'ditolak',
        ]);

        return redirect()
            ->route('kepala-instalasi.index')
            ->with('success', 'Permintaan ditolak');
    }

    /**
     * Request revisi dari pemohon
     */
    public function requestRevision(Request $request, Permintaan $permintaan)
    {
        $data = $request->validate([
            'catatan_revisi' => 'required|string',
        ]);

        // Update status permintaan
        $permintaan->update([
            'status' => 'revisi',
            'deskripsi' => $permintaan->deskripsi . "\n\n[CATATAN REVISI] " . $data['catatan_revisi'],
        ]);

        return redirect()
            ->route('kepala-instalasi.index')
            ->with('success', 'Permintaan revisi telah dikirim ke pemohon');
    }
}
