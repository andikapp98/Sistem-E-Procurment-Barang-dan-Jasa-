<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class AdminPermintaanKlasifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder untuk data permintaan admin dengan klasifikasi barang.
     * 
     * KONSEP KLASIFIKASI:
     * 1. MEDIS → Kepala Bidang Pelayanan Medis
     *    - Alat kesehatan langsung untuk pasien (IGD, Bedah, ICU, dll)
     *    - Obat-obatan
     *    
     * 2. PENUNJANG MEDIS → Kepala Bidang Penunjang Medis  
     *    - Laboratorium (reagen, alat lab)
     *    - Radiologi (kontras, film, alat radiologi)
     *    - Farmasi (peralatan farmasi, bukan obat)
     *    
     * 3. NON MEDIS → Kepala Bidang lain/Direktur
     *    - Rekam Medis (IT, filing)
     *    - Gizi (bahan makanan, dapur)
     *    - Sanitasi & Pemeliharaan
     *    - Laundry & Linen
     */
    public function run(): void
    {
        // Ambil data user
        $users = $this->getUsers();
        
        $permintaans = $this->getPermintaanData($users);

        DB::table('permintaan')->insert($permintaans);

        $this->displaySummary($users);
    }

    private function getUsers()
    {
        return [
            'kepalaIGD' => User::where('email', 'kepala.igd@rsud.id')->first(),
            'kepalaFarmasi' => User::where('email', 'kepala.farmasi@rsud.id')->first(),
            'kepalaLab' => User::where('email', 'kepala.lab@rsud.id')->first(),
            'kepalaRadiologi' => User::where('email', 'kepala.radiologi@rsud.id')->first(),
            'kepalaBedah' => User::where('email', 'kepala.bedah@rsud.id')->first(),
            'kepalaRanap' => User::where('email', 'kepala.ranap@rsud.id')->first(),
            'kepalaRajal' => User::where('email', 'kepala.rajal@rsud.id')->first(),
            'kepalaICU' => User::where('email', 'kepala.icu@rsud.id')->first(),
            'kepalaRM' => User::where('email', 'kepala.rm@rsud.id')->first(),
            'kepalaGizi' => User::where('email', 'kepala.gizi@rsud.id')->first(),
            'kepalaSanitasi' => User::where('email', 'kepala.sanitasi@rsud.id')->first(),
            'kepalaLaundry' => User::where('email', 'kepala.laundry@rsud.id')->first(),
        ];
    }

    private function getPermintaanData($u)
    {
        return [
            // ========== KLASIFIKASI: MEDIS ==========
            
            // IGD - Alat emergency (MEDIS)
            [
                'user_id' => $u['kepalaIGD']->id,
                'bidang' => $u['kepalaIGD']->unit_kerja,
                'klasifikasi_permintaan' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan alat emergency IGD:\n- Defibrillator @ 2 unit\n- Ventilator @ 1 unit\n- Monitor vital sign @ 3 unit\n- Syringe pump @ 2 unit\n- Infusion pump @ 2 unit",
                'status' => 'diajukan',
                'pic_pimpinan' => $u['kepalaIGD']->name,
                'no_nota_dinas' => 'ND/IGD/2025/M-001/X',
                'link_scan' => 'https://drive.google.com/igd-medis-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            // IGD - Obat emergency (MEDIS)
            [
                'user_id' => $u['kepalaIGD']->id,
                'bidang' => $u['kepalaIGD']->unit_kerja,
                'klasifikasi_permintaan' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-24'),
                'deskripsi' => "Permintaan obat emergency:\n- Adrenalin @ 200 ampul\n- Dopamin @ 100 ampul\n- Furosemide @ 150 ampul\n- NaCl 0.9% @ 300 kolf\n- RL @ 300 kolf",
                'status' => 'proses',
                'pic_pimpinan' => $u['kepalaIGD']->name,
                'no_nota_dinas' => 'ND/IGD/2025/M-002/X',
                'link_scan' => 'https://drive.google.com/igd-medis-002',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],
            
            // IGD - APD (MEDIS)
            [
                'user_id' => $u['kepalaIGD']->id,
                'bidang' => $u['kepalaIGD']->unit_kerja,
                'klasifikasi_permintaan' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-23'),
                'deskripsi' => "Permintaan APD IGD:\n- Masker bedah @ 10.000 pcs\n- Masker N95 @ 1.000 pcs\n- Sarung tangan @ 10.000 pasang\n- Gown @ 500 pcs\n- Hand sanitizer @ 20 galon",
                'status' => 'disetujui',
                'pic_pimpinan' => $u['kepalaIGD']->name,
                'no_nota_dinas' => 'ND/IGD/2025/M-003/X',
                'link_scan' => 'https://drive.google.com/igd-medis-003',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDay(1),
            ],

            // Farmasi - Obat rutin (MEDIS)
            [
                'user_id' => $u['kepalaFarmasi']->id,
                'bidang' => $u['kepalaFarmasi']->unit_kerja,
                'klasifikasi_permintaan' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan obat rutin:\n- Amoxicillin @ 15.000 tablet\n- Paracetamol @ 30.000 tablet\n- Ciprofloxacin @ 7.000 tablet\n- Amlodipine @ 10.000 tablet\n- Ibuprofen @ 15.000 tablet",
                'status' => 'diajukan',
                'pic_pimpinan' => $u['kepalaFarmasi']->name,
                'no_nota_dinas' => 'ND/FARM/2025/M-001/X',
                'link_scan' => 'https://drive.google.com/farm-medis-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Bedah - Alat operasi (MEDIS)
            [
                'user_id' => $u['kepalaBedah']->id,
                'bidang' => $u['kepalaBedah']->unit_kerja,
                'klasifikasi_permintaan' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan habis pakai operasi:\n- Benang operasi @ 2.800 pack\n- Sarung tangan steril @ 7.000 pasang\n- Gown & drape @ 1.000 pcs\n- Kasa steril @ 3.000 pack",
                'status' => 'diajukan',
                'pic_pimpinan' => $u['kepalaBedah']->name,
                'no_nota_dinas' => 'ND/OK/2025/M-001/X',
                'link_scan' => 'https://drive.google.com/ok-medis-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Bedah - Instrumen (MEDIS)
            [
                'user_id' => $u['kepalaBedah']->id,
                'bidang' => $u['kepalaBedah']->unit_kerja,
                'klasifikasi_permintaan' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-24'),
                'deskripsi' => "Permintaan instrumen bedah:\n- Set instrumen @ 8 set\n- Gunting & pinset @ 60 pcs\n- Needle holder @ 15 pcs\n- Blade pisau @ 2.500 pcs",
                'status' => 'proses',
                'pic_pimpinan' => $u['kepalaBedah']->name,
                'no_nota_dinas' => 'ND/OK/2025/M-002/X',
                'link_scan' => 'https://drive.google.com/ok-medis-002',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],

            // Rawat Inap - Alat medis (MEDIS)
            [
                'user_id' => $u['kepalaRanap']->id,
                'bidang' => $u['kepalaRanap']->unit_kerja,
                'klasifikasi_permintaan' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan alat medis:\n- Patient monitor @ 10 unit\n- Infusion pump @ 15 unit\n- Syringe pump @ 10 unit\n- Nebulizer @ 15 unit\n- Tempat tidur pasien @ 15 unit",
                'status' => 'diajukan',
                'pic_pimpinan' => $u['kepalaRanap']->name,
                'no_nota_dinas' => 'ND/RANAP/2025/M-001/X',
                'link_scan' => 'https://drive.google.com/ranap-medis-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Rawat Jalan - Alat poliklinik (MEDIS)
            [
                'user_id' => $u['kepalaRajal']->id,
                'bidang' => $u['kepalaRajal']->unit_kerja,
                'klasifikasi_permintaan' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan alat poliklinik:\n- Tensimeter digital @ 20 unit\n- Stetoskop @ 25 unit\n- Thermometer @ 15 unit\n- Examination lamp @ 15 unit",
                'status' => 'diajukan',
                'pic_pimpinan' => $u['kepalaRajal']->name,
                'no_nota_dinas' => 'ND/RAJAL/2025/M-001/X',
                'link_scan' => 'https://drive.google.com/rajal-medis-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // ICU - Alat ICU (MEDIS)
            [
                'user_id' => $u['kepalaICU']->id,
                'bidang' => $u['kepalaICU']->unit_kerja,
                'klasifikasi_permintaan' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan peralatan ICU:\n- Ventilator ICU @ 3 unit\n- Monitor 12 parameter @ 8 unit\n- Syringe pump @ 20 unit\n- Defibrillator @ 2 unit",
                'status' => 'diajukan',
                'pic_pimpinan' => $u['kepalaICU']->name,
                'no_nota_dinas' => 'ND/ICU/2025/M-001/X',
                'link_scan' => 'https://drive.google.com/icu-medis-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // ICU - Consumable (MEDIS)
            [
                'user_id' => $u['kepalaICU']->id,
                'bidang' => $u['kepalaICU']->unit_kerja,
                'klasifikasi_permintaan' => 'medis',
                'kabid_tujuan' => 'Bidang Pelayanan Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-24'),
                'deskripsi' => "Permintaan consumable ICU:\n- ETT @ 250 pcs\n- Ventilator circuit @ 100 set\n- Suction catheter @ 1.500 pcs\n- CVC @ 50 set\n- Sarung tangan steril @ 3.000 pasang",
                'status' => 'proses',
                'pic_pimpinan' => $u['kepalaICU']->name,
                'no_nota_dinas' => 'ND/ICU/2025/M-002/X',
                'link_scan' => 'https://drive.google.com/icu-medis-002',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],

            // ========== KLASIFIKASI: PENUNJANG MEDIS ==========

            // Farmasi - Peralatan (PENUNJANG MEDIS)
            [
                'user_id' => $u['kepalaFarmasi']->id,
                'bidang' => $u['kepalaFarmasi']->unit_kerja,
                'klasifikasi_permintaan' => 'penunjang_medis',
                'kabid_tujuan' => 'Bidang Penunjang Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-24'),
                'deskripsi' => "Permintaan peralatan farmasi:\n- Lemari es khusus obat @ 3 unit\n- Freezer vaksin @ 1 unit\n- Rak penyimpanan @ 8 unit\n- Timbangan digital @ 3 unit\n- Thermohygrometer @ 10 unit",
                'status' => 'proses',
                'pic_pimpinan' => $u['kepalaFarmasi']->name,
                'no_nota_dinas' => 'ND/FARM/2025/P-001/X',
                'link_scan' => 'https://drive.google.com/farm-penunjang-001',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],

            // Laboratorium - Reagen (PENUNJANG MEDIS)
            [
                'user_id' => $u['kepalaLab']->id,
                'bidang' => $u['kepalaLab']->unit_kerja,
                'klasifikasi_permintaan' => 'penunjang_medis',
                'kabid_tujuan' => 'Bidang Penunjang Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan reagen lab:\n- Reagen hematologi @ 15 box\n- Reagen kimia klinik @ 40 box\n- Reagen golongan darah @ 10 kit\n- Calibrator @ 5 set\n- Control @ 25 vial",
                'status' => 'diajukan',
                'pic_pimpinan' => $u['kepalaLab']->name,
                'no_nota_dinas' => 'ND/LAB/2025/P-001/X',
                'link_scan' => 'https://drive.google.com/lab-penunjang-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Laboratorium - Alat habis pakai (PENUNJANG MEDIS)
            [
                'user_id' => $u['kepalaLab']->id,
                'bidang' => $u['kepalaLab']->unit_kerja,
                'klasifikasi_permintaan' => 'penunjang_medis',
                'kabid_tujuan' => 'Bidang Penunjang Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-23'),
                'deskripsi' => "Permintaan alat habis pakai:\n- Vacutainer @ 6.800 pcs\n- Spuit @ 11.000 pcs\n- Mikropipet tips @ 21.000 pcs\n- Tabung reaksi @ 13.000 pcs\n- Object glass @ 5.000 pcs",
                'status' => 'proses',
                'pic_pimpinan' => $u['kepalaLab']->name,
                'no_nota_dinas' => 'ND/LAB/2025/P-002/X',
                'link_scan' => 'https://drive.google.com/lab-penunjang-002',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDay(1),
            ],

            // Radiologi - Kontras & Film (PENUNJANG MEDIS)
            [
                'user_id' => $u['kepalaRadiologi']->id,
                'bidang' => $u['kepalaRadiologi']->unit_kerja,
                'klasifikasi_permintaan' => 'penunjang_medis',
                'kabid_tujuan' => 'Bidang Penunjang Medis',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan bahan radiologi:\n- Kontras iodine @ 110 botol\n- Kontras barium @ 80 box\n- Film rontgen @ 2.300 lembar\n- Developer fixer @ 10 set",
                'status' => 'diajukan',
                'pic_pimpinan' => $u['kepalaRadiologi']->name,
                'no_nota_dinas' => 'ND/RAD/2025/P-001/X',
                'link_scan' => 'https://drive.google.com/rad-penunjang-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // ========== KLASIFIKASI: NON MEDIS ==========

            // Rawat Inap - Linen (NON MEDIS)
            [
                'user_id' => $u['kepalaRanap']->id,
                'bidang' => $u['kepalaRanap']->unit_kerja,
                'klasifikasi_permintaan' => 'non_medis',
                'kabid_tujuan' => 'Bidang Keperawatan',
                'tanggal_permintaan' => Carbon::parse('2025-10-23'),
                'deskripsi' => "Permintaan linen pasien:\n- Sprei @ 200 pcs\n- Selimut @ 150 pcs\n- Sarung bantal @ 300 pcs\n- Handuk @ 200 pcs\n- Baju pasien @ 400 set",
                'status' => 'proses',
                'pic_pimpinan' => $u['kepalaRanap']->name,
                'no_nota_dinas' => 'ND/RANAP/2025/N-001/X',
                'link_scan' => 'https://drive.google.com/ranap-nonmedis-001',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDay(1),
            ],

            // Rekam Medis - Peralatan IT (NON MEDIS)
            [
                'user_id' => $u['kepalaRM']->id,
                'bidang' => $u['kepalaRM']->unit_kerja,
                'klasifikasi_permintaan' => 'non_medis',
                'kabid_tujuan' => 'Bagian Umum',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan peralatan RM:\n- Komputer desktop @ 10 unit\n- Printer & scanner @ 8 unit\n- Filing cabinet @ 20 unit\n- Rak arsip @ 15 unit\n- Formulir RM @ 15.000 set",
                'status' => 'diajukan',
                'pic_pimpinan' => $u['kepalaRM']->name,
                'no_nota_dinas' => 'ND/RM/2025/N-001/X',
                'link_scan' => 'https://drive.google.com/rm-nonmedis-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Gizi - Peralatan dapur (NON MEDIS)
            [
                'user_id' => $u['kepalaGizi']->id,
                'bidang' => $u['kepalaGizi']->unit_kerja,
                'klasifikasi_permintaan' => 'non_medis',
                'kabid_tujuan' => 'Bagian Umum',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan peralatan dapur:\n- Kompor gas @ 2 unit\n- Rice cooker @ 5 unit\n- Lemari es @ 3 unit\n- Peralatan makan @ 2.700 pcs\n- Food trolley @ 5 unit",
                'status' => 'diajukan',
                'pic_pimpinan' => $u['kepalaGizi']->name,
                'no_nota_dinas' => 'ND/GIZI/2025/N-001/X',
                'link_scan' => 'https://drive.google.com/gizi-nonmedis-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Gizi - Bahan makanan (NON MEDIS)
            [
                'user_id' => $u['kepalaGizi']->id,
                'bidang' => $u['kepalaGizi']->unit_kerja,
                'klasifikasi_permintaan' => 'non_medis',
                'kabid_tujuan' => 'Bagian Umum',
                'tanggal_permintaan' => Carbon::parse('2025-10-24'),
                'deskripsi' => "Permintaan bahan makanan:\n- Beras @ 100 karung\n- Gula @ 200 kg\n- Minyak @ 100 liter\n- Susu formula @ 50 kaleng\n- Bumbu dapur lengkap",
                'status' => 'proses',
                'pic_pimpinan' => $u['kepalaGizi']->name,
                'no_nota_dinas' => 'ND/GIZI/2025/N-002/X',
                'link_scan' => 'https://drive.google.com/gizi-nonmedis-002',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],

            // Sanitasi - Peralatan kebersihan (NON MEDIS)
            [
                'user_id' => $u['kepalaSanitasi']->id,
                'bidang' => $u['kepalaSanitasi']->unit_kerja,
                'klasifikasi_permintaan' => 'non_medis',
                'kabid_tujuan' => 'Bagian Umum',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan peralatan sanitasi:\n- Mesin scrubber @ 2 unit\n- Vacuum cleaner @ 5 unit\n- Trolley cleaning @ 10 set\n- Tempat sampah @ 130 pcs\n- Chemical @ 750 liter",
                'status' => 'diajukan',
                'pic_pimpinan' => $u['kepalaSanitasi']->name,
                'no_nota_dinas' => 'ND/SANITASI/2025/N-001/X',
                'link_scan' => 'https://drive.google.com/sanitasi-nonmedis-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Sanitasi - Peralatan pemeliharaan (NON MEDIS)
            [
                'user_id' => $u['kepalaSanitasi']->id,
                'bidang' => $u['kepalaSanitasi']->unit_kerja,
                'klasifikasi_permintaan' => 'non_medis',
                'kabid_tujuan' => 'Bagian Umum',
                'tanggal_permintaan' => Carbon::parse('2025-10-23'),
                'deskripsi' => "Permintaan peralatan maintenance:\n- Toolset listrik & plumbing @ 10 set\n- Kabel listrik @ 800 meter\n- Pipa PVC @ 100 batang\n- Cat tembok @ 100 kaleng",
                'status' => 'proses',
                'pic_pimpinan' => $u['kepalaSanitasi']->name,
                'no_nota_dinas' => 'ND/SANITASI/2025/N-002/X',
                'link_scan' => 'https://drive.google.com/sanitasi-nonmedis-002',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDay(1),
            ],

            // Laundry - Peralatan laundry (NON MEDIS)
            [
                'user_id' => $u['kepalaLaundry']->id,
                'bidang' => $u['kepalaLaundry']->unit_kerja,
                'klasifikasi_permintaan' => 'non_medis',
                'kabid_tujuan' => 'Bagian Umum',
                'tanggal_permintaan' => Carbon::parse('2025-10-25'),
                'deskripsi' => "Permintaan peralatan laundry:\n- Mesin cuci @ 2 unit\n- Mesin pengering @ 2 unit\n- Setrika uap @ 3 unit\n- Trolley linen @ 10 unit\n- Chemical laundry @ 650 kg/liter",
                'status' => 'diajukan',
                'pic_pimpinan' => $u['kepalaLaundry']->name,
                'no_nota_dinas' => 'ND/LAUNDRY/2025/N-001/X',
                'link_scan' => 'https://drive.google.com/laundry-nonmedis-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Laundry - Linen RS (NON MEDIS)
            [
                'user_id' => $u['kepalaLaundry']->id,
                'bidang' => $u['kepalaLaundry']->unit_kerja,
                'klasifikasi_permintaan' => 'non_medis',
                'kabid_tujuan' => 'Bagian Umum',
                'tanggal_permintaan' => Carbon::parse('2025-10-24'),
                'deskripsi' => "Permintaan linen RS:\n- Sprei & selimut @ 900 pcs\n- Linen operasi @ 500 pcs\n- Linen petugas @ 650 pcs\n- Kain lap & pel @ 800 pcs\n- Gordyn @ 100 set",
                'status' => 'proses',
                'pic_pimpinan' => $u['kepalaLaundry']->name,
                'no_nota_dinas' => 'ND/LAUNDRY/2025/N-002/X',
                'link_scan' => 'https://drive.google.com/laundry-nonmedis-002',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function displaySummary($u)
    {
        $this->command->info('✅ Seeder Admin Permintaan dengan Klasifikasi berhasil dijalankan!');
        $this->command->info('');
        $this->command->info('📋 RINGKASAN PERMINTAAN PER KLASIFIKASI:');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════');
        $this->command->info('KLASIFIKASI        | KABID TUJUAN                    | UNIT              | Jml');
        $this->command->info('───────────────────────────────────────────────────────────────────────────────');
        $this->command->info('MEDIS              | Bid. Pelayanan Medis            | IGD               | 3');
        $this->command->info('MEDIS              | Bid. Pelayanan Medis            | Farmasi (obat)    | 1');
        $this->command->info('MEDIS              | Bid. Pelayanan Medis            | Bedah Sentral     | 2');
        $this->command->info('MEDIS              | Bid. Pelayanan Medis            | Rawat Inap        | 1');
        $this->command->info('MEDIS              | Bid. Pelayanan Medis            | Rawat Jalan       | 1');
        $this->command->info('MEDIS              | Bid. Pelayanan Medis            | ICU/ICCU          | 2');
        $this->command->info('───────────────────────────────────────────────────────────────────────────────');
        $this->command->info('PENUNJANG MEDIS    | Bid. Penunjang Medis            | Farmasi (alat)    | 1');
        $this->command->info('PENUNJANG MEDIS    | Bid. Penunjang Medis            | Laboratorium      | 2');
        $this->command->info('PENUNJANG MEDIS    | Bid. Penunjang Medis            | Radiologi         | 1');
        $this->command->info('───────────────────────────────────────────────────────────────────────────────');
        $this->command->info('NON MEDIS          | Bid. Keperawatan/Bagian Umum    | Rawat Inap        | 1');
        $this->command->info('NON MEDIS          | Bagian Umum                     | Rekam Medis       | 1');
        $this->command->info('NON MEDIS          | Bagian Umum                     | Gizi              | 2');
        $this->command->info('NON MEDIS          | Bagian Umum                     | Sanitasi          | 2');
        $this->command->info('NON MEDIS          | Bagian Umum                     | Laundry           | 2');
        $this->command->info('═══════════════════════════════════════════════════════════════════════════════');
        $this->command->info('TOTAL: 22 permintaan - 10 MEDIS | 4 PENUNJANG MEDIS | 8 NON MEDIS');
        $this->command->info('');
        $this->command->info('💡 ROUTING PERMINTAAN:');
        $this->command->info('• Kepala Instalasi → Kabid (sesuai klasifikasi) → Direktur → Staff Perencanaan');
        $this->command->info('• MEDIS → Kabid Pelayanan Medis (kabid.yanmed@rsud.id)');
        $this->command->info('• PENUNJANG MEDIS → Kabid Penunjang Medis (kabid.penunjang@rsud.id)');
        $this->command->info('• NON MEDIS → Kabid Keperawatan/Bagian Umum');
    }
}

