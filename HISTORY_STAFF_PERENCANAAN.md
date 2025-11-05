# History Perencanaan - Staff Perencanaan

## Overview
Fitur History Perencanaan memungkinkan Staff Perencanaan untuk melihat riwayat aktivitas lengkap dari sebuah permintaan pengadaan, menampilkan semua aktivitas dari tahap perencanaan hingga siap diteruskan ke Staff Pengadaan.

## Workflow
```
Staff Perencanaan → Staff Pengadaan
```

Setelah Staff Perencanaan menyelesaikan semua dokumen perencanaan, permintaan akan diteruskan ke Staff Pengadaan untuk proses pengadaan barang/jasa.

## Fitur History

### 1. Informasi Permintaan
- No. Permintaan
- Bidang Pemohon
- Status Saat Ini
- PIC (Person In Charge) Saat Ini

### 2. Workflow Info
- Menampilkan alur proses dari Staff Perencanaan ke Staff Pengadaan
- Penjelasan tentang proses dan dokumen yang diperlukan

### 3. Riwayat Aktivitas
Menampilkan timeline lengkap aktivitas yang dilakukan pada permintaan, termasuk:
- **Create/Upload** - Pembuatan atau upload dokumen (hijau)
- **Update/Edit** - Perubahan data atau dokumen (biru)
- **Delete/Reject** - Penghapusan atau penolakan (merah)
- **Approve/Forward/Submit** - Persetujuan atau penerusan (ungu)
- **View/Show** - Aktivitas melihat data (kuning)
- **Aktivitas lainnya** - Aktivitas umum (abu-abu)

Setiap aktivitas menampilkan:
- Deskripsi aktivitas
- Nama user yang melakukan
- Role user
- Tanggal dan waktu
- Module terkait
- Tipe relasi (Permintaan, Perencanaan, HPS, SpesifikasiTeknis)

## Cara Mengakses

### Dari Halaman Detail Permintaan
1. Login sebagai Staff Perencanaan
2. Buka detail permintaan
3. Klik tombol **"Lihat History"** di bagian header (tombol indigo/ungu dengan ikon jam)

### URL Pattern
```
/staff-perencanaan/permintaan/{permintaan_id}/history
```

### Route Name
```php
route('staff-perencanaan.history', $permintaan_id)
```

## File Terkait

### Controller
- `app/Http/Controllers/StaffPerencanaanController.php`
  - Method: `showHistory(Permintaan $permintaan)`

### View
- `resources/js/Pages/StaffPerencanaan/History.vue`

### Route
- `routes/web.php`
  ```php
  Route::get('/permintaan/{permintaan}/history', [StaffPerencanaanController::class, 'showHistory'])
      ->name('history');
  ```

### Model
- `app/Models/UserActivityLog.php` - Model untuk log aktivitas
- `app/Models/Permintaan.php` - Model permintaan
- `app/Models/Perencanaan.php` - Model perencanaan
- `app/Models/Hps.php` - Model HPS
- `app/Models/SpesifikasiTeknis.php` - Model spesifikasi teknis

## Data yang Ditampilkan

History menampilkan aktivitas dari:
1. **Permintaan** - Aktivitas terkait permintaan utama
2. **Perencanaan** - Aktivitas pembuatan/edit perencanaan
3. **HPS** - Aktivitas dokumen Harga Perkiraan Sendiri
4. **Spesifikasi Teknis** - Aktivitas dokumen spesifikasi teknis

## Tombol Navigasi
- **Kembali ke Detail** - Kembali ke halaman detail permintaan
- **Lihat Semua Permintaan** - Ke halaman daftar permintaan Staff Perencanaan

## Keamanan
- Hanya dapat diakses oleh user yang sudah login
- Hanya menampilkan aktivitas yang relevan dengan permintaan tertentu
- Terintegrasi dengan middleware auth dan verified

## Penggunaan
History ini berguna untuk:
- Audit trail - Melacak siapa yang melakukan apa dan kapan
- Troubleshooting - Membantu menemukan masalah dalam proses
- Dokumentasi - Mencatat semua perubahan yang terjadi
- Transparansi - Memberikan visibilitas penuh atas proses perencanaan

## Update Log
- 2025-11-05: Initial creation - History view untuk Staff Perencanaan
