# Testing Guide: KSO Show Page (http://localhost:8000/kso/permintaan/17)

## URL Pattern
```
http://localhost:8000/kso/permintaan/{permintaan_id}
```

## Route Information
- **Route Name:** `kso.show`
- **Method:** GET
- **Controller:** `KSOController@show`
- **Middleware:** `auth`, `verified`

## Prerequisites

### 1. User Login
Pastikan Anda login sebagai user dengan role `kso`:
```sql
-- Check user role
SELECT user_id, nama, email, role, jabatan 
FROM users 
WHERE role = 'kso';
```

### 2. Permintaan Must Exist
```sql
-- Check if permintaan ID 17 exists
SELECT permintaan_id, deskripsi, bidang, status, pic_pimpinan
FROM permintaan
WHERE permintaan_id = 17;
```

### 3. PIC Pimpinan Harus "Bagian KSO"
```sql
-- Update pic_pimpinan if needed
UPDATE permintaan 
SET pic_pimpinan = 'Bagian KSO'
WHERE permintaan_id = 17;
```

## Testing Steps

### Step 1: Login sebagai KSO User
```
1. Buka http://localhost:8000/login
2. Login dengan:
   - Email: kso@example.com (atau sesuai data Anda)
   - Password: password
3. Seharusnya redirect ke dashboard KSO
```

### Step 2: Akses Show Page
```
1. Buka URL: http://localhost:8000/kso/permintaan/17
2. Atau dari dashboard KSO, klik "Detail" pada permintaan ID 17
```

### Step 3: Verify Page Content

#### A. Informasi Permintaan Section
✅ Harus tampil:
- ID Permintaan (#17)
- Tanggal Permintaan
- Bidang
- Unit Kerja
- Deskripsi
- Jumlah & Satuan
- Status badge (proses/disetujui)

#### B. Data KSO Section

**Jika KSO Sudah Ada:**
✅ Tampil:
- No. KSO
- Tanggal KSO
- Pihak Pertama (RSUD Ibnu Sina Kabupaten Gresik)
- Pihak Kedua (Vendor)
- Status KSO (badge: draft/aktif/selesai)
- Nilai Kontrak (jika ada)
- Isi Kerjasama (jika ada)

✅ Dokumen KSO:
- PKS: Nama file + button Download (jika ada)
- MoU: Nama file + button Download (jika ada)
- Jika belum upload: "Belum diupload" + button disabled

✅ Keterangan (jika ada)

✅ Timeline:
- Dibuat: tanggal
- Terakhir Diupdate: tanggal

✅ Action Buttons:
- Edit button (biru)
- Hapus button (merah)

**Jika KSO Belum Ada:**
✅ Tampil:
- Icon document
- "Belum Ada Data KSO"
- "Silakan buat dokumen KSO untuk permintaan ini."
- Button "Buat Dokumen KSO" (hijau)

## Expected Response

### Success (200 OK)
```
Page loads successfully with:
- Header: "Detail Permintaan #17"
- Back button to KSO Index
- Two sections: Informasi Permintaan & Data KSO
```

### Error Responses

#### 403 Forbidden
```
Error: "Anda tidak memiliki akses untuk melihat permintaan ini."

Penyebab:
- pic_pimpinan != 'Bagian KSO'
- pic_pimpinan != current user nama

Solusi:
UPDATE permintaan 
SET pic_pimpinan = 'Bagian KSO'
WHERE permintaan_id = 17;
```

#### 404 Not Found
```
Error: Page not found

Penyebab:
- Permintaan ID 17 tidak ada di database

Solusi:
- Cek dengan: SELECT * FROM permintaan WHERE permintaan_id = 17;
- Gunakan ID yang valid
```

#### 401 Unauthorized
```
Error: Unauthenticated

Penyebab:
- Belum login

Solusi:
- Login terlebih dahulu di /login
```

## Troubleshooting

### Problem 1: "Belum Ada Data KSO" muncul terus
**Diagnosa:**
```sql
-- Check if KSO exists for this permintaan
SELECT k.*, p.perencanaan_id 
FROM kso k
JOIN perencanaan p ON k.perencanaan_id = p.perencanaan_id
JOIN disposisi d ON p.disposisi_id = d.disposisi_id
JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = 17;
```

**Solusi:**
- Jika tidak ada KSO, klik "Buat Dokumen KSO"
- Isi form dan upload PKS & MoU

### Problem 2: File PKS/MoU tidak bisa didownload
**Diagnosa:**
```sql
-- Check file paths
SELECT kso_id, file_pks, file_mou 
FROM kso 
WHERE kso_id = 1; -- sesuaikan dengan kso_id
```

**Solusi:**
```bash
# Create storage link jika belum
php artisan storage:link

# Verify files exist
ls storage/app/public/kso/pks/
ls storage/app/public/kso/mou/
```

### Problem 3: Page expired (419)
**Solusi:**
```bash
# Clear cache
php artisan optimize:clear

# Verify CSRF token
# Pastikan form menggunakan Inertia router.post()
```

### Problem 4: Layout rusak
**Solusi:**
```bash
# Rebuild assets
yarn build

# Clear browser cache
Ctrl + Shift + R (hard refresh)
```

## Sample Data

### Create Sample Permintaan (if needed)
```sql
-- Insert sample permintaan
INSERT INTO permintaan (
    user_id, tanggal_permintaan, deskripsi, 
    jumlah, satuan, bidang, unit_kerja, 
    status, pic_pimpinan
) VALUES (
    1, '2025-10-28', 'Permintaan untuk testing KSO',
    10, 'unit', 'IGD', 'Instalasi Gawat Darurat',
    'proses', 'Bagian KSO'
);
```

### Create Sample KSO (if needed)
```sql
-- First, need to create nota_dinas, disposisi, perencanaan
-- Then create KSO
INSERT INTO kso (
    perencanaan_id, no_kso, tanggal_kso,
    pihak_pertama, pihak_kedua,
    file_pks, file_mou, status
) VALUES (
    1, 'KSO/001/X/2025', '2025-10-28',
    'RSUD Ibnu Sina Kabupaten Gresik', 'PT Vendor Test',
    'kso/pks/PKS_17_1730118000.pdf',
    'kso/mou/MoU_17_1730118000.pdf',
    'aktif'
);
```

## Quick Access Links

### From Dashboard
```
1. http://localhost:8000/kso/dashboard
2. Click "Detail" pada permintaan yang diinginkan
```

### From Index
```
1. http://localhost:8000/kso
2. Click "Detail" pada row permintaan ID 17
```

### Direct URL
```
http://localhost:8000/kso/permintaan/17
```

## Expected View Components

### Components Used:
- ✅ AuthenticatedLayout
- ✅ Head (title: "Detail Permintaan KSO")
- ✅ Link (untuk navigasi)
- ✅ Conditional rendering (v-if/v-else)

### Features:
- ✅ Responsive design (mobile & desktop)
- ✅ Status badges dengan warna
- ✅ Download buttons untuk files
- ✅ Delete confirmation
- ✅ Format tanggal Indonesia
- ✅ Format rupiah untuk nilai kontrak

## Security

### Authorization Check:
```php
if ($permintaan->pic_pimpinan !== 'Bagian KSO' && 
    $permintaan->pic_pimpinan !== $user->nama) {
    abort(403);
}
```

### CSRF Protection:
- ✅ Delete form uses Inertia router.delete()
- ✅ Automatic CSRF token handling

## Performance

### Expected Load Time:
- Initial page load: < 500ms
- File downloads: depends on file size

### Database Queries:
- 1 query untuk permintaan
- 1 query untuk relations (eager loading)
- 1 query untuk perencanaan
- 1 query untuk kso (jika ada)

Total: ~4 queries

## Next Actions from Show Page

### If KSO Not Exists:
1. ➡️ Click "Buat Dokumen KSO"
2. ➡️ Redirect to `/kso/permintaan/17/create`

### If KSO Exists:
1. ➡️ Click "Edit" → `/kso/permintaan/17/kso/{kso_id}/edit`
2. ➡️ Click "Hapus" → Delete confirmation → Delete KSO
3. ➡️ Click "Download PKS" → Download file
4. ➡️ Click "Download MoU" → Download file
5. ➡️ Click "← Kembali" → Back to `/kso`

## Status Checklist

### Before Testing:
- [ ] Laravel server running (`php artisan serve`)
- [ ] Vite dev server running (`yarn dev`) or assets built (`yarn build`)
- [ ] Database accessible
- [ ] User KSO exists and can login
- [ ] Permintaan ID 17 exists
- [ ] Permintaan.pic_pimpinan = 'Bagian KSO'

### After Testing:
- [ ] Page loads without error
- [ ] All sections display correctly
- [ ] Buttons work as expected
- [ ] Files can be downloaded (if exist)
- [ ] No console errors

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| 403 Error | Update pic_pimpinan to 'Bagian KSO' |
| 404 Error | Check permintaan exists, use valid ID |
| 419 Error | Clear cache, rebuild assets |
| Files not found | Run `php artisan storage:link` |
| Layout broken | Run `yarn build` |
| White page | Check browser console for JS errors |

---

## Quick Test Commands

```bash
# 1. Check if permintaan exists
php artisan tinker --execute="App\Models\Permintaan::find(17)"

# 2. Update pic_pimpinan
php artisan tinker --execute="App\Models\Permintaan::find(17)->update(['pic_pimpinan' => 'Bagian KSO'])"

# 3. Check if KSO exists
php artisan tinker --execute="App\Models\Permintaan::find(17)->notaDinas->first()->disposisi->first()->perencanaan->kso ?? null"

# 4. Clear all caches
php artisan optimize:clear

# 5. Rebuild assets
yarn build
```

## Support

Jika masih ada masalah, cek:
1. Laravel log: `storage/logs/laravel.log`
2. Browser console: F12 → Console tab
3. Network tab: F12 → Network tab untuk melihat request/response

---

**Status:** ✅ View sudah tersedia dan siap digunakan
**Route:** `http://localhost:8000/kso/permintaan/17`
**File:** `resources/js/Pages/KSO/Show.vue`
