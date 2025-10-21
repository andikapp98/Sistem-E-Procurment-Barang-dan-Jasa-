# Fix: Data yang Sudah Diproses Direktur/Kepala Bidang Masih Muncul

## Status: ✅ SELESAI

## Masalah
Data permintaan yang sudah disetujui, ditolak, atau direvisi oleh Direktur dan Kepala Bidang masih muncul di daftar permintaan mereka.

## Penyebab
Query di `dashboard()` dan `index()` menggunakan filter yang terlalu luas:
- **Direktur:** `whereIn('status', ['proses', 'disetujui'])` - Ini menangkap semua permintaan dengan status disetujui, termasuk yang sudah bukan di tangan Direktur lagi
- **Kepala Bidang:** Query menggunakan `orWhereHas` untuk disposisi yang sudah tidak relevan

### Workflow yang Benar:
```
1. Kepala Bidang approve → pic_pimpinan = 'Direktur', status = 'proses'
2. Direktur approve → pic_pimpinan = 'Kepala Bidang', status = 'disetujui' (disposisi balik)
3. Kepala Bidang forward → pic_pimpinan = 'Staff Perencanaan', status = 'disetujui'
```

### Masalah Lama:
- Setelah step 2, Direktur masih melihat permintaan karena status = 'disetujui'
- Setelah step 3, Kepala Bidang masih melihat permintaan karena ada disposisi lama

## Solusi Diterapkan

### 1. DirekturController - Dashboard

**Sebelum:**
```php
$permintaans = Permintaan::with(['user', 'notaDinas'])
    ->where(function($q) use ($user) {
        $q->where('pic_pimpinan', 'Direktur')
          ->orWhere('pic_pimpinan', $user->nama);
    })
    ->whereIn('status', ['proses', 'disetujui']) // ❌ Terlalu luas
    ->get();
```

**Sesudah:**
```php
$permintaans = Permintaan::with(['user', 'notaDinas'])
    ->where(function($q) use ($user) {
        $q->where('pic_pimpinan', 'Direktur')
          ->orWhere('pic_pimpinan', $user->nama);
    })
    ->where('status', 'proses') // ✅ HANYA yang sedang proses
    ->get();
```

### 2. DirekturController - Index

**Sebelum:**
```php
$query = Permintaan::with(['user', 'notaDinas'])
    ->where(function($q) use ($user) {
        $q->where('pic_pimpinan', 'Direktur')
          ->orWhere('pic_pimpinan', $user->nama);
    })
    ->whereIn('status', ['proses', 'disetujui']); // ❌ Terlalu luas
```

**Sesudah:**
```php
$query = Permintaan::with(['user', 'notaDinas'])
    ->where(function($q) use ($user) {
        $q->where('pic_pimpinan', 'Direktur')
          ->orWhere('pic_pimpinan', $user->nama);
    })
    ->where('status', 'proses'); // ✅ HANYA yang sedang proses
```

### 3. KepalaBidangController - Dashboard

**Sebelum:**
```php
$permintaans = Permintaan::with(['user', 'notaDinas.disposisi'])
    ->where(function($q) use ($user) {
        $q->where('pic_pimpinan', 'Kepala Bidang')
          ->orWhere('pic_pimpinan', $user->nama)
          // ❌ Query disposisi yang sudah tidak relevan
          ->orWhereHas('notaDinas.disposisi', function($query) {
              $query->where('jabatan_tujuan', 'Kepala Bidang')
                    ->where('status', 'pending');
          });
    })
    ->whereIn('status', ['proses', 'disetujui'])
    ->get();
```

**Sesudah:**
```php
$permintaans = Permintaan::with(['user', 'notaDinas.disposisi'])
    ->where(function($q) use ($user) {
        // ✅ Hanya cek pic_pimpinan, tidak perlu cek disposisi lama
        $q->where('pic_pimpinan', 'Kepala Bidang')
          ->orWhere('pic_pimpinan', $user->nama);
    })
    ->whereIn('status', ['proses', 'disetujui'])
    ->get();
```

### 4. KepalaBidangController - Index

**Sebelum:**
```php
$query = Permintaan::with(['user', 'notaDinas.disposisi'])
    ->where(function($q) use ($user) {
        $q->where('pic_pimpinan', 'Kepala Bidang')
          ->orWhere('pic_pimpinan', $user->nama)
          // ❌ Query disposisi yang sudah tidak relevan
          ->orWhereHas('notaDinas.disposisi', function($query) {
              $query->where('jabatan_tujuan', 'Kepala Bidang')
                    ->where('status', 'pending');
          });
    })
    ->whereIn('status', ['proses', 'disetujui']);
```

**Sesudah:**
```php
$query = Permintaan::with(['user', 'notaDinas.disposisi'])
    ->where(function($q) use ($user) {
        // ✅ Hanya cek pic_pimpinan
        $q->where('pic_pimpinan', 'Kepala Bidang')
          ->orWhere('pic_pimpinan', $user->nama);
    })
    ->whereIn('status', ['proses', 'disetujui']);
```

## Logika Baru

### Direktur
**Yang Muncul:**
- `pic_pimpinan = 'Direktur'` DAN `status = 'proses'`

**Yang TIDAK Muncul:**
- Permintaan yang sudah approve/reject/revisi (pic_pimpinan berubah ke Kepala Bidang/Unit Pemohon)

### Kepala Bidang
**Yang Muncul:**
- `pic_pimpinan = 'Kepala Bidang'` DAN `status IN ('proses', 'disetujui')`

**Dua Skenario:**
1. **Permintaan Baru dari Kepala Instalasi:** status = 'proses'
2. **Disposisi Balik dari Direktur:** status = 'disetujui'

**Yang TIDAK Muncul:**
- Permintaan yang sudah diteruskan ke Staff Perencanaan (pic_pimpinan = 'Staff Perencanaan')

## Key Principle: "Follow the PIC"

Query sekarang mengikuti prinsip sederhana:
> **"Hanya tampilkan permintaan yang pic_pimpinan-nya adalah role saya"**

Ketika permintaan diproses (approve/reject/revisi), `pic_pimpinan` berubah, sehingga otomatis tidak muncul lagi di daftar role sebelumnya.

## Diagram Alur

```
[Kepala Instalasi]
    ↓ approve
[Kepala Bidang] ← pic_pimpinan = 'Kepala Bidang', status = 'proses'
    ↓ approve
[Direktur] ← pic_pimpinan = 'Direktur', status = 'proses'
    ↓ approve (disposisi balik)
[Kepala Bidang] ← pic_pimpinan = 'Kepala Bidang', status = 'disetujui'
    ↓ forward
[Staff Perencanaan] ← pic_pimpinan = 'Staff Perencanaan', status = 'disetujui'
```

**Direktur hanya melihat:** Step 3 (ketika pic_pimpinan = 'Direktur')
**Kepala Bidang hanya melihat:** Step 2 dan Step 4 (ketika pic_pimpinan = 'Kepala Bidang')

## File yang Dimodifikasi

1. ✅ `app/Http/Controllers/DirekturController.php`
   - Method `dashboard()`: Filter hanya status 'proses'
   - Method `index()`: Filter hanya status 'proses'

2. ✅ `app/Http/Controllers/KepalaBidangController.php`
   - Method `dashboard()`: Hapus query disposisi yang tidak relevan
   - Method `index()`: Hapus query disposisi yang tidak relevan

## Testing Scenarios

### Test 1: Direktur Approve
1. Login sebagai Direktur
2. Lihat daftar permintaan (seharusnya ada data)
3. Approve salah satu permintaan
4. Refresh/kembali ke index
5. ✅ **Expected:** Permintaan yang di-approve TIDAK MUNCUL lagi

### Test 2: Direktur Reject
1. Login sebagai Direktur
2. Reject salah satu permintaan
3. Refresh/kembali ke index
4. ✅ **Expected:** Permintaan yang di-reject TIDAK MUNCUL lagi

### Test 3: Direktur Revisi
1. Login sebagai Direktur
2. Minta revisi salah satu permintaan
3. Refresh/kembali ke index
4. ✅ **Expected:** Permintaan yang di-revisi TIDAK MUNCUL lagi

### Test 4: Kepala Bidang - Disposisi Balik dari Direktur
1. Login sebagai Direktur, approve permintaan
2. Login sebagai Kepala Bidang
3. ✅ **Expected:** Permintaan yang sudah diapprove Direktur MUNCUL dengan status 'disetujui'
4. Forward ke Staff Perencanaan
5. Refresh/kembali ke index
6. ✅ **Expected:** Permintaan TIDAK MUNCUL lagi

### Test 5: Kepala Bidang - Permintaan Baru
1. Login sebagai Kepala Instalasi, approve permintaan
2. Login sebagai Kepala Bidang
3. ✅ **Expected:** Permintaan baru MUNCUL dengan status 'proses'
4. Forward ke Direktur
5. Refresh/kembali ke index
6. ✅ **Expected:** Permintaan TIDAK MUNCUL lagi

## Benefits

1. ✅ **Clean Dashboard** - Hanya menampilkan permintaan yang memang perlu ditangani
2. ✅ **No Duplicates** - Data yang sudah diproses tidak muncul lagi
3. ✅ **Clear Workflow** - User tahu exactly permintaan mana yang perlu action
4. ✅ **Better Performance** - Query lebih efisien dengan filter yang tepat
5. ✅ **Data Integrity** - Mengikuti prinsip "single source of truth" (pic_pimpinan)

## Notes

- Stats di dashboard tetap menampilkan count yang akurat
- History/tracking tetap bisa diakses via menu "Tracking & History"
- Filter di index page tetap berfungsi normal
- Approved list menampilkan ALL permintaan yang pernah ditangani (untuk audit trail)
