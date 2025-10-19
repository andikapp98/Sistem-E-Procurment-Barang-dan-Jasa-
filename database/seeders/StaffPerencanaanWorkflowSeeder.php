<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StaffPerencanaanWorkflowSeeder extends Seeder
{
    /**
     * Seed permintaan yang sudah sampai ke Staff Perencanaan
     * Workflow: Kepala Instalasi → Kabid → Wadir → Direktur → Staff Perencanaan
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding workflow data untuk Staff Perencanaan...');
        
        // Get user IDs
        $kepalaIGD = DB::table('users')->where('email', 'kepala.igd@rsud.id')->value('id');
        $kepalaFarmasi = DB::table('users')->where('email', 'kepala.farmasi@rsud.id')->value('id');
        $kepalaLab = DB::table('users')->where('email', 'kepala.lab@rsud.id')->value('id');
        $kabidYanmed = DB::table('users')->where('email', 'kabid.yanmed@rsud.id')->value('id');
        $kabidPenunjang = DB::table('users')->where('email', 'kabid.penunjang@rsud.id')->value('id');
        $wadirUmum = DB::table('users')->where('email', 'wadir.umum@rsud.id')->value('id');
        $direktur = DB::table('users')->where('email', 'direktur@rsud.id')->value('id');
        
        // ============================================================
        // PERMINTAAN 1: Status DISETUJUI - Siap untuk Staff Perencanaan
        // ============================================================
        $permintaanId1 = DB::table('permintaan')->insertGetId([
            'user_id' => $kepalaIGD,
            'bidang' => 'Gawat Darurat',
            'tanggal_permintaan' => Carbon::now()->subDays(15),
            'deskripsi' => 'Pengadaan 2 unit defibrillator portable untuk ruang IGD. Defibrillator yang ada sudah berusia 10 tahun dan sering mengalami gangguan. Diperlukan segera untuk meningkatkan kesiapan penanganan pasien cardiac arrest.',
            'status' => 'disetujui',
            'pic_pimpinan' => 'Staff Perencanaan',
            'created_at' => Carbon::now()->subDays(15),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        // Nota Dinas 1: Kepala IGD → Kabid Yanmed
        $notaId1_1 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId1,
            'no_nota' => 'ND/IGD/2025/001',
            'dari' => 'Kepala Instalasi Gawat Darurat',
            'kepada' => 'Kepala Bidang Pelayanan Medis',
            'perihal' => 'Permohonan Pengadaan Defibrillator Portable',
            'tanggal_nota' => Carbon::now()->subDays(15),
            'created_at' => Carbon::now()->subDays(15),
        ]);

        // Disposisi 1-1: Kabid Yanmed Approve
        DB::table('disposisi')->insert([
            'nota_id' => $notaId1_1,
            'no_disposisi' => 'DISP/IGD/2025/001',
            'tanggal_disposisi' => Carbon::now()->subDays(13),
            'dari' => 'Kepala Bidang Pelayanan Medis',
            'kepada' => 'Wakil Direktur Umum & Keuangan',
            'isi_disposisi' => 'Disetujui. Sangat mendesak untuk meningkatkan kualitas pelayanan IGD.',
            'status' => 'selesai',
            'created_at' => Carbon::now()->subDays(13),
            'updated_at' => Carbon::now()->subDays(13),
        ]);

        // Nota Dinas 1-2: Kabid → Wadir
        $notaId1_2 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId1,
            'no_nota' => 'ND/YANMED/2025/012',
            'dari' => 'Kepala Bidang Pelayanan Medis',
            'kepada' => 'Wakil Direktur Umum & Keuangan',
            'perihal' => 'Terusan: Permohonan Pengadaan Defibrillator Portable',
            'tanggal_nota' => Carbon::now()->subDays(13),
            'created_at' => Carbon::now()->subDays(13),
        ]);

        // Disposisi 1-2: Wadir Approve
        DB::table('disposisi')->insert([
            'nota_id' => $notaId1_2,
            'no_disposisi' => 'DISP/YANMED/2025/012',
            'tanggal_disposisi' => Carbon::now()->subDays(10),
            'dari' => 'Wakil Direktur Umum & Keuangan',
            'kepada' => 'Direktur RSUD',
            'isi_disposisi' => 'Disetujui dan diteruskan ke Direktur untuk persetujuan final. Alokasi anggaran tersedia.',
            'status' => 'selesai',
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10),
        ]);

        // Nota Dinas 1-3: Wadir → Direktur
        $notaId1_3 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId1,
            'no_nota' => 'ND/WADIR/2025/045',
            'dari' => 'Wakil Direktur Umum & Keuangan',
            'kepada' => 'Direktur RSUD',
            'perihal' => 'Persetujuan Pengadaan Defibrillator Portable IGD',
            'tanggal_nota' => Carbon::now()->subDays(10),
            'created_at' => Carbon::now()->subDays(10),
        ]);

        // Disposisi 1-3: Direktur Approve → Staff Perencanaan
        DB::table('disposisi')->insert([
            'nota_id' => $notaId1_3,
            'permintaan_id' => $permintaanId1,
            'jabatan_asal' => 'Wakil Direktur',
            'jabatan_tujuan' => 'Staff Perencanaan',
            'tanggal_disposisi' => Carbon::now()->subDays(1),
            'catatan' => 'Disetujui. Segera disposisi ke Staff Perencanaan untuk proses perencanaan dan pengadaan.',
            'status' => 'disetujui',
            'created_at' => Carbon::now()->subDays(1),
        ]);

        // ============================================================
        // PERMINTAAN 2: Status DISETUJUI - Siap untuk Staff Perencanaan
        // ============================================================
        $permintaanId2 = DB::table('permintaan')->insertGetId([
            'user_id' => $kepalaFarmasi,
            'bidang' => 'Farmasi',
            'tanggal_permintaan' => Carbon::now()->subDays(20),
            'deskripsi' => 'Pengadaan obat-obatan esensial untuk stok 3 bulan: Antibiotik (Ceftriaxone, Levofloxacin), Analgesik (Ketorolac, Tramadol), dan Cairan Infus (RL, NS, D5%). Total estimasi 150 juta rupiah.',
            'status' => 'disetujui',
            'pic_pimpinan' => 'Staff Perencanaan',
            'created_at' => Carbon::now()->subDays(20),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // Nota Dinas 2-1
        $notaId2_1 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId2,
            'no_nota' => 'ND/FARM/2025/023',
            'dari' => 'Kepala Instalasi Farmasi',
            'kepada' => 'Kepala Bidang Pelayanan Medis',
            'perihal' => 'Permohonan Pengadaan Obat Esensial Triwulan I',
            'tanggal_nota' => Carbon::now()->subDays(20),
            'created_at' => Carbon::now()->subDays(20),
        ]);

        DB::table('disposisi')->insert([
            'nota_id' => $notaId2_1,
            'permintaan_id' => $permintaanId2,
            'jabatan_asal' => 'Kepala Instalasi Farmasi',
            'jabatan_tujuan' => 'Wakil Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(17),
            'catatan' => 'Disetujui. Obat esensial prioritas tinggi untuk kontinuitas pelayanan.',
            'status' => 'disetujui',
            'created_at' => Carbon::now()->subDays(17),
        ]);

        // Nota Dinas 2-2
        $notaId2_2 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId2,
            'no_nota' => 'ND/YANMED/2025/015',
            'dari' => 'Kepala Bidang Pelayanan Medis',
            'kepada' => 'Wakil Direktur Umum & Keuangan',
            'perihal' => 'Terusan: Pengadaan Obat Esensial',
            'tanggal_nota' => Carbon::now()->subDays(17),
            'created_at' => Carbon::now()->subDays(17),
        ]);

        DB::table('disposisi')->insert([
            'nota_id' => $notaId2_2,
            'permintaan_id' => $permintaanId2,
            'jabatan_asal' => 'Kepala Bidang Pelayanan Medis',
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(14),
            'catatan' => 'Disetujui. Anggaran mencukupi untuk pengadaan obat esensial.',
            'status' => 'disetujui',
            'created_at' => Carbon::now()->subDays(14),
        ]);

        // Nota Dinas 2-3
        $notaId2_3 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId2,
            'no_nota' => 'ND/WADIR/2025/048',
            'dari' => 'Wakil Direktur Umum & Keuangan',
            'kepada' => 'Direktur RSUD',
            'perihal' => 'Persetujuan Pengadaan Obat Esensial Triwulan I',
            'tanggal_nota' => Carbon::now()->subDays(14),
            'created_at' => Carbon::now()->subDays(14),
        ]);

        DB::table('disposisi')->insert([
            'nota_id' => $notaId2_3,
            'permintaan_id' => $permintaanId2,
            'jabatan_asal' => 'Wakil Direktur',
            'jabatan_tujuan' => 'Staff Perencanaan',
            'tanggal_disposisi' => Carbon::now()->subDays(2),
            'catatan' => 'Disetujui. Disposisi ke Staff Perencanaan untuk proses lelang umum.',
            'status' => 'disetujui',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // ============================================================
        // PERMINTAAN 3: Status DISETUJUI - Baru sampai Staff Perencanaan
        // ============================================================
        $permintaanId3 = DB::table('permintaan')->insertGetId([
            'user_id' => $kepalaLab,
            'bidang' => 'Laboratorium',
            'tanggal_permintaan' => Carbon::now()->subDays(12),
            'deskripsi' => 'Pengadaan reagen laboratorium untuk pemeriksaan hematologi lengkap, kimia darah, dan serologi. Stok saat ini tinggal untuk 2 minggu. Diperlukan reagen untuk 1000 sampel.',
            'status' => 'disetujui',
            'pic_pimpinan' => 'Staff Perencanaan',
            'created_at' => Carbon::now()->subDays(12),
            'updated_at' => Carbon::now(),
        ]);

        // Workflow lengkap untuk Permintaan 3
        $notaId3_1 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId3,
            'no_nota' => 'ND/LAB/2025/008',
            'dari' => 'Kepala Instalasi Laboratorium',
            'kepada' => 'Kepala Bidang Penunjang Medis',
            'perihal' => 'Permohonan Pengadaan Reagen Laboratorium',
            'tanggal_nota' => Carbon::now()->subDays(12),
            'created_at' => Carbon::now()->subDays(12),
        ]);

        DB::table('disposisi')->insert([
            'nota_id' => $notaId3_1,
            'permintaan_id' => $permintaanId3,
            'jabatan_asal' => 'Kepala Instalasi Laboratorium',
            'jabatan_tujuan' => 'Wakil Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(10),
            'catatan' => 'Disetujui. Reagen laboratorium sangat penting untuk kelancaran pemeriksaan diagnostik.',
            'status' => 'disetujui',
            'created_at' => Carbon::now()->subDays(10),
        ]);

        $notaId3_2 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId3,
            'no_nota' => 'ND/PENUNJANG/2025/006',
            'dari' => 'Kepala Bidang Penunjang Medis',
            'kepada' => 'Wakil Direktur Umum & Keuangan',
            'perihal' => 'Terusan: Pengadaan Reagen Laboratorium',
            'tanggal_nota' => Carbon::now()->subDays(10),
            'created_at' => Carbon::now()->subDays(10),
        ]);

        DB::table('disposisi')->insert([
            'nota_id' => $notaId3_2,
            'permintaan_id' => $permintaanId3,
            'jabatan_asal' => 'Kepala Bidang Penunjang Medis',
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(7),
            'catatan' => 'Disetujui dan diteruskan untuk persetujuan Direktur.',
            'status' => 'disetujui',
            'created_at' => Carbon::now()->subDays(7),
        ]);

        $notaId3_3 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId3,
            'no_nota' => 'ND/WADIR/2025/051',
            'dari' => 'Wakil Direktur Umum & Keuangan',
            'kepada' => 'Direktur RSUD',
            'perihal' => 'Persetujuan Pengadaan Reagen Laboratorium',
            'tanggal_nota' => Carbon::now()->subDays(7),
            'created_at' => Carbon::now()->subDays(7),
        ]);

        DB::table('disposisi')->insert([
            'nota_id' => $notaId3_3,
            'permintaan_id' => $permintaanId3,
            'jabatan_asal' => 'Wakil Direktur',
            'jabatan_tujuan' => 'Staff Perencanaan',
            'tanggal_disposisi' => Carbon::now(),
            'catatan' => 'Disetujui. Segera proses perencanaan pengadaan dengan metode e-purchasing.',
            'status' => 'disetujui',
            'created_at' => Carbon::now(),
        ]);

        // ============================================================
        // PERMINTAAN 4: Status PROSES - Sedang di Wadir (belum sampai Staff Perencanaan)
        // ============================================================
        $permintaanId4 = DB::table('permintaan')->insertGetId([
            'user_id' => $kepalaIGD,
            'bidang' => 'Gawat Darurat',
            'tanggal_permintaan' => Carbon::now()->subDays(5),
            'deskripsi' => 'Pengadaan 50 set APD lengkap (coverall, masker N95, face shield, sarung tangan) untuk persiapan menghadapi potensi wabah.',
            'status' => 'proses',
            'pic_pimpinan' => 'Wakil Direktur',
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        $notaId4_1 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId4,
            'no_nota' => 'ND/IGD/2025/002',
            'dari' => 'Kepala Instalasi Gawat Darurat',
            'kepada' => 'Kepala Bidang Pelayanan Medis',
            'perihal' => 'Permohonan Pengadaan APD',
            'tanggal_nota' => Carbon::now()->subDays(5),
            'created_at' => Carbon::now()->subDays(5),
        ]);

        DB::table('disposisi')->insert([
            'nota_id' => $notaId4_1,
            'permintaan_id' => $permintaanId4,
            'jabatan_asal' => 'Kepala Instalasi Gawat Darurat',
            'jabatan_tujuan' => 'Wakil Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(3),
            'catatan' => 'Disetujui untuk diteruskan ke Wakil Direktur.',
            'status' => 'disetujui',
            'created_at' => Carbon::now()->subDays(3),
        ]);

        $notaId4_2 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId4,
            'no_nota' => 'ND/YANMED/2025/018',
            'dari' => 'Kepala Bidang Pelayanan Medis',
            'kepada' => 'Wakil Direktur Umum & Keuangan',
            'perihal' => 'Terusan: Pengadaan APD',
            'tanggal_nota' => Carbon::now()->subDays(3),
            'created_at' => Carbon::now()->subDays(3),
        ]);

        DB::table('disposisi')->insert([
            'nota_id' => $notaId4_2,
            'permintaan_id' => $permintaanId4,
            'jabatan_asal' => 'Kepala Bidang Pelayanan Medis',
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(2),
            'catatan' => 'Sedang dalam review anggaran.',
            'status' => 'dalam_proses',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // ============================================================
        // PERMINTAAN 5: Status PROSES - Sudah ada perencanaan
        // ============================================================
        $permintaanId5 = DB::table('permintaan')->insertGetId([
            'user_id' => $kepalaFarmasi,
            'bidang' => 'Farmasi',
            'tanggal_permintaan' => Carbon::now()->subDays(30),
            'deskripsi' => 'Pengadaan alat kesehatan: Tensimeter digital 10 unit, Termometer infrared 15 unit, Pulse oximeter 20 unit untuk seluruh ruangan rawat inap.',
            'status' => 'proses',
            'pic_pimpinan' => 'Bagian Pengadaan',
            'created_at' => Carbon::now()->subDays(30),
            'updated_at' => Carbon::now()->subDays(5),
        ]);

        // Workflow lengkap sampai Staff Perencanaan sudah buat perencanaan
        $notaId5_1 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId5,
            'no_nota' => 'ND/FARM/2025/020',
            'dari' => 'Kepala Instalasi Farmasi',
            'kepada' => 'Kepala Bidang Pelayanan Medis',
            'perihal' => 'Permohonan Pengadaan Alat Kesehatan',
            'tanggal_nota' => Carbon::now()->subDays(30),
            'created_at' => Carbon::now()->subDays(30),
        ]);

        DB::table('disposisi')->insert([
            'nota_id' => $notaId5_1,
            'permintaan_id' => $permintaanId5,
            'jabatan_asal' => 'Kepala Instalasi Farmasi',
            'jabatan_tujuan' => 'Wakil Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(27),
            'catatan' => 'Disetujui untuk pengadaan alat kesehatan.',
            'status' => 'disetujui',
            'created_at' => Carbon::now()->subDays(27),
        ]);

        $notaId5_2 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId5,
            'no_nota' => 'ND/YANMED/2025/010',
            'dari' => 'Kepala Bidang Pelayanan Medis',
            'kepada' => 'Wakil Direktur Umum & Keuangan',
            'perihal' => 'Terusan: Pengadaan Alat Kesehatan',
            'tanggal_nota' => Carbon::now()->subDays(27),
            'created_at' => Carbon::now()->subDays(27),
        ]);

        DB::table('disposisi')->insert([
            'nota_id' => $notaId5_2,
            'permintaan_id' => $permintaanId5,
            'jabatan_asal' => 'Kepala Bidang Pelayanan Medis',
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(24),
            'catatan' => 'Disetujui, anggaran tersedia.',
            'status' => 'disetujui',
            'created_at' => Carbon::now()->subDays(24),
        ]);

        $notaId5_3 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId5,
            'no_nota' => 'ND/WADIR/2025/042',
            'dari' => 'Wakil Direktur Umum & Keuangan',
            'kepada' => 'Direktur RSUD',
            'perihal' => 'Persetujuan Pengadaan Alat Kesehatan',
            'tanggal_nota' => Carbon::now()->subDays(24),
            'created_at' => Carbon::now()->subDays(24),
        ]);

        DB::table('disposisi')->insert([
            'nota_id' => $notaId5_3,
            'permintaan_id' => $permintaanId5,
            'jabatan_asal' => 'Wakil Direktur',
            'jabatan_tujuan' => 'Staff Perencanaan',
            'tanggal_disposisi' => Carbon::now()->subDays(20),
            'catatan' => 'Disetujui, disposisi ke Staff Perencanaan untuk proses pengadaan.',
            'status' => 'disetujui',
            'created_at' => Carbon::now()->subDays(20),
        ]);

        // Perencanaan sudah dibuat oleh Staff Perencanaan
        $perencanaanId5 = DB::table('perencanaan')->insertGetId([
            'permintaan_id' => $permintaanId5,
            'metode_pengadaan' => 'E-Purchasing',
            'estimasi_biaya' => 45000000,
            'sumber_dana' => 'APBD',
            'jadwal_pelaksanaan' => Carbon::now()->addDays(30),
            'catatan_perencanaan' => 'Pengadaan melalui e-catalog untuk efisiensi waktu dan biaya.',
            'created_at' => Carbon::now()->subDays(15),
            'updated_at' => Carbon::now()->subDays(15),
        ]);

        // Disposisi dari Staff Perencanaan ke Bagian Pengadaan
        $notaId5_4 = DB::table('nota_dinas')->insertGetId([
            'permintaan_id' => $permintaanId5,
            'no_nota' => 'ND/PERENCANAAN/2025/005',
            'dari' => 'Staff Perencanaan',
            'kepada' => 'Bagian Pengadaan',
            'perihal' => 'Disposisi Pelaksanaan Pengadaan Alat Kesehatan',
            'tanggal_nota' => Carbon::now()->subDays(15),
            'created_at' => Carbon::now()->subDays(15),
        ]);

        DB::table('disposisi')->insert([
            'nota_id' => $notaId5_4,
            'permintaan_id' => $permintaanId5,
            'jabatan_asal' => 'Staff Perencanaan',
            'jabatan_tujuan' => 'Bagian Pengadaan',
            'tanggal_disposisi' => Carbon::now()->subDays(15),
            'catatan' => 'Perencanaan telah selesai. Silakan proses pengadaan melalui e-purchasing sesuai jadwal.',
            'status' => 'dalam_proses',
            'created_at' => Carbon::now()->subDays(15),
        ]);

        // Tracking status untuk semua permintaan
        $trackingData = [
            // Permintaan 1
            [
                'permintaan_id' => $permintaanId1,
                'tahapan' => 'Pengajuan',
                'tanggal' => Carbon::now()->subDays(15),
                'status' => 'Selesai',
                'keterangan' => 'Permintaan diajukan oleh Kepala IGD',
            ],
            [
                'permintaan_id' => $permintaanId1,
                'tahapan' => 'Review Kepala Bidang',
                'tanggal' => Carbon::now()->subDays(13),
                'status' => 'Selesai',
                'keterangan' => 'Disetujui oleh Kepala Bidang Pelayanan Medis',
            ],
            [
                'permintaan_id' => $permintaanId1,
                'tahapan' => 'Review Wakil Direktur',
                'tanggal' => Carbon::now()->subDays(10),
                'status' => 'Selesai',
                'keterangan' => 'Disetujui oleh Wakil Direktur',
            ],
            [
                'permintaan_id' => $permintaanId1,
                'tahapan' => 'Persetujuan Direktur',
                'tanggal' => Carbon::now()->subDays(1),
                'status' => 'Selesai',
                'keterangan' => 'Disetujui oleh Direktur, disposisi ke Staff Perencanaan',
            ],
            [
                'permintaan_id' => $permintaanId1,
                'tahapan' => 'Perencanaan',
                'tanggal' => Carbon::now(),
                'status' => 'Dalam Proses',
                'keterangan' => 'Menunggu perencanaan dari Staff Perencanaan',
            ],
            
            // Permintaan 2
            [
                'permintaan_id' => $permintaanId2,
                'tahapan' => 'Pengajuan',
                'tanggal' => Carbon::now()->subDays(20),
                'status' => 'Selesai',
                'keterangan' => 'Permintaan diajukan oleh Kepala Farmasi',
            ],
            [
                'permintaan_id' => $permintaanId2,
                'tahapan' => 'Review Kepala Bidang',
                'tanggal' => Carbon::now()->subDays(17),
                'status' => 'Selesai',
                'keterangan' => 'Disetujui oleh Kepala Bidang',
            ],
            [
                'permintaan_id' => $permintaanId2,
                'tahapan' => 'Review Wakil Direktur',
                'tanggal' => Carbon::now()->subDays(14),
                'status' => 'Selesai',
                'keterangan' => 'Disetujui oleh Wakil Direktur',
            ],
            [
                'permintaan_id' => $permintaanId2,
                'tahapan' => 'Persetujuan Direktur',
                'tanggal' => Carbon::now()->subDays(2),
                'status' => 'Selesai',
                'keterangan' => 'Disetujui oleh Direktur',
            ],
            [
                'permintaan_id' => $permintaanId2,
                'tahapan' => 'Perencanaan',
                'tanggal' => Carbon::now(),
                'status' => 'Dalam Proses',
                'keterangan' => 'Menunggu perencanaan dari Staff Perencanaan',
            ],
            
            // Permintaan 3
            [
                'permintaan_id' => $permintaanId3,
                'tahapan' => 'Pengajuan',
                'tanggal' => Carbon::now()->subDays(12),
                'status' => 'Selesai',
                'keterangan' => 'Permintaan diajukan oleh Kepala Laboratorium',
            ],
            [
                'permintaan_id' => $permintaanId3,
                'tahapan' => 'Review Kepala Bidang',
                'tanggal' => Carbon::now()->subDays(10),
                'status' => 'Selesai',
                'keterangan' => 'Disetujui oleh Kepala Bidang Penunjang Medis',
            ],
            [
                'permintaan_id' => $permintaanId3,
                'tahapan' => 'Review Wakil Direktur',
                'tanggal' => Carbon::now()->subDays(7),
                'status' => 'Selesai',
                'keterangan' => 'Disetujui oleh Wakil Direktur',
            ],
            [
                'permintaan_id' => $permintaanId3,
                'tahapan' => 'Persetujuan Direktur',
                'tanggal' => Carbon::now(),
                'status' => 'Selesai',
                'keterangan' => 'Disetujui oleh Direktur, baru saja masuk ke Staff Perencanaan',
            ],
            
            // Permintaan 5
            [
                'permintaan_id' => $permintaanId5,
                'tahapan' => 'Pengajuan',
                'tanggal' => Carbon::now()->subDays(30),
                'status' => 'Selesai',
                'keterangan' => 'Permintaan diajukan oleh Kepala Farmasi',
            ],
            [
                'permintaan_id' => $permintaanId5,
                'tahapan' => 'Review Kepala Bidang',
                'tanggal' => Carbon::now()->subDays(27),
                'status' => 'Selesai',
                'keterangan' => 'Disetujui oleh Kepala Bidang',
            ],
            [
                'permintaan_id' => $permintaanId5,
                'tahapan' => 'Review Wakil Direktur',
                'tanggal' => Carbon::now()->subDays(24),
                'status' => 'Selesai',
                'keterangan' => 'Disetujui oleh Wakil Direktur',
            ],
            [
                'permintaan_id' => $permintaanId5,
                'tahapan' => 'Persetujuan Direktur',
                'tanggal' => Carbon::now()->subDays(20),
                'status' => 'Selesai',
                'keterangan' => 'Disetujui oleh Direktur',
            ],
            [
                'permintaan_id' => $permintaanId5,
                'tahapan' => 'Perencanaan',
                'tanggal' => Carbon::now()->subDays(15),
                'status' => 'Selesai',
                'keterangan' => 'Perencanaan selesai dibuat, metode E-Purchasing',
            ],
            [
                'permintaan_id' => $permintaanId5,
                'tahapan' => 'Pelaksanaan Pengadaan',
                'tanggal' => Carbon::now()->subDays(5),
                'status' => 'Dalam Proses',
                'keterangan' => 'Sedang diproses oleh Bagian Pengadaan',
            ],
        ];

        foreach ($trackingData as $tracking) {
            DB::table('tracking_status')->insert(array_merge($tracking, [
                'created_at' => $tracking['tanggal'],
                'updated_at' => $tracking['tanggal'],
            ]));
        }

        $this->command->info('');
        $this->command->info('✅ Seeding selesai!');
        $this->command->info('');
        $this->command->info('📊 Data yang dibuat:');
        $this->command->info('   - 5 Permintaan (3 siap untuk Staff Perencanaan, 1 masih di Wadir, 1 sudah ada perencanaan)');
        $this->command->info('   - 14 Nota Dinas (workflow lengkap)');
        $this->command->info('   - 14 Disposisi (approval chain)');
        $this->command->info('   - 1 Perencanaan (contoh yang sudah selesai)');
        $this->command->info('   - 16 Tracking Status (timeline)');
        $this->command->info('');
        $this->command->info('🎯 Status Permintaan:');
        $this->command->info('   1. Defibrillator IGD      → DISETUJUI (pic: Staff Perencanaan) ✅');
        $this->command->info('   2. Obat Esensial Farmasi  → DISETUJUI (pic: Staff Perencanaan) ✅');
        $this->command->info('   3. Reagen Laboratorium    → DISETUJUI (pic: Staff Perencanaan) ✅ NEW!');
        $this->command->info('   4. APD IGD                → PROSES (pic: Wakil Direktur)');
        $this->command->info('   5. Alat Kesehatan         → PROSES (pic: Bagian Pengadaan) - Sudah ada perencanaan');
        $this->command->info('');
        $this->command->info('📝 Login sebagai Staff Perencanaan untuk melihat:');
        $this->command->info('   Email: perencanaan@rsud.id');
        $this->command->info('   Password: password');
    }
}
