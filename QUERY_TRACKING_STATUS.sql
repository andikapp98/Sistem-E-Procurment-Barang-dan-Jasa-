-- ====================================================================
-- QUERY TRACKING RIWAYAT TAHAPAN E-PROCUREMENT
-- Sistem e-Procurement RSUD Ibnu Sina
-- ====================================================================
-- Query ini menampilkan tracking lengkap status di setiap tahap proses
-- dengan informasi detail kemajuan pengajuan

SELECT 
    ID_Permintaan,
    Deskripsi_Permintaan,
    Tahapan,
    Tanggal_Tahapan,
    Status_Tahapan,
    Keterangan
FROM (
    -- TAHAP 1: PERMINTAAN
    SELECT 
        p.permintaan_id AS ID_Permintaan,
        p.deskripsi AS Deskripsi_Permintaan,
        'Permintaan' AS Tahapan,
        p.tanggal_permintaan AS Tanggal_Tahapan,
        LOWER(p.status) AS Status_Tahapan,
        '-' AS Keterangan,
        1 AS urutan_tahap
    FROM permintaan p
    
    UNION ALL
    
    -- TAHAP 2: NOTA DINAS
    SELECT 
        p.permintaan_id AS ID_Permintaan,
        p.deskripsi AS Deskripsi_Permintaan,
        'Nota Dinas' AS Tahapan,
        nd.tanggal_nota AS Tanggal_Tahapan,
        LOWER(nd.status) AS Status_Tahapan,
        CONCAT('ke: ', nd.ke_jabatan) AS Keterangan,
        2 AS urutan_tahap
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    
    UNION ALL
    
    -- TAHAP 3: DISPOSISI
    SELECT 
        p.permintaan_id AS ID_Permintaan,
        p.deskripsi AS Deskripsi_Permintaan,
        'Disposisi' AS Tahapan,
        d.tanggal_disposisi AS Tanggal_Tahapan,
        LOWER(d.status) AS Status_Tahapan,
        CONCAT('ke: ', d.jabatan_tujuan,
               CASE 
                   WHEN d.catatan IS NOT NULL AND d.catatan != '' 
                   THEN CONCAT(' | catatan: ', d.catatan)
                   ELSE ''
               END) AS Keterangan,
        3 AS urutan_tahap
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    INNER JOIN disposisi d ON nd.nota_id = d.nota_id
    
    UNION ALL
    
    -- TAHAP 4: PERENCANAAN
    SELECT 
        p.permintaan_id AS ID_Permintaan,
        p.deskripsi AS Deskripsi_Permintaan,
        'Perencanaan' AS Tahapan,
        pr.tanggal_perencanaan AS Tanggal_Tahapan,
        LOWER(pr.status) AS Status_Tahapan,
        CONCAT('rincian: ', 
               CASE 
                   WHEN LENGTH(pr.rincian) > 50 
                   THEN CONCAT(SUBSTRING(pr.rincian, 1, 50), '...')
                   ELSE pr.rincian
               END) AS Keterangan,
        4 AS urutan_tahap
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    INNER JOIN disposisi d ON nd.nota_id = d.nota_id
    INNER JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
    
    UNION ALL
    
    -- TAHAP 5: KSO
    SELECT 
        p.permintaan_id AS ID_Permintaan,
        p.deskripsi AS Deskripsi_Permintaan,
        'KSO' AS Tahapan,
        k.tanggal_kso AS Tanggal_Tahapan,
        LOWER(k.status) AS Status_Tahapan,
        CONCAT('kso: ', 
               CASE 
                   WHEN LENGTH(k.deskripsi) > 50 
                   THEN CONCAT(SUBSTRING(k.deskripsi, 1, 50), '...')
                   ELSE k.deskripsi
               END) AS Keterangan,
        5 AS urutan_tahap
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    INNER JOIN disposisi d ON nd.nota_id = d.nota_id
    INNER JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
    INNER JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
    
    UNION ALL
    
    -- TAHAP 6: PENGADAAN
    SELECT 
        p.permintaan_id AS ID_Permintaan,
        p.deskripsi AS Deskripsi_Permintaan,
        'Pengadaan' AS Tahapan,
        pg.tanggal_pengadaan AS Tanggal_Tahapan,
        LOWER(pg.status) AS Status_Tahapan,
        CONCAT('vendor: ', pg.vendor,
               CASE 
                   WHEN pg.tracking IS NOT NULL AND pg.tracking != '' 
                   THEN CONCAT(' | tracking: ', pg.tracking)
                   ELSE ''
               END) AS Keterangan,
        6 AS urutan_tahap
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    INNER JOIN disposisi d ON nd.nota_id = d.nota_id
    INNER JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
    INNER JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
    INNER JOIN pengadaan pg ON k.kso_id = pg.kso_id
    
    UNION ALL
    
    -- TAHAP 7: NOTA PENERIMAAN
    SELECT 
        p.permintaan_id AS ID_Permintaan,
        p.deskripsi AS Deskripsi_Permintaan,
        'Nota Penerimaan' AS Tahapan,
        np.tanggal_penerimaan AS Tanggal_Tahapan,
        LOWER(np.status) AS Status_Tahapan,
        CONCAT('penerimaan barang/jasa',
               CASE 
                   WHEN np.catatan IS NOT NULL AND np.catatan != '' 
                   THEN CONCAT(' | catatan: ', np.catatan)
                   ELSE ''
               END) AS Keterangan,
        7 AS urutan_tahap
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    INNER JOIN disposisi d ON nd.nota_id = d.nota_id
    INNER JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
    INNER JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
    INNER JOIN pengadaan pg ON k.kso_id = pg.kso_id
    INNER JOIN nota_penerimaan np ON pg.pengadaan_id = np.pengadaan_id
    
    UNION ALL
    
    -- TAHAP 8: SERAH TERIMA
    SELECT 
        p.permintaan_id AS ID_Permintaan,
        p.deskripsi AS Deskripsi_Permintaan,
        'Serah Terima' AS Tahapan,
        st.tanggal_serah AS Tanggal_Tahapan,
        LOWER(st.status) AS Status_Tahapan,
        CONCAT('penerima: ', st.penerima) AS Keterangan,
        8 AS urutan_tahap
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    INNER JOIN disposisi d ON nd.nota_id = d.nota_id
    INNER JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
    INNER JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
    INNER JOIN pengadaan pg ON k.kso_id = pg.kso_id
    INNER JOIN nota_penerimaan np ON pg.pengadaan_id = np.pengadaan_id
    INNER JOIN serah_terima st ON np.nota_penerimaan_id = st.nota_penerimaan_id
    
) AS riwayat_tracking

ORDER BY ID_Permintaan, urutan_tahap, Tanggal_Tahapan;

-- ====================================================================
-- CONTOH OUTPUT:
-- ====================================================================
-- ID_Permintaan | Deskripsi_Permintaan  | Tahapan         | Tanggal_Tahapan | Status_Tahapan  | Keterangan
-- --------------|----------------------|-----------------|-----------------|-----------------|---------------------------
-- 7             | Pengadaan alat IGD   | Permintaan      | 2025-10-15      | diajukan        | -
-- 7             | Pengadaan alat IGD   | Nota Dinas      | 2025-10-15      | proses          | ke: Kepala Instalasi
-- 7             | Pengadaan alat IGD   | Disposisi       | 2025-10-16      | dalam_proses    | ke: Bagian Pengadaan
-- 7             | Pengadaan alat IGD   | Pengadaan       | 2025-10-17      | pembelian       | vendor: PT Meditek
-- 7             | Pengadaan alat IGD   | Serah Terima    | 2025-10-20      | diterima_unit   | penerima: IGD
-- ====================================================================

-- ====================================================================
-- REKOMENDASI STATUS PER TAHAP:
-- ====================================================================
-- 
-- PERMINTAAN:
-- - diajukan
-- - ditolak
-- - disetujui
--
-- NOTA DINAS:
-- - draft
-- - proses
-- - dikirim
-- - ditolak
-- - disetujui
--
-- DISPOSISI:
-- - menunggu
-- - dalam_proses
-- - ditolak
-- - disetujui
--
-- PERENCANAAN:
-- - draft
-- - review
-- - revisi
-- - disetujui
--
-- KSO:
-- - draft
-- - negosiasi
-- - proses_kontrak
-- - aktif
-- - selesai
--
-- PENGADAAN:
-- - tender
-- - pembelian
-- - pengiriman
-- - diterima
-- - ditolak
--
-- NOTA PENERIMAAN:
-- - pending
-- - diterima_sebagian
-- - diterima_lengkap
-- - ditolak
--
-- SERAH TERIMA:
-- - menunggu_penerima
-- - diterima_unit
-- - selesai
-- ====================================================================
