# Fix Validasi Form KSO - Error Handling

## Masalah
Form KSO menampilkan error umum: "Error! Terjadi kesalahan. Silakan periksa form Anda." tanpa detail error yang jelas.

## Penyebab
1. Field `status` dikirim dari frontend tapi tidak ada di validasi backend
2. Error message tidak menampilkan detail error per field
3. Tidak ada logging error untuk debugging

## Perbaikan yang Dilakukan

### 1. Hapus Field `status` dari Form
**File**: `resources/js/Pages/KSO/Create.vue`

**Before:**
```javascript
const form = useForm({
    no_kso: "",
    tanggal_kso: new Date().toISOString().split("T")[0],
    pihak_pertama: "RSUD Ibnu Sina Kabupaten Gresik",
    pihak_kedua: "",
    isi_kerjasama: "",
    nilai_kontrak: "",
    file_pks: null,
    file_mou: null,
    keterangan: "",
    status: "aktif", // ← Field ini tidak perlu, auto set di backend
});
```

**After:**
```javascript
const form = useForm({
    no_kso: "",
    tanggal_kso: new Date().toISOString().split("T")[0],
    pihak_pertama: "RSUD Ibnu Sina Kabupaten Gresik",
    pihak_kedua: "",
    isi_kerjasama: "",
    nilai_kontrak: "",
    file_pks: null,
    file_mou: null,
    keterangan: "",
    // status dihapus, akan di-set otomatis di backend
});
```

### 2. Improve Error Logging
**Before:**
```javascript
onError: (errors) => {
    console.error('Error creating KSO:', errors);
}
```

**After:**
```javascript
onError: (errors) => {
    console.error('Error creating KSO:', errors);
    // Log individual errors for debugging
    Object.keys(errors).forEach(key => {
        console.error(`Error ${key}:`, errors[key]);
    });
}
```

### 3. Better Error Display
**Before:**
```vue
<div v-if="form.hasErrors" class="...">
    <strong>Error!</strong>
    <span v-if="form.errors.error">{{ form.errors.error }}</span>
    <span v-else>Terjadi kesalahan. Silakan periksa form Anda.</span>
</div>
```

**After:**
```vue
<div v-if="form.hasErrors" class="...">
    <strong>Error!</strong>
    <span>Terjadi kesalahan. Silakan periksa form Anda:</span>
    <ul class="mt-2 ml-5 list-disc text-sm">
        <li v-for="(error, key) in form.errors" :key="key">
            <strong>{{ key }}:</strong> {{ Array.isArray(error) ? error[0] : error }}
        </li>
    </ul>
</div>
```

## Validasi Backend yang Benar

```php
// KsoController@store
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

// Status di-set otomatis di controller, tidak dari form
$data['status'] = 'aktif';
```

## Kemungkinan Error dan Solusi

### Error 1: "The no_kso has already been taken"
**Penyebab**: No. KSO sudah ada di database
**Solusi**: Gunakan nomor KSO yang unik

### Error 2: "The file_pks must be a file of type: pdf, doc, docx"
**Penyebab**: Format file tidak sesuai
**Solusi**: Upload file dengan format PDF, DOC, atau DOCX

### Error 3: "The file_pks may not be greater than 5120 kilobytes"
**Penyebab**: File lebih dari 5MB
**Solusi**: Compress file atau gunakan file yang lebih kecil

### Error 4: "The nilai_kontrak field is required"
**Penyebab**: Field nilai kontrak kosong
**Solusi**: Isi nilai kontrak dengan angka

### Error 5: "Permintaan ini belum memiliki perencanaan (DPP)"
**Penyebab**: Belum ada dokumen DPP
**Solusi**: Tunggu Staff Perencanaan membuat DPP terlebih dahulu

## Testing Checklist

### ✅ Validasi Field
- [x] no_kso - required, unique
- [x] tanggal_kso - required, date
- [x] pihak_pertama - required
- [x] pihak_kedua - required
- [x] isi_kerjasama - required
- [x] nilai_kontrak - required, numeric, min:0
- [x] file_pks - required, file, pdf/doc/docx, max:5MB
- [x] file_mou - required, file, pdf/doc/docx, max:5MB
- [x] keterangan - optional

### ✅ Error Handling
- [x] Error ditampilkan per field
- [x] Error message jelas dan informatif
- [x] Console log untuk debugging
- [x] User tahu field mana yang error

### ✅ Form State
- [x] Processing state saat submit
- [x] Tombol disabled saat processing
- [x] Loading indicator
- [x] Form tetap terisi jika error (preserveScroll)

## Cara Debug Error

### 1. Buka Browser Console (F12)
Lihat error yang di-log:
```
Error creating KSO: Object { no_kso: Array(1), file_pks: Array(1) }
Error no_kso: The no_kso has already been taken.
Error file_pks: The file_pks must be a file of type: pdf, doc, docx.
```

### 2. Lihat Network Tab
- Cek request payload
- Cek response dari server
- Cek status code (422 untuk validation error)

### 3. Periksa Error Display di Form
Error akan muncul di banner merah dengan list:
```
Error! Terjadi kesalahan. Silakan periksa form Anda:
• no_kso: The no_kso has already been taken.
• file_pks: The file_pks must be a file of type: pdf, doc, docx.
```

## Common Fixes

### Fix 1: Clear Form Cache
```javascript
// Refresh halaman
location.reload();
```

### Fix 2: Check File Upload
```javascript
// Pastikan file di-set dengan benar
const handlePKSUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        console.log('PKS file:', file.name, file.type, file.size);
        form.file_pks = file;
    }
};
```

### Fix 3: Validate Before Submit
```javascript
const submit = () => {
    // Client-side validation
    if (!form.no_kso) {
        alert('No. KSO harus diisi');
        return;
    }
    if (!form.file_pks) {
        alert('File PKS harus diupload');
        return;
    }
    if (!form.file_mou) {
        alert('File MoU harus diupload');
        return;
    }
    
    // Submit form
    form.post(...);
};
```

## Build Status
```bash
npm run build
# ✅ Build successful
# ✅ No errors
# ✅ Create.vue compiled with validation fixes
```

## Kesimpulan

✅ **Field `status` dihapus dari form**
✅ **Error logging ditambahkan**
✅ **Error display lebih informatif**
✅ **Validasi backend tetap konsisten**
✅ **User bisa lihat detail error per field**

**Validasi form KSO sudah diperbaiki dengan error handling yang lebih baik!** ✅

## Update Log
- 2025-11-05: Hapus field status dari form
- 2025-11-05: Tambah error logging di console
- 2025-11-05: Improve error display dengan list per field
- 2025-11-05: Build successful
