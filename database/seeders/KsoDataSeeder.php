<?php

namespace Database\Seeders;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use App\Models\Disposisi;
use App\Models\Perencanaan;
use App\Models\Kso;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class KsoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder ini akan membuat data KSO lengkap dengan workflow:
     * Permintaan → Nota Dinas → Disposisi → Perencanaan → KSO
     */
    public function run(): void
    {
        $this->command->info('🔄 Creating KSO data...');

        // Ambil user terkait
        $kepalaIGD = \App\Models\User::where('email', 'kepala.igd@rsud.id')->first();
        $kepalaBidang = \App\Models\User::where('email', 'kabid.yanmed@rsud.id')->first();
        $direktur = \App\Models\User::where('email', 'direktur@rsud.id')->first();
        $staffPerencanaan = \App\Models\User::where('email', 'perencanaan@rsud.id')->first();
        $kso = \App\Models\User::where('email', 'kso@rsud.id')->first();

        if (!$kepalaIGD || !$kepalaBidang || !$direktur || !$staffPerencanaan || !$kso) {
            $this->command->error('❌ User tidak lengkap! Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // ==============================================
        // KSO 1: Pengadaan Alat Medis - Sudah ada KSO
        // ==============================================
        $permintaan1 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'bidang' => 'Instalasi Gawat Darurat',
            'tanggal_permintaan' => Carbon::now()->subDays(30),
            'deskripsi' => 'Pengadaan Ventilator dan Monitor ICU untuk meningkatkan kapasitas perawatan kritis di IGD.',
            'status' => 'proses',
            'pic_pimpinan' => 'Bagian KSO',
            'no_nota_dinas' => 'ND/IGD/KSO/2025/001',
        ]);

        // Nota Dinas: Instalasi → Kepala Bidang
        $nota1a = NotaDinas::create([
            'permintaan_id' => $permintaan1->permintaan_id,
            'no_nota' => 'ND/IGD/2025/KSO-001',
            'dari' => 'Instalasi Gawat Darurat',
            'kepada' => 'Kepala Bidang Pelayanan Medis',
            'tanggal_nota' => Carbon::now()->subDays(29),
            'perihal' => 'Permohonan Pengadaan Ventilator dan Monitor ICU',
        ]);

        // Disposisi: Kepala Bidang → Direktur
        $disp1a = Disposisi::create([
            'nota_id' => $nota1a->nota_id,
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(28),
            'catatan' => 'Mohon persetujuan untuk pengadaan alat medis kritis',
            'status' => 'disetujui',
        ]);

        // Nota Dinas: Direktur → Staff Perencanaan
        $nota1b = NotaDinas::create([
            'permintaan_id' => $permintaan1->permintaan_id,
            'no_nota' => 'ND/DIR/2025/KSO-001',
            'dari' => 'Direktur RSUD',
            'kepada' => 'Staff Perencanaan',
            'tanggal_nota' => Carbon::now()->subDays(27),
            'perihal' => 'Disposisi Perencanaan Pengadaan Ventilator dan Monitor ICU',
        ]);

        // Perencanaan
        $perencanaan1 = Perencanaan::create([
            'disposisi_id' => $disp1a->disposisi_id,
            'rencana_kegiatan' => 'Perencanaan pengadaan Ventilator dan Monitor ICU',
            'tanggal_mulai' => Carbon::now()->subDays(25),
            'tanggal_selesai' => Carbon::now()->subDays(10),
            'anggaran' => 500000000, // 500 juta
            'metode_pengadaan' => 'Tender',
            'nama_paket' => 'Pengadaan Ventilator dan Monitor ICU',
            'nilai_hps' => 480000000,
        ]);

        // KSO - Sudah dibuat
        $kso1 = Kso::create([
            'perencanaan_id' => $perencanaan1->perencanaan_id,
            'no_kso' => 'KSO/2025/001',
            'tanggal_kso' => Carbon::now()->subDays(8),
            'pihak_pertama' => 'RSUD - Direktur',
            'pihak_kedua' => 'PT. Alat Medis Indonesia',
            'isi_kerjasama' => 'Pengadaan 5 unit Ventilator ICU dan 10 unit Monitor Pasien dengan spesifikasi sesuai tender.',
            'nilai_kontrak' => 475000000, // 475 juta
            'status' => 'aktif',
        ]);

        // ==============================================
        // KSO 2: Pengadaan Obat-obatan - Dalam Proses
        // ==============================================
        $permintaan2 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'bidang' => 'Instalasi Farmasi',
            'tanggal_permintaan' => Carbon::now()->subDays(20),
            'deskripsi' => 'Pengadaan Obat-obatan untuk kebutuhan 1 tahun: Antibiotik, Analgesik, dan Obat Kardiovaskular.',
            'status' => 'proses',
            'pic_pimpinan' => 'Bagian KSO',
            'no_nota_dinas' => 'ND/FAR/KSO/2025/001',
        ]);

        $nota2a = NotaDinas::create([
            'permintaan_id' => $permintaan2->permintaan_id,
            'no_nota' => 'ND/FAR/2025/KSO-001',
            'dari' => 'Instalasi Farmasi',
            'kepada' => 'Kepala Bidang Penunjang Medis',
            'tanggal_nota' => Carbon::now()->subDays(19),
            'perihal' => 'Permohonan Pengadaan Obat-obatan Tahunan',
        ]);

        $disp2a = Disposisi::create([
            'nota_id' => $nota2a->nota_id,
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(18),
            'catatan' => 'Urgent - Stok obat menipis',
            'status' => 'disetujui',
        ]);

        $nota2b = NotaDinas::create([
            'permintaan_id' => $permintaan2->permintaan_id,
            'no_nota' => 'ND/DIR/2025/KSO-002',
            'dari' => 'Direktur RSUD',
            'kepada' => 'Staff Perencanaan',
            'tanggal_nota' => Carbon::now()->subDays(17),
            'perihal' => 'Disposisi Perencanaan Pengadaan Obat-obatan',
        ]);

        $perencanaan2 = Perencanaan::create([
            'disposisi_id' => $disp2a->disposisi_id,
            'rencana_kegiatan' => 'Perencanaan pengadaan obat-obatan tahunan',
            'tanggal_mulai' => Carbon::now()->subDays(15),
            'tanggal_selesai' => Carbon::now()->addDays(5),
            'anggaran' => 250000000, // 250 juta
            'metode_pengadaan' => 'E-Purchasing',
            'nama_paket' => 'Pengadaan Obat-obatan Tahunan',
            'nilai_hps' => 245000000,
        ]);

        $kso2 = Kso::create([
            'perencanaan_id' => $perencanaan2->perencanaan_id,
            'no_kso' => 'KSO/2025/002',
            'tanggal_kso' => Carbon::now()->subDays(5),
            'pihak_pertama' => 'RSUD - Direktur',
            'pihak_kedua' => 'PT. Kimia Farma Trading & Distribution',
            'isi_kerjasama' => 'Pengadaan obat-obatan untuk kebutuhan RSUD periode 2025 sesuai e-catalog.',
            'nilai_kontrak' => 242000000, // 242 juta
            'status' => 'aktif', // Changed from 'dalam_proses' to 'aktif'
        ]);

        // ==============================================
        // KSO 3: Pengadaan Alat Lab - Baru Dibuat
        // ==============================================
        $permintaan3 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'bidang' => 'Instalasi Laboratorium',
            'tanggal_permintaan' => Carbon::now()->subDays(15),
            'deskripsi' => 'Pengadaan Alat Laboratorium: Hematology Analyzer dan Chemistry Analyzer.',
            'status' => 'proses',
            'pic_pimpinan' => 'Bagian KSO',
            'no_nota_dinas' => 'ND/LAB/KSO/2025/001',
        ]);

        $nota3a = NotaDinas::create([
            'permintaan_id' => $permintaan3->permintaan_id,
            'no_nota' => 'ND/LAB/2025/KSO-001',
            'dari' => 'Instalasi Laboratorium',
            'kepada' => 'Kepala Bidang Penunjang Medis',
            'tanggal_nota' => Carbon::now()->subDays(14),
            'perihal' => 'Permohonan Pengadaan Alat Laboratorium',
        ]);

        $disp3a = Disposisi::create([
            'nota_id' => $nota3a->nota_id,
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(13),
            'catatan' => 'Alat lama sudah rusak, perlu penggantian',
            'status' => 'disetujui',
        ]);

        $nota3b = NotaDinas::create([
            'permintaan_id' => $permintaan3->permintaan_id,
            'no_nota' => 'ND/DIR/2025/KSO-003',
            'dari' => 'Direktur RSUD',
            'kepada' => 'Staff Perencanaan',
            'tanggal_nota' => Carbon::now()->subDays(12),
            'perihal' => 'Disposisi Perencanaan Pengadaan Alat Lab',
        ]);

        $perencanaan3 = Perencanaan::create([
            'disposisi_id' => $disp3a->disposisi_id,
            'rencana_kegiatan' => 'Perencanaan pengadaan alat laboratorium',
            'tanggal_mulai' => Carbon::now()->subDays(10),
            'tanggal_selesai' => Carbon::now()->addDays(10),
            'anggaran' => 350000000, // 350 juta
            'metode_pengadaan' => 'Tender',
            'nama_paket' => 'Pengadaan Alat Laboratorium',
            'nilai_hps' => 340000000,
        ]);

        $kso3 = Kso::create([
            'perencanaan_id' => $perencanaan3->perencanaan_id,
            'no_kso' => 'KSO/2025/003',
            'tanggal_kso' => Carbon::now()->subDays(2),
            'pihak_pertama' => 'RSUD - Direktur',
            'pihak_kedua' => 'PT. Labora Medika',
            'isi_kerjasama' => 'Pengadaan Hematology Analyzer 1 unit dan Chemistry Analyzer 1 unit termasuk instalasi dan training.',
            'nilai_kontrak' => 335000000, // 335 juta
            'status' => 'draft',
        ]);

        $this->command->info('');
        $this->command->info('✅ Berhasil membuat 3 data KSO:');
        $this->command->info('   1. KSO #' . $kso1->kso_id . ' - Ventilator & Monitor ICU (Rp 475 juta) - AKTIF');
        $this->command->info('   2. KSO #' . $kso2->kso_id . ' - Obat-obatan Tahunan (Rp 242 juta) - AKTIF');
        $this->command->info('   3. KSO #' . $kso3->kso_id . ' - Alat Laboratorium (Rp 335 juta) - DRAFT');
        $this->command->info('');
        $this->command->info('📊 Detail:');
        $this->command->info('   - Status Aktif: 2 KSO');
        $this->command->info('   - Status Draft: 1 KSO');
        $this->command->info('   - Total Nilai Kontrak: Rp 1,052,000,000');
        $this->command->info('');
        $this->command->info('🔐 Login Info:');
        $this->command->info('   Email: kso@rsud.id');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('📝 Catatan:');
        $this->command->info('   - Semua permintaan sudah PIC: Bagian KSO');
        $this->command->info('   - Workflow lengkap: Permintaan → Nota Dinas → Disposisi → Perencanaan → KSO');
        $this->command->info('   - Siap untuk diproses ke tahap Pengadaan');
        $this->command->info('');
        $this->command->info('📋 Status KSO yang tersedia:');
        $this->command->info('   - draft   : KSO dalam tahap penyusunan');
        $this->command->info('   - aktif   : KSO sudah ditandatangani dan aktif');
        $this->command->info('   - selesai : KSO sudah selesai dilaksanakan');
        $this->command->info('   - batal   : KSO dibatalkan');
    }
}
