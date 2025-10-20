-- Test Query untuk Verifikasi Workflow Direktur
-- Jalankan di MySQL setelah testing

-- 1. Cek permintaan yang ada di Direktur
SELECT 
    p.permintaan_id,
    p.status,
    p.pic_pimpinan,
    p.bidang,
    u.nama as pemohon,
    DATE_FORMAT(p.tanggal_permintaan, '%d-%m-%Y') as tanggal
FROM permintaan p
LEFT JOIN users u ON p.user_id = u.user_id
WHERE p.pic_pimpinan LIKE '%Direktur%'
ORDER BY p.permintaan_id DESC;

-- 2. Cek disposisi terakhir untuk permintaan tertentu
-- Ganti <ID> dengan permintaan_id yang ingin dicek
SELECT 
    d.disposisi_id,
    d.jabatan_tujuan,
    d.status,
    d.catatan,
    DATE_FORMAT(d.tanggal_disposisi, '%d-%m-%Y %H:%i') as tanggal,
    n.no_nota_dinas
FROM disposisi d
JOIN nota_dinas n ON d.nota_id = n.nota_id
JOIN permintaan p ON n.permintaan_id = p.permintaan_id
WHERE p.permintaan_id = <ID>
ORDER BY d.tanggal_disposisi DESC;

-- 3. Tracking lengkap satu permintaan (workflow)
-- Ganti <ID> dengan permintaan_id
SELECT 
    'Permintaan' as tahapan,
    u.nama as actor,
    p.status,
    DATE_FORMAT(p.tanggal_permintaan, '%d-%m-%Y %H:%i') as tanggal
FROM permintaan p
LEFT JOIN users u ON p.user_id = u.user_id
WHERE p.permintaan_id = <ID>

UNION ALL

SELECT 
    'Nota Dinas' as tahapan,
    n.dari as actor,
    'dikirim' as status,
    DATE_FORMAT(n.tanggal_nota, '%d-%m-%Y %H:%i') as tanggal
FROM nota_dinas n
WHERE n.permintaan_id = <ID>

UNION ALL

SELECT 
    CONCAT('Disposisi ke ', d.jabatan_tujuan) as tahapan,
    'Pimpinan' as actor,
    d.status,
    DATE_FORMAT(d.tanggal_disposisi, '%d-%m-%Y %H:%i') as tanggal
FROM disposisi d
JOIN nota_dinas n ON d.nota_id = n.nota_id
WHERE n.permintaan_id = <ID>
ORDER BY tanggal;

-- 4. Cek permintaan yang sudah disetujui Direktur (ke Staff Perencanaan)
SELECT 
    p.permintaan_id,
    p.status,
    p.pic_pimpinan,
    p.bidang,
    d.jabatan_tujuan,
    d.catatan,
    DATE_FORMAT(d.tanggal_disposisi, '%d-%m-%Y %H:%i') as tgl_disposisi
FROM permintaan p
JOIN nota_dinas n ON p.permintaan_id = n.permintaan_id
JOIN disposisi d ON n.nota_id = d.nota_id
WHERE d.jabatan_tujuan = 'Staff Perencanaan'
  AND d.status = 'disetujui'
  AND d.catatan LIKE '%Direktur%'
ORDER BY p.permintaan_id DESC;

-- 5. Cek permintaan yang ditolak Direktur
SELECT 
    p.permintaan_id,
    p.status,
    p.bidang,
    u.nama as pemohon,
    SUBSTRING(p.deskripsi, LOCATE('[DITOLAK oleh Direktur]', p.deskripsi), 200) as alasan_penolakan,
    d.catatan as catatan_disposisi
FROM permintaan p
LEFT JOIN users u ON p.user_id = u.user_id
LEFT JOIN nota_dinas n ON p.permintaan_id = n.permintaan_id
LEFT JOIN disposisi d ON n.nota_id = d.nota_id AND d.status = 'ditolak'
WHERE p.status = 'ditolak'
  AND p.deskripsi LIKE '%DITOLAK oleh Direktur%'
ORDER BY p.permintaan_id DESC;

-- 6. Cek permintaan revisi dari Direktur ke Kepala Bidang
SELECT 
    p.permintaan_id,
    p.status,
    p.pic_pimpinan,
    p.bidang,
    d.jabatan_tujuan,
    d.catatan,
    DATE_FORMAT(d.tanggal_disposisi, '%d-%m-%Y %H:%i') as tgl_disposisi,
    SUBSTRING(p.deskripsi, LOCATE('[CATATAN REVISI dari Direktur]', p.deskripsi), 300) as catatan_revisi
FROM permintaan p
JOIN nota_dinas n ON p.permintaan_id = n.permintaan_id
JOIN disposisi d ON n.nota_id = d.nota_id
WHERE p.status = 'revisi'
  AND d.status = 'revisi'
  AND d.jabatan_tujuan = 'Kepala Bidang'
  AND d.catatan LIKE '%REVISI dari Direktur%'
ORDER BY p.permintaan_id DESC;

-- 7. Summary statistics Direktur
SELECT 
    'Total di Direktur' as metric,
    COUNT(*) as count
FROM permintaan
WHERE pic_pimpinan LIKE '%Direktur%'

UNION ALL

SELECT 
    'Menunggu Review (proses)' as metric,
    COUNT(*) as count
FROM permintaan
WHERE pic_pimpinan LIKE '%Direktur%'
  AND status = 'proses'

UNION ALL

SELECT 
    'Disetujui (ke Staff Perencanaan)' as metric,
    COUNT(*) as count
FROM permintaan p
JOIN nota_dinas n ON p.permintaan_id = n.permintaan_id
JOIN disposisi d ON n.nota_id = d.nota_id
WHERE d.jabatan_tujuan = 'Staff Perencanaan'
  AND d.status = 'disetujui'
  AND d.catatan LIKE '%Direktur%'

UNION ALL

SELECT 
    'Ditolak oleh Direktur' as metric,
    COUNT(*) as count
FROM permintaan
WHERE status = 'ditolak'
  AND deskripsi LIKE '%DITOLAK oleh Direktur%'

UNION ALL

SELECT 
    'Revisi ke Kepala Bidang' as metric,
    COUNT(*) as count
FROM permintaan p
JOIN nota_dinas n ON p.permintaan_id = n.permintaan_id
JOIN disposisi d ON n.nota_id = d.nota_id
WHERE p.status = 'revisi'
  AND d.jabatan_tujuan = 'Kepala Bidang'
  AND d.catatan LIKE '%REVISI dari Direktur%';

-- 8. Verify disposisi untuk satu permintaan (detail check)
-- Ganti <ID> dengan permintaan_id yang baru saja di approve/reject/revisi
SELECT 
    p.permintaan_id,
    p.status as status_permintaan,
    p.pic_pimpinan,
    n.no_nota_dinas,
    d.jabatan_tujuan,
    d.status as status_disposisi,
    d.catatan,
    DATE_FORMAT(d.tanggal_disposisi, '%d-%m-%Y %H:%i:%s') as waktu_disposisi
FROM permintaan p
LEFT JOIN nota_dinas n ON p.permintaan_id = n.permintaan_id
LEFT JOIN disposisi d ON n.nota_id = d.nota_id
WHERE p.permintaan_id = <ID>
ORDER BY d.tanggal_disposisi DESC
LIMIT 5;
