<?php

namespace Database\Seeders;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use App\Models\Disposisi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

/**
 * Seeder untuk 10 Permintaan yang Siap Disetujui Direktur
 * 
 * Workflow: Kepala Instalasi → Kepala Bidang → DIREKTUR → Kepala Bidang → Staff Perencanaan
 * Status: Semua dalam status "proses" dengan pic_pimpinan = "Direktur"
 * 
 * Setelah Direktur approve:
 * - Permintaan kembali ke Kepala Bidang (pic_pimpinan = "Kepala Bidang")
 * - Kepala Bidang akan disposisi ke Staff Perencanaan
 * 
 * Data:
 * - 10 permintaan dari berbagai unit instalasi
 * - Sudah melalui approval Kepala Bidang
 * - Siap untuk final approval oleh Direktur
 */
class DirekturApproval10Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Membuat 10 permintaan untuk disetujui Direktur...');
        $this->command->info('');

        // Get users
        $direktur = User::where('role', 'direktur')->first();
        
        // Kepala Instalasi
        $kepalaIGD = User::where('unit_kerja', 'Gawat Darurat')->where('role', 'kepala_instalasi')->first();
        $kepalaFarmasi = User::where('unit_kerja', 'Farmasi')->where('role', 'kepala_instalasi')->first();
        $kepalaLab = User::where('unit_kerja', 'Laboratorium')->where('role', 'kepala_instalasi')->first();
        $kepalaRadiologi = User::where('unit_kerja', 'Radiologi')->where('role', 'kepala_instalasi')->first();
        $kepalaOK = User::where('unit_kerja', 'Kamar Operasi')->where('role', 'kepala_instalasi')->first();
        $kepalaPerawatan = User::where('unit_kerja', 'Rawat Inap')->where('role', 'kepala_instalasi')->first();
        $kepalaPoliklinik = User::where('unit_kerja', 'Poliklinik')->where('role', 'kepala_instalasi')->first();
        $kepalaICU = User::where('unit_kerja', 'ICU')->where('role', 'kepala_instalasi')->first();
        
        // Kepala Bidang
        $kabidYanmed = User::where('unit_kerja', 'Bidang Pelayanan Medis')->where('role', 'kepala_bidang')->first();
        $kabidPenunjang = User::where('unit_kerja', 'Bidang Penunjang Medis')->where('role', 'kepala_bidang')->first();
        $kabidKeperawatan = User::where('unit_kerja', 'Bidang Keperawatan')->where('role', 'kepala_bidang')->first();

        if (!$direktur) {
            $this->command->error('❌ User Direktur tidak ditemukan! Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // Hapus data lama
        $this->command->warn('⚠️  Menghapus data testing lama...');
        $this->cleanOldData();

        $permintaans = [];
        $counter = 1;

        // Data template untuk 10 permintaan
        $dataPermintaan = [
            [
                'kepala' => $kepalaIGD,
                'kabid' => $kabidYanmed,
                'unit' => 'Gawat Darurat',
                'kabid_name' => 'Pelayanan Medis',
                'item' => 'Ventilator Portabel',
                'deskripsi' => 'Pengadaan Ventilator Portabel untuk Unit IGD. Diperlukan 2 unit ventilator portabel untuk menangani pasien dengan gangguan pernapasan akut. Spesifikasi: Mode ventilasi SIMV/CPAP/PS, Tidal volume 50-1500ml, FiO2 21-100%, Battery backup 4 jam, Alarm system lengkap.',
            ],
            [
                'kepala' => $kepalaFarmasi,
                'kabid' => $kabidKeperawatan,
                'unit' => 'Farmasi',
                'kabid_name' => 'Keperawatan',
                'item' => 'Refrigerator Farmasi',
                'deskripsi' => 'Pengadaan Refrigerator Farmasi khusus untuk penyimpanan obat dan vaksin. Kapasitas 600 liter, suhu 2-8°C dengan monitoring digital, alarm suhu, auto defrost, battery backup untuk power failure, data logger untuk monitoring suhu 24/7. Diperlukan untuk menjaga kualitas obat dan vaksin yang disimpan.',
            ],
            [
                'kepala' => $kepalaLab,
                'kabid' => $kabidPenunjang,
                'unit' => 'Laboratorium',
                'kabid_name' => 'Penunjang Medis',
                'item' => 'Hematology Analyzer',
                'deskripsi' => 'Pengadaan Hematology Analyzer Otomatis untuk pemeriksaan darah lengkap. Throughput 60 sampel/jam, Parameter CBC 23 parameter, Auto sampling system, Internal quality control, Hasil terintegrasi dengan LIS. Alat ini akan meningkatkan efisiensi dan akurasi pemeriksaan hematologi.',
            ],
            [
                'kepala' => $kepalaRadiologi,
                'kabid' => $kabidPenunjang,
                'unit' => 'Radiologi',
                'kabid_name' => 'Penunjang Medis',
                'item' => 'Digital X-Ray System',
                'deskripsi' => 'Pengadaan Digital X-Ray System untuk mengganti sistem konvensional. Flat Panel Detector 17x17 inch, Wireless connectivity, DICOM compliant, PACS integration, Dose reduction technology, High resolution imaging. Sistem digital akan meningkatkan kualitas gambar dan efisiensi workflow radiologi.',
            ],
            [
                'kepala' => $kepalaOK,
                'kabid' => $kabidYanmed,
                'unit' => 'Kamar Operasi',
                'kabid_name' => 'Pelayanan Medis',
                'item' => 'Operating Table',
                'deskripsi' => 'Pengadaan Operating Table Elektrik untuk kamar operasi utama. Electric control, Kapasitas beban 250kg, Trendelenburg 25°, Reverse trend 15°, Lateral tilt 20°, Remote control, Memory position, Battery backup. Diperlukan untuk meningkatkan fleksibilitas dan keamanan prosedur operasi.',
            ],
            [
                'kepala' => $kepalaICU,
                'kabid' => $kabidYanmed,
                'unit' => 'ICU',
                'kabid_name' => 'Pelayanan Medis',
                'item' => 'Patient Monitor',
                'deskripsi' => 'Pengadaan Patient Monitor Multi-parameter untuk ICU. Parameter: ECG 5 lead, NIBP, SpO2, Temp, Resp, IBP 2 channel. Display 15 inch touchscreen, Arrhythmia detection, ST segment analysis, Drug calculation, Central monitoring system compatible. Dibutuhkan 5 unit untuk upgrade monitoring pasien ICU.',
            ],
            [
                'kepala' => $kepalaPerawatan,
                'kabid' => $kabidKeperawatan,
                'unit' => 'Rawat Inap',
                'kabid_name' => 'Keperawatan',
                'item' => 'Hospital Bed Electric',
                'deskripsi' => 'Pengadaan Tempat Tidur Pasien Elektrik untuk ruang rawat inap. 3 fungsi elektrik (height, backrest, footrest), Side rail elektrik, Central locking castors, IV pole holder, Kapasitas 250kg, Remote control. Diperlukan 20 unit untuk meningkatkan kenyamanan pasien dan kemudahan perawatan.',
            ],
            [
                'kepala' => $kepalaPoliklinik,
                'kabid' => $kabidYanmed,
                'unit' => 'Poliklinik',
                'kabid_name' => 'Pelayanan Medis',
                'item' => 'Examination Lamp',
                'deskripsi' => 'Pengadaan Lampu Pemeriksaan LED untuk poliklinik. LED technology, Intensitas 100.000 lux, Color temperature 4500K, Flexible arm, Mobile stand with brake, Fokus adjustable. Diperlukan 10 unit untuk seluruh poliklinik spesialis guna meningkatkan kualitas pemeriksaan.',
            ],
            [
                'kepala' => $kepalaFarmasi,
                'kabid' => $kabidKeperawatan,
                'unit' => 'Farmasi',
                'kabid_name' => 'Keperawatan',
                'item' => 'Pill Counter Automatic',
                'deskripsi' => 'Pengadaan Pill Counter Otomatis untuk instalasi farmasi. Counting speed 1000 tablet/menit, Accuracy 99.9%, Multiple container size, Touch screen control, Auto calibration, Data recording. Alat ini akan meningkatkan kecepatan dan akurasi dalam dispensing obat tablet/kapsul.',
            ],
            [
                'kepala' => $kepalaLab,
                'kabid' => $kabidPenunjang,
                'unit' => 'Laboratorium',
                'kabid_name' => 'Penunjang Medis',
                'item' => 'Centrifuge Refrigerated',
                'deskripsi' => 'Pengadaan Centrifuge Refrigerated untuk pemrosesan sampel laboratorium. Kapasitas 24x15ml, Max speed 15000 rpm, Temperature range -20 hingga 40°C, Digital display, Auto imbalance detection, Safety lock. Dibutuhkan untuk pemeriksaan yang memerlukan suhu rendah seperti pemeriksaan enzim dan hormon.',
            ],
        ];

        // Create 10 permintaan
        foreach ($dataPermintaan as $data) {
            if (!$data['kepala'] || !$data['kabid']) {
                $this->command->warn("⚠️  Skip permintaan #{$counter}: User tidak ditemukan");
                $counter++;
                continue;
            }

            $this->command->info("📝 Membuat Permintaan #{$counter}: {$data['unit']} → Direktur");

            // Create permintaan
            $permintaan = Permintaan::create([
                'user_id' => $data['kepala']->id,
                'tanggal_permintaan' => Carbon::now()->subDays(rand(3, 10)),
                'bidang' => $data['unit'],
                'deskripsi' => $data['deskripsi'],
                'status' => 'proses',
                'pic_pimpinan' => 'Direktur',
            ]);

            // Create nota dinas
            $notaDinas = NotaDinas::create([
                'permintaan_id' => $permintaan->permintaan_id,
                'no_nota' => sprintf('ND/%s/%03d/%s', 
                    strtoupper(substr($data['unit'], 0, 3)), 
                    $counter,
                    Carbon::now()->format('m/Y')
                ),
                'tanggal_nota' => Carbon::now()->subDays(rand(2, 8)),
                'dari' => 'Kepala Instalasi ' . $data['unit'],
                'kepada' => 'Kepala Bidang ' . $data['kabid_name'],
                'perihal' => 'Permohonan Pengadaan ' . $data['item'],
            ]);

            // Disposisi 1: Kepala Instalasi → Kepala Bidang
            Disposisi::create([
                'nota_id' => $notaDinas->nota_id,
                'jabatan_tujuan' => 'Kepala Bidang ' . $data['kabid_name'],
                'tanggal_disposisi' => Carbon::now()->subDays(rand(2, 7)),
                'catatan' => 'Mohon ditindaklanjuti untuk pengadaan ' . $data['item'] . '. Kebutuhan mendesak untuk meningkatkan pelayanan.',
                'status' => 'diproses',
            ]);

            // Disposisi 2: Kepala Bidang → DIREKTUR
            Disposisi::create([
                'nota_id' => $notaDinas->nota_id,
                'jabatan_tujuan' => 'Direktur',
                'tanggal_disposisi' => Carbon::now()->subDays(rand(1, 3)),
                'catatan' => "Disetujui oleh Kepala Bidang {$data['kabid_name']}. Diteruskan ke Direktur untuk Final Approval. Pengadaan ini penting untuk meningkatkan kualitas pelayanan {$data['unit']}.",
                'status' => 'selesai',
            ]);

            $permintaans[] = [
                'id' => $permintaan->permintaan_id,
                'unit' => $data['unit'],
                'item' => $data['item'],
                'kabid' => $data['kabid_name'],
            ];

            $counter++;
        }

        // Summary
        $this->command->info('');
        $this->command->info('✅ Berhasil membuat 10 permintaan untuk Direktur!');
        $this->command->info('');
        $this->command->info('📊 RINGKASAN DATA');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════');
        $this->command->info(sprintf('%-5s | %-20s | %-30s | %-20s', 'ID', 'Unit Instalasi', 'Item', 'Via Kepala Bidang'));
        $this->command->info('───────────────────────────────────────────────────────────────────────────────────');
        
        foreach ($permintaans as $p) {
            $this->command->info(sprintf('%-5s | %-20s | %-30s | %-20s', 
                $p['id'], 
                $p['unit'],
                substr($p['item'], 0, 30),
                $p['kabid']
            ));
        }
        
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🎯 CARA MENGGUNAKAN:');
        $this->command->info('');
        $this->command->info('1. LOGIN SEBAGAI DIREKTUR');
        $this->command->info('   Email    : direktur@rsud.id');
        $this->command->info('   Password : password');
        $this->command->info('');
        $this->command->info('2. DASHBOARD');
        $this->command->info('   ✅ Total permintaan menunggu: 10');
        $this->command->info('   ✅ Semua permintaan muncul di recent list');
        $this->command->info('');
        $this->command->info('3. REVIEW SETIAP PERMINTAAN');
        $this->command->info('   ✅ APPROVE → Kembali ke Kepala Bidang untuk disposisi ke Staff Perencanaan');
        $this->command->info('   ✅ REJECT → Stop process, kembali ke unit pemohon');
        $this->command->info('   ✅ REVISI → Kembali ke Kepala Bidang untuk perbaikan');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════');
    }

    /**
     * Hapus data testing lama
     */
    private function cleanOldData(): void
    {
        // Cari permintaan testing dengan marker khusus
        $keywords = [
            'Ventilator Portabel',
            'Refrigerator Farmasi',
            'Hematology Analyzer',
            'Digital X-Ray System',
            'Operating Table',
            'Patient Monitor',
            'Hospital Bed Electric',
            'Examination Lamp',
            'Pill Counter Automatic',
            'Centrifuge Refrigerated',
        ];

        $testPermintaans = Permintaan::where('pic_pimpinan', 'Direktur')
            ->where('status', 'proses')
            ->where(function($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('deskripsi', 'like', "%{$keyword}%");
                }
            })
            ->pluck('permintaan_id');

        if ($testPermintaans->isNotEmpty()) {
            // Hapus disposisi
            $notaDinasIds = NotaDinas::whereIn('permintaan_id', $testPermintaans)->pluck('nota_id');
            if ($notaDinasIds->isNotEmpty()) {
                Disposisi::whereIn('nota_id', $notaDinasIds)->delete();
                $this->command->info('   - Disposisi lama dihapus');
            }

            // Hapus nota dinas
            NotaDinas::whereIn('permintaan_id', $testPermintaans)->delete();
            $this->command->info('   - Nota Dinas lama dihapus');

            // Hapus permintaan
            Permintaan::whereIn('permintaan_id', $testPermintaans)->delete();
            $this->command->info('   - Permintaan lama dihapus');
        }

        $this->command->info('');
    }
}
