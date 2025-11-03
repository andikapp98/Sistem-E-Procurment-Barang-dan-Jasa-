-- Query: Cek klasifikasi permintaan dan routing Direktur
SELECT 
    p.permintaan_id,
    p.klasifikasi_permintaan,
    p.kabid_tujuan,
    p.pic_pimpinan,
    p.status,
    d.disposisi_id,
    d.jabatan_tujuan,
    LEFT(d.catatan, 80) as catatan_singkat
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE p.klasifikasi_permintaan IN ('Penunjang', 'penunjang_medis', 'penunjang medis')
  OR p.kabid_tujuan LIKE '%Penunjang%'
ORDER BY p.permintaan_id DESC
LIMIT 10;
