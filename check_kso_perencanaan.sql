-- Check workflow chain for permintaan 17
SELECT 
    p.permintaan_id,
    p.deskripsi,
    p.pic_pimpinan,
    p.status,
    nd.nota_id,
    nd.no_nota,
    nd.tanggal_nota,
    d.disposisi_id,
    d.tanggal_disposisi,
    pr.perencanaan_id,
    pr.nama_paket,
    k.kso_id,
    k.no_kso
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
LEFT JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
LEFT JOIN kso k ON pr.perencanaan_id = k.perencanaan_id
WHERE p.permintaan_id = 17;

-- Check if there's any nota_dinas
SELECT COUNT(*) as nota_count FROM nota_dinas WHERE permintaan_id = 17;

-- Check if there's any disposisi
SELECT COUNT(*) as disposisi_count 
FROM disposisi d
JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = 17;

-- Check if there's any perencanaan
SELECT COUNT(*) as perencanaan_count
FROM perencanaan pr
JOIN disposisi d ON pr.disposisi_id = d.disposisi_id
JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = 17;
