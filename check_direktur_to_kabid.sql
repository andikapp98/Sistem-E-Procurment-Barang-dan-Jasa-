SELECT 
    p.permintaan_id,
    p.klasifikasi_permintaan,
    p.kabid_tujuan,
    p.pic_pimpinan,
    p.status,
    d.jabatan_tujuan,
    d.catatan,
    d.status as disposisi_status
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE d.catatan LIKE '%Disetujui oleh Direktur%'
ORDER BY p.permintaan_id DESC
LIMIT 5;
