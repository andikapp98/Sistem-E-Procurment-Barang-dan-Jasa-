<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KabidToStaffPerencanaanSeeder extends Seeder
{
    /**
     * Seed data workflow: Kabid → Wadir → Direktur → Staff Perencanaan
     * Status: DISETUJUI dan siap untuk upload dokumen oleh Staff Perencanaan
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding workflow Kabid → Staff Perencanaan...');
        $this->command->info('');
        
        // Get user IDs
        $kepalaIGD = DB::table('users')->where('email', 'kepala.igd@rsud.id')->value('id');
        $kepalaFarmasi = DB::table('users')->where('email', 'kepala.farmasi@rsud.id')->value('id');
        $kepalaLab = DB::table('users')->where('email', 'kepala.lab@rsud.id')->value('id');
        
        // Data permintaan yang sudah disetujui hingga Direktur
        $permintaanData = [
            [
                'user_id' => $kepalaIGD,
                'bidang' => 'Gawat Darurat',
                'tanggal_permintaan' => Carbon::now()->subDays(15),
                'deskripsi' => 'Pengadaan 2 unit defibrillator portable untuk ruang IGD. Defibrillator yang ada sudah berusia 10 tahun dan sering mengalami gangguan. Diperlukan segera untuk meningkatkan kesiapan penanganan pasien cardiac arrest.',
                'status' => 'disetujui',
                'pic_pimpinan' => 'Staff Perencanaan',
                'estimasi_biaya' => 'Rp 75.000.000',
                'days_ago' => 15,
            ],
            [
                'user_id' => $kepalaFarmasi,
                'bidang' => 'Farmasi',
                'tanggal_permintaan' => Carbon::now()->subDays(20),
                'deskripsi' => 'Pengadaan obat-obatan esensial untuk stok 3 bulan: Antibiotik (Ceftriaxone 1g - 500 vial, Levofloxacin 500mg - 1000 tablet), Analgesik (Ketorolac 30mg - 500 ampul, Tramadol 50mg - 2000 tablet), dan Cairan Infus (RL 500ml - 3000 botol, NS 500ml - 2000 botol, D5% 500ml - 1000 botol).',
                'status' => 'disetujui',
                'pic_pimpinan' => 'Staff Perencanaan',
                'estimasi_biaya' => 'Rp 150.000.000',
                'days_ago' => 20,
            ],
            [
                'user_id' => $kepalaLab,
                'bidang' => 'Laboratorium',
                'tanggal_permintaan' => Carbon::now()->subDays(12),
                'deskripsi' => 'Pengadaan reagen laboratorium untuk pemeriksaan hematologi lengkap, kimia darah, dan serologi. Stok saat ini tinggal untuk 2 minggu. Diperlukan reagen untuk 1000 sampel. Termasuk: Reagen hematologi (CBC, Diff), Reagen kimia klinik (GDS, Ureum, Creatinin, SGOT, SGPT), dan Reagen serologi (HBsAg, HIV).',
                'status' => 'disetujui',
                'pic_pimpinan' => 'Staff Perencanaan',
                'estimasi_biaya' => 'Rp 85.000.000',
                'days_ago' => 12,
            ],
        ];
        
        foreach ($permintaanData as $index => $data) {
            $daysAgo = $data['days_ago'];
            
            // Insert permintaan
            $permintaanId = DB::table('permintaan')->insertGetId([
                'user_id' => $data['user_id'],
                'bidang' => $data['bidang'],
                'tanggal_permintaan' => $data['tanggal_permintaan'],
                'deskripsi' => $data['deskripsi'],
                'status' => $data['status'],
                'pic_pimpinan' => $data['pic_pimpinan'],
                'created_at' => $data['tanggal_permintaan'],
                'updated_at' => Carbon::now()->subDays(1),
            ]);
            
            $this->command->info('   ✓ Permintaan #' . $permintaanId . ': ' . $data['bidang'] . ' - ' . $data['estimasi_biaya']);
        }
        
        $this->command->info('');
        $this->command->info('✅ Seeding selesai!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   • 3 Permintaan Status DISETUJUI');
        $this->command->info('   • PIC: Staff Perencanaan');
        $this->command->info('   • Workflow: Kepala Instalasi → Kabid → Wadir → Direktur → Staff Perencanaan');
        $this->command->info('   • Tahap sekarang: Menunggu upload dokumen');
        $this->command->info('');
        $this->command->info('📝 Dokumen yang harus diupload:');
        $this->command->info('   1. Nota Dinas');
        $this->command->info('   2. DPP (Dokumen Perencanaan Pengadaan)');
        $this->command->info('   3. KAK (Kerangka Acuan Kerja)');
        $this->command->info('   4. SP (Surat Pesanan)');
        $this->command->info('   5. Kuitansi');
        $this->command->info('   6. BAST (Berita Acara Serah Terima)');
        $this->command->info('');
        $this->command->info('🎯 Permintaan:');
        $this->command->info('   1. Gawat Darurat  → Defibrillator (Rp 75 juta)');
        $this->command->info('   2. Farmasi        → Obat Esensial (Rp 150 juta)');
        $this->command->info('   3. Laboratorium   → Reagen Lab (Rp 85 juta)');
        $this->command->info('');
        $this->command->info('👤 Login sebagai Staff Perencanaan:');
        $this->command->info('   Email: perencanaan@rsud.id');
        $this->command->info('   Password: password');
    }
}
