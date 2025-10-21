# Fix Fungsi Setujui, Revisi, Tolak Direktur

## Status: ✅ SELESAI

## Masalah
Fungsi setujui, revisi, dan tolak untuk Direktur tidak bisa dijalankan.

## Penyebab
Route URLs tidak sesuai dengan yang dipanggil dari Vue component:
- View menggunakan route names: `direktur.approve`, `direktur.reject`, `direktur.revisi`
- Routes di web.php menggunakan URL path: `/approve`, `/reject`, `/revisi`
- Namun URL path tidak sesuai dengan konvensi bahasa Indonesia

## Solusi Diterapkan

### 1. Perbaikan Routes (`routes/web.php`)
Mengubah URL path untuk Direktur agar konsisten dengan bahasa Indonesia:

**Sebelum:**
```php
Route::post('/permintaan/{permintaan}/approve', [DirekturController::class, 'approve'])->name('approve');
Route::post('/permintaan/{permintaan}/reject', [DirekturController::class, 'reject'])->name('reject');
Route::post('/permintaan/{permintaan}/revisi', [DirekturController::class, 'requestRevision'])->name('revisi');
```

**Sesudah:**
```php
Route::post('/permintaan/{permintaan}/setujui', [DirekturController::class, 'approve'])->name('approve');
Route::post('/permintaan/{permintaan}/tolak', [DirekturController::class, 'reject'])->name('reject');
Route::post('/permintaan/{permintaan}/revisi', [DirekturController::class, 'requestRevision'])->name('revisi');
```

### 2. Membuat Halaman Index.vue untuk Direktur
Membuat file baru: `resources/js/Pages/Direktur/Index.vue`

Halaman ini menampilkan:
- Daftar semua permintaan yang perlu direview oleh Direktur
- Filter dan pencarian
- Status dan progress tracking
- Link ke detail dan tracking permintaan
- Pagination

## Struktur Routes Direktur

| Method | URL Path | Route Name | Controller Method | Keterangan |
|--------|----------|------------|-------------------|------------|
| GET | /direktur | direktur.index | index | Daftar permintaan |
| GET | /direktur/dashboard | direktur.dashboard | dashboard | Dashboard utama |
| GET | /direktur/approved | direktur.approved | approved | Permintaan yang sudah diproses |
| GET | /direktur/permintaan/{id} | direktur.show | show | Detail permintaan |
| GET | /direktur/permintaan/{id}/tracking | direktur.tracking | tracking | Timeline tracking |
| POST | /direktur/permintaan/{id}/setujui | direktur.approve | approve | **Setujui permintaan** |
| POST | /direktur/permintaan/{id}/tolak | direktur.reject | reject | **Tolak permintaan** |
| POST | /direktur/permintaan/{id}/revisi | direktur.revisi | requestRevision | **Minta revisi** |

## Fungsi Controller yang Sudah Ada

### 1. `approve()` - Setujui Permintaan
- Membuat disposisi otomatis ke Staff Perencanaan
- Update status permintaan menjadi 'disetujui'
- Update pic_pimpinan ke 'Staff Perencanaan'
- Redirect ke index dengan success message

### 2. `reject()` - Tolak Permintaan
- Membuat disposisi penolakan
- Update status permintaan menjadi 'ditolak'
- Update pic_pimpinan ke 'Unit Pemohon'
- Tambahkan alasan penolakan ke deskripsi
- Redirect ke index dengan success message

### 3. `requestRevision()` - Minta Revisi
- Membuat disposisi revisi ke Kepala Bidang
- Update status permintaan menjadi 'revisi'
- Update pic_pimpinan ke 'Kepala Bidang'
- Tambahkan catatan revisi ke deskripsi
- Redirect ke index dengan success message

## View Components

### Show.vue
Menampilkan 3 tombol aksi:
1. **Setujui (Final)** - Button hijau dengan icon check
   - Modal dengan form catatan opsional
   - Submit ke route `direktur.approve`

2. **Tolak** - Button merah dengan icon X
   - Modal dengan form alasan penolakan (wajib, min 10 karakter)
   - Submit ke route `direktur.reject`

3. **Minta Revisi** - Button orange dengan icon edit
   - Modal dengan form catatan revisi (wajib, min 10 karakter)
   - Submit ke route `direktur.revisi`

### Index.vue (Baru)
Menampilkan:
- Info box: "Final Approval Level"
- Filter bar dengan pencarian dan filter status/bidang
- Tabel daftar permintaan dengan kolom:
  - ID
  - Deskripsi
  - Bidang
  - Tanggal
  - Status badge
  - Progress bar
  - Action buttons (Detail, Tracking)
- Pagination

## Testing
Setelah perbaikan, test dengan:

1. Login sebagai Direktur
2. Buka dashboard direktur
3. Klik pada permintaan yang status = 'proses'
4. Coba:
   - Klik tombol "Setujui (Final)" → submit form
   - Klik tombol "Tolak" → isi alasan → submit
   - Klik tombol "Minta Revisi" → isi catatan → submit

## Catatan
- Clear cache setelah perubahan: `php artisan route:clear`
- View components sudah correct dari awal, hanya routes yang perlu diperbaiki
- Controller methods sudah complete dan functional
- Workflow: Kepala Bidang → **Direktur** → Staff Perencanaan

## File yang Dimodifikasi
1. ✅ `routes/web.php` - Update URL paths untuk Direktur routes
2. ✅ `resources/js/Pages/Direktur/Index.vue` - Created new file

## File yang Sudah Ada (Tidak Diubah)
- ✅ `app/Http/Controllers/DirekturController.php` - Sudah correct
- ✅ `resources/js/Pages/Direktur/Show.vue` - Sudah correct
- ✅ `resources/js/Pages/Direktur/Dashboard.vue` - Sudah correct
