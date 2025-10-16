-- ====================================================================
-- VIEW: Tracking Status Permintaan (Simplified)
-- ====================================================================
-- Create VIEW untuk memudahkan query tracking status
-- VIEW ini bisa langsung digunakan seperti tabel biasa

CREATE OR REPLACE VIEW v_tracking_status AS
SELECT 
    p.permintaan_id AS ID_Permintaan,
    p.deskripsi AS Deskripsi_Permintaan,
    p.tanggal_permintaan AS Tanggal_Permintaan,
    -- Status Permintaan
    p.status AS Status_Permintaan,
    p.tanggal_permintaan AS Tgl_Permintaan,
    -- Status Nota Dinas
    nd.status AS Status_Nota_Dinas,
    nd.tanggal_nota AS Tgl_Nota_Dinas,
    nd.ke_jabatan AS Nota_Ke_Jabatan,
    -- Status Disposisi
    d.status AS Status_Disposisi,
    d.tanggal_disposisi AS Tgl_Disposisi,
    d.jabatan_tujuan AS Disposisi_Ke_Jabatan,
    -- Status Perencanaan
    pr.status AS Status_Perencanaan,
    pr.tanggal_perencanaan AS Tgl_Perencanaan,
    -- Status KSO
    k.status AS Status_KSO,
    k.tanggal_kso AS Tgl_KSO,
    -- Status Pengadaan
    pg.status AS Status_Pengadaan,
    pg.tanggal_pengadaan AS Tgl_Pengadaan,
    pg.vendor AS Vendor,
    pg.tracking AS Tracking_Number,
    -- Status Nota Penerimaan
    np.status AS Status_Nota_Penerimaan,
    np.tanggal_penerimaan AS Tgl_Penerimaan,
    -- Status Serah Terima
    st.status AS Status_Serah_Terima,
    st.tanggal_serah AS Tgl_Serah_Terima,
    st.penerima AS Penerima,
    -- Progress Indicator (tahap terakhir yang dieksekusi)
    CASE 
        WHEN st.serah_id IS NOT NULL THEN 'Serah Terima'
        WHEN np.nota_penerimaan_id IS NOT NULL THEN 'Nota Penerimaan'
        WHEN pg.pengadaan_id IS NOT NULL THEN 'Pengadaan'
        WHEN k.kso_id IS NOT NULL THEN 'KSO'
        WHEN pr.perencanaan_id IS NOT NULL THEN 'Perencanaan'
        WHEN d.disposisi_id IS NOT NULL THEN 'Disposisi'
        WHEN nd.nota_id IS NOT NULL THEN 'Nota Dinas'
        ELSE 'Permintaan'
    END AS Tahap_Saat_Ini,
    -- Durasi total (dari permintaan sampai tahap terakhir)
    DATEDIFF(
        COALESCE(st.tanggal_serah, np.tanggal_penerimaan, pg.tanggal_pengadaan, 
                 k.tanggal_kso, pr.tanggal_perencanaan, d.tanggal_disposisi, 
                 nd.tanggal_nota, p.tanggal_permintaan),
        p.tanggal_permintaan
    ) AS Durasi_Hari
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
LEFT JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
LEFT JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
LEFT JOIN pengadaan pg ON k.kso_id = pg.kso_id
LEFT JOIN nota_penerimaan np ON pg.pengadaan_id = np.pengadaan_id
LEFT JOIN serah_terima st ON np.nota_penerimaan_id = st.nota_penerimaan_id;

-- ====================================================================
-- CARA PENGGUNAAN VIEW:
-- ====================================================================

-- 1. Lihat semua tracking status
SELECT * FROM v_tracking_status;

-- 2. Filter berdasarkan permintaan tertentu
SELECT * FROM v_tracking_status WHERE ID_Permintaan = 7;

-- 3. Lihat permintaan yang masih di tahap tertentu
SELECT ID_Permintaan, Deskripsi_Permintaan, Tahap_Saat_Ini, Status_Permintaan
FROM v_tracking_status
WHERE Tahap_Saat_Ini = 'Disposisi';

-- 4. Lihat yang sudah selesai (sampai serah terima)
SELECT ID_Permintaan, Deskripsi_Permintaan, Durasi_Hari, Penerima
FROM v_tracking_status
WHERE Tahap_Saat_Ini = 'Serah Terima';

-- 5. Monitoring permintaan yang lama (lebih dari 30 hari)
SELECT ID_Permintaan, Deskripsi_Permintaan, Tahap_Saat_Ini, Durasi_Hari
FROM v_tracking_status
WHERE Durasi_Hari > 30
  AND Tahap_Saat_Ini != 'Serah Terima'
ORDER BY Durasi_Hari DESC;
