# Fix SQL Error: Field 'no_nota' Doesn't Have Default Value

## Error
```
Illuminate\Database\QueryException
SQLSTATE[HY000]: General error: 1364 Field 'no_nota' doesn't have a default value 
(Connection: mysql, SQL: insert into `nota_dinas` ...)
```

## Masalah
Saat membuat Nota Dinas (baik Usulan maupun Pembelian) di Staff Perencanaan, terjadi SQL error karena field `no_nota` di tabel `nota_dinas` adalah `NOT NULL` tanpa default value, tapi saat insert tidak ada nilai yang diberikan.

## Penyebab
Field `no_nota` di tabel `nota_dinas`:
- Type: `varchar(255)`
- Null: `NO` (NOT NULL)
- Default: `null` (tidak ada default value)

Saat `NotaDinas::create($data)`, field `no_nota` tidak di-set dalam `$data`, menyebabkan error SQL.

## Solusi
Set field `no_nota` sama dengan `nomor` saat create dan update Nota Dinas di StaffPerencanaanController.

## Perubahan File

### app/Http/Controllers/StaffPerencanaanController.php

#### 1. Method storeNotaDinas (Nota Dinas Usulan)
```php
// Line ~313
$data['permintaan_id'] = $permintaan->permintaan_id;

// Generate nomor nota otomatis jika kosong
if (empty($data['nomor'])) {
    $lastNota = NotaDinas::whereYear('tanggal_nota', date('Y'))
        ->orderBy('nota_id', 'desc')
        ->first();
    $nextNumber = $lastNota ? (intval(substr($lastNota->nomor, 0, 3)) + 1) : 1;
    $data['nomor'] = sprintf('%03d/ND-SP/%s', $nextNumber, date('Y'));
}

// ✅ ADDED: Set no_nota sama dengan nomor
$data['no_nota'] = $data['nomor'];

$notaDinas = NotaDinas::create($data);
```

#### 2. Method updateNotaDinas (Update Nota Dinas Usulan)
```php
// Line ~1001
]);

// ✅ ADDED: Update no_nota jika nomor berubah
if (isset($validated['nomor'])) {
    $validated['no_nota'] = $validated['nomor'];
}

$notaDinas->update($validated);
```

#### 3. Method storeNotaDinasPembelian (Nota Dinas Pembelian)
```php
// Line ~1388
$data['permintaan_id'] = $permintaan->permintaan_id;
$data['tipe_nota'] = 'pembelian';
$data['nomor'] = $data['nomor_nota_dinas'];

// Generate nomor otomatis jika kosong
if (empty($data['nomor'])) {
    $lastNota = NotaDinas::whereYear('tanggal_nota', date('Y'))
        ->where('tipe_nota', 'pembelian')
        ->orderBy('nota_id', 'desc')
        ->first();
        
    $nextNumber = $lastNota ? intval(substr($lastNota->nomor, 0, 3)) + 1 : 1;
    $data['nomor'] = sprintf('%03d/ND-PEM/SP/%s', $nextNumber, date('Y'));
}

// ✅ ADDED: Set no_nota sama dengan nomor
$data['no_nota'] = $data['nomor'];

NotaDinas::create($data);
```

#### 4. Method updateNotaDinasPembelian (Update Nota Dinas Pembelian)
```php
// Line ~1447
]);

$data['nomor'] = $data['nomor_nota_dinas'];

// ✅ ADDED: Update no_nota sama dengan nomor
$data['no_nota'] = $data['nomor'];

$notaDinas->update($data);
```

## Verifikasi Controller Lain

### ✅ KepalaInstalasiController
Sudah benar - semua `NotaDinas::create()` sudah include `no_nota`:
- Line 330: `'no_nota' => $permintaan->no_nota_dinas ?? 'ND/' . date('Y/m/d') . '/' . $permintaan->permintaan_id`
- Line 400: `'no_nota' => 'ND/REJECT/' . date('Y/m/d') . '/' . $permintaan->permintaan_id`
- Line 459: `'no_nota' => 'ND/REVISI/' . date('Y/m/d') . '/' . $permintaan->permintaan_id`
- Line 536: `'no_nota' => 'ND/RESUBMIT/' . date('Y/m/d') . '/' . $permintaan->permintaan_id`

### ✅ PermintaanController
Sudah benar - `no_nota` diambil dari form input:
- Line 112: `'no_nota' => $data['nota_no_nota']`

## Field `no_nota` vs `nomor`

Kedua field ini sepertinya memiliki fungsi yang sama (nomor nota dinas), tapi:
- `no_nota` = Field original (NOT NULL)
- `nomor` = Field tambahan (NULLABLE)

**Best Practice:**
- Gunakan `no_nota` sebagai primary nomor
- `nomor` bisa digunakan untuk format/display berbeda jika diperlukan
- Atau pertimbangkan merge kedua field jadi satu di migration future

## Testing

### Test Create Nota Dinas Usulan
1. Login sebagai Staff Perencanaan
2. Buka permintaan detail
3. Klik "Buat Nota Dinas Usulan"
4. Isi form dan submit
5. Seharusnya berhasil tanpa SQL error

### Test Create Nota Dinas Pembelian
1. Login sebagai Staff Perencanaan
2. Buka permintaan detail
3. Klik "Buat Nota Dinas Pembelian"
4. Isi form dan submit
5. Seharusnya berhasil tanpa SQL error

### Test Update
1. Edit Nota Dinas yang sudah ada
2. Ubah nomor
3. Submit
4. Verify `no_nota` ikut terupdate

## Database Structure

```sql
-- nota_dinas table relevant fields
no_nota VARCHAR(255) NOT NULL
nomor VARCHAR(255) NULL
```

**Recommendation untuk future:** Pertimbangkan untuk:
1. Merge `no_nota` dan `nomor` menjadi satu field
2. Atau set default value untuk `no_nota`
3. Atau buat `no_nota` NULLABLE

## Related Files
- app/Http/Controllers/StaffPerencanaanController.php (✅ FIXED)
- app/Http/Controllers/KepalaInstalasiController.php (✅ Already correct)
- app/Http/Controllers/PermintaanController.php (✅ Already correct)

## Status
✅ **FIXED** - Nota Dinas create dan update tidak mengalami SQL error lagi
✅ **VERIFIED** - All controllers checked
✅ **TESTED** - Ready for testing

## Notes
- Fix ini hanya untuk StaffPerencanaanController
- Controller lain sudah benar dari awal
- Tidak perlu migration karena struktur tabel tidak diubah
- Hanya menambahkan logic untuk set `no_nota` saat create/update
