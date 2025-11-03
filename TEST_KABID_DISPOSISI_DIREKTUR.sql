-- QUERY TESTING: Kabid Disposisi dari Direktur

-- 1. Cek permintaan yang sudah di-approve Direktur dan seharusnya muncul di Kabid
SELECT 
    p.permintaan_id,
    p.klasifikasi_permintaan,
    p.kabid_tujuan,
    p.pic_pimpinan,
    p.status,
    p.deskripsi,
    d.disposisi_id,
    d.jabatan_tujuan,
    d.catatan,
    d.status as disposisi_status,
    nd.tanggal_nota
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE d.catatan LIKE '%Disetujui oleh Direktur%'
  AND p.status = 'proses'
ORDER BY p.permintaan_id DESC
LIMIT 10;

-- Expected Result:
-- Seharusnya ada permintaan dengan:
-- - klasifikasi_permintaan = 'medis' / 'penunjang_medis' / 'non_medis'
-- - kabid_tujuan = 'Bidang Pelayanan Medis' / 'Bidang Penunjang Medis' / 'Bidang Umum & Keuangan'
-- - jabatan_tujuan di disposisi = sama dengan kabid_tujuan
-- - status = 'proses'
-- - catatan disposisi mengandung "Disetujui oleh Direktur"


-- 2. Cek apakah query baru untuk Kabid akan menangkap permintaan di atas
-- Simulasi query untuk Kabid Yanmed (unit_kerja = 'Bidang Pelayanan Medis')
SELECT 
    p.permintaan_id,
    p.klasifikasi_permintaan,
    p.kabid_tujuan,
    p.pic_pimpinan,
    p.status,
    COUNT(d.disposisi_id) as jumlah_disposisi_dari_direktur
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id 
    AND d.jabatan_tujuan LIKE '%Bidang Pelayanan Medis%'
    AND d.catatan LIKE '%Disetujui oleh Direktur%'
WHERE (
    -- Kondisi 1: Permintaan baru dengan klasifikasi medis
    p.klasifikasi_permintaan IN ('Medis', 'medis')
    OR p.kabid_tujuan LIKE '%Bidang Pelayanan Medis%'
)
AND p.status = 'proses'
AND (
    -- Kondisi 1: pic_pimpinan = Kepala Bidang (permintaan baru)
    p.pic_pimpinan LIKE '%Kepala Bidang%'
    -- Kondisi 2: Ada disposisi dari Direktur (disposisi balik)
    OR d.disposisi_id IS NOT NULL
)
GROUP BY p.permintaan_id, p.klasifikasi_permintaan, p.kabid_tujuan, p.pic_pimpinan, p.status
ORDER BY p.permintaan_id DESC;

-- Expected Result:
-- Permintaan yang:
-- 1. Baru dari Kepala Instalasi (pic_pimpinan = Kepala Bidang)
-- 2. Disposisi balik dari Direktur (jumlah_disposisi_dari_direktur > 0)


-- 3. Simulasi untuk Kabid Penunjang Medis
SELECT 
    p.permintaan_id,
    p.klasifikasi_permintaan,
    p.kabid_tujuan,
    p.pic_pimpinan,
    'Kabid Penunjang Medis' as untuk_kabid
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id 
    AND d.jabatan_tujuan LIKE '%Bidang Penunjang Medis%'
    AND d.catatan LIKE '%Disetujui oleh Direktur%'
WHERE (
    p.klasifikasi_permintaan IN ('Penunjang', 'penunjang_medis')
    OR p.kabid_tujuan LIKE '%Bidang Penunjang Medis%'
)
AND p.status = 'proses'
AND (
    p.pic_pimpinan LIKE '%Kepala Bidang%'
    OR d.disposisi_id IS NOT NULL
)
ORDER BY p.permintaan_id DESC;


-- 4. Simulasi untuk Kabid Umum & Keuangan
SELECT 
    p.permintaan_id,
    p.klasifikasi_permintaan,
    p.kabid_tujuan,
    p.pic_pimpinan,
    'Kabid Umum & Keuangan' as untuk_kabid
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id 
    AND (d.jabatan_tujuan LIKE '%Bidang Umum%' OR d.jabatan_tujuan LIKE '%Keuangan%')
    AND d.catatan LIKE '%Disetujui oleh Direktur%'
WHERE (
    p.klasifikasi_permintaan IN ('Non Medis', 'non_medis')
    OR p.kabid_tujuan LIKE '%Bidang Umum%'
)
AND p.status = 'proses'
AND (
    p.pic_pimpinan LIKE '%Kepala Bidang%'
    OR d.disposisi_id IS NOT NULL
)
ORDER BY p.permintaan_id DESC;


-- 5. Cek workflow lengkap untuk satu permintaan
-- Ganti XXX dengan permintaan_id yang ingin dicek
SELECT 
    p.permintaan_id,
    p.klasifikasi_permintaan,
    p.kabid_tujuan,
    p.pic_pimpinan,
    p.status,
    d.disposisi_id,
    d.jabatan_tujuan,
    d.tanggal_disposisi,
    d.catatan,
    d.status as disposisi_status
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE p.permintaan_id = XXX
ORDER BY d.disposisi_id ASC;

-- Expected Workflow:
-- 1. Disposisi dari Kepala Instalasi ke Kabid (jabatan_tujuan = Kepala Bidang/Kabid)
-- 2. Disposisi dari Kabid ke Direktur (jabatan_tujuan = Direktur)
-- 3. Disposisi dari Direktur ke Kabid (jabatan_tujuan = Bidang xxx, catatan = Disetujui oleh Direktur)
-- 4. (Setelah fix) Kabid bisa lihat permintaan dan disposisi ke Staff Perencanaan
