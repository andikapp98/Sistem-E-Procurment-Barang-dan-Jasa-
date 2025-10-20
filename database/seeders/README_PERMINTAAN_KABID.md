# Permintaan Kepala Bidang Seeder

## Deskripsi

Seeder untuk membuat data permintaan lengkap yang sudah approved oleh Kepala Instalasi dan siap untuk di-review oleh Kepala Bidang.

## Data yang Dibuat

### 1. Permintaan (10 items)
Data permintaan dari berbagai instalasi dengan status "proses" (sudah approved oleh Kepala Instalasi).

**Instalasi yang tercakup**:
- 🚨 Instalasi Gawat Darurat (2 permintaan)
- 💊 Instalasi Farmasi (2 permintaan)
- 🔬 Instalasi Laboratorium Patologi Klinik (2 permintaan)
- 📻 Instalasi Radiologi (1 permintaan)
- 🏥 Instalasi Bedah Sentral (1 permintaan)
- 🛏️ Instalasi Rawat Inap (1 permintaan)
- 🏃 Instalasi Rehabilitasi Medik (1 permintaan)

### 2. Nota Dinas (10 items)
Setiap permintaan memiliki Nota Dinas dari Kepala Instalasi yang ditujukan ke "Kepala Bidang".

### 3. Disposisi (10 items)
Setiap Nota Dinas memiliki Disposisi otomatis dengan:
- `jabatan_tujuan`: "Kepala Bidang"
- `status`: "pending" (menunggu review)
- `catatan`: Detail informasi termasuk prioritas dan estimasi biaya

## Prioritas Permintaan

Setiap permintaan memiliki level prioritas:

| Icon | Prioritas | Deskripsi |
|------|-----------|-----------|
| 🔴 | URGENT | Harus segera ditindaklanjuti |
| 🟠 | HIGH | Prioritas tinggi |
| 🟢 | NORMAL | Prioritas normal |

**Contoh Permintaan Urgent**:
- Monitor pasien IGD (alat lama rusak)
- Lemari pendingin vaksin (tidak akurat lagi)
- Hematology analyzer (alat rusak)

## Cara Menggunakan

### 1. Run Seeder

```bash
php artisan db:seed --class=PermintaanKepalaBidangSeeder
```

### 2. Output yang Diharapkan

```
═══════════════════════════════════════════════════════════════════════════════════════
   🏥 SEEDER: Permintaan untuk Kepala Bidang
═══════════════════════════════════════════════════════════════════════════════════════

📊 Membuat data permintaan untuk berbagai instalasi...

  🔴 [1/10] Instalasi Gawat Darurat
  🟢 [2/10] Instalasi Gawat Darurat
  🟠 [3/10] Instalasi Farmasi
  🔴 [4/10] Instalasi Farmasi
  ...

✅ Seeder berhasil dijalankan!

📊 RINGKASAN DATA YANG DIBUAT:
═══════════════════════════════════════════════════════════════════════════════════════
  📄 Permintaan dibuat      : 10
  📝 Nota Dinas dibuat      : 10
  📋 Disposisi dibuat       : 10
═══════════════════════════════════════════════════════════════════════════════════════
```

### 3. Verifikasi Data

**Query SQL untuk cek**:

```sql
-- Lihat semua permintaan yang siap untuk Kepala Bidang
SELECT 
    p.permintaan_id,
    p.bidang,
    p.status,
    p.pic_pimpinan,
    nd.kepada,
    d.jabatan_tujuan,
    d.status AS disposisi_status
FROM permintaan p
INNER JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
INNER JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE p.status = 'proses'
AND d.jabatan_tujuan = 'Kepala Bidang'
AND d.status = 'pending'
ORDER BY p.permintaan_id DESC;
```

**Expected Result**: 10 rows

## Testing Flow

### 1. Login sebagai Kepala Bidang

```
Email: kepala.bidang@rsud.id
Password: password
```

### 2. Verifikasi Dashboard

✅ **Harus melihat**:
- Total permintaan: 10
- Menunggu Review: 10
- Statistik per instalasi

### 3. Verifikasi Index/List

✅ **Harus melihat**:
- Tabel dengan 10 permintaan
- Semua status: "proses"
- Semua pic_pimpinan: "Kepala Bidang"

### 4. Verifikasi Detail Permintaan

✅ **Harus bisa**:
- Klik salah satu permintaan
- Lihat detail lengkap
- Lihat Nota Dinas
- Lihat Disposisi
- Action: Approve/Reject tersedia

## Contoh Data Permintaan

### IGD - Monitor Pasien (URGENT)
```
Bidang: Instalasi Gawat Darurat
Deskripsi: Permintaan pengadaan 2 unit monitor pasien multi-parameter 
           untuk ruang resusitasi IGD...
Prioritas: 🔴 URGENT
Estimasi: Rp 150.000.000
Status: proses
```

### Farmasi - Antibiotik (HIGH)
```
Bidang: Instalasi Farmasi
Deskripsi: Permintaan pengadaan obat antibiotik untuk stok bulan 
           Februari 2025: Ceftriaxone, Levofloxacin, Azithromycin...
Prioritas: 🟠 HIGH
Estimasi: Rp 85.000.000
Status: proses
```

### Lab - Reagen Kimia (NORMAL)
```
Bidang: Instalasi Laboratorium Patologi Klinik
Deskripsi: Permintaan pengadaan reagen kimia klinik untuk pemeriksaan 
           fungsi hati, ginjal, dan profil lipid...
Prioritas: 🟢 NORMAL
Estimasi: Rp 65.000.000
Status: proses
```

## Struktur Data

### Permintaan
```php
[
    'user_id' => admin->id,
    'bidang' => 'Instalasi Gawat Darurat',
    'tanggal_permintaan' => Carbon date,
    'deskripsi' => 'Detail lengkap permintaan...',
    'status' => 'proses',
    'pic_pimpinan' => 'Kepala Bidang',
    'no_nota_dinas' => 'ND/IGD/2025/001/I',
    'link_scan' => 'https://drive.google.com/...',
]
```

### Nota Dinas
```php
[
    'permintaan_id' => permintaan_id,
    'no_nota' => 'ND/IGD/2025/001/I',
    'tanggal_nota' => Carbon date,
    'dari' => 'Dr. Ahmad Fauzi, Sp.EM', // Kepala Instalasi
    'kepada' => 'Kepala Bidang',
    'perihal' => 'Persetujuan Permintaan Pengadaan - ...',
]
```

### Disposisi
```php
[
    'nota_id' => nota_id,
    'jabatan_tujuan' => 'Kepala Bidang',
    'tanggal_disposisi' => Carbon date,
    'catatan' => 'URGENT - Segera ditindaklanjuti. Estimasi: Rp xxx...',
    'status' => 'pending',
]
```

## Troubleshooting

### Data tidak muncul di Kepala Bidang?

**Cek 1**: Verifikasi user admin ada
```bash
php artisan tinker
>>> User::where('email', 'admin@rsud.id')->first()
```

**Cek 2**: Verifikasi data disposisi
```sql
SELECT * FROM disposisi 
WHERE jabatan_tujuan = 'Kepala Bidang' 
AND status = 'pending';
```
Expected: 10 rows

**Cek 3**: Clear cache
```bash
php artisan cache:clear
php artisan config:clear
```

**Cek 4**: Verifikasi query controller
- Pastikan `KepalaBidangController` sudah menggunakan `whereHas('notaDinas.disposisi')`
- Lihat file: `KEPALA_BIDANG_DISPOSISI_FIX.md`

### Duplicate data?

**Sebelum run ulang**:
```sql
-- Hapus data lama
DELETE FROM disposisi WHERE jabatan_tujuan = 'Kepala Bidang';
DELETE FROM nota_dinas WHERE kepada = 'Kepala Bidang';
DELETE FROM permintaan WHERE status = 'proses' AND pic_pimpinan = 'Kepala Bidang';
```

Atau reset database:
```bash
php artisan migrate:fresh
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=PermintaanKepalaBidangSeeder
```

## Integration dengan Seeder Lain

### Dependencies
Seeder ini membutuhkan:
- ✅ UserSeeder (untuk user admin)

### Optional (Recommended)
Untuk data lebih lengkap, jalankan juga:
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=KepalaBidangSeeder
php artisan db:seed --class=PermintaanKepalaBidangSeeder
```

## Update DatabaseSeeder.php

Tambahkan ke `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        UserSeeder::class,
        KepalaBidangSeeder::class,
        PermintaanKepalaBidangSeeder::class,
    ]);
}
```

Lalu jalankan:
```bash
php artisan migrate:fresh --seed
```

## Notes

- Data dibuat dengan tanggal bertahap (base date - 5 hari yang lalu)
- Setiap permintaan memiliki Nota Dinas dan Disposisi lengkap
- Status semua permintaan: "proses" (ready for Kepala Bidang review)
- Disposisi status: "pending" (belum di-approve Kepala Bidang)
- Estimasi biaya bervariasi: Rp 5 juta - Rp 175 juta

## Author

- AI Assistant
- Date: 2025-01-20
- Version: 1.0
- Status: ✅ Ready to use

---

**Happy Seeding! 🌱**
