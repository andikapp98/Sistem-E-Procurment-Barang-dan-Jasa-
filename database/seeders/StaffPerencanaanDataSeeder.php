<?php

namespace Database\Seeders;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use App\Models\Disposisi;
use App\Models\Perencanaan;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StaffPerencanaanDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder ini akan membuat data permintaan yang sudah sampai ke Staff Perencanaan
     * setelah melalui alur: Kepala Instalasi → Kepala Bidang → Direktur → Staff Perencanaan
     */
    public function run(): void
    {
        $this->command->info('🔄 Creating permintaan data untuk Staff Perencanaan...');

        // Ambil user terkait
        $kepalaIGD = \App\Models\User::where('email', 'kepala.igd@rsud.id')->first();
        $kepalaBidang = \App\Models\User::where('email', 'kabid.yanmed@rsud.id')->first();
        $direktur = \App\Models\User::where('email', 'direktur@rsud.id')->first();
        $staffPerencanaan = \App\Models\User::where('email', 'perencanaan@rsud.id')->first();

        if (!$kepalaIGD || !$kepalaBidang || !$direktur || !$staffPerencanaan) {
            $this->command->error('❌ User tidak lengkap! Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // PERMINTAAN 1: Sudah di-approve Direktur, siap untuk Staff Perencanaan
        $permintaan1 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'bidang' => 'Instalasi Gawat Darurat',
            'tanggal_permintaan' => Carbon::now()->subDays(10),
            'deskripsi' => 'Pengadaan Alat Kesehatan untuk IGD: Defibrillator, Nebulizer, dan Pulse Oximeter untuk meningkatkan pelayanan pasien gawat darurat.',
            'status' => 'disetujui',
            'pic_pimpinan' => 'Staff Perencanaan', // PENTING: Set ke Staff Perencanaan
            'no_nota_dinas' => 'ND/IGD/2025/001',
        ]);

        // Nota Dinas 1: Kepala Instalasi → Kepala Bidang
        $nota1 = NotaDinas::create([
            'permintaan_id' => $permintaan1->permintaan_id,
            'no_nota' => 'ND/IGD/2025/001',
            'dari' => 'Instalasi Gawat Darurat',
            'kepada' => 'Kepala Bidang Pelayanan Medis',
            'tanggal_nota' => Carbon::now()->subDays(9),
            'perihal' => 'Pengadaan Alat Kesehatan IGD',
            'detail' => 'Mohon persetujuan untuk pengadaan alat kesehatan untuk IGD.',
        ]);

        // Disposisi 1: Kepala Bidang → Direktur
        $disposisi1 = Disposisi::create([
            'nota_id' => $nota1->nota_id,
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(8),
            'catatan' => 'Mohon persetujuan Direktur untuk pengadaan ini',
            'status' => 'disetujui',
        ]);

        // Nota Dinas 2: Direktur → Staff Perencanaan
        $nota2 = NotaDinas::create([
            'permintaan_id' => $permintaan1->permintaan_id,
            'no_nota' => 'ND/DIR/2025/001',
            'dari' => 'Direktur RSUD',
            'kepada' => 'Staff Perencanaan',
            'tanggal_nota' => Carbon::now()->subDays(7),
            'perihal' => 'Disposisi Perencanaan Pengadaan Alat Kesehatan IGD',
            'detail' => 'Disetujui. Silakan lakukan perencanaan pengadaan.',
        ]);

        // PERMINTAAN 2: Approved, siap untuk Staff Perencanaan
        $permintaan2 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'bidang' => 'Instalasi Farmasi',
            'tanggal_permintaan' => Carbon::now()->subDays(8),
            'deskripsi' => 'Pengadaan Obat-obatan Emergency: Adrenalin, Atropin, dan Dopamin untuk stok emergency farmasi.',
            'status' => 'disetujui',
            'pic_pimpinan' => 'Staff Perencanaan',
            'no_nota_dinas' => 'ND/FAR/2025/001',
        ]);

        NotaDinas::create([
            'permintaan_id' => $permintaan2->permintaan_id,
            'no_nota' => 'ND/FAR/2025/001',
            'dari' => 'Instalasi Farmasi',
            'kepada' => 'Kepala Bidang Penunjang Medis',
            'tanggal_nota' => Carbon::now()->subDays(7),
            'perihal' => 'Pengadaan Obat Emergency',
        ]);

        NotaDinas::create([
            'permintaan_id' => $permintaan2->permintaan_id,
            'no_nota' => 'ND/DIR/2025/002',
            'dari' => 'Direktur RSUD',
            'kepada' => 'Staff Perencanaan',
            'tanggal_nota' => Carbon::now()->subDays(5),
            'perihal' => 'Disposisi Perencanaan Pengadaan Obat Emergency',
        ]);

        // PERMINTAAN 3: Sudah ada perencanaan (sedang diproses Staff Perencanaan)
        $permintaan3 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'bidang' => 'Instalasi Laboratorium',
            'tanggal_permintaan' => Carbon::now()->subDays(15),
            'deskripsi' => 'Pengadaan Reagen Laboratorium untuk pemeriksaan COVID-19, Hepatitis, dan HIV.',
            'status' => 'proses',
            'pic_pimpinan' => 'Staff Perencanaan',
            'no_nota_dinas' => 'ND/LAB/2025/001',
        ]);

        $nota3a = NotaDinas::create([
            'permintaan_id' => $permintaan3->permintaan_id,
            'no_nota' => 'ND/LAB/2025/001',
            'dari' => 'Instalasi Laboratorium',
            'kepada' => 'Kepala Bidang Penunjang Medis',
            'tanggal_nota' => Carbon::now()->subDays(14),
            'perihal' => 'Pengadaan Reagen Lab',
        ]);

        $disp3 = Disposisi::create([
            'nota_id' => $nota3a->nota_id,
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(13),
            'catatan' => 'Mohon persetujuan',
            'status' => 'disetujui',
        ]);

        $nota3b = NotaDinas::create([
            'permintaan_id' => $permintaan3->permintaan_id,
            'no_nota' => 'ND/DIR/2025/003',
            'dari' => 'Direktur RSUD',
            'kepada' => 'Staff Perencanaan',
            'tanggal_nota' => Carbon::now()->subDays(12),
            'perihal' => 'Disposisi Perencanaan Pengadaan Reagen Lab',
        ]);

        // PERENCANAAN sudah dibuat untuk permintaan 3
        Perencanaan::create([
            'disposisi_id' => $disp3->disposisi_id,
            'tanggal_mulai' => Carbon::now()->subDays(10),
            'tanggal_selesai' => Carbon::now()->addDays(5),
            'anggaran' => 15000000,
            'rencana_kegiatan' => 'Perencanaan pengadaan reagen laboratorium sedang dalam proses',
        ]);

        // PERMINTAAN 4: Melalui Wakil Direktur
        $permintaan4 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'bidang' => 'Instalasi Radiologi',
            'tanggal_permintaan' => Carbon::now()->subDays(6),
            'deskripsi' => 'Pengadaan Film Rontgen dan Kontras untuk pemeriksaan radiologi.',
            'status' => 'disetujui',
            'pic_pimpinan' => 'Staff Perencanaan',
            'no_nota_dinas' => 'ND/RAD/2025/001',
            'wadir_tujuan' => 'Wakil Direktur Pelayanan Medis',
        ]);

        NotaDinas::create([
            'permintaan_id' => $permintaan4->permintaan_id,
            'no_nota' => 'ND/WADIR/2025/001',
            'dari' => 'Wakil Direktur Pelayanan Medis',
            'kepada' => 'Staff Perencanaan',
            'tanggal_nota' => Carbon::now()->subDays(4),
            'perihal' => 'Disposisi Perencanaan Pengadaan Radiologi',
        ]);

        // PERMINTAAN 5: Siap untuk perencanaan
        $permintaan5 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'bidang' => 'Instalasi Bedah Sentral',
            'tanggal_permintaan' => Carbon::now()->subDays(5),
            'deskripsi' => 'Pengadaan Alat Bedah: Surgical Set, Electrocautery, dan Suction untuk OK.',
            'status' => 'disetujui',
            'pic_pimpinan' => 'Staff Perencanaan',
            'no_nota_dinas' => 'ND/BEDAH/2025/001',
        ]);

        NotaDinas::create([
            'permintaan_id' => $permintaan5->permintaan_id,
            'no_nota' => 'ND/DIR/2025/004',
            'dari' => 'Direktur RSUD',
            'kepada' => 'Staff Perencanaan',
            'tanggal_nota' => Carbon::now()->subDays(3),
            'perihal' => 'Disposisi Perencanaan Pengadaan Alat Bedah',
        ]);

        $this->command->info('');
        $this->command->info('✅ Berhasil membuat 5 permintaan untuk Staff Perencanaan:');
        $this->command->info('   1. Permintaan #' . $permintaan1->permintaan_id . ' - Alat Kesehatan IGD (disetujui)');
        $this->command->info('   2. Permintaan #' . $permintaan2->permintaan_id . ' - Obat Emergency (disetujui)');
        $this->command->info('   3. Permintaan #' . $permintaan3->permintaan_id . ' - Reagen Lab (proses - ada perencanaan)');
        $this->command->info('   4. Permintaan #' . $permintaan4->permintaan_id . ' - Film Rontgen (disetujui - via Wadir)');
        $this->command->info('   5. Permintaan #' . $permintaan5->permintaan_id . ' - Alat Bedah (disetujui)');
        $this->command->info('');
        $this->command->info('📊 Status:');
        $this->command->info('   - Status disetujui: 4 permintaan (siap untuk perencanaan)');
        $this->command->info('   - Status proses: 1 permintaan (sudah ada perencanaan)');
        $this->command->info('   - Semua PIC Pimpinan: Staff Perencanaan');
        $this->command->info('');
        $this->command->info('🔐 Login Info:');
        $this->command->info('   Email: perencanaan@rsud.id');
        $this->command->info('   Password: password');
    }
}
