<?php

namespace Database\Seeders;

use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class KepalaInstalasiPermintaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder untuk data permintaan dari berbagai Kepala Instalasi
     * dengan 15 permintaan yang beragam
     */
    public function run(): void
    {
        // Get all Kepala Instalasi users
        $kepalaIGD = User::where('email', 'kepala.igd@rsud.id')->first();
        $kepalaFarmasi = User::where('email', 'kepala.farmasi@rsud.id')->first();
        $kepalaLab = User::where('email', 'kepala.lab@rsud.id')->first();
        $kepalaRadiologi = User::where('email', 'kepala.radiologi@rsud.id')->first();
        $kepalaBedah = User::where('email', 'kepala.bedah@rsud.id')->first();

        if (!$kepalaIGD || !$kepalaFarmasi || !$kepalaLab || !$kepalaRadiologi || !$kepalaBedah) {
            $this->command->error('❌ Kepala Instalasi users tidak ditemukan. Jalankan UserSeeder terlebih dahulu!');
            return;
        }

        $permintaanData = [
            // Permintaan dari IGD
            [
                'user_id' => $kepalaIGD->id,
                'bidang' => 'Gawat Darurat',
                'tanggal_permintaan' => Carbon::now()->subDays(45),
                'deskripsi' => 'Permintaan pengadaan ventilator portable untuk ruang IGD. Saat ini hanya memiliki 2 unit dan sering kekurangan saat ada pasien kritis. Dibutuhkan minimal 3 unit tambahan untuk mengantisipasi lonjakan pasien emergency.',
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Ahmad Yani, Sp.PD',
                'no_nota_dinas' => 'ND/IGD/2025/001',
                'link_scan' => 'https://drive.google.com/file/igd-ventilator-001',
            ],
            [
                'user_id' => $kepalaIGD->id,
                'bidang' => 'Gawat Darurat',
                'tanggal_permintaan' => Carbon::now()->subDays(38),
                'deskripsi' => 'Permintaan obat-obatan emergency kit meliputi: Adrenalin, Atropin, Dopamine, Amiodarone untuk stok ruang resusitasi IGD. Stock saat ini menipis dan harus segera diisi kembali.',
                'status' => 'proses',
                'pic_pimpinan' => 'Dr. Ahmad Yani, Sp.PD',
                'no_nota_dinas' => 'ND/IGD/2025/002',
                'link_scan' => 'https://drive.google.com/file/igd-emergency-kit-002',
            ],
            [
                'user_id' => $kepalaIGD->id,
                'bidang' => 'Gawat Darurat',
                'tanggal_permintaan' => Carbon::now()->subDays(30),
                'deskripsi' => 'Permintaan brankar emergency sebanyak 5 unit. Brankar yang ada sudah tua dan sering rusak saat digunakan untuk memindahkan pasien emergency.',
                'status' => 'proses',
                'pic_pimpinan' => 'Dr. Ahmad Yani, Sp.PD',
                'no_nota_dinas' => 'ND/IGD/2025/003',
                'link_scan' => 'https://drive.google.com/file/igd-brankar-003',
            ],

            // Permintaan dari Farmasi
            [
                'user_id' => $kepalaFarmasi->id,
                'bidang' => 'Farmasi',
                'tanggal_permintaan' => Carbon::now()->subDays(42),
                'deskripsi' => 'Permintaan pengadaan sistem informasi manajemen farmasi (SIMRS Farmasi) untuk meningkatkan efisiensi pelayanan dan kontrol stok obat. Sistem saat ini masih manual dan sering terjadi kesalahan pencatatan.',
                'status' => 'diajukan',
                'pic_pimpinan' => 'Apt. Siti Nurhaliza, S.Farm',
                'no_nota_dinas' => 'ND/FAR/2025/001',
                'link_scan' => 'https://drive.google.com/file/farmasi-simrs-001',
            ],
            [
                'user_id' => $kepalaFarmasi->id,
                'bidang' => 'Farmasi',
                'tanggal_permintaan' => Carbon::now()->subDays(35),
                'deskripsi' => 'Permintaan obat-obatan untuk stok rutin: Paracetamol 500mg (200 strip), Amoxicillin 500mg (150 strip), Cetirizine 10mg (100 strip), dan vitamin B complex (100 botol) mengingat meningkatnya jumlah pasien rawat jalan.',
                'status' => 'proses',
                'pic_pimpinan' => 'Apt. Siti Nurhaliza, S.Farm',
                'no_nota_dinas' => 'ND/FAR/2025/002',
                'link_scan' => 'https://drive.google.com/file/farmasi-obat-rutin-002',
            ],
            [
                'user_id' => $kepalaFarmasi->id,
                'bidang' => 'Farmasi',
                'tanggal_permintaan' => Carbon::now()->subDays(28),
                'deskripsi' => 'Permintaan lemari pendingin khusus untuk penyimpanan vaksin dan obat-obatan yang memerlukan suhu terkontrol. Lemari yang ada sudah tidak optimal dan berisiko merusak kualitas vaksin.',
                'status' => 'disetujui',
                'pic_pimpinan' => 'Apt. Siti Nurhaliza, S.Farm',
                'no_nota_dinas' => 'ND/FAR/2025/003',
                'link_scan' => 'https://drive.google.com/file/farmasi-cold-storage-003',
            ],

            // Permintaan dari Laboratorium
            [
                'user_id' => $kepalaLab->id,
                'bidang' => 'Laboratorium',
                'tanggal_permintaan' => Carbon::now()->subDays(40),
                'deskripsi' => 'Permintaan pengadaan alat Hematology Analyzer untuk pemeriksaan darah lengkap. Alat yang ada sudah berusia 10 tahun dan sering error, menyebabkan keterlambatan hasil pemeriksaan pasien.',
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Budi Santoso, Sp.PK',
                'no_nota_dinas' => 'ND/LAB/2025/001',
                'link_scan' => 'https://drive.google.com/file/lab-hematology-001',
            ],
            [
                'user_id' => $kepalaLab->id,
                'bidang' => 'Laboratorium',
                'tanggal_permintaan' => Carbon::now()->subDays(33),
                'deskripsi' => 'Permintaan reagen dan bahan habis pakai laboratorium: Reagen kimia klinik, reagen hematologi, tabung vacutainer, dan slide mikroskop untuk 3 bulan ke depan.',
                'status' => 'proses',
                'pic_pimpinan' => 'Dr. Budi Santoso, Sp.PK',
                'no_nota_dinas' => 'ND/LAB/2025/002',
                'link_scan' => 'https://drive.google.com/file/lab-reagen-002',
            ],
            [
                'user_id' => $kepalaLab->id,
                'bidang' => 'Laboratorium',
                'tanggal_permintaan' => Carbon::now()->subDays(25),
                'deskripsi' => 'Permintaan mikroskop binokuler digital 2 unit untuk pemeriksaan mikrobiologi dan patologi anatomi. Mikroskop yang ada sudah buram dan tidak dapat dihubungkan ke komputer untuk dokumentasi.',
                'status' => 'proses',
                'pic_pimpinan' => 'Dr. Budi Santoso, Sp.PK',
                'no_nota_dinas' => 'ND/LAB/2025/003',
                'link_scan' => 'https://drive.google.com/file/lab-mikroskop-003',
            ],

            // Permintaan dari Radiologi
            [
                'user_id' => $kepalaRadiologi->id,
                'bidang' => 'Radiologi',
                'tanggal_permintaan' => Carbon::now()->subDays(50),
                'deskripsi' => 'Permintaan pengadaan CT Scan 64 Slice untuk meningkatkan kualitas diagnostik dan mengurangi waktu tunggu pasien. Saat ini hanya memiliki 1 unit CT Scan 16 Slice yang sudah tidak memadai untuk jumlah pasien.',
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Dewi Kusuma, Sp.Rad',
                'no_nota_dinas' => 'ND/RAD/2025/001',
                'link_scan' => 'https://drive.google.com/file/radiologi-ctscan-001',
            ],
            [
                'user_id' => $kepalaRadiologi->id,
                'bidang' => 'Radiologi',
                'tanggal_permintaan' => Carbon::now()->subDays(36),
                'deskripsi' => 'Permintaan APD khusus radiologi (apron timbal, thyroid shield, dan gonad shield) untuk 5 petugas radiologi. APD yang ada sudah tipis dan tidak memberikan perlindungan optimal dari radiasi.',
                'status' => 'proses',
                'pic_pimpinan' => 'Dr. Dewi Kusuma, Sp.Rad',
                'no_nota_dinas' => 'ND/RAD/2025/002',
                'link_scan' => 'https://drive.google.com/file/radiologi-apd-002',
            ],
            [
                'user_id' => $kepalaRadiologi->id,
                'bidang' => 'Radiologi',
                'tanggal_permintaan' => Carbon::now()->subDays(20),
                'deskripsi' => 'Permintaan sistem PACS (Picture Archiving and Communication System) untuk penyimpanan dan distribusi gambar radiologi secara digital, menggantikan film konvensional yang mahal dan tidak efisien.',
                'status' => 'disetujui',
                'pic_pimpinan' => 'Dr. Dewi Kusuma, Sp.Rad',
                'no_nota_dinas' => 'ND/RAD/2025/003',
                'link_scan' => 'https://drive.google.com/file/radiologi-pacs-003',
            ],

            // Permintaan dari Bedah Sentral
            [
                'user_id' => $kepalaBedah->id,
                'bidang' => 'Bedah Sentral',
                'tanggal_permintaan' => Carbon::now()->subDays(44),
                'deskripsi' => 'Permintaan alat laparoskopi lengkap untuk operasi minimal invasif. Saat ini belum memiliki peralatan laparoskopi sehingga semua operasi masih menggunakan teknik konvensional yang berisiko lebih tinggi bagi pasien.',
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Eko Prasetyo, Sp.B',
                'no_nota_dinas' => 'ND/BEDAH/2025/001',
                'link_scan' => 'https://drive.google.com/file/bedah-laparoskopi-001',
            ],
            [
                'user_id' => $kepalaBedah->id,
                'bidang' => 'Bedah Sentral',
                'tanggal_permintaan' => Carbon::now()->subDays(32),
                'deskripsi' => 'Permintaan instrumen bedah set lengkap untuk operasi orthopedi dan bedah trauma. Instrumen yang ada sudah tumpul dan berkarat, menghambat kelancaran operasi dan berisiko infeksi.',
                'status' => 'proses',
                'pic_pimpinan' => 'Dr. Eko Prasetyo, Sp.B',
                'no_nota_dinas' => 'ND/BEDAH/2025/002',
                'link_scan' => 'https://drive.google.com/file/bedah-instrumen-002',
            ],
            [
                'user_id' => $kepalaBedah->id,
                'bidang' => 'Bedah Sentral',
                'tanggal_permintaan' => Carbon::now()->subDays(15),
                'deskripsi' => 'Permintaan mesin anestesi modern 2 unit dengan ventilator built-in dan monitoring parameter lengkap. Mesin anestesi yang ada sudah 15 tahun dan tidak memiliki fitur keamanan modern.',
                'status' => 'disetujui',
                'pic_pimpinan' => 'Dr. Eko Prasetyo, Sp.B',
                'no_nota_dinas' => 'ND/BEDAH/2025/003',
                'link_scan' => 'https://drive.google.com/file/bedah-anestesi-003',
            ],
        ];

        foreach ($permintaanData as $data) {
            Permintaan::create($data);
        }

        $this->command->info('✅ 15 Permintaan dari Kepala Instalasi berhasil dibuat!');
        $this->command->info('');
        $this->command->info('📋 Ringkasan Permintaan per Instalasi:');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info('Instalasi           | Jumlah | Status Diajukan | Status Proses | Status Disetujui');
        $this->command->info('───────────────────────────────────────────────────────────────────────────────────────');
        
        $instalasi = [
            'Gawat Darurat' => 3,
            'Farmasi' => 3,
            'Laboratorium' => 3,
            'Radiologi' => 3,
            'Bedah Sentral' => 3,
        ];
        
        foreach ($instalasi as $name => $count) {
            $diajukan = Permintaan::where('bidang', $name)->where('status', 'diajukan')->count();
            $proses = Permintaan::where('bidang', $name)->where('status', 'proses')->count();
            $disetujui = Permintaan::where('bidang', $name)->where('status', 'disetujui')->count();
            
            $this->command->info(
                str_pad($name, 19) . ' | ' . 
                str_pad($count, 6) . ' | ' . 
                str_pad($diajukan, 15) . ' | ' . 
                str_pad($proses, 13) . ' | ' . 
                str_pad($disetujui, 16)
            );
        }
        
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📊 Total: 15 permintaan dari 5 instalasi berbeda');
    }
}
