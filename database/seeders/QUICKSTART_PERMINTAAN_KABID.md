# Quick Start - Permintaan Kepala Bidang Seeder

## Run Seeder

```bash
php artisan db:seed --class=PermintaanKepalaBidangSeeder
```

## Output

```
========================================================================================================
   SEEDER: Permintaan untuk Kepala Bidang
========================================================================================================

  [URGENT] [1/10] Instalasi Gawat Darurat
  [NORMAL] [2/10] Instalasi Gawat Darurat
  [HIGH] [3/10] Instalasi Farmasi
  [URGENT] [4/10] Instalasi Farmasi
  [NORMAL] [5/10] Instalasi Laboratorium Patologi Klinik
  [URGENT] [6/10] Instalasi Laboratorium Patologi Klinik
  [NORMAL] [7/10] Instalasi Radiologi
  [HIGH] [8/10] Instalasi Bedah Sentral
  [NORMAL] [9/10] Instalasi Rawat Inap
  [NORMAL] [10/10] Instalasi Rehabilitasi Medik

[OK] Seeder berhasil dijalankan!

RINGKASAN DATA YANG DIBUAT:
  Permintaan dibuat      : 10
  Nota Dinas dibuat      : 10
  Disposisi dibuat       : 10

DETAIL PER INSTALASI:
  [IGD] Instalasi Gawat Darurat: 2 permintaan
  [FAR] Instalasi Farmasi: 2 permintaan
  [LAB] Instalasi Laboratorium Patologi Klinik: 2 permintaan
  [RAD] Instalasi Radiologi: 1 permintaan
  [BEDAH] Instalasi Bedah Sentral: 1 permintaan
  [RANAP] Instalasi Rawat Inap: 1 permintaan
  [RM] Instalasi Rehabilitasi Medik: 1 permintaan
```

## Test Login

1. **Login sebagai Kepala Bidang**
   - URL: `http://localhost/login`
   - Email: `kepala.bidang@rsud.id`
   - Password: `password`

2. **Verifikasi Dashboard**
   - ✅ Menunggu Review: 10 permintaan
   - ✅ Total: 10

3. **Verifikasi Index/List**
   - ✅ Tabel menampilkan 10 permintaan
   - ✅ Semua status: "proses"
   - ✅ Semua pic_pimpinan: "Kepala Bidang"

4. **Verifikasi Detail**
   - ✅ Klik salah satu permintaan
   - ✅ Lihat detail lengkap
   - ✅ Lihat Nota Dinas
   - ✅ Lihat Disposisi

## Quick Verification SQL

```sql
-- Check total
SELECT COUNT(*) FROM permintaan 
WHERE status = 'proses' 
AND pic_pimpinan = 'Kepala Bidang';
-- Expected: 10

-- Check with disposisi
SELECT p.permintaan_id, p.bidang, d.status
FROM permintaan p
INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
INNER JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE d.jabatan_tujuan = 'Kepala Bidang'
AND d.status = 'pending';
-- Expected: 10 rows
```

## Sample Data

### Urgent Requests
1. Monitor pasien IGD - Rp 150.000.000
2. Lemari pendingin vaksin - Rp 35.000.000
3. Hematology analyzer - Rp 175.000.000

### High Priority
1. Antibiotik (Farmasi) - Rp 85.000.000
2. Alat bedah minor set - Rp 45.000.000

### Normal Priority
1. Infus set IGD
2. Reagen kimia klinik
3. Film X-ray
4. Tempat tidur pasien
5. Treadmill medis

## Troubleshooting

### Data tidak muncul?

**Check 1**: User admin exists
```bash
php artisan tinker
>>> User::where('email', 'admin@rsud.id')->exists()
```

**Check 2**: Clear cache
```bash
php artisan cache:clear
```

**Check 3**: Verify controller
- File: `app/Http/Controllers/KepalaBidangController.php`
- Must have: `whereHas('notaDinas.disposisi')`

### Run again?

```bash
# Delete existing data first
php artisan tinker
>>> Disposisi::where('jabatan_tujuan', 'Kepala Bidang')->delete();
>>> NotaDinas::where('kepada', 'Kepala Bidang')->delete();
>>> Permintaan::where('pic_pimpinan', 'Kepala Bidang')->delete();

# Then run seeder again
php artisan db:seed --class=PermintaanKepalaBidangSeeder
```

---

**Created**: 2025-01-20  
**Status**: ✅ Ready to use  
**Version**: 1.0
