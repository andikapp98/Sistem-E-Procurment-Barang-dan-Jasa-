# 🧪 Panduan Testing Isolasi Data Kepala Instalasi

Panduan untuk melakukan testing fitur isolasi data kepala instalasi.

---

## 📋 Persiapan

### 1. Jalankan Migration & Seeder

```bash
# Jika fresh install
php artisan migrate:fresh

# Atau jika sudah ada data, jalankan seeder saja
php artisan db:seed --class=KepalaInstalasiDataSeeder
```

**Output yang diharapkan:**
```
✓ Kepala Instalasi: Dr. Siti Rahayu, S.Farm., Apt
✓ Staff Farmasi: Apt. Budi Santoso
✓ Permintaan 1-5 (Farmasi)
✓ Kepala IGD: Dr. Ahmad Fauzi, Sp.EM
✓ Staff IGD: Ns. Dewi Lestari, S.Kep
✓ Permintaan IGD
========================================
DATA SEEDING BERHASIL!
Total Permintaan: 6 (5 Farmasi + 1 IGD)
```

### 2. Jalankan Server

```bash
php artisan serve
```

Buka browser di `http://localhost:8000`

---

## ✅ Test Case 1: Login Kepala Instalasi Farmasi

### Langkah 1: Login

1. Akses: `http://localhost:8000/login`
2. Masukkan kredensial:
   - **Email:** `kepala_instalasi@rsud.id`
   - **Password:** `password123`
3. Klik **Login**

**Expected:** Redirect ke `/kepala-instalasi/dashboard`

---

### Langkah 2: Cek Dashboard

**URL:** `/kepala-instalasi/dashboard`

**Verifikasi:**
- ✅ Statistik menampilkan:
  - Total: **5** permintaan
  - Diajukan: **2**
  - Proses: **1**
  - Disetujui: **1**
  - Ditolak: **1**

- ✅ Recent Permintaan menampilkan **5 items** terakhir
- ✅ Semua permintaan memiliki bidang: **"Instalasi Farmasi"**

**Screenshot:** Ambil screenshot dashboard

---

### Langkah 3: Cek Daftar Permintaan

**URL:** `/kepala-instalasi`

**Verifikasi:**
- ✅ Tabel menampilkan **5 permintaan**
- ✅ Semua permintaan bidang = "Instalasi Farmasi"
- ✅ Tidak ada permintaan IGD yang muncul

**Data yang harus terlihat:**
| ID | Deskripsi | Status | Bidang |
|----|-----------|--------|--------|
| 1  | Pengadaan Obat-obatan | Diajukan | Instalasi Farmasi |
| 2  | Pengadaan Alat Kesehatan | Diajukan | Instalasi Farmasi |
| 3  | Pengadaan Vitamin | Proses | Instalasi Farmasi |
| 4  | Pengadaan Antibiotik | Disetujui | Instalasi Farmasi |
| 5  | Pengadaan Obat Generik | Ditolak | Instalasi Farmasi |

**Data yang TIDAK boleh terlihat:**
| ID | Deskripsi | Status | Bidang |
|----|-----------|--------|--------|
| 6  | Pengadaan Alat Medis IGD | Diajukan | Instalasi IGD |

---

### Langkah 4: Test Akses Detail Permintaan Farmasi

**URL:** `/kepala-instalasi/permintaan/1`

**Verifikasi:**
- ✅ Halaman detail terbuka
- ✅ Data permintaan ditampilkan lengkap
- ✅ Tombol aksi tersedia (Setujui, Tolak, dll)

---

### Langkah 5: Test Akses Detail Permintaan IGD (Forbidden)

**URL:** `/kepala-instalasi/permintaan/6`

**Verifikasi:**
- ✅ Muncul error **403 Forbidden**
- ✅ Message: "Anda tidak memiliki akses untuk melihat permintaan ini."
- ✅ Tidak bisa melihat detail permintaan

**Screenshot:** Ambil screenshot error 403

---

### Langkah 6: Test Approve Permintaan

1. Pilih permintaan dengan status "Diajukan" (ID 1 atau 2)
2. Klik tombol **"Setujui"**
3. Konfirmasi persetujuan

**Verifikasi:**
- ✅ Status berubah menjadi "Disetujui"
- ✅ Muncul notifikasi sukses
- ✅ Nota dinas otomatis dibuat

---

### Langkah 7: Test Reject Permintaan

1. Pilih permintaan dengan status "Diajukan"
2. Klik tombol **"Tolak"**
3. Masukkan alasan penolakan
4. Konfirmasi penolakan

**Verifikasi:**
- ✅ Status berubah menjadi "Ditolak"
- ✅ Alasan penolakan tersimpan
- ✅ Muncul notifikasi sukses

---

### Langkah 8: Logout

Klik **Logout** untuk melanjutkan testing dengan user lain.

---

## ✅ Test Case 2: Login Kepala Instalasi IGD

### Langkah 1: Login

1. Akses: `http://localhost:8000/login`
2. Masukkan kredensial:
   - **Email:** `kepala_igd@rsud.id`
   - **Password:** `password123`
3. Klik **Login**

**Expected:** Redirect ke `/kepala-instalasi/dashboard`

---

### Langkah 2: Cek Dashboard

**URL:** `/kepala-instalasi/dashboard`

**Verifikasi:**
- ✅ Statistik menampilkan:
  - Total: **1** permintaan
  - Diajukan: **1**
  - Proses: **0**
  - Disetujui: **0**
  - Ditolak: **0**

- ✅ Recent Permintaan menampilkan **1 item**
- ✅ Permintaan memiliki bidang: **"Instalasi IGD"**

---

### Langkah 3: Cek Daftar Permintaan

**URL:** `/kepala-instalasi`

**Verifikasi:**
- ✅ Tabel menampilkan **1 permintaan**
- ✅ Permintaan bidang = "Instalasi IGD"
- ✅ Tidak ada permintaan Farmasi yang muncul

**Data yang harus terlihat:**
| ID | Deskripsi | Status | Bidang |
|----|-----------|--------|--------|
| 6  | Pengadaan Alat Medis IGD | Diajukan | Instalasi IGD |

**Data yang TIDAK boleh terlihat:**
| ID | Deskripsi | Status | Bidang |
|----|-----------|--------|--------|
| 1-5 | Permintaan Farmasi | - | Instalasi Farmasi |

---

### Langkah 4: Test Akses Detail Permintaan IGD

**URL:** `/kepala-instalasi/permintaan/6`

**Verifikasi:**
- ✅ Halaman detail terbuka
- ✅ Data permintaan ditampilkan lengkap
- ✅ Tombol aksi tersedia

---

### Langkah 5: Test Akses Detail Permintaan Farmasi (Forbidden)

**URL:** `/kepala-instalasi/permintaan/1`

**Verifikasi:**
- ✅ Muncul error **403 Forbidden**
- ✅ Message: "Anda tidak memiliki akses untuk melihat permintaan ini."
- ✅ Tidak bisa melihat detail permintaan Farmasi

---

## ✅ Test Case 3: Cross-Access Prevention

### Test 1: Kepala Farmasi Coba Approve Permintaan IGD

1. Login sebagai Kepala Farmasi
2. Gunakan API client (Postman/Insomnia) atau browser developer tools
3. POST ke: `/kepala-instalasi/permintaan/6/approve`

**Expected Result:**
```json
{
  "message": "403 Forbidden",
  "error": "Anda tidak memiliki akses untuk menyetujui permintaan ini."
}
```

---

### Test 2: Kepala IGD Coba Reject Permintaan Farmasi

1. Login sebagai Kepala IGD
2. POST ke: `/kepala-instalasi/permintaan/1/reject`

**Expected Result:**
```json
{
  "message": "403 Forbidden",
  "error": "Anda tidak memiliki akses untuk menolak permintaan ini."
}
```

---

## 📊 Checklist Testing

### Kepala Instalasi Farmasi
- [ ] ✅ Login berhasil
- [ ] ✅ Dashboard menampilkan 5 permintaan
- [ ] ✅ Daftar permintaan hanya Farmasi
- [ ] ✅ Dapat akses detail permintaan Farmasi
- [ ] ✅ Error 403 saat akses permintaan IGD
- [ ] ✅ Dapat approve permintaan Farmasi
- [ ] ✅ Dapat reject permintaan Farmasi
- [ ] ✅ Tidak dapat akses API permintaan IGD

### Kepala Instalasi IGD
- [ ] ✅ Login berhasil
- [ ] ✅ Dashboard menampilkan 1 permintaan
- [ ] ✅ Daftar permintaan hanya IGD
- [ ] ✅ Dapat akses detail permintaan IGD
- [ ] ✅ Error 403 saat akses permintaan Farmasi
- [ ] ✅ Dapat approve permintaan IGD
- [ ] ✅ Dapat reject permintaan IGD
- [ ] ✅ Tidak dapat akses API permintaan Farmasi

### Data Isolation
- [ ] ✅ Farmasi tidak melihat data IGD
- [ ] ✅ IGD tidak melihat data Farmasi
- [ ] ✅ Direct URL access di-block (403)
- [ ] ✅ API endpoint di-block (403)

---

## 🐛 Troubleshooting

### Issue 1: Semua Permintaan Terlihat di Semua User

**Penyebab:** Filter tidak bekerja dengan benar

**Solusi:**
1. Cek field `bidang` di tabel permintaan
2. Cek field `unit_kerja` di tabel users
3. Pastikan nilai sama persis (case-sensitive)

```sql
-- Cek data
SELECT id, bidang FROM permintaan;
SELECT user_id, nama, unit_kerja FROM users WHERE role = 'kepala_instalasi';
```

---

### Issue 2: Error 403 di Semua Permintaan

**Penyebab:** Validasi terlalu ketat atau `unit_kerja` NULL

**Solusi:**
1. Cek `unit_kerja` user login
2. Pastikan tidak NULL
3. Cek apakah sesuai dengan `bidang` permintaan

```sql
-- Update unit_kerja jika NULL
UPDATE users 
SET unit_kerja = 'Instalasi Farmasi' 
WHERE email = 'kepala_instalasi@rsud.id';
```

---

### Issue 3: Permintaan IGD Terlihat di Kepala Farmasi

**Penyebab:** Field `bidang` tidak konsisten

**Solusi:**
```sql
-- Perbaiki bidang permintaan
UPDATE permintaan 
SET bidang = 'Instalasi Farmasi' 
WHERE permintaan_id IN (1,2,3,4,5);

UPDATE permintaan 
SET bidang = 'Instalasi IGD' 
WHERE permintaan_id = 6;
```

---

## 📝 Reporting Bug

Jika menemukan bug, catat:

1. **User yang login:** (Kepala Farmasi / Kepala IGD)
2. **URL yang diakses:** 
3. **Expected behavior:** 
4. **Actual behavior:** 
5. **Screenshot:** (jika ada)
6. **Error message:** (jika ada)

---

## ✅ Testing Berhasil!

Jika semua test case di atas lolos, maka implementasi isolasi data berhasil! 🎉

**Kriteria Sukses:**
- ✅ Setiap kepala instalasi hanya melihat data bagiannya
- ✅ Akses ke data bagian lain di-block dengan 403
- ✅ Filter bekerja di dashboard dan list
- ✅ Validasi bekerja di semua method (show, approve, reject, dll)

---

**Last Updated:** 17 Oktober 2025  
**Version:** 1.0.0
