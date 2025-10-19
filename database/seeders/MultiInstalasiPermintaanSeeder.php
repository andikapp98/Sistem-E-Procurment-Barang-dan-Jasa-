<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MultiInstalasiPermintaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeder untuk 20 permintaan dari berbagai instalasi
     */
    public function run(): void
    {
        $permintaans = [
            // ========== INSTALASI GAWAT DARURAT (IGD) - 4 permintaan ==========
            [
                'user_id' => 3, // kepala.igd@rsud.id - Dr. Ahmad Yani, Sp.PD
                'bidang' => 'Gawat Darurat',
                'tanggal_permintaan' => Carbon::parse('2025-10-18'),
                'deskripsi' => "Permintaan pengadaan alat kesehatan untuk ruang resusitasi IGD:\n1. Defibrillator portable 1 unit\n2. Oksigen konsentrator 2 unit\n3. Suction pump portable 3 unit\n4. Ventilator transport 1 unit\n5. Monitor vital sign 2 unit\n\nAlat-alat di atas sangat mendesak mengingat peningkatan kasus emergency dan kondisi beberapa alat existing yang sudah tidak layak pakai.",
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Ahmad Yani, Sp.PD',
                'no_nota_dinas' => 'ND/IGD/2025/001/X',
                'link_scan' => 'https://drive.google.com/file/d/igd-alkes-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 3,
                'bidang' => 'Gawat Darurat',
                'tanggal_permintaan' => Carbon::parse('2025-10-17'),
                'deskripsi' => "Permintaan obat-obatan emergency kit:\n- Adrenalin 1mg/ml ampul @ 100 ampul\n- Dopamin 200mg/5ml ampul @ 50 ampul\n- Furosemide 20mg/2ml ampul @ 100 ampul\n- Dexamethasone 5mg/ml ampul @ 100 ampul\n- Cairan NaCl 0.9% 500ml @ 200 kolf\n- RL (Ringer Laktat) 500ml @ 200 kolf\n\nStok obat emergency menipis dan perlu segera diisi ulang.",
                'status' => 'proses',
                'pic_pimpinan' => 'Dr. Ahmad Yani, Sp.PD',
                'no_nota_dinas' => 'ND/IGD/2025/002/X',
                'link_scan' => 'https://drive.google.com/file/d/igd-obat-002',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 3,
                'bidang' => 'Gawat Darurat',
                'tanggal_permintaan' => Carbon::parse('2025-10-15'),
                'deskripsi' => "Permintaan APD untuk petugas IGD:\n1. Masker bedah 3 ply @ 5000 pcs\n2. Masker N95 @ 500 pcs\n3. Sarung tangan steril ukuran M @ 1000 pasang\n4. Sarung tangan steril ukuran L @ 1000 pasang\n5. Gown pelindung @ 200 pcs\n6. Face shield @ 100 pcs\n\nAPD diperlukan untuk melindungi petugas kesehatan dalam menangani pasien.",
                'status' => 'disetujui',
                'pic_pimpinan' => 'Dr. Ahmad Yani, Sp.PD',
                'no_nota_dinas' => 'ND/IGD/2025/003/X',
                'link_scan' => 'https://drive.google.com/file/d/igd-apd-003',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDay(1),
            ],
            [
                'user_id' => 3,
                'bidang' => 'Gawat Darurat',
                'tanggal_permintaan' => Carbon::parse('2025-10-14'),
                'deskripsi' => "Permintaan furnitur dan peralatan penunjang:\n1. Brancard IGD (emergency bed) @ 3 unit\n2. Kursi roda @ 2 unit\n3. Tandu lipat (stretcher) @ 2 unit\n4. Troli emergency (crash cart) @ 1 unit\n5. Lampu sorot (examination lamp) @ 2 unit\n\nPeralatan ini dibutuhkan untuk meningkatkan kualitas pelayanan di IGD.",
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Ahmad Yani, Sp.PD',
                'no_nota_dinas' => 'ND/IGD/2025/004/X',
                'link_scan' => 'https://drive.google.com/file/d/igd-furnitur-004',
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],

            // ========== INSTALASI FARMASI - 4 permintaan ==========
            [
                'user_id' => 4, // kepala.farmasi@rsud.id - Apt. Siti Nurhaliza, S.Farm
                'bidang' => 'Farmasi',
                'tanggal_permintaan' => Carbon::parse('2025-10-18'),
                'deskripsi' => "Permintaan obat-obatan untuk stok rutin farmasi:\n\nANTIBIOTIK:\n- Amoxicillin 500mg tablet @ 10.000 tablet\n- Ciprofloxacin 500mg tablet @ 5.000 tablet\n- Cefixime 200mg kapsul @ 5.000 kapsul\n- Azithromycin 500mg tablet @ 3.000 tablet\n\nANALGETIK & ANTIPIRETIK:\n- Paracetamol 500mg tablet @ 20.000 tablet\n- Ibuprofen 400mg tablet @ 10.000 tablet\n- Asam mefenamat 500mg tablet @ 8.000 tablet\n\nStok obat rutin perlu diisi untuk kebutuhan 2 bulan ke depan.",
                'status' => 'diajukan',
                'pic_pimpinan' => 'Apt. Siti Nurhaliza, S.Farm',
                'no_nota_dinas' => 'ND/FARM/2025/001/X',
                'link_scan' => 'https://drive.google.com/file/d/farmasi-obat-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 4,
                'bidang' => 'Farmasi',
                'tanggal_permintaan' => Carbon::parse('2025-10-16'),
                'deskripsi' => "Permintaan obat khusus dan high alert:\n\nOBAT KHUSUS:\n- Insulin glargine 100IU/ml vial @ 50 vial\n- Metformin 500mg tablet @ 15.000 tablet\n- Amlodipine 10mg tablet @ 12.000 tablet\n- Atorvastatin 20mg tablet @ 8.000 tablet\n\nHIGH ALERT MEDICATION:\n- Heparin 5000IU/ml vial @ 30 vial\n- Dopamin 200mg ampul @ 100 ampul\n- KCl injection 25 meq/10ml @ 100 ampul\n\nObat-obatan ini diperlukan untuk pasien dengan kondisi kritis dan kronik.",
                'status' => 'proses',
                'pic_pimpinan' => 'Apt. Siti Nurhaliza, S.Farm',
                'no_nota_dinas' => 'ND/FARM/2025/002/X',
                'link_scan' => 'https://drive.google.com/file/d/farmasi-highalert-002',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDay(1),
            ],
            [
                'user_id' => 4,
                'bidang' => 'Farmasi',
                'tanggal_permintaan' => Carbon::parse('2025-10-13'),
                'deskripsi' => "Permintaan peralatan farmasi:\n1. Lemari es khusus obat 2-8°C @ 2 unit\n2. Freezer -20°C untuk vaksin @ 1 unit\n3. Rak penyimpanan obat @ 5 unit\n4. Timbangan digital kapasitas 2kg @ 2 unit\n5. Blender obat @ 1 unit\n6. Peralatan racik obat set lengkap @ 3 set\n\nPeralatan ini diperlukan untuk meningkatkan standar penyimpanan dan pelayanan farmasi.",
                'status' => 'disetujui',
                'pic_pimpinan' => 'Apt. Siti Nurhaliza, S.Farm',
                'no_nota_dinas' => 'ND/FARM/2025/003/X',
                'link_scan' => 'https://drive.google.com/file/d/farmasi-alat-003',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => 4,
                'bidang' => 'Farmasi',
                'tanggal_permintaan' => Carbon::parse('2025-10-12'),
                'deskripsi' => "Permintaan bahan kemasan obat:\n- Kantong plastik obat ukuran kecil @ 10.000 pcs\n- Kantong plastik obat ukuran sedang @ 5.000 pcs\n- Botol plastik 60ml @ 1.000 pcs\n- Botol plastik 100ml @ 1.000 pcs\n- Etiket obat putih @ 20.000 lembar\n- Etiket obat biru @ 10.000 lembar\n- Label peringatan obat @ 5.000 lembar\n\nBahan kemasan habis dan perlu segera diadakan.",
                'status' => 'proses',
                'pic_pimpinan' => 'Apt. Siti Nurhaliza, S.Farm',
                'no_nota_dinas' => 'ND/FARM/2025/004/X',
                'link_scan' => 'https://drive.google.com/file/d/farmasi-kemasan-004',
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(3),
            ],

            // ========== INSTALASI LABORATORIUM - 4 permintaan ==========
            [
                'user_id' => 5, // kepala.lab@rsud.id - Dr. Budi Santoso, Sp.PK
                'bidang' => 'Laboratorium',
                'tanggal_permintaan' => Carbon::parse('2025-10-17'),
                'deskripsi' => "Permintaan reagen untuk pemeriksaan hematologi:\n\nREAGEN HEMATOLOGI:\n- Reagen hematologi analyzer (CBC) @ 10 box\n- Reagen LED @ 5 box\n- Larutan Turk @ 5 liter\n- Larutan Hayem @ 5 liter\n- Sahli reagen @ 3 set\n\nKALIBRATOR & KONTROL:\n- Hematology calibrator @ 5 vial\n- Hematology control level 1,2,3 @ masing-masing 10 vial\n\nReagen habis dan diperlukan untuk pemeriksaan hematologi rutin.",
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Budi Santoso, Sp.PK',
                'no_nota_dinas' => 'ND/LAB/2025/001/X',
                'link_scan' => 'https://drive.google.com/file/d/lab-reagen-001',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now()->subDay(1),
            ],
            [
                'user_id' => 5,
                'bidang' => 'Laboratorium',
                'tanggal_permintaan' => Carbon::parse('2025-10-16'),
                'deskripsi' => "Permintaan reagen kimia klinik:\n\nREAGEN KIMIA KLINIK:\n- Reagen glukosa @ 5 box (2x100ml)\n- Reagen kolesterol total @ 3 box\n- Reagen trigliserida @ 3 box\n- Reagen HDL/LDL @ 3 box\n- Reagen ureum @ 3 box\n- Reagen kreatinin @ 3 box\n- Reagen asam urat @ 3 box\n- Reagen SGOT/SGPT @ 5 box\n- Reagen albumin @ 2 box\n\nReagen diperlukan untuk pemeriksaan kimia klinik rutin pasien rawat inap dan jalan.",
                'status' => 'proses',
                'pic_pimpinan' => 'Dr. Budi Santoso, Sp.PK',
                'no_nota_dinas' => 'ND/LAB/2025/002/X',
                'link_scan' => 'https://drive.google.com/file/d/lab-kimia-002',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDay(1),
            ],
            [
                'user_id' => 5,
                'bidang' => 'Laboratorium',
                'tanggal_permintaan' => Carbon::parse('2025-10-14'),
                'deskripsi' => "Permintaan alat habis pakai laboratorium:\n\nALAT HABIS PAKAI:\n- Spuit 3cc @ 5.000 pcs\n- Vacutainer EDTA (ungu) @ 2.000 pcs\n- Vacutainer plain (merah) @ 2.000 pcs\n- Vacutainer citrate (biru) @ 500 pcs\n- Lancet @ 3.000 pcs\n- Kapas alkohol @ 100 pack\n- Tourniquet @ 50 pcs\n- Mikropipet tip 1000μl @ 5.000 pcs\n- Mikropipet tip 200μl @ 5.000 pcs\n- Mikropipet tip 10μl @ 3.000 pcs\n- Tabung reaksi @ 5.000 pcs\n- Object glass @ 1.000 pcs\n- Cover glass @ 2.000 pcs\n\nAlat habis pakai untuk operasional laboratorium harian.",
                'status' => 'disetujui',
                'pic_pimpinan' => 'Dr. Budi Santoso, Sp.PK',
                'no_nota_dinas' => 'ND/LAB/2025/003/X',
                'link_scan' => 'https://drive.google.com/file/d/lab-habispakai-003',
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => 5,
                'bidang' => 'Laboratorium',
                'tanggal_permintaan' => Carbon::parse('2025-10-13'),
                'deskripsi' => "Permintaan alat laboratorium baru:\n\nALAT LABORATORIUM:\n1. Chemistry Analyzer semi-auto @ 1 unit\n2. Centrifuge 6 tabung @ 2 unit\n3. Mikroskop binokuler LED @ 2 unit\n4. Inkubator bakteriologi @ 1 unit\n5. Water bath @ 1 unit\n6. Rotator mixer @ 1 unit\n7. Timer digital @ 3 unit\n8. Rak tabung reaksi @ 10 unit\n\nAlat-alat ini diperlukan untuk meningkatkan kapasitas dan kualitas pemeriksaan laboratorium.",
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Budi Santoso, Sp.PK',
                'no_nota_dinas' => 'ND/LAB/2025/004/X',
                'link_scan' => 'https://drive.google.com/file/d/lab-alat-004',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],

            // ========== INSTALASI RADIOLOGI - 4 permintaan ==========
            [
                'user_id' => 6, // kepala.radiologi@rsud.id - Dr. Dewi Kusuma, Sp.Rad
                'bidang' => 'Radiologi',
                'tanggal_permintaan' => Carbon::parse('2025-10-18'),
                'deskripsi' => "Permintaan bahan kontras dan film radiologi:\n\nBAHAN KONTRAS:\n- Kontras iodine 300mg/ml @ 50 botol (untuk CT Scan)\n- Kontras barium sulfat @ 30 botol (untuk pemeriksaan saluran cerna)\n- Kontras gadolinium @ 20 vial (untuk MRI)\n\nFILM RADIOLOGI:\n- Film rontgen 35x35 cm @ 500 lembar\n- Film rontgen 30x40 cm @ 300 lembar\n- Film rontgen 24x30 cm @ 300 lembar\n- Film gigi @ 200 lembar\n\nBahan kontras dan film habis untuk pemeriksaan radiologi rutin.",
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Dewi Kusuma, Sp.Rad',
                'no_nota_dinas' => 'ND/RAD/2025/001/X',
                'link_scan' => 'https://drive.google.com/file/d/rad-kontras-001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 6,
                'bidang' => 'Radiologi',
                'tanggal_permintaan' => Carbon::parse('2025-10-15'),
                'deskripsi' => "Permintaan APD khusus radiologi:\n\nAPD PROTEKSI RADIASI:\n- Apron timbal (lead apron) 0.5mm @ 5 unit\n- Thyroid shield @ 5 unit\n- Kacamata proteksi radiasi @ 3 unit\n- Sarung tangan timbal @ 5 pasang\n- Gonad shield set @ 2 set\n\nAPD UMUM:\n- Masker bedah @ 2.000 pcs\n- Sarung tangan non-steril @ 2.000 pasang\n- Hand sanitizer 5L @ 10 galon\n\nAPD untuk melindungi petugas dan pasien dari paparan radiasi.",
                'status' => 'proses',
                'pic_pimpinan' => 'Dr. Dewi Kusuma, Sp.Rad',
                'no_nota_dinas' => 'ND/RAD/2025/002/X',
                'link_scan' => 'https://drive.google.com/file/d/rad-apd-002',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDay(1),
            ],
            [
                'user_id' => 6,
                'bidang' => 'Radiologi',
                'tanggal_permintaan' => Carbon::parse('2025-10-13'),
                'deskripsi' => "Permintaan maintenance dan kalibrasi alat radiologi:\n\nMAINTENANCE RUTIN:\n1. Pesawat rontgen konvensional @ 2 unit (maintenance rutin tahunan)\n2. CT Scan 16 slice @ 1 unit (maintenance + kalibrasi)\n3. USG 4D @ 1 unit (maintenance + kalibrasi)\n4. Dental rontgen @ 1 unit (maintenance rutin)\n\nSERVICE PARTS:\n- X-ray tube replacement untuk rontgen unit-2\n- Cooling system service untuk CT Scan\n- Probe ultrasound kalibrasi\n\nMaintenance diperlukan untuk memastikan akurasi dan keamanan alat.",
                'status' => 'disetujui',
                'pic_pimpinan' => 'Dr. Dewi Kusuma, Sp.Rad',
                'no_nota_dinas' => 'ND/RAD/2025/003/X',
                'link_scan' => 'https://drive.google.com/file/d/rad-maintenance-003',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => 6,
                'bidang' => 'Radiologi',
                'tanggal_permintaan' => Carbon::parse('2025-10-11'),
                'deskripsi' => "Permintaan upgrade sistem PACS dan alat penunjang:\n\nUPGRADE SISTEM:\n1. PACS (Picture Archiving Communication System) upgrade ke versi terbaru\n2. Workstation PACS @ 2 unit (PC spec tinggi dengan monitor medis)\n3. Storage server tambahan 20TB untuk arsip digital\n\nALAT PENUNJANG:\n- Printer film digital (dry imager) @ 1 unit\n- Monitor diagnostic grade 21 inch @ 3 unit\n- UPS 3000VA @ 2 unit\n\nUpgrade sistem untuk meningkatkan efisiensi pelayanan radiologi digital.",
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Dewi Kusuma, Sp.Rad',
                'no_nota_dinas' => 'ND/RAD/2025/004/X',
                'link_scan' => 'https://drive.google.com/file/d/rad-pacs-004',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],

            // ========== INSTALASI BEDAH SENTRAL - 4 permintaan ==========
            [
                'user_id' => 7, // kepala.bedah@rsud.id - Dr. Eko Prasetyo, Sp.B
                'bidang' => 'Bedah Sentral',
                'tanggal_permintaan' => Carbon::parse('2025-10-17'),
                'deskripsi' => "Permintaan alat dan instrumen bedah:\n\nINSTRUMEN BEDAH:\n- Set instrumen bedah umum @ 2 set\n- Set instrumen bedah minor @ 3 set\n- Gunting bedah berbagai ukuran @ 10 pcs\n- Pinset anatomis & sirurgis @ 20 pasang\n- Needle holder berbagai ukuran @ 10 pcs\n- Hemostatic forceps @ 20 pcs\n- Scalpel handle @ 10 pcs\n- Blade pisau bedah no. 11,15,22 @ masing-masing 500 pcs\n\nINSTRUMEN KHUSUS:\n- Retractor set @ 2 set\n- Bone saw @ 1 unit\n- Drill bedah orthopedi @ 1 unit\n\nInstrumen diperlukan untuk operasi rutin dan emergency.",
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Eko Prasetyo, Sp.B',
                'no_nota_dinas' => 'ND/OK/2025/001/X',
                'link_scan' => 'https://drive.google.com/file/d/ok-instrumen-001',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now()->subDay(1),
            ],
            [
                'user_id' => 7,
                'bidang' => 'Bedah Sentral',
                'tanggal_permintaan' => Carbon::parse('2025-10-16'),
                'deskripsi' => "Permintaan bahan habis pakai operasi:\n\nBENANG OPERASI:\n- Catgut chromic 2/0 @ 200 pack\n- Catgut plain 3/0 @ 200 pack\n- Silk 2/0, 3/0 @ masing-masing 200 pack\n- Nylon 3/0, 4/0 @ masing-masing 200 pack\n- Vicryl 2/0, 3/0 @ masing-masing 100 pack\n\nALAT HABIS PAKAI:\n- Sarung tangan steril no. 6.5 @ 1.000 pasang\n- Sarung tangan steril no. 7.0 @ 1.500 pasang\n- Sarung tangan steril no. 7.5 @ 1.500 pasang\n- Surgical mask @ 3.000 pcs\n- Surgical gown @ 500 pcs\n- Surgical drape @ 300 pcs\n- Kasa steril 16x16 @ 1.000 pack\n- Gauze bandage @ 500 roll\n\nBahan habis pakai untuk kebutuhan operasi 2 bulan.",
                'status' => 'proses',
                'pic_pimpinan' => 'Dr. Eko Prasetyo, Sp.B',
                'no_nota_dinas' => 'ND/OK/2025/002/X',
                'link_scan' => 'https://drive.google.com/file/d/ok-habispakai-002',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDay(1),
            ],
            [
                'user_id' => 7,
                'bidang' => 'Bedah Sentral',
                'tanggal_permintaan' => Carbon::parse('2025-10-14'),
                'deskripsi' => "Permintaan alat anestesi dan monitoring:\n\nALAT ANESTESI:\n- Anesthesia machine @ 1 unit (replacement unit lama)\n- Laringoskop set @ 2 set\n- Endotracheal tube berbagai ukuran @ 200 pcs\n- LMA (Laryngeal Mask Airway) ukuran 3,4,5 @ masing-masing 20 pcs\n- Guedel airway berbagai ukuran @ 50 pcs\n\nMONITORING:\n- Patient monitor 5 parameter @ 2 unit\n- Pulse oximeter portable @ 3 unit\n- Syringe pump @ 2 unit\n- Infusion pump @ 2 unit\n\nAlat anestesi dan monitoring untuk keselamatan pasien operasi.",
                'status' => 'disetujui',
                'pic_pimpinan' => 'Dr. Eko Prasetyo, Sp.B',
                'no_nota_dinas' => 'ND/OK/2025/003/X',
                'link_scan' => 'https://drive.google.com/file/d/ok-anestesi-003',
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => 7,
                'bidang' => 'Bedah Sentral',
                'tanggal_permintaan' => Carbon::parse('2025-10-12'),
                'deskripsi' => "Permintaan sterilisator dan peralatan sterilisasi:\n\nALAT STERILISASI:\n- Autoclave steam sterilizer 100L @ 1 unit\n- Dry heat sterilizer @ 1 unit\n- UV sterilizer cabinet @ 2 unit\n- Washer disinfector @ 1 unit\n\nPENUNJANG STERILISASI:\n- Indicator tape @ 100 roll\n- Chemical indicator strips @ 500 strips\n- Biological indicator @ 100 vial\n- Sterilization pouch berbagai ukuran @ 5.000 pcs\n- Sterilization container @ 10 unit\n\nPeralatan untuk memastikan sterilitas instrumen bedah.",
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Eko Prasetyo, Sp.B',
                'no_nota_dinas' => 'ND/OK/2025/004/X',
                'link_scan' => 'https://drive.google.com/file/d/ok-sterilisasi-004',
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(6),
            ],
        ];

        DB::table('permintaan')->insert($permintaans);

        $this->command->info('✅ 20 permintaan dari berbagai instalasi berhasil dibuat!');
        $this->command->info('');
        $this->command->info('📋 Ringkasan:');
        $this->command->info('─────────────────────────────────────────────');
        $this->command->info('• Gawat Darurat: 4 permintaan');
        $this->command->info('• Farmasi: 4 permintaan');
        $this->command->info('• Laboratorium: 4 permintaan');
        $this->command->info('• Radiologi: 4 permintaan');
        $this->command->info('• Bedah Sentral: 4 permintaan');
        $this->command->info('─────────────────────────────────────────────');
        $this->command->info('Total: 20 permintaan');
    }
}
