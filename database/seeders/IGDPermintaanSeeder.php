<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IGDPermintaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeder untuk contoh data permintaan Instalasi Gawat Darurat
     */
    public function run(): void
    {
        $permintaans = [
            // Contoh 1: Alat Kesehatan Emergency
            [
                'user_id' => 1,
                'bidang' => 'Instalasi Gawat Darurat',
                'tanggal_permintaan' => Carbon::parse('2025-10-16'),
                'deskripsi' => "Permintaan pengadaan alat kesehatan untuk ruang resusitasi IGD:\n1. Defibrillator portable 1 unit\n2. Oksigen konsentrator 2 unit\n3. Suction pump portable 3 unit\n4. Ventilator transport 1 unit\n5. Monitor vital sign 2 unit\n\nAlat-alat di atas sangat mendesak mengingat peningkatan kasus emergency dan kondisi beberapa alat existing yang sudah tidak layak pakai.",
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Siti Nurhaliza, Sp.EM',
                'no_nota_dinas' => 'ND/IGD/2025/001/X',
                'link_scan' => 'https://drive.google.com/file/d/1abc123_nota_dinas_igd_alkes',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Contoh 2: Obat-obatan Emergency Kit
            [
                'user_id' => 1,
                'bidang' => 'Instalasi Gawat Darurat',
                'tanggal_permintaan' => Carbon::parse('2025-10-16'),
                'deskripsi' => "Permintaan pengadaan obat-obatan untuk Emergency Kit IGD:\n\nOBAT INJEKSI:\n- Adrenalin 1mg/ml ampul @ 100 ampul\n- Dopamin 200mg/5ml ampul @ 50 ampul\n- Furosemide 20mg/2ml ampul @ 100 ampul\n- Dexamethasone 5mg/ml ampul @ 100 ampul\n- Midazolam 5mg/ml ampul @ 50 ampul\n- Aminophilin 250mg/10ml ampul @ 50 ampul\n\nCAIRAN INFUS:\n- NaCl 0.9% 500ml @ 200 kolf\n- RL (Ringer Laktat) 500ml @ 200 kolf\n- Dextrose 5% 500ml @ 100 kolf\n\nStok obat emergency saat ini menipis dan perlu segera diisi ulang untuk antisipasi kasus kegawatdaruratan.",
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Budi Santoso, Sp.An',
                'no_nota_dinas' => 'ND/IGD/2025/002/X',
                'link_scan' => 'https://drive.google.com/file/d/2xyz456_nota_obat_emergency',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Contoh 3: APD
            [
                'user_id' => 1,
                'bidang' => 'Instalasi Gawat Darurat',
                'tanggal_permintaan' => Carbon::parse('2025-10-15'),
                'deskripsi' => "Permintaan pengadaan APD untuk petugas IGD:\n\n1. Masker bedah 3 ply @ 5000 pcs\n2. Masker N95 @ 500 pcs\n3. Face shield @ 100 pcs\n4. Sarung tangan steril ukuran M @ 1000 pasang\n5. Sarung tangan steril ukuran L @ 1000 pasang\n6. Gown pelindung @ 200 pcs\n7. Apron plastik @ 300 pcs\n8. Goggle pelindung @ 50 pcs\n9. Head cover @ 1000 pcs\n10. Shoe cover @ 2000 pcs\n\nAPD diperlukan untuk melindungi petugas kesehatan IGD dalam menangani pasien dengan berbagai kondisi, termasuk pasien dengan penyakit menular.",
                'status' => 'proses',
                'pic_pimpinan' => 'Dr. Siti Nurhaliza, Sp.EM',
                'no_nota_dinas' => 'ND/IGD/2025/003/X',
                'link_scan' => 'https://drive.google.com/file/d/3def789_apd_igd',
                'created_at' => Carbon::now()->subDay(1),
                'updated_at' => Carbon::now(),
            ],

            // Contoh 4: Alat Medis Habis Pakai
            [
                'user_id' => 1,
                'bidang' => 'Instalasi Gawat Darurat',
                'tanggal_permintaan' => Carbon::parse('2025-10-14'),
                'deskripsi' => "Permintaan alat medis habis pakai untuk IGD periode November 2025:\n\nKATETER & SELANG:\n- IV Catheter no. 18 @ 500 pcs\n- IV Catheter no. 20 @ 500 pcs\n- IV Catheter no. 22 @ 300 pcs\n- NGT (Nasogastric Tube) no. 14 @ 100 pcs\n- NGT no. 16 @ 100 pcs\n- Urinary catheter no. 14 @ 100 pcs\n- Urinary catheter no. 16 @ 100 pcs\n\nSPUIT & JARUM:\n- Spuit 3cc @ 2000 pcs\n- Spuit 5cc @ 2000 pcs\n- Spuit 10cc @ 1000 pcs\n- Spuit 20cc @ 500 pcs\n- Jarum suntik 23G @ 1000 pcs\n- Jarum suntik 25G @ 1000 pcs\n\nLAIN-LAIN:\n- Infus set @ 500 pcs\n- Three way stopcock @ 200 pcs\n- Plester medis @ 200 roll\n- Kasa steril 16x16 @ 500 pack",
                'status' => 'disetujui',
                'pic_pimpinan' => 'Ns. Rina Wijayanti, S.Kep',
                'no_nota_dinas' => 'ND/IGD/2025/004/X',
                'link_scan' => 'https://drive.google.com/file/d/4ghi012_alkes_habis_pakai',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now(),
            ],

            // Contoh 5: Perbaikan dan Kalibrasi
            [
                'user_id' => 1,
                'bidang' => 'Instalasi Gawat Darurat',
                'tanggal_permintaan' => Carbon::parse('2025-10-13'),
                'deskripsi' => "Permintaan perbaikan dan kalibrasi alat-alat medis IGD:\n\nALAT YANG PERLU KALIBRASI:\n1. Monitor EKG 12 lead - 2 unit (kalibrasi rutin)\n2. Pulse oximeter - 5 unit (kalibrasi rutin)\n3. Sphygmomanometer digital - 4 unit (kalibrasi rutin)\n4. Termometer digital - 10 unit (kalibrasi rutin)\n\nALAT YANG PERLU PERBAIKAN:\n1. Ventilator mekanik (serial: VT-001) - error sensor\n2. Infusion pump (serial: IP-003) - alarm error\n3. Syringe pump (serial: SP-005) - motor rusak\n\nKalibrasi dan perbaikan diperlukan untuk memastikan akurasi dan keamanan penggunaan alat medis dalam penanganan pasien gawat darurat.",
                'status' => 'disetujui',
                'pic_pimpinan' => 'Dr. Siti Nurhaliza, Sp.EM',
                'no_nota_dinas' => 'ND/IGD/2025/005/X',
                'link_scan' => 'https://drive.google.com/file/d/5jkl345_kalibrasi_alat',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now(),
            ],

            // Contoh 6: Furnitur dan Peralatan
            [
                'user_id' => 1,
                'bidang' => 'Instalasi Gawat Darurat',
                'tanggal_permintaan' => Carbon::parse('2025-10-12'),
                'deskripsi' => "Permintaan pengadaan furnitur dan peralatan penunjang IGD:\n\nFURNITUR MEDIS:\n1. Brancard IGD (emergency bed) @ 3 unit\n2. Kursi roda @ 2 unit\n3. Tandu lipat (stretcher) @ 2 unit\n4. Meja instrumen stainless steel @ 3 unit\n5. Lemari obat emergency @ 2 unit\n6. Troli emergency (crash cart) @ 1 unit\n\nPENUNJANG LAINNYA:\n1. Tempat sampah medis 50L @ 10 unit\n2. Tempat sampah non-medis 50L @ 5 unit\n3. Safety box (tempat limbah tajam) @ 20 unit\n4. Lampu sorot (examination lamp) @ 2 unit\n5. Sterilisator instrumen @ 1 unit\n\nPeralatan di atas dibutuhkan untuk meningkatkan kualitas pelayanan dan keselamatan pasien di IGD.",
                'status' => 'proses',
                'pic_pimpinan' => 'Dr. Budi Santoso, Sp.An',
                'no_nota_dinas' => 'ND/IGD/2025/006/X',
                'link_scan' => 'https://drive.google.com/file/d/6mno678_furnitur_igd',
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now(),
            ],

            // Contoh 7: Obat Antidotum
            [
                'user_id' => 1,
                'bidang' => 'Instalasi Gawat Darurat',
                'tanggal_permintaan' => Carbon::parse('2025-10-11'),
                'deskripsi' => "Permintaan pengadaan obat antidotum untuk penanganan kasus keracunan di IGD:\n\nANTIDOTUM:\n1. Atropin sulfate 0.25mg/ml ampul @ 50 ampul (antidot organofosfat)\n2. Naloxone 0.4mg/ml ampul @ 30 ampul (antidot opioid)\n3. N-Acetylcysteine (NAC) 200mg/ml vial @ 20 vial (antidot parasetamol)\n4. Vitamin K1 10mg/ml ampul @ 50 ampul (antidot warfarin)\n5. Flumazenil 0.1mg/ml ampul @ 20 ampul (antidot benzodiazepin)\n6. Dimercaprol (BAL) 100mg/ml ampul @ 10 ampul (antidot logam berat)\n7. Calcium gluconate 10% 10ml ampul @ 30 ampul (antidot asam fluorida)\n\nAntidotum sangat diperlukan di IGD untuk penanganan cepat kasus keracunan yang memerlukan intervensi segera.",
                'status' => 'diajukan',
                'pic_pimpinan' => 'Dr. Siti Nurhaliza, Sp.EM',
                'no_nota_dinas' => 'ND/IGD/2025/007/X',
                'link_scan' => 'https://drive.google.com/file/d/7pqr901_antidotum',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('permintaan')->insert($permintaans);
    }
}
