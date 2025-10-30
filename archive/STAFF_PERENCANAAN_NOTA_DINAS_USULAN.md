# STAFF PERENCANAAN NOTA DINAS USULAN - COMPLETE

## Ringkasan Perubahan

Staff Perencanaan sekarang memiliki form Nota Dinas Usulan yang lengkap dengan field pagu anggaran yang telah ditetapkan, sama seperti form Kepala Instalasi.

## Perubahan yang Dilakukan

### 1. Controller - StaffPerencanaanController.php

#### Method Baru:
- **`createNotaDinas()`** - Menampilkan form nota dinas usulan
- **`storeNotaDinas()`** - Menyimpan nota dinas usulan dengan validasi lengkap

**Fitur:**
- Validasi semua field pagu anggaran
- Auto-generate nomor nota jika kosong (format: 001/ND-SP/2025)
- Default perihal otomatis jika tidak diisi
- Update status permintaan ke 'proses'
- Redirect ke detail permintaan setelah berhasil

### 2. Routes - web.php

**Route Baru:**
```php
Route::get('/permintaan/{permintaan}/nota-dinas/create', [StaffPerencanaanController::class, 'createNotaDinas'])->name('nota-dinas.create');
Route::post('/permintaan/{permintaan}/nota-dinas', [StaffPerencanaanController::class, 'storeNotaDinas'])->name('nota-dinas.store');
```

### 3. Vue Component - CreateNotaDinas.vue

**Location:** `resources/js/Pages/StaffPerencanaan/CreateNotaDinas.vue`

**Features:**
- Form lengkap dengan semua field pagu anggaran
- Default value "Staff Perencanaan" untuk field "Dari"
- Pilihan tujuan: Direktur, Wakil Direktur, Kepala Bagian Keuangan, Bagian KSO, Bagian Pengadaan
- Validasi client-side untuk field wajib
- Submit ke route `staff-perencanaan.nota-dinas.store`

### 4. Show.vue - Action Button

**Perubahan:**
- Mengganti component `GenerateNotaDinas` dengan Link langsung
- Button baru: "Buat Nota Dinas Usulan" dengan warna teal (#028174)
- Link ke route `staff-perencanaan.nota-dinas.create`
- Remove unused import `GenerateNotaDinas`

## Field-Field dalam Form

### Informasi Dasar
1. **Tanggal** ⭐ (Wajib)
2. **Nomor** (Auto-generate jika kosong)
3. **Penerima**
4. **Dari** ⭐ (Wajib - Default: "Staff Perencanaan")
5. **Kepada** ⭐ (Wajib - Dropdown)
6. **Sifat** (Dropdown: Sangat Segera, Segera, Biasa, Rahasia)

### Kode Anggaran
7. **Kode Program**
8. **Kode Kegiatan**
9. **Kode Rekening**

### Detail Pengadaan
10. **Uraian** (Textarea)
11. **Pagu Anggaran** ⭐ (Wajib - Format Rupiah)

### Pajak
12. **PPh**
13. **PPN**
14. **PPh 21**
15. **PPh 4(2)**
16. **PPh 22**

### Unit & Dokumen
17. **Unit Instalasi** (Default dari user yang mengajukan)
18. **No Faktur Pajak**
19. **Tanggal Faktur Pajak**
20. **No Kwitansi**

## Auto-Generate Nomor Nota

Format: `001/ND-SP/2025`
- Prefix: 3 digit angka sequential
- Middle: ND-SP (Nota Dinas - Staff Perencanaan)
- Suffix: Tahun

Sistem akan otomatis mencari nomor terakhir di tahun yang sama dan increment +1.

## Validasi

### Server-Side (Controller):
- `tanggal_nota`: required, date
- `dari`: required, string
- `kepada`: required, string
- `pagu_anggaran`: required, numeric, min:0
- `sifat`: nullable, hanya menerima nilai tertentu
- Semua field pajak: nullable, numeric, min:0

### Client-Side (Vue):
- Field "Dari" harus diisi
- Field "Kepada" harus dipilih
- Tanggal nota harus diisi
- Pagu anggaran harus diisi dan > 0

## Cara Menggunakan

1. Login sebagai Staff Perencanaan
2. Buka daftar permintaan di `/staff-perencanaan`
3. Klik permintaan yang ingin diproses
4. Klik button **"Buat Nota Dinas Usulan"**
5. Isi form dengan lengkap (minimal field wajib)
6. Submit form
7. Nota dinas tersimpan dan status permintaan berubah ke "proses"

## Perbedaan dengan Kepala Instalasi

| Fitur | Kepala Instalasi | Staff Perencanaan |
|-------|------------------|-------------------|
| Default "Dari" | Unit Kerja User | "Staff Perencanaan" |
| Pilihan "Kepada" | Direktur, Wakil Direktur, Kepala Bagian | Direktur, Wakil Direktur, Kepala Bagian Keuangan, Bagian KSO, Bagian Pengadaan |
| Format Nomor | Manual | Auto-generate (001/ND-SP/2025) |
| Route | kepala-instalasi.nota-dinas.* | staff-perencanaan.nota-dinas.* |

## File yang Dimodifikasi

1. ✅ `app/Http/Controllers/StaffPerencanaanController.php` - Added createNotaDinas & storeNotaDinas methods
2. ✅ `routes/web.php` - Added GET & POST routes for nota dinas
3. ✅ `resources/js/Pages/StaffPerencanaan/CreateNotaDinas.vue` - Created form component
4. ✅ `resources/js/Pages/StaffPerencanaan/Show.vue` - Updated action button

## Testing

### Test Flow:
1. ✅ Route accessible: `/staff-perencanaan/permintaan/{id}/nota-dinas/create`
2. ✅ Form displays with all fields
3. ✅ Client-side validation works
4. ✅ Submit to server with validation
5. ✅ Auto-generate nomor nota
6. ✅ Save to database with all budget fields
7. ✅ Redirect to show page with success message

## Status

✅ Controller methods created
✅ Routes added
✅ Vue component created
✅ Show page updated
✅ Form validation implemented
✅ Auto-generate nomor nota
✅ Integration complete

**Staff Perencanaan dapat membuat Nota Dinas Usulan dengan pagu anggaran lengkap!**
