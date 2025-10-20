<?php

namespace Database\Seeders;

use App\Models\Permintaan;
use App\Models\NotaDinas;
use App\Models\Disposisi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

/**
 * Seeder untuk membuat data permintaan lengkap dengan workflow ke Kepala Bidang
 * 
 * Flow:
 * 1. Buat Permintaan dari berbagai instalasi
 * 2. Buat Nota Dinas dari Kepala Instalasi
 * 3. Buat Disposisi otomatis ke Kepala Bidang yang sesuai
 * 
 * Hasil: Kepala Bidang akan melihat permintaan di dashboard mereka
 */
class PermintaanKepalaBidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('========================================================================================================');
        $this->command->info('   SEEDER: Permintaan untuk Kepala Bidang');
        $this->command->info('========================================================================================================');
        $this->command->info('');

        // Cek user yang dibutuhkan
        $admin = User::where('email', 'admin@rsud.id')->first();
        
        if (!$admin) {
            $this->command->error('❌ User admin tidak ditemukan. Jalankan UserSeeder terlebih dahulu!');
            return;
        }

        $this->command->info('📊 Membuat data permintaan untuk berbagai instalasi...');
        $this->command->info('');

        // Counter
        $permintaanCount = 0;
        $notaDinasCount = 0;
        $disposisiCount = 0;

        // Data permintaan untuk berbagai instalasi
        $permintaanData = [
            // IGD
            [
                'bidang' => 'Instalasi Gawat Darurat',
                'kepala_instalasi' => 'Dr. Ahmad Fauzi, Sp.EM',
                'kepala_bidang' => 'Kepala Bidang',
                'deskripsi' => 'Permintaan pengadaan 2 unit monitor pasien multi-parameter untuk ruang resusitasi IGD. Spesifikasi: ECG 12 lead, SpO2, NIBP, temperatur, respirasi, dengan layar 15 inch touchscreen. Merek: Philips atau setara. Urgent karena monitor lama sudah rusak.',
                'pic_pimpinan' => 'Dr. Ahmad Fauzi, Sp.EM',
                'no_nota_dinas' => 'ND/IGD/2025/001/I',
                'priority' => 'urgent',
                'estimated_cost' => 150000000,
            ],
            [
                'bidang' => 'Instalasi Gawat Darurat',
                'kepala_instalasi' => 'Dr. Ahmad Fauzi, Sp.EM',
                'kepala_bidang' => 'Kepala Bidang',
                'deskripsi' => 'Pengadaan 100 set infus set dewasa dan 50 set infus set anak untuk stok IGD bulan Februari-Maret 2025. Spesifikasi: steril, disposable, dengan flow regulator. Merek: Terumo atau setara.',
                'pic_pimpinan' => 'Dr. Ahmad Fauzi, Sp.EM',
                'no_nota_dinas' => 'ND/IGD/2025/002/I',
                'priority' => 'normal',
                'estimated_cost' => 5000000,
            ],
            
            // Farmasi
            [
                'bidang' => 'Instalasi Farmasi',
                'kepala_instalasi' => 'Apt. Siti Nurhaliza, S.Farm, M.Farm',
                'kepala_bidang' => 'Kepala Bidang',
                'deskripsi' => 'Permintaan pengadaan obat antibiotik untuk stok bulan Februari 2025: Ceftriaxone 1g injeksi (1000 vial), Levofloxacin 500mg tablet (5000 tablet), Azithromycin 500mg tablet (3000 tablet). Semua harus bersertifikat BPOM dan halal.',
                'pic_pimpinan' => 'Apt. Siti Nurhaliza, S.Farm, M.Farm',
                'no_nota_dinas' => 'ND/FAR/2025/003/I',
                'priority' => 'high',
                'estimated_cost' => 85000000,
            ],
            [
                'bidang' => 'Instalasi Farmasi',
                'kepala_instalasi' => 'Apt. Siti Nurhaliza, S.Farm, M.Farm',
                'kepala_bidang' => 'Kepala Bidang',
                'deskripsi' => 'Pengadaan 1 unit lemari pendingin khusus untuk penyimpanan vaksin dan obat cold chain. Spesifikasi: suhu 2-8°C dengan alarm, kapasitas 200 liter, dilengkapi data logger. Untuk mengganti lemari lama yang sudah tidak akurat.',
                'pic_pimpinan' => 'Apt. Siti Nurhaliza, S.Farm, M.Farm',
                'no_nota_dinas' => 'ND/FAR/2025/004/I',
                'priority' => 'urgent',
                'estimated_cost' => 35000000,
            ],

            // Laboratorium
            [
                'bidang' => 'Instalasi Laboratorium Patologi Klinik',
                'kepala_instalasi' => 'dr. Retno Wijayanti, Sp.PK',
                'kepala_bidang' => 'Kepala Bidang',
                'deskripsi' => 'Permintaan pengadaan reagen kimia klinik untuk pemeriksaan fungsi hati (SGOT, SGPT, Bilirubin), fungsi ginjal (Ureum, Creatinin), dan profil lipid (Cholesterol, HDL, LDL, Trigliserida). Untuk kebutuhan 3 bulan (Februari-April 2025), estimasi 5000 test.',
                'pic_pimpinan' => 'dr. Retno Wijayanti, Sp.PK',
                'no_nota_dinas' => 'ND/LAB/2025/005/I',
                'priority' => 'normal',
                'estimated_cost' => 65000000,
            ],
            [
                'bidang' => 'Instalasi Laboratorium Patologi Klinik',
                'kepala_instalasi' => 'dr. Retno Wijayanti, Sp.PK',
                'kepala_bidang' => 'Kepala Bidang',
                'deskripsi' => 'Pengadaan 1 unit hematology analyzer otomatis 3-part differential untuk mengganti alat yang rusak. Spesifikasi: throughput minimal 60 sampel/jam, parameter CBC lengkap, dilengkapi printer dan koneksi LIS. Merek: Sysmex atau Mindray.',
                'pic_pimpinan' => 'dr. Retno Wijayanti, Sp.PK',
                'no_nota_dinas' => 'ND/LAB/2025/006/I',
                'priority' => 'urgent',
                'estimated_cost' => 175000000,
            ],

            // Radiologi
            [
                'bidang' => 'Instalasi Radiologi',
                'kepala_instalasi' => 'dr. Bambang Prasetyo, Sp.Rad',
                'kepala_bidang' => 'Kepala Bidang',
                'deskripsi' => 'Permintaan pengadaan film X-ray size 14x17 inch sebanyak 500 lembar dan size 10x12 inch sebanyak 300 lembar untuk stok bulan Februari-Maret 2025. Merek: Fuji atau Kodak. Untuk keperluan radiologi konvensional.',
                'pic_pimpinan' => 'dr. Bambang Prasetyo, Sp.Rad',
                'no_nota_dinas' => 'ND/RAD/2025/007/I',
                'priority' => 'normal',
                'estimated_cost' => 25000000,
            ],

            // Bedah Sentral
            [
                'bidang' => 'Instalasi Bedah Sentral',
                'kepala_instalasi' => 'dr. Andi Surya, Sp.B',
                'kepala_bidang' => 'Kepala Bidang',
                'deskripsi' => 'Permintaan pengadaan alat bedah minor set lengkap (1 set): gunting jaringan, gunting benang, pinset anatomis, pinset chirurgis, needle holder, scalpel handle, retractor, klem arteri. Material: stainless steel medis grade, untuk OK baru yang akan dibuka bulan Maret.',
                'pic_pimpinan' => 'dr. Andi Surya, Sp.B',
                'no_nota_dinas' => 'ND/BEDAH/2025/008/I',
                'priority' => 'high',
                'estimated_cost' => 45000000,
            ],

            // Rawat Inap
            [
                'bidang' => 'Instalasi Rawat Inap',
                'kepala_instalasi' => 'Ns. Dewi Kartika, S.Kep, M.Kep',
                'kepala_bidang' => 'Kepala Bidang',
                'deskripsi' => 'Pengadaan 20 unit tempat tidur pasien elektrik 3 crank untuk ruang rawat inap kelas 2 dan 3. Spesifikasi: rangka besi dengan cat powder coating, kasur busa medis, railing samping dapat dilipat, dengan roda dan rem. Sesuai standar RS.',
                'pic_pimpinan' => 'Ns. Dewi Kartika, S.Kep, M.Kep',
                'no_nota_dinas' => 'ND/RANAP/2025/009/I',
                'priority' => 'normal',
                'estimated_cost' => 120000000,
            ],

            // Rehabilitasi Medik
            [
                'bidang' => 'Instalasi Rehabilitasi Medik',
                'kepala_instalasi' => 'dr. Fitri Handayani, Sp.KFR',
                'kepala_bidang' => 'Kepala Bidang',
                'deskripsi' => 'Permintaan pengadaan 1 unit treadmill medis untuk terapi kardiovaskular pasien rehabilitasi jantung. Spesifikasi: kecepatan 0-16 km/h, inklinasi 0-15%, dengan monitor heart rate, emergency stop, berat max user 150kg. Merek: Tunturi Medical atau setara.',
                'pic_pimpinan' => 'dr. Fitri Handayani, Sp.KFR',
                'no_nota_dinas' => 'ND/RM/2025/010/I',
                'priority' => 'normal',
                'estimated_cost' => 55000000,
            ],
        ];

        // Tanggal base
        $baseDate = Carbon::now()->subDays(5);

        // Buat permintaan untuk setiap data
        foreach ($permintaanData as $index => $data) {
            // Buat permintaan
            $tanggalPermintaan = $baseDate->copy()->addDays($index);
            
            $permintaan = Permintaan::create([
                'user_id' => $admin->id,
                'bidang' => $data['bidang'],
                'tanggal_permintaan' => $tanggalPermintaan,
                'deskripsi' => $data['deskripsi'],
                'status' => 'proses', // Sudah disetujui Kepala Instalasi
                'pic_pimpinan' => $data['kepala_bidang'], // Diteruskan ke Kepala Bidang
                'no_nota_dinas' => $data['no_nota_dinas'],
                'link_scan' => 'https://drive.google.com/sample-scan-' . ($index + 1),
            ]);

            $permintaanCount++;

            // Buat Nota Dinas dari Kepala Instalasi ke Kepala Bidang
            $tanggalNota = $tanggalPermintaan->copy()->addDay();
            
            $notaDinas = NotaDinas::create([
                'permintaan_id' => $permintaan->permintaan_id,
                'no_nota' => $data['no_nota_dinas'],
                'tanggal_nota' => $tanggalNota,
                'dari' => $data['kepala_instalasi'],
                'kepada' => 'Kepala Bidang',
                'perihal' => 'Persetujuan Permintaan Pengadaan - ' . substr($data['deskripsi'], 0, 80) . '...',
            ]);

            $notaDinasCount++;

            // Buat Disposisi otomatis ke Kepala Bidang
            $tanggalDisposisi = $tanggalNota->copy()->addHours(6);
            
            $disposisi = Disposisi::create([
                'nota_id' => $notaDinas->nota_id,
                'jabatan_tujuan' => 'Kepala Bidang',
                'tanggal_disposisi' => $tanggalDisposisi,
                'catatan' => $this->generateCatatanDisposisi($data),
                'status' => 'pending', // Menunggu review Kepala Bidang
            ]);

            $disposisiCount++;

            // Show progress
            $priority = $data['priority'] ?? 'normal';
            if ($priority === 'urgent') {
                $priorityText = '[URGENT]';
            } elseif ($priority === 'high') {
                $priorityText = '[HIGH]';
            } else {
                $priorityText = '[NORMAL]';
            }
            
            $count = $index + 1;
            $this->command->info("  {$priorityText} [{$count}/10] {$data['bidang']}");
        }

        // Summary
        $this->command->info('');
        $this->command->info('[OK] Seeder berhasil dijalankan!');
        $this->command->info('');
        $this->command->info('RINGKASAN DATA YANG DIBUAT:');
        $this->command->info('========================================================================================================');
        $this->command->info("  Permintaan dibuat      : {$permintaanCount}");
        $this->command->info("  Nota Dinas dibuat      : {$notaDinasCount}");
        $this->command->info("  Disposisi dibuat       : {$disposisiCount}");
        $this->command->info('========================================================================================================');
        $this->command->info('');
        
        $this->command->info('DETAIL PER INSTALASI:');
        $this->showInstalasiFummary();

        $this->command->info('');
        $this->command->info('CARA TESTING:');
        $this->command->info('========================================================================================================');
        $this->command->info('  1. Login sebagai Kepala Bidang');
        $this->command->info('  2. Buka Dashboard -> Harus ada ' . $permintaanCount . ' permintaan di "Menunggu Review"');
        $this->command->info('  3. Buka menu Index -> Harus melihat semua ' . $permintaanCount . ' permintaan');
        $this->command->info('  4. Klik salah satu permintaan untuk melihat detail');
        $this->command->info('========================================================================================================');
        $this->command->info('');
    }

    /**
     * Generate catatan disposisi yang informatif
     */
    private function generateCatatanDisposisi($data)
    {
        $priority = $data['priority'] ?? 'normal';
        $priorityText = [
            'urgent' => 'URGENT - Segera ditindaklanjuti',
            'high' => 'PRIORITAS TINGGI',
            'normal' => 'Mohon untuk ditindaklanjuti'
        ];

        $catatan = $priorityText[$priority] . ". ";
        $catatan .= "Permintaan pengadaan dari {$data['bidang']}. ";
        
        if (isset($data['estimated_cost'])) {
            $catatan .= "Estimasi biaya: Rp " . number_format($data['estimated_cost'], 0, ',', '.') . ". ";
        }
        
        $catatan .= "Kepada Yth. Kepala Bidang, agar dapat dilakukan review dan persetujuan sesuai prosedur yang berlaku.";

        return $catatan;
    }

    /**
     * Show summary per instalasi
     */
    private function showInstalasiFummary()
    {
        $instalasi = [
            'Instalasi Gawat Darurat' => 'IGD',
            'Instalasi Farmasi' => 'FAR',
            'Instalasi Laboratorium Patologi Klinik' => 'LAB',
            'Instalasi Radiologi' => 'RAD',
            'Instalasi Bedah Sentral' => 'BEDAH',
            'Instalasi Rawat Inap' => 'RANAP',
            'Instalasi Rehabilitasi Medik' => 'RM',
        ];

        foreach ($instalasi as $namaInstalasi => $prefix) {
            $count = Permintaan::where('bidang', $namaInstalasi)
                ->where('status', 'proses')
                ->whereHas('notaDinas.disposisi', function($q) {
                    $q->where('jabatan_tujuan', 'Kepala Bidang')
                      ->where('status', 'pending');
                })
                ->count();

            if ($count > 0) {
                $this->command->info("  [{$prefix}] {$namaInstalasi}: {$count} permintaan");
            }
        }
    }
}
