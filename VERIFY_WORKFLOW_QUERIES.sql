-- SQL Query untuk Verify Workflow Fix
-- Cek apakah Kepala Bidang approve langsung ke Direktur

-- ========================================
-- 1. CHECK LATEST DISPOSISI AFTER KABID APPROVE
-- ========================================

-- Setelah Kepala Bidang approve, disposisi terakhir harus ke Direktur
SELECT 
    p.permintaan_id,
    p.deskripsi,
    p.status,
    p.pic_pimpinan,
    d.disposisi_id,
    d.jabatan_tujuan,
    d.catatan,
    d.tanggal_disposisi
FROM permintaan p
JOIN nota_dinas n ON p.permintaan_id = n.permintaan_id
JOIN disposisi d ON n.nota_id = d.nota_id
WHERE p.pic_pimpinan = 'Direktur'
ORDER BY p.permintaan_id DESC, d.tanggal_disposisi DESC
LIMIT 10;

-- Expected result:
-- jabatan_tujuan should be 'Direktur' (NOT 'Wakil Direktur') ✅


-- ========================================
-- 2. COUNT PERMINTAAN BY PIC_PIMPINAN
-- ========================================

-- Verify distribution of permintaan
SELECT 
    pic_pimpinan,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'proses' THEN 1 ELSE 0 END) as proses,
    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as disetujui
FROM permintaan
WHERE status IN ('proses', 'disetujui')
GROUP BY pic_pimpinan
ORDER BY total DESC;

-- Should see:
-- Direktur: X items ✅
-- Wakil Direktur: 0 or very few (old data only) ✅


-- ========================================
-- 3. TRACE FULL WORKFLOW FOR SPECIFIC PERMINTAAN
-- ========================================

-- Replace <ID> with actual permintaan_id
SELECT 
    d.disposisi_id,
    d.jabatan_tujuan,
    d.catatan,
    d.status,
    d.tanggal_disposisi,
    n.dari,
    n.kepada,
    n.perihal
FROM disposisi d
JOIN nota_dinas n ON d.nota_id = n.nota_id
WHERE n.permintaan_id = <ID>
ORDER BY d.tanggal_disposisi ASC;

-- Expected workflow:
-- 1. Kepala Instalasi → Kepala Bidang ✅
-- 2. Kepala Bidang → Direktur ✅ (should NOT go to Wakil Direktur)
-- 3. Direktur → Staff Perencanaan (after final approve)


-- ========================================
-- 4. FIND PERMINTAAN STILL GOING TO WADIR (OLD WORKFLOW)
-- ========================================

-- This should return EMPTY or only old records
SELECT 
    p.permintaan_id,
    p.deskripsi,
    p.created_at,
    p.pic_pimpinan,
    d.jabatan_tujuan,
    d.tanggal_disposisi
FROM permintaan p
JOIN nota_dinas n ON p.permintaan_id = n.permintaan_id
JOIN disposisi d ON n.nota_id = d.nota_id
WHERE d.jabatan_tujuan = 'Wakil Direktur'
  AND p.pic_pimpinan = 'Wakil Direktur'
  AND p.created_at > NOW() - INTERVAL 1 DAY  -- Only recent (after fix)
ORDER BY p.permintaan_id DESC;

-- Expected: EMPTY RESULT ✅ (no new permintaan should go to Wakil Direktur)


-- ========================================
-- 5. VERIFY DIREKTUR DASHBOARD WILL SHOW CORRECT DATA
-- ========================================

-- Simulate Direktur dashboard query
SELECT 
    p.permintaan_id,
    p.deskripsi,
    p.status,
    p.pic_pimpinan,
    p.bidang,
    p.tanggal_permintaan,
    u.nama as pemohon
FROM permintaan p
LEFT JOIN users u ON p.user_id = u.id
WHERE (p.pic_pimpinan = 'Direktur' OR p.pic_pimpinan = 'John Doe')  -- Replace with actual Direktur name
  AND p.status IN ('proses', 'disetujui')
ORDER BY p.permintaan_id DESC
LIMIT 20;

-- Should return permintaan that were approved by Kepala Bidang ✅


-- ========================================
-- 6. CHECK CATATAN IN DISPOSISI
-- ========================================

-- Verify disposisi catatan mentions correct target
SELECT 
    p.permintaan_id,
    d.jabatan_tujuan,
    d.catatan,
    d.tanggal_disposisi
FROM disposisi d
JOIN nota_dinas n ON d.nota_id = n.nota_id
JOIN permintaan p ON n.permintaan_id = p.permintaan_id
WHERE d.catatan LIKE '%Kepala Bidang%'
  AND d.tanggal_disposisi > NOW() - INTERVAL 1 DAY
ORDER BY d.tanggal_disposisi DESC
LIMIT 10;

-- Catatan should say: "diteruskan ke Direktur" (NOT "ke Wakil Direktur") ✅


-- ========================================
-- 7. UPDATE OLD DATA (OPTIONAL - IF NEEDED)
-- ========================================

-- ⚠️ CAUTION: Backup database before running this!
-- This updates old permintaan that are stuck at Wakil Direktur

-- Update permintaan pic_pimpinan
-- UPDATE permintaan 
-- SET pic_pimpinan = 'Direktur'
-- WHERE pic_pimpinan = 'Wakil Direktur'
--   AND status = 'proses';

-- Update disposisi jabatan_tujuan
-- UPDATE disposisi d
-- JOIN nota_dinas n ON d.nota_id = n.nota_id
-- JOIN permintaan p ON n.permintaan_id = p.permintaan_id
-- SET d.jabatan_tujuan = 'Direktur'
-- WHERE d.jabatan_tujuan = 'Wakil Direktur'
--   AND p.status = 'proses'
--   AND d.status = 'pending';


-- ========================================
-- 8. MONITORING QUERY - DAILY CHECK
-- ========================================

-- Run this daily to ensure workflow is correct
SELECT 
    DATE(p.created_at) as tanggal,
    COUNT(DISTINCT CASE WHEN p.pic_pimpinan = 'Direktur' THEN p.permintaan_id END) as ke_direktur,
    COUNT(DISTINCT CASE WHEN p.pic_pimpinan = 'Wakil Direktur' THEN p.permintaan_id END) as ke_wadir,
    COUNT(DISTINCT CASE WHEN p.pic_pimpinan = 'Kepala Bidang' THEN p.permintaan_id END) as ke_kabid
FROM permintaan p
WHERE p.created_at > NOW() - INTERVAL 7 DAY
  AND p.status IN ('proses', 'disetujui')
GROUP BY DATE(p.created_at)
ORDER BY tanggal DESC;

-- After fix:
-- ke_direktur: Should increase ✅
-- ke_wadir: Should be 0 for new data ✅


-- ========================================
-- 9. PERFORMANCE CHECK
-- ========================================

-- Check average processing time (before/after fix)
SELECT 
    AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as avg_days_to_complete
FROM permintaan
WHERE status = 'disetujui'
  AND created_at > NOW() - INTERVAL 30 DAY
GROUP BY DATE_FORMAT(created_at, '%Y-%m');

-- Should be faster after eliminating Wakil Direktur step ✅
