# Perbaikan Error: Call to undefined method App\Models\Perencanaan::addEagerConstraints()

## Masalah
Error `BadMethodCallException: Call to undefined method App\Models\Perencanaan::addEagerConstraints()` terjadi saat mengakses halaman yang melakukan eager loading relationship `perencanaan`.

## Root Cause
Di file `app/Models/Permintaan.php`, method `perencanaan()` mengembalikan `->latest()->first()` yang mengembalikan model instance atau null, bukan relationship instance (HasManyThrough).

Laravel membutuhkan relationship method untuk mengembalikan relationship instance agar bisa melakukan eager loading dengan method `addEagerConstraints()`.

## File yang Diperbaiki

### app/Models/Permintaan.php

**Sebelum:**
```php
public function perencanaan()
{
    return $this->hasManyThrough(
        Perencanaan::class,
        Disposisi::class,
        'nota_id',       // Foreign key on disposisi table
        'disposisi_id',  // Foreign key on perencanaan table
        'permintaan_id', // Local key on permintaan table
        'disposisi_id'   // Local key on disposisi table
    )->latest()->first();  // ❌ WRONG: Mengembalikan model instance
}
```

**Sesudah:**
```php
public function perencanaan()
{
    return $this->hasManyThrough(
        Perencanaan::class,
        Disposisi::class,
        'nota_id',       // Foreign key on disposisi table
        'disposisi_id',  // Foreign key on perencanaan table
        'permintaan_id', // Local key on permintaan table
        'disposisi_id'   // Local key on disposisi table
    )->latest();  // ✅ CORRECT: Mengembalikan relationship instance
}
```

## Cara Penggunaan

### Eager Loading (di Controller)
```php
// ✅ Benar - untuk eager loading
$permintaan = Permintaan::with('perencanaan')->find($id);
```

### Mengambil Satu Record
Jika ingin mengambil satu record saja:

```php
// ✅ Benar - memanggil method dulu, baru first()
$perencanaan = $permintaan->perencanaan()->first();

// atau mengambil collection
$perencanaan = $permintaan->perencanaan;  // Collection
$firstPerencanaan = $permintaan->perencanaan->first();  // Model pertama
```

## Testing
```bash
# Clear cache
php artisan optimize:clear
php artisan config:cache

# Test eager loading
php artisan tinker
>>> use App\Models\Permintaan;
>>> $p = Permintaan::with(['user', 'notaDinas', 'disposisi', 'perencanaan'])->find(34);
>>> echo $p->permintaan_id;
Success: 34
```

## Related Files Checked
- ✅ `app/Models/Disposisi.php` - OK (hasOne)
- ✅ `app/Models/Pengadaan.php` - OK (belongsTo)
- ✅ `app/Models/Kso.php` - OK (belongsTo)
- ✅ `app/Http/Controllers/KsoController.php` - OK (menggunakan `->perencanaan()->first()`)
- ✅ `app/Http/Controllers/PengadaanController.php` - OK (menggunakan `->perencanaan()->first()`)

## Notes
- Relationship method harus selalu return relationship instance (HasMany, HasOne, BelongsTo, dll)
- Jangan chain method seperti `->first()`, `->get()`, `->count()` di return statement relationship
- Jika perlu single record, panggil method tersebut di luar relationship: `$model->relationship()->first()`
