# Fitur Lampiran Nota Dinas untuk Kepala Instalasi

## Deskripsi
Kepala Instalasi sekarang bisa melihat dan mengakses lampiran nota dinas (seperti URL eksternal atau file).

## Perubahan yang Dilakukan

### 1. Routes (routes/web.php)
Ditambahkan 2 route baru dalam group kepala-instalasi:

```php
// Routes untuk cetak dan lampiran nota dinas
Route::get('/permintaan/{permintaan}/cetak-nota-dinas', [KepalaInstalasiController::class, 'cetakNotaDinas'])->name('cetak-nota-dinas');
Route::get('/nota-dinas/{notaDinas}/lampiran', [KepalaInstalasiController::class, 'lihatLampiran'])->name('lampiran');
```

**Named Routes:**
- `kepala-instalasi.cetak-nota-dinas` - untuk cetak nota dinas (backend support)
- `kepala-instalasi.lampiran` - untuk lihat/download lampiran

### 2. Controller (KepalaInstalasiController.php)
Ditambahkan 2 method baru:

#### Method `cetakNotaDinas()`
- Menerima parameter `Permintaan $permintaan`
- Load nota dinas terkait permintaan
- Return view `cetak.nota-dinas` untuk cetak
- Error handling jika nota dinas tidak ditemukan

#### Method `lihatLampiran()`
- Menerima parameter `NotaDinas $notaDinas`
- Validasi keberadaan lampiran
- Support 2 jenis lampiran:
  - **URL (http/https)** - redirect ke URL seperti `http://dadada/`
  - **File path** - return file untuk download/display
- Error handling untuk lampiran tidak ditemukan

### 3. Frontend (Show.vue)
Diupdate 1 bagian di `resources/js/Pages/KepalaInstalasi/Show.vue`:

#### Link Lampiran
Route lampiran diubah dari `nota-dinas.lampiran` (route admin) ke `kepala-instalasi.lampiran`:
```vue
<!-- BEFORE -->
:href="route('nota-dinas.lampiran', nota.nota_id)"

<!-- AFTER -->
:href="route('kepala-instalasi.lampiran', nota.nota_id)"
```

**CATATAN PENTING:** Tombol **Setujui**, **Minta Revisi**, dan **Tolak** tetap dipertahankan di halaman ini.

## Tampilan di Halaman Detail Permintaan
- **Link Lampiran**: Muncul di dalam detail setiap nota dinas jika ada lampiran (tombol indigo dengan icon paperclip "Lihat Lampiran")
- **Action Buttons**: Setujui, Minta Revisi, Tolak (tidak diubah, tetap ada)

## Fitur yang Didapat
✅ Lihat/download lampiran nota dinas  
✅ Support lampiran berbasis URL (seperti `http://dadada/`)  
✅ Support lampiran berbasis file lokal  
✅ Error handling yang sama dengan Admin  
✅ **Tetap mempertahankan semua tombol action (Approve, Revisi, Reject)**

## Testing
Route telah diverifikasi dengan command:
```bash
php artisan route:list --name=kepala-instalasi
```

Output:
- ✅ `GET kepala-instalasi/permintaan/{permintaan}/cetak-nota-dinas`
- ✅ `GET kepala-instalasi/nota-dinas/{notaDinas}/lampiran`

## URL Lampiran
Kepala Instalasi sekarang bisa mengakses lampiran dengan URL seperti:
- URL eksternal: `http://dadada/` atau URL lainnya
- File lokal: Disimpan di `storage/app/public/`

Kedua jenis lampiran akan bekerja dengan baik melalui route `kepala-instalasi.lampiran`.

## Catatan
- Menggunakan method yang sama dengan Admin (logic identik)
- Tidak ada perubahan pada model atau database
- UI tombol Approve/Revisi/Reject tidak diubah sama sekali
- Hanya memperbaiki route lampiran agar sesuai dengan role kepala instalasi
- Route menggunakan prefix `kepala-instalasi` untuk isolasi role yang baik
