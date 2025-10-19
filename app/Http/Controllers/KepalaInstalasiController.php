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
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Query dasar dengan filter isolasi data
        $query = Permintaan::with(['user', 'notaDinas'])
            ->where(function($q) use ($user) {
                if ($user->unit_kerja) {
                    $q->where('bidang', $user->unit_kerja);
                }
                $q->orWhere('pic_pimpinan', $user->nama);
            });

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

        return Inertia::render('KepalaInstalasi/Index', [
            'permintaans' => $permintaans,
            'userLogin' => $user,
            'filters' => $request->only(['search', 'status', 'tanggal_dari', 'tanggal_sampai', 'per_page']),
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
            'dari' => 'required|string',
            'kepada' => 'required|string',
            'tanggal_nota' => 'required|date',
            'perihal' => 'nullable|string',
        ]);

        $data['permintaan_id'] = $permintaan->permintaan_id;
        
        // Set default perihal jika tidak ada
        if (!isset($data['perihal'])) {
            $data['perihal'] = 'Permintaan Pengadaan - ' . $permintaan->no_nota_dinas;
        }

        $notaDinas = NotaDinas::create($data);

        // Update status permintaan
        $permintaan->update([
            'status' => 'proses',
            'pic_pimpinan' => $data['kepada'],
        ]);

        return redirect()
            ->route('kepala-instalasi.show', $permintaan)
            ->with('success', 'Nota Dinas berhasil dibuat dan dikirim ke ' . $data['kepada']);
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
            'no_nota' => $permintaan->no_nota_dinas ?? 'ND/' . date('Y/m/d') . '/' . $permintaan->permintaan_id,
            'dari' => $user->unit_kerja ?? $user->jabatan,
            'kepada' => 'Kepala Bidang',
            'tanggal_nota' => Carbon::now(),
            'perihal' => 'Persetujuan Permintaan - ' . substr($permintaan->deskripsi, 0, 100),
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
            'no_nota' => 'ND/REJECT/' . date('Y/m/d') . '/' . $permintaan->permintaan_id,
            'dari' => $user->unit_kerja ?? $user->jabatan,
            'kepada' => $permintaan->user->jabatan ?? 'Unit Pemohon',
            'tanggal_nota' => Carbon::now(),
            'perihal' => 'Penolakan Permintaan - ' . $data['alasan'],
        ]);

        return redirect()
            ->route('kepala-instalasi.index')
            ->with('success', 'Permintaan ditolak dan dikembalikan ke unit pemohon');
    }

    /**
     * Request revisi dari pemohon
     * Kepala Instalasi meminta staff untuk memperbaiki permintaan
     */
    public function requestRevision(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk meminta revisi permintaan ini.');
        }
        
        $data = $request->validate([
            'catatan_revisi' => 'required|string|min:10',
        ]);

        // Update status permintaan ke revisi
        $permintaan->update([
            'status' => 'revisi',
            'pic_pimpinan' => $permintaan->user->name ?? 'Staff Unit', // Kembalikan ke pembuat permintaan
            'deskripsi' => $permintaan->deskripsi . "\n\n[CATATAN REVISI dari {$user->jabatan} - " . Carbon::now()->format('d/m/Y H:i') . "] " . $data['catatan_revisi'],
        ]);

        // Buat Nota Dinas untuk dokumentasi permintaan revisi
        NotaDinas::create([
            'permintaan_id' => $permintaan->permintaan_id,
            'no_nota' => 'ND/REVISI/' . date('Y/m/d') . '/' . $permintaan->permintaan_id,
            'dari' => $user->unit_kerja ?? $user->jabatan,
            'kepada' => $permintaan->user->jabatan ?? 'Staff Unit',
            'tanggal_nota' => Carbon::now(),
            'perihal' => 'Permintaan Revisi - ' . substr($data['catatan_revisi'], 0, 100),
        ]);

        return redirect()
            ->route('kepala-instalasi.index')
            ->with('success', 'Permintaan revisi telah dikirim ke ' . ($permintaan->user->name ?? 'pemohon') . ' untuk diperbaiki');
    }

    /**
     * Review kembali permintaan yang ditolak oleh Kepala Bidang
     * Kepala Instalasi dapat memperbaiki dan mengajukan kembali
     */
    public function reviewRejected(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk mereview permintaan ini.');
        }

        // Hanya bisa review jika status ditolak
        if ($permintaan->status !== 'ditolak') {
            return redirect()
                ->route('kepala-instalasi.show', $permintaan)
                ->with('error', 'Permintaan ini tidak dalam status ditolak.');
        }

        $permintaan->load('user', 'notaDinas');
        
        return Inertia::render('KepalaInstalasi/ReviewRejected', [
            'permintaan' => $permintaan,
            'userLogin' => $user,
        ]);
    }

    /**
     * Ajukan kembali permintaan yang ditolak setelah diperbaiki
     */
    public function resubmit(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk mengajukan kembali permintaan ini.');
        }

        // Hanya bisa resubmit jika status ditolak
        if ($permintaan->status !== 'ditolak') {
            return redirect()
                ->route('kepala-instalasi.show', $permintaan)
                ->with('error', 'Permintaan ini tidak dalam status ditolak.');
        }

        $data = $request->validate([
            'deskripsi' => 'required|string',
            'catatan_perbaikan' => 'nullable|string',
        ]);

        // Update permintaan dengan deskripsi baru
        $updateData = [
            'status' => 'diajukan', // Reset ke status diajukan
            'deskripsi' => $data['deskripsi'],
            'pic_pimpinan' => 'Kepala Bidang', // Kembali ke Kepala Bidang
        ];

        // Tambahkan catatan perbaikan jika ada
        if (isset($data['catatan_perbaikan']) && $data['catatan_perbaikan']) {
            $updateData['deskripsi'] .= "\n\n[PERBAIKAN dari {$user->jabatan}] " . $data['catatan_perbaikan'];
        }

        $permintaan->update($updateData);

        // Buat nota dinas baru untuk pengajuan ulang
        NotaDinas::create([
            'permintaan_id' => $permintaan->permintaan_id,
            'no_nota' => 'ND/RESUBMIT/' . date('Y/m/d') . '/' . $permintaan->permintaan_id,
            'dari' => $user->unit_kerja ?? $user->jabatan,
            'kepada' => 'Kepala Bidang',
            'tanggal_nota' => Carbon::now(),
            'perihal' => 'Pengajuan Ulang Permintaan (Setelah Perbaikan)',
        ]);

        return redirect()
            ->route('kepala-instalasi.index')
            ->with('success', 'Permintaan berhasil diajukan kembali ke Kepala Bidang setelah diperbaiki');
    }
}

