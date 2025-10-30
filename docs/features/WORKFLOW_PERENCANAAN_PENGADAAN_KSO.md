# WORKFLOW UPDATE: PERENCANAAN → PENGADAAN → KSO

## Ringkasan Perubahan

Workflow telah diubah dengan 2 fitur utama:

### 1. Form DPP - Hapus Field yang Tidak Diperlukan
Field berikut telah dihapus dari form DPP di Staff Perencanaan:
- ❌ Jenis Kontrak
- ❌ Kualifikasi  
- ❌ Jangka Waktu Pelaksanaan

### 2. Alur Workflow Baru dengan Validasi Dokumen Lengkap
```
Staff Perencanaan (Semua Dokumen Lengkap) → Bagian Pengadaan → Bagian KSO
```

**Persyaratan untuk forward ke Pengadaan:**
- ✅ Nota Dinas
- ✅ DPP (Dokumen Persiapan Pengadaan)
- ✅ HPS (Harga Perkiraan Satuan)
- ✅ Nota Dinas Pembelian
- ✅ Spesifikasi Teknis

## Perubahan yang Dilakukan

### 1. Staff Perencanaan Controller (`StaffPerencanaanController.php`)

**A. Method `storeDPP()` - Update workflow (line 441-465)**

DPP tidak lagi langsung mengirim ke Pengadaan. Sekarang hanya membuat dokumen DPP saja.

**B. Method baru: `forwardToPengadaan()` (line 603-720)**

Method khusus untuk mengirim ke Bagian Pengadaan setelah **SEMUA dokumen lengkap**:

```php
public function forwardToPengadaan(Request $request, Permintaan $permintaan)
{
    // Validasi semua dokumen harus lengkap
    $hasNotaDinas = ...;
    $hasDPP = ...;
    $hasHPS = ...;
    $hasNotaDinasPembelian = ...;
    $hasSpesifikasiTeknis = ...;
    
    if (!$hasNotaDinas || !$hasDPP || !$hasHPS || 
        !$hasNotaDinasPembelian || !$hasSpesifikasiTeknis) {
        return redirect()->back()->withErrors(['error' => '...']);
    }
    
    // Buat disposisi ke Bagian Pengadaan
    // Update pic_pimpinan menjadi 'Bagian Pengadaan'
    // Catat dalam deskripsi semua dokumen yang sudah lengkap
}
```

### 2. View Staff Perencanaan (`Show.vue`)

**File:** `resources/js/Pages/StaffPerencanaan/Show.vue`

**Perubahan:**

A. **Computed Property baru:**
```javascript
const allDocumentsComplete = computed(() => {
    return props.hasNotaDinas && 
           props.hasDPP && 
           props.hasHPS && 
           props.hasNotaDinasPembelian && 
           props.hasSpesifikasiTeknis;
});
```

B. **Alert Box Hijau** - Muncul jika semua dokumen lengkap:
- Menampilkan checklist dokumen yang sudah dibuat
- Tombol besar "Kirim ke Bagian Pengadaan"
- Desain menarik dengan gradient hijau

C. **Alert Box Kuning** - Muncul jika dokumen belum lengkap:
- Menampilkan daftar dokumen yang belum dibuat
- Mencegah user mengirim sebelum lengkap

D. **Modal Konfirmasi** - Untuk forward ke Pengadaan:
- Form untuk menambahkan catatan
- Konfirmasi sebelum mengirim
- Loading state saat proses

### 3. Form DPP (`CreateDPP.vue`)

**File:** `resources/js/Pages/StaffPerencanaan/CreateDPP.vue`

**Section yang dihapus:**
- Section "Kontrak dan Pelaksanaan" (70 baris kode)
- Form fields: Jenis Kontrak, Kualifikasi, Jangka Waktu Pelaksanaan
- Validasi frontend dan backend untuk 3 field tersebut

### 4. Pengadaan Controller (`PengadaanController.php`)

**File:** `app/Http/Controllers/PengadaanController.php`

**Method baru:** `forwardToKSO()` (line 285-340)

Method untuk meneruskan dari Pengadaan ke KSO (tahap kedua workflow):

```php
public function forwardToKSO(Request $request, Permintaan $permintaan)
{
    // Validasi akses
    // Buat disposisi ke Bagian KSO
    // Update pic_pimpinan menjadi 'Bagian KSO'
    // Catat dalam deskripsi: [FORWARDED: Pengadaan → KSO]
}
```

### 5. View Pengadaan (`Show.vue`)

**File:** `resources/js/Pages/Pengadaan/Show.vue` - **BARU**

Halaman detail dengan:
- Informasi permintaan lengkap
- Data KSO (jika ada)
- Data Pengadaan (jika ada)
- **Tombol "Forward ke Bagian KSO"** dengan modal

### 6. Routes (`routes/web.php`)

**File:** `routes/web.php`

**Route baru ditambahkan:**

```php
// Staff Perencanaan - Forward dengan validasi dokumen lengkap
Route::post('/permintaan/{permintaan}/forward-to-pengadaan', 
    [StaffPerencanaanController::class, 'forwardToPengadaan'])
    ->name('staff-perencanaan.forward-to-pengadaan');

// Pengadaan - Forward ke KSO
Route::post('/permintaan/{permintaan}/forward-to-kso', 
    [PengadaanController::class, 'forwardToKSO'])
    ->name('pengadaan.forward-to-kso');
```

## Cara Menggunakan Workflow Baru

### Langkah-langkah Detail:

#### 1. **Staff Perencanaan:**

a. Login dan buka permintaan yang sudah disetujui Direktur
b. Buat dokumen-dokumen berikut **satu per satu**:
   - **Nota Dinas** - Nota usulan perencanaan
   - **DPP** - Dokumen Persiapan Pengadaan (tanpa Jenis Kontrak, Kualifikasi, Jangka Waktu)
   - **HPS** - Harga Perkiraan Satuan dengan item detail
   - **Nota Dinas Pembelian** - Nota khusus pembelian
   - **Spesifikasi Teknis** - Detail spesifikasi barang/jasa

c. Setelah **SEMUA dokumen lengkap**, akan muncul:
   - ✅ **Alert hijau besar** dengan tombol "Kirim ke Bagian Pengadaan"
   
d. Klik tombol → Isi catatan (opsional) → Submit

e. Sistem akan:
   - Validasi semua dokumen lengkap
   - Buat disposisi ke Bagian Pengadaan
   - Update `pic_pimpinan` menjadi "Bagian Pengadaan"
   - Catat di deskripsi dengan checklist dokumen

#### 2. **Bagian Pengadaan:**

a. Login dan lihat permintaan yang masuk dari Staff Perencanaan
b. Buka detail permintaan (klik permintaan)
c. Review semua dokumen yang sudah dibuat
d. Proses pengadaan sesuai kebutuhan
e. Klik tombol **"Forward ke Bagian KSO"**
f. Tambahkan catatan (opsional)
g. Submit untuk mengirim ke **Bagian KSO**

#### 3. **Bagian KSO:**

a. Login dan lihat permintaan yang masuk dari Pengadaan
b. Lanjutkan proses KSO sesuai prosedur

## UI/UX Improvements

### Alert Box Hijau (Dokumen Lengkap):
```
┌─────────────────────────────────────────────────────┐
│ ✓ Semua Dokumen Sudah Lengkap!                      │
│                                                      │
│ Nota Dinas, DPP, HPS, Nota Dinas Pembelian, dan    │
│ Spesifikasi Teknis telah dibuat. Anda dapat         │
│ melanjutkan ke Bagian Pengadaan.                    │
│                                        [KIRIM →]    │
└─────────────────────────────────────────────────────┘
```

### Alert Box Kuning (Dokumen Belum Lengkap):
```
┌─────────────────────────────────────────────────────┐
│ ⚠ Dokumen Belum Lengkap                             │
│                                                      │
│ Silakan lengkapi dokumen berikut:                   │
│ • Nota Dinas                                         │
│ • DPP (Dokumen Persiapan Pengadaan)                 │
│ • HPS (Harga Perkiraan Satuan)                      │
└─────────────────────────────────────────────────────┘
```

## Testing

### Test Scenario 1: Dokumen Belum Lengkap

1. Login sebagai Staff Perencanaan
2. Pilih permintaan dengan status "disetujui"
3. Buat hanya beberapa dokumen (misal: Nota Dinas + DPP saja)
4. **Expected:** Alert kuning muncul, tombol kirim TIDAK muncul
5. Coba akses route forward secara manual
6. **Expected:** Error "Dokumen belum lengkap"

### Test Scenario 2: Semua Dokumen Lengkap

1. Login sebagai Staff Perencanaan
2. Pilih permintaan
3. Buat **semua dokumen**: Nota Dinas, DPP, HPS, Nota Dinas Pembelian, Spesifikasi Teknis
4. **Expected:** Alert hijau muncul dengan tombol "Kirim ke Bagian Pengadaan"
5. Klik tombol → Isi catatan → Submit
6. **Expected:** 
   - Success message
   - `pic_pimpinan` berubah menjadi "Bagian Pengadaan"
   - Deskripsi mencatat semua dokumen lengkap
   - Disposisi baru dibuat

### Test Scenario 3: Forward dari Pengadaan ke KSO

1. Logout dan login sebagai user Bagian Pengadaan
2. Buka permintaan yang diterima dari Staff Perencanaan
3. Klik "Forward ke Bagian KSO"
4. **Expected:**
   - `pic_pimpinan` berubah menjadi "Bagian KSO"
   - Deskripsi mencatat forward
   - Success message

## Catatan Penting

### Keuntungan Workflow Baru:

✅ **Validasi Otomatis** - Mencegah kirim dokumen tidak lengkap
✅ **User Friendly** - Alert visual yang jelas
✅ **Tracking Lengkap** - Semua perubahan tercatat di deskripsi
✅ **Fleksibilitas** - Staff Perencanaan bisa buat dokumen sesuai urutan yang diinginkan
✅ **Disposisi Terpisah** - Setiap perpindahan memiliki disposisi tersendiri

### Database Impact:

- ⚠️ **Tidak ada perubahan struktur database**
- ✅ Field `jenis_kontrak`, `kualifikasi`, `jangka_waktu_pelaksanaan` tetap ada (nullable)
- ✅ Backward compatible dengan data lama
- ✅ Tidak perlu migrasi

### Perbedaan dengan Workflow Lama:

| Aspek | Lama | Baru |
|-------|------|------|
| Trigger Forward | Buat DPP saja | Semua dokumen lengkap |
| Validasi | Tidak ada | Ada (5 dokumen wajib) |
| UI Indicator | Tidak ada | Alert box hijau/kuning |
| Modal Konfirmasi | Tidak ada | Ada dengan form catatan |
| Form DPP | 3 field extra | Field minimal (lebih sederhana) |

## File yang Diubah/Dibuat

### Modified:
1. ✅ `app/Http/Controllers/StaffPerencanaanController.php`
   - Update `storeDPP()` - hapus auto-forward
   - Tambah `forwardToPengadaan()` - forward dengan validasi

2. ✅ `app/Http/Controllers/PengadaanController.php`
   - Tambah `forwardToKSO()`

3. ✅ `routes/web.php`
   - Tambah route `staff-perencanaan.forward-to-pengadaan`
   - Tambah route `pengadaan.forward-to-kso`

4. ✅ `resources/js/Pages/StaffPerencanaan/CreateDPP.vue`
   - Hapus section "Kontrak dan Pelaksanaan"
   - Hapus 3 form fields
   - Update validasi

5. ✅ `resources/js/Pages/StaffPerencanaan/Show.vue`
   - Tambah computed `allDocumentsComplete`
   - Tambah alert hijau (dokumen lengkap)
   - Tambah alert kuning (dokumen belum lengkap)
   - Tambah modal konfirmasi forward
   - Import Modal component

### Created:
6. ✅ `resources/js/Pages/Pengadaan/Show.vue` (BARU)
   - Halaman detail permintaan
   - Tombol "Forward ke Bagian KSO"
   - Modal konfirmasi

7. ✅ `WORKFLOW_PERENCANAAN_PENGADAAN_KSO.md` (DOKUMENTASI)

## Diagram Workflow

```
┌─────────────────────┐
│ Staff Perencanaan   │
│                     │
│ 1. Nota Dinas       │
│ 2. DPP              │
│ 3. HPS              │
│ 4. ND Pembelian     │
│ 5. Spek Teknis      │
│                     │
│ [Semua Lengkap?]    │
│   ✓ YES             │
└──────────┬──────────┘
           │
           │ Forward (dengan validasi)
           ▼
┌─────────────────────┐
│ Bagian Pengadaan    │
│                     │
│ - Review dokumen    │
│ - Proses pengadaan  │
│                     │
│ [Forward ke KSO]    │
└──────────┬──────────┘
           │
           │ Forward
           ▼
┌─────────────────────┐
│ Bagian KSO          │
│                     │
│ - Proses KSO        │
│ - Selesai           │
└─────────────────────┘
```

---

**Tanggal:** 30 Oktober 2025  
**Status:** ✅ Completed  
**Tested:** Ready for Testing