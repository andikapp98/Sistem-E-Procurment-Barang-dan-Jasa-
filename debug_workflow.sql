-- Debug Workflow Direktur ke Staff Perencanaan
-- Jalankan query ini untuk cek data

-- 1. Cek permintaan yang ada di Kepala Bidang
SELECT 
    p.permintaan_id,
    p.deskripsi,
    p.status,
    p.pic_pimpinan,
    p.tanggal_permintaan
FROM permintaan p
WHERE p.pic_pimpinan = 'Kepala Bidang' 
  AND p.status = 'proses'
ORDER BY p.permintaan_id DESC
LIMIT 5;

-- 2. Cek disposisi untuk permintaan tersebut
SELECT 
    p.permintaan_id,
    nd.nota_id,
    d.disposisi_id,
    d.jabatan_tujuan,
    d.status AS disposisi_status,
    d.catatan,
    d.tanggal_disposisi
FROM permintaan p
JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE p.pic_pimpinan = 'Kepala Bidang' 
  AND p.status = 'proses'
ORDER BY d.tanggal_disposisi DESC;

-- 3. Cek apakah ada permintaan di Staff Perencanaan
SELECT 
    p.permintaan_id,
    p.deskripsi,
    p.status,
    p.pic_pimpinan,
    p.tanggal_permintaan
FROM permintaan p
WHERE p.pic_pimpinan = 'Staff Perencanaan'
ORDER BY p.permintaan_id DESC
LIMIT 5;

-- 4. Cek history workflow dari Direktur
SELECT 
    p.permintaan_id,
    d.disposisi_id,
    d.jabatan_tujuan,
    d.status AS disposisi_status,
    d.catatan,
    d.tanggal_disposisi
FROM permintaan p
JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE d.catatan LIKE '%Disetujui oleh Direktur%'
ORDER BY p.permintaan_id DESC, d.tanggal_disposisi DESC
LIMIT 10;

-- 5. Cek disposisi yang menuju ke Staff Perencanaan
SELECT 
    p.permintaan_id,
    d.disposisi_id,
    d.jabatan_tujuan,
    d.status AS disposisi_status,
    d.catatan,
    d.tanggal_disposisi
FROM permintaan p
JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE d.jabatan_tujuan = 'Staff Perencanaan'
ORDER BY p.permintaan_id DESC, d.tanggal_disposisi DESC
LIMIT 10;
