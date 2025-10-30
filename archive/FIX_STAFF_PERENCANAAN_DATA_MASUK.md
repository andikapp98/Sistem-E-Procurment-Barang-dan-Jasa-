# Fix: Data Tidak Masuk ke Staff Perencanaan

## Masalah
Setelah Kepala Bidang approve dan kirim disposisi ke Staff Perencanaan, data belum masuk ke dashboard Staff Perencanaan.

## Root Cause
StaffPerencanaanController memiliki filter `whereHas('notaDinas')` yang terlalu ketat. Filter ini mengharuskan nota dinas berasal dari Direktur/Wakil Direktur, padahal workflow sebenarnya:

1. **Kepala Instalasi** → Buat permintaan + nota dinas
2. **Direktur** → Approve, buat **disposisi** (bukan nota dinas baru) ke Kepala Bidang
3. **Kepala Bidang** → Approve, buat **disposisi** (bukan nota dinas baru) ke Staff Perencanaan

Jadi nota dinas aslinya tetap dari Kepala Instalasi, tapi sudah ada chain disposisi dari Direktur → Kepala Bidang → Staff Perencanaan.

## Perbaikan yang Dilakukan

### File: `app/Http/Controllers/StaffPerencanaanController.php`

**1. Method dashboard() - Line 36-53**
```php
// BEFORE: Filter terlalu ketat
$permintaans = Permintaan::with(['user', 'notaDinas', 'notaDinas.disposisi'])
    ->where(function($q) use ($user) {
        $q->where('pic_pimpinan', 'Staff Perencanaan')
          ->orWhere('pic_pimpinan', $user->nama);
    })
    ->whereIn('status', ['disetujui', 'proses'])
    ->whereHas('notaDinas', function($q) {
        $q->where(function($query) {
            $query->where('dari', 'like', '%Direktur%')
                  ->orWhere('dari', 'like', '%Wakil Direktur%')
                  ->orWhere('kepada', 'like', '%Staff Perencanaan%');
        });
    })
    ->get();

// AFTER: Cukup cek pic_pimpinan dan status
$permintaans = Permintaan::with(['user', 'notaDinas', 'notaDinas.disposisi'])
    ->where(function($q) use ($user) {
        $q->where('pic_pimpinan', 'Staff Perencanaan')
          ->orWhere('pic_pimpinan', $user->nama);
    })
    ->whereIn('status', ['disetujui', 'proses'])
    ->get();
```

**2. Method index() - Line 89-105**
```php
// BEFORE: Query dengan filter nota dinas
$query = Permintaan::with(['user', 'notaDinas', 'notaDinas.disposisi'])
    ->where(function($q) use ($user) {
        $q->where('pic_pimpinan', 'Staff Perencanaan')
          ->orWhere('pic_pimpinan', $user->nama);
    })
    ->whereIn('status', ['disetujui', 'proses'])
    ->whereHas('notaDinas', function($q) {
        $q->where(function($query) {
            $query->where('dari', 'like', '%Direktur%')
                  ->orWhere('dari', 'like', '%Wakil Direktur%')
                  ->orWhere('kepada', 'like', '%Staff Perencanaan%');
        });
    });

// AFTER: Query sederhana
$query = Permintaan::with(['user', 'notaDinas', 'notaDinas.disposisi'])
    ->where(function($q) use ($user) {
        $q->where('pic_pimpinan', 'Staff Perencanaan')
          ->orWhere('pic_pimpinan', $user->nama);
    })
    ->whereIn('status', ['disetujui', 'proses']);
```

**3. Method show() - Line 149-159**
```php
// BEFORE: Validasi nota dinas
// Validasi tambahan: Pastikan permintaan sudah melalui alur dari Direktur/Wakil Direktur
$hasValidNotaDinas = $permintaan->notaDinas()
    ->where(function($q) {
        $q->where('dari', 'like', '%Direktur%')
          ->orWhere('dari', 'like', '%Wakil Direktur%')
          ->orWhere('kepada', 'like', '%Staff Perencanaan%');
    })
    ->exists();

$permintaan->load(['user', 'notaDinas.disposisi']);

// AFTER: Langsung load data
$permintaan->load(['user', 'notaDinas.disposisi']);
```

**4. Method createPerencanaan() - Line 161-181**
```php
// BEFORE: Validasi sebelum form
public function createPerencanaan(Permintaan $permintaan)
{
    $user = Auth::user();
    
    $hasValidNotaDinas = $permintaan->notaDinas()
        ->where(function($q) {
            $q->where('dari', 'like', '%Direktur%')
              ->orWhere('dari', 'like', '%Wakil Direktur%')
              ->orWhere('kepada', 'like', '%Staff Perencanaan%');
        })
        ->exists();
    
    if (!$hasValidNotaDinas) {
        return redirect()->route('staff-perencanaan.index')
            ->withErrors(['error' => 'Permintaan ini belum melalui proses approval yang lengkap dari Direktur/Wakil Direktur.']);
    }
    
    $permintaan->load(['user', 'notaDinas']);
    
    return Inertia::render('StaffPerencanaan/CreatePerencanaan', [
        'permintaan' => $permintaan,
    ]);
}

// AFTER: Hapus validasi
public function createPerencanaan(Permintaan $permintaan)
{
    $user = Auth::user();
    
    $permintaan->load(['user', 'notaDinas']);
    
    return Inertia::render('StaffPerencanaan/CreatePerencanaan', [
        'permintaan' => $permintaan,
    ]);
}
```

**5. Method uploadDokumen() - Line 607-627**
Sama seperti createPerencanaan(), hapus validasi nota dinas.

## Workflow yang Benar Sekarang

```
[Kepala Instalasi] → Buat Permintaan + Nota Dinas
   ↓
[Direktur] → Approve
   - Buat Disposisi ke Kepala Bidang
   - Update: pic_pimpinan = 'Kepala Bidang', status = 'proses'
   ↓
[Kepala Bidang] → Approve (deteksi disposisi dari Direktur)
   - Buat Disposisi ke Staff Perencanaan
   - Update: pic_pimpinan = 'Staff Perencanaan', status = 'disetujui'
   ↓
[Staff Perencanaan] → Terima Data ✅
   - Query: WHERE pic_pimpinan = 'Staff Perencanaan' AND status IN ('disetujui', 'proses')
   - Data MUNCUL di dashboard dan index
```

## Penjelasan
Sistem tracking menggunakan `pic_pimpinan` dan `status`, bukan melihat siapa pembuat nota dinas. Jadi cukup cek:
- `pic_pimpinan = 'Staff Perencanaan'` → Permintaan sedang di tangan Staff Perencanaan
- `status IN ('disetujui', 'proses')` → Permintaan sudah disetujui atau sedang diproses

Chain approval dilacak melalui tabel `disposisi`, bukan tabel `nota_dinas`.

## Files Modified
- `app/Http/Controllers/StaffPerencanaanController.php`
  - Line 36-53: Method dashboard()
  - Line 89-105: Method index()
  - Line 149-159: Method show()
  - Line 161-181: Method createPerencanaan()
  - Line 607-627: Method uploadDokumen()

## Status
✅ **FIXED** - Filter berlebihan dihapus, data sekarang akan masuk ke Staff Perencanaan
