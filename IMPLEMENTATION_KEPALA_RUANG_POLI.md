# Implementasi Kepala Ruang dan Kepala Poli - Input Permission

## Ringkasan Perubahan

Kepala Ruang (IRNA) dan Kepala Poli (IRJA) sekarang memiliki hak akses yang sama dengan Admin untuk melakukan input permintaan baru.

## File yang Dibuat

### 1. KepalaRuangController.php
**Lokasi:** `app/Http/Controllers/KepalaRuangController.php`

**Fungsi:**
- Controller untuk Kepala Ruang (Instalasi Rawat Inap - IRNA)
- Dapat membuat, melihat, mengedit, dan menghapus permintaan
- Hanya dapat mengakses permintaan dari unit kerjanya sendiri
- Filter berdasarkan `unit_kerja` user

**Method yang Tersedia:**
- `dashboard()` - Dashboard dengan statistik permintaan
- `index()` - List permintaan dengan filter
- `create()` - Form input permintaan baru
- `store()` - Simpan permintaan baru
- `show()` - Detail permintaan
- `edit()` - Form edit permintaan
- `update()` - Update permintaan
- `destroy()` - Hapus permintaan
- `tracking()` - Tracking status permintaan
- `cetakNotaDinas()` - Cetak nota dinas
- `lihatLampiran()` - Lihat lampiran nota dinas

### 2. KepalaPoliController.php
**Lokasi:** `app/Http/Controllers/KepalaPoliController.php`

**Fungsi:**
- Controller untuk Kepala Poli (Instalasi Rawat Jalan - IRJA)
- Identik dengan KepalaRuangController
- Dapat membuat, melihat, mengedit, dan menghapus permintaan
- Hanya dapat mengakses permintaan dari unit kerjanya sendiri
- Filter berdasarkan `unit_kerja` user

**Method yang Tersedia:**
- Sama seperti KepalaRuangController

## File yang Dimodifikasi

### 1. routes/web.php
**Perubahan:**
- Menambahkan import controller baru:
  ```php
  use App\Http\Controllers\KepalaRuangController;
  use App\Http\Controllers\KepalaPoliController;
  ```

- Menambahkan route group untuk Kepala Ruang:
  ```php
  Route::middleware(['auth', 'verified'])
      ->prefix('kepala-ruang')
      ->name('kepala-ruang.')
      ->group(function () {
          // CRUD operations
      });
  ```

- Menambahkan route group untuk Kepala Poli:
  ```php
  Route::middleware(['auth', 'verified'])
      ->prefix('kepala-poli')
      ->name('kepala-poli.')
      ->group(function () {
          // CRUD operations
      });
  ```

### 2. app/Http/Middleware/RedirectBasedOnRole.php
**Perubahan:**
- Menambahkan redirect untuk `kepala_ruang` ke dashboard mereka
- Menambahkan redirect untuk `kepala_poli` ke dashboard mereka
- Update switch case pada `/dashboard` dan `/permintaan`

## Routes yang Tersedia

### Kepala Ruang Routes
| Method | URI | Action | Route Name |
|--------|-----|--------|------------|
| GET | /kepala-ruang/dashboard | dashboard | kepala-ruang.dashboard |
| GET | /kepala-ruang | index | kepala-ruang.index |
| GET | /kepala-ruang/create | create | kepala-ruang.create |
| POST | /kepala-ruang | store | kepala-ruang.store |
| GET | /kepala-ruang/permintaan/{id} | show | kepala-ruang.show |
| GET | /kepala-ruang/permintaan/{id}/edit | edit | kepala-ruang.edit |
| PUT | /kepala-ruang/permintaan/{id} | update | kepala-ruang.update |
| DELETE | /kepala-ruang/permintaan/{id} | destroy | kepala-ruang.destroy |
| GET | /kepala-ruang/permintaan/{id}/tracking | tracking | kepala-ruang.tracking |
| GET | /kepala-ruang/permintaan/{id}/cetak-nota-dinas | cetakNotaDinas | kepala-ruang.cetak-nota-dinas |
| GET | /kepala-ruang/nota-dinas/{id}/lampiran | lihatLampiran | kepala-ruang.lampiran |

### Kepala Poli Routes
| Method | URI | Action | Route Name |
|--------|-----|--------|------------|
| GET | /kepala-poli/dashboard | dashboard | kepala-poli.dashboard |
| GET | /kepala-poli | index | kepala-poli.index |
| GET | /kepala-poli/create | create | kepala-poli.create |
| POST | /kepala-poli | store | kepala-poli.store |
| GET | /kepala-poli/permintaan/{id} | show | kepala-poli.show |
| GET | /kepala-poli/permintaan/{id}/edit | edit | kepala-poli.edit |
| PUT | /kepala-poli/permintaan/{id} | update | kepala-poli.update |
| DELETE | /kepala-poli/permintaan/{id} | destroy | kepala-poli.destroy |
| GET | /kepala-poli/permintaan/{id}/tracking | tracking | kepala-poli.tracking |
| GET | /kepala-poli/permintaan/{id}/cetak-nota-dinas | cetakNotaDinas | kepala-poli.cetak-nota-dinas |
| GET | /kepala-poli/nota-dinas/{id}/lampiran | lihatLampiran | kepala-poli.lampiran |

## Security & Access Control

### Kepala Ruang (IRNA)
- **Role:** `kepala_ruang`
- **Akses:** Hanya permintaan dari `unit_kerja` yang sama
- **Contoh Unit Kerja:**
  - Ruang Anggrek
  - Ruang Bougenville
  - Ruang Cempaka
  - Ruang Dahlia
  - dll (semua ruang IRNA)

### Kepala Poli (IRJA)
- **Role:** `kepala_poli`
- **Akses:** Hanya permintaan dari `unit_kerja` yang sama
- **Contoh Unit Kerja:**
  - Poli Bedah
  - Poli Gigi
  - Poli Kulit Kelamin
  - Poli Penyakit Dalam
  - dll (semua poli IRJA)

### Validasi Akses
Setiap method yang mengakses permintaan tertentu akan melakukan validasi:
```php
if ($permintaan->user->unit_kerja !== $user->unit_kerja) {
    abort(403, 'Anda tidak memiliki akses ke permintaan ini');
}
```

## Fitur yang Sama dengan Admin

1. **Input Permintaan Baru**
   - Form create dengan validasi lengkap
   - Auto-set `user_id`, `bidang`, `tanggal_permintaan`, dan `status`
   - Membuat Nota Dinas bersamaan dengan Permintaan

2. **Edit Permintaan**
   - Hanya bisa edit permintaan dari unit kerjanya sendiri
   - Form edit dengan data existing

3. **Hapus Permintaan**
   - Hanya bisa hapus permintaan dari unit kerjanya sendiri
   - Soft delete atau hard delete (tergantung model)

4. **View & Tracking**
   - Melihat detail permintaan
   - Tracking timeline permintaan
   - Cetak Nota Dinas
   - Lihat lampiran

## Langkah Selanjutnya

### 1. Buat View/Frontend Components
Perlu dibuat folder dan file React/Inertia untuk:
- `resources/js/Pages/KepalaRuang/`
  - Dashboard.jsx
  - Index.jsx
  - Create.jsx
  - Edit.jsx
  - Show.jsx
  - Tracking.jsx
  - CetakNotaDinas.jsx
  - LampiranNotaDinas.jsx

- `resources/js/Pages/KepalaPoli/`
  - Dashboard.jsx
  - Index.jsx
  - Create.jsx
  - Edit.jsx
  - Show.jsx
  - Tracking.jsx
  - CetakNotaDinas.jsx
  - LampiranNotaDinas.jsx

### 2. Update Database (Jika Diperlukan)
Pastikan ada user dengan role `kepala_ruang` dan `kepala_poli`:
```sql
-- Contoh insert user kepala ruang
INSERT INTO users (name, email, password, role, jabatan, unit_kerja)
VALUES ('Kepala Ruang Anggrek', 'kepala.anggrek@example.com', 
        '$2y$10$...', 'kepala_ruang', 'Kepala Ruang', 'Anggrek');

-- Contoh insert user kepala poli
INSERT INTO users (name, email, password, role, jabatan, unit_kerja)
VALUES ('Kepala Poli Bedah', 'kepala.bedah@example.com', 
        '$2y$10$...', 'kepala_poli', 'Kepala Poli', 'Poli Bedah');
```

### 3. Testing
Test semua fitur untuk memastikan:
- ✅ Kepala Ruang bisa login dan redirect ke dashboard mereka
- ✅ Kepala Poli bisa login dan redirect ke dashboard mereka
- ✅ Keduanya bisa membuat permintaan baru
- ✅ Keduanya hanya bisa melihat permintaan dari unit kerja mereka
- ✅ Keduanya bisa edit/hapus permintaan yang mereka buat
- ✅ Validasi akses bekerja dengan baik (403 jika akses permintaan unit lain)

## Catatan Penting

1. **Perbedaan dengan Admin:**
   - Admin bisa melihat SEMUA permintaan dari semua unit
   - Kepala Ruang/Poli hanya bisa melihat permintaan dari unit kerjanya

2. **Perbedaan dengan Kepala Instalasi:**
   - Kepala Instalasi (IGD, IRJA, IRNA) menerima dan mereview permintaan dari semua unit di bawahnya
   - Kepala Ruang/Poli hanya mengelola permintaan dari ruang/poli mereka sendiri

3. **Auto-fill Data:**
   - `user_id` → dari Auth::id()
   - `bidang` → dari Auth::user()->unit_kerja
   - `tanggal_permintaan` → now() jika kosong
   - `status` → 'diajukan' jika kosong

4. **Validation:**
   - Semua field yang required harus diisi
   - Nota Dinas dibuat bersamaan dengan Permintaan
   - `klasifikasi_permintaan` harus salah satu dari: Medis, Non Medis, Penunjang

## Verifikasi Routes

Jalankan command berikut untuk memverifikasi routes sudah terdaftar:
```bash
php artisan route:list --name=kepala-ruang
php artisan route:list --name=kepala-poli
```

## Update Log

**Tanggal:** 2025-11-01
**Developer:** AI Assistant
**Status:** Backend Implementation Complete ✅

**Yang Sudah Dikerjakan:**
- ✅ KepalaRuangController.php dibuat
- ✅ KepalaPoliController.php dibuat
- ✅ Routes ditambahkan di web.php
- ✅ Middleware RedirectBasedOnRole diupdate
- ✅ Dokumentasi dibuat

**Yang Perlu Dikerjakan:**
- ⏳ Frontend views (React/Inertia components)
- ⏳ Database seeding untuk user kepala_ruang dan kepala_poli
- ⏳ Testing dan debugging
