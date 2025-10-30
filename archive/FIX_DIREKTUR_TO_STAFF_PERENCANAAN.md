# Fix: Direktur ke Staff Perencanaan Workflow

## Masalah
Data dari Direktur setelah approve tidak masuk ke Staff Perencanaan.

## Analisis Workflow
1. **Direktur Approve** → Disposisi ke Kepala Bidang dengan catatan "Disetujui oleh Direktur (Final Approval)"
2. **Kepala Bidang** → Harus mendeteksi disposisi dari Direktur dan meneruskan ke Staff Perencanaan
3. **Staff Perencanaan** → Menerima permintaan dengan status 'disetujui'

## Perbaikan yang Dilakukan

### 1. KepalaBidangController.php - Method approve() (Line 240-251)
**Sebelum:**
```php
$disposisiDariDirektur = Disposisi::where('nota_id', $notaDinas->nota_id)
    ->where('jabatan_tujuan', 'Kepala Bidang')
    ->where('status', 'disetujui')
    ->whereHas('notaDinas', function($q) {
        $q->whereHas('disposisi', function($query) {
            $query->where('jabatan_tujuan', 'Direktur');
        });
    })
    ->exists();
```

**Sesudah:**
```php
$disposisiDariDirektur = Disposisi::where('nota_id', $notaDinas->nota_id)
    ->where('jabatan_tujuan', 'Kepala Bidang')
    ->where(function($q) {
        $q->where('catatan', 'like', '%Disetujui oleh Direktur%')
          ->orWhere('status', 'selesai');
    })
    ->exists();
```

**Penjelasan:**
- Deteksi lebih sederhana dan langsung
- Cek berdasarkan catatan disposisi yang berisi "Disetujui oleh Direktur"
- Atau cek status 'selesai' yang menandakan final approval dari Direktur

### 2. KepalaBidangController.php - Method show() (Line 145-153)
**Sebelum:**
```php
$isDisposisiDariDirektur = Disposisi::where('nota_id', $notaDinas->nota_id)
    ->where('jabatan_tujuan', 'Kepala Bidang')
    ->where('status', 'disetujui')
    ->whereHas('notaDinas', function($q) {
        $q->whereHas('disposisi', function($query) {
            $query->where('jabatan_tujuan', 'Direktur');
        });
    })
    ->exists();

// Return dengan kondisi tambahan
'isDisposisiDariDirektur' => $isDisposisiDariDirektur || $permintaan->status === 'disetujui',
```

**Sesudah:**
```php
// Gunakan logic yang sama dengan approve method
$isDisposisiDariDirektur = Disposisi::where('nota_id', $notaDinas->nota_id)
    ->where('jabatan_tujuan', 'Kepala Bidang')
    ->where(function($q) {
        $q->where('catatan', 'like', '%Disetujui oleh Direktur%')
          ->orWhere('status', 'selesai');
    })
    ->exists();

// Return tanpa kondisi tambahan
'isDisposisiDariDirektur' => $isDisposisiDariDirektur,
```

**Penjelasan:**
- Konsisten dengan logic di method approve()
- Menghapus kondisi `|| $permintaan->status === 'disetujui'` yang bisa false positive
- Sekarang hanya berdasarkan disposisi dari Direktur saja

### 3. Workflow yang Sudah Benar di DirekturController.php (Line 219-226)
```php
Disposisi::create([
    'nota_id' => $notaDinas->nota_id,
    'jabatan_tujuan' => 'Kepala Bidang',
    'tanggal_disposisi' => Carbon::now(),
    'catatan' => 'Disetujui oleh Direktur (Final Approval). ' . ($data['catatan'] ?? 'Silakan disposisi ke Staff Perencanaan untuk perencanaan pengadaan.'),
    'status' => 'selesai',  // ← Status ini yang menandakan final approval
]);
```

### 4. KepalaBidangController approve() - Sudah Benar (Line 254-267)
Ketika mendeteksi disposisi dari Direktur, otomatis:
```php
// Buat disposisi ke Staff Perencanaan
Disposisi::create([
    'nota_id' => $notaDinas->nota_id,
    'jabatan_tujuan' => 'Staff Perencanaan',
    'tanggal_disposisi' => Carbon::now(),
    'catatan' => $data['catatan'] ?? 'Sudah disetujui Direktur. Mohon lakukan perencanaan pengadaan.',
    'status' => 'disetujui',
]);

// Update status permintaan - teruskan ke Staff Perencanaan
$permintaan->update([
    'status' => 'disetujui',
    'pic_pimpinan' => 'Staff Perencanaan',
]);
```

## Alur Lengkap Setelah Fix

### Step 1: Direktur Approve
```
Permintaan (pic_pimpinan: 'Direktur', status: 'proses')
   ↓ [Direktur klik Approve]
Disposisi dibuat:
   - jabatan_tujuan: 'Kepala Bidang'
   - catatan: 'Disetujui oleh Direktur (Final Approval)...'
   - status: 'selesai'
Permintaan diupdate:
   - pic_pimpinan: 'Kepala Bidang'
   - status: 'proses'
```

### Step 2: Kepala Bidang Terima & Forward
```
Kepala Bidang melihat permintaan di dashboard/index
   ↓ [Buka detail permintaan]
Sistem deteksi: Ada disposisi dari Direktur (status='selesai' atau catatan mengandung 'Disetujui oleh Direktur')
   ↓ [Kepala Bidang klik Approve]
Disposisi dibuat:
   - jabatan_tujuan: 'Staff Perencanaan'
   - catatan: 'Sudah disetujui Direktur. Mohon lakukan perencanaan pengadaan.'
   - status: 'disetujui'
Permintaan diupdate:
   - pic_pimpinan: 'Staff Perencanaan'
   - status: 'disetujui'
```

### Step 3: Staff Perencanaan Terima
```
Staff Perencanaan melihat permintaan di dashboard/index
Query filter:
   - pic_pimpinan = 'Staff Perencanaan'
   - status IN ('disetujui', 'proses')
   - Ada nota dinas dari Direktur/Wakil Direktur (sudah benar di controller)
```

## Testing Steps

1. **Login sebagai Direktur**
   - Buka permintaan yang sedang di tangan Direktur
   - Klik "Approve"
   - Verify: Permintaan pindah ke Kepala Bidang

2. **Login sebagai Kepala Bidang**
   - Refresh dashboard
   - Verify: Permintaan muncul dengan indikasi dari Direktur
   - Buka detail permintaan
   - Klik "Approve"
   - Verify: Permintaan diteruskan ke Staff Perencanaan

3. **Login sebagai Staff Perencanaan**
   - Refresh dashboard
   - Verify: Permintaan muncul di daftar
   - Verify: Status = 'disetujui'
   - Verify: Bisa buat perencanaan/DPP/HPS

## Files Modified
1. `app/Http/Controllers/KepalaBidangController.php`
   - Line 145-153: Method show() - Fixed isDisposisiDariDirektur detection
   - Line 240-251: Method approve() - Fixed disposisi detection logic

## Root Cause
Ada 2 masalah:
1. Logic deteksi disposisi dari Direktur terlalu kompleks dan tidak match dengan data yang dibuat oleh DirekturController
2. Method `show()` menggunakan logic berbeda dengan method `approve()` sehingga UI menampilkan info yang salah

## Solution
Menyamakan logic deteksi di kedua method dengan cara sederhana: cek catatan disposisi yang mengandung "Disetujui oleh Direktur" atau status 'selesai'.

## Status
✅ **FIXED** - Workflow sudah diperbaiki dengan deteksi yang lebih sederhana dan akurat
