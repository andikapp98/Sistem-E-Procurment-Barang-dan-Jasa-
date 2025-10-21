# NOTA DINAS USULAN - PAGU ANGGARAN (STAFF PERENCANAAN ONLY)

## Ringkasan Perubahan

Form Nota Dinas Usulan dengan field pagu anggaran **HANYA untuk Staff Perencanaan**. Kepala Instalasi tidak memerlukan form ini dan telah dihapus.

## Alasan

- Nota Dinas Usulan dengan pagu anggaran yang ditetapkan dibuat oleh **Staff Perencanaan**
- Kepala Instalasi hanya perlu approve/reject permintaan, tidak perlu buat nota dinas usulan
- Staff Perencanaan yang mengelola pagu anggaran dan membuat perencanaan pengadaan

## Yang Dihapus dari Kepala Instalasi

1. ❌ File: `resources/js/Pages/KepalaInstalasi/CreateNotaDinas.vue` - DELETED
2. ❌ Method: `KepalaInstalasiController::createNotaDinas()` - REMOVED
3. ❌ Method: `KepalaInstalasiController::storeNotaDinas()` - REMOVED
4. ❌ Route: `kepala-instalasi.nota-dinas.create` - REMOVED
5. ❌ Route: `kepala-instalasi.nota-dinas.store` - REMOVED

## Yang Tetap Ada untuk Staff Perencanaan

### 1. Database Migration
**File:** `database/migrations/2025_10_21_000000_add_budget_fields_to_nota_dinas_table.php`
- 18 kolom baru untuk pagu anggaran dan pajak
- ✅ Migration sudah dijalankan

### 2. Model NotaDinas
**File:** `app/Models/NotaDinas.php`
- Fillable fields mencakup semua field budget
- Casting untuk decimal dan date fields

### 3. Controller - StaffPerencanaanController
**File:** `app/Http/Controllers/StaffPerencanaanController.php`
- `createNotaDinas()` - Form nota dinas usulan
- `storeNotaDinas()` - Simpan dengan validasi lengkap
- Auto-generate nomor: `001/ND-SP/2025`

### 4. Routes
**File:** `routes/web.php`
```php
Route::get('/permintaan/{permintaan}/nota-dinas/create', [StaffPerencanaanController::class, 'createNotaDinas'])->name('nota-dinas.create');
Route::post('/permintaan/{permintaan}/nota-dinas', [StaffPerencanaanController::class, 'storeNotaDinas'])->name('nota-dinas.store');
```

### 5. Vue Component
**File:** `resources/js/Pages/StaffPerencanaan/CreateNotaDinas.vue`
- Form lengkap dengan 20 fields termasuk pagu anggaran
- Default "Dari": Staff Perencanaan
- Validasi client-side dan server-side

### 6. Show Page
**File:** `resources/js/Pages/StaffPerencanaan/Show.vue`
- Button: "Buat Nota Dinas Usulan"
- Link ke form nota dinas

## Field dalam Form Nota Dinas Usulan

### Field Wajib ⭐
1. **Tanggal** - Tanggal nota dinas
2. **Dari** - Default: "Staff Perencanaan"
3. **Kepada** - Dropdown: Direktur, Wakil Direktur, dll
4. **Pagu Anggaran** - Nilai pagu yang ditetapkan (format Rupiah)

### Field Optional
5. **Nomor** - Auto-generate jika kosong: `001/ND-SP/2025`
6. **Penerima** - Nama penerima
7. **Sifat** - Sangat Segera, Segera, Biasa, Rahasia
8. **Kode Program** - Kode program anggaran
9. **Kode Kegiatan** - Kode kegiatan anggaran
10. **Kode Rekening** - Kode rekening anggaran
11. **Uraian** - Detail pengadaan (textarea)
12. **PPh** - Pajak Penghasilan
13. **PPN** - Pajak Pertambahan Nilai
14. **PPh 21** - PPh Pasal 21
15. **PPh 4(2)** - PPh Pasal 4 ayat 2
16. **PPh 22** - PPh Pasal 22
17. **Unit Instalasi** - Unit yang mengajukan
18. **No Faktur Pajak** - Nomor faktur pajak
19. **Tanggal Faktur Pajak** - Tanggal faktur
20. **No Kwitansi** - Nomor kwitansi

## Workflow

### Kepala Instalasi:
1. Menerima permintaan dari unit
2. Review permintaan
3. **APPROVE** atau **REJECT** saja
4. ✅ Tidak perlu buat nota dinas usulan

### Staff Perencanaan:
1. Menerima permintaan yang sudah disetujui
2. **Buat Nota Dinas Usulan** dengan pagu anggaran
3. Isi semua detail anggaran dan pajak
4. Submit ke Direktur/pihak terkait
5. Koordinasi pengadaan

## Cara Menggunakan (Staff Perencanaan)

1. Login sebagai **Staff Perencanaan**
2. Buka permintaan yang statusnya "disetujui" atau "proses"
3. Klik button **"Buat Nota Dinas Usulan"**
4. Isi form:
   - Tanggal ⭐
   - Dari: Staff Perencanaan (sudah terisi) ⭐
   - Kepada: Pilih tujuan ⭐
   - Pagu Anggaran: Isi nilai ⭐
   - Field lain: Optional
5. Submit
6. Nota dinas tersimpan dengan pagu anggaran lengkap

## File yang Dimodifikasi/Dihapus

### Added/Modified:
1. ✅ `database/migrations/2025_10_21_000000_add_budget_fields_to_nota_dinas_table.php`
2. ✅ `app/Models/NotaDinas.php`
3. ✅ `app/Http/Controllers/StaffPerencanaanController.php`
4. ✅ `routes/web.php` (Staff Perencanaan routes)
5. ✅ `resources/js/Pages/StaffPerencanaan/CreateNotaDinas.vue`
6. ✅ `resources/js/Pages/StaffPerencanaan/Show.vue`

### Removed:
1. ❌ `resources/js/Pages/KepalaInstalasi/CreateNotaDinas.vue`
2. ❌ `KepalaInstalasiController::createNotaDinas()`
3. ❌ `KepalaInstalasiController::storeNotaDinas()`
4. ❌ `routes/web.php` (Kepala Instalasi nota-dinas routes)

## Status

✅ Migration berhasil dijalankan
✅ Model telah diupdate
✅ Controller Staff Perencanaan lengkap
✅ Routes Staff Perencanaan added
✅ Form Vue Staff Perencanaan created
✅ Show page Staff Perencanaan updated
✅ Kepala Instalasi nota dinas removed
✅ Dev server running

**Nota Dinas Usulan dengan pagu anggaran HANYA untuk Staff Perencanaan!**
