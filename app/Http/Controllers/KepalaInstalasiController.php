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
 * Controller untuk Kepala Instalasi
 * 
 * Tugas Kepala Instalasi:
 * 1. Menerima permintaan dari unit
 * 2. Mereview permintaan
 * 3. Menentukan klasifikasi permintaan dan routing ke Kabid yang sesuai
 * 4. Membuat Nota Dinas untuk dikirim ke Kepala Bidang
 * 5. Meneruskan ke bagian pengadaan
 * 
 * ROUTING BERDASARKAN KLASIFIKASI:
 * - Alat medis, obat → klasifikasi: 'medis' → Kabid Pelayanan Medis
 * - Reagen lab, film radiologi → klasifikasi: 'penunjang_medis' → Kabid Penunjang Medis
 * - Linen, IT, makanan → klasifikasi: 'non_medis' → Kabid Keperawatan/Bagian Umum
 */
class KepalaInstalasiController extends Controller
{
    /**
     * Mapping abbreviasi unit kerja ke nama lengkap
     * Untuk matching bidang permintaan dengan unit_kerja kepala instalasi
     */
    private function getUnitMapping()
    {
        return [
            'IGD' => 'Instalasi Gawat Darurat',
            'IRJ' => 'Instalasi Rawat Jalan',
            'IRJA' => 'Instalasi Rawat Jalan',
            'IRNA' => 'Instalasi Rawat Inap',
            'IBS' => 'Instalasi Bedah Sentral',
            'ICU' => 'Instalasi Intensif Care',
            'Farmasi' => 'Instalasi Farmasi',
            'Lab' => 'Instalasi Laboratorium Patologi Klinik',
            'Radiologi' => 'Instalasi Radiologi',
            'Rehab Medik' => 'Instalasi Rehabilitasi Medik',
            'Gizi' => 'Instalasi Gizi',
            'Forensik' => 'Instalasi Kedokteran Forensik dan Medikolegal',
            'Hemodialisa' => 'Unit Hemodialisa',
            'Bank Darah' => 'Unit Bank Darah Rumah Sakit',
            'Patologi Anatomi' => 'Unit Laboratorium Patologi Anatomi',
            'Sterilisasi' => 'Unit Sterilisasi Sentral',
            'Endoskopi' => 'Unit Endoskopi',
            'Pemasaran' => 'Unit Pemasaran dan Promosi Kesehatan Rumah Sakit',
            'Rekam Medik' => 'Unit Rekam Medik',
            'Pendidikan' => 'Instalasi Pendidikan dan Penelitian',
            'Pemeliharaan' => 'Instalasi Pemeliharaan Sarana',
            'Sanitasi' => 'Instalasi Penyehatan Lingkungan',
            'IT' => 'Unit Teknologi Informasi',
            'K3' => 'Unit Keselamatan dan Kesehatan Kerja Rumah Sakit',
            'Pengadaan' => 'Unit Pengadaan',
            'Logistik' => 'Unit Aset & Logistik',
            'Penjaminan' => 'Unit Penjaminan',
            'Pengaduan' => 'Unit Pengaduan',
        ];
    }

    /**
     * Get sub-departments under IRJA (Instalasi Rawat Jalan)
     * Semua departemen ini berada di bawah Kepala IRJA
     */
    private function getIRJADepartments()
    {
        return [
            'Poli Bedah',
            'Poli Gigi',
            'Poli Kulit Kelamin',
            'Poli Penyakit Dalam',
            'Poli Jiwa',
            'Poli Psikologi',
            'Poli Mata',
            'Klinik Gizi',
            'Laboratorium',
            'Apotek',
        ];
    }

    /**
     * Get sub-departments under IRNA (Instalasi Rawat Inap)
     * Semua ruangan ini berada di bawah Kepala IRNA
     */
    private function getIRNADepartments()
    {
        return [
            'Anggrek',
            'Bougenville',
            'Cempaka',
            'Dahlia',
            'Edelweiss',
            'Flamboyan',
            'Gardena',
            'Heliconia',
            'Ixia',
        ];
    }

    /**
     * Get sub-departments under IGD (Instalasi Gawat Darurat)
     * Semua departemen ini berada di bawah Kepala IGD
     */
    private function getIGDDepartments()
    {
        return [
            'UGD',
            'Triase',
            'Observasi',
            'Ruang Tindakan IGD',
        ];
    }

    /**
     * Tentukan klasifikasi permintaan berdasarkan unit kerja
     * 
     * MEDIS: IGD, Farmasi (obat), Bedah, Rawat Inap, Rawat Jalan, ICU
     * PENUNJANG MEDIS: Lab, Radiologi, Farmasi (alat)
     * NON MEDIS: Rekam Medik, Gizi, Sanitasi, Laundry, IT
     */
    private function determineKlasifikasi($bidang)
    {
        $medisUnits = [
            'IGD', 'Instalasi Gawat Darurat', 'Gawat Darurat',
            'IBS', 'Instalasi Bedah Sentral', 'Bedah Sentral',
            'ICU', 'Instalasi Intensif Care', 'ICU/ICCU',
            'IRJ', 'Instalasi Rawat Jalan', 'Rawat Jalan',
            'IRNA', 'Instalasi Rawat Inap', 'Rawat Inap',
        ];

        $penunjangMedisUnits = [
            'Lab', 'Instalasi Laboratorium', 'Laboratorium',
            'Radiologi', 'Instalasi Radiologi',
        ];

        $nonMedisUnits = [
            'Rekam Medik', 'Unit Rekam Medik',
            'Gizi', 'Instalasi Gizi',
            'Sanitasi', 'Instalasi Penyehatan Lingkungan', 'Sanitasi & Pemeliharaan',
            'Laundry', 'Linen', 'Laundry & Linen',
            'IT', 'Teknologi Informasi',
            'Pemeliharaan', 'Instalasi Pemeliharaan',
        ];

        foreach ($medisUnits as $unit) {
            if (stripos($bidang, $unit) !== false) {
                return 'medis';
            }
        }

        foreach ($penunjangMedisUnits as $unit) {
            if (stripos($bidang, $unit) !== false) {
                return 'penunjang_medis';
            }
        }

        foreach ($nonMedisUnits as $unit) {
            if (stripos($bidang, $unit) !== false) {
                return 'non_medis';
            }
        }

        // Default ke medis jika tidak bisa ditentukan
        return 'medis';
    }

    /**
     * Tentukan Kabid tujuan berdasarkan klasifikasi
     */
    private function getKabidTujuan($klasifikasi)
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
     * Get bidang variations untuk matching
     * Menghasilkan array kemungkinan nama bidang berdasarkan unit_kerja
     */
    private function getBidangVariations($unitKerja)
    {
        if (!$unitKerja) {
            return [];
        }

        $mapping = $this->getUnitMapping();
        $variations = [$unitKerja]; // Tambahkan unit_kerja original

        // Jika unit_kerja adalah abbreviasi, tambahkan nama lengkapnya
        if (isset($mapping[$unitKerja])) {
            $variations[] = $mapping[$unitKerja];
        }

        // Jika unit_kerja adalah nama lengkap, tambahkan abbreviasinya
        $reversedMapping = array_flip($mapping);
        if (isset($reversedMapping[$unitKerja])) {
            $variations[] = $reversedMapping[$unitKerja];
        }

        // SPECIAL CASE: Jika Kepala IRJA (Rawat Jalan), tambahkan semua sub-departments
        if (stripos($unitKerja, 'Rawat Jalan') !== false || 
            $unitKerja === 'IRJ' || 
            $unitKerja === 'IRJA') {
            $variations = array_merge($variations, $this->getIRJADepartments());
            $variations[] = 'IRJ';
            $variations[] = 'IRJA';
            $variations[] = 'Instalasi Rawat Jalan';
        }

        // SPECIAL CASE: Jika Kepala IRNA (Rawat Inap), tambahkan semua ruangan
        if (stripos($unitKerja, 'Rawat Inap') !== false || 
            $unitKerja === 'IRNA') {
            $variations = array_merge($variations, $this->getIRNADepartments());
            $variations[] = 'IRNA';
            $variations[] = 'Instalasi Rawat Inap';
        }

        // SPECIAL CASE: Jika Kepala IGD, tambahkan semua sub-departments
        if (stripos($unitKerja, 'Gawat Darurat') !== false || 
            $unitKerja === 'IGD') {
            $variations = array_merge($variations, $this->getIGDDepartments());
            $variations[] = 'IGD';
            $variations[] = 'Instalasi Gawat Darurat';
        }

        return array_unique($variations);
    }

    /**
     * Dashboard Kepala Instalasi
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Hitung statistik - ambil permintaan untuk unit kerja kepala instalasi
        // Filter berdasarkan bidang di tabel permintaan sesuai dengan unit_kerja
        $permintaans = Permintaan::with(['user', 'notaDinas'])
            ->where(function($query) use ($user) {
                if ($user->unit_kerja) {
                    // Get all possible bidang variations
                    $variations = $this->getBidangVariations($user->unit_kerja);
                    
                    // Match dengan salah satu variasi
                    $query->where(function($q) use ($variations, $user) {
                        foreach ($variations as $variation) {
                            $q->orWhere('bidang', $variation)
                              ->orWhere('bidang', 'LIKE', '%' . $variation . '%');
                        }
                    });
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
     * Hanya menampilkan permintaan untuk unit yang dipimpin
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Query dasar - filter berdasarkan unit_kerja
        $query = Permintaan::with(['user', 'notaDinas'])
            ->where(function($q) use ($user) {
                if ($user->unit_kerja) {
                    // Get all possible bidang variations
                    $variations = $this->getBidangVariations($user->unit_kerja);
                    
                    // Match dengan salah satu variasi
                    $q->where(function($subQuery) use ($variations) {
                        foreach ($variations as $variation) {
                            $subQuery->orWhere('bidang', $variation)
                                    ->orWhere('bidang', 'LIKE', '%' . $variation . '%');
                        }
                    });
                }
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
     * Hanya bisa melihat permintaan dari unit yang dipimpin
     */
    public function show(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek apakah kepala instalasi berhak melihat permintaan ini
        // Gunakan flexible matching seperti di index()
        if ($user->unit_kerja) {
            $variations = $this->getBidangVariations($user->unit_kerja);
            $isAuthorized = false;
            
            foreach ($variations as $variation) {
                if ($permintaan->bidang === $variation || 
                    stripos($permintaan->bidang, $variation) !== false) {
                    $isAuthorized = true;
                    break;
                }
            }
            
            if (!$isAuthorized) {
                return redirect()
                    ->route('kepala-instalasi.index')
                    ->with('error', 'Anda hanya dapat melihat permintaan dari unit kerja ' . $user->unit_kerja);
            }
        }
        
        // Load relasi
        $permintaan->load(['user', 'notaDinas.disposisi']);
        
        // Get timeline tracking
        $timeline = $permintaan->getTimelineTracking();
        $progress = $permintaan->getProgressPercentage();
        
        // Tentukan klasifikasi dan kabid tujuan untuk preview
        $klasifikasi = $permintaan->klasifikasi_permintaan ?? $this->determineKlasifikasi($permintaan->bidang);
        $kabidTujuan = $this->getKabidTujuan($klasifikasi);
        
        return Inertia::render('KepalaInstalasi/Show', [
            'permintaan' => $permintaan,
            'trackingStatus' => $permintaan->trackingStatus,
            'timeline' => $timeline,
            'progress' => $progress,
            'userLogin' => $user,
            'klasifikasi' => $klasifikasi,
            'kabidTujuan' => $kabidTujuan,
        ]);
    }

    /**
     * Tampilkan timeline tracking untuk permintaan
     * Dedicated page untuk melihat tracking detail
     */
    public function tracking(Permintaan $permintaan)
    {
        $user = Auth::user();
        
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
     * Approve permintaan - Otomatis teruskan ke Kepala Bidang
     * Membuat Nota Dinas DAN Disposisi secara otomatis
     * Hanya bisa approve permintaan dari unit yang dipimpin
     */
    public function approve(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek authorization dengan flexible matching
        if ($user->unit_kerja) {
            $variations = $this->getBidangVariations($user->unit_kerja);
            $isAuthorized = false;
            
            foreach ($variations as $variation) {
                if ($permintaan->bidang === $variation || 
                    stripos($permintaan->bidang, $variation) !== false) {
                    $isAuthorized = true;
                    break;
                }
            }
            
            if (!$isAuthorized) {
                return redirect()
                    ->route('kepala-instalasi.index')
                    ->with('error', 'Anda hanya dapat menyetujui permintaan dari unit kerja ' . $user->unit_kerja);
            }
        }
        
        // Validasi input - bisa dengan atau tanpa catatan
        $data = $request->validate([
            'catatan' => 'nullable|string',
            'klasifikasi_permintaan' => 'nullable|in:medis,penunjang_medis,non_medis',
        ]);

        // Tentukan klasifikasi jika belum ada atau di-override
        $klasifikasi = $data['klasifikasi_permintaan'] ?? 
                       $permintaan->klasifikasi_permintaan ?? 
                       $this->determineKlasifikasi($permintaan->bidang);
        
        // Tentukan Kabid tujuan berdasarkan klasifikasi
        $kabidTujuan = $this->getKabidTujuan($klasifikasi);

        // Update status permintaan dan set klasifikasi + kabid_tujuan
        $permintaan->update([
            'status' => 'proses', // Masih proses karena menunggu Kepala Bidang
            'pic_pimpinan' => 'Kepala Bidang', // Diteruskan ke Kepala Bidang
            'klasifikasi_permintaan' => $klasifikasi,
            'kabid_tujuan' => $kabidTujuan,
        ]);

        // Buat nota dinas ke Kepala Bidang
        $notaDinas = NotaDinas::create([
            'permintaan_id' => $permintaan->permintaan_id,
            'no_nota' => $permintaan->no_nota_dinas ?? 'ND/' . date('Y/m/d') . '/' . $permintaan->permintaan_id,
            'dari' => $user->unit_kerja ?? $user->jabatan ?? 'Kepala Instalasi',
            'kepada' => $kabidTujuan, // Kirim ke Kabid yang sesuai
            'tanggal_nota' => Carbon::now(),
            'perihal' => 'Persetujuan Permintaan [' . strtoupper($klasifikasi) . '] - ' . substr($permintaan->deskripsi, 0, 100),
        ]);

        // OTOMATIS BUAT DISPOSISI ke Kepala Bidang yang sesuai
        Disposisi::create([
            'nota_id' => $notaDinas->nota_id,
            'jabatan_tujuan' => $kabidTujuan,
            'tanggal_disposisi' => Carbon::now(),
            'catatan' => ($data['catatan'] ?? 'Mohon review dan persetujuan') . 
                        "\n\nKlasifikasi: " . strtoupper(str_replace('_', ' ', $klasifikasi)) .
                        "\nDiteruskan ke: " . $kabidTujuan,
            'status' => 'pending',
        ]);

        $message = 'Permintaan disetujui dan otomatis diteruskan ke ' . $kabidTujuan . 
                   ' (Klasifikasi: ' . strtoupper(str_replace('_', ' ', $klasifikasi)) . ')';
        if (isset($data['catatan']) && $data['catatan']) {
            $message .= ' dengan catatan';
        }

        return redirect()
            ->route('kepala-instalasi.index')
            ->with('success', $message);
    }

    /**
     * Reject permintaan
     * Hanya bisa menolak permintaan dari unit yang dipimpin
     */
    public function reject(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek authorization dengan flexible matching
        if ($user->unit_kerja) {
            $variations = $this->getBidangVariations($user->unit_kerja);
            $isAuthorized = false;
            
            foreach ($variations as $variation) {
                if ($permintaan->bidang === $variation || 
                    stripos($permintaan->bidang, $variation) !== false) {
                    $isAuthorized = true;
                    break;
                }
            }
            
            if (!$isAuthorized) {
                return redirect()
                    ->route('kepala-instalasi.index')
                    ->with('error', 'Anda hanya dapat menolak permintaan dari unit kerja ' . $user->unit_kerja);
            }
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
        
        // Cek authorization dengan flexible matching
        if ($user->unit_kerja) {
            $variations = $this->getBidangVariations($user->unit_kerja);
            $isAuthorized = false;
            
            foreach ($variations as $variation) {
                if ($permintaan->bidang === $variation || 
                    stripos($permintaan->bidang, $variation) !== false) {
                    $isAuthorized = true;
                    break;
                }
            }
            
            if (!$isAuthorized) {
                return redirect()
                    ->route('kepala-instalasi.index')
                    ->with('error', 'Anda hanya dapat meminta revisi permintaan dari unit kerja ' . $user->unit_kerja);
            }
        }
        
        // Validasi dengan min 5 karakter agar lebih fleksibel
        $data = $request->validate([
            'catatan_revisi' => 'required|string|min:5',
        ], [
            'catatan_revisi.required' => 'Catatan revisi wajib diisi',
            'catatan_revisi.min' => 'Catatan revisi minimal 5 karakter',
        ]);

        try {
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
                'dari' => $user->unit_kerja ?? $user->jabatan ?? 'Kepala Instalasi',
                'kepada' => $permintaan->user->jabatan ?? $permintaan->user->name ?? 'Staff Unit',
                'tanggal_nota' => Carbon::now(),
                'perihal' => 'Permintaan Revisi - ' . substr($data['catatan_revisi'], 0, 100),
                'detail' => $data['catatan_revisi'],
            ]);

            return redirect()
                ->route('kepala-instalasi.index')
                ->with('success', 'Permintaan revisi telah dikirim ke ' . ($permintaan->user->name ?? 'pemohon') . ' untuk diperbaiki');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Review kembali permintaan yang ditolak oleh Kepala Bidang
     * Kepala Instalasi dapat memperbaiki dan mengajukan kembali
     */
    public function reviewRejected(Permintaan $permintaan)
    {
        $user = Auth::user();
        
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

    /**
     * Cetak Nota Dinas
     * Generate HTML untuk cetak nota dinas (sama seperti admin)
     */
    public function cetakNotaDinas(Permintaan $permintaan)
    {
        // Load nota dinas terkait
        $permintaan->load(['notaDinas', 'user']);

        // Ambil nota dinas pertama (atau yang terbaru)
        $notaDinas = $permintaan->notaDinas()->latest('created_at')->first();

        if (!$notaDinas) {
            return redirect()->route('kepala-instalasi.show', $permintaan)
                ->with('error', 'Nota Dinas tidak ditemukan untuk permintaan ini.');
        }

        // Return view untuk cetak
        return view('cetak.nota-dinas', [
            'permintaan' => $permintaan,
            'notaDinas' => $notaDinas,
        ]);
    }

    /**
     * Lihat/Download Lampiran Nota Dinas
     * (sama seperti admin)
     */
    public function lihatLampiran(NotaDinas $notaDinas)
    {
        // Validasi apakah nota dinas punya lampiran
        if (!$notaDinas->lampiran) {
            return redirect()->back()->with('error', 'Lampiran tidak ditemukan untuk nota dinas ini.');
        }

        // Jika lampiran adalah URL (http/https), redirect ke URL tersebut
        if (filter_var($notaDinas->lampiran, FILTER_VALIDATE_URL)) {
            return redirect($notaDinas->lampiran);
        }

        // Jika lampiran adalah file path, coba untuk menampilkan atau download
        $filePath = storage_path('app/public/' . $notaDinas->lampiran);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File lampiran tidak ditemukan di server.');
        }

        // Return file untuk di-download atau ditampilkan
        return response()->file($filePath);
    }
}

