# Upload Dokumen KSO - View Aktif dan Lengkap

## Status
✅ **View Upload Dokumen KSO sudah AKTIF dan dapat digunakan!**

## Cara Kerja Upload Dokumen KSO

Upload dokumen KSO terintegrasi dalam form **Create KSO**. Ketika membuat KSO, otomatis akan upload 2 dokumen:
1. **PKS** (Perjanjian Kerja Sama)
2. **MoU** (Memorandum of Understanding)

## Cara Akses View Upload Dokumen

### 1. Dari Dashboard KSO
1. Login sebagai user dengan role 'kso'
2. Buka **Dashboard KSO** (`/kso/dashboard`)
3. Lihat di tabel "5 Permintaan Terbaru"
4. Jika belum ada KSO, akan ada tombol **"Upload Dokumen KSO"**

### 2. Dari Index/List Permintaan
1. Buka **Daftar Permintaan KSO** (`/kso`)
2. Lihat tabel permintaan
3. Kolom "Dokumen KSO" menampilkan status PKS & MoU
4. Kolom "Aksi" ada tombol **"Upload Dokumen KSO"** (dengan icon upload)
5. Klik tombol tersebut untuk membuka form upload

### 3. Dari Detail Permintaan
1. Buka detail permintaan (`/kso/permintaan/{id}`)
2. Jika belum ada KSO, akan ada tombol **"Buat Dokumen KSO"**
3. Klik untuk membuka form upload

## Route Upload Dokumen

```
GET /kso/permintaan/{permintaan}/create
POST /kso/permintaan/{permintaan}
```

**Route Names:**
- `kso.create` - Tampilkan form upload
- `kso.store` - Submit data upload

## Form Upload Dokumen KSO

### Field yang Harus Diisi:

**1. Informasi Dasar KSO:**
- No. KSO (required) - Nomor dokumen, contoh: KSO/001/XI/2025
- Tanggal KSO (required) - Minimal hari ini
- Pihak Pertama (read-only) - Auto-filled: RSUD Ibnu Sina Kabupaten Gresik
- Pihak Kedua (required) - Nama Vendor/Partner

**2. Detail Kerjasama:**
- Isi Kerjasama (required) - Textarea untuk detail kerjasama
- Nilai Kontrak (required) - Nilai dalam Rupiah (numeric)
- Keterangan (optional) - Catatan tambahan

**3. Upload Dokumen (WAJIB):**
- **Upload PKS** (required) - File Perjanjian Kerja Sama
  - Format: PDF, DOC, DOCX
  - Max size: 5MB
  - Drag & drop atau klik untuk browse
  
- **Upload MoU** (required) - File Memorandum of Understanding
  - Format: PDF, DOC, DOCX
  - Max size: 5MB
  - Drag & drop atau klik untuk browse

## Tombol Upload di Interface

### Di Index.vue (List Permintaan)
```vue
<Link :href="route('kso.create', { permintaan: permintaan.permintaan_id })" 
      class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700">
    <svg><!-- Upload Icon --></svg>
    Upload Dokumen KSO
</Link>
```

**Tampilan:**
- ✅ Tombol hijau dengan icon upload
- ✅ Text: "Upload Dokumen KSO"
- ✅ Hover effect (bg-green-700)
- ✅ Hanya muncul jika belum ada KSO

### Indicator Status Dokumen di Tabel

Kolom "Dokumen KSO" menampilkan:

**Jika Sudah Ada KSO:**
- ✅ **PKS Badge** (hijau) - Jika sudah upload
- ❌ **PKS Badge** (merah) - Jika belum upload
- ✅ **MoU Badge** (biru) - Jika sudah upload
- ❌ **MoU Badge** (merah) - Jika belum upload

**Jika Belum Ada KSO:**
- Badge kuning: "Belum Ada KSO"

## Workflow Setelah Upload

1. ✅ User mengisi semua field required
2. ✅ User upload file PKS (drag & drop atau browse)
3. ✅ User upload file MoU (drag & drop atau browse)
4. ✅ Klik "Simpan & Upload"
5. ✅ Form tervalidasi (frontend & backend)
6. ✅ File terupload ke server
   - PKS: `storage/app/public/kso/pks/PKS_{id}_{timestamp}.pdf`
   - MoU: `storage/app/public/kso/mou/MoU_{id}_{timestamp}.pdf`
7. ✅ Data KSO tersimpan di database
8. ✅ Status KSO = 'aktif'
9. ✅ **Auto-forward ke Bagian Pengadaan**
   - `pic_pimpinan` → 'Bagian Pengadaan'
   - Deskripsi permintaan terupdate
10. ✅ Activity log tercatat
11. ✅ Redirect ke dashboard dengan success message

## Perbaikan yang Dilakukan

### Before
```vue
<!-- Tombol text link biasa -->
<Link href="..." class="text-green-600 hover:text-green-900">
    Buat KSO
</Link>
```

### After
```vue
<!-- Tombol dengan background, icon upload, dan text jelas -->
<Link href="..." class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700">
    <svg><!-- Upload Icon --></svg>
    Upload Dokumen KSO
</Link>
```

**Improvements:**
- ✅ Background hijau yang mencolok
- ✅ Icon upload yang jelas
- ✅ Text eksplisit "Upload Dokumen KSO"
- ✅ Padding untuk tombol yang lebih besar
- ✅ Hover effect yang smooth

## Preview Upload Area

Form upload menggunakan area **drag & drop** yang user-friendly:

```
┌─────────────────────────────────────────────┐
│           📤 Upload Icon (Large)            │
│                                             │
│     Klik untuk upload atau drag and drop   │
│     PDF, DOC, DOCX (Max. 5MB)              │
│                                             │
│     ✓ nama_file.pdf (jika sudah dipilih)   │
└─────────────────────────────────────────────┘
```

**Features:**
- Border dashed yang berubah warna saat hover
- Icon upload besar di tengah
- Instruksi jelas
- Preview nama file setelah dipilih
- Checkmark hijau saat file valid

## Validasi Upload

### Frontend Validation:
- ✅ Field required tidak boleh kosong
- ✅ File type must be PDF, DOC, or DOCX
- ✅ File size max 5MB
- ✅ Tanggal minimal hari ini

### Backend Validation:
```php
'file_pks' => 'required|file|mimes:pdf,doc,docx|max:5120'
'file_mou' => 'required|file|mimes:pdf,doc,docx|max:5120'
```

### Error Handling:
- Error ditampilkan per field dengan text merah
- Loading state saat upload
- Tombol disabled saat processing
- Alert jika upload gagal

## Akses Download Dokumen

Setelah upload, dokumen bisa didownload dari:

**1. Detail Permintaan** (`/kso/permintaan/{id}`)
- Section "Dokumen KSO"
- Tombol "Download" untuk PKS
- Tombol "Download" untuk MoU

**2. List All KSO** (`/kso/list-all`)
- Tabel menampilkan semua KSO
- Link download untuk setiap dokumen

## Testing Checklist

### ✅ UI/UX
- [x] Tombol "Upload Dokumen KSO" terlihat jelas di Index
- [x] Icon upload yang jelas
- [x] Text eksplisit "Upload Dokumen KSO"
- [x] Background hijau yang mencolok
- [x] Hover effect yang smooth
- [x] Tombol hanya muncul jika belum ada KSO

### ✅ Functionality
- [x] Klik tombol membuka form Create KSO
- [x] Drag & drop file berfungsi
- [x] Browse file berfungsi
- [x] Preview nama file muncul
- [x] Validasi file type bekerja
- [x] Validasi file size bekerja
- [x] Submit berhasil
- [x] File tersimpan di storage
- [x] Data tersimpan di database
- [x] Auto-forward ke Pengadaan

### ✅ Build
- [x] No syntax errors
- [x] Build successful
- [x] Index.vue compiled
- [x] Create.vue compiled

## File yang Dimodifikasi

1. **resources/js/Pages/KSO/Index.vue**
   - Update tombol "Buat KSO" menjadi "Upload Dokumen KSO"
   - Tambah icon upload
   - Ubah dari text link menjadi button dengan background

2. **resources/js/Pages/KSO/Create.vue**
   - Sudah ada (form upload lengkap)
   - Drag & drop file upload
   - Validasi lengkap

3. **app/Http/Controllers/KsoController.php**
   - Method `create()` - Render form
   - Method `store()` - Handle upload & save

## URL Patterns

```
/kso                              → Index (List)
/kso/dashboard                    → Dashboard
/kso/permintaan/123               → Show Detail
/kso/permintaan/123/create        → Form Upload (CREATE VIEW)
```

## Kesimpulan

✅ **View Upload Dokumen KSO AKTIF**
✅ **Tombol Upload JELAS dan TERLIHAT**
✅ **Form Upload LENGKAP dengan Drag & Drop**
✅ **Validasi LENGKAP (Frontend & Backend)**
✅ **Auto-forward ke Pengadaan BERFUNGSI**
✅ **Build SUCCESSFUL tanpa error**

**Upload Dokumen KSO siap digunakan dengan interface yang user-friendly!** 🎉

## Update Log
- 2025-11-05: Update tombol di Index.vue menjadi lebih jelas
- 2025-11-05: Tambah icon upload dan background hijau
- 2025-11-05: Ubah text menjadi "Upload Dokumen KSO"
- 2025-11-05: Verifikasi build successful
- 2025-11-05: Dokumentasi lengkap upload workflow
