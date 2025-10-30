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
        
        // Ambil permintaan yang SEDANG di tangan Direktur (pic_pimpinan = Direktur DAN status = proses)
        // Setelah Direktur approve/reject/revisi, pic_pimpinan berubah sehingga tidak muncul lagi
        $permintaans = Permintaan::with(['user', 'notaDinas'])
            ->where(function($q) use ($user) {
                $q->where('pic_pimpinan', 'Direktur')
                  ->orWhere('pic_pimpinan', $user->nama);
            })
            ->where('status', 'proses') // HANYA yang sedang proses
            ->get();

        $stats = [
            'total' => $permintaans->count(),
            'menunggu' => $permintaans->where('status', 'proses')->count(),
            'disetujui' => Permintaan::where('status', 'disetujui')
                ->where('deskripsi', 'like', '%disetujui oleh Direktur%')
                ->count(),
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
        
        // Query dasar - HANYA permintaan yang SEDANG di tangan Direktur (status = proses)
        $query = Permintaan::with(['user', 'notaDinas'])
            ->where(function($q) use ($user) {
                $q->where('pic_pimpinan', 'Direktur')
                  ->orWhere('pic_pimpinan', $user->nama);
            })
            ->where('status', 'proses'); // HANYA yang sedang proses

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('permintaan_id', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
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
     * Approve permintaan - Langsung ke Kabid sesuai klasifikasi
     * Final Approval dari Direktur, diteruskan ke Kepala Bidang yang sesuai
     * 
     * ROUTING OTOMATIS:
     * - Permintaan MEDIS → Kabid Yanmed (Bidang Pelayanan Medis)
     * - Permintaan PENUNJANG_MEDIS → Kabid Penunjang Medis
     * - Permintaan NON_MEDIS → Kabid Umum & Keuangan
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

        // Tentukan Kabid tujuan berdasarkan klasifikasi permintaan
        $klasifikasi = $permintaan->klasifikasi_permintaan;
        $kabidTujuan = $this->getKabidTujuanByKlasifikasi($klasifikasi);

        // Buat disposisi ke Kepala Bidang yang sesuai
        Disposisi::create([
            'nota_id' => $notaDinas->nota_id,
            'jabatan_tujuan' => $kabidTujuan,
            'tanggal_disposisi' => Carbon::now(),
            'catatan' => 'Disetujui oleh Direktur (Final Approval). ' . 
                        ($data['catatan'] ?? 'Silakan disposisi ke Staff Perencanaan untuk perencanaan pengadaan.') .
                        "\n\nKlasifikasi: " . strtoupper(str_replace('_', ' ', $klasifikasi)) .
                        "\nDiteruskan ke: " . $kabidTujuan,
            'status' => 'selesai',
        ]);

        // Update status permintaan - ke Kepala Bidang yang sesuai
        $permintaan->update([
            'status' => 'proses',
            'pic_pimpinan' => 'Kepala Bidang',
            'kabid_tujuan' => $kabidTujuan, // Set kabid_tujuan agar routing jelas
        ]);

        return redirect()
            ->route('direktur.index')
            ->with('success', 'Permintaan disetujui (Final Approval) dan diteruskan ke ' . $kabidTujuan . ' untuk disposisi ke Staff Perencanaan.');
    }

    /**
     * Helper: Tentukan Kabid tujuan berdasarkan klasifikasi
     */
    private function getKabidTujuanByKlasifikasi($klasifikasi)
    {
        $mapping = [
            'Medis' => 'Bidang Pelayanan Medis',
            'medis' => 'Bidang Pelayanan Medis',
            'Penunjang' => 'Bidang Penunjang Medis',
            'penunjang_medis' => 'Bidang Penunjang Medis',
            'Non Medis' => 'Bidang Umum & Keuangan',
            'non_medis' => 'Bidang Umum & Keuangan',
        ];

        return $mapping[$klasifikasi] ?? 'Bidang Pelayanan Medis';
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

        // Get complete tracking (termasuk tahapan yang belum dilalui)
        $completeTracking = $permintaan->getCompleteTracking();
        $progress = $permintaan->getProgressPercentage();
        $nextStep = $permintaan->getNextStep();

        // Pisahkan completed dan pending steps
        $completedSteps = array_filter($completeTracking, function($item) {
            return $item['completed'];
        });
        
        $pendingSteps = array_filter($completeTracking, function($item) {
            return !$item['completed'];
        });

        return Inertia::render('Direktur/Tracking', [
            'permintaan' => $permintaan,
            'completeTracking' => array_values($completeTracking),
            'completedSteps' => array_values($completedSteps),
            'pendingSteps' => array_values($pendingSteps),
            'nextStep' => $nextStep,
            'progress' => $progress,
            'userLogin' => $user,
        ]);
    }

    /**
     * Tampilkan daftar permintaan yang sudah diproses Direktur
     * Menampilkan semua permintaan yang sudah di-approve, reject, atau revisi oleh Direktur
     */
    public function approved(Request $request)
    {
        $user = Auth::user();
        
        // Query - ambil permintaan yang sudah diproses Direktur
        // Cari dari disposisi dengan catatan mengandung tanda keputusan Direktur
        $query = Permintaan::with(['user', 'notaDinas.disposisi'])
            ->whereHas('notaDinas.disposisi', function($q) {
                // Cari disposisi yang dibuat DARI Direktur (setelah approve/reject/revisi)
                $q->where(function($subQ) {
                    $subQ->where('catatan', 'like', '%Disetujui oleh Direktur%')
                         ->orWhere('catatan', 'like', '%DITOLAK oleh Direktur%')
                         ->orWhere('catatan', 'like', '%REVISI dari Direktur%');
                });
            });

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('permintaan_id', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('bidang', 'like', "%{$search}%");
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
                
                // Cek keputusan Direktur dari disposisi
                $direkturDisposisi = null;
                if ($permintaan->notaDinas && $permintaan->notaDinas->count() > 0) {
                    $direkturDisposisi = $permintaan->notaDinas->flatMap->disposisi
                        ->filter(function($disp) {
                            // Cari disposisi yang mengandung keputusan Direktur
                            return stripos($disp->catatan ?? '', 'Disetujui oleh Direktur') !== false
                                || stripos($disp->catatan ?? '', 'DITOLAK oleh Direktur') !== false
                                || stripos($disp->catatan ?? '', 'REVISI dari Direktur') !== false;
                        })
                        ->last();
                }
                
                if ($direkturDisposisi) {
                    if (stripos($direkturDisposisi->catatan, 'DITOLAK oleh Direktur') !== false) {
                        $permintaan->direktur_decision = 'Ditolak';
                        $permintaan->direktur_decision_class = 'rejected';
                    } elseif (stripos($direkturDisposisi->catatan, 'REVISI dari Direktur') !== false) {
                        $permintaan->direktur_decision = 'Revisi';
                        $permintaan->direktur_decision_class = 'revision';
                    } elseif (stripos($direkturDisposisi->catatan, 'Disetujui oleh Direktur') !== false) {
                        $permintaan->direktur_decision = 'Disetujui';
                        $permintaan->direktur_decision_class = 'approved';
                    } else {
                        $permintaan->direktur_decision = '-';
                        $permintaan->direktur_decision_class = 'unknown';
                    }
                    $permintaan->direktur_date = $direkturDisposisi->tanggal_disposisi;
                    $permintaan->direktur_notes = $direkturDisposisi->catatan;
                } else {
                    $permintaan->direktur_decision = '-';
                    $permintaan->direktur_decision_class = 'unknown';
                    $permintaan->direktur_date = null;
                    $permintaan->direktur_notes = null;
                }
                
                return $permintaan;
            });

        // Statistics
        $stats = [
            'total' => Permintaan::whereHas('notaDinas.disposisi', function($q) {
                $q->where(function($subQ) {
                    $subQ->where('catatan', 'like', '%Disetujui oleh Direktur%')
                         ->orWhere('catatan', 'like', '%DITOLAK oleh Direktur%')
                         ->orWhere('catatan', 'like', '%REVISI dari Direktur%');
                });
            })->count(),
            'approved' => Permintaan::whereHas('notaDinas.disposisi', function($q) {
                $q->where('catatan', 'like', '%Disetujui oleh Direktur%');
            })->count(),
            'rejected' => Permintaan::whereHas('notaDinas.disposisi', function($q) {
                $q->where('catatan', 'like', '%DITOLAK oleh Direktur%');
            })->count(),
            'revision' => Permintaan::whereHas('notaDinas.disposisi', function($q) {
                $q->where('catatan', 'like', '%REVISI dari Direktur%');
            })->count(),
        ];

        return Inertia::render('Direktur/Approved', [
            'permintaans' => $permintaans,
            'stats' => $stats,
            'userLogin' => $user,
            'filters' => $request->only(['search', 'bidang', 'status', 'tanggal_dari', 'tanggal_sampai', 'per_page']),
        ]);
    }
}
