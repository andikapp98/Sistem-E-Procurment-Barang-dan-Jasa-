-- Contoh data permintaan obat penurun panas
-- Pastikan user_id sudah ada di tabel users

INSERT INTO permintaan (
    user_id,
    tanggal_permintaan,
    deskripsi,
    status,
    pic_pimpinan,
    no_nota_dinas,
    link_scan
) VALUES (
    1, -- Sesuaikan dengan ID user yang ada
    '2025-10-16',
    'Permintaan obat penurun panas (paracetamol 500mg) sebanyak 100 strip untuk stok ruang farmasi mengingat meningkatnya kasus demam pada pasien rawat inap dan rawat jalan',
    'diajukan',
    'Dr. Ahmad Suryanto, Sp.PD',
    'ND/RS/2025/001/X',
    'https://drive.google.com/file/d/example-scan-nota-dinas'
);
