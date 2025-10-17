<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use App\Models\Disposisi;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Controller untuk Kepala Bidang
 * 
 * Tugas Kepala Bidang:
 * 1. Menerima permintaan dari Kepala Instalasi
 * 2. Review dan validasi permintaan
 * 3. Membuat disposisi untuk diteruskan ke bagian terkait
 * 4. Approve atau reject permintaan
 */
class KepalaBidangController extends Controller
{
    /**
     * Dashboard Kepala Bidang
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ambil semua permintaan yang ditujukan ke Kepala Bidang
        $permintaans = Permintaan::with(['user', 'notaDinas'])
            ->where('pic_pimpinan', 'Kepala Bidang')
            ->orWhere('pic_pimpinan', $user->nama)
            ->get();

        $stats = [
            'total' => $permintaans->count(),
            'menunggu' => $permintaans->where('status', 'proses')->count(),
            'disetujui' => $permintaans->where('status', 'disetujui')->count(),
            'ditolak' => $permintaans->where('status', 'ditolak')->count(),
        ];

        // Ambil 5 permintaan terbaru
        $recentPermintaans = $permintaans
            ->sortByDesc('permintaan_id')
            ->take(5)
            ->map(function($permintaan) {
                $permintaan->tracking_status = $permintaan->trackingStatus;
                $permintaan->progress = $permintaan->getProgressPercentage();
                return $permintaan;
            })
            ->values();

        return Inertia::render('KepalaBidang/Dashboard', [
            'stats' => $stats,
            'recentPermintaans' => $recentPermintaans,
            'userLogin' => $user,
        ]);
    }

    /**
     * Tampilkan daftar permintaan untuk Kepala Bidang
     */
    public function index()
    {
        $user = Auth::user();
        
        $permintaans = Permintaan::with(['user', 'notaDinas'])
            ->where('pic_pimpinan', 'Kepala Bidang')
            ->orWhere('pic_pimpinan', $user->nama)
            ->orderByDesc('permintaan_id')
            ->get()
            ->map(function($permintaan) {
                $permintaan->tracking_status = $permintaan->trackingStatus;
                $permintaan->progress = $permintaan->getProgressPercentage();
                return $permintaan;
            });

        return Inertia::render('KepalaBidang/Index', [
            'permintaans' => $permintaans,
            'userLogin' => $user,
        ]);
    }

    /**
     * Tampilkan detail permintaan
     */
    public function show(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Kepala Bidang' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk melihat permintaan ini.');
        }
        
        $permintaan->load(['user', 'notaDinas.disposisi']);
        
        // Get timeline tracking
        $timeline = $permintaan->getTimelineTracking();
        $progress = $permintaan->getProgressPercentage();
        
        return Inertia::render('KepalaBidang/Show', [
            'permintaan' => $permintaan,
            'trackingStatus' => $permintaan->trackingStatus,
            'timeline' => $timeline,
            'progress' => $progress,
        ]);
    }

    /**
     * Form membuat disposisi
     */
    public function createDisposisi(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Kepala Bidang' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk membuat disposisi permintaan ini.');
        }
        
        $permintaan->load(['user', 'notaDinas']);
        
        return Inertia::render('KepalaBidang/CreateDisposisi', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Store disposisi
     */
    public function storeDisposisi(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Kepala Bidang' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk membuat disposisi permintaan ini.');
        }
        
        $data = $request->validate([
            'jabatan_tujuan' => 'required|string',
            'tanggal_disposisi' => 'required|date',
            'catatan' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        // Ambil nota dinas terakhir
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if (!$notaDinas) {
            return redirect()->back()->withErrors(['error' => 'Nota dinas tidak ditemukan']);
        }

        $data['nota_id'] = $notaDinas->nota_id;
        
        // Set default status
        if (!isset($data['status'])) {
            $data['status'] = 'dalam_proses';
        }

        Disposisi::create($data);

        // Update status permintaan
        $permintaan->update([
            'status' => 'proses',
            'pic_pimpinan' => $data['jabatan_tujuan'],
        ]);

        return redirect()
            ->route('kepala-bidang.show', $permintaan)
            ->with('success', 'Disposisi berhasil dibuat dan dikirim ke ' . $data['jabatan_tujuan']);
    }

    /**
     * Approve permintaan - Teruskan ke bagian terkait
     */
    public function approve(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Kepala Bidang' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk menyetujui permintaan ini.');
        }

        $data = $request->validate([
            'tujuan' => 'required|string', // Bagian Perencanaan, Bagian Pengadaan, dll
            'catatan' => 'nullable|string',
        ]);

        // Ambil nota dinas terakhir
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if (!$notaDinas) {
            return redirect()->back()->withErrors(['error' => 'Nota dinas tidak ditemukan']);
        }

        // Buat disposisi otomatis
        Disposisi::create([
            'nota_id' => $notaDinas->nota_id,
            'jabatan_tujuan' => $data['tujuan'],
            'tanggal_disposisi' => Carbon::now(),
            'catatan' => $data['catatan'] ?? 'Disetujui oleh Kepala Bidang',
            'status' => 'disetujui',
        ]);

        // Update status permintaan
        $permintaan->update([
            'status' => 'disetujui',
            'pic_pimpinan' => $data['tujuan'],
        ]);

        return redirect()
            ->route('kepala-bidang.index')
            ->with('success', 'Permintaan disetujui dan diteruskan ke ' . $data['tujuan']);
    }

    /**
     * Reject permintaan
     */
    public function reject(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Kepala Bidang' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk menolak permintaan ini.');
        }
        
        $data = $request->validate([
            'alasan' => 'required|string',
        ]);

        // Update status permintaan
        $permintaan->update([
            'status' => 'ditolak',
            'pic_pimpinan' => $user->nama,
            'deskripsi' => $permintaan->deskripsi . "\n\n[DITOLAK oleh Kepala Bidang] " . $data['alasan'],
        ]);

        // Ambil nota dinas terakhir dan buat disposisi penolakan
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if ($notaDinas) {
            Disposisi::create([
                'nota_id' => $notaDinas->nota_id,
                'jabatan_tujuan' => $permintaan->user->jabatan ?? 'Unit Pemohon',
                'tanggal_disposisi' => Carbon::now(),
                'catatan' => $data['alasan'],
                'status' => 'ditolak',
            ]);
        }

        return redirect()
            ->route('kepala-bidang.index')
            ->with('success', 'Permintaan ditolak dan dikembalikan ke unit pemohon');
    }

    /**
     * Request revisi dari pemohon
     */
    public function requestRevision(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Kepala Bidang' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk meminta revisi permintaan ini.');
        }
        
        $data = $request->validate([
            'catatan_revisi' => 'required|string',
        ]);

        // Update status permintaan
        $permintaan->update([
            'status' => 'revisi',
            'deskripsi' => $permintaan->deskripsi . "\n\n[CATATAN REVISI dari Kepala Bidang] " . $data['catatan_revisi'],
        ]);

        return redirect()
            ->route('kepala-bidang.index')
            ->with('success', 'Permintaan revisi telah dikirim ke pemohon');
    }
}
