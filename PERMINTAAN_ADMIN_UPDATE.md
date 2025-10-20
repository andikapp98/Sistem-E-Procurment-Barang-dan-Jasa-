# Update Fitur Permintaan untuk Role Admin

## Perubahan yang Diimplementasikan

### 1. Auto-set Status "Diajukan" pada Pembuatan Permintaan
**File: `resources/js/Pages/Permintaan/Create.vue`**
- ✅ Menghapus dropdown select status dari form pembuatan permintaan
- ✅ Status otomatis di-set ke "diajukan" ketika admin membuat permintaan
- ✅ Field status sekarang menggunakan input hidden

**File: `app/Http/Controllers/PermintaanController.php` (method `store`)**
- ✅ Menambahkan validasi di backend: jika status kosong, auto-set ke "diajukan"

### 2. Edit Hanya Tersedia untuk Status "Ditolak" (Revisi)
**File: `app/Http/Controllers/PermintaanController.php`**
- ✅ Method `edit`: Menambahkan validasi - hanya bisa mengakses halaman edit jika status = "ditolak"
- ✅ Method `update`: Menambahkan validasi - hanya bisa update data jika status = "ditolak"
- ✅ Redirect dengan pesan error jika mencoba edit permintaan dengan status selain "ditolak"

**File: `resources/js/Pages/Permintaan/Index.vue`**
- ✅ Update fungsi `canEdit()`: Hanya return true jika user adalah admin DAN status = "ditolak"
- ✅ Tombol edit hanya muncul di tabel jika kondisi terpenuhi
- ✅ Menambahkan tooltip "Edit (Revisi)" untuk memperjelas

**File: `resources/js/Pages/Permintaan/Show.vue`**
- ✅ Update fungsi `canEdit()`: Hanya return true jika user adalah admin DAN status = "ditolak"
- ✅ Tombol edit di detail page hanya muncul jika status = "ditolak"
- ✅ Menampilkan alert merah khusus untuk permintaan yang ditolak

### 3. Delete Hanya Tersedia untuk Status "Ditolak"
**File: `app/Http/Controllers/PermintaanController.php` (method `destroy`)**
- ✅ Menambahkan validasi - hanya bisa delete jika status = "ditolak"
- ✅ Redirect dengan pesan error jika mencoba delete permintaan dengan status selain "ditolak"

**File: `resources/js/Pages/Permintaan/Index.vue`**
- ✅ Menambahkan fungsi `canDelete()`: Hanya return true jika user adalah admin DAN status = "ditolak"
- ✅ Tombol delete hanya muncul di tabel jika kondisi terpenuhi

**File: `resources/js/Pages/Permintaan/Show.vue`**
- ✅ Menambahkan fungsi `canDelete()`: Hanya return true jika user adalah admin DAN status = "ditolak"
- ✅ Tombol delete di detail page hanya muncul jika status = "ditolak"

## Ringkasan Business Rules

### Status Permintaan
1. **Diajukan** - Status awal otomatis ketika admin membuat permintaan
2. **Proses** - Permintaan sedang diproses
3. **Disetujui** - Permintaan disetujui
4. **Ditolak** - Permintaan ditolak (butuh revisi)

### Aksi yang Diizinkan Berdasarkan Status

| Status | View | Edit | Delete | Keterangan |
|--------|------|------|--------|------------|
| Diajukan | ✅ | ❌ | ❌ | Permintaan baru diajukan |
| Proses | ✅ | ❌ | ❌ | Sedang diproses |
| Disetujui | ✅ | ❌ | ❌ | Sudah disetujui |
| **Ditolak** | ✅ | ✅ | ✅ | **Dapat diedit & dihapus (revisi)** |

## File yang Dimodifikasi

1. **Backend (Controller)**
   - `app/Http/Controllers/PermintaanController.php`
     - Method: `store()`, `edit()`, `update()`, `destroy()`

2. **Frontend (Vue Components)**
   - `resources/js/Pages/Permintaan/Create.vue`
   - `resources/js/Pages/Permintaan/Index.vue`
   - `resources/js/Pages/Permintaan/Show.vue`

## Testing Checklist

### Create Permintaan
- [ ] Buka form create permintaan
- [ ] Pastikan tidak ada dropdown status
- [ ] Submit form
- [ ] Verifikasi status otomatis = "diajukan"

### Edit Permintaan
- [ ] Coba edit permintaan dengan status "diajukan" → Harus ditolak
- [ ] Coba edit permintaan dengan status "proses" → Harus ditolak
- [ ] Coba edit permintaan dengan status "disetujui" → Harus ditolak
- [ ] Ubah status permintaan ke "ditolak"
- [ ] Coba edit permintaan dengan status "ditolak" → Harus berhasil
- [ ] Tombol edit hanya muncul untuk permintaan dengan status "ditolak"

### Delete Permintaan
- [ ] Coba hapus permintaan dengan status "diajukan" → Harus ditolak
- [ ] Coba hapus permintaan dengan status "proses" → Harus ditolak
- [ ] Coba hapus permintaan dengan status "disetujui" → Harus ditolak
- [ ] Ubah status permintaan ke "ditolak"
- [ ] Coba hapus permintaan dengan status "ditolak" → Harus berhasil
- [ ] Tombol delete hanya muncul untuk permintaan dengan status "ditolak"

### UI/UX
- [ ] Alert merah muncul di halaman detail untuk permintaan yang ditolak
- [ ] Tombol "Edit & Ajukan Ulang" berwarna merah untuk permintaan ditolak
- [ ] Tooltip menunjukkan "Edit (Revisi)" pada tombol edit
- [ ] Pesan error yang jelas jika mencoba aksi yang tidak diizinkan

## Catatan Tambahan

1. **Keamanan**: Validasi dilakukan di backend dan frontend untuk mencegah bypass
2. **User Experience**: Alert dan pesan error yang jelas membantu user memahami flow
3. **Status Flow**: Status "ditolak" berfungsi sebagai trigger untuk revisi
4. **Konsistensi**: Logika yang sama diterapkan di Index, Show, dan Controller

## Deployment

Setelah testing selesai, jalankan:
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---
**Tanggal Update**: 2025-01-20
**Developer**: AI Assistant
**Status**: ✅ Implemented & Ready for Testing
