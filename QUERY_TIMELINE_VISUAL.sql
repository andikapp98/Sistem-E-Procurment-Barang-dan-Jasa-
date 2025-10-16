-- ====================================================================
-- QUERY: Tracking Timeline (Untuk Dashboard/Monitoring)
-- ====================================================================
-- Query ini menampilkan timeline lengkap dengan indikator visual

SELECT 
    ID_Permintaan,
    Deskripsi_Permintaan,
    Tahapan,
    Tanggal_Tahapan,
    Status_Tahapan,
    Status_Visual,
    Keterangan
FROM (
    -- TAHAP 1: PERMINTAAN
    SELECT 
        p.permintaan_id AS ID_Permintaan,
        p.deskripsi AS Deskripsi_Permintaan,
        'Permintaan' AS Tahapan,
        p.tanggal_permintaan AS Tanggal_Tahapan,
        LOWER(p.status) AS Status_Tahapan,
        CASE 
            WHEN LOWER(p.status) = 'diajukan' THEN '🟡 Diajukan'
            WHEN LOWER(p.status) = 'disetujui' THEN '✅ Disetujui'
            WHEN LOWER(p.status) = 'ditolak' THEN '❌ Ditolak'
            ELSE '⚪ ' || p.status
        END AS Status_Visual,
        '-' AS Keterangan,
        1 AS urutan_tahap
    FROM permintaan p
    
    UNION ALL
    
    -- TAHAP 2: NOTA DINAS
    SELECT 
        p.permintaan_id,
        p.deskripsi,
        'Nota Dinas',
        nd.tanggal_nota,
        LOWER(nd.status),
        CASE 
            WHEN LOWER(nd.status) = 'draft' THEN '⚪ Draft'
            WHEN LOWER(nd.status) = 'proses' THEN '🔄 Proses'
            WHEN LOWER(nd.status) = 'dikirim' THEN '📤 Dikirim'
            WHEN LOWER(nd.status) = 'disetujui' THEN '✅ Disetujui'
            WHEN LOWER(nd.status) = 'ditolak' THEN '❌ Ditolak'
            ELSE '⚪ ' || nd.status
        END,
        CONCAT('ke: ', nd.ke_jabatan),
        2
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    
    UNION ALL
    
    -- TAHAP 3: DISPOSISI
    SELECT 
        p.permintaan_id,
        p.deskripsi,
        'Disposisi',
        d.tanggal_disposisi,
        LOWER(d.status),
        CASE 
            WHEN LOWER(d.status) = 'menunggu' THEN '⏳ Menunggu'
            WHEN LOWER(d.status) = 'dalam_proses' THEN '🔄 Dalam Proses'
            WHEN LOWER(d.status) = 'disetujui' THEN '✅ Disetujui'
            WHEN LOWER(d.status) = 'ditolak' THEN '❌ Ditolak'
            ELSE '⚪ ' || d.status
        END,
        CONCAT('ke: ', d.jabatan_tujuan),
        3
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    INNER JOIN disposisi d ON nd.nota_id = d.nota_id
    
    UNION ALL
    
    -- TAHAP 4: PERENCANAAN
    SELECT 
        p.permintaan_id,
        p.deskripsi,
        'Perencanaan',
        pr.tanggal_perencanaan,
        LOWER(pr.status),
        CASE 
            WHEN LOWER(pr.status) = 'draft' THEN '⚪ Draft'
            WHEN LOWER(pr.status) = 'review' THEN '👁️ Review'
            WHEN LOWER(pr.status) = 'revisi' THEN '🔄 Revisi'
            WHEN LOWER(pr.status) = 'disetujui' THEN '✅ Disetujui'
            ELSE '⚪ ' || pr.status
        END,
        CONCAT('rincian: ', SUBSTRING(pr.rincian, 1, 30), '...'),
        4
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    INNER JOIN disposisi d ON nd.nota_id = d.nota_id
    INNER JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
    
    UNION ALL
    
    -- TAHAP 5: KSO
    SELECT 
        p.permintaan_id,
        p.deskripsi,
        'KSO',
        k.tanggal_kso,
        LOWER(k.status),
        CASE 
            WHEN LOWER(k.status) = 'draft' THEN '⚪ Draft'
            WHEN LOWER(k.status) = 'negosiasi' THEN '💬 Negosiasi'
            WHEN LOWER(k.status) = 'proses_kontrak' THEN '📝 Proses Kontrak'
            WHEN LOWER(k.status) = 'aktif' THEN '✅ Aktif'
            WHEN LOWER(k.status) = 'selesai' THEN '🏁 Selesai'
            ELSE '⚪ ' || k.status
        END,
        CONCAT('kso: ', SUBSTRING(k.deskripsi, 1, 30), '...'),
        5
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    INNER JOIN disposisi d ON nd.nota_id = d.nota_id
    INNER JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
    INNER JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
    
    UNION ALL
    
    -- TAHAP 6: PENGADAAN
    SELECT 
        p.permintaan_id,
        p.deskripsi,
        'Pengadaan',
        pg.tanggal_pengadaan,
        LOWER(pg.status),
        CASE 
            WHEN LOWER(pg.status) = 'tender' THEN '📢 Tender'
            WHEN LOWER(pg.status) = 'pembelian' THEN '🛒 Pembelian'
            WHEN LOWER(pg.status) = 'pengiriman' THEN '🚚 Pengiriman'
            WHEN LOWER(pg.status) = 'diterima' THEN '📦 Diterima'
            WHEN LOWER(pg.status) = 'ditolak' THEN '❌ Ditolak'
            ELSE '⚪ ' || pg.status
        END,
        CONCAT('vendor: ', pg.vendor),
        6
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    INNER JOIN disposisi d ON nd.nota_id = d.nota_id
    INNER JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
    INNER JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
    INNER JOIN pengadaan pg ON k.kso_id = pg.kso_id
    
    UNION ALL
    
    -- TAHAP 7: NOTA PENERIMAAN
    SELECT 
        p.permintaan_id,
        p.deskripsi,
        'Nota Penerimaan',
        np.tanggal_penerimaan,
        LOWER(np.status),
        CASE 
            WHEN LOWER(np.status) = 'pending' THEN '⏳ Pending'
            WHEN LOWER(np.status) = 'diterima_sebagian' THEN '📦 Sebagian'
            WHEN LOWER(np.status) = 'diterima_lengkap' THEN '✅ Lengkap'
            WHEN LOWER(np.status) = 'ditolak' THEN '❌ Ditolak'
            ELSE '⚪ ' || np.status
        END,
        'penerimaan barang/jasa',
        7
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
        p.permintaan_id,
        p.deskripsi,
        'Serah Terima',
        st.tanggal_serah,
        LOWER(st.status),
        CASE 
            WHEN LOWER(st.status) = 'menunggu_penerima' THEN '⏳ Menunggu'
            WHEN LOWER(st.status) = 'diterima_unit' THEN '✅ Diterima Unit'
            WHEN LOWER(st.status) = 'selesai' THEN '🏁 Selesai'
            ELSE '⚪ ' || st.status
        END,
        CONCAT('penerima: ', st.penerima),
        8
    FROM permintaan p
    INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
    INNER JOIN disposisi d ON nd.nota_id = d.nota_id
    INNER JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
    INNER JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
    INNER JOIN pengadaan pg ON k.kso_id = pg.kso_id
    INNER JOIN nota_penerimaan np ON pg.pengadaan_id = np.pengadaan_id
    INNER JOIN serah_terima st ON np.nota_penerimaan_id = st.nota_penerimaan_id
    
) AS timeline

ORDER BY ID_Permintaan, urutan_tahap, Tanggal_Tahapan;

-- ====================================================================
-- CONTOH OUTPUT DENGAN EMOJI VISUAL:
-- ====================================================================
-- ID | Deskripsi          | Tahapan        | Tanggal    | Status        | Status_Visual      | Keterangan
-- ---|-------------------|----------------|------------|---------------|-------------------|-------------------
-- 7  | Pengadaan alat IGD| Permintaan     | 2025-10-15 | diajukan      | 🟡 Diajukan       | -
-- 7  | Pengadaan alat IGD| Nota Dinas     | 2025-10-15 | proses        | 🔄 Proses         | ke: Kepala Instalasi
-- 7  | Pengadaan alat IGD| Disposisi      | 2025-10-16 | dalam_proses  | 🔄 Dalam Proses   | ke: Bagian Pengadaan
-- 7  | Pengadaan alat IGD| Pengadaan      | 2025-10-17 | pembelian     | 🛒 Pembelian      | vendor: PT Meditek
-- 7  | Pengadaan alat IGD| Serah Terima   | 2025-10-20 | diterima_unit | ✅ Diterima Unit  | penerima: IGD
-- ====================================================================
