-- =====================================================================
-- TROUBLESHOOTING QUERIES - Kepala Bidang Tidak Melihat Permintaan
-- =====================================================================

-- 1. CEK PERMINTAAN TERBARU DAN STATUSNYA
-- Gunakan untuk verifikasi data permintaan
SELECT 
    permintaan_id,
    bidang,
    status,
    pic_pimpinan,
    tanggal_permintaan,
    LEFT(deskripsi, 50) AS deskripsi_short
FROM permintaan
ORDER BY permintaan_id DESC
LIMIT 10;

-- 2. CEK NOTA DINAS UNTUK PERMINTAAN TERTENTU
-- Replace 123 dengan ID permintaan yang ingin dicek
SELECT 
    nd.nota_id,
    nd.permintaan_id,
    nd.no_nota,
    nd.dari,
    nd.kepada,
    nd.tanggal_nota,
    nd.perihal
FROM nota_dinas nd
WHERE nd.permintaan_id = 123
ORDER BY nd.nota_id DESC;

-- 3. CEK DISPOSISI UNTUK PERMINTAAN TERTENTU
-- Replace 123 dengan ID permintaan
SELECT 
    d.disposisi_id,
    d.nota_id,
    d.jabatan_tujuan,
    d.tanggal_disposisi,
    d.catatan,
    d.status,
    nd.permintaan_id
FROM disposisi d
INNER JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = 123
ORDER BY d.disposisi_id DESC;

-- 4. CEK SEMUA PERMINTAAN YANG SEHARUSNYA MUNCUL DI KEPALA BIDANG
-- Query ini meniru logic controller KepalaBidangController
SELECT DISTINCT
    p.permintaan_id,
    p.deskripsi,
    p.status,
    p.pic_pimpinan,
    p.bidang,
    nd.kepada AS nota_kepada,
    d.jabatan_tujuan,
    d.status AS disposisi_status
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE p.status IN ('proses', 'disetujui')
AND (
    p.pic_pimpinan = 'Kepala Bidang'
    OR d.jabatan_tujuan = 'Kepala Bidang'
)
ORDER BY p.permintaan_id DESC
LIMIT 20;

-- 5. CEK DISPOSISI YANG PENDING KE KEPALA BIDANG
-- Semua disposisi yang masih pending untuk Kepala Bidang
SELECT 
    d.disposisi_id,
    d.nota_id,
    nd.permintaan_id,
    p.deskripsi,
    p.status AS permintaan_status,
    d.jabatan_tujuan,
    d.status AS disposisi_status,
    d.tanggal_disposisi
FROM disposisi d
INNER JOIN nota_dinas nd ON d.nota_id = nd.nota_id
INNER JOIN permintaan p ON nd.permintaan_id = p.permintaan_id
WHERE d.jabatan_tujuan = 'Kepala Bidang'
AND d.status = 'pending'
ORDER BY d.disposisi_id DESC;

-- 6. DEBUG QUERY - FULL JOIN UNTUK MELIHAT SEMUA RELASI
-- Untuk debugging lengkap satu permintaan
-- Replace 123 dengan ID permintaan
SELECT 
    p.permintaan_id,
    p.status AS p_status,
    p.pic_pimpinan,
    p.bidang,
    nd.nota_id,
    nd.kepada AS nd_kepada,
    nd.dari AS nd_dari,
    d.disposisi_id,
    d.jabatan_tujuan AS d_jabatan_tujuan,
    d.status AS d_status
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE p.permintaan_id = 123;

-- 7. CEK APAKAH AUTO-CREATE DISPOSISI BERJALAN
-- Cek permintaan yang punya nota dinas tapi tidak punya disposisi
SELECT 
    p.permintaan_id,
    p.status,
    p.pic_pimpinan,
    nd.nota_id,
    nd.kepada,
    COUNT(d.disposisi_id) AS jumlah_disposisi
FROM permintaan p
INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE p.status = 'proses'
AND nd.kepada = 'Kepala Bidang'
GROUP BY p.permintaan_id, p.status, p.pic_pimpinan, nd.nota_id, nd.kepada
HAVING jumlah_disposisi = 0;

-- Jika query ini return data, berarti ada permintaan yang tidak punya disposisi!

-- 8. STATS DISPOSISI
-- Lihat distribusi disposisi berdasarkan jabatan dan status
SELECT 
    jabatan_tujuan,
    status,
    COUNT(*) AS jumlah
FROM disposisi
GROUP BY jabatan_tujuan, status
ORDER BY jabatan_tujuan, status;

-- 9. CEK USER KEPALA BIDANG
-- Pastikan user dengan role Kepala Bidang ada
SELECT 
    id,
    nama,
    email,
    role,
    jabatan,
    unit_kerja
FROM users
WHERE role LIKE '%Kepala Bidang%'
OR jabatan LIKE '%Kepala Bidang%';

-- 10. VERIFIKASI DATA INTEGRITY
-- Cek apakah ada nota_dinas tanpa permintaan (orphaned)
SELECT nd.*
FROM nota_dinas nd
LEFT JOIN permintaan p ON nd.permintaan_id = p.permintaan_id
WHERE p.permintaan_id IS NULL;

-- 11. VERIFIKASI DISPOSISI INTEGRITY
-- Cek apakah ada disposisi tanpa nota_dinas (orphaned)
SELECT d.*
FROM disposisi d
LEFT JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.nota_id IS NULL;

-- =====================================================================
-- DIAGNOSTIC PROCEDURE
-- =====================================================================
-- Step 1: Run Query #4 untuk lihat permintaan yang seharusnya muncul
-- Step 2: Jika tidak ada data, run Query #7 untuk cek disposisi missing
-- Step 3: Run Query #5 untuk lihat semua disposisi pending ke Kepala Bidang
-- Step 4: Jika masih bermasalah, run Query #6 untuk debug detail
-- =====================================================================

-- =====================================================================
-- FIX QUERIES (Jika ada data rusak)
-- =====================================================================

-- Fix 1: Jika ada permintaan yang seharusnya punya disposisi tapi tidak ada
-- Manual create disposisi untuk permintaan tertentu
-- Replace 123 dengan permintaan_id, dan XYZ dengan nota_id dari query #2
INSERT INTO disposisi (
    nota_id, 
    jabatan_tujuan, 
    tanggal_disposisi, 
    catatan, 
    status,
    created_at,
    updated_at
) VALUES (
    XYZ,  -- nota_id dari nota_dinas
    'Kepala Bidang',
    NOW(),
    'Manual fix - disposisi dibuat manual',
    'pending',
    NOW(),
    NOW()
);

-- Fix 2: Update pic_pimpinan jika salah
-- UPDATE permintaan 
-- SET pic_pimpinan = 'Kepala Bidang'
-- WHERE permintaan_id = 123;

-- Fix 3: Update status disposisi jika застрял
-- UPDATE disposisi 
-- SET status = 'pending'
-- WHERE disposisi_id = XYZ;
