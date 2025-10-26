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
        
        // Ambil permintaan yang SEDANG di tangan Kepala Bidang
        $permintaans = Permintaan::with(['user', 'notaDinas.disposisi'])
            ->where(function($q) use ($user) {
                // Cek berdasarkan pic_pimpinan = Kepala Bidang
                $q->where('pic_pimpinan', 'Kepala Bidang')
                  ->orWhere('pic_pimpinan', $user->nama);
            })
            ->whereIn('status', ['proses', 'disetujui'])
            ->get();

        $stats = [
            'total' => $permintaans->count(),
            'menunggu' => $permintaans->where('status', 'proses')->count(),
            'disetujui' => $permintaans->where('status', 'disetujui')->count(),
            'ditolak' => Permintaan::where('status', 'ditolak')
                ->where('deskripsi', 'like', '%DITOLAK oleh Kepala Bidang%')
                ->count(),
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
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Query dasar - permintaan yang SEDANG di tangan Kepala Bidang
        $query = Permintaan::with(['user', 'notaDinas.disposisi'])
            ->where(function($q) use ($user) {
                // Cek berdasarkan pic_pimpinan = Kepala Bidang
                $q->where('pic_pimpinan', 'Kepala Bidang')
                  ->orWhere('pic_pimpinan', $user->nama);
            })
            ->whereIn('status', ['proses', 'disetujui']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('permintaan_id', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bidang')) {
            $query->where('bidang', $request->bidang);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_permintaan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_permintaan', '<=', $request->tanggal_sampai);
        }

        // Pagination dengan 10 items per page (bisa diubah sesuai kebutuhan)
        $perPage = $request->input('per_page', 10);
        $permintaans = $query->orderByDesc('permintaan_id')
            ->paginate($perPage)
            ->through(function($permintaan) {
                $permintaan->tracking_status = $permintaan->trackingStatus;
                $permintaan->progress = $permintaan->getProgressPercentage();
                $permintaan->timeline_count = count($permintaan->getTimelineTracking());
                return $permintaan;
            });

        return Inertia::render('KepalaBidang/Index', [
            'permintaans' => $permintaans,
            'userLogin' => $user,
            'filters' => $request->only(['search', 'status', 'bidang', 'tanggal_dari', 'tanggal_sampai', 'per_page']),
        ]);
    }

    /**
     * Tampilkan detail permintaan
     */
    public function show(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $permintaan->load(['user', 'notaDinas.disposisi']);
        
        // Get timeline tracking
        $timeline = $permintaan->getTimelineTracking();
        $progress = $permintaan->getProgressPercentage();
        
        // Cek apakah ini disposisi balik dari Direktur
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        $isDisposisiDariDirektur = false;
        
        if ($notaDinas) {
            // Gunakan logic yang sama dengan approve method
            $isDisposisiDariDirektur = Disposisi::where('nota_id', $notaDinas->nota_id)
                ->where('jabatan_tujuan', 'Kepala Bidang')
                ->where(function($q) {
                    $q->where('catatan', 'like', '%Disetujui oleh Direktur%')
                      ->orWhere('status', 'selesai');
                })
                ->exists();
        }
        
        return Inertia::render('KepalaBidang/Show', [
            'permintaan' => $permintaan,
            'trackingStatus' => $permintaan->trackingStatus,
            'timeline' => $timeline,
            'progress' => $progress,
            'isDisposisiDariDirektur' => $isDisposisiDariDirektur,
        ]);
    }

    /**
     * Form membuat disposisi
     */
    public function createDisposisi(Permintaan $permintaan)
    {
        $user = Auth::user();
        
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
     * Approve permintaan - Ada 2 skenario:
     * 1. Pertama kali dari Kepala Instalasi → Teruskan ke Direktur
     * 2. Disposisi balik dari Direktur → Teruskan ke Staff Perencanaan
     */
    public function approve(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();

        $data = $request->validate([
            'catatan' => 'nullable|string',
        ]);

        // Ambil nota dinas terakhir
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if (!$notaDinas) {
            return redirect()->back()->withErrors(['error' => 'Nota dinas tidak ditemukan']);
        }

        // Cek apakah ini disposisi balik dari Direktur atau permintaan baru dari Kepala Instalasi
        // Cek apakah ada disposisi dengan catatan dari Direktur (approval)
        $disposisiDariDirektur = Disposisi::where('nota_id', $notaDinas->nota_id)
            ->where('jabatan_tujuan', 'Kepala Bidang')
            ->where(function($q) {
                $q->where('catatan', 'like', '%Disetujui oleh Direktur%')
                  ->orWhere('status', 'selesai');
            })
            ->exists();

        // Skenario 1: Disposisi balik dari Direktur - Teruskan ke Staff Perencanaan
        if ($disposisiDariDirektur) {
            // Buat disposisi ke Staff Perencanaan
            Disposisi::create([
                'nota_id' => $notaDinas->nota_id,
                'jabatan_tujuan' => 'Staff Perencanaan',
                'tanggal_disposisi' => Carbon::now(),
                'catatan' => $data['catatan'] ?? 'Sudah disetujui Direktur. Mohon lakukan perencanaan pengadaan.',
                'status' => 'disetujui',
            ]);

            // Update status permintaan - teruskan ke Staff Perencanaan
            $permintaan->update([
                'status' => 'disetujui',
                'pic_pimpinan' => 'Staff Perencanaan',
            ]);

            return redirect()
                ->route('kepala-bidang.index')
                ->with('success', 'Permintaan diteruskan ke Staff Perencanaan untuk perencanaan pengadaan');
        }

        // Skenario 2: Permintaan baru dari Kepala Instalasi - Teruskan ke Direktur
        // Buat disposisi otomatis LANGSUNG ke Direktur (skip Wakil Direktur)
        Disposisi::create([
            'nota_id' => $notaDinas->nota_id,
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now(),
            'catatan' => $data['catatan'] ?? 'Disetujui oleh Kepala Bidang, diteruskan ke Direktur',
            'status' => 'disetujui',
        ]);

        // Update status permintaan - teruskan LANGSUNG ke Direktur
        $permintaan->update([
            'status' => 'proses',
            'pic_pimpinan' => 'Direktur',
        ]);

        return redirect()
            ->route('kepala-bidang.index')
            ->with('success', 'Permintaan disetujui dan diteruskan ke Direktur');
    }

    /**
     * Reject permintaan
     */
    public function reject(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
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

    /**
     * Tampilkan timeline tracking untuk permintaan
     * Untuk melihat progress permintaan yang sudah disetujui
     */
    public function tracking(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Load all relations untuk tracking
        $permintaan->load([
            'user',
            'notaDinas.disposisi.perencanaan.kso.pengadaan.notaPenerimaan.serahTerima'
        ]);

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
            'Serah Terima',
        ];

        $completedSteps = array_column($timeline, 'tahapan');
        $pendingSteps = array_diff($allSteps, $completedSteps);

        return Inertia::render('KepalaBidang/Tracking', [
            'permintaan' => $permintaan,
            'timeline' => $timeline,
            'progress' => $progress,
            'completedSteps' => $completedSteps,
            'pendingSteps' => array_values($pendingSteps),
            'userLogin' => $user,
        ]);
    }

    /**
     * Tampilkan daftar permintaan yang sudah disetujui (untuk tracking)
     */
    public function approved(Request $request)
    {
        $user = Auth::user();
        
        // Query - ambil semua permintaan yang sudah pernah melalui Kepala Bidang
        // dan statusnya disetujui, ditolak, atau revisi (sudah melewati tahap Kepala Bidang)
        $query = Permintaan::with(['user', 'notaDinas.disposisi'])
            ->whereHas('notaDinas.disposisi', function($q) use ($user) {
                // Cari disposisi yang pernah ditujukan ke Kepala Bidang
                $q->where('jabatan_tujuan', 'like', '%Kepala Bidang%')
                  ->orWhere('jabatan_tujuan', $user->jabatan);
            })
            ->whereIn('status', ['proses', 'disetujui', 'ditolak', 'revisi']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('permintaan_id', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('no_nota_dinas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('bidang')) {
            $query->where('bidang', $request->bidang);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_permintaan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_permintaan', '<=', $request->tanggal_sampai);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $permintaans = $query->orderByDesc('permintaan_id')
            ->paginate($perPage)
            ->through(function($permintaan) {
                // Tambahkan tracking info
                $permintaan->tracking_status = $permintaan->trackingStatus;
                $permintaan->progress = $permintaan->getProgressPercentage();
                $permintaan->timeline_count = count($permintaan->getTimelineTracking());
                
                // Cek tahap terakhir
                $timeline = $permintaan->getTimelineTracking();
                $permintaan->current_stage = !empty($timeline) ? $timeline[count($timeline) - 1]['tahapan'] : 'Permintaan';
                
                return $permintaan;
            });

        return Inertia::render('KepalaBidang/Approved', [
            'permintaans' => $permintaans,
            'userLogin' => $user,
            'filters' => $request->only(['search', 'bidang', 'status', 'tanggal_dari', 'tanggal_sampai', 'per_page']),
        ]);
    }
}
