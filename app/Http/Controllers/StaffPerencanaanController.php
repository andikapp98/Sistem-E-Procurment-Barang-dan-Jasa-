<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use App\Models\Disposisi;
use App\Models\DokumenPengadaan;
use App\Models\Perencanaan;
use App\Models\Hps;
use App\Models\HpsItem;
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
        
        
        $permintaan->load(['user', 'notaDinas.disposisi']);
        
        // Get timeline tracking
        $timeline = $permintaan->getTimelineTracking();
        $progress = $permintaan->getProgressPercentage();
        
        return Inertia::render('StaffPerencanaan/Show', [
            'permintaan' => $permintaan,
            'trackingStatus' => $permintaan->trackingStatus,
            'timeline' => $timeline,
            'progress' => $progress,
            'userLogin' => $user,
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
            'tanggal_nota' => 'required|date',
            'nomor' => 'nullable|string',
            'penerima' => 'nullable|string',
            'dari' => 'required|string',
            'kepada' => 'required|string',
            'sifat' => 'nullable|in:Sangat Segera,Segera,Biasa,Rahasia',
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
            'perihal' => 'nullable|string',
        ]);

        $data['permintaan_id'] = $permintaan->permintaan_id;
        
        // Set default perihal jika tidak ada
        if (!isset($data['perihal'])) {
            $data['perihal'] = 'Nota Dinas Usulan Pengadaan - ' . $permintaan->deskripsi;
        }

        // Generate nomor nota otomatis jika kosong
        if (empty($data['nomor'])) {
            $lastNota = NotaDinas::whereYear('tanggal_nota', date('Y'))
                ->orderBy('nota_id', 'desc')
                ->first();
            $nextNumber = $lastNota ? (intval(substr($lastNota->nomor, 0, 3)) + 1) : 1;
            $data['nomor'] = sprintf('%03d/ND-SP/%s', $nextNumber, date('Y'));
        }

        $notaDinas = NotaDinas::create($data);

        // Update status permintaan
        $permintaan->update([
            'status' => 'proses',
            'pic_pimpinan' => $data['kepada'],
        ]);

        return redirect()
            ->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Nota Dinas Usulan berhasil dibuat dan dikirim ke ' . $data['kepada']);
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
     * Store Nota Dinas Pembelian
     */
    public function storeNotaDinasPembelian(Request $request, Permintaan $permintaan)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'nomor_nota_dinas' => 'required|string',
            'tanggal_nota' => 'required|date',
            'usulan_ruangan' => 'required|string',
            'sifat' => 'required|in:Sangat Segera,Segera,Biasa,Rahasia',
            'perihal' => 'required|string',
            'dari' => 'required|string',
            'kepada' => 'required|string',
            'isi_nota' => 'nullable|string',
            'tipe_nota' => 'nullable|string',
        ]);

        // Buat nota dinas dengan tipe pembelian
        $notaDinas = NotaDinas::create([
            'permintaan_id' => $permintaan->permintaan_id,
            'nomor' => $data['nomor_nota_dinas'],
            'tanggal_nota' => $data['tanggal_nota'],
            'dari' => $data['dari'],
            'kepada' => $data['kepada'],
            'sifat' => $data['sifat'],
            'perihal' => $data['perihal'],
            'uraian' => $data['isi_nota'] ?? '',
            'unit_instalasi' => $data['usulan_ruangan'],
            'pagu_anggaran' => 0, // Set default atau bisa diisi nanti
        ]);

        // Buat disposisi ke tujuan
        Disposisi::create([
            'nota_id' => $notaDinas->nota_id,
            'jabatan_tujuan' => $data['kepada'],
            'tanggal_disposisi' => Carbon::now(),
            'catatan' => "Nota Dinas Pembelian telah dibuat.\nUsulan dari: {$data['usulan_ruangan']}\nPerihal: {$data['perihal']}",
            'status' => 'dalam_proses',
        ]);

        // Update status permintaan
        $permintaan->update([
            'status' => 'proses',
            'pic_pimpinan' => $data['kepada'],
            'deskripsi' => $permintaan->deskripsi . "\n\n[NOTA DINAS PEMBELIAN]\n" . 
                          "Nomor: {$data['nomor_nota_dinas']}\n" .
                          "Tanggal: " . Carbon::parse($data['tanggal_nota'])->format('d/m/Y') . "\n" .
                          "Usulan Ruangan: {$data['usulan_ruangan']}\n" .
                          "Perihal: {$data['perihal']}",
        ]);

        return redirect()
            ->route('staff-perencanaan.show', $permintaan)
            ->with('success', 'Nota Dinas Pembelian berhasil dibuat dan dikirim ke ' . $data['kepada']);
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
            
            // Kontrak dan Pelaksanaan
            'jenis_kontrak' => 'required|string',
            'kualifikasi' => 'required|string',
            'jangka_waktu_pelaksanaan' => 'required|integer|min:1',
            
            // Detail Pengadaan
            'nama_kegiatan' => 'required|string',
            'jenis_pengadaan' => 'required|string',
        ]);

        // Ambil nota dinas terakhir untuk membuat disposisi
        $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
        
        if (!$notaDinas) {
            return redirect()->back()->withErrors(['error' => 'Nota dinas tidak ditemukan']);
        }

        // Buat disposisi untuk DPP
        $disposisi = Disposisi::create([
            'nota_id' => $notaDinas->nota_id,
            'jabatan_tujuan' => 'Bagian Pengadaan', // Default atau bisa disesuaikan
            'tanggal_disposisi' => Carbon::now(),
            'catatan' => "DPP telah dibuat untuk paket: {$data['nama_paket']}",
            'status' => 'dalam_proses',
        ]);

        // Simpan DPP ke tabel perencanaan
        $data['disposisi_id'] = $disposisi->disposisi_id;
        $data['rencana_kegiatan'] = $data['nama_kegiatan'];
        $data['anggaran'] = $data['pagu_paket'];
        
        Perencanaan::create($data);

        // Update status permintaan
        $permintaan->update([
            'status' => 'proses',
            'pic_pimpinan' => 'Bagian Pengadaan',
            'deskripsi' => $permintaan->deskripsi . "\n\n[DPP DIBUAT]\n" .
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
}
