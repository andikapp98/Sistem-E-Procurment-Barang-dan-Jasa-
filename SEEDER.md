# 📊 Data Demo & Seeder

Panduan untuk menggunakan data demo yang tersedia dalam aplikasi.

---

## 📋 Daftar Isi

- [Data Seeder yang Tersedia](#data-seeder-yang-tersedia)
- [Cara Menjalankan Seeder](#cara-menjalankan-seeder)
- [Akun Default](#akun-default)
- [Data Permintaan Demo](#data-permintaan-demo)

---

## 🗂️ Data Seeder yang Tersedia

### 1. KepalaInstalasiDataSeeder

Seeder ini membuat data lengkap untuk testing fitur Kepala Instalasi.

**Yang Dibuat:**
- 1 User Kepala Instalasi (Instalasi Farmasi)
- 1 User Staff Farmasi (Unit)
- 5 Permintaan dengan berbagai status
- 3 Nota Dinas

**File:** `database/seeders/KepalaInstalasiDataSeeder.php`

---

## 🚀 Cara Menjalankan Seeder

### Jalankan Seeder Tertentu

```bash
php artisan db:seed --class=KepalaInstalasiDataSeeder
```

### Jalankan Semua Seeder

```bash
php artisan db:seed
```

### Reset Database + Seed

```bash
php artisan migrate:fresh --seed
```

---

## 👥 Akun Default

Setelah menjalankan `KepalaInstalasiDataSeeder`:

### Kepala Instalasi Farmasi
```
Email:    kepala_instalasi@rsud.id
Password: password123
Role:     kepala_instalasi
Unit:     Instalasi Farmasi
```

### Staff Farmasi
```
Email:    staff.farmasi@rsud.id
Password: password123
Role:     unit
Unit:     Instalasi Farmasi
```

---

## 📝 Data Permintaan Demo

### Permintaan #1 - Status: Diajukan
**Deskripsi:** Pengadaan Obat-obatan
- Paracetamol 500mg - 10.000 tablet
- Amoxicillin 500mg - 5.000 kapsul
- OBH Sirup 100ml - 500 botol
- Betadine Solution 100ml - 200 botol

**Pemohon:** Staff Farmasi  
**Status:** Menunggu review dari Kepala Instalasi

---

### Permintaan #2 - Status: Diajukan
**Deskripsi:** Pengadaan Alat Kesehatan
- Spuit 3cc disposable - 2.000 pcs
- Spuit 5cc disposable - 1.500 pcs
- Masker medis 3 ply - 5.000 pcs
- Handscoon latex size M - 1.000 box
- Alkohol 70% 1 liter - 100 botol

**Pemohon:** Staff Farmasi  
**Status:** Menunggu review

---

### Permintaan #3 - Status: Proses
**Deskripsi:** Pengadaan Vitamin dan Suplemen
- Vitamin C 1000mg - 3.000 tablet
- Vitamin B Complex - 2.000 tablet
- Zinc 50mg - 1.500 tablet
- Multivitamin Syrup - 500 botol

**Pemohon:** Staff Farmasi  
**Status:** Sudah dibuat nota dinas ke Direktur  
**Nota Dinas:** ND/IF/2025/001

---

### Permintaan #4 - Status: Disetujui
**Deskripsi:** Pengadaan Obat Antibiotik
- Cefadroxil 500mg - 3.000 kapsul
- Ciprofloxacin 500mg - 2.000 tablet
- Metronidazole 500mg - 2.500 tablet
- Gentamicin injection - 500 ampul

**Pemohon:** Staff Farmasi  
**Status:** Sudah disetujui dan diteruskan ke Bagian Pengadaan  
**Nota Dinas:** ND/IF/2024/012

---

### Permintaan #5 - Status: Ditolak
**Deskripsi:** Pengadaan Obat Generik
- Ibuprofen 400mg - 5.000 tablet
- Asam Mefenamat 500mg - 4.000 tablet

**Pemohon:** Staff Farmasi  
**Status:** Ditolak  
**Alasan:** Stok obat generik masih mencukupi untuk 3 bulan ke depan

---

## 📊 Statistik Data Demo

Setelah seeder dijalankan:

**Total Data:**
- Users: 2 (1 Kepala + 1 Staff)
- Permintaan: 5
- Nota Dinas: 3

**Status Permintaan:**
- Diajukan: 2 permintaan
- Proses: 1 permintaan
- Disetujui: 1 permintaan
- Ditolak: 1 permintaan

---

## 🔄 Reset Data Demo

Jika ingin mereset dan menjalankan ulang seeder:

```bash
# Hapus semua data dan jalankan ulang migration + seeder
php artisan migrate:fresh --seed
```

**⚠️ WARNING:** Perintah ini akan **menghapus semua data** di database!

---

## 💡 Tips

1. **Gunakan data demo** untuk testing fitur baru
2. **Jangan gunakan di production** - data ini hanya untuk development
3. **Buat seeder sendiri** untuk data production yang sesuai kebutuhan
4. **Backup database** sebelum menjalankan `migrate:fresh`

---

## 📝 Membuat Seeder Sendiri

Jika ingin membuat seeder untuk unit lain:

```bash
php artisan make:seeder IGDDataSeeder
```

Edit file `database/seeders/IGDDataSeeder.php` dan ikuti pola dari `KepalaInstalasiDataSeeder.php`.

---

<p align="center">
  <em>Last Updated: October 16, 2025</em>
</p>
