# PANDUAN TESTING: Direktur → Kabid → Staff Perencanaan

## Quick Start

### 1. Pastikan Data Testing Ada

```bash
# Jalankan test script
php test_kabid_disposisi_direktur.php
```

**Expected Output:**
```
✅ Ada data testing dari Direktur
👤 KABID: kabid.yanmed@rsud.id
   Total: 1 permintaan
   ✓ [🔄 DARI DIREKTUR] Permintaan #18
```

Jika tidak ada data, jalankan:
```bash
php artisan db:seed --class=DirekturWorkflowSeeder
```

### 2. Login sebagai Direktur

```
URL      : http://localhost:8000/login
Email    : direktur@rsud.id
Password : password
```

**Actions:**
1. Buka dashboard
2. Klik permintaan yang menunggu review
3. Klik tombol **"Setujui (Final)"**
4. Isi catatan (optional): "Disetujui untuk pengadaan"
5. Submit
6. ✅ Permintaan diteruskan ke Kabid sesuai klasifikasi
7. Logout

### 3. Login sebagai Kabid Yanmed

```
URL      : http://localhost:8000/login
Email    : kabid.yanmed@rsud.id
Password : password
```

**Expected:**
- ✅ Dashboard menampilkan permintaan dengan badge "Dari Direktur" atau status khusus
- ✅ Index/List menampilkan permintaan yang sudah di-approve Direktur
- ✅ Permintaan memiliki disposisi dari Direktur

**Actions:**
1. Buka dashboard - **permintaan HARUS muncul**
2. Atau buka index/list - **permintaan HARUS ada di list**
3. Klik "Lihat Detail" atau "Review"
4. Baca disposisi dari Direktur
5. Klik tombol **"Setujui"**
6. Isi catatan: "Sudah disetujui Direktur, diteruskan ke perencanaan"
7. Submit
8. ✅ Permintaan diteruskan ke Staff Perencanaan
9. Logout

### 4. Login sebagai Staff Perencanaan

```
URL      : http://localhost:8000/login
Email    : perencanaan@rsud.id
Password : password
```

**Expected:**
- ✅ Dashboard menampilkan permintaan baru
- ✅ Status: pic_pimpinan = "Staff Perencanaan"
- ✅ Status: status = "disetujui"
- ✅ Ada disposisi dari Kabid

**Actions:**
1. Buka dashboard - **permintaan HARUS muncul**
2. Klik "Lihat Detail"
3. Cek workflow/timeline - harus ada:
   - Disposisi dari Kepala Instalasi
   - Disposisi dari Kabid ke Direktur
   - Disposisi dari Direktur ke Kabid
   - Disposisi dari Kabid ke Staff Perencanaan
4. Mulai proses perencanaan pengadaan

## Troubleshooting

### Issue 1: Kabid tidak melihat permintaan setelah Direktur approve

**Cek:**
```bash
# 1. Test script
php test_kabid_disposisi_direktur.php

# 2. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# 3. Hard refresh browser
# Tekan Ctrl+Shift+R atau Ctrl+F5
```

**Cek Database:**
```sql
SELECT 
    p.permintaan_id,
    p.klasifikasi_permintaan,
    p.kabid_tujuan,
    p.pic_pimpinan,
    d.jabatan_tujuan,
    d.catatan
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE d.catatan LIKE '%Disetujui oleh Direktur%'
  AND p.status = 'proses';
```

**Expected:**
- permintaan_id: 18
- klasifikasi_permintaan: Medis
- kabid_tujuan: Bidang Pelayanan Medis
- pic_pimpinan: Kepala Bidang
- jabatan_tujuan: Bidang Pelayanan Medis
- catatan: Disetujui oleh Direktur...

### Issue 2: Kabid tidak bisa kirim ke Staff Perencanaan

**Cek:**
```bash
# 1. Clear cache
php artisan cache:clear

# 2. Cek log error
tail -f storage/logs/laravel.log
```

**Cek Method Approve:**
- Masuk detail permintaan
- Buka browser console (F12)
- Klik "Setujui"
- Cek response di Network tab

### Issue 3: Staff Perencanaan tidak menerima

**Cek Database:**
```sql
SELECT 
    p.permintaan_id,
    p.pic_pimpinan,
    p.status,
    d.jabatan_tujuan,
    d.tanggal_disposisi,
    d.catatan
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE p.permintaan_id = 18
ORDER BY d.disposisi_id DESC
LIMIT 1;
```

**Expected:**
- pic_pimpinan: Staff Perencanaan
- status: disetujui
- jabatan_tujuan: Staff Perencanaan
- catatan: Sudah disetujui Direktur...

## Verification Checklist

### ✅ Direktur
- [ ] Dashboard menampilkan permintaan menunggu review
- [ ] Bisa approve dengan routing otomatis ke Kabid
- [ ] Success message: "...diteruskan ke [Kabid]..."
- [ ] Setelah approve, permintaan hilang dari dashboard Direktur

### ✅ Kabid
- [ ] Dashboard menampilkan permintaan dari Direktur
- [ ] Index menampilkan permintaan dari Direktur
- [ ] Detail permintaan bisa dibuka
- [ ] Ada disposisi dari Direktur
- [ ] Bisa approve dan kirim ke Staff Perencanaan
- [ ] Success message: "...diteruskan ke Staff Perencanaan..."
- [ ] Setelah approve, permintaan hilang dari dashboard Kabid

### ✅ Staff Perencanaan
- [ ] Dashboard menampilkan permintaan baru
- [ ] Status = "Staff Perencanaan"
- [ ] Status permintaan = "disetujui"
- [ ] Ada disposisi dari Kabid
- [ ] Timeline/Tracking lengkap (semua disposisi)
- [ ] Bisa mulai proses perencanaan

## Test Data

### Existing Test Data (Permintaan #18)

```
Permintaan ID: 18
Dari         : Kepala Instalasi Laboratorium
Klasifikasi  : Medis
Item         : Chemistry Analyzer Otomatis
Kabid        : Bidang Pelayanan Medis (Kabid Yanmed)
Status       : Menunggu approve dari Kabid (setelah Direktur approve)
```

**Login Credentials:**
- Direktur: direktur@rsud.id / password
- Kabid Yanmed: kabid.yanmed@rsud.id / password
- Staff Perencanaan: perencanaan@rsud.id / password

### Create New Test Data

```bash
# Buat 3 permintaan baru dengan workflow lengkap
php artisan db:seed --class=DirekturWorkflowSeeder
```

## Quick Commands

```bash
# Test query
php test_kabid_disposisi_direktur.php

# Clear cache
php artisan cache:clear && php artisan config:clear && php artisan route:clear

# Create test data
php artisan db:seed --class=DirekturWorkflowSeeder

# Check logs
tail -f storage/logs/laravel.log
```

---

**Last Updated:** 3 November 2025
**Status:** ✅ Ready for Testing
**Contact:** Admin if issues persist
