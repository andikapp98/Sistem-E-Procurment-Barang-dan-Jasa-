-- ============================================
-- VERIFIKASI DOKUMEN KSO
-- ============================================

-- 1. CEK PERMINTAAN DI BAGIAN KSO
-- ============================================
SELECT 
    p.permintaan_id,
    p.bidang,
    p.status,
    p.pic_pimpinan,
    p.tanggal_permintaan,
    p.created_at
FROM permintaans p
WHERE p.pic_pimpinan = 'Bagian KSO'
    AND p.status IN ('proses', 'disetujui')
ORDER BY p.permintaan_id DESC
LIMIT 10;

-- 2. CEK DOKUMEN KSO YANG SUDAH DIBUAT
-- ============================================
SELECT 
    k.kso_id,
    k.no_kso,
    k.tanggal_kso,
    k.pihak_pertama,
    k.pihak_kedua,
    k.isi_kerjasama,
    k.nilai_kontrak,
    k.file_pks,
    k.file_mou,
    k.status,
    k.created_at,
    pr.perencanaan_id,
    nd.permintaan_id
FROM kso k
    INNER JOIN perencanaans pr ON k.perencanaan_id = pr.perencanaan_id
    INNER JOIN disposisis d ON pr.disposisi_id = d.disposisi_id
    INNER JOIN nota_dinas nd ON d.nota_id = nd.nota_id
ORDER BY k.created_at DESC
LIMIT 20;

-- 3. CEK KELENGKAPAN DOKUMEN UNTUK PERMINTAAN TERTENTU
-- ============================================
-- Ganti 456 dengan permintaan_id yang ingin dicek
SET @permintaan_id = 456;

SELECT 
    'Permintaan' as dokumen,
    COUNT(*) as jumlah,
    CASE WHEN COUNT(*) > 0 THEN '✓' ELSE '✗' END as status
FROM permintaans 
WHERE permintaan_id = @permintaan_id

UNION ALL

SELECT 
    'Perencanaan (DPP)' as dokumen,
    COUNT(*) as jumlah,
    CASE WHEN COUNT(*) > 0 THEN '✓' ELSE '✗' END as status
FROM perencanaans pr
    INNER JOIN disposisis d ON pr.disposisi_id = d.disposisi_id
    INNER JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = @permintaan_id

UNION ALL

SELECT 
    'Dokumen KSO' as dokumen,
    COUNT(*) as jumlah,
    CASE WHEN COUNT(*) > 0 THEN '✓' ELSE '✗' END as status
FROM kso k
    INNER JOIN perencanaans pr ON k.perencanaan_id = pr.perencanaan_id
    INNER JOIN disposisis d ON pr.disposisi_id = d.disposisi_id
    INNER JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = @permintaan_id;

-- 4. DETAIL LENGKAP KSO UNTUK PERMINTAAN
-- ============================================
SELECT 
    p.permintaan_id,
    p.bidang,
    p.status,
    p.pic_pimpinan,
    k.kso_id,
    k.no_kso,
    k.tanggal_kso,
    k.pihak_pertama,
    k.pihak_kedua,
    k.isi_kerjasama,
    k.nilai_kontrak,
    k.file_pks,
    k.file_mou,
    k.keterangan,
    k.status as kso_status,
    k.created_at as kso_created_at
FROM permintaans p
    LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    LEFT JOIN disposisis d ON nd.nota_id = d.nota_id
    LEFT JOIN perencanaans pr ON d.disposisi_id = pr.disposisi_id
    LEFT JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
WHERE p.permintaan_id = @permintaan_id;

-- 5. STATISTIK KSO
-- ============================================
SELECT 
    'Total KSO Dibuat' as keterangan,
    COUNT(*) as jumlah
FROM kso

UNION ALL

SELECT 
    'KSO Status Aktif' as keterangan,
    COUNT(*) as jumlah
FROM kso
WHERE status = 'aktif'

UNION ALL

SELECT 
    'KSO Status Selesai' as keterangan,
    COUNT(*) as jumlah
FROM kso
WHERE status = 'selesai'

UNION ALL

SELECT 
    'KSO Status Draft' as keterangan,
    COUNT(*) as jumlah
FROM kso
WHERE status = 'draft'

UNION ALL

SELECT 
    'Permintaan di Bagian Pengadaan (dari KSO)' as keterangan,
    COUNT(*) as jumlah
FROM permintaans p
WHERE p.pic_pimpinan = 'Bagian Pengadaan'
    AND p.deskripsi LIKE '%DOKUMEN KSO LENGKAP%';

-- 6. TRACKING WORKFLOW LENGKAP (PERENCANAAN → KSO → PENGADAAN)
-- ============================================
SELECT 
    p.permintaan_id,
    p.bidang,
    p.status,
    p.pic_pimpinan,
    pr.nama_paket as dpp_nama_paket,
    pr.pagu_paket as dpp_pagu,
    k.no_kso,
    k.pihak_kedua,
    k.nilai_kontrak,
    k.status as kso_status,
    k.file_pks,
    k.file_mou,
    p.tanggal_permintaan,
    k.tanggal_kso,
    k.created_at as kso_created_at
FROM permintaans p
    LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    LEFT JOIN disposisis d ON nd.nota_id = d.nota_id
    LEFT JOIN perencanaans pr ON d.disposisi_id = pr.disposisi_id
    LEFT JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
WHERE k.kso_id IS NOT NULL
ORDER BY k.created_at DESC
LIMIT 20;

-- 7. CEK FILE UPLOAD KSO
-- ============================================
SELECT 
    k.kso_id,
    k.no_kso,
    k.file_pks,
    k.file_mou,
    CASE 
        WHEN k.file_pks IS NOT NULL AND k.file_pks != '' THEN '✓'
        ELSE '✗'
    END as pks_uploaded,
    CASE 
        WHEN k.file_mou IS NOT NULL AND k.file_mou != '' THEN '✓'
        ELSE '✗'
    END as mou_uploaded,
    k.created_at
FROM kso k
ORDER BY k.created_at DESC
LIMIT 20;

-- 8. ACTIVITY LOG UNTUK KSO
-- ============================================
SELECT 
    ual.id,
    ual.user_id,
    u.nama as user_nama,
    ual.role,
    ual.action,
    ual.module,
    ual.description,
    ual.related_type,
    ual.related_id,
    ual.created_at
FROM user_activity_logs ual
    LEFT JOIN users u ON ual.user_id = u.id
WHERE ual.module = 'kso'
    OR (ual.module = 'permintaan' AND ual.description LIKE '%KSO%')
ORDER BY ual.created_at DESC
LIMIT 30;

-- 9. CEK PERMINTAAN YANG BELUM ADA KSO
-- ============================================
SELECT 
    p.permintaan_id,
    p.bidang,
    p.pic_pimpinan,
    p.status,
    CASE 
        WHEN k.kso_id IS NOT NULL THEN '✓ Ada KSO'
        ELSE '✗ Belum ada KSO'
    END as status_kso,
    p.tanggal_permintaan
FROM permintaans p
    LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    LEFT JOIN disposisis d ON nd.nota_id = d.nota_id
    LEFT JOIN perencanaans pr ON d.disposisi_id = pr.disposisi_id
    LEFT JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
WHERE p.pic_pimpinan = 'Bagian KSO'
    AND p.status IN ('proses', 'disetujui')
ORDER BY p.permintaan_id DESC
LIMIT 20;

-- 10. VALIDASI DATA INTEGRITY KSO
-- ============================================
SELECT 
    'KSO tanpa Perencanaan' as issue,
    COUNT(*) as jumlah
FROM kso k
WHERE k.perencanaan_id NOT IN (SELECT perencanaan_id FROM perencanaans)

UNION ALL

SELECT 
    'KSO tanpa File PKS' as issue,
    COUNT(*) as jumlah
FROM kso k
WHERE k.file_pks IS NULL OR k.file_pks = ''

UNION ALL

SELECT 
    'KSO tanpa File MoU' as issue,
    COUNT(*) as jumlah
FROM kso k
WHERE k.file_mou IS NULL OR k.file_mou = ''

UNION ALL

SELECT 
    'KSO tanpa Nilai Kontrak' as issue,
    COUNT(*) as jumlah
FROM kso k
WHERE k.nilai_kontrak IS NULL OR k.nilai_kontrak = 0

UNION ALL

SELECT 
    'KSO tanpa Isi Kerjasama' as issue,
    COUNT(*) as jumlah
FROM kso k
WHERE k.isi_kerjasama IS NULL OR k.isi_kerjasama = '';

-- 11. NILAI TOTAL KONTRAK KSO
-- ============================================
SELECT 
    COUNT(*) as total_kso,
    SUM(nilai_kontrak) as total_nilai_kontrak,
    AVG(nilai_kontrak) as rata_rata_nilai_kontrak,
    MIN(nilai_kontrak) as nilai_kontrak_minimum,
    MAX(nilai_kontrak) as nilai_kontrak_maksimum
FROM kso
WHERE status = 'aktif';

-- 12. KSO BERDASARKAN PIHAK KEDUA
-- ============================================
SELECT 
    k.pihak_kedua,
    COUNT(*) as jumlah_kso,
    SUM(k.nilai_kontrak) as total_nilai_kontrak,
    GROUP_CONCAT(k.no_kso SEPARATOR ', ') as daftar_no_kso
FROM kso k
GROUP BY k.pihak_kedua
ORDER BY jumlah_kso DESC, total_nilai_kontrak DESC
LIMIT 10;
