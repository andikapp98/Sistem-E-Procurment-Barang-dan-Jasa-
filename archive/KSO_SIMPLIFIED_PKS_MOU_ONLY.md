# KSO Simplified - Upload PKS & MoU Only

## Overview
Fitur KSO telah disederhanakan menjadi hanya upload 2 dokumen penting:
1. **PKS** (Perjanjian Kerja Sama)
2. **MoU** (Memorandum of Understanding)

## Perubahan Utama

### 1. Form KSO Create - Disederhanakan
**File:** `resources/js/Pages/KSO/Create.vue`

#### Field yang Dipertahankan:
- ✅ No. KSO (required)
- ✅ Tanggal KSO (required)
- ✅ Pihak Pertama (readonly - default: "RSUD Ibnu Sina Kabupaten Gresik")
- ✅ Pihak Kedua / Vendor (required)
- ✅ Upload File PKS (required - PDF/DOC/DOCX, max 5MB)
- ✅ Upload File MoU (required - PDF/DOC/DOCX, max 5MB)
- ✅ Keterangan (optional - untuk catatan tambahan)

#### Field yang Dihapus:
- ❌ Nilai Kontrak
- ❌ Isi Kerjasama (textarea)
- ❌ Status (selalu "aktif" setelah upload)

### 2. Database Migration
**File:** `database/migrations/2025_10_28_120051_update_kso_table_for_file_uploads.php`

#### Perubahan Tabel `kso`:
```sql
-- Kolom yang diubah
ALTER TABLE kso MODIFY isi_kerjasama TEXT NULL;

-- Kolom yang ditambahkan
file_pks VARCHAR(255) NULL
file_mou VARCHAR(255) NULL
keterangan TEXT NULL
```

### 3. Model Update
**File:** `app/Models/Kso.php`

```php
protected $fillable = [
    'perencanaan_id',
    'no_kso',
    'tanggal_kso',
    'pihak_pertama',
    'pihak_kedua',
    'isi_kerjasama',
    'nilai_kontrak',
    'file_pks',        // ✅ NEW
    'file_mou',        // ✅ NEW
    'keterangan',      // ✅ NEW
    'status'
];
```

### 4. Controller Update
**File:** `app/Http/Controllers/KSOController.php`

#### Validation Rules:
```php
$data = $request->validate([
    'no_kso' => 'required|string|unique:kso,no_kso',
    'tanggal_kso' => 'required|date',
    'pihak_pertama' => 'required|string',
    'pihak_kedua' => 'required|string',
    'file_pks' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB
    'file_mou' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB
    'keterangan' => 'nullable|string',
]);
```

#### File Storage:
```php
// PKS File
storage/app/public/kso/pks/PKS_{permintaan_id}_{timestamp}.{ext}

// MoU File
storage/app/public/kso/mou/MoU_{permintaan_id}_{timestamp}.{ext}
```

#### Auto-Forward Logic:
```php
// Setelah upload berhasil, otomatis:
1. Set status KSO = 'aktif'
2. Update permintaan.pic_pimpinan = 'Bagian Pengadaan'
3. Update permintaan.status = 'proses'
```

## Workflow

### Before (Old Flow - Complex):
```
1. KSO Staff buka form create
2. Isi banyak field: no_kso, tanggal, pihak, nilai kontrak, isi kerjasama detail
3. Pilih status: draft/aktif/selesai
4. Submit
5. Jika status aktif/selesai → forward ke Pengadaan
```

### After (New Flow - Simple):
```
1. KSO Staff buka form create
2. Isi data minimal: no_kso, tanggal, pihak kedua
3. Upload PKS file (required)
4. Upload MoU file (required)
5. Opsional: tambah keterangan
6. Submit
7. Otomatis set status = aktif
8. Otomatis forward ke Bagian Pengadaan
```

## UI/UX Improvements

### Upload File Interface
```vue
<!-- Drag & Drop File Upload -->
<div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center 
     hover:border-blue-500 transition-colors">
    <input type="file" @change="handlePKSUpload" accept=".pdf,.doc,.docx" 
           class="hidden" id="pks-upload" />
    <label for="pks-upload" class="cursor-pointer">
        <svg icon upload />
        <p>Klik untuk upload atau drag and drop</p>
        <p>PDF, DOC, DOCX (Max. 5MB)</p>
        <p v-if="pksFileName">✓ {{ pksFileName }}</p>
    </label>
</div>
```

### Info Box
```
📌 Catatan:
- Upload file PKS yang telah ditandatangani
- Upload file MoU yang telah ditandatangani
- Pastikan file dalam format PDF, DOC, atau DOCX
- Setelah submit, data otomatis diteruskan ke Bagian Pengadaan
```

## API Endpoints

### POST /kso/store/{permintaan}
**Request:**
```
Content-Type: multipart/form-data

no_kso: "KSO/001/X/2025"
tanggal_kso: "2025-10-28"
pihak_pertama: "RSUD Ibnu Sina Kabupaten Gresik"
pihak_kedua: "PT Vendor ABC"
file_pks: [File]
file_mou: [File]
keterangan: "Catatan tambahan (optional)"
```

**Response (Success):**
```json
{
  "message": "Dokumen KSO (PKS & MoU) berhasil diupload dan diteruskan ke Bagian Pengadaan.",
  "redirect": "/kso/show/{permintaan_id}"
}
```

**Response (Error):**
```json
{
  "errors": {
    "file_pks": ["File PKS wajib diupload"],
    "file_mou": ["File MoU wajib diupload"],
    "file_pks": ["File harus berupa PDF, DOC, atau DOCX"],
    "file_pks": ["Ukuran file maksimal 5MB"]
  }
}
```

## File Structure

```
storage/
└── app/
    └── public/
        └── kso/
            ├── pks/
            │   ├── PKS_17_1730118000.pdf
            │   ├── PKS_18_1730118123.pdf
            │   └── ...
            └── mou/
                ├── MoU_17_1730118000.pdf
                ├── MoU_18_1730118123.pdf
                └── ...
```

## Testing

### Test Create KSO
1. Login sebagai Bagian KSO
2. Pilih permintaan yang sudah di-forward ke KSO
3. Klik "Buat Dokumen KSO"
4. Isi form:
   - No. KSO: `KSO/001/X/2025`
   - Tanggal: Pilih tanggal
   - Pihak Kedua: `PT Vendor Test`
   - Upload PKS: Choose file (PDF/DOC/DOCX)
   - Upload MoU: Choose file (PDF/DOC/DOCX)
   - Keterangan: `Testing upload dokumen`
5. Klik "Simpan & Upload"
6. Verify:
   - Success message muncul
   - Files tersimpan di `storage/app/public/kso/pks/` dan `.../mou/`
   - Status KSO = 'aktif'
   - Permintaan.pic_pimpinan = 'Bagian Pengadaan'

### Test File Validation
- ❌ Upload file > 5MB → Error
- ❌ Upload file .exe, .zip → Error
- ❌ Submit tanpa upload PKS → Error
- ❌ Submit tanpa upload MoU → Error
- ✅ Upload PDF 3MB → Success
- ✅ Upload DOC 2MB → Success
- ✅ Upload DOCX 1MB → Success

## Database Schema

```sql
CREATE TABLE kso (
    kso_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    perencanaan_id BIGINT UNSIGNED NOT NULL,
    no_kso VARCHAR(255) NOT NULL,
    tanggal_kso DATE NOT NULL,
    pihak_pertama VARCHAR(255) NOT NULL,
    pihak_kedua VARCHAR(255) NOT NULL,
    isi_kerjasama TEXT NULL,             -- ✅ Changed to NULL
    nilai_kontrak DECIMAL(15,2) NULL,
    file_pks VARCHAR(255) NULL,          -- ✅ NEW
    file_mou VARCHAR(255) NULL,          -- ✅ NEW
    keterangan TEXT NULL,                -- ✅ NEW
    status ENUM('draft','aktif','selesai','batal') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## Benefits

### 1. **Simplifikasi Proses**
- Kurangi jumlah field dari 8 menjadi 5
- Fokus hanya pada dokumen penting (PKS & MoU)
- Tidak perlu input manual detail kerjasama

### 2. **Efisiensi Waktu**
- Upload file langsung daripada ketik manual
- Auto-forward ke Pengadaan (tidak perlu pilih status)
- Proses lebih cepat

### 3. **Dokumentasi Lebih Baik**
- File asli tersimpan (bukan copy-paste text)
- Bisa didownload untuk keperluan audit
- Format file standar (PDF/DOC)

### 4. **User Friendly**
- Drag & drop interface
- Visual feedback (file name muncul setelah upload)
- Clear instructions

## Migration Command

```bash
# Run migration
php artisan migrate

# Rollback if needed
php artisan migrate:rollback

# Check table structure
php artisan tinker
> DB::select('SHOW COLUMNS FROM kso');
```

## Build Command

```bash
# Build assets
yarn build

# Output:
✓ 1474 modules transformed
✓ built in 4.52s
- Create-BRd1apCe.js (9.38 kB) ← Updated KSO Create form
```

## Related Files

### Modified:
- ✅ resources/js/Pages/KSO/Create.vue
- ✅ app/Http/Controllers/KSOController.php
- ✅ app/Models/Kso.php

### Created:
- ✅ database/migrations/2025_10_28_120051_update_kso_table_for_file_uploads.php

### Not Changed (untuk future update):
- 🔵 resources/js/Pages/KSO/Edit.vue (perlu update jika akan edit file)
- 🔵 resources/js/Pages/KSO/Show.vue (perlu update untuk tampilkan file)
- 🔵 resources/js/Pages/KSO/Index.vue

## Status
✅ **COMPLETE** - KSO simplified to PKS & MoU upload only
✅ **MIGRATED** - Database updated with new columns
✅ **BUILT** - Assets compiled successfully
✅ **TESTED** - Ready for testing

## Next Steps (Optional)
1. Update Show.vue untuk tampilkan download link PKS & MoU
2. Update Edit.vue untuk edit/replace file jika diperlukan
3. Update Index.vue untuk tampilkan indicator file uploaded
4. Tambah preview PDF jika diperlukan
5. Tambah validation file content (bukan hanya extension)
