<?php

namespace Database\Seeders;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use App\Models\Disposisi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PenunjangMedisTestSeeder extends Seeder
{
    /**
     * Seed data testing untuk Kabid Penunjang Medis
     * 
     * Workflow:
     * 1. Kepala Instalasi Lab/Radiologi buat permintaan
     * 2. Approve → klasifikasi = 'penunjang_medis'
     * 3. Kabid Penunjang approve → ke Direktur
     * 4. Direktur approve → routing ke Kabid Penunjang
     * 5. Kabid Penunjang (kembali) → ke Staff Perencanaan
     */
    public function run(): void
    {
        $this->command->info('🚀 Membuat data testing untuk Kabid Penunjang Medis...');
        $this->command->info('');

        // Get users
        $kepalaLab = User::where('email', 'kepala.lab@rsud.id')->first();
        $kepalaRadiologi = User::where('email', 'kepala.radiologi@rsud.id')->first();
        $kabidPenunjang = User::where('email', 'kabid.penunjang@rsud.id')->first();
        $direktur = User::where('email', 'direktur@rsud.id')->first();

        if (!$kepalaLab || !$kabidPenunjang || !$direktur) {
            $this->command->error('❌ User tidak lengkap. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // Clean old test data
        $this->command->info('⚠️  Menghapus data testing lama...');
        Permintaan::where('deskripsi', 'LIKE', '%TEST PENUNJANG MEDIS%')->delete();

        // 1. Permintaan dari Laboratorium - SIAP UNTUK DIREKTUR
        $this->command->info('📝 Membuat Permintaan #1: Laboratorium → Kabid Penunjang → Direktur (MENUNGGU)');
        
        $permintaan1 = Permintaan::create([
            'user_id' => $kepalaLab->id,
            'bidang' => 'Penunjang Medis',
            'klasifikasi_permintaan' => 'Penunjang',
            'kabid_tujuan' => 'Bidang Penunjang Medis',
            'tanggal_permintaan' => Carbon::now()->subDays(3),
            'deskripsi' => 'TEST PENUNJANG MEDIS - Pengadaan reagen kimia klinik set lengkap untuk pemeriksaan laboratorium rutin. Set reagen meliputi: Glukosa, Kolesterol, Trigliserida, SGOT/SGPT, Ureum/Kreatinin. Jumlah: 10 set @ Rp 15.000.000 = Rp 150.000.000. Stock reagen saat ini hampir habis dan diperlukan segera untuk kontinuitas pelayanan laboratorium.',
            'status' => 'proses',
            'pic_pimpinan' => 'Direktur',
        ]);

        // Nota Dinas untuk permintaan 1
        $notaDinas1 = NotaDinas::create([
            'permintaan_id' => $permintaan1->permintaan_id,
            'no_nota' => 'ND/LAB/' . date('Y') . '/001',
            'tanggal_nota' => Carbon::now()->subDays(3),
            'perihal' => 'Permohonan Pengadaan Reagen Kimia Klinik',
            'dari' => 'Kepala Instalasi Laboratorium',
            'kepada' => 'Kabid Penunjang Medis',
        ]);

        // Disposisi: Kepala Lab → Kabid Penunjang
        Disposisi::create([
            'nota_id' => $notaDinas1->nota_id,
            'jabatan_tujuan' => 'Bidang Penunjang Medis',
            'tanggal_disposisi' => Carbon::now()->subDays(3),
            'catatan' => 'Disetujui oleh Kepala Instalasi Laboratorium. Mohon review dan persetujuan untuk diteruskan ke Direktur.',
            'status' => 'dalam_proses',
        ]);

        // Disposisi: Kabid Penunjang → Direktur
        Disposisi::create([
            'nota_id' => $notaDinas1->nota_id,
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(2),
            'catatan' => 'Disetujui oleh Kepala Bidang Penunjang Medis. Mohon persetujuan Direktur untuk proses pengadaan reagen laboratorium.',
            'status' => 'disetujui',
        ]);

        // 2. Permintaan dari Radiologi - SIAP UNTUK DIREKTUR
        if ($kepalaRadiologi) {
            $this->command->info('📝 Membuat Permintaan #2: Radiologi → Kabid Penunjang → Direktur (MENUNGGU)');
            
            $permintaan2 = Permintaan::create([
                'user_id' => $kepalaRadiologi->id,
                'bidang' => 'Penunjang Medis',
                'klasifikasi_permintaan' => 'Penunjang',
                'kabid_tujuan' => 'Bidang Penunjang Medis',
                'tanggal_permintaan' => Carbon::now()->subDays(2),
                'deskripsi' => 'TEST PENUNJANG MEDIS - Pengadaan film radiologi dan bahan kimia processing untuk kebutuhan rutin Instalasi Radiologi. Item: Film X-Ray 14x17 inch (500 lembar), Film X-Ray 10x12 inch (300 lembar), Developer 5 liter (10 galon), Fixer 5 liter (10 galon). Total: Rp 25.000.000. Diperlukan untuk pemeriksaan radiologi konvensional.',
                'status' => 'proses',
                'pic_pimpinan' => 'Direktur',
            ]);

            $notaDinas2 = NotaDinas::create([
                'permintaan_id' => $permintaan2->permintaan_id,
                'no_nota' => 'ND/RAD/' . date('Y') . '/001',
                'tanggal_nota' => Carbon::now()->subDays(2),
                'perihal' => 'Permohonan Pengadaan Film Radiologi',
                'dari' => 'Kepala Instalasi Radiologi',
                'kepada' => 'Kabid Penunjang Medis',
            ]);

            Disposisi::create([
                'nota_id' => $notaDinas2->nota_id,
                'jabatan_tujuan' => 'Bidang Penunjang Medis',
                'tanggal_disposisi' => Carbon::now()->subDays(2),
                'catatan' => 'Disetujui oleh Kepala Instalasi Radiologi. Mohon review untuk kelengkapan stok film.',
                'status' => 'dalam_proses',
            ]);

            Disposisi::create([
                'nota_id' => $notaDinas2->nota_id,
                'jabatan_tujuan' => 'Direktur',
                'tanggal_disposisi' => Carbon::now()->subDays(1),
                'catatan' => 'Disetujui oleh Kepala Bidang Penunjang Medis. Mohon persetujuan untuk pengadaan film radiologi.',
                'status' => 'disetujui',
            ]);
        }

        $this->command->info('');
        $this->command->info('✅ Data testing Kabid Penunjang Medis berhasil dibuat!');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('📋 DATA TESTING PENUNJANG MEDIS');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('Permintaan #1:');
        $this->command->info('  - Dari: Kepala Instalasi Laboratorium');
        $this->command->info('  - Item: Reagen Kimia Klinik (10 set @ Rp 15jt)');
        $this->command->info('  - Klasifikasi: Penunjang');
        $this->command->info('  - Status: Menunggu review Direktur');
        $this->command->info('  - Kabid Tujuan: Bidang Penunjang Medis');
        $this->command->info('');
        
        if ($kepalaRadiologi) {
            $this->command->info('Permintaan #2:');
            $this->command->info('  - Dari: Kepala Instalasi Radiologi');
            $this->command->info('  - Item: Film Radiologi & Chemicals (Rp 25jt)');
            $this->command->info('  - Klasifikasi: Penunjang');
            $this->command->info('  - Status: Menunggu review Direktur');
            $this->command->info('  - Kabid Tujuan: Bidang Penunjang Medis');
            $this->command->info('');
        }
        
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🔐 TESTING STEPS:');
        $this->command->info('');
        $this->command->info('1. Login sebagai Direktur:');
        $this->command->info('   Email: direktur@rsud.id');
        $this->command->info('   Password: password');
        $this->command->info('   Action: Approve permintaan penunjang');
        $this->command->info('');
        $this->command->info('2. Expected: Routing otomatis ke Bidang Penunjang Medis');
        $this->command->info('');
        $this->command->info('3. Login sebagai Kabid Penunjang:');
        $this->command->info('   Email: kabid.penunjang@rsud.id');
        $this->command->info('   Password: password');
        $this->command->info('   Expected: Permintaan muncul di dashboard (dari Direktur)');
        $this->command->info('');
        $this->command->info('4. Kabid approve → Kirim ke Staff Perencanaan');
        $this->command->info('');
        $this->command->info('5. Verify dengan test script:');
        $this->command->info('   php test_penunjang_routing.php');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════════');
    }
}
