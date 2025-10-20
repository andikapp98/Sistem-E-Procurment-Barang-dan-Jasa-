-- Testing Query untuk Fix Admin ke IGD
-- Verifikasi bahwa Kepala Instalasi IGD bisa melihat permintaan

-- ==================================================
-- 1. CEK DATA USERS - KEPALA INSTALASI IGD
-- ==================================================

SELECT 
    id,
    name,
    email,
    role,
    jabatan,
    unit_kerja
FROM users 
WHERE role = 'kepala_instalasi' 
  AND (unit_kerja LIKE '%IGD%' OR unit_kerja LIKE '%Gawat Darurat%')
ORDER BY id;

-- Expected: Harus ada minimal 1 user dengan role kepala_instalasi dan unit_kerja IGD


-- ==================================================
-- 2. CEK DATA PERMINTAAN - BIDANG IGD
-- ==================================================

SELECT 
    permintaan_id,
    user_id,
    bidang,
    tanggal_permintaan,
    deskripsi,
    status,
    pic_pimpinan
FROM permintaan 
WHERE bidang LIKE '%IGD%' 
   OR bidang LIKE '%Gawat Darurat%'
ORDER BY permintaan_id DESC
LIMIT 10;

-- Expected: Harus ada permintaan dengan bidang IGD atau Instalasi Gawat Darurat


-- ==================================================
-- 3. TEST MATCHING LOGIC - SCENARIO 1
-- Bidang = "Instalasi Gawat Darurat", unit_kerja = "IGD"
-- ==================================================

-- Simulasi query yang akan dijalankan oleh controller
SELECT p.*
FROM permintaan p
WHERE (
    p.bidang = 'IGD'
    OR p.bidang = 'Instalasi Gawat Darurat'
    OR p.bidang LIKE '%IGD%'
    OR p.bidang LIKE '%Instalasi Gawat Darurat%'
)
ORDER BY p.permintaan_id DESC;

-- Expected: Harus return permintaan dengan bidang "Instalasi Gawat Darurat"


-- ==================================================
-- 4. TEST MATCHING LOGIC - SCENARIO 2
-- Bidang = "IGD", unit_kerja = "IGD"
-- ==================================================

SELECT p.*
FROM permintaan p
WHERE (
    p.bidang = 'IGD'
    OR p.bidang = 'Instalasi Gawat Darurat'
    OR p.bidang LIKE '%IGD%'
    OR p.bidang LIKE '%Instalasi Gawat Darurat%'
)
ORDER BY p.permintaan_id DESC;

-- Expected: Harus return permintaan dengan bidang "IGD"


-- ==================================================
-- 5. CREATE TEST DATA - PERMINTAAN IGD
-- ==================================================

-- Insert test permintaan dengan bidang nama lengkap
INSERT INTO permintaan (
    user_id,
    bidang,
    tanggal_permintaan,
    deskripsi,
    status,
    pic_pimpinan,
    created_at,
    updated_at
) VALUES (
    1, -- Ganti dengan admin user_id yang sesuai
    'Instalasi Gawat Darurat',
    NOW(),
    'Test permintaan untuk IGD - Created by Admin',
    'diajukan',
    'Kepala Instalasi',
    NOW(),
    NOW()
);

-- Insert test permintaan dengan bidang abbreviasi
INSERT INTO permintaan (
    user_id,
    bidang,
    tanggal_permintaan,
    deskripsi,
    status,
    pic_pimpinan,
    created_at,
    updated_at
) VALUES (
    1, -- Ganti dengan admin user_id yang sesuai
    'IGD',
    NOW(),
    'Test permintaan untuk IGD (abbreviasi) - Created by Admin',
    'diajukan',
    'Kepala Instalasi',
    NOW(),
    NOW()
);


-- ==================================================
-- 6. VERIFY TEST DATA
-- ==================================================

-- Cek apakah test data berhasil dibuat
SELECT 
    permintaan_id,
    bidang,
    deskripsi,
    status,
    created_at
FROM permintaan 
WHERE deskripsi LIKE '%Test permintaan untuk IGD%'
ORDER BY permintaan_id DESC;


-- ==================================================
-- 7. TEST QUERY DENGAN JOIN USER
-- Simulasi query lengkap di controller
-- ==================================================

SELECT 
    p.permintaan_id,
    p.bidang,
    p.deskripsi,
    p.status,
    p.tanggal_permintaan,
    u.name as creator_name,
    u.email as creator_email
FROM permintaan p
LEFT JOIN users u ON p.user_id = u.id
WHERE (
    p.bidang = 'IGD'
    OR p.bidang = 'Instalasi Gawat Darurat'
    OR p.bidang LIKE '%IGD%'
    OR p.bidang LIKE '%Instalasi Gawat Darurat%'
)
ORDER BY p.permintaan_id DESC
LIMIT 20;


-- ==================================================
-- 8. CHECK ALL UNITS - COMPREHENSIVE TEST
-- ==================================================

-- Cek semua kombinasi unit_kerja dan bidang
SELECT 
    u.id as user_id,
    u.name,
    u.unit_kerja,
    COUNT(p.permintaan_id) as total_permintaan
FROM users u
LEFT JOIN permintaan p ON (
    p.bidang = u.unit_kerja
    OR p.bidang LIKE CONCAT('%', u.unit_kerja, '%')
)
WHERE u.role = 'kepala_instalasi'
GROUP BY u.id, u.name, u.unit_kerja
ORDER BY total_permintaan DESC;


-- ==================================================
-- 9. FIX DATA - UPDATE INCONSISTENT BIDANG
-- ==================================================

-- Jika ada bidang yang tidak konsisten, update ke format standard
-- HATI-HATI: Backup dulu sebelum run query ini!

-- Update abbreviasi ke nama lengkap (optional, jika mau standardisasi)
UPDATE permintaan 
SET bidang = 'Instalasi Gawat Darurat'
WHERE bidang IN ('IGD', 'igd', 'Igd');

-- Atau sebaliknya, standardisasi ke abbreviasi (optional)
-- UPDATE permintaan 
-- SET bidang = 'IGD'
-- WHERE bidang LIKE '%Gawat Darurat%';


-- ==================================================
-- 10. CLEAN UP TEST DATA
-- ==================================================

-- Hapus test data yang dibuat (jika perlu)
-- DELETE FROM permintaan 
-- WHERE deskripsi LIKE '%Test permintaan untuk IGD%';


-- ==================================================
-- 11. MONITORING QUERY - REAL-TIME CHECK
-- ==================================================

-- Query untuk monitoring apakah permintaan masuk ke dashboard
SELECT 
    p.permintaan_id,
    p.bidang,
    p.status,
    p.deskripsi,
    DATE_FORMAT(p.tanggal_permintaan, '%Y-%m-%d %H:%i') as tanggal,
    CASE 
        WHEN p.bidang = 'IGD' THEN 'Exact Match (IGD)'
        WHEN p.bidang = 'Instalasi Gawat Darurat' THEN 'Exact Match (Full Name)'
        WHEN p.bidang LIKE '%IGD%' THEN 'Partial Match (IGD)'
        WHEN p.bidang LIKE '%Gawat Darurat%' THEN 'Partial Match (Gawat Darurat)'
        ELSE 'No Match'
    END as match_type
FROM permintaan p
WHERE (
    p.bidang = 'IGD'
    OR p.bidang = 'Instalasi Gawat Darurat'
    OR p.bidang LIKE '%IGD%'
    OR p.bidang LIKE '%Gawat Darurat%'
)
ORDER BY p.permintaan_id DESC;


-- ==================================================
-- 12. PERFORMANCE CHECK
-- ==================================================

-- Cek performa query dengan EXPLAIN
EXPLAIN SELECT p.*
FROM permintaan p
WHERE (
    p.bidang = 'IGD'
    OR p.bidang = 'Instalasi Gawat Darurat'
    OR p.bidang LIKE '%IGD%'
    OR p.bidang LIKE '%Instalasi Gawat Darurat%'
)
ORDER BY p.permintaan_id DESC;

-- Note: Jika slow, consider adding index on bidang column:
-- CREATE INDEX idx_permintaan_bidang ON permintaan(bidang);


-- ==================================================
-- 13. SUMMARY STATISTICS
-- ==================================================

-- Statistik permintaan per bidang
SELECT 
    bidang,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'diajukan' THEN 1 ELSE 0 END) as diajukan,
    SUM(CASE WHEN status = 'proses' THEN 1 ELSE 0 END) as proses,
    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
FROM permintaan
GROUP BY bidang
ORDER BY total DESC;
