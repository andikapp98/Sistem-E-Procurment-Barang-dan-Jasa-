# Admin Permintaan By Unit Seeder

## Deskripsi

Seeder ini membuat data permintaan admin berdasarkan unit kerja masing-masing. Setiap Kepala Instalasi hanya dapat melihat permintaan dari staff unit mereka sendiri.

## Konsep

1. **Admin** membuat permintaan atas nama unit tertentu
2. Permintaan disimpan dengan `user_id` = ID dari **Kepala Instalasi** unit tersebut  
3. **Kepala Instalasi** hanya melihat permintaan dengan `user_id` mereka sendiri
4. Setiap unit hanya melihat permintaan dari staff unit mereka

## Data yang Dibuat

Seeder ini membuat **22 permintaan** dari **12 unit berbeda**:

| Unit                          | Jumlah | Kepala Instalasi                    |
|------------------------------|--------|-------------------------------------|
| Gawat Darurat                | 3      | Dr. Ahmad Yani, Sp.PD              |
| Farmasi                      | 2      | Apt. Siti Nurhaliza, S.Farm        |
| Laboratorium                 | 2      | Dr. Budi Santoso, Sp.PK            |
| Radiologi                    | 1      | Dr. Dewi Kusuma, Sp.Rad            |
| Bedah Sentral                | 2      | Dr. Eko Prasetyo, Sp.B             |
| Rawat Inap                   | 2      | Ns. Siti Aminah, S.Kep, M.Kep      |
| Rawat Jalan                  | 1      | Dr. Putri Handayani, Sp.PD         |
| ICU/ICCU                     | 2      | Dr. Muhammad Fajar, Sp.An          |
| Rekam Medis                  | 1      | Ns. Retno Wulan, S.KM, M.Kes       |
| Gizi                         | 2      | Nurhayati, S.Gz, M.Gizi            |
| Sanitasi & Pemeliharaan      | 2      | Ir. Bambang Susilo, M.T            |
| Laundry & Linen              | 2      | Sri Wahyuni, S.T                   |

## Cara Menggunakan

### 1. Jalankan Seeder

```bash
php artisan db:seed --class=AdminPermintaanByUnitSeeder
```

### 2. Reset dan Jalankan Ulang (Opsional)

Jika ingin reset semua data dan seed ulang:

```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=AdminPermintaanByUnitSeeder
```

### 3. Jalankan Hanya Seeder Ini

```bash
# Truncate tabel permintaan terlebih dahulu
DB::table('permintaan')->truncate();

# Jalankan seeder
php artisan db:seed --class=AdminPermintaanByUnitSeeder
```

## Testing

Login sebagai salah satu Kepala Instalasi untuk melihat permintaan unit mereka:

### IGD (3 permintaan)
```
Email: kepala.igd@rsud.id
Password: password
```
Akan melihat 3 permintaan dari unit Gawat Darurat.

### Farmasi (2 permintaan)
```
Email: kepala.farmasi@rsud.id
Password: password
```
Akan melihat 2 permintaan dari unit Farmasi.

### Laboratorium (2 permintaan)
```
Email: kepala.lab@rsud.id
Password: password
```
Akan melihat 2 permintaan dari unit Laboratorium.

### Dan seterusnya...

Setiap Kepala Instalasi hanya akan melihat permintaan dari unit mereka sendiri.

## Detail Permintaan Per Unit

### 1. Gawat Darurat (IGD)
- **Permintaan 1**: Alat kesehatan emergency (Defibrillator, Ventilator, Monitor) - Status: diajukan
- **Permintaan 2**: Obat emergency kit (Adrenalin, Dopamin, Cairan) - Status: proses
- **Permintaan 3**: APD lengkap (Masker, Sarung tangan, Gown) - Status: disetujui

### 2. Farmasi
- **Permintaan 1**: Obat rutin (Antibiotik, Analgetik, Cardiovascular) - Status: diajukan
- **Permintaan 2**: Peralatan farmasi (Lemari es, Freezer, Rak) - Status: proses

### 3. Laboratorium
- **Permintaan 1**: Reagen laboratorium (Hematologi, Kimia klinik) - Status: diajukan
- **Permintaan 2**: Alat habis pakai lab (Vacutainer, Spuit, Tips) - Status: proses

### 4. Radiologi
- **Permintaan 1**: Bahan kontras dan film radiologi - Status: diajukan

### 5. Bedah Sentral
- **Permintaan 1**: Bahan habis pakai operasi (Benang, Sarung tangan, Kasa) - Status: diajukan
- **Permintaan 2**: Instrumen bedah (Set instrumen, Gunting, Pinset) - Status: proses

### 6. Rawat Inap
- **Permintaan 1**: Peralatan medis (Monitor, Pump, Nebulizer) - Status: diajukan
- **Permintaan 2**: Linen pasien (Sprei, Selimut, Baju pasien) - Status: proses

### 7. Rawat Jalan
- **Permintaan 1**: Peralatan poliklinik (Tensimeter, Stetoskop, Meja periksa) - Status: diajukan

### 8. ICU/ICCU
- **Permintaan 1**: Peralatan ICU (Ventilator, Monitor 12 parameter, Defibrillator) - Status: diajukan
- **Permintaan 2**: Consumable ICU (ETT, Circuit, CVC) - Status: proses

### 9. Rekam Medis
- **Permintaan 1**: Peralatan rekam medis (Komputer, Filing cabinet, Formulir) - Status: diajukan

### 10. Gizi
- **Permintaan 1**: Peralatan dapur (Kompor, Rice cooker, Peralatan makan) - Status: diajukan
- **Permintaan 2**: Bahan makanan (Beras, Gula, Minyak, Bumbu) - Status: proses

### 11. Sanitasi & Pemeliharaan
- **Permintaan 1**: Peralatan sanitasi (Mesin scrubber, Vacuum, Chemical) - Status: diajukan
- **Permintaan 2**: Peralatan pemeliharaan (Toolset, Kabel, Pipa, Cat) - Status: proses

### 12. Laundry & Linen
- **Permintaan 1**: Peralatan laundry (Mesin cuci, Pengering, Chemical) - Status: diajukan
- **Permintaan 2**: Linen rumah sakit (Sprei, Linen operasi, Linen petugas) - Status: proses

## Status Permintaan

Seeder ini membuat permintaan dengan berbagai status:
- **diajukan**: Permintaan baru yang masih dalam review
- **proses**: Permintaan yang sedang diproses
- **disetujui**: Permintaan yang sudah disetujui (hanya IGD/APD)

## Nomor Nota Dinas

Format nomor nota dinas: `ND/[KODE_UNIT]/2025/[NOMOR]/X`

Contoh:
- `ND/IGD/2025/101/X` - Nota dinas IGD nomor 101
- `ND/FARM/2025/201/X` - Nota dinas Farmasi nomor 201
- `ND/LAB/2025/301/X` - Nota dinas Laboratorium nomor 301

## Query Testing

Untuk melihat permintaan per unit di database:

```sql
-- Lihat semua permintaan IGD
SELECT p.*, u.name, u.unit_kerja 
FROM permintaan p 
JOIN users u ON p.user_id = u.id 
WHERE u.unit_kerja = 'Gawat Darurat';

-- Lihat semua permintaan dengan status
SELECT u.unit_kerja, COUNT(*) as total, p.status
FROM permintaan p
JOIN users u ON p.user_id = u.id
GROUP BY u.unit_kerja, p.status
ORDER BY u.unit_kerja;

-- Lihat ringkasan per unit
SELECT u.unit_kerja, u.name as kepala_instalasi, COUNT(*) as total_permintaan
FROM permintaan p
JOIN users u ON p.user_id = u.id
GROUP BY u.unit_kerja, u.name
ORDER BY u.unit_kerja;
```

## Notes

1. Seeder ini bergantung pada data user yang sudah ada dari `UserSeeder`
2. Pastikan `UserSeeder` sudah dijalankan sebelum menjalankan seeder ini
3. Setiap permintaan memiliki tanggal berbeda untuk simulasi timeline
4. Data deskripsi sudah dibuat realistis sesuai kebutuhan rumah sakit

## Integrasi dengan Controller

Controller Kepala Instalasi harus memfilter permintaan berdasarkan `user_id`:

```php
// PermintaanController.php untuk Kepala Instalasi
public function index()
{
    // Hanya tampilkan permintaan dengan user_id = ID Kepala Instalasi login
    $permintaans = Permintaan::where('user_id', Auth::id())
                             ->orderBy('created_at', 'desc')
                             ->paginate(10);
    
    return view('kepala-instalasi.permintaan.index', compact('permintaans'));
}
```

## Author

Created for Pengadaan App - RSUD Management System

Last Updated: 28 Oktober 2025
