<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use App\Models\Disposisi;
use App\Models\DokumenPengadaan;
use App\Models\Perencanaan;
use App\Models\Hps;
use App\Models\HpsItem;
use App\Models\SpesifikasiTeknis;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * Controller untuk Staff Perencanaan
 * 
 * Tugas Staff Perencanaan:
 * 1. Menerima permintaan yang sudah disetujui Direktur
 * 2. Membuat perencanaan pengadaan
 * 3. Koordinasi dengan bagian terkait (KSO, Pengadaan)
 * 4. Membuat disposisi ke bagian pelaksana
 */
class StaffPerencanaanController extends Controller
{
    /**
     * Dashboard Staff Perencanaan
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ambil semua permintaan yang ditujukan ke Staff Perencanaan
        // Permintaan yang pic_pimpinan = 'Staff Perencanaan' dan status 'disetujui'
        $permintaans = Permintaan::with(['user', 'notaDinas', 'notaDinas.disposisi'])
            ->where(function($q) use ($user) {
                $q->where('pic_pimpinan', 'Staff Perencanaan')
                  ->orWhere('pic_pimpinan', $user->nama);
            })
            ->whereIn('status', ['disetujui', 'proses'])
            ->get();

        $stats = [
            'total' => $permintaans->count(),
            'belum_diproses' => $permintaans->where('status', 'disetujui')->count(),
            'sedang_diproses' => $permintaans->where('status', 'proses')->count(),
            'selesai' => Permintaan::where('status', 'selesai')
                ->where('deskripsi', 'like', '%Staff Perencanaan%')
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

        return Inertia::render('StaffPerencanaan/Dashboard', [
            'stats' => $stats,
            'recentPermintaans' => $recentPermintaans,
            'userLogin' => $user,
        ]);
    }

    /**
     * Tampilkan daftar permintaan untuk Staff Perencanaan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Query dasar - hanya permintaan yang ditujukan ke Staff Perencanaan
        $query = Permintaan::with(['user', 'notaDinas', 'notaDinas.disposisi'])
            ->where(function($q) use ($user) {
                $q->where('pic_pimpinan', 'Staff Perencanaan')
                  ->orWhere('pic_pimpinan', $user->nama);
            })
            ->whereIn('status', ['disetujui', 'proses']);

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

        return Inertia::render('StaffPerencanaan/Index', [
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
        
        
        $permintaan->load(['user', 'notaDinas.disposisi', 'hps.items']);
        
        // Get timeline tracking
        $timeline = $permintaan->getTimelineTracking();
        $progress = $permintaan->getProgressPercentage();
        
        // Cek apakah dokumen sudah ada
        $hasNotaDinas = $permintaan->notaDinas()->exists();
        
        // Cek DPP via Perencanaan
        $hasDPP = Perencanaan::whereHas('disposisi.notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->exists();
        
        // Cek HPS
        $hasHPS = $permintaan->hps()->exists();
        
        // Cek Disposisi
        $hasDisposisi = Disposisi::whereHas('notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->exists();
        
        // Cek Nota Dinas Pembelian - untuk sementara cek dari tipe_nota jika ada di nota_dinas
        $hasNotaDinasPembelian = NotaDinas::where('permintaan_id', $permintaan->permintaan_id)
            ->where('tipe_nota', 'pembelian')
            ->exists();
        
        // Cek Spesifikasi Teknis
        $hasSpesifikasiTeknis = $permintaan->spesifikasiTeknis()->exists();
        
        return Inertia::render('StaffPerencanaan/Show', [
            'permintaan' => $permintaan,
            'trackingStatus' => $permintaan->trackingStatus,
            'timeline' => $timeline,
            'progress' => $progress,
            'userLogin' => $user,
            'hasNotaDinas' => $hasNotaDinas,
            'hasDPP' => $hasDPP,
            'hasHPS' => $hasHPS,
            'hasDisposisi' => $hasDisposisi,
            'hasNotaDinasPembelian' => $hasNotaDinasPembelian,
            'hasSpesifikasiTeknis' => $hasSpesifikasiTeknis,
        ]);
    }

    /**
     * Form membuat perencanaan
     */
    public function createPerencanaan(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $permintaan->load(['user', 'notaDinas']);
        
        return Inertia::render('StaffPerencanaan/CreatePerencanaan', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Simpan perencanaan dan disposisi ke bagian terkait
     */
    public function storePerencanaan(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'metode_pengadaan' => 'required|string', // Lelang, Penunjukan Langsung, dll
            'estimasi_biaya' => 'required|numeric',
            'sumber_dana' => 'required|string',
            'jadwal_pelaksanaan' => 'required|date',
            'catatan_perencanaan' => 'nullable|string',
            'disposisi_ke' => 'required|string', // Bagian KSO, Bagian Pengadaan, dll
        ]);

        // Ambil nota dinas terakhir
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if (!$notaDinas) {
            return redirect()->back()->withErrors(['error' => 'Nota dinas tidak ditemukan']);
        }

        // Simpan data perencanaan di deskripsi (atau bisa buat tabel perencanaan terpisah)
        $perencanaanInfo = "\n\n[PERENCANAAN oleh Staff Perencanaan]" .
                          "\nMetode: " . $data['metode_pengadaan'] .
                          "\nEstimasi Biaya: Rp " . number_format($data['estimasi_biaya'], 0, ',', '.') .
                          "\nSumber Dana: " . $data['sumber_dana'] .
                          "\nJadwal: " . Carbon::parse($data['jadwal_pelaksanaan'])->format('d/m/Y') .
                          "\nCatatan: " . ($data['catatan_perencanaan'] ?? '-');

        // Update deskripsi permintaan dengan info perencanaan
        $permintaan->update([
            'deskripsi' => $permintaan->deskripsi . $perencanaanInfo,
        ]);

        // Buat disposisi ke bagian terkait
        Disposisi::create([
            'nota_id' => $notaDinas->nota_id,
            'jabatan_tujuan' => $data['disposisi_ke'],
            'tanggal_disposisi' => Carbon::now(),
            'catatan' => "Perencanaan telah dibuat. Metode: {$data['metode_pengadaan']}. " . 
                        ($data['catatan_perencanaan'] ?? 'Silakan lanjutkan proses pengadaan.'),
            'status' => 'disetujui',
        ]);

        // Update status permintaan
        $permintaan->update([
            'status' => 'proses',
            'pic_pimpinan' => $data['disposisi_ke'],
        ]);

        return redirect()
            ->route('staff-perencanaan.index')
            ->with('success', 'Perencanaan berhasil dibuat dan didisposisi ke ' . $data['disposisi_ke']);
    }

    /**
     * Form membuat Nota Dinas Usulan
     */
    public function createNotaDinas(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $permintaan->load('user');
        
        return Inertia::render('StaffPerencanaan/CreateNotaDinas', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Store Nota Dinas Usulan
     */
    public function storeNotaDinas(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'nomor' => 'required|string',
            'tanggal_nota' => 'required|date',
            'usulan_ruangan' => 'required|string',
            'sifat' => 'required|in:Sangat Segera,Segera,Biasa,Rahasia',
            'perihal' => 'required|string',
            'dari' => 'required|string',
            'kepada' => 'required|string',
            'isi_nota' => 'nullable|string',
            'penerima' => 'nullable|string',
            'kode_program' => 'nullable|string',
            'kode_kegiatan' => 'nullable|string',
            'kode_rekening' => 'nullable|string',
            'uraian' => 'nullable|string',
            'pagu_anggaran' => 'required|numeric|min:0',
            'pph' => 'nullable|numeric|min:0',
            'ppn' => 'nullable|numeric|min:0',
            'pph_21' => 'nullable|numeric|min:0',
            'pph_4_2' => 'nullable|numeric|min:0',
            'pph_22' => 'nullable|numeric|min:0',
            'unit_instalasi' => 'nullable|string',
            'no_faktur_pajak' => 'nullable|string',
            'no_kwitansi' => 'nullable|string',
            'tanggal_faktur_pajak' => 'nullable|date',
        ]);

        $data['permintaan_id'] = $permintaan->permintaan_id;

        // Generate nomor nota otomatis jika kosong
        if (empty($data['nomor'])) {
            $lastNota = NotaDinas::whereYear('tanggal_nota', date('Y'))
                ->orderBy('nota_id', 'desc')
                ->first();
            $nextNumber = $lastNota ? (intval(substr($lastNota->nomor, 0, 3)) + 1) : 1;
            $data['nomor'] = sprintf('%03d/ND-SP/%s', $nextNumber, date('Y'));
        }

        // Set no_nota sama dengan nomor - REQUIRED field
        $data['no_nota'] = $data['nomor'];
        
        // Set tipe_nota untuk pembelian (usulan)
        $data['tipe_nota'] = 'usulan';
        
        // Set isi_nota default jika tidak ada
        if (empty($data['isi_nota'])) {
            $data['isi_nota'] = $data['perihal'];
        }

        $notaDinas = NotaDinas::create($data);

        // Update status permintaan
        $permintaan->update([
            'status' => 'proses',
            'pic_pimpinan' => $data['kepada'],
        ]);

        return redirect()
            ->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Nota Dinas Usulan berhasil dibuat');
    }

    /**
     * Form membuat Nota Dinas Pembelian
     */
    public function createNotaDinasPembelian(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $permintaan->load('user');
        
        return Inertia::render('StaffPerencanaan/CreateNotaDinasPembelian', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Form disposisi manual
     */
    public function createDisposisi(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $permintaan->load(['user', 'notaDinas']);
        
        return Inertia::render('StaffPerencanaan/CreateDisposisi', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Form membuat DPP (Dokumen Persiapan Pengadaan)
     */
    public function createDPP(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $permintaan->load('user');
        
        return Inertia::render('StaffPerencanaan/CreateDPP', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Store DPP (Dokumen Persiapan Pengadaan)
     */
    public function storeDPP(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            // PPK dan Identifikasi
            'ppk_ditunjuk' => 'required|string',
            'nama_paket' => 'required|string',
            'lokasi' => 'required|string',
            
            // Program dan Kegiatan
            'uraian_program' => 'required|string',
            'uraian_kegiatan' => 'required|string',
            'sub_kegiatan' => 'nullable|string',
            'sub_sub_kegiatan' => 'nullable|string',
            'kode_rekening' => 'required|string',
            
            // Anggaran dan HPS
            'sumber_dana' => 'required|string',
            'pagu_paket' => 'required|numeric|min:0',
            'nilai_hps' => 'required|numeric|min:0',
            'sumber_data_survei_hps' => 'required|string',
            
            // Detail Pengadaan
            'nama_kegiatan' => 'required|string',
            'jenis_pengadaan' => 'required|string',
        ]);

        // Ambil nota dinas terakhir untuk membuat disposisi
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if (!$notaDinas) {
            return redirect()->back()->withErrors(['error' => 'Nota dinas tidak ditemukan']);
        }

        // Buat Nota Dinas untuk DPP jika belum ada
        if (!$notaDinas) {
            // Generate nomor nota otomatis
            $lastNota = NotaDinas::whereYear('tanggal_nota', date('Y'))
                ->orderBy('nota_id', 'desc')
                ->first();
            $nextNumber = $lastNota ? (intval(substr($lastNota->nomor, 0, 3)) + 1) : 1;
            $nomorNota = sprintf('%03d/ND-DPP/%s', $nextNumber, date('Y'));
            
            $notaDinas = NotaDinas::create([
                'permintaan_id' => $permintaan->permintaan_id,
                'no_nota' => $nomorNota,
                'nomor' => $nomorNota,
                'tanggal_nota' => Carbon::now(),
                'dari' => 'Staff Perencanaan',
                'kepada' => 'Bagian Pengadaan',
                'perihal' => "DPP - {$data['nama_paket']}",
                'sifat' => 'Biasa',
                'tipe_nota' => 'dpp',
                'isi_nota' => "Dokumen Persiapan Pengadaan untuk paket: {$data['nama_paket']}",
            ]);
        }

        // Buat disposisi untuk DPP - dikirim ke Bagian Pengadaan
        $disposisi = Disposisi::create([
            'nota_id' => $notaDinas->nota_id,
            'jabatan_tujuan' => 'Bagian Pengadaan',
            'tanggal_disposisi' => Carbon::now(),
            'catatan' => "DPP telah dibuat untuk paket: {$data['nama_paket']}. Mohon ditindaklanjuti ke Bagian KSO setelah proses pengadaan.",
            'status' => 'dalam_proses',
        ]);

        // Simpan DPP ke tabel perencanaan
        $data['disposisi_id'] = $disposisi->disposisi_id;
        $data['rencana_kegiatan'] = $data['nama_kegiatan'];
        $data['anggaran'] = $data['pagu_paket'];
        
        Perencanaan::create($data);

        // Update status permintaan - dikirim ke Bagian Pengadaan
        $permintaan->update([
            'status' => 'proses',
            'pic_pimpinan' => 'Bagian Pengadaan',
            'deskripsi' => $permintaan->deskripsi . "\n\n[DPP DIBUAT - WORKFLOW: Perencanaan → Pengadaan → KSO]\n" .
                          "Nama Paket: {$data['nama_paket']}\n" .
                          "PPK: {$data['ppk_ditunjuk']}\n" .
                          "Nilai HPS: Rp " . number_format($data['nilai_hps'], 0, ',', '.'),
        ]);

        return redirect()
            ->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Dokumen Persiapan Pengadaan (DPP) berhasil dibuat');
    }

    /**
     * Form membuat HPS (Harga Perkiraan Satuan)
     */
    public function createHPS(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $permintaan->load('user');
        
        return Inertia::render('StaffPerencanaan/CreateHPS', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Store HPS (Harga Perkiraan Satuan)
     */
    public function storeHPS(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'ppk' => 'required|string',
            'surat_penawaran_harga' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.nama_item' => 'required|string',
            'items.*.volume' => 'required|integer|min:1',
            'items.*.satuan' => 'required|string',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.type' => 'nullable|string',
            'items.*.merk' => 'nullable|string',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        // Hitung grand total
        $grandTotal = 0;
        foreach ($data['items'] as $item) {
            $grandTotal += $item['total'];
        }

        // Buat HPS header
        $hps = Hps::create([
            'permintaan_id' => $permintaan->permintaan_id,
            'ppk' => $data['ppk'],
            'surat_penawaran_harga' => $data['surat_penawaran_harga'],
            'grand_total' => $grandTotal,
        ]);

        // Buat HPS items
        foreach ($data['items'] as $item) {
            HpsItem::create([
                'hps_id' => $hps->hps_id,
                'nama_item' => $item['nama_item'],
                'volume' => $item['volume'],
                'satuan' => $item['satuan'],
                'harga_satuan' => $item['harga_satuan'],
                'type' => $item['type'] ?? null,
                'merk' => $item['merk'] ?? null,
                'total' => $item['total'],
            ]);
        }

        // Update permintaan
        $permintaan->update([
            'deskripsi' => $permintaan->deskripsi . "\n\n[HPS DIBUAT]\n" .
                          "PPK: {$data['ppk']}\n" .
                          "Surat Penawaran: {$data['surat_penawaran_harga']}\n" .
                          "Total Item: " . count($data['items']) . "\n" .
                          "Grand Total: Rp " . number_format($grandTotal, 0, ',', '.'),
        ]);

        return redirect()
            ->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Harga Perkiraan Satuan (HPS) berhasil dibuat dengan ' . count($data['items']) . ' item');
    }

    /**
     * Form disposisi manual
     */
    public function createDisposisiOld2(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $permintaan->load(['user', 'notaDinas']);
        
        return Inertia::render('StaffPerencanaan/CreateDisposisi', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Store disposisi manual
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
            ->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Disposisi berhasil dibuat dan dikirim ke ' . $data['jabatan_tujuan']);
    }

    /**
     * Forward ke Bagian Pengadaan setelah semua dokumen lengkap
     * Workflow: Perencanaan → Pengadaan → KSO
     */
    public function forwardToPengadaan(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($permintaan->pic_pimpinan !== 'Staff Perencanaan' && $permintaan->pic_pimpinan !== $user->nama) {
            abort(403, 'Anda tidak memiliki akses untuk memforward permintaan ini.');
        }

        // Validasi semua dokumen sudah lengkap
        $hasNotaDinas = $permintaan->notaDinas()->exists();
        $hasDPP = Perencanaan::whereHas('disposisi.notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->exists();
        $hasHPS = $permintaan->hps()->exists();
        $hasNotaDinasPembelian = NotaDinas::where('permintaan_id', $permintaan->permintaan_id)
            ->where('tipe_nota', 'pembelian')
            ->exists();
        $hasSpesifikasiTeknis = $permintaan->spesifikasiTeknis()->exists();

        if (!$hasNotaDinas || !$hasDPP || !$hasHPS || !$hasNotaDinasPembelian || !$hasSpesifikasiTeknis) {
            return redirect()->back()->withErrors([
                'error' => 'Semua dokumen harus lengkap sebelum dikirim ke Bagian Pengadaan. ' .
                          'Pastikan Nota Dinas, DPP, HPS, Nota Dinas Pembelian, dan Spesifikasi Teknis sudah dibuat.'
            ]);
        }

        // Ambil nota dinas terakhir
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if (!$notaDinas) {
            return redirect()->back()->withErrors(['error' => 'Nota dinas tidak ditemukan.']);
        }

        // Buat disposisi ke Bagian Pengadaan
        Disposisi::create([
            'nota_id' => $notaDinas->nota_id,
            'jabatan_tujuan' => 'Bagian Pengadaan',
            'tanggal_disposisi' => Carbon::now(),
            'catatan' => $request->input('catatan', 'Semua dokumen perencanaan telah lengkap. Mohon ditindaklanjuti untuk proses pengadaan dan selanjutnya ke Bagian KSO.'),
            'status' => 'dalam_proses',
        ]);

        // Update status permintaan - dikirim ke Bagian Pengadaan
        $permintaan->update([
            'status' => 'proses',
            'pic_pimpinan' => 'Bagian Pengadaan',
            'deskripsi' => $permintaan->deskripsi . "\n\n[DOKUMEN LENGKAP - DIKIRIM KE PENGADAAN]\n" .
                          "Tanggal: " . Carbon::now()->format('d/m/Y H:i') . "\n" .
                          "Semua dokumen perencanaan sudah lengkap:\n" .
                          "✓ Nota Dinas\n" .
                          "✓ DPP (Dokumen Persiapan Pengadaan)\n" .
                          "✓ HPS (Harga Perkiraan Satuan)\n" .
                          "✓ Nota Dinas Pembelian\n" .
                          "✓ Spesifikasi Teknis\n" .
                          "Workflow: Staff Perencanaan → Bagian Pengadaan → Bagian KSO",
        ]);

        return redirect()
            ->route('staff-perencanaan.index')
            ->with('success', 'Permintaan berhasil dikirim ke Bagian Pengadaan dengan semua dokumen lengkap.');
    }

    /**
     * Form upload dokumen pengadaan
     */
    public function uploadDokumen(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        // Load dokumen yang sudah ada
        $permintaan->load(['user', 'notaDinas', 'dokumenPengadaan']);
        
        // Definisi 6 jenis dokumen yang diperlukan
        $jenisDokumen = [
            'Nota Dinas' => 'Nota Dinas',
            'DPP' => 'DPP (Dokumen Perencanaan Pengadaan)',
            'KAK' => 'KAK (Kerangka Acuan Kerja)',
            'SP' => 'SP (Surat Pesanan)',
            'Kuitansi' => 'Kuitansi',
            'BAST' => 'Berita Acara Serah Terima'
        ];
        
        // Map dokumen yang sudah diupload
        $dokumenDiupload = [];
        foreach ($permintaan->dokumenPengadaan as $dok) {
            $dokumenDiupload[$dok->jenis_dokumen] = [
                'dokumen_id' => $dok->dokumen_id,
                'nama_file' => $dok->nama_file,
                'file_path' => $dok->file_path,
                'tanggal_upload' => $dok->tanggal_upload,
            ];
        }
        
        return Inertia::render('StaffPerencanaan/ScanBerkas', [
            'permintaan' => $permintaan,
            'jenisDokumen' => $jenisDokumen,
            'dokumenDiupload' => $dokumenDiupload,
            'progress' => [
                'uploaded' => count($dokumenDiupload),
                'total' => count($jenisDokumen),
            ],
        ]);
    }

    /**
     * Store/Update dokumen pengadaan
     */
    public function storeDokumen(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'jenis_dokumen' => 'required|in:Nota Dinas,DPP,KAK,SP,Kuitansi,BAST',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10MB
        ]);

        // Cek apakah dokumen jenis ini sudah ada
        $existingDokumen = DokumenPengadaan::where('permintaan_id', $permintaan->permintaan_id)
            ->where('jenis_dokumen', $data['jenis_dokumen'])
            ->first();

        // Upload file
        $file = $request->file('file');
        $fileName = time() . '_' . str_replace(' ', '_', $data['jenis_dokumen']) . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('dokumen_pengadaan', $fileName, 'public');

        if ($existingDokumen) {
            // Delete old file
            if (Storage::disk('public')->exists($existingDokumen->link_file)) {
                Storage::disk('public')->delete($existingDokumen->link_file);
            }
            
            // Update existing
            $existingDokumen->update([
                'nama_file' => $fileName,
                'link_file' => $filePath,
                'tanggal_upload' => Carbon::now(),
                'uploaded_by' => $user->nama,
            ]);
            
            $message = 'Dokumen berhasil diperbarui';
        } else {
            // Create new
            DokumenPengadaan::create([
                'permintaan_id' => $permintaan->permintaan_id,
                'jenis_dokumen' => $data['jenis_dokumen'],
                'nama_file' => $fileName,
                'link_file' => $filePath,
                'tanggal_upload' => Carbon::now(),
                'uploaded_by' => $user->nama,
            ]);
            
            $message = 'Dokumen berhasil diupload';
        }

        // Cek apakah semua dokumen sudah lengkap (6 dokumen)
        $totalDokumen = DokumenPengadaan::where('permintaan_id', $permintaan->permintaan_id)->count();
        
        if ($totalDokumen >= 6) {
            // Update status ke proses dan PIC ke Bagian Pengadaan
            $permintaan->update([
                'status' => 'proses',
                'pic_pimpinan' => 'Bagian Pengadaan',
            ]);
            
            // Buat disposisi otomatis ke Bagian Pengadaan
            $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
            if ($notaDinas) {
                Disposisi::create([
                    'nota_id' => $notaDinas->nota_id,
                    'jabatan_tujuan' => 'Bagian Pengadaan',
                    'tanggal_disposisi' => Carbon::now(),
                    'catatan' => 'Semua dokumen pengadaan telah lengkap. Silakan lanjutkan proses pengadaan.',
                    'status' => 'disetujui',
                ]);
            }
            
            $message .= '. Semua dokumen lengkap, permintaan diteruskan ke Bagian Pengadaan.';
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Download dokumen
     */
    public function downloadDokumen(Permintaan $permintaan, DokumenPengadaan $dokumen)
    {
        $user = Auth::user();
        
        // Cek apakah dokumen milik permintaan ini
        if ($dokumen->permintaan_id !== $permintaan->permintaan_id) {
            return redirect()->back()->withErrors(['error' => 'Dokumen tidak sesuai dengan permintaan.']);
        }

        if (!Storage::disk('public')->exists($dokumen->link_file)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($dokumen->link_file, $dokumen->nama_file);
    }

    /**
     * Hapus dokumen
     */
    public function deleteDokumen(Permintaan $permintaan, DokumenPengadaan $dokumen)
    {
        $user = Auth::user();
        
        // Cek apakah dokumen milik permintaan ini
        if ($dokumen->permintaan_id !== $permintaan->permintaan_id) {
            return redirect()->back()->withErrors(['error' => 'Dokumen tidak sesuai dengan permintaan.']);
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($dokumen->link_file)) {
            Storage::disk('public')->delete($dokumen->link_file);
        }

        // Delete from database
        $dokumen->delete();

        return redirect()
            ->back()
            ->with('success', 'Dokumen berhasil dihapus');
    }

    /**
     * Tampilkan timeline tracking untuk permintaan
     * Untuk melihat progress permintaan yang sudah diproses
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

        return Inertia::render('StaffPerencanaan/Tracking', [
            'permintaan' => $permintaan,
            'timeline' => $timeline,
            'progress' => $progress,
            'completedSteps' => $completedSteps,
            'pendingSteps' => array_values($pendingSteps),
            'userLogin' => $user,
        ]);
    }

    /**
     * Tampilkan daftar permintaan yang sudah diproses (untuk tracking)
     */
    public function approved(Request $request)
    {
        $user = Auth::user();
        
        // Query - ambil semua permintaan yang sudah pernah melalui Staff Perencanaan
        $query = Permintaan::with(['user', 'notaDinas.disposisi'])
            ->whereHas('notaDinas.disposisi', function($q) use ($user) {
                // Cari disposisi yang pernah ditujukan ke Staff Perencanaan
                $q->where('jabatan_tujuan', 'like', '%Staff Perencanaan%')
                  ->orWhere('jabatan_tujuan', $user->jabatan);
            })
            ->whereIn('status', ['proses', 'disetujui', 'ditolak', 'revisi', 'selesai']);

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

        return Inertia::render('StaffPerencanaan/Approved', [
            'permintaans' => $permintaans,
            'userLogin' => $user,
            'filters' => $request->only(['search', 'bidang', 'status', 'tanggal_dari', 'tanggal_sampai', 'per_page']),
        ]);
    }

    // ==================== CRUD untuk Perencanaan ====================
    public function editPerencanaan(Permintaan $permintaan)
    {
        // Perencanaan via Nota Dinas -> Disposisi -> Perencanaan
        $perencanaan = Perencanaan::whereHas('disposisi.notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->first();
        
        if (!$perencanaan) {
            return redirect()->back()->with('error', 'Data perencanaan tidak ditemukan');
        }

        return Inertia::render('StaffPerencanaan/CreatePerencanaan', [
            'permintaan' => $permintaan,
            'perencanaan' => $perencanaan,
            'isEdit' => true,
        ]);
    }

    public function updatePerencanaan(Request $request, Permintaan $permintaan)
    {
        $perencanaan = Perencanaan::whereHas('disposisi.notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->first();
        
        if (!$perencanaan) {
            return redirect()->back()->with('error', 'Data perencanaan tidak ditemukan');
        }

        $validated = $request->validate([
            'rencana_kegiatan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'anggaran' => 'nullable|numeric',
            'metode_pengadaan' => 'nullable|string',
        ]);

        $perencanaan->update($validated);

        return redirect()->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Data perencanaan berhasil diupdate');
    }

    public function deletePerencanaan(Permintaan $permintaan)
    {
        $perencanaan = Perencanaan::whereHas('disposisi.notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->first();
        
        if (!$perencanaan) {
            return redirect()->back()->with('error', 'Data perencanaan tidak ditemukan');
        }

        $perencanaan->delete();

        return redirect()->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Data perencanaan berhasil dihapus');
    }

    // ==================== CRUD untuk Disposisi ====================
    public function editDisposisi(Permintaan $permintaan)
    {
        // Disposisi via Nota Dinas
        $disposisi = Disposisi::whereHas('notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->first();
        
        if (!$disposisi) {
            return redirect()->back()->with('error', 'Data disposisi tidak ditemukan');
        }

        return Inertia::render('StaffPerencanaan/CreateDisposisi', [
            'permintaan' => $permintaan,
            'disposisi' => $disposisi,
            'isEdit' => true,
        ]);
    }

    public function updateDisposisi(Request $request, Permintaan $permintaan)
    {
        $disposisi = Disposisi::whereHas('notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->first();
        
        if (!$disposisi) {
            return redirect()->back()->with('error', 'Data disposisi tidak ditemukan');
        }

        $validated = $request->validate([
            'jabatan_tujuan' => 'required|string',
            'tanggal_disposisi' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $disposisi->update($validated);

        return redirect()->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Data disposisi berhasil diupdate');
    }

    public function deleteDisposisi(Permintaan $permintaan)
    {
        $disposisi = Disposisi::whereHas('notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->first();
        
        if (!$disposisi) {
            return redirect()->back()->with('error', 'Data disposisi tidak ditemukan');
        }

        $disposisi->delete();

        return redirect()->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Data disposisi berhasil dihapus');
    }

    // ==================== CRUD untuk Nota Dinas ====================
    public function editNotaDinas(Permintaan $permintaan)
    {
        $notaDinas = $permintaan->notaDinas()->latest()->first();
        
        if (!$notaDinas) {
            return redirect()->back()->with('error', 'Data nota dinas tidak ditemukan');
        }

        return Inertia::render('StaffPerencanaan/CreateNotaDinas', [
            'permintaan' => $permintaan,
            'notaDinas' => $notaDinas,
            'isEdit' => true,
        ]);
    }

    public function updateNotaDinas(Request $request, Permintaan $permintaan)
    {
        $notaDinas = $permintaan->notaDinas()->latest()->first();
        
        if (!$notaDinas) {
            return redirect()->back()->with('error', 'Data nota dinas tidak ditemukan');
        }

        $validated = $request->validate([
            'no_nota_dinas' => 'required|string|max:255',
            'tanggal_nota_dinas' => 'required|date',
            'perihal' => 'required|string',
            'kepada' => 'required|string',
            'isi_nota_dinas' => 'nullable|string',
            'pagu_anggaran' => 'nullable|numeric',
        ]);

        // Update no_nota jika nomor berubah
        if (isset($validated['nomor'])) {
            $validated['no_nota'] = $validated['nomor'];
        }

        $notaDinas->update($validated);

        return redirect()->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Data nota dinas berhasil diupdate');
    }

    public function deleteNotaDinas(Permintaan $permintaan)
    {
        $notaDinas = $permintaan->notaDinas()->latest()->first();
        
        if (!$notaDinas) {
            return redirect()->back()->with('error', 'Data nota dinas tidak ditemukan');
        }

        $notaDinas->delete();

        return redirect()->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Data nota dinas berhasil dihapus');
    }

    // ==================== CRUD untuk DPP ====================
    public function editDPP(Permintaan $permintaan)
    {
        // DPP ada di tabel perencanaan
        $dpp = Perencanaan::whereHas('disposisi.notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->first();
        
        if (!$dpp) {
            return redirect()->back()->with('error', 'Data DPP tidak ditemukan');
        }

        return Inertia::render('StaffPerencanaan/CreateDPP', [
            'permintaan' => $permintaan,
            'dpp' => $dpp,
            'isEdit' => true,
        ]);
    }

    public function updateDPP(Request $request, Permintaan $permintaan)
    {
        $dpp = Perencanaan::whereHas('disposisi.notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->first();
        
        if (!$dpp) {
            return redirect()->back()->with('error', 'Data DPP tidak ditemukan');
        }

        $validated = $request->validate([
            'ppk_ditunjuk' => 'required|string',
            'nama_paket' => 'required|string',
            'lokasi' => 'nullable|string',
            'sumber_dana' => 'required|string',
            'pagu_paket' => 'nullable|numeric',
            'nilai_hps' => 'nullable|numeric',
            'jangka_waktu_pelaksanaan' => 'required|integer',
            'metode_pengadaan' => 'nullable|string',
        ]);

        $dpp->update($validated);

        return redirect()->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Data DPP berhasil diupdate');
    }

    public function deleteDPP(Permintaan $permintaan)
    {
        $dpp = Perencanaan::whereHas('disposisi.notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->first();
        
        if (!$dpp) {
            return redirect()->back()->with('error', 'Data DPP tidak ditemukan');
        }

        $dpp->delete();

        return redirect()->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Data DPP berhasil dihapus');
    }

    // ==================== CRUD untuk HPS ====================
    public function editHPS(Permintaan $permintaan)
    {
        $hps = $permintaan->hps()->with('items')->first();
        
        if (!$hps) {
            return redirect()->back()->with('error', 'Data HPS tidak ditemukan');
        }

        return Inertia::render('StaffPerencanaan/CreateHPS', [
            'permintaan' => $permintaan,
            'hps' => $hps,
            'isEdit' => true,
        ]);
    }

    public function updateHPS(Request $request, Permintaan $permintaan)
    {
        $hps = $permintaan->hps()->first();
        
        if (!$hps) {
            return redirect()->back()->with('error', 'Data HPS tidak ditemukan');
        }

        $validated = $request->validate([
            'ppk' => 'required|string',
            'surat_penawaran_harga' => 'required|string',
            'tanggal_penetapan' => 'required|date',
            'grand_total' => 'required|numeric',
            'items' => 'required|array',
            'items.*.nama_item' => 'required|string',
            'items.*.spesifikasi' => 'required|string',
            'items.*.satuan' => 'required|string',
            'items.*.volume' => 'required|numeric',
            'items.*.harga_satuan' => 'required|numeric',
            'items.*.jumlah' => 'required|numeric',
        ]);

        // Update HPS
        $hps->update([
            'ppk' => $validated['ppk'],
            'surat_penawaran_harga' => $validated['surat_penawaran_harga'],
            'tanggal_penetapan' => $validated['tanggal_penetapan'],
            'grand_total' => $validated['grand_total'],
        ]);

        // Delete existing items
        $hps->items()->delete();

        // Create new items
        foreach ($validated['items'] as $item) {
            $hps->items()->create($item);
        }

        return redirect()->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Data HPS berhasil diupdate');
    }

    public function deleteHPS(Permintaan $permintaan)
    {
        $hps = $permintaan->hps()->first();
        
        if (!$hps) {
            return redirect()->back()->with('error', 'Data HPS tidak ditemukan');
        }

        // Delete items first
        $hps->items()->delete();
        $hps->delete();

        return redirect()->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Data HPS berhasil dihapus');
    }

    // ==================== CETAK DOKUMEN ====================
    
    /**
     * Cetak Nota Dinas
     */
    public function cetakNotaDinas(Permintaan $permintaan)
    {
        $permintaan->load(['user', 'notaDinas']);
        
        $notaDinas = $permintaan->notaDinas()->latest()->first();
        
        if (!$notaDinas) {
            return redirect()->back()->with('error', 'Nota Dinas belum dibuat');
        }
        
        return view('cetak.nota-dinas-staff-perencanaan', [
            'permintaan' => $permintaan,
            'notaDinas' => $notaDinas,
        ]);
    }
    
    /**
     * Cetak DPP
     */
    public function cetakDPP(Permintaan $permintaan)
    {
        $permintaan->load('user');
        
        $dpp = Perencanaan::whereHas('disposisi.notaDinas', function($query) use ($permintaan) {
            $query->where('permintaan_id', $permintaan->permintaan_id);
        })->first();
        
        if (!$dpp) {
            return redirect()->back()->with('error', 'DPP belum dibuat');
        }
        
        return view('cetak.dpp-staff-perencanaan', [
            'permintaan' => $permintaan,
            'dpp' => $dpp,
        ]);
    }
    
    /**
     * Cetak HPS
     */
    public function cetakHPS(Permintaan $permintaan)
    {
        $permintaan->load('user');
        
        $hps = $permintaan->hps()->with('items')->first();
        
        if (!$hps) {
            return redirect()->back()->with('error', 'HPS belum dibuat');
        }
        
        return view('cetak.hps-staff-perencanaan', [
            'permintaan' => $permintaan,
            'hps' => $hps,
        ]);
    }

    // ==================== CRUD SPESIFIKASI TEKNIS ====================
    
    /**
     * Form membuat Spesifikasi Teknis
     */
    public function createSpesifikasiTeknis(Permintaan $permintaan)
    {
        $user = Auth::user();
        
        return Inertia::render('StaffPerencanaan/CreateSpesifikasiTeknis', [
            'permintaan' => $permintaan,
        ]);
    }

    /**
     * Store Spesifikasi Teknis
     */
    public function storeSpesifikasiTeknis(Request $request, Permintaan $permintaan)
    {
        $data = $request->validate([
            // Section 1: Latar Belakang & Tujuan
            'latar_belakang' => 'required|string',
            'maksud_tujuan' => 'required|string',
            'target_sasaran' => 'required|string',
            
            // Section 2: Pejabat & Anggaran
            'pejabat_pengadaan' => 'required|string',
            'sumber_dana' => 'required|string',
            'perkiraan_biaya' => 'required|string',
            
            // Section 3: Detail Barang/Jasa
            'jenis_barang_jasa' => 'required|string',
            'fungsi_manfaat' => 'required|string',
            'kegiatan_rutin' => 'required|in:Ya,Tidak',
            
            // Section 4: Waktu & Tenaga
            'jangka_waktu' => 'required|string',
            'estimasi_waktu_datang' => 'required|string',
            'tenaga_diperlukan' => 'required|string',
            
            // Section 5: Pelaku Usaha & Konsolidasi
            'pelaku_usaha' => 'required|string',
            'pengadaan_sejenis' => 'required|in:Ya,Tidak',
            'pengadaan_sejenis_keterangan' => 'nullable|string',
            'indikasi_konsolidasi' => 'required|in:Ya,Tidak',
            'indikasi_konsolidasi_keterangan' => 'nullable|string',
        ]);

        $data['permintaan_id'] = $permintaan->permintaan_id;

        SpesifikasiTeknis::create($data);

        return redirect()
            ->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Spesifikasi Teknis berhasil dibuat');
    }

    /**
     * Form edit Spesifikasi Teknis
     */
    public function editSpesifikasiTeknis(Permintaan $permintaan)
    {
        $spesifikasi = $permintaan->spesifikasiTeknis;
        
        if (!$spesifikasi) {
            return redirect()->back()->with('error', 'Data Spesifikasi Teknis tidak ditemukan');
        }

        return Inertia::render('StaffPerencanaan/CreateSpesifikasiTeknis', [
            'permintaan' => $permintaan,
            'spesifikasi' => $spesifikasi,
            'isEdit' => true,
        ]);
    }

    /**
     * Update Spesifikasi Teknis
     */
    public function updateSpesifikasiTeknis(Request $request, Permintaan $permintaan)
    {
        $spesifikasi = $permintaan->spesifikasiTeknis;
        
        if (!$spesifikasi) {
            return redirect()->back()->with('error', 'Data Spesifikasi Teknis tidak ditemukan');
        }

        $data = $request->validate([
            // Section 1: Latar Belakang & Tujuan
            'latar_belakang' => 'required|string',
            'maksud_tujuan' => 'required|string',
            'target_sasaran' => 'required|string',
            
            // Section 2: Pejabat & Anggaran
            'pejabat_pengadaan' => 'required|string',
            'sumber_dana' => 'required|string',
            'perkiraan_biaya' => 'required|string',
            
            // Section 3: Detail Barang/Jasa
            'jenis_barang_jasa' => 'required|string',
            'fungsi_manfaat' => 'required|string',
            'kegiatan_rutin' => 'required|in:Ya,Tidak',
            
            // Section 4: Waktu & Tenaga
            'jangka_waktu' => 'required|string',
            'estimasi_waktu_datang' => 'required|string',
            'tenaga_diperlukan' => 'required|string',
            
            // Section 5: Pelaku Usaha & Konsolidasi
            'pelaku_usaha' => 'required|string',
            'pengadaan_sejenis' => 'required|in:Ya,Tidak',
            'pengadaan_sejenis_keterangan' => 'nullable|string',
            'indikasi_konsolidasi' => 'required|in:Ya,Tidak',
            'indikasi_konsolidasi_keterangan' => 'nullable|string',
        ]);

        $spesifikasi->update($data);

        return redirect()
            ->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Spesifikasi Teknis berhasil diupdate');
    }

    /**
     * Delete Spesifikasi Teknis
     */
    public function deleteSpesifikasiTeknis(Permintaan $permintaan)
    {
        $spesifikasi = $permintaan->spesifikasiTeknis;
        
        if (!$spesifikasi) {
            return redirect()->back()->with('error', 'Data Spesifikasi Teknis tidak ditemukan');
        }

        $spesifikasi->delete();

        return redirect()
            ->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Spesifikasi Teknis berhasil dihapus');
    }

    // ==================== CRUD NOTA DINAS PEMBELIAN ====================
    
    /**
     * Store Nota Dinas Pembelian
     */
    public function storeNotaDinasPembelian(Request $request, Permintaan $permintaan)
    {
        $data = $request->validate([
            'nomor_nota_dinas' => 'nullable|string', // Changed to nullable
            'tanggal_nota' => 'required|date',
            'usulan_ruangan' => 'required|string',
            'sifat' => 'required|in:Sangat Segera,Segera,Biasa,Rahasia',
            'perihal' => 'required|string',
            'dari' => 'required|string',
            'kepada' => 'required|string',
            'isi_nota' => 'nullable|string',
        ]);

        $data['permintaan_id'] = $permintaan->permintaan_id;
        $data['tipe_nota'] = 'pembelian';
        
        // Set nomor dari nomor_nota_dinas atau generate baru
        if (!empty($data['nomor_nota_dinas'])) {
            $data['nomor'] = $data['nomor_nota_dinas'];
        } else {
            // Generate nomor otomatis jika kosong
            $lastNota = NotaDinas::whereYear('tanggal_nota', date('Y'))
                ->where('tipe_nota', 'pembelian')
                ->orderBy('nota_id', 'desc')
                ->first();
                
            $nextNumber = $lastNota ? intval(substr($lastNota->nomor, 0, 3)) + 1 : 1;
            $data['nomor'] = sprintf('%03d/ND-PEM/SP/%s', $nextNumber, date('Y'));
        }

        // Set no_nota sama dengan nomor - REQUIRED FIELD
        $data['no_nota'] = $data['nomor'];
        
        // Set isi_nota default jika tidak ada
        if (empty($data['isi_nota'])) {
            $data['isi_nota'] = $data['perihal'];
        }

        // Remove nomor_nota_dinas from data before create
        unset($data['nomor_nota_dinas']);

        NotaDinas::create($data);

        return redirect()
            ->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Nota Dinas Pembelian berhasil dibuat');
    }

    /**
     * Form edit Nota Dinas Pembelian
     */
    public function editNotaDinasPembelian(Permintaan $permintaan)
    {
        $notaDinas = NotaDinas::where('permintaan_id', $permintaan->permintaan_id)
            ->where('tipe_nota', 'pembelian')
            ->first();
        
        if (!$notaDinas) {
            return redirect()->back()->with('error', 'Nota Dinas Pembelian tidak ditemukan');
        }

        return Inertia::render('StaffPerencanaan/CreateNotaDinasPembelian', [
            'permintaan' => $permintaan,
            'notaDinas' => $notaDinas,
            'isEdit' => true,
        ]);
    }

    /**
     * Update Nota Dinas Pembelian
     */
    public function updateNotaDinasPembelian(Request $request, Permintaan $permintaan)
    {
        $notaDinas = NotaDinas::where('permintaan_id', $permintaan->permintaan_id)
            ->where('tipe_nota', 'pembelian')
            ->first();
        
        if (!$notaDinas) {
            return redirect()->back()->with('error', 'Nota Dinas Pembelian tidak ditemukan');
        }

        $data = $request->validate([
            'nomor_nota_dinas' => 'required|string',
            'tanggal_nota' => 'required|date',
            'usulan_ruangan' => 'required|string',
            'sifat' => 'required|in:Sangat Segera,Segera,Biasa,Rahasia',
            'perihal' => 'required|string',
            'dari' => 'required|string',
            'kepada' => 'required|string',
            'isi_nota' => 'nullable|string',
        ]);

        $data['nomor'] = $data['nomor_nota_dinas'];
        
        // Update no_nota sama dengan nomor
        $data['no_nota'] = $data['nomor'];
        
        $notaDinas->update($data);

        return redirect()
            ->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Nota Dinas Pembelian berhasil diupdate');
    }

    /**
     * Delete Nota Dinas Pembelian
     */
    public function deleteNotaDinasPembelian(Permintaan $permintaan)
    {
        $notaDinas = NotaDinas::where('permintaan_id', $permintaan->permintaan_id)
            ->where('tipe_nota', 'pembelian')
            ->first();
        
        if (!$notaDinas) {
            return redirect()->back()->with('error', 'Nota Dinas Pembelian tidak ditemukan');
        }

        $notaDinas->delete();

        return redirect()
            ->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Nota Dinas Pembelian berhasil dihapus');
    }
}
