-- ============================================
-- VERIFIKASI WORKFLOW STAFF PERENCANAAN KE PENGADAAN
-- ============================================

-- 1. CEK PERMINTAAN YANG SUDAH DI-FORWARD KE PENGADAAN
-- ============================================
SELECT 
    p.permintaan_id,
    p.bidang,
    p.status,
    p.pic_pimpinan,
    p.tanggal_permintaan,
    p.created_at,
    p.updated_at
FROM permintaans p
WHERE p.pic_pimpinan = 'Bagian Pengadaan'
    AND p.status = 'proses'
ORDER BY p.permintaan_id DESC
LIMIT 10;

-- 2. CEK KELENGKAPAN DOKUMEN UNTUK PERMINTAAN TERTENTU
-- ============================================
-- Ganti 123 dengan permintaan_id yang ingin dicek
SET @permintaan_id = 123;

SELECT 
    'Permintaan' as dokumen,
    COUNT(*) as jumlah,
    CASE WHEN COUNT(*) > 0 THEN '✓' ELSE '✗' END as status
FROM permintaans 
WHERE permintaan_id = @permintaan_id

UNION ALL

SELECT 
    'Nota Dinas (Default)' as dokumen,
    COUNT(*) as jumlah,
    CASE WHEN COUNT(*) > 0 THEN '✓' ELSE '✗' END as status
FROM nota_dinas 
WHERE permintaan_id = @permintaan_id 
    AND (tipe_nota IS NULL OR tipe_nota = 'default' OR tipe_nota = '')

UNION ALL

SELECT 
    'Nota Dinas Pembelian' as dokumen,
    COUNT(*) as jumlah,
    CASE WHEN COUNT(*) > 0 THEN '✓' ELSE '✗' END as status
FROM nota_dinas 
WHERE permintaan_id = @permintaan_id 
    AND tipe_nota = 'pembelian'

UNION ALL

SELECT 
    'DPP (Perencanaan)' as dokumen,
    COUNT(*) as jumlah,
    CASE WHEN COUNT(*) > 0 THEN '✓' ELSE '✗' END as status
FROM perencanaans pr
    INNER JOIN disposisis d ON pr.disposisi_id = d.disposisi_id
    INNER JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = @permintaan_id

UNION ALL

SELECT 
    'HPS' as dokumen,
    COUNT(*) as jumlah,
    CASE WHEN COUNT(*) > 0 THEN '✓' ELSE '✗' END as status
FROM hps 
WHERE permintaan_id = @permintaan_id

UNION ALL

SELECT 
    'HPS Items' as dokumen,
    COUNT(*) as jumlah,
    CASE WHEN COUNT(*) > 0 THEN '✓' ELSE '✗' END as status
FROM hps_items hi
    INNER JOIN hps h ON hi.hps_id = h.hps_id
WHERE h.permintaan_id = @permintaan_id

UNION ALL

SELECT 
    'Spesifikasi Teknis' as dokumen,
    COUNT(*) as jumlah,
    CASE WHEN COUNT(*) > 0 THEN '✓' ELSE '✗' END as status
FROM spesifikasi_teknis 
WHERE permintaan_id = @permintaan_id

UNION ALL

SELECT 
    'Disposisi ke Pengadaan' as dokumen,
    COUNT(*) as jumlah,
    CASE WHEN COUNT(*) > 0 THEN '✓' ELSE '✗' END as status
FROM disposisis d
    INNER JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = @permintaan_id
    AND d.jabatan_tujuan = 'Bagian Pengadaan';

-- 3. DETAIL LENGKAP PERMINTAAN DAN SEMUA DOKUMENNYA
-- ============================================
-- Permintaan
SELECT 
    'PERMINTAAN' as kategori,
    p.*
FROM permintaans p
WHERE p.permintaan_id = @permintaan_id;

-- Nota Dinas (Semua)
SELECT 
    'NOTA DINAS' as kategori,
    nd.*
FROM nota_dinas nd
WHERE nd.permintaan_id = @permintaan_id;

-- DPP (Perencanaan)
SELECT 
    'DPP (PERENCANAAN)' as kategori,
    pr.*
FROM perencanaans pr
    INNER JOIN disposisis d ON pr.disposisi_id = d.disposisi_id
    INNER JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = @permintaan_id;

-- HPS
SELECT 
    'HPS' as kategori,
    h.*
FROM hps h
WHERE h.permintaan_id = @permintaan_id;

-- HPS Items
SELECT 
    'HPS ITEMS' as kategori,
    hi.*
FROM hps_items hi
    INNER JOIN hps h ON hi.hps_id = h.hps_id
WHERE h.permintaan_id = @permintaan_id;

-- Spesifikasi Teknis
SELECT 
    'SPESIFIKASI TEKNIS' as kategori,
    st.*
FROM spesifikasi_teknis st
WHERE st.permintaan_id = @permintaan_id;

-- Disposisi
SELECT 
    'DISPOSISI' as kategori,
    d.*
FROM disposisis d
    INNER JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = @permintaan_id;

-- Activity Logs
SELECT 
    'ACTIVITY LOG' as kategori,
    ual.*
FROM user_activity_logs ual
WHERE ual.related_type = 'Permintaan' 
    AND ual.related_id = @permintaan_id
ORDER BY ual.created_at DESC;

-- 4. STATISTIK PERMINTAAN YANG SUDAH DI-FORWARD
-- ============================================
SELECT 
    'Total permintaan di Bagian Pengadaan' as keterangan,
    COUNT(*) as jumlah
FROM permintaans
WHERE pic_pimpinan = 'Bagian Pengadaan'

UNION ALL

SELECT 
    'Permintaan dengan status proses' as keterangan,
    COUNT(*) as jumlah
FROM permintaans
WHERE pic_pimpinan = 'Bagian Pengadaan' 
    AND status = 'proses'

UNION ALL

SELECT 
    'Permintaan lengkap dengan semua dokumen' as keterangan,
    COUNT(DISTINCT p.permintaan_id) as jumlah
FROM permintaans p
    INNER JOIN nota_dinas nd1 ON p.permintaan_id = nd1.permintaan_id 
        AND (nd1.tipe_nota IS NULL OR nd1.tipe_nota = '' OR nd1.tipe_nota = 'default')
    INNER JOIN nota_dinas nd2 ON p.permintaan_id = nd2.permintaan_id 
        AND nd2.tipe_nota = 'pembelian'
    INNER JOIN hps h ON p.permintaan_id = h.permintaan_id
    INNER JOIN spesifikasi_teknis st ON p.permintaan_id = st.permintaan_id
WHERE p.pic_pimpinan = 'Bagian Pengadaan';

-- 5. CEK PERMINTAAN YANG BELUM LENGKAP DOKUMENNYA
-- ============================================
SELECT 
    p.permintaan_id,
    p.bidang,
    p.pic_pimpinan,
    CASE WHEN nd1.nota_id IS NOT NULL THEN '✓' ELSE '✗' END as nota_dinas,
    CASE WHEN nd2.nota_id IS NOT NULL THEN '✓' ELSE '✗' END as nota_dinas_pembelian,
    CASE WHEN pr.perencanaan_id IS NOT NULL THEN '✓' ELSE '✗' END as dpp,
    CASE WHEN h.hps_id IS NOT NULL THEN '✓' ELSE '✗' END as hps,
    CASE WHEN st.spesifikasi_id IS NOT NULL THEN '✓' ELSE '✗' END as spesifikasi_teknis
FROM permintaans p
    LEFT JOIN nota_dinas nd1 ON p.permintaan_id = nd1.permintaan_id 
        AND (nd1.tipe_nota IS NULL OR nd1.tipe_nota = '' OR nd1.tipe_nota = 'default')
    LEFT JOIN nota_dinas nd2 ON p.permintaan_id = nd2.permintaan_id 
        AND nd2.tipe_nota = 'pembelian'
    LEFT JOIN (
        SELECT DISTINCT nd.permintaan_id, pr.perencanaan_id
        FROM perencanaans pr
            INNER JOIN disposisis d ON pr.disposisi_id = d.disposisi_id
            INNER JOIN nota_dinas nd ON d.nota_id = nd.nota_id
    ) pr ON p.permintaan_id = pr.permintaan_id
    LEFT JOIN hps h ON p.permintaan_id = h.permintaan_id
    LEFT JOIN spesifikasi_teknis st ON p.permintaan_id = st.permintaan_id
WHERE p.pic_pimpinan = 'Staff Perencanaan'
    AND p.status IN ('disetujui', 'proses')
ORDER BY p.permintaan_id DESC
LIMIT 20;

-- 6. TRACKING WORKFLOW LENGKAP UNTUK PERMINTAAN
-- ============================================
SELECT 
    p.permintaan_id,
    p.bidang,
    p.status,
    p.pic_pimpinan,
    p.tanggal_permintaan,
    nd1.nomor as nota_dinas_nomor,
    nd1.tanggal_nota as nota_dinas_tanggal,
    nd2.nomor as nota_pembelian_nomor,
    nd2.tanggal_nota as nota_pembelian_tanggal,
    pr.nama_paket as dpp_nama_paket,
    pr.pagu_paket as dpp_pagu,
    h.total_hps,
    (SELECT COUNT(*) FROM hps_items WHERE hps_id = h.hps_id) as jumlah_item_hps,
    st.jenis_barang_jasa as spesifikasi_jenis,
    d.jabatan_tujuan,
    d.tanggal_disposisi,
    d.status as disposisi_status
FROM permintaans p
    LEFT JOIN nota_dinas nd1 ON p.permintaan_id = nd1.permintaan_id 
        AND (nd1.tipe_nota IS NULL OR nd1.tipe_nota = '' OR nd1.tipe_nota = 'default')
    LEFT JOIN nota_dinas nd2 ON p.permintaan_id = nd2.permintaan_id 
        AND nd2.tipe_nota = 'pembelian'
    LEFT JOIN (
        SELECT nd.permintaan_id, pr.nama_paket, pr.pagu_paket
        FROM perencanaans pr
            INNER JOIN disposisis d ON pr.disposisi_id = d.disposisi_id
            INNER JOIN nota_dinas nd ON d.nota_id = nd.nota_id
    ) pr ON p.permintaan_id = pr.permintaan_id
    LEFT JOIN hps h ON p.permintaan_id = h.permintaan_id
    LEFT JOIN spesifikasi_teknis st ON p.permintaan_id = st.permintaan_id
    LEFT JOIN disposisis d ON nd1.nota_id = d.nota_id OR nd2.nota_id = d.nota_id
WHERE p.permintaan_id = @permintaan_id
    OR p.pic_pimpinan = 'Bagian Pengadaan'
ORDER BY p.permintaan_id DESC
LIMIT 10;

-- 7. ACTIVITY LOG UNTUK FORWARD KE PENGADAAN
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
WHERE ual.action = 'forward'
    AND ual.module = 'permintaan'
    AND ual.description LIKE '%Bagian Pengadaan%'
ORDER BY ual.created_at DESC
LIMIT 20;

-- 8. VALIDASI DATA INTEGRITY
-- ============================================
-- Cek ada orphan records atau tidak
SELECT 
    'Nota Dinas tanpa Permintaan' as issue,
    COUNT(*) as jumlah
FROM nota_dinas nd
WHERE nd.permintaan_id NOT IN (SELECT permintaan_id FROM permintaans)

UNION ALL

SELECT 
    'HPS tanpa Permintaan' as issue,
    COUNT(*) as jumlah
FROM hps h
WHERE h.permintaan_id NOT IN (SELECT permintaan_id FROM permintaans)

UNION ALL

SELECT 
    'Spesifikasi Teknis tanpa Permintaan' as issue,
    COUNT(*) as jumlah
FROM spesifikasi_teknis st
WHERE st.permintaan_id NOT IN (SELECT permintaan_id FROM permintaans)

UNION ALL

SELECT 
    'HPS Items tanpa HPS' as issue,
    COUNT(*) as jumlah
FROM hps_items hi
WHERE hi.hps_id NOT IN (SELECT hps_id FROM hps)

UNION ALL

SELECT 
    'Disposisi tanpa Nota Dinas' as issue,
    COUNT(*) as jumlah
FROM disposisis d
WHERE d.nota_id NOT IN (SELECT nota_id FROM nota_dinas);
