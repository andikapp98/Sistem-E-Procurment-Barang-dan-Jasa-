<?php

namespace Database\Seeders;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use App\Models\Disposisi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

/**
 * Seeder untuk Workflow Direktur
 * 
 * Membuat data lengkap dari Kepala Instalasi → Kepala Bidang → DIREKTUR
 * 
 * Workflow:
 * 1. Kepala Instalasi buat permintaan
 * 2. Nota Dinas ke Kepala Bidang
 * 3. Kepala Bidang approve → Disposisi ke DIREKTUR (skip Wakil Direktur)
 * 4. Direktur dapat review (approve/reject/revisi)
 * 
 * Data yang dibuat:
 * - 3 permintaan untuk testing Direktur
 * - Status: proses (menunggu review Direktur)
 * - pic_pimpinan: Direktur
 */
class DirekturWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Mulai membuat data workflow untuk Direktur...');
        $this->command->info('');

        // Get users
        $direktur = User::where('role', 'direktur')->first();
        $kepalaIGD = User::where('unit_kerja', 'Gawat Darurat')->where('role', 'kepala_instalasi')->first();
        $kepalaFarmasi = User::where('unit_kerja', 'Farmasi')->where('role', 'kepala_instalasi')->first();
        $kepalaLab = User::where('unit_kerja', 'Laboratorium')->where('role', 'kepala_instalasi')->first();
        $kabidYanmed = User::where('unit_kerja', 'Bidang Pelayanan Medis')->where('role', 'kepala_bidang')->first();
        $kabidPenunjang = User::where('unit_kerja', 'Bidang Penunjang Medis')->where('role', 'kepala_bidang')->first();
        $kabidKeperawatan = User::where('unit_kerja', 'Bidang Keperawatan')->where('role', 'kepala_bidang')->first();

        if (!$direktur) {
            $this->command->error('❌ User Direktur tidak ditemukan! Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        if (!$kepalaIGD || !$kepalaFarmasi || !$kepalaLab) {
            $this->command->error('❌ User Kepala Instalasi tidak ditemukan! Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // Hapus data lama jika ada
        $this->command->warn('⚠️  Menghapus data testing Direktur yang lama...');
        $this->cleanOldData();

        $permintaans = [];

        // ========================================
        // 1. PERMINTAAN IGD → Kepala Bidang Yanmed → DIREKTUR
        // ========================================
        $this->command->info('📝 Membuat Permintaan #1: IGD → Kabid Yanmed → Direktur (MENUNGGU REVIEW)');
        
        $permintaan1 = Permintaan::create([
            'user_id' => $kepalaIGD->id,
            'tanggal_permintaan' => Carbon::now()->subDays(7),
            'bidang' => 'Gawat Darurat',
            'deskripsi' => 'Pengadaan Defibrillator Portabel untuk Unit IGD. Defibrillator lama sudah tidak berfungsi optimal dan perlu diganti untuk meningkatkan kualitas pelayanan kegawatdaruratan. Spesifikasi: Biphasic, Energy 200-360 Joules, LCD Display, Battery backup minimal 4 jam.',
            'status' => 'proses',
            'pic_pimpinan' => 'Direktur',
        ]);

        // Nota Dinas dari Kepala IGD ke Kepala Bidang Yanmed
        $notaDinas1 = NotaDinas::create([
            'permintaan_id' => $permintaan1->permintaan_id,
            'no_nota' => 'ND/IGD/001/' . Carbon::now()->format('m/Y'),
            'tanggal_nota' => Carbon::now()->subDays(6),
            'dari' => 'Kepala Instalasi Gawat Darurat',
            'kepada' => 'Kepala Bidang Pelayanan Medis',
            'perihal' => 'Permohonan Pengadaan Defibrillator Portabel',
        ]);

        // Disposisi 1: Kepala Instalasi → Kepala Bidang
        Disposisi::create([
            'nota_id' => $notaDinas1->nota_id,
            'jabatan_tujuan' => 'Kepala Bidang Pelayanan Medis',
            'tanggal_disposisi' => Carbon::now()->subDays(5),
            'catatan' => 'Mohon ditindaklanjuti. Kebutuhan mendesak untuk pelayanan IGD.',
            'status' => 'diproses',
        ]);

        // Disposisi 2: Kepala Bidang → DIREKTUR (skip Wakil Direktur)
        Disposisi::create([
            'nota_id' => $notaDinas1->nota_id,
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(3),
            'catatan' => 'Disetujui oleh Kepala Bidang Pelayanan Medis. Diteruskan ke Direktur untuk Final Approval. Pengadaan ini sangat mendesak untuk meningkatkan kualitas pelayanan IGD.',
            'status' => 'selesai',
        ]);

        $permintaans[] = [
            'id' => $permintaan1->permintaan_id,
            'dari' => 'IGD',
            'via' => 'Kabid Yanmed',
            'status' => 'Menunggu Review Direktur',
            'item' => 'Defibrillator Portabel',
        ];

        // ========================================
        // 2. PERMINTAAN FARMASI → Kepala Bidang Keperawatan → DIREKTUR
        // ========================================
        $this->command->info('📝 Membuat Permintaan #2: Farmasi → Kabid Keperawatan → Direktur (MENUNGGU REVIEW)');
        
        $permintaan2 = Permintaan::create([
            'user_id' => $kepalaFarmasi->id,
            'tanggal_permintaan' => Carbon::now()->subDays(5),
            'bidang' => 'Farmasi',
            'deskripsi' => 'Pengadaan Sistem Otomasi Farmasi (Pharmacy Automation System). Sistem ini akan meningkatkan efisiensi pelayanan farmasi, mengurangi medication error, dan mempercepat proses dispensing obat. Fitur: Barcode scanning, inventory management, automated dispensing cabinet, terintegrasi dengan EMR.',
            'status' => 'proses',
            'pic_pimpinan' => 'Direktur',
        ]);

        $notaDinas2 = NotaDinas::create([
            'permintaan_id' => $permintaan2->permintaan_id,
            'no_nota' => 'ND/FAR/002/' . Carbon::now()->format('m/Y'),
            'tanggal_nota' => Carbon::now()->subDays(4),
            'dari' => 'Kepala Instalasi Farmasi',
            'kepada' => 'Kepala Bidang Keperawatan',
            'perihal' => 'Permohonan Pengadaan Sistem Otomasi Farmasi',
        ]);

        Disposisi::create([
            'nota_id' => $notaDinas2->nota_id,
            'jabatan_tujuan' => 'Kepala Bidang Keperawatan',
            'tanggal_disposisi' => Carbon::now()->subDays(3),
            'catatan' => 'Sistem ini akan meningkatkan patient safety dan efisiensi.',
            'status' => 'diproses',
        ]);

        Disposisi::create([
            'nota_id' => $notaDinas2->nota_id,
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(2),
            'catatan' => 'Disetujui oleh Kepala Bidang Keperawatan. Diteruskan ke Direktur untuk persetujuan final. Investasi strategis untuk meningkatkan mutu pelayanan farmasi.',
            'status' => 'selesai',
        ]);

        $permintaans[] = [
            'id' => $permintaan2->permintaan_id,
            'dari' => 'Farmasi',
            'via' => 'Kabid Keperawatan',
            'status' => 'Menunggu Review Direktur',
            'item' => 'Sistem Otomasi Farmasi',
        ];

        // ========================================
        // 3. PERMINTAAN LABORATORIUM → Kepala Bidang Penunjang → DIREKTUR
        // ========================================
        $this->command->info('📝 Membuat Permintaan #3: Lab → Kabid Penunjang → Direktur (MENUNGGU REVIEW)');
        
        $permintaan3 = Permintaan::create([
            'user_id' => $kepalaLab->id,
            'tanggal_permintaan' => Carbon::now()->subDays(4),
            'bidang' => 'Laboratorium',
            'deskripsi' => 'Pengadaan Chemistry Analyzer Otomatis untuk pemeriksaan kimia darah. Alat ini akan meningkatkan kapasitas dan kecepatan pemeriksaan laboratorium. Spesifikasi: Throughput 400 test/hour, ISE module, refrigerated reagent compartment, auto calibration, minimal 40 test parameters.',
            'status' => 'proses',
            'pic_pimpinan' => 'Direktur',
        ]);

        $notaDinas3 = NotaDinas::create([
            'permintaan_id' => $permintaan3->permintaan_id,
            'no_nota' => 'ND/LAB/003/' . Carbon::now()->format('m/Y'),
            'tanggal_nota' => Carbon::now()->subDays(3),
            'dari' => 'Kepala Instalasi Laboratorium',
            'kepada' => 'Kepala Bidang Penunjang Medis',
            'perihal' => 'Permohonan Pengadaan Chemistry Analyzer Otomatis',
        ]);

        Disposisi::create([
            'nota_id' => $notaDinas3->nota_id,
            'jabatan_tujuan' => 'Kepala Bidang Penunjang Medis',
            'tanggal_disposisi' => Carbon::now()->subDays(2),
            'catatan' => 'Alat laboratorium yang ada sudah overload. Perlu upgrade.',
            'status' => 'diproses',
        ]);

        Disposisi::create([
            'nota_id' => $notaDinas3->nota_id,
            'jabatan_tujuan' => 'Direktur',
            'tanggal_disposisi' => Carbon::now()->subDays(1),
            'catatan' => 'Disetujui oleh Kepala Bidang Penunjang Medis. Diteruskan ke Direktur untuk approval final. Pengadaan ini penting untuk meningkatkan kualitas layanan laboratorium.',
            'status' => 'selesai',
        ]);

        $permintaans[] = [
            'id' => $permintaan3->permintaan_id,
            'dari' => 'Laboratorium',
            'via' => 'Kabid Penunjang',
            'status' => 'Menunggu Review Direktur',
            'item' => 'Chemistry Analyzer',
        ];

        // Summary
        $this->command->info('');
        $this->command->info('✅ Data workflow Direktur berhasil dibuat!');
        $this->command->info('');
        $this->command->info('📊 RINGKASAN DATA TESTING DIREKTUR');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info(sprintf('%-5s | %-15s | %-20s | %-30s | %s', 'ID', 'Dari Instalasi', 'Via Kepala Bidang', 'Item', 'Status'));
        $this->command->info('───────────────────────────────────────────────────────────────────────────────────────');
        
        foreach ($permintaans as $p) {
            $this->command->info(sprintf('%-5s | %-15s | %-20s | %-30s | %s', 
                $p['id'], 
                $p['dari'], 
                $p['via'], 
                $p['item'],
                $p['status']
            ));
        }
        
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🎯 TESTING CHECKLIST:');
        $this->command->info('');
        $this->command->info('1. LOGIN DIREKTUR');
        $this->command->info('   Email    : direktur@rsud.id');
        $this->command->info('   Password : password');
        $this->command->info('');
        $this->command->info('2. DASHBOARD DIREKTUR');
        $this->command->info('   ✅ Cek stats: Total = 3, Menunggu = 3');
        $this->command->info('   ✅ Cek recent list: Harus muncul 3 permintaan');
        $this->command->info('');
        $this->command->info('3. REVIEW PERMINTAAN (Click "Review" di setiap permintaan)');
        $this->command->info('   ✅ Test APPROVE (Final Approval) → ke Staff Perencanaan');
        $this->command->info('   ✅ Test REJECT dengan alasan min 10 chars → stop process');
        $this->command->info('   ✅ Test REVISI dengan catatan min 10 chars → back to Kepala Bidang');
        $this->command->info('');
        $this->command->info('4. VERIFIKASI WORKFLOW');
        $this->command->info('   ✅ Approve: status = disetujui, pic = Staff Perencanaan');
        $this->command->info('   ✅ Reject: status = ditolak, pic = Unit Pemohon');
        $this->command->info('   ✅ Revisi: status = revisi, pic = Kepala Bidang (CRITICAL!)');
        $this->command->info('');
        $this->command->info('5. CEK DISPOSISI (gunakan VERIFY_DIREKTUR_WORKFLOW.sql)');
        $this->command->info('   ✅ Query #6: Cek revisi ke Kepala Bidang');
        $this->command->info('   ✅ Query #7: Summary statistics');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('💡 TIPS:');
        $this->command->info('   - Gunakan permintaan #1 untuk test APPROVE');
        $this->command->info('   - Gunakan permintaan #2 untuk test REJECT');
        $this->command->info('   - Gunakan permintaan #3 untuk test REVISI');
        $this->command->info('   - Setelah REVISI, login sebagai Kepala Bidang untuk verify permintaan kembali');
        $this->command->info('');
    }

    /**
     * Hapus data testing lama
     */
    private function cleanOldData(): void
    {
        // Cari permintaan testing Direktur (yang pic_pimpinan = Direktur dan dibuat oleh seeder ini)
        $testPermintaans = Permintaan::where('pic_pimpinan', 'Direktur')
            ->where('status', 'proses')
            ->whereIn('bidang', ['Gawat Darurat', 'Farmasi', 'Laboratorium'])
            ->where('deskripsi', 'like', '%Defibrillator Portabel%')
            ->orWhere('deskripsi', 'like', '%Sistem Otomasi Farmasi%')
            ->orWhere('deskripsi', 'like', '%Chemistry Analyzer Otomatis%')
            ->pluck('permintaan_id');

        if ($testPermintaans->isNotEmpty()) {
            // Hapus disposisi
            $notaDinasIds = NotaDinas::whereIn('permintaan_id', $testPermintaans)->pluck('nota_id');
            if ($notaDinasIds->isNotEmpty()) {
                Disposisi::whereIn('nota_id', $notaDinasIds)->delete();
                $this->command->info('   - Disposisi testing dihapus');
            }

            // Hapus nota dinas
            NotaDinas::whereIn('permintaan_id', $testPermintaans)->delete();
            $this->command->info('   - Nota Dinas testing dihapus');

            // Hapus permintaan
            Permintaan::whereIn('permintaan_id', $testPermintaans)->delete();
            $this->command->info('   - Permintaan testing dihapus');
        }

        $this->command->info('');
    }
}
