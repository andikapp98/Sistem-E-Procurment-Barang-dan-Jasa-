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
 * Controller untuk Direktur
 * 
 * Tugas Direktur:
 * 1. Menerima permintaan LANGSUNG dari Kepala Bidang (tanpa melalui Wakil Direktur)
 * 2. Review dan validasi final tingkat eksekutif tertinggi
 * 3. Approve dan disposisi kembali ke Kepala Bidang untuk perencanaan
 * 4. Reject jika tidak sesuai
 */
class DirekturController extends Controller
{
    /**
     * Dashboard Direktur
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ambil semua permintaan yang ditujukan ke Direktur
        // HANYA yang statusnya proses atau disetujui
        $permintaans = Permintaan::with(['user', 'notaDinas'])
            ->where(function($q) use ($user) {
                $q->where('pic_pimpinan', 'Direktur')
                  ->orWhere('pic_pimpinan', $user->nama);
            })
            ->whereIn('status', ['proses', 'disetujui'])
            ->get();

        $stats = [
            'total' => $permintaans->count(),
            'menunggu' => $permintaans->where('status', 'proses')->count(),
            'disetujui' => $permintaans->where('status', 'disetujui')->count(),
            'ditolak' => Permintaan::where('status', 'ditolak')
                ->where('deskripsi', 'like', '%DITOLAK oleh Direktur%')
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

        return Inertia::render('Direktur/Dashboard', [
            'stats' => $stats,
            'recentPermintaans' => $recentPermintaans,
            'userLogin' => $user,
        ]);
    }

    /**
     * Tampilkan daftar permintaan untuk Direktur
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Query dasar - hanya permintaan yang ditujukan ke Direktur
        $query = Permintaan::with(['user', 'notaDinas'])
            ->where(function($q) use ($user) {
                $q->where('pic_pimpinan', 'Direktur')
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

        // Pagination dengan 10 items per page
        $perPage = $request->input('per_page', 10);
        $permintaans = $query->orderByDesc('permintaan_id')
            ->paginate($perPage)
            ->through(function($permintaan) {
                $permintaan->tracking_status = $permintaan->trackingStatus;
                $permintaan->progress = $permintaan->getProgressPercentage();
                $permintaan->timeline_count = count($permintaan->getTimelineTracking());
                return $permintaan;
            });

        return Inertia::render('Direktur/Index', [
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
        
        return Inertia::render('Direktur/Show', [
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
        
        $permintaan->load(['user', 'notaDinas']);
        
        return Inertia::render('Direktur/CreateDisposisi', [
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
            ->route('direktur.show', $permintaan)
            ->with('success', 'Disposisi berhasil dibuat dan dikirim ke ' . $data['jabatan_tujuan']);
    }

    /**
     * Approve permintaan - Disposisi BALIK ke Kepala Bidang
     * Final Approval dari Direktur, kemudian dikembalikan ke Kepala Bidang
     * untuk diteruskan ke Staff Perencanaan
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
            return redirect()->back()->withErrors(['error' => 'Nota dinas tidak ditemukan. Silakan hubungi administrator.']);
        }

        // Buat disposisi BALIK ke Kepala Bidang
        Disposisi::create([
            'nota_id' => $notaDinas->nota_id,
            'jabatan_tujuan' => 'Kepala Bidang',
            'tanggal_disposisi' => Carbon::now(),
            'catatan' => $data['catatan'] ?? 'Disetujui oleh Direktur (Final Approval). Silakan disposisi ke Staff Perencanaan untuk perencanaan pengadaan.',
            'status' => 'disetujui',
        ]);

        // Update status permintaan - kembalikan ke Kepala Bidang
        $permintaan->update([
            'status' => 'disetujui',
            'pic_pimpinan' => 'Kepala Bidang',
        ]);

        return redirect()
            ->route('direktur.index')
            ->with('success', 'Permintaan disetujui (Final Approval) dan didisposisi balik ke Kepala Bidang untuk diteruskan ke Staff Perencanaan.');
    }

    /**
     * Reject permintaan
     * Direktur menolak permintaan dan menghentikan proses
     */
    public function reject(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'alasan' => 'required|string|min:10',
        ]);

        // Ambil nota dinas terakhir dan buat disposisi penolakan
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if ($notaDinas) {
            Disposisi::create([
                'nota_id' => $notaDinas->nota_id,
                'jabatan_tujuan' => $permintaan->user->jabatan ?? 'Unit Pemohon',
                'tanggal_disposisi' => Carbon::now(),
                'catatan' => '[DITOLAK oleh Direktur] ' . $data['alasan'],
                'status' => 'ditolak',
            ]);
        }

        // Update status permintaan
        $permintaan->update([
            'status' => 'ditolak',
            'pic_pimpinan' => 'Unit Pemohon',
            'deskripsi' => $permintaan->deskripsi . "\n\n---\n[DITOLAK oleh Direktur]\nAlasan: " . $data['alasan'] . "\nTanggal: " . Carbon::now()->format('d-m-Y H:i:s'),
        ]);

        return redirect()
            ->route('direktur.index')
            ->with('success', 'Permintaan ditolak. Proses dihentikan dan dikembalikan ke unit pemohon.');
    }

    /**
     * Request revisi - Kembalikan ke Kepala Bidang untuk diperbaiki
     * Direktur meminta revisi dan mengembalikan ke level sebelumnya
     */
    public function requestRevision(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'catatan_revisi' => 'required|string|min:10',
        ]);

        // Ambil nota dinas terakhir
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if ($notaDinas) {
            // Buat disposisi revisi ke Kepala Bidang
            Disposisi::create([
                'nota_id' => $notaDinas->nota_id,
                'jabatan_tujuan' => 'Kepala Bidang',
                'tanggal_disposisi' => Carbon::now(),
                'catatan' => '[REVISI dari Direktur] ' . $data['catatan_revisi'],
                'status' => 'revisi',
            ]);
        }

        // Update status permintaan - kembalikan ke Kepala Bidang
        $permintaan->update([
            'status' => 'revisi',
            'pic_pimpinan' => 'Kepala Bidang',
            'deskripsi' => $permintaan->deskripsi . "\n\n---\n[CATATAN REVISI dari Direktur]\n" . $data['catatan_revisi'] . "\nTanggal: " . Carbon::now()->format('d-m-Y H:i:s'),
        ]);

        return redirect()
            ->route('direktur.index')
            ->with('success', 'Permintaan revisi telah dikirim ke Kepala Bidang untuk diperbaiki.');
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

        return Inertia::render('Direktur/Tracking', [
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
        
        // Query - ambil semua permintaan yang sudah pernah melalui Direktur
        $query = Permintaan::with(['user', 'notaDinas.disposisi'])
            ->whereHas('notaDinas.disposisi', function($q) use ($user) {
                // Cari disposisi yang pernah ditujukan ke Direktur
                $q->where('jabatan_tujuan', 'like', '%Direktur%')
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        return Inertia::render('Direktur/Approved', [
            'permintaans' => $permintaans,
            'userLogin' => $user,
            'filters' => $request->only(['search', 'bidang', 'status', 'tanggal_dari', 'tanggal_sampai', 'per_page']),
        ]);
    }
}
