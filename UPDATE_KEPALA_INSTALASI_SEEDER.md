# Update UserSeeder - Tambah Kepala Instalasi

## Perubahan
Menambahkan 7 Kepala Instalasi baru untuk unit-unit yang umum di rumah sakit.

## Kepala Instalasi yang Ditambahkan

### Sebelumnya (5 unit):
1. Gawat Darurat (IGD)
2. Farmasi
3. Laboratorium
4. Radiologi
5. Bedah Sentral

### Ditambahkan (7 unit baru):
6. **Rawat Inap** - kepala.ranap@rsud.id
7. **Rawat Jalan** - kepala.rajal@rsud.id
8. **ICU/ICCU** - kepala.icu@rsud.id
9. **Rekam Medis** - kepala.rm@rsud.id
10. **Gizi** - kepala.gizi@rsud.id
11. **Sanitasi & Pemeliharaan** - kepala.sanitasi@rsud.id
12. **Laundry & Linen** - kepala.laundry@rsud.id

### Total Sekarang:
- **15 Kepala Instalasi** (12 unit spesifik + 1 generic)
- **30 total users** (naik dari 23)

## Daftar Lengkap Kepala Instalasi

| No | Email | Password | Unit Kerja | Nama |
|----|-------|----------|------------|------|
| 1 | kepala.igd@rsud.id | password | Gawat Darurat | Dr. Ahmad Yani, Sp.PD |
| 2 | kepala.farmasi@rsud.id | password | Farmasi | Apt. Siti Nurhaliza, S.Farm |
| 3 | kepala.lab@rsud.id | password | Laboratorium | Dr. Budi Santoso, Sp.PK |
| 4 | kepala.radiologi@rsud.id | password | Radiologi | Dr. Dewi Kusuma, Sp.Rad |
| 5 | kepala.bedah@rsud.id | password | Bedah Sentral | Dr. Eko Prasetyo, Sp.B |
| 6 | kepala.ranap@rsud.id | password | Rawat Inap | Ns. Siti Aminah, S.Kep, M.Kep |
| 7 | kepala.rajal@rsud.id | password | Rawat Jalan | Dr. Putri Handayani, Sp.PD |
| 8 | kepala.icu@rsud.id | password | ICU/ICCU | Dr. Muhammad Fajar, Sp.An |
| 9 | kepala.rm@rsud.id | password | Rekam Medis | Ns. Retno Wulan, S.KM, M.Kes |
| 10 | kepala.gizi@rsud.id | password | Gizi | Nurhayati, S.Gz, M.Gizi |
| 11 | kepala.sanitasi@rsud.id | password | Sanitasi & Pemeliharaan | Ir. Bambang Susilo, M.T |
| 12 | kepala.laundry@rsud.id | password | Laundry & Linen | Sri Wahyuni, S.T |
| 13 | kepala_instalasi@rsud.id | password123 | Instalasi | Kepala Instalasi (Generic) |

## Cara Running Seeder

```bash
# Fresh migration + seed semua user
php artisan migrate:fresh --seed

# Atau hanya seed UserSeeder
php artisan db:seed --class=UserSeeder
```

## File Modified
- `database/seeders/UserSeeder.php`
  - Line 107-185: Tambah 7 Kepala Instalasi baru
  - Line 334-364: Update info output (23 → 30 users)

## Status
✅ **DONE** - UserSeeder sudah dilengkapi dengan 12 unit instalasi + 1 generic account
