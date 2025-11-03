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
 * 1. Menerima permintaan dari Kepala Instalasi berdasarkan klasifikasi
 * 2. Review dan validasi permintaan
 * 3. Membuat disposisi untuk diteruskan ke Direktur
 * 4. Approve atau reject permintaan
 * 
 * ROUTING BERDASARKAN KLASIFIKASI:
 * - klasifikasi_permintaan = 'medis' → Kabid Pelayanan Medis
 * - klasifikasi_permintaan = 'penunjang_medis' → Kabid Penunjang Medis  
 * - klasifikasi_permintaan = 'non_medis' → Kabid Keperawatan/Bagian Umum
 */
class KepalaBidangController extends Controller
{
    /**
     * Get klasifikasi yang sesuai dengan unit kerja Kabid
     */
    private function getKlasifikasiByUnitKerja($unitKerja)
    {
        $mapping = [
            // Format baru
            'Bidang Pelayanan Medis' => ['Medis', 'medis'],
            'Bidang Penunjang Medis' => ['Penunjang', 'penunjang_medis'],
            'Bidang Keperawatan' => ['Non Medis', 'non_medis'],
            'Bagian Umum' => ['Non Medis', 'non_medis'],
            'Bidang Umum' => ['Non Medis', 'non_medis'],
            'Bidang Umum & Keuangan' => ['Non Medis', 'non_medis'],
        ];

        return $mapping[$unitKerja] ?? null;
    }

    /**
     * Dashboard Kepala Bidang
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Dapatkan klasifikasi yang sesuai dengan unit kerja Kabid
        $klasifikasiArray = $this->getKlasifikasiByUnitKerja($user->unit_kerja);
        
        // Ambil permintaan yang butuh action dari Kabid:
        // 1. Permintaan baru dari Kepala Instalasi (pic_pimpinan = Kepala Bidang)
        // 2. Disposisi balik dari Direktur (cek disposisi dengan jabatan_tujuan sesuai unit kerja)
        $permintaans = Permintaan::with(['user', 'notaDinas.disposisi'])
            ->where('status', 'proses')
            ->where(function($q) use ($user, $klasifikasiArray) {
                // Kondisi 1: Permintaan baru dari Kepala Instalasi
                // - pic_pimpinan = Kepala Bidang
                // - klasifikasi sesuai dengan unit kerja Kabid
                $q->where(function($subQ) use ($user, $klasifikasiArray) {
                    $subQ->where('pic_pimpinan', 'LIKE', '%Kepala Bidang%');
                    if ($klasifikasiArray) {
                        $subQ->whereIn('klasifikasi_permintaan', $klasifikasiArray);
                    }
                })
                // Kondisi 2: Disposisi balik dari Direktur
                // - Ada disposisi dengan jabatan_tujuan = unit kerja Kabid
                // - Catatan mengandung "Disetujui oleh Direktur"
                ->orWhere(function($subQ) use ($user) {
                    $subQ->where('kabid_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
                         ->whereHas('notaDinas.disposisi', function($dispQ) use ($user) {
                             $dispQ->where('jabatan_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
                                   ->where('catatan', 'LIKE', '%Disetujui oleh Direktur%');
                         });
                });
            })
            ->get();

        $stats = [
            'total' => $permintaans->count(),
            'menunggu' => $permintaans->count(), // Semua adalah menunggu action Kabid
            'disetujui' => Permintaan::whereHas('notaDinas.disposisi', function($q) {
                // Permintaan yang sudah di-approve Kabid (ada disposisi ke Direktur)
                $q->where('jabatan_tujuan', 'Direktur')
                  ->where('catatan', 'LIKE', '%Kepala Bidang%');
            })->count(),
            'ditolak' => Permintaan::where('status', 'ditolak')
                ->where(function($q) use ($klasifikasiArray) {
                    if ($klasifikasiArray) {
                        $q->whereIn('klasifikasi_permintaan', $klasifikasiArray);
                    }
                })
                ->count(),
        ];

        // Ambil 5 permintaan terbaru yang menunggu
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
            'klasifikasi' => $klasifikasiArray ? $klasifikasiArray[0] : null,
        ]);
    }

    /**
     * Tampilkan daftar permintaan untuk Kepala Bidang
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Dapatkan klasifikasi yang sesuai dengan unit kerja Kabid
        $klasifikasiArray = $this->getKlasifikasiByUnitKerja($user->unit_kerja);
        
        // Query dasar - permintaan yang butuh action dari Kabid:
        // 1. Permintaan baru dari Kepala Instalasi (pic_pimpinan = Kepala Bidang)
        // 2. Disposisi balik dari Direktur (cek disposisi dengan jabatan_tujuan sesuai unit kerja)
        $query = Permintaan::with(['user', 'notaDinas.disposisi'])
            ->where('status', 'proses')
            ->where(function($q) use ($user, $klasifikasiArray) {
                // Kondisi 1: Permintaan baru dari Kepala Instalasi
                // - pic_pimpinan = Kepala Bidang
                // - klasifikasi sesuai dengan unit kerja Kabid
                $q->where(function($subQ) use ($user, $klasifikasiArray) {
                    $subQ->where('pic_pimpinan', 'LIKE', '%Kepala Bidang%');
                    if ($klasifikasiArray) {
                        $subQ->whereIn('klasifikasi_permintaan', $klasifikasiArray);
                    }
                })
                // Kondisi 2: Disposisi balik dari Direktur
                // - Ada disposisi dengan jabatan_tujuan = unit kerja Kabid
                // - Catatan mengandung "Disetujui oleh Direktur"
                ->orWhere(function($subQ) use ($user) {
                    $subQ->where('kabid_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
                         ->whereHas('notaDinas.disposisi', function($dispQ) use ($user) {
                             $dispQ->where('jabatan_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
                                   ->where('catatan', 'LIKE', '%Disetujui oleh Direktur%');
                         });
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
            'klasifikasi' => $klasifikasiArray ? $klasifikasiArray[0] : null,
            'filters' => $request->only(['search', 'status', 'bidang', 'tanggal_dari', 'tanggal_sampai', 'per_page']),
        ]);
    }

    /**
     * Tampilkan detail permintaan
     */
    public function show(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Validasi: Cek apakah permintaan ini untuk Kabid ini berdasarkan klasifikasi
        $klasifikasiArray = $this->getKlasifikasiByUnitKerja($user->unit_kerja);
        
        // Cek apakah klasifikasi cocok
        $klasifikasiCocok = false;
        if ($klasifikasiArray && in_array($permintaan->klasifikasi_permintaan, $klasifikasiArray)) {
            $klasifikasiCocok = true;
        }
        
        // Cek apakah kabid_tujuan cocok (flexible match)
        $kabidCocok = false;
        if ($permintaan->kabid_tujuan) {
            // Match exact atau partial (untuk backward compatibility)
            if ($permintaan->kabid_tujuan === $user->unit_kerja ||
                str_contains($permintaan->kabid_tujuan, 'Umum') && str_contains($user->unit_kerja, 'Umum') ||
                str_contains($permintaan->kabid_tujuan, $user->unit_kerja) ||
                str_contains($user->unit_kerja, $permintaan->kabid_tujuan)) {
                $kabidCocok = true;
            }
        }
        
        // Jika tidak ada yang cocok, tolak akses
        if (!$klasifikasiCocok && !$kabidCocok) {
            abort(403, 'Permintaan ini bukan untuk bidang Anda.');
        }
        
        $permintaan->load(['user', 'notaDinas.disposisi']);
        
        // Get klasifikasi untuk info
        $klasifikasi = $permintaan->klasifikasi_permintaan;
        
        // Get timeline tracking
        $timeline = $permintaan->getTimelineTracking();
        $progress = $permintaan->getProgressPercentage();
        
        // Cek apakah ini disposisi balik dari Direktur
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        $isDisposisiDariDirektur = false;
        
        if ($notaDinas) {
            // Gunakan logic yang sama dengan approve method
            $isDisposisiDariDirektur = Disposisi::where('nota_id', $notaDinas->nota_id)
                ->where(function($q) use ($user) {
                    // Cek berdasarkan catatan "Disetujui oleh Direktur"
                    $q->where('catatan', 'like', '%Disetujui oleh Direktur%')
                      // ATAU jabatan_tujuan mengandung unit kerja Kabid ini
                      ->orWhere(function($subQ) use ($user) {
                          $subQ->where('jabatan_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
                               ->where('catatan', 'LIKE', '%Disetujui oleh Direktur%');
                      });
                })
                ->exists();
        }
        
        return Inertia::render('KepalaBidang/Show', [
            'permintaan' => $permintaan,
            'trackingStatus' => $permintaan->trackingStatus,
            'timeline' => $timeline,
            'progress' => $progress,
            'isDisposisiDariDirektur' => $isDisposisiDariDirektur,
            'userLogin' => $user,
            'klasifikasi' => $klasifikasi,
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
            ->where(function($q) use ($user) {
                // Cek berdasarkan catatan "Disetujui oleh Direktur"
                $q->where('catatan', 'like', '%Disetujui oleh Direktur%')
                  // ATAU jabatan_tujuan mengandung unit kerja Kabid ini
                  ->orWhere(function($subQ) use ($user) {
                      $subQ->where('jabatan_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
                           ->where('catatan', 'LIKE', '%Disetujui oleh Direktur%');
                  });
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
     * Reject permintaan - Kembalikan ke Kepala Instalasi (pemohon)
     */
    public function reject(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'alasan' => 'required|string',
        ]);

        // Update status permintaan - kembalikan ke Kepala Instalasi
        $permintaan->update([
            'status' => 'ditolak',
            'pic_pimpinan' => $permintaan->user->name ?? 'Kepala Instalasi', // Nama pemohon
            'deskripsi' => $permintaan->deskripsi . "\n\n[DITOLAK oleh Kepala Bidang] " . $data['alasan'],
        ]);

        // Ambil nota dinas terakhir dan buat disposisi penolakan ke Kepala Instalasi
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if ($notaDinas) {
            Disposisi::create([
                'nota_id' => $notaDinas->nota_id,
                'jabatan_tujuan' => $permintaan->user->jabatan ?? 'Kepala Instalasi', // Jabatan pemohon
                'tanggal_disposisi' => Carbon::now(),
                'catatan' => $data['alasan'],
                'status' => 'ditolak',
            ]);
        }

        return redirect()
            ->route('kepala-bidang.index')
            ->with('success', 'Permintaan ditolak dan dikembalikan ke Kepala Instalasi (Pemohon)');
    }

    /**
     * Request revisi dari Kepala Instalasi (pemohon)
     */
    public function requestRevision(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'catatan_revisi' => 'required|string',
        ]);

        // Update status permintaan - kembalikan ke Kepala Instalasi
        $permintaan->update([
            'status' => 'revisi',
            'pic_pimpinan' => $permintaan->user->name ?? 'Kepala Instalasi', // Nama pemohon
            'deskripsi' => $permintaan->deskripsi . "\n\n[CATATAN REVISI dari Kepala Bidang] " . $data['catatan_revisi'],
        ]);

        // Buat disposisi revisi ke Kepala Instalasi
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if ($notaDinas) {
            Disposisi::create([
                'nota_id' => $notaDinas->nota_id,
                'jabatan_tujuan' => $permintaan->user->jabatan ?? 'Kepala Instalasi', // Jabatan pemohon
                'tanggal_disposisi' => Carbon::now(),
                'catatan' => $data['catatan_revisi'],
                'status' => 'revisi',
            ]);
        }

        return redirect()
            ->route('kepala-bidang.index')
            ->with('success', 'Permintaan revisi telah dikirim ke Kepala Instalasi (Pemohon)');
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
     * Tampilkan daftar permintaan yang sudah disetujui Kabid (untuk tracking)
     */
    public function approved(Request $request)
    {
        $user = Auth::user();
        $klasifikasiArray = $this->getKlasifikasiByUnitKerja($user->unit_kerja);
        
        // Query - ambil permintaan yang SUDAH di-approve Kabid
        // Cek: Ada disposisi dari Kabid ke Direktur (artinya sudah di-approve)
        $query = Permintaan::with(['user', 'notaDinas.disposisi'])
            ->where(function($q) use ($klasifikasiArray) {
                if ($klasifikasiArray) {
                    $q->whereIn('klasifikasi_permintaan', $klasifikasiArray);
                }
            })
            ->whereHas('notaDinas.disposisi', function($q) {
                // Disposisi ke Direktur atau Staff Perencanaan (artinya sudah di-approve Kabid)
                $q->whereIn('jabatan_tujuan', ['Direktur', 'Staff Perencanaan'])
                  ->where('catatan', 'LIKE', '%Kepala Bidang%');
            })
            // Status bisa proses (di Direktur/Staff) atau disetujui (selesai)
            ->whereIn('status', ['proses', 'disetujui']);

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
