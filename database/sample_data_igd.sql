-- ====================================================================
-- CONTOH DATA PERMINTAAN INSTALASI GAWAT DARURAT (IGD)
-- ====================================================================
-- Pastikan user_id sudah ada di tabel users sebelum menjalankan script ini
-- Sesuaikan user_id dengan ID yang sesuai di database Anda

-- Contoh 1: Permintaan Alat Kesehatan Emergency
INSERT INTO permintaan (
    user_id,
    bidang,
    tanggal_permintaan,
    deskripsi,
    status,
    pic_pimpinan,
    no_nota_dinas,
    link_scan,
    created_at,
    updated_at
) VALUES (
    1,
    'Instalasi Gawat Darurat',
    '2025-10-16',
    'Permintaan pengadaan alat kesehatan untuk ruang resusitasi IGD:
1. Defibrillator portable 1 unit
2. Oksigen konsentrator 2 unit
3. Suction pump portable 3 unit
4. Ventilator transport 1 unit
5. Monitor vital sign 2 unit

Alat-alat di atas sangat mendesak mengingat peningkatan kasus emergency dan kondisi beberapa alat existing yang sudah tidak layak pakai.',
    'diajukan',
    'Dr. Siti Nurhaliza, Sp.EM',
    'ND/IGD/2025/001/X',
    'https://drive.google.com/file/d/1abc123_nota_dinas_igd_alkes',
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
);

-- Contoh 2: Permintaan Obat-obatan Emergency Kit
INSERT INTO permintaan (
    user_id,
    bidang,
    tanggal_permintaan,
    deskripsi,
    status,
    pic_pimpinan,
    no_nota_dinas,
    link_scan,
    created_at,
    updated_at
) VALUES (
    1,
    'Instalasi Gawat Darurat',
    '2025-10-16',
    'Permintaan pengadaan obat-obatan untuk Emergency Kit IGD:

OBAT INJEKSI:
- Adrenalin 1mg/ml ampul @ 100 ampul
- Dopamin 200mg/5ml ampul @ 50 ampul
- Furosemide 20mg/2ml ampul @ 100 ampul
- Dexamethasone 5mg/ml ampul @ 100 ampul
- Midazolam 5mg/ml ampul @ 50 ampul
- Aminophilin 250mg/10ml ampul @ 50 ampul

CAIRAN INFUS:
- NaCl 0.9% 500ml @ 200 kolf
- RL (Ringer Laktat) 500ml @ 200 kolf
- Dextrose 5% 500ml @ 100 kolf

Stok obat emergency saat ini menipis dan perlu segera diisi ulang untuk antisipasi kasus kegawatdaruratan.',
    'diajukan',
    'Dr. Budi Santoso, Sp.An',
    'ND/IGD/2025/002/X',
    'https://drive.google.com/file/d/2xyz456_nota_obat_emergency',
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
);

-- Contoh 3: Permintaan Alat Pelindung Diri (APD)
INSERT INTO permintaan (
    user_id,
    bidang,
    tanggal_permintaan,
    deskripsi,
    status,
    pic_pimpinan,
    no_nota_dinas,
    link_scan,
    created_at,
    updated_at
) VALUES (
    1,
    'Instalasi Gawat Darurat',
    '2025-10-15',
    'Permintaan pengadaan APD untuk petugas IGD:

1. Masker bedah 3 ply @ 5000 pcs
2. Masker N95 @ 500 pcs
3. Face shield @ 100 pcs
4. Sarung tangan steril ukuran M @ 1000 pasang
5. Sarung tangan steril ukuran L @ 1000 pasang
6. Gown pelindung @ 200 pcs
7. Apron plastik @ 300 pcs
8. Goggle pelindung @ 50 pcs
9. Head cover @ 1000 pcs
10. Shoe cover @ 2000 pcs

APD diperlukan untuk melindungi petugas kesehatan IGD dalam menangani pasien dengan berbagai kondisi, termasuk pasien dengan penyakit menular.',
    'proses',
    'Dr. Siti Nurhaliza, Sp.EM',
    'ND/IGD/2025/003/X',
    'https://drive.google.com/file/d/3def789_apd_igd',
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
);

-- Contoh 4: Permintaan Alat Medis Habis Pakai
INSERT INTO permintaan (
    user_id,
    bidang,
    tanggal_permintaan,
    deskripsi,
    status,
    pic_pimpinan,
    no_nota_dinas,
    link_scan,
    created_at,
    updated_at
) VALUES (
    1,
    'Instalasi Gawat Darurat',
    '2025-10-14',
    'Permintaan alat medis habis pakai untuk IGD periode November 2025:

KATETER & SELANG:
- IV Catheter no. 18 @ 500 pcs
- IV Catheter no. 20 @ 500 pcs
- IV Catheter no. 22 @ 300 pcs
- NGT (Nasogastric Tube) no. 14 @ 100 pcs
- NGT no. 16 @ 100 pcs
- Urinary catheter no. 14 @ 100 pcs
- Urinary catheter no. 16 @ 100 pcs

SPUIT & JARUM:
- Spuit 3cc @ 2000 pcs
- Spuit 5cc @ 2000 pcs
- Spuit 10cc @ 1000 pcs
- Spuit 20cc @ 500 pcs
- Jarum suntik 23G @ 1000 pcs
- Jarum suntik 25G @ 1000 pcs

LAIN-LAIN:
- Infus set @ 500 pcs
- Three way stopcock @ 200 pcs
- Plester medis @ 200 roll
- Kasa steril 16x16 @ 500 pack',
    'disetujui',
    'Ns. Rina Wijayanti, S.Kep',
    'ND/IGD/2025/004/X',
    'https://drive.google.com/file/d/4ghi012_alkes_habis_pakai',
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
);

-- Contoh 5: Permintaan Perbaikan dan Kalibrasi Alat
INSERT INTO permintaan (
    user_id,
    bidang,
    tanggal_permintaan,
    deskripsi,
    status,
    pic_pimpinan,
    no_nota_dinas,
    link_scan,
    created_at,
    updated_at
) VALUES (
    1,
    'Instalasi Gawat Darurat',
    '2025-10-13',
    'Permintaan perbaikan dan kalibrasi alat-alat medis IGD:

ALAT YANG PERLU KALIBRASI:
1. Monitor EKG 12 lead - 2 unit (kalibrasi rutin)
2. Pulse oximeter - 5 unit (kalibrasi rutin)
3. Sphygmomanometer digital - 4 unit (kalibrasi rutin)
4. Termometer digital - 10 unit (kalibrasi rutin)

ALAT YANG PERLU PERBAIKAN:
1. Ventilator mekanik (serial: VT-001) - error sensor
2. Infusion pump (serial: IP-003) - alarm error
3. Syringe pump (serial: SP-005) - motor rusak

Kalibrasi dan perbaikan diperlukan untuk memastikan akurasi dan keamanan penggunaan alat medis dalam penanganan pasien gawat darurat.',
    'disetujui',
    'Dr. Siti Nurhaliza, Sp.EM',
    'ND/IGD/2025/005/X',
    'https://drive.google.com/file/d/5jkl345_kalibrasi_alat',
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
);

-- Contoh 6: Permintaan Furnitur dan Peralatan Penunjang
INSERT INTO permintaan (
    user_id,
    bidang,
    tanggal_permintaan,
    deskripsi,
    status,
    pic_pimpinan,
    no_nota_dinas,
    link_scan,
    created_at,
    updated_at
) VALUES (
    1,
    'Instalasi Gawat Darurat',
    '2025-10-12',
    'Permintaan pengadaan furnitur dan peralatan penunjang IGD:

FURNITUR MEDIS:
1. Brancard IGD (emergency bed) @ 3 unit
2. Kursi roda @ 2 unit
3. Tandu lipat (stretcher) @ 2 unit
4. Meja instrumen stainless steel @ 3 unit
5. Lemari obat emergency @ 2 unit
6. Troli emergency (crash cart) @ 1 unit

PENUNJANG LAINNYA:
1. Tempat sampah medis 50L @ 10 unit
2. Tempat sampah non-medis 50L @ 5 unit
3. Safety box (tempat limbah tajam) @ 20 unit
4. Lampu sorot (examination lamp) @ 2 unit
5. Sterilisator instrumen @ 1 unit

Peralatan di atas dibutuhkan untuk meningkatkan kualitas pelayanan dan keselamatan pasien di IGD.',
    'proses',
    'Dr. Budi Santoso, Sp.An',
    'ND/IGD/2025/006/X',
    'https://drive.google.com/file/d/6mno678_furnitur_igd',
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
);

-- Contoh 7: Permintaan Obat Antidotum
INSERT INTO permintaan (
    user_id,
    bidang,
    tanggal_permintaan,
    deskripsi,
    status,
    pic_pimpinan,
    no_nota_dinas,
    link_scan,
    created_at,
    updated_at
) VALUES (
    1,
    'Instalasi Gawat Darurat',
    '2025-10-11',
    'Permintaan pengadaan obat antidotum untuk penanganan kasus keracunan di IGD:

ANTIDOTUM:
1. Atropin sulfate 0.25mg/ml ampul @ 50 ampul (antidot organofosfat)
2. Naloxone 0.4mg/ml ampul @ 30 ampul (antidot opioid)
3. N-Acetylcysteine (NAC) 200mg/ml vial @ 20 vial (antidot parasetamol)
4. Vitamin K1 10mg/ml ampul @ 50 ampul (antidot warfarin)
5. Flumazenil 0.1mg/ml ampul @ 20 ampul (antidot benzodiazepin)
6. Dimercaprol (BAL) 100mg/ml ampul @ 10 ampul (antidot logam berat)
7. Calcium gluconate 10% 10ml ampul @ 30 ampul (antidot asam fluorida)

Antidotum sangat diperlukan di IGD untuk penanganan cepat kasus keracunan yang memerlukan intervensi segera.',
    'diajukan',
    'Dr. Siti Nurhaliza, Sp.EM',
    'ND/IGD/2025/007/X',
    'https://drive.google.com/file/d/7pqr901_antidotum',
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
);

-- ====================================================================
-- Selesai
-- ====================================================================
-- Untuk menjalankan seeder melalui Laravel:
-- php artisan db:seed --class=IGDPermintaanSeeder
-- ====================================================================
