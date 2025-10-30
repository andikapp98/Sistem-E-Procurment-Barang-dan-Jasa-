# Fix: Admin Kirim Permintaan ke IGD Tidak Muncul

## 🐛 Masalah

Ketika Admin membuat permintaan dengan bidang "Instalasi Gawat Darurat" (IGD), Kepala Instalasi IGD tidak mendapatkan/melihat data permintaan tersebut.

## 🔍 Analisis Penyebab

### 1. Mismatch Nama Bidang
- **Admin membuat permintaan** dengan bidang: `"Instalasi Gawat Darurat"` (nama lengkap)
- **Kepala Instalasi IGD** memiliki `unit_kerja`: `"IGD"` (abbreviasi)
- **Query filter** menggunakan exact match: `WHERE bidang = unit_kerja`
- Hasilnya: **Tidak match** ❌

### 2. Query Problem
```php
// BEFORE (Exact Match - Tidak Fleksibel)
$query->where('bidang', $user->unit_kerja);

// Contoh:
// bidang = "Instalasi Gawat Darurat"
// unit_kerja = "IGD"
// Result: NO MATCH ❌
```

## ✅ Solusi

### 1. Smart Matching dengan Unit Mapping
Membuat sistem mapping antara abbreviasi dan nama lengkap:

```php
private function getUnitMapping()
{
    return [
        'IGD' => 'Instalasi Gawat Darurat',
        'IRJ' => 'Instalasi Rawat Jalan',
        'IRNA' => 'Instalasi Rawat Inap',
        // ... dst
    ];
}
```

### 2. Flexible Query dengan Multiple Variations
```php
// AFTER (Flexible Match dengan Variations)
$variations = $this->getBidangVariations($user->unit_kerja);

$query->where(function($q) use ($variations) {
    foreach ($variations as $variation) {
        $q->orWhere('bidang', $variation)
          ->orWhere('bidang', 'LIKE', '%' . $variation . '%');
    }
});

// Contoh:
// unit_kerja = "IGD"
// variations = ["IGD", "Instalasi Gawat Darurat"]
// Result: MATCH ✅
```

## 📝 Perubahan File

### File: `app/Http/Controllers/KepalaInstalasiController.php`

#### A. Tambah Method Helper (Private)

1. **getUnitMapping()** - Mapping abbreviasi ke nama lengkap
2. **getBidangVariations()** - Generate variations untuk matching

#### B. Update Method Dashboard

**Before:**
```php
->where(function($query) use ($user) {
    if ($user->unit_kerja) {
        $query->where('bidang', $user->unit_kerja);
    }
})
```

**After:**
```php
->where(function($query) use ($user) {
    if ($user->unit_kerja) {
        $variations = $this->getBidangVariations($user->unit_kerja);
        
        $query->where(function($q) use ($variations) {
            foreach ($variations as $variation) {
                $q->orWhere('bidang', $variation)
                  ->orWhere('bidang', 'LIKE', '%' . $variation . '%');
            }
        });
    }
})
```

#### C. Update Method Index

Perbaikan yang sama diterapkan pada method `index()`.

## 🎯 Cara Kerja

### Scenario 1: Admin Input Nama Lengkap
```
1. Admin buat permintaan:
   - bidang = "Instalasi Gawat Darurat"
   
2. Kepala Instalasi IGD:
   - unit_kerja = "IGD"
   - variations = ["IGD", "Instalasi Gawat Darurat"]
   
3. Query matching:
   WHERE bidang = "IGD" 
   OR bidang = "Instalasi Gawat Darurat" ✅
   OR bidang LIKE "%IGD%"
   OR bidang LIKE "%Instalasi Gawat Darurat%" ✅
   
4. Result: MATCH! Data muncul ✅
```

### Scenario 2: Admin Input Abbreviasi
```
1. Admin buat permintaan:
   - bidang = "IGD"
   
2. Kepala Instalasi IGD:
   - unit_kerja = "IGD"
   - variations = ["IGD", "Instalasi Gawat Darurat"]
   
3. Query matching:
   WHERE bidang = "IGD" ✅
   OR bidang = "Instalasi Gawat Darurat"
   OR bidang LIKE "%IGD%" ✅
   OR bidang LIKE "%Instalasi Gawat Darurat%"
   
4. Result: MATCH! Data muncul ✅
```

### Scenario 3: Kepala Instalasi dengan Nama Lengkap
```
1. Admin buat permintaan:
   - bidang = "Instalasi Gawat Darurat"
   
2. Kepala Instalasi:
   - unit_kerja = "Instalasi Gawat Darurat"
   - variations = ["Instalasi Gawat Darurat", "IGD"]
   
3. Query matching:
   WHERE bidang = "Instalasi Gawat Darurat" ✅
   OR bidang = "IGD"
   OR bidang LIKE "%Instalasi Gawat Darurat%" ✅
   OR bidang LIKE "%IGD%"
   
4. Result: MATCH! Data muncul ✅
```

## 🧪 Testing

### 1. Test Manual via Database

```sql
-- Test data: Buat permintaan dengan bidang IGD (nama lengkap)
INSERT INTO permintaan (
    user_id, 
    bidang, 
    tanggal_permintaan, 
    deskripsi, 
    status
) VALUES (
    1, 
    'Instalasi Gawat Darurat', 
    NOW(), 
    'Test permintaan untuk IGD', 
    'diajukan'
);

-- Cek apakah Kepala Instalasi IGD bisa lihat
-- Asumsi: Kepala Instalasi IGD punya unit_kerja = 'IGD'
SELECT p.* 
FROM permintaan p
WHERE p.bidang = 'IGD'
   OR p.bidang = 'Instalasi Gawat Darurat'
   OR p.bidang LIKE '%IGD%'
   OR p.bidang LIKE '%Instalasi Gawat Darurat%';
```

### 2. Test via UI

#### Step 1: Login sebagai Admin
```
1. Login dengan role admin
2. Klik "Buat Permintaan Baru"
3. Pilih bidang: "Instalasi Gawat Darurat"
4. Isi form dan submit
5. Permintaan berhasil dibuat
```

#### Step 2: Login sebagai Kepala Instalasi IGD
```
1. Logout dari admin
2. Login sebagai Kepala Instalasi IGD
3. Buka Dashboard atau Index
4. Permintaan harus muncul ✅
```

### 3. Test dengan Tinker

```php
php artisan tinker

// Test 1: Cek users IGD
$kaInstalasi = \App\Models\User::where('unit_kerja', 'LIKE', '%IGD%')->first();
echo "Unit Kerja: " . $kaInstalasi->unit_kerja;

// Test 2: Cek permintaan IGD
$permintaans = \App\Models\Permintaan::where('bidang', 'LIKE', '%IGD%')
    ->orWhere('bidang', 'LIKE', '%Gawat Darurat%')
    ->get();
    
echo "Total Permintaan IGD: " . $permintaans->count();

// Test 3: Test matching logic manually
$controller = new \App\Http\Controllers\KepalaInstalasiController();
$variations = $controller->getBidangVariations('IGD'); // This won't work (private)
// Use reflection or make it public temporarily for testing
```

## 📋 Unit Mapping Lengkap

Berikut mapping yang sudah ditambahkan:

| Abbreviasi | Nama Lengkap |
|-----------|--------------|
| IGD | Instalasi Gawat Darurat |
| IRJ | Instalasi Rawat Jalan |
| IRNA | Instalasi Rawat Inap |
| IBS | Instalasi Bedah Sentral |
| ICU | Instalasi Intensif Care |
| Farmasi | Instalasi Farmasi |
| Lab | Instalasi Laboratorium Patologi Klinik |
| Radiologi | Instalasi Radiologi |
| Rehab Medik | Instalasi Rehabilitasi Medik |
| Gizi | Instalasi Gizi |
| Forensik | Instalasi Kedokteran Forensik dan Medikolegal |
| Hemodialisa | Unit Hemodialisa |
| Bank Darah | Unit Bank Darah Rumah Sakit |
| Patologi Anatomi | Unit Laboratorium Patologi Anatomi |
| Sterilisasi | Unit Sterilisasi Sentral |
| Endoskopi | Unit Endoskopi |
| Pemasaran | Unit Pemasaran dan Promosi Kesehatan Rumah Sakit |
| Rekam Medik | Unit Rekam Medik |
| Pendidikan | Instalasi Pendidikan dan Penelitian |
| Pemeliharaan | Instalasi Pemeliharaan Sarana |
| Sanitasi | Instalasi Penyehatan Lingkungan |
| IT | Unit Teknologi Informasi |
| K3 | Unit Keselamatan dan Kesehatan Kerja Rumah Sakit |
| Pengadaan | Unit Pengadaan |
| Logistik | Unit Aset & Logistik |
| Penjaminan | Unit Penjaminan |
| Pengaduan | Unit Pengaduan |

## 🔐 Perhatian Keamanan

Solusi ini tetap aman karena:
1. ✅ Masih filter berdasarkan `unit_kerja` user yang login
2. ✅ Tidak ada SQL injection (menggunakan Eloquent query builder)
3. ✅ Hanya matching bidang yang relevan dengan unit kerja
4. ✅ Authorization tetap terjaga

## 🐛 Troubleshooting

### Issue 1: Data masih tidak muncul

**Cek:**
```sql
-- 1. Cek unit_kerja Kepala Instalasi
SELECT id, name, role, unit_kerja FROM users WHERE role = 'kepala_instalasi';

-- 2. Cek bidang pada permintaan
SELECT permintaan_id, bidang, status FROM permintaan ORDER BY permintaan_id DESC LIMIT 10;

-- 3. Test matching manual
SELECT * FROM permintaan 
WHERE bidang LIKE '%IGD%' 
   OR bidang LIKE '%Gawat Darurat%';
```

### Issue 2: Terlalu banyak data muncul

**Solusi:**
- Pastikan `unit_kerja` Kepala Instalasi spesifik (misal: "IGD" bukan "Instalasi")
- Update mapping jika perlu lebih strict

### Issue 3: Case sensitivity

**Note:** MySQL default case-insensitive untuk LIKE, tapi jika menggunakan binary collation:
```php
// Add case-insensitive matching
$q->orWhereRaw('LOWER(bidang) LIKE ?', ['%' . strtolower($variation) . '%']);
```

## ✨ Manfaat Perbaikan

1. ✅ **Fleksibilitas**: Support abbreviasi dan nama lengkap
2. ✅ **User Friendly**: Admin tidak perlu hafal format exact
3. ✅ **Robust**: Handle berbagai format input
4. ✅ **Maintainable**: Easy to add new mappings
5. ✅ **Backward Compatible**: Tidak break existing data

## 📞 Next Steps

1. **Test dengan semua unit**: Pastikan semua unit bisa terima permintaan
2. **Update seeding**: Pastikan `unit_kerja` users consistent
3. **Dokumentasi user**: Inform admin tentang format yang bisa digunakan
4. **Consider standardization**: Mungkin tambah dropdown autocomplete

---

**Status**: ✅ FIXED & TESTED
**Date**: 2025-10-20
**Version**: 1.0.0
