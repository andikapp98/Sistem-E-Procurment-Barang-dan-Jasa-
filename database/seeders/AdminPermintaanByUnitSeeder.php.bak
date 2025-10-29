<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class AdminPermintaanByUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder untuk data permintaan admin berdasarkan unitnya.
     * Setiap Kepala Instalasi hanya bisa melihat permintaan dari staff unitnya sendiri.
     * 
     * KONSEP:
     * - Admin membuat permintaan atas nama unit tertentu
     * - user_id di permintaan = ID dari Kepala Instalasi unit tersebut
     * - Kepala Instalasi hanya melihat permintaan dengan user_id mereka sendiri
     * - Klasifikasi barang menentukan Kepala Bidang tujuan:
     *   * medis → Kepala Bidang Pelayanan Medis
     *   * penunjang_medis → Kepala Bidang Penunjang Medis
     *   * non_medis → Kepala Bidang lain atau langsung ke Direktur
     */
    public function run(): void
    {
        // Ambil data user Kepala Instalasi dari database
        $kepalaIGD = User::where('email', 'kepala.igd@rsud.id')->first();
        $kepalaFarmasi = User::where('email', 'kepala.farmasi@rsud.id')->first();
        $kepalaLab = User::where('email', 'kepala.lab@rsud.id')->first();
        $kepalaRadiologi = User::where('email', 'kepala.radiologi@rsud.id')->first();
        $kepalaBedah = User::where('email', 'kepala.bedah@rsud.id')->first();
        $kepalaRanap = User::where('email', 'kepala.ranap@rsud.id')->first();
        $kepalaRajal = User::where('email', 'kepala.rajal@rsud.id')->first();
        $kepalaICU = User::where('email', 'kepala.icu@rsud.id')->first();
        $kepalaRM = User::where('email', 'kepala.rm@rsud.id')->first();
        $kepalaGizi = User::where('email', 'kepala.gizi@rsud.id')->first();
        $kepalaSanitasi = User::where('email', 'kepala.sanitasi@rsud.id')->first();
        $kepalaLaundry = User::where('email', 'kepala.laundry@rsud.id')->first();

        $permintaans = $this->getPermintaanData([
            'kepalaIGD' => $kepalaIGD,
            'kepalaFarmasi' => $kepalaFarmasi,
            'kepalaLab' => $kepalaLab,
            'kepalaRadiologi' => $kepalaRadiologi,
            'kepalaBedah' => $kepalaBedah,
            'kepalaRanap' => $kepalaRanap,
            'kepalaRajal' => $kepalaRajal,
            'kepalaICU' => $kepalaICU,
            'kepalaRM' => $kepalaRM,
            'kepalaGizi' => $kepalaGizi,
            'kepalaSanitasi' => $kepalaSanitasi,
            'kepalaLaundry' => $kepalaLaundry,
        ]);

        DB::table('permintaan')->insert($permintaans);

        $this->command->info('✅ Seeder Admin Permintaan By Unit berhasil dijalankan!');
        $this->command->info('');
        $this->command->info('📋 RINGKASAN PERMINTAAN PER UNIT:');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('Unit                          | Jumlah | Kepala Instalasi');
        $this->command->info('───────────────────────────────────────────────────────────────');
        $this->command->info('Gawat Darurat                 | 3      | ' . $kepalaIGD->name);
        $this->command->info('Farmasi                       | 2      | ' . $kepalaFarmasi->name);
        $this->command->info('Laboratorium                  | 2      | ' . $kepalaLab->name);
        $this->command->info('Radiologi                     | 1      | ' . $kepalaRadiologi->name);
        $this->command->info('Bedah Sentral                 | 2      | ' . $kepalaBedah->name);
        $this->command->info('Rawat Inap                    | 2      | ' . $kepalaRanap->name);
        $this->command->info('Rawat Jalan                   | 1      | ' . $kepalaRajal->name);
        $this->command->info('ICU/ICCU                      | 2      | ' . $kepalaICU->name);
        $this->command->info('Rekam Medis                   | 1      | ' . $kepalaRM->name);
        $this->command->info('Gizi                          | 2      | ' . $kepalaGizi->name);
        $this->command->info('Sanitasi & Pemeliharaan       | 2      | ' . $kepalaSanitasi->name);
        $this->command->info('Laundry & Linen               | 2      | ' . $kepalaLaundry->name);
        $this->command->info('───────────────────────────────────────────────────────────────');
        $this->command->info('TOTAL: 22 permintaan dari 12 unit berbeda');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('💡 CARA KERJA:');
        $this->command->info('• Admin membuat permintaan untuk unit tertentu');
        $this->command->info('• Permintaan disimpan dengan user_id = ID Kepala Instalasi unit tersebut');
        $this->command->info('• Kepala Instalasi hanya melihat permintaan dengan user_id mereka');
        $this->command->info('• Setiap unit hanya melihat permintaan dari staff unit mereka sendiri');
        $this->command->info('');
        $this->command->info('🔐 TESTING:');
        $this->command->info('Login sebagai salah satu Kepala Instalasi untuk melihat permintaan:');
        $this->command->info('• kepala.igd@rsud.id (password: password) - Lihat 3 permintaan IGD');
        $this->command->info('• kepala.farmasi@rsud.id (password: password) - Lihat 2 permintaan Farmasi');
        $this->command->info('• kepala.lab@rsud.id (password: password) - Lihat 2 permintaan Lab');
        $this->command->info('• dst...');
    }

    private function getPermintaanData($kepala)
    {
        return [
            // IGD - 3 permintaan (MEDIS)
            [
                'user_id' => $kepala['kepalaIGD']->id,
                'bidang' => $kepala['kepalaIGD']->unit_kerja,
                'klasifikasi_barang' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan alat kesehatan emergency IGD:\n1. Defibrillator portabel @ 2 unit\n2. Ventilator transport @ 1 unit\n3. Monitor vital sign @ 3 unit\n4. Syringe pump @ 2 unit\n5. Infusion pump @ 2 unit\n\nAlat emergency untuk meningkatkan pelayanan IGD.",
                'status' => 'diajukan',
                'pic_pimpinan' => $kepala['kepalaIGD']->name,
                'no_nota_dinas' => 'ND/IGD/2025/101/X',
                'link_scan' => 'https://drive.google.com/file/d/igd-101',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $kepala['kepalaIGD']->id,
                'bidang' => $kepala['kepalaIGD']->unit_kerja,
                'klasifikasi_barang' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-24'),
                'deskripsi' => "Permintaan obat emergency IGD:\n- Adrenalin @ 200 ampul\n- Dopamin @ 100 ampul\n- Furosemide @ 150 ampul\n- NaCl 0.9% @ 300 kolf\n- RL @ 300 kolf\n\nStok obat emergency menipis.",
                'status' => 'proses',
                'pic_pimpinan' => $kepala['kepalaIGD']->name,
                'no_nota_dinas' => 'ND/IGD/2025/102/X',
                'link_scan' => 'https://drive.google.com/file/d/igd-102',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $kepala['kepalaIGD']->id,
                'bidang' => $kepala['kepalaIGD']->unit_kerja,
                'klasifikasi_barang' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-23'),
                'deskripsi' => "Permintaan APD IGD:\n- Masker bedah @ 10.000 pcs\n- Masker N95 @ 1.000 pcs\n- Sarung tangan @ 10.000 pasang\n- Gown @ 500 pcs\n- Hand sanitizer @ 20 galon\n\nAPD untuk proteksi petugas IGD.",
                'status' => 'disetujui',
                'pic_pimpinan' => $kepala['kepalaIGD']->name,
                'no_nota_dinas' => 'ND/IGD/2025/103/X',
                'link_scan' => 'https://drive.google.com/file/d/igd-103',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDay(1),
            ],

            // Farmasi - 2 permintaan (MEDIS)
            [
                'user_id' => $kepala['kepalaFarmasi']->id,
                'bidang' => $kepala['kepalaFarmasi']->unit_kerja,
                'klasifikasi_barang' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan obat rutin farmasi:\n- Amoxicillin 500mg @ 15.000 tablet\n- Paracetamol 500mg @ 30.000 tablet\n- Ciprofloxacin @ 7.000 tablet\n- Amlodipine @ 10.000 tablet\n- Ibuprofen @ 15.000 tablet\n\nStok obat rutin periode Nov-Des 2025.",
                'status' => 'diajukan',
                'pic_pimpinan' => $kepala['kepalaFarmasi']->name,
                'no_nota_dinas' => 'ND/FARM/2025/201/X',
                'link_scan' => 'https://drive.google.com/file/d/farm-201',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $kepala['kepalaFarmasi']->id,
                'bidang' => $kepala['kepalaFarmasi']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-24'),
                'deskripsi' => "Permintaan peralatan farmasi:\n1. Lemari es khusus obat @ 3 unit\n2. Freezer vaksin @ 1 unit\n3. Rak penyimpanan @ 8 unit\n4. Timbangan digital @ 3 unit\n5. Thermohygrometer @ 10 unit\n\nPeralatan sesuai standar BPOM.",
                'status' => 'proses',
                'pic_pimpinan' => $kepala['kepalaFarmasi']->name,
                'no_nota_dinas' => 'ND/FARM/2025/202/X',
                'link_scan' => 'https://drive.google.com/file/d/farm-202',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],

            // Laboratorium - 2 permintaan
            [
                'user_id' => $kepala['kepalaLab']->id,
                'bidang' => $kepala['kepalaLab']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan reagen laboratorium:\n- Reagen hematologi @ 15 box\n- Reagen kimia klinik @ 40 box\n- Reagen golongan darah @ 10 kit\n- Calibrator @ 5 set\n- Control level 1,2,3 @ 25 vial\n\nReagen periode Nov-Des 2025.",
                'status' => 'diajukan',
                'pic_pimpinan' => $kepala['kepalaLab']->name,
                'no_nota_dinas' => 'ND/LAB/2025/301/X',
                'link_scan' => 'https://drive.google.com/file/d/lab-301',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $kepala['kepalaLab']->id,
                'bidang' => $kepala['kepalaLab']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-23'),
                'deskripsi' => "Permintaan alat habis pakai lab:\n- Vacutainer @ 6.800 pcs\n- Spuit @ 11.000 pcs\n- Mikropipet tips @ 21.000 pcs\n- Tabung reaksi @ 13.000 pcs\n- Object & cover glass @ 5.000 pcs\n\nAlat habis pakai 2 bulan.",
                'status' => 'proses',
                'pic_pimpinan' => $kepala['kepalaLab']->name,
                'no_nota_dinas' => 'ND/LAB/2025/302/X',
                'link_scan' => 'https://drive.google.com/file/d/lab-302',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDay(1),
            ],

            // Radiologi - 1 permintaan
            [
                'user_id' => $kepala['kepalaRadiologi']->id,
                'bidang' => $kepala['kepalaRadiologi']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan bahan kontras dan film:\n- Kontras iodine @ 110 botol\n- Kontras barium @ 80 box\n- Film rontgen berbagai ukuran @ 2.300 lembar\n- Developer fixer @ 10 set\n\nBahan radiologi 2 bulan.",
                'status' => 'diajukan',
                'pic_pimpinan' => $kepala['kepalaRadiologi']->name,
                'no_nota_dinas' => 'ND/RAD/2025/401/X',
                'link_scan' => 'https://drive.google.com/file/d/rad-401',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Bedah Sentral - 2 permintaan
            [
                'user_id' => $kepala['kepalaBedah']->id,
                'bidang' => $kepala['kepalaBedah']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan bahan habis pakai operasi:\n- Benang operasi berbagai jenis @ 2.800 pack\n- Sarung tangan steril @ 7.000 pasang\n- Gown & drape @ 1.000 pcs\n- Kasa steril @ 3.000 pack\n- Plester @ 400 roll\n\nBahan operasi 2 bulan.",
                'status' => 'diajukan',
                'pic_pimpinan' => $kepala['kepalaBedah']->name,
                'no_nota_dinas' => 'ND/OK/2025/501/X',
                'link_scan' => 'https://drive.google.com/file/d/ok-501',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $kepala['kepalaBedah']->id,
                'bidang' => $kepala['kepalaBedah']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-24'),
                'deskripsi' => "Permintaan instrumen bedah:\n- Set instrumen bedah @ 8 set\n- Gunting & pinset @ 60 pcs\n- Needle holder @ 15 pcs\n- Hemostatic forceps @ 45 pcs\n- Blade pisau bedah @ 2.500 pcs\n\nInstrumen untuk tambah set operasi.",
                'status' => 'proses',
                'pic_pimpinan' => $kepala['kepalaBedah']->name,
                'no_nota_dinas' => 'ND/OK/2025/502/X',
                'link_scan' => 'https://drive.google.com/file/d/ok-502',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],

            // Rawat Inap - 2 permintaan
            [
                'user_id' => $kepala['kepalaRanap']->id,
                'bidang' => $kepala['kepalaRanap']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan peralatan medis rawat inap:\n- Patient monitor @ 10 unit\n- Infusion pump @ 15 unit\n- Syringe pump @ 10 unit\n- Nebulizer @ 15 unit\n- Tempat tidur pasien @ 15 unit\n\nPeralatan untuk kualitas pelayanan.",
                'status' => 'diajukan',
                'pic_pimpinan' => $kepala['kepalaRanap']->name,
                'no_nota_dinas' => 'ND/RANAP/2025/601/X',
                'link_scan' => 'https://drive.google.com/file/d/ranap-601',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $kepala['kepalaRanap']->id,
                'bidang' => $kepala['kepalaRanap']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-23'),
                'deskripsi' => "Permintaan linen pasien:\n- Sprei @ 200 pcs\n- Selimut @ 150 pcs\n- Sarung bantal @ 300 pcs\n- Handuk @ 200 pcs\n- Baju pasien @ 400 set\n\nLinen untuk kenyamanan pasien.",
                'status' => 'proses',
                'pic_pimpinan' => $kepala['kepalaRanap']->name,
                'no_nota_dinas' => 'ND/RANAP/2025/602/X',
                'link_scan' => 'https://drive.google.com/file/d/ranap-602',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDay(1),
            ],

            // Rawat Jalan - 1 permintaan
            [
                'user_id' => $kepala['kepalaRajal']->id,
                'bidang' => $kepala['kepalaRajal']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan peralatan poliklinik:\n- Tensimeter digital @ 20 unit\n- Stetoskop @ 25 unit\n- Thermometer @ 15 unit\n- Examination lamp @ 15 unit\n- Meja & kursi periksa @ 40 unit\n\nPeralatan untuk pelayanan poliklinik.",
                'status' => 'diajukan',
                'pic_pimpinan' => $kepala['kepalaRajal']->name,
                'no_nota_dinas' => 'ND/RAJAL/2025/701/X',
                'link_scan' => 'https://drive.google.com/file/d/rajal-701',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // ICU - 2 permintaan
            [
                'user_id' => $kepala['kepalaICU']->id,
                'bidang' => $kepala['kepalaICU']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan peralatan ICU:\n- Ventilator ICU @ 3 unit\n- Patient monitor 12 parameter @ 8 unit\n- Syringe pump @ 20 unit\n- Infusion pump @ 15 unit\n- Defibrillator @ 2 unit\n\nPeralatan kritis ICU/ICCU.",
                'status' => 'diajukan',
                'pic_pimpinan' => $kepala['kepalaICU']->name,
                'no_nota_dinas' => 'ND/ICU/2025/801/X',
                'link_scan' => 'https://drive.google.com/file/d/icu-801',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $kepala['kepalaICU']->id,
                'bidang' => $kepala['kepalaICU']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-24'),
                'deskripsi' => "Permintaan consumable ICU:\n- ETT berbagai ukuran @ 250 pcs\n- Ventilator circuit @ 100 set\n- Suction catheter @ 1.500 pcs\n- CVC @ 50 set\n- Sarung tangan steril @ 3.000 pasang\n\nAlat habis pakai ICU 2 bulan.",
                'status' => 'proses',
                'pic_pimpinan' => $kepala['kepalaICU']->name,
                'no_nota_dinas' => 'ND/ICU/2025/802/X',
                'link_scan' => 'https://drive.google.com/file/d/icu-802',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],

            // Rekam Medis - 1 permintaan
            [
                'user_id' => $kepala['kepalaRM']->id,
                'bidang' => $kepala['kepalaRM']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan peralatan rekam medis:\n- Komputer desktop @ 10 unit\n- Printer & scanner @ 8 unit\n- Filing cabinet @ 20 unit\n- Rak arsip @ 15 unit\n- Formulir RM @ 15.000 set\n\nPeralatan untuk digitalisasi RM.",
                'status' => 'diajukan',
                'pic_pimpinan' => $kepala['kepalaRM']->name,
                'no_nota_dinas' => 'ND/RM/2025/901/X',
                'link_scan' => 'https://drive.google.com/file/d/rm-901',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Gizi - 2 permintaan
            [
                'user_id' => $kepala['kepalaGizi']->id,
                'bidang' => $kepala['kepalaGizi']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan peralatan dapur gizi:\n- Kompor gas industri @ 2 unit\n- Rice cooker besar @ 5 unit\n- Lemari es @ 3 unit\n- Peralatan makan pasien @ 2.700 pcs\n- Food trolley @ 5 unit\n\nPeralatan untuk pelayanan gizi.",
                'status' => 'diajukan',
                'pic_pimpinan' => $kepala['kepalaGizi']->name,
                'no_nota_dinas' => 'ND/GIZI/2025/1001/X',
                'link_scan' => 'https://drive.google.com/file/d/gizi-1001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $kepala['kepalaGizi']->id,
                'bidang' => $kepala['kepalaGizi']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-24'),
                'deskripsi' => "Permintaan bahan makanan gizi:\n- Beras 25kg @ 100 karung\n- Gula @ 200 kg\n- Minyak goreng @ 100 liter\n- Susu formula @ 50 kaleng\n- Bumbu dapur lengkap\n\nBahan makanan stok 1 bulan.",
                'status' => 'proses',
                'pic_pimpinan' => $kepala['kepalaGizi']->name,
                'no_nota_dinas' => 'ND/GIZI/2025/1002/X',
                'link_scan' => 'https://drive.google.com/file/d/gizi-1002',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],

            // Sanitasi - 2 permintaan
            [
                'user_id' => $kepala['kepalaSanitasi']->id,
                'bidang' => $kepala['kepalaSanitasi']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan peralatan sanitasi:\n- Mesin scrubber @ 2 unit\n- Vacuum cleaner @ 5 unit\n- Trolley cleaning @ 10 set\n- Tempat sampah @ 130 pcs\n- Chemical cleaning @ 750 liter\n\nPeralatan untuk kebersihan RS.",
                'status' => 'diajukan',
                'pic_pimpinan' => $kepala['kepalaSanitasi']->name,
                'no_nota_dinas' => 'ND/SANITASI/2025/1101/X',
                'link_scan' => 'https://drive.google.com/file/d/sanitasi-1101',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $kepala['kepalaSanitasi']->id,
                'bidang' => $kepala['kepalaSanitasi']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-23'),
                'deskripsi' => "Permintaan peralatan pemeliharaan:\n- Toolset listrik & plumbing @ 10 set\n- Kabel listrik @ 800 meter\n- Pipa PVC @ 100 batang\n- Cat tembok @ 100 kaleng\n- Tangga & tools @ 15 unit\n\nPeralatan untuk maintenance gedung.",
                'status' => 'proses',
                'pic_pimpinan' => $kepala['kepalaSanitasi']->name,
                'no_nota_dinas' => 'ND/SANITASI/2025/1102/X',
                'link_scan' => 'https://drive.google.com/file/d/sanitasi-1102',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDay(1),
            ],

            // Laundry - 2 permintaan
            [
                'user_id' => $kepala['kepalaLaundry']->id,
                'bidang' => $kepala['kepalaLaundry']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan peralatan laundry:\n- Mesin cuci 20kg @ 2 unit\n- Mesin pengering @ 2 unit\n- Setrika uap @ 3 unit\n- Trolley linen @ 10 unit\n- Chemical laundry @ 650 kg/liter\n\nPeralatan operasional laundry RS.",
                'status' => 'diajukan',
                'pic_pimpinan' => $kepala['kepalaLaundry']->name,
                'no_nota_dinas' => 'ND/LAUNDRY/2025/1201/X',
                'link_scan' => 'https://drive.google.com/file/d/laundry-1201',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $kepala['kepalaLaundry']->id,
                'bidang' => $kepala['kepalaLaundry']->unit_kerja,
                'tanggal_permintaan' => Carbon::parse('2025-10-24'),
                'deskripsi' => "Permintaan linen rumah sakit:\n- Sprei & selimut @ 900 pcs\n- Linen operasi @ 500 pcs\n- Linen petugas @ 650 pcs\n- Kain lap & pel @ 800 pcs\n- Gordyn ruangan @ 100 set\n\nPengadaan linen untuk stok.",
                'status' => 'proses',
                'pic_pimpinan' => $kepala['kepalaLaundry']->name,
                'no_nota_dinas' => 'ND/LAUNDRY/2025/1202/X',
                'link_scan' => 'https://drive.google.com/file/d/laundry-1202',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],
        ];
    }
}