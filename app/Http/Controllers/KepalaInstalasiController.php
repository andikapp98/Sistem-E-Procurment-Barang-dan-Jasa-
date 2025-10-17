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
        
        // Hitung statistik - ambil semua permintaan untuk bagian/unit kerja kepala instalasi
        // Filter berdasarkan bidang di tabel permintaan, bukan unit_kerja dari user pembuat
        $permintaans = Permintaan::with(['user', 'notaDinas'])
            ->where(function($query) use ($user) {
                if ($user->unit_kerja) {
                    // Filter berdasarkan bidang yang sesuai dengan unit_kerja kepala instalasi
                    $query->where('bidang', $user->unit_kerja);
                }
                // Atau permintaan yang ditugaskan langsung ke kepala instalasi ini
                $query->orWhere('pic_pimpinan', $user->nama);
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
                $permintaan->progress = $permintaan->getProgressPercentage();
                $permintaan->timeline_count = count($permintaan->getTimelineTracking());
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
     * Hanya menampilkan permintaan untuk bagian/unit yang dipimpin
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil permintaan yang ditujukan untuk bagian/unit kerja kepala instalasi
        // Filter berdasarkan kolom 'bidang' di tabel permintaan
        $permintaans = Permintaan::with(['user', 'notaDinas'])
            ->where(function($query) use ($user) {
                if ($user->unit_kerja) {
                    // Filter berdasarkan bidang yang sesuai dengan unit_kerja kepala instalasi
                    $query->where('bidang', $user->unit_kerja);
                }
                // Atau permintaan yang ditugaskan langsung ke kepala instalasi ini
                $query->orWhere('pic_pimpinan', $user->nama);
            })
            ->orderByDesc('permintaan_id')
            ->get()
            ->map(function($permintaan) {
                // Tambahkan status tracking dan progress
                $permintaan->tracking_status = $permintaan->trackingStatus;
                $permintaan->progress = $permintaan->getProgressPercentage();
                $permintaan->timeline_count = count($permintaan->getTimelineTracking());
                return $permintaan;
            });

        return Inertia::render('KepalaInstalasi/Index', [
            'permintaans' => $permintaans,
            'userLogin' => $user,
        ]);
    }

    /**
     * Tampilkan detail permintaan untuk review
     * Hanya bisa melihat permintaan untuk bagiannya sendiri
     */
    public function show(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek apakah kepala instalasi berhak melihat permintaan ini
        // Hanya boleh jika bidang permintaan sesuai dengan unit_kerja kepala instalasi
        // atau jika permintaan ditugaskan ke kepala instalasi ini
        if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk melihat permintaan ini.');
        }
        
        $permintaan->load(['user', 'notaDinas.disposisi']);
        
        // Get timeline tracking
        $timeline = $permintaan->getTimelineTracking();
        $progress = $permintaan->getProgressPercentage();
        
        return Inertia::render('KepalaInstalasi/Show', [
            'permintaan' => $permintaan,
            'trackingStatus' => $permintaan->trackingStatus,
            'timeline' => $timeline,
            'progress' => $progress,
        ]);
    }

    /**
     * Tampilkan timeline tracking untuk permintaan
     * Dedicated page untuk melihat tracking detail
     */
    public function tracking(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk melihat tracking permintaan ini.');
        }
        
        $permintaan->load(['user', 'notaDinas.disposisi.perencanaan.kso.pengadaan.notaPenerimaan.serahTerima']);
        
        // Get timeline tracking lengkap
        $timeline = $permintaan->getTimelineTracking();
        $progress = $permintaan->getProgressPercentage();
        
        // Tahapan yang belum dilalui
        $allSteps = [
            'Permintaan',
            'Nota Dinas',
            'Disposisi',
            'Perencanaan',
            'KSO',
            'Pengadaan',
            'Nota Penerimaan',
            'Serah Terima'
        ];
        
        $completedSteps = array_column($timeline, 'tahapan');
        $pendingSteps = array_diff($allSteps, $completedSteps);
        
        return Inertia::render('KepalaInstalasi/Tracking', [
            'permintaan' => $permintaan,
            'timeline' => $timeline,
            'progress' => $progress,
            'completedSteps' => $completedSteps,
            'pendingSteps' => array_values($pendingSteps),
            'currentStep' => end($completedSteps),
        ]);
    }

    /**
     * Form untuk membuat Nota Dinas dari permintaan
     * Hanya bisa membuat nota dinas untuk permintaan bagiannya sendiri
     */
    public function createNotaDinas(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk membuat nota dinas untuk permintaan ini.');
        }
        
        $permintaan->load('user');
        
        return Inertia::render('KepalaInstalasi/CreateNotaDinas', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Store Nota Dinas
     * Hanya bisa menyimpan nota dinas untuk permintaan bagiannya sendiri
     */
    public function storeNotaDinas(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk membuat nota dinas untuk permintaan ini.');
        }
        
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
     * Approve permintaan - Teruskan ke Kepala Bidang
     * Hanya bisa menyetujui permintaan untuk bagiannya sendiri
     */
    public function approve(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk menyetujui permintaan ini.');
        }

        // Validasi input - bisa dengan atau tanpa catatan
        $data = $request->validate([
            'catatan' => 'nullable|string',
        ]);

        // Update status permintaan menjadi disetujui oleh Kepala Instalasi
        $permintaan->update([
            'status' => 'proses', // Masih proses karena menunggu Kepala Bidang
            'pic_pimpinan' => 'Kepala Bidang', // Diteruskan ke Kepala Bidang
        ]);

        // Buat nota dinas ke Kepala Bidang
        NotaDinas::create([
            'permintaan_id' => $permintaan->permintaan_id,
            'dari_unit' => $user->unit_kerja ?? $user->jabatan,
            'ke_jabatan' => 'Kepala Bidang',
            'tanggal_nota' => Carbon::now(),
            'status' => 'dikirim',
        ]);

        $message = 'Permintaan disetujui dan diteruskan ke Kepala Bidang';
        if (isset($data['catatan']) && $data['catatan']) {
            $message .= ' dengan catatan: ' . $data['catatan'];
        }

        return redirect()
            ->route('kepala-instalasi.index')
            ->with('success', $message);
    }

    /**
     * Reject permintaan
     * Hanya bisa menolak permintaan untuk bagiannya sendiri
     */
    public function reject(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk menolak permintaan ini.');
        }
        
        $data = $request->validate([
            'alasan' => 'required|string',
        ]);

        // Update status permintaan menjadi ditolak
        $permintaan->update([
            'status' => 'ditolak',
            'pic_pimpinan' => $user->nama,
            'deskripsi' => $permintaan->deskripsi . "\n\n[DITOLAK oleh {$user->jabatan}] " . $data['alasan'],
        ]);

        // Buat nota dinas penolakan ke unit pemohon
        NotaDinas::create([
            'permintaan_id' => $permintaan->permintaan_id,
            'dari_unit' => $user->unit_kerja ?? $user->jabatan,
            'ke_jabatan' => $permintaan->user->jabatan ?? 'Unit Pemohon',
            'tanggal_nota' => Carbon::now(),
            'status' => 'ditolak',
        ]);

        return redirect()
            ->route('kepala-instalasi.index')
            ->with('success', 'Permintaan ditolak dan dikembalikan ke unit pemohon');
    }

    /**
     * Request revisi dari pemohon
     * Hanya bisa meminta revisi untuk permintaan bagiannya sendiri
     */
    public function requestRevision(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk meminta revisi permintaan ini.');
        }
        
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
