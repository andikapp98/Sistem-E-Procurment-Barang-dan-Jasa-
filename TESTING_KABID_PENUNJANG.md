# PANDUAN FIX: Routing Kabid Penunjang Medis

## Status

✅ Data testing sudah dibuat
✅ Permintaan #21 & #22 (Penunjang) sudah ada
✅ Query sudah benar
⏳ Menunggu Direktur approve

## Data Testing

### Permintaan #21 - Reagen Kimia Klinik
- **Dari:** Kepala Instalasi Laboratorium
- **Item:** Reagen Kimia Klinik (10 set @ Rp 15jt)
- **Klasifikasi:** Penunjang
- **Kabid Tujuan:** Bidang Penunjang Medis
- **Status:** Menunggu review Direktur

### Permintaan #22 - Film Radiologi
- **Dari:** Kepala Instalasi Radiologi
- **Item:** Film Radiologi & Chemicals (Rp 25jt)
- **Klasifikasi:** Penunjang
- **Kabid Tujuan:** Bidang Penunjang Medis
- **Status:** Menunggu review Direktur

## Testing Steps

### 1. Login sebagai Direktur

```
URL      : http://localhost:8000/login
Email    : direktur@rsud.id
Password : password
```

**Actions:**
1. Buka dashboard
2. Cari permintaan #21 atau #22 (dari Lab/Radiologi)
3. Klik "Lihat Detail"
4. Klik tombol **"Setujui (Final)"**
5. Isi catatan (optional): "Disetujui untuk proses pengadaan"
6. Submit

**Expected Result:**
- ✅ Routing otomatis ke: **Bidang Penunjang Medis**
- ✅ Success message: "...diteruskan ke Bidang Penunjang Medis..."
- ✅ Permintaan hilang dari dashboard Direktur
- ✅ Disposisi created dengan:
  - `jabatan_tujuan` = "Bidang Penunjang Medis"
  - `catatan` = "Disetujui oleh Direktur (Final Approval)..."

### 2. Login sebagai Kabid Penunjang

```
URL      : http://localhost:8000/login
Email    : kabid.penunjang@rsud.id
Password : password
```

**Expected:**
- ✅ Dashboard menampilkan permintaan #21 atau #22
- ✅ Badge/status menunjukkan "Dari Direktur"
- ✅ Ada disposisi dari Direktur

**Actions:**
1. Buka dashboard - **permintaan HARUS muncul**
2. Klik "Lihat Detail" atau "Review"
3. Baca disposisi dari Direktur
4. Klik tombol **"Setujui"**
5. Isi catatan: "Sudah disetujui Direktur, diteruskan ke perencanaan"
6. Submit

**Expected Result:**
- ✅ Permintaan diteruskan ke **Staff Perencanaan**
- ✅ Success message: "...diteruskan ke Staff Perencanaan..."
- ✅ Permintaan hilang dari dashboard Kabid Penunjang

### 3. Login sebagai Staff Perencanaan

```
URL      : http://localhost:8000/login
Email    : perencanaan@rsud.id
Password : password
```

**Expected:**
- ✅ Dashboard menampilkan permintaan #21 atau #22
- ✅ Status: pic_pimpinan = "Staff Perencanaan"
- ✅ Status: status = "disetujui"
- ✅ Ada disposisi dari Kabid Penunjang

## Verification

### Query Database

```sql
-- 1. Cek disposisi dari Direktur ke Penunjang
SELECT 
    p.permintaan_id,
    p.klasifikasi_permintaan,
    p.kabid_tujuan,
    d.jabatan_tujuan,
    d.catatan,
    d.tanggal_disposisi
FROM permintaan p
JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE p.permintaan_id IN (21, 22)
  AND d.catatan LIKE '%Disetujui oleh Direktur%'
ORDER BY p.permintaan_id;

-- Expected:
-- permintaan_id | klasifikasi | kabid_tujuan              | jabatan_tujuan
-- 21            | Penunjang   | Bidang Penunjang Medis    | Bidang Penunjang Medis
-- 22            | Penunjang   | Bidang Penunjang Medis    | Bidang Penunjang Medis


-- 2. Cek workflow lengkap untuk permintaan #21
SELECT 
    d.disposisi_id,
    d.jabatan_tujuan,
    d.tanggal_disposisi,
    LEFT(d.catatan, 60) as catatan_singkat,
    d.status
FROM permintaan p
JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE p.permintaan_id = 21
ORDER BY d.disposisi_id;

-- Expected workflow:
-- 1. Kepala Lab → Kabid Penunjang
-- 2. Kabid Penunjang → Direktur
-- 3. Direktur → Kabid Penunjang (routing balik)
-- 4. Kabid Penunjang → Staff Perencanaan
```

### Test Script

```bash
# Before Direktur approve
php test_penunjang_routing.php

# Expected:
# - 2 permintaan dengan klasifikasi Penunjang
# - Tidak ada disposisi dari Direktur
# - Total query Kabid: 0 permintaan


# After Direktur approve
php test_penunjang_routing.php

# Expected:
# - 2 permintaan dengan klasifikasi Penunjang
# - ✅ Ada disposisi dari Direktur ke Penunjang
# - ✅ Total query Kabid: 1-2 permintaan (DARI DIREKTUR)
```

## Troubleshooting

### Issue: Kabid Penunjang tidak melihat permintaan setelah Direktur approve

**Cek:**
```bash
# 1. Clear cache
php artisan cache:clear
php artisan config:clear

# 2. Hard refresh browser
# Ctrl+Shift+R atau Ctrl+F5

# 3. Test query
php test_penunjang_routing.php
```

**Cek Database:**
```sql
SELECT 
    p.permintaan_id,
    p.klasifikasi_permintaan,
    p.kabid_tujuan,
    p.pic_pimpinan,
    COUNT(d.disposisi_id) as jumlah_disposisi
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id 
    AND d.jabatan_tujuan LIKE '%Penunjang%'
    AND d.catatan LIKE '%Disetujui oleh Direktur%'
WHERE p.permintaan_id IN (21, 22)
GROUP BY p.permintaan_id, p.klasifikasi_permintaan, p.kabid_tujuan, p.pic_pimpinan;
```

### Issue: Routing tidak ke Penunjang Medis

**Cek Mapping Direktur:**
- File: `app/Http/Controllers/DirekturController.php`
- Method: `getKabidTujuanByKlasifikasi()`

```php
'Penunjang' => 'Bidang Penunjang Medis',
'penunjang_medis' => 'Bidang Penunjang Medis',
```

**Cek Mapping Kabid:**
- File: `app/Http/Controllers/KepalaBidangController.php`
- Method: `getKlasifikasiByUnitKerja()`

```php
'Bidang Penunjang Medis' => ['Penunjang', 'penunjang_medis'],
```

## Quick Commands

```bash
# Create test data
php artisan db:seed --class=PenunjangMedisTestSeeder

# Test routing
php test_penunjang_routing.php

# Clear cache
php artisan cache:clear && php artisan config:clear

# Check database
mysql -u root -p pengadaan_db
```

## Related Files

- `database/seeders/PenunjangMedisTestSeeder.php` - Seeder data testing
- `test_penunjang_routing.php` - Test script
- `app/Http/Controllers/DirekturController.php` - Routing logic
- `app/Http/Controllers/KepalaBidangController.php` - Query Kabid

---

**Created:** 3 November 2025  
**Status:** ✅ Data testing ready, waiting for Direktur approval  
**Next:** Login Direktur → Approve permintaan #21 atau #22
