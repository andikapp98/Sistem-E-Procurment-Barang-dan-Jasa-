<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkflowKabidToStaffPerencanaanSeeder extends Seeder
{
    /**
     * Seeder lengkap untuk workflow: Kepala Instalasi → Kabid → Wadir → Direktur → Staff Perencanaan
     * Dengan Nota Dinas dan Disposisi lengkap di setiap tahap
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🌱 ===== SEEDER WORKFLOW LENGKAP: KABID → STAFF PERENCANAAN =====');
        $this->command->info('');
        
        // Get user IDs
        $kepalaIGD = DB::table('users')->where('email', 'kepala.igd@rsud.id')->value('id');
        $kepalaFarmasi = DB::table('users')->where('email', 'kepala.farmasi@rsud.id')->value('id');
        $kepalaLab = DB::table('users')->where('email', 'kepala.lab@rsud.id')->value('id');
        
        // Data permintaan
        $permintaanData = [
            [
                'user_id' => $kepalaIGD,
                'bidang' => 'Gawat Darurat',
                'tanggal_permintaan' => Carbon::now()->subDays(25),
                'deskripsi' => 'Pengadaan 2 unit defibrillator portable untuk ruang IGD. Defibrillator yang ada sudah berusia 10 tahun dan sering mengalami gangguan. Diperlukan segera untuk meningkatkan kesiapan penanganan pasien cardiac arrest.',
                'estimasi_biaya' => 75000000,
                'days_ago' => 25,
                'no_nota' => 'ND/IGD/001/X/2025',
            ],
            [
                'user_id' => $kepalaFarmasi,
                'bidang' => 'Farmasi',
                'tanggal_permintaan' => Carbon::now()->subDays(30),
                'deskripsi' => 'Pengadaan obat-obatan esensial untuk stok 3 bulan: Antibiotik (Ceftriaxone 1g - 500 vial, Levofloxacin 500mg - 1000 tablet), Analgesik (Ketorolac 30mg - 500 ampul, Tramadol 50mg - 2000 tablet), dan Cairan Infus (RL 500ml - 3000 botol, NS 500ml - 2000 botol, D5% 500ml - 1000 botol).',
                'estimasi_biaya' => 150000000,
                'days_ago' => 30,
                'no_nota' => 'ND/FARM/002/X/2025',
            ],
            [
                'user_id' => $kepalaLab,
                'bidang' => 'Laboratorium',
                'tanggal_permintaan' => Carbon::now()->subDays(20),
                'deskripsi' => 'Pengadaan reagen laboratorium untuk pemeriksaan hematologi lengkap, kimia darah, dan serologi. Stok saat ini tinggal untuk 2 minggu. Diperlukan reagen untuk 1000 sampel. Termasuk: Reagen hematologi (CBC, Diff), Reagen kimia klinik (GDS, Ureum, Creatinin, SGOT, SGPT), dan Reagen serologi (HBsAg, HIV).',
                'estimasi_biaya' => 85000000,
                'days_ago' => 20,
                'no_nota' => 'ND/LAB/003/X/2025',
            ],
        ];
        
        foreach ($permintaanData as $index => $data) {
            $num = $index + 1;
            $this->command->info("📋 Permintaan #{$num}: {$data['bidang']}");
            $this->command->info("   Estimasi: Rp " . number_format($data['estimasi_biaya'], 0, ',', '.'));
            $this->command->info('');
            
            $daysAgo = $data['days_ago'];
            
            // 1. Insert Permintaan
            $permintaanId = DB::table('permintaan')->insertGetId([
                'user_id' => $data['user_id'],
                'bidang' => $data['bidang'],
                'tanggal_permintaan' => $data['tanggal_permintaan'],
                'deskripsi' => $data['deskripsi'],
                'status' => 'disetujui',
                'pic_pimpinan' => 'Staff Perencanaan',
                'created_at' => $data['tanggal_permintaan'],
                'updated_at' => Carbon::now()->subDays(1),
            ]);
            $this->command->info("   ✓ Permintaan ID: {$permintaanId}");
            
            // 2. Nota Dinas dari Kepala Instalasi ke Kabid
            $notaId1 = DB::table('nota_dinas')->insertGetId([
                'permintaan_id' => $permintaanId,
                'tanggal_nota' => Carbon::now()->subDays($daysAgo - 1),
                'no_nota' => $data['no_nota'],
                'dari' => 'Kepala Instalasi ' . $data['bidang'],
                'kepada' => 'Kepala Bidang Pelayanan',
                'perihal' => 'Permintaan Pengadaan ' . $data['bidang'] . ' - ' . $data['deskripsi'],
                'created_at' => Carbon::now()->subDays($daysAgo - 1),
                'updated_at' => Carbon::now()->subDays($daysAgo - 2),
            ]);
            $this->command->info("   ✓ Nota Dinas #1: Kepala Instalasi → Kabid");
            
            // 3. Disposisi dari Kabid ke Wadir
            $disposisiId1 = DB::table('disposisi')->insertGetId([
                'nota_id' => $notaId1,
                'no_disposisi' => 'DISP/KABID/' . str_pad($num, 3, '0', STR_PAD_LEFT) . '/X/2025',
                'tanggal_disposisi' => Carbon::now()->subDays($daysAgo - 3),
                'dari' => 'Kepala Bidang Pelayanan',
                'kepada' => 'Wakil Direktur',
                'isi_disposisi' => 'Mohon ditindaklanjuti. Permintaan ini sangat urgent untuk operasional ' . $data['bidang'] . '.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays($daysAgo - 3),
                'updated_at' => Carbon::now()->subDays($daysAgo - 4),
            ]);
            $this->command->info("   ✓ Disposisi #1: Kabid → Wadir");
            
            // 4. Nota Dinas dari Wadir ke Direktur
            $notaId2 = DB::table('nota_dinas')->insertGetId([
                'permintaan_id' => $permintaanId,
                'tanggal_nota' => Carbon::now()->subDays($daysAgo - 5),
                'no_nota' => str_replace('/X/', '/WADIR/', $data['no_nota']),
                'dari' => 'Wakil Direktur',
                'kepada' => 'Direktur',
                'perihal' => 'Rekomendasi Pengadaan ' . $data['bidang'] . ' - Berdasarkan review, permintaan ini layak untuk disetujui dengan estimasi biaya Rp ' . 
                            number_format($data['estimasi_biaya'], 0, ',', '.'),
                'created_at' => Carbon::now()->subDays($daysAgo - 5),
                'updated_at' => Carbon::now()->subDays($daysAgo - 6),
            ]);
            $this->command->info("   ✓ Nota Dinas #2: Wadir → Direktur");
            
            // 5. Disposisi dari Direktur ke Staff Perencanaan
            $disposisiId2 = DB::table('disposisi')->insertGetId([
                'nota_id' => $notaId2,
                'no_disposisi' => 'DISP/DIR/' . str_pad($num, 3, '0', STR_PAD_LEFT) . '/X/2025',
                'tanggal_disposisi' => Carbon::now()->subDays($daysAgo - 7),
                'dari' => 'Direktur',
                'kepada' => 'Staff Perencanaan',
                'isi_disposisi' => 'Disetujui. Silakan proses perencanaan pengadaan dan upload dokumen yang diperlukan: Nota Dinas, DPP, KAK, SP, Kuitansi, dan BAST.',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays($daysAgo - 7),
                'updated_at' => Carbon::now()->subDays($daysAgo - 8),
            ]);
            $this->command->info("   ✓ Disposisi #2: Direktur → Staff Perencanaan");
            $this->command->info('');
        }
        
        $this->command->info('✅ ===== SEEDING SELESAI =====');
        $this->command->info('');
        $this->command->info('📊 SUMMARY:');
        $this->command->info('   • 3 Permintaan Status: DISETUJUI');
        $this->command->info('   • PIC Sekarang: Staff Perencanaan');
        $this->command->info('   • Total Nota Dinas: 6 (2 per permintaan)');
        $this->command->info('   • Total Disposisi: 6 (2 per permintaan)');
        $this->command->info('');
        $this->command->info('🔄 WORKFLOW LENGKAP:');
        $this->command->info('   1. Kepala Instalasi → Membuat Permintaan');
        $this->command->info('   2. Kepala Instalasi → Nota Dinas ke Kabid');
        $this->command->info('   3. Kabid → Disposisi ke Wadir');
        $this->command->info('   4. Wadir → Nota Dinas ke Direktur');
        $this->command->info('   5. Direktur → Disposisi ke Staff Perencanaan');
        $this->command->info('   6. Staff Perencanaan → Upload 6 Dokumen');
        $this->command->info('');
        $this->command->info('📝 DOKUMEN YANG HARUS DIUPLOAD (6):');
        $this->command->info('   1. Nota Dinas');
        $this->command->info('   2. DPP (Dokumen Perencanaan Pengadaan)');
        $this->command->info('   3. KAK (Kerangka Acuan Kerja)');
        $this->command->info('   4. SP (Surat Pesanan)');
        $this->command->info('   5. Kuitansi');
        $this->command->info('   6. BAST (Berita Acara Serah Terima)');
        $this->command->info('');
        $this->command->info('🎯 PERMINTAAN:');
        $this->command->info('   1. Gawat Darurat → Defibrillator (Rp 75 juta)');
        $this->command->info('   2. Farmasi → Obat Esensial (Rp 150 juta)');
        $this->command->info('   3. Laboratorium → Reagen Lab (Rp 85 juta)');
        $this->command->info('');
        $this->command->info('👤 LOGIN INFO:');
        $this->command->info('   📧 Email: perencanaan@rsud.id');
        $this->command->info('   🔑 Password: password');
        $this->command->info('');
        $this->command->info('🚀 AKSI SELANJUTNYA:');
        $this->command->info('   • Login sebagai Staff Perencanaan');
        $this->command->info('   • Buka Dashboard Staff Perencanaan');
        $this->command->info('   • Pilih permintaan yang akan diproses');
        $this->command->info('   • Klik "Scan Berkas" untuk upload dokumen');
        $this->command->info('   • Upload semua 6 dokumen yang diperlukan');
        $this->command->info('   • Setelah lengkap, permintaan otomatis lanjut ke Bagian Pengadaan');
        $this->command->info('');
    }
}
