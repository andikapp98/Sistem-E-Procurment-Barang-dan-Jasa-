# Dokumen KSO - Fitur dan Perbaikan

## Overview
Dokumen ini menjelaskan fitur lengkap Bagian KSO (Kerja Sama Operasional) yang telah diperbaiki dan ditingkatkan untuk memastikan semua form bisa disave dengan lengkap.

## Perbaikan yang Dilakukan

### 1. Form Create KSO (CreateKSO.vue)
**Perbaikan:**
- ✅ Menambahkan field `isi_kerjasama` (required) - Detail kerjasama yang akan dilakukan
- ✅ Menambahkan field `nilai_kontrak` (required) - Nilai kontrak dalam Rupiah
- ✅ Memastikan validasi lengkap untuk semua field
- ✅ Upload file PKS dan MoU dengan drag & drop
- ✅ Loading indicator saat proses upload

**Field Lengkap:**
1. **No. KSO** (required) - Nomor dokumen KSO, contoh: KSO/001/X/2025
2. **Tanggal KSO** (required) - Tanggal pembuatan KSO (minimal hari ini)
3. **Pihak Pertama** (readonly) - RSUD Ibnu Sina Kabupaten Gresik
4. **Pihak Kedua** (required) - Nama Vendor/Partner
5. **Isi Kerjasama** (required) - Detail kerjasama yang dilakukan
6. **Nilai Kontrak** (required) - Nilai kontrak dalam Rupiah
7. **Upload PKS** (required) - File Perjanjian Kerja Sama (PDF/DOC/DOCX, max 5MB)
8. **Upload MoU** (required) - File Memorandum of Understanding (PDF/DOC/DOCX, max 5MB)
9. **Keterangan** (optional) - Catatan tambahan

### 2. Controller KsoController.php
**Perbaikan:**
- ✅ Update method `store()` dengan validasi field baru
- ✅ Update method `update()` dengan validasi field baru
- ✅ Tambah activity logging untuk audit trail
- ✅ Auto-forward ke Bagian Pengadaan setelah upload dokumen
- ✅ Update deskripsi permintaan dengan info KSO lengkap

**Validasi Backend:**
```php
$data = $request->validate([
    'no_kso' => 'required|string|unique:kso,no_kso',
    'tanggal_kso' => 'required|date',
    'pihak_pertama' => 'required|string',
    'pihak_kedua' => 'required|string',
    'isi_kerjasama' => 'required|string',
    'nilai_kontrak' => 'required|numeric|min:0',
    'file_pks' => 'required|file|mimes:pdf,doc,docx|max:5120',
    'file_mou' => 'required|file|mimes:pdf,doc,docx|max:5120',
    'keterangan' => 'nullable|string',
]);
```

### 3. File Upload Handling
**Fitur:**
- File PKS disimpan di: `storage/app/public/kso/pks/PKS_{permintaan_id}_{timestamp}.ext`
- File MoU disimpan di: `storage/app/public/kso/mou/MoU_{permintaan_id}_{timestamp}.ext`
- Format yang diterima: PDF, DOC, DOCX
- Maksimal ukuran file: 5MB per file
- Nama file otomatis dengan timestamp untuk mencegah duplicate

## Workflow Lengkap

### Step 1: Bagian KSO Menerima Permintaan
- Permintaan datang dari Staff Perencanaan dengan `pic_pimpinan` = 'Bagian KSO'
- Status permintaan: 'proses' atau 'disetujui'
- Permintaan sudah memiliki dokumen perencanaan lengkap

### Step 2: Membuat Dokumen KSO
1. Klik "Buat Dokumen KSO" dari halaman Show permintaan
2. Isi semua field required:
   - No. KSO
   - Tanggal KSO
   - Pihak Kedua
   - Isi Kerjasama
   - Nilai Kontrak
3. Upload file PKS (Perjanjian Kerja Sama)
4. Upload file MoU (Memorandum of Understanding)
5. Tambah keterangan jika diperlukan
6. Klik "Simpan & Upload"

### Step 3: Auto-Forward ke Pengadaan
**Setelah dokumen KSO berhasil disimpan:**
1. ✅ Data KSO tersimpan di database
2. ✅ File PKS dan MoU terupload ke storage
3. ✅ Status KSO = 'aktif'
4. ✅ Permintaan auto-forward ke Bagian Pengadaan:
   - `pic_pimpinan` berubah menjadi 'Bagian Pengadaan'
   - `status` tetap 'proses'
   - Deskripsi ditambah info KSO lengkap
5. ✅ Activity log tercatat
6. ✅ Redirect ke dashboard dengan success message

## Data yang Tersimpan

### Tabel `kso`
```sql
kso_id: 1 (auto increment)
perencanaan_id: 123 (FK ke tabel perencanaans)
no_kso: 'KSO/001/X/2025'
tanggal_kso: '2025-11-05'
pihak_pertama: 'RSUD Ibnu Sina Kabupaten Gresik'
pihak_kedua: 'PT. ABC Medical Supplies'
isi_kerjasama: 'Kerjasama pengadaan alat kesehatan...'
nilai_kontrak: 500000000
file_pks: 'kso/pks/PKS_456_1730825000.pdf'
file_mou: 'kso/mou/MoU_456_1730825000.pdf'
keterangan: 'Catatan tambahan'
status: 'aktif'
created_at: '2025-11-05 10:30:00'
updated_at: '2025-11-05 10:30:00'
```

### Tabel `permintaans` (Updated)
```sql
permintaan_id: 456
status: 'proses'
pic_pimpinan: 'Bagian Pengadaan'
deskripsi: '... [DOKUMEN KSO LENGKAP - DIKIRIM KE PENGADAAN]
Tanggal: 05/11/2025 10:30
No. KSO: KSO/001/X/2025
Pihak Kedua: PT. ABC Medical Supplies
Nilai Kontrak: Rp 500.000.000
File PKS & MoU telah diupload
Workflow: Staff Perencanaan → Bagian KSO → Bagian Pengadaan'
```

### Tabel `user_activity_logs`
```sql
-- Log create KSO
action: 'create'
module: 'kso'
description: 'Membuat dokumen KSO #KSO/001/X/2025 untuk permintaan #456...'

-- Log forward
action: 'forward'
module: 'permintaan'
description: 'Mengirim permintaan #456 ke Bagian Pengadaan setelah dokumen KSO lengkap'
```

## Fitur Tambahan

### Edit KSO
- Dapat mengedit semua field kecuali file upload
- Validasi unique untuk no_kso (kecuali milik sendiri)
- Activity log saat update
- Auto-forward jika status berubah ke 'aktif' atau 'selesai'

### View KSO
- Lihat detail lengkap dokumen KSO
- Download file PKS dan MoU
- Tracking status KSO

### List All KSO
- Halaman khusus untuk melihat semua KSO yang pernah dibuat
- Filter by status (aktif, selesai, batal, draft)
- Search by no_kso, pihak_kedua, pihak_pertama
- Pagination

## Routes

```php
// Dashboard KSO
GET  /kso/dashboard

// Daftar permintaan untuk KSO
GET  /kso

// List semua KSO
GET  /kso/list-all

// Detail permintaan
GET  /kso/permintaan/{permintaan}

// Create KSO
GET  /kso/permintaan/{permintaan}/create
POST /kso/permintaan/{permintaan}

// Edit KSO
GET  /kso/permintaan/{permintaan}/kso/{kso}/edit
PUT  /kso/permintaan/{permintaan}/kso/{kso}

// Delete KSO
DELETE /kso/permintaan/{permintaan}/kso/{kso}
```

## Error Handling

### Validasi Form
- Field required tidak boleh kosong
- No. KSO harus unique
- Tanggal tidak boleh kurang dari hari ini
- File harus PDF/DOC/DOCX
- File max 5MB
- Nilai kontrak harus angka positif

### Otorisasi
- Hanya role 'kso' yang bisa create/update KSO
- 403 Forbidden jika user tidak memiliki akses

### File Upload
- Error jika file terlalu besar
- Error jika format file tidak didukung
- Error jika storage tidak tersedia

## Testing Checklist

### ✅ Form Create KSO
- [x] Semua field required terisi
- [x] Validasi no_kso unique
- [x] Validasi tanggal minimal hari ini
- [x] Validasi nilai_kontrak numeric
- [x] Upload file PKS berhasil
- [x] Upload file MoU berhasil
- [x] Form submit berhasil
- [x] Redirect dengan success message

### ✅ Auto-Forward ke Pengadaan
- [x] Status permintaan berubah
- [x] pic_pimpinan berubah ke 'Bagian Pengadaan'
- [x] Deskripsi permintaan terupdate dengan info KSO
- [x] Activity log tercatat (create + forward)

### ✅ File Storage
- [x] File tersimpan di storage/app/public/kso/
- [x] File accessible via public URL
- [x] Nama file unik dengan timestamp

### ✅ Edit KSO
- [x] Form edit terisi dengan data existing
- [x] Update berhasil
- [x] Validasi unique no_kso (kecuali milik sendiri)
- [x] Activity log update tercatat

## Akses Bagian Pengadaan

Setelah KSO selesai, Bagian Pengadaan dapat:
1. Melihat permintaan di dashboard mereka
2. Filter permintaan dengan `pic_pimpinan` = 'Bagian Pengadaan'
3. Akses semua dokumen:
   - Nota Dinas
   - DPP
   - HPS
   - Spesifikasi Teknis
   - **Dokumen KSO (PKS & MoU)** ← BARU
4. Download file PKS dan MoU
5. Melanjutkan proses pengadaan

## Kesimpulan

✅ **Form KSO Lengkap** - Semua field required tersedia
✅ **Validasi Lengkap** - Backend dan frontend validation
✅ **File Upload Berfungsi** - PKS dan MoU bisa diupload
✅ **Auto-Forward ke Pengadaan** - Otomatis setelah save
✅ **Activity Logging** - Audit trail lengkap
✅ **Error Handling** - Komprehensif untuk semua skenario

**Fitur dokumen KSO siap digunakan dengan lengkap!**

## Update Log
- 2025-11-05: Perbaikan dan peningkatan fitur KSO
  - Tambah field isi_kerjasama dan nilai_kontrak
  - Perbaiki validasi lengkap
  - Tambah activity logging
  - Update deskripsi permintaan saat forward
  - Dokumentasi lengkap
