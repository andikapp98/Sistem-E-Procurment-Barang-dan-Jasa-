# Panduan Contoh Data IGD - Sistem e-Procurement

## 📋 Deskripsi

File-file ini berisi contoh data permintaan untuk **Instalasi Gawat Darurat (IGD)** yang dapat digunakan untuk:
- Testing aplikasi
- Demo kepada stakeholder
- Template pengisian untuk user
- Seed data untuk development

## 📁 File yang Tersedia

### 1. `CONTOH_DATA_IGD.md`
File dokumentasi berisi 7 contoh lengkap permintaan IGD dengan berbagai kategori:
- ✅ Alat Kesehatan Emergency (Defibrillator, Ventilator, dll)
- ✅ Obat-obatan Emergency Kit (Injeksi, Cairan Infus)
- ✅ Alat Pelindung Diri (APD)
- ✅ Alat Medis Habis Pakai (Kateter, Spuit, Jarum)
- ✅ Perbaikan dan Kalibrasi Alat
- ✅ Furnitur dan Peralatan Penunjang
- ✅ Obat Antidotum

### 2. `database/seeders/IGDPermintaanSeeder.php`
Laravel seeder untuk memasukkan data ke database menggunakan Eloquent/Query Builder.

### 3. `database/sample_data_igd.sql`
File SQL murni untuk insert data langsung ke database.

## 🚀 Cara Menggunakan

### Metode 1: Menggunakan Laravel Seeder (Recommended)

```bash
# 1. Pastikan sudah ada user di database (user_id = 1)
# 2. Jalankan seeder
php artisan db:seed --class=IGDPermintaanSeeder

# Atau tambahkan ke DatabaseSeeder.php:
# $this->call([
#     IGDPermintaanSeeder::class,
# ]);
# Lalu jalankan:
php artisan db:seed
```

### Metode 2: Menggunakan SQL Langsung

```bash
# Untuk SQLite
sqlite3 database/database.sqlite < database/sample_data_igd.sql

# Untuk MySQL/PostgreSQL
# Import melalui phpMyAdmin, DBeaver, atau command line
mysql -u username -p database_name < database/sample_data_igd.sql
```

### Metode 3: Input Manual via Form

1. Login ke aplikasi
2. Buka menu **Permintaan** → **Buat Permintaan**
3. Copy-paste data dari `CONTOH_DATA_IGD.md`
4. Submit form

## 📝 Contoh Singkat

Berikut contoh 1 permintaan yang siap digunakan:

**Bidang:** Instalasi Gawat Darurat

**Deskripsi:**
```
Permintaan pengadaan alat kesehatan untuk ruang resusitasi IGD:
1. Defibrillator portable 1 unit
2. Oksigen konsentrator 2 unit
3. Suction pump portable 3 unit
4. Ventilator transport 1 unit
5. Monitor vital sign 2 unit

Alat-alat di atas sangat mendesak mengingat peningkatan kasus emergency 
dan kondisi beberapa alat existing yang sudah tidak layak pakai.
```

**Tanggal Permintaan:** 2025-10-16

**PIC Pimpinan:** Dr. Siti Nurhaliza, Sp.EM

**No Nota Dinas:** ND/IGD/2025/001/X

**Link Scan:** https://drive.google.com/file/d/1abc123_nota_dinas_igd_alkes

**Status:** diajukan

## ⚙️ Kustomisasi

### Sesuaikan User ID

Pastikan `user_id` di seeder/SQL sesuai dengan data user di database Anda:

```php
// Di IGDPermintaanSeeder.php
'user_id' => 1, // Ganti dengan ID user yang sesuai
```

```sql
-- Di sample_data_igd.sql
user_id = 1 -- Ganti dengan ID user yang sesuai
```

### Ubah Tanggal

```php
// Format: YYYY-MM-DD
'tanggal_permintaan' => Carbon::parse('2025-10-16'),
```

### Modifikasi Status

Status yang tersedia:
- `diajukan` - Permintaan baru yang diajukan
- `proses` - Sedang dalam proses verifikasi/approval
- `disetujui` - Sudah disetujui

## 🎯 Kategori Permintaan IGD

File contoh mencakup berbagai kategori yang umum di IGD:

1. **Alat Medis Besar**
   - Defibrillator, Ventilator, Monitor

2. **Obat Emergency**
   - Injeksi life-saving, Cairan infus, Antidotum

3. **Alat Habis Pakai**
   - Kateter, Spuit, Jarum, Kasa

4. **APD (Alat Pelindung Diri)**
   - Masker, Gown, Sarung tangan, Face shield

5. **Furnitur Medis**
   - Brancard, Stretcher, Troli emergency

6. **Maintenance**
   - Kalibrasi alat, Perbaikan equipment

## 💡 Tips Pengisian

1. **Deskripsi Detail** - Sertakan nama barang, spesifikasi, jumlah, dan justifikasi
2. **Format Nomor Nota** - Ikuti: `ND/[UNIT]/[TAHUN]/[NO URUT]/[BULAN]`
3. **PIC Lengkap** - Cantumkan nama, gelar, dan jabatan
4. **Link Dokumen** - Upload scan nota dinas ke Google Drive atau sistem dokumen RS
5. **Status Sesuai** - Pilih status yang tepat sesuai tahapan approval

## 🔍 Verifikasi Data

Setelah input data, verifikasi dengan:

```bash
# Cek jumlah data yang masuk
php artisan tinker
>>> \App\Models\Permintaan::where('bidang', 'Instalasi Gawat Darurat')->count();

# Lihat data terakhir
>>> \App\Models\Permintaan::latest()->first();
```

## 📞 Support

Jika ada pertanyaan atau masalah:
1. Cek dokumentasi Laravel Seeder
2. Review structure database di `database/migrations/`
3. Pastikan field `bidang` sudah ada di tabel `permintaan`

## 📄 License

Data contoh ini bebas digunakan untuk keperluan development dan testing aplikasi e-Procurement RSUD.

---

**Dibuat:** Oktober 2025  
**Versi:** 1.0  
**Untuk:** Sistem e-Procurement RSUD Ibnu Sina Kabupaten Gresik
