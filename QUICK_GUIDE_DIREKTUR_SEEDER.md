# Direktur Seeder - Quick Guide

## Overview
Seeder untuk membuat user Direktur yang siap digunakan untuk testing.

## Quick Start

### 1. Run Seeder
```bash
php artisan db:seed --class=DirekturSeeder
```

### 2. Login
```
URL      : http://localhost:8000/login
Email    : direktur@rsud.id
Password : password
```

## User Details

| Field | Value |
|-------|-------|
| **Email** | direktur@rsud.id |
| **Password** | password |
| **Name** | Dr. Soekarno Hatta, Sp.OG, M.Kes |
| **Role** | direktur |
| **Jabatan** | Direktur RSUD |
| **Unit Kerja** | Direksi |

## Workflow Direktur

```
Kepala Bidang
    ↓
  Approve & Disposisi ke Direktur
    ↓
DIREKTUR
    ├─→ [APPROVE] → Routing Otomatis ke Kabid (sesuai klasifikasi)
    │                 ├─ Medis → Kabid Yanmed
    │                 ├─ Penunjang → Kabid Penunjang Medis
    │                 └─ Non Medis → Kabid Umum & Keuangan
    │
    ├─→ [REJECT] → Kembali ke Unit Pemohon (proses dihentikan)
    │
    └─→ [REVISI] → Kembali ke Kepala Bidang (untuk perbaikan)
```

## Fitur yang Tersedia

### Dashboard
- ✅ Statistik: Total, Menunggu, Disetujui, Ditolak
- ✅ Recent 5 permintaan terbaru
- ✅ Progress tracking untuk setiap permintaan

### Index/List Permintaan
- ✅ Filter: Search, Bidang, Tanggal
- ✅ Pagination
- ✅ Status tracking untuk setiap permintaan
- ✅ Quick action buttons (Lihat Detail, Review)

### Detail Permintaan
- ✅ Informasi lengkap permintaan
- ✅ Timeline tracking (step-by-step workflow)
- ✅ Nota Dinas & Disposisi history
- ✅ Action buttons:
  - Setujui (Final Approval)
  - Tolak
  - Minta Revisi
  - Buat Disposisi Manual

### Actions

#### 1. Approve (Final Approval)
- Input: Catatan (optional)
- Result:
  - Status: `disetujui` → `proses`
  - PIC: `Staff Perencanaan`
  - Routing otomatis ke Kabid sesuai klasifikasi
  - Disposisi created dengan catatan approval

#### 2. Reject
- Input: Alasan (min 10 karakter) **REQUIRED**
- Validation: Button disabled jika < 10 chars
- Result:
  - Status: `ditolak`
  - PIC: `Unit Pemohon`
  - Disposisi created dengan catatan rejection
  - Deskripsi updated dengan formatted note

#### 3. Minta Revisi
- Input: Catatan revisi (min 10 karakter) **REQUIRED**
- Validation: Button disabled jika < 10 chars
- Result:
  - Status: `revisi`
  - PIC: `Kepala Bidang`
  - Disposisi created ke Kepala Bidang
  - Deskripsi updated dengan formatted note

## Routing Otomatis Setelah Approve

Setelah Direktur approve, sistem akan otomatis routing ke Kabid yang sesuai:

| Klasifikasi Permintaan | Kabid Tujuan | Unit Kerja |
|------------------------|--------------|------------|
| `medis` atau `Medis` | Bidang Pelayanan Medis | Kabid Yanmed |
| `penunjang_medis` atau `Penunjang` | Bidang Penunjang Medis | Kabid Penunjang |
| `non_medis` atau `Non Medis` | Bidang Umum & Keuangan | Kabid Umum |

**Flow setelah approve:**
```
Direktur Approve
    ↓
Buat Disposisi ke Kabid (sesuai klasifikasi)
    ↓
Update permintaan:
  - pic_pimpinan = 'Kepala Bidang'
  - kabid_tujuan = 'Bidang xxx' (sesuai klasifikasi)
    ↓
Kabid menerima disposisi ✅ (FIX: Query sudah diperbaiki)
    ↓
Kabid disposisi ke Staff Perencanaan
```

## Testing

### Scenario 1: Complete Workflow with Approve
1. **Login as Kepala Instalasi** → Buat permintaan medis
2. **Login as Kepala Instalasi** → Approve permintaan
3. **Login as Kabid Yanmed** → Review & Approve → Disposisi ke Direktur
4. **Login as Direktur** → Approve (Final)
5. **Login as Kabid Yanmed** → ✅ **Permintaan muncul kembali** (disposisi dari Direktur)
6. **Login as Kabid Yanmed** → Disposisi ke Staff Perencanaan
7. **Login as Staff Perencanaan** → Proses perencanaan

### Scenario 2: Rejection
1. **Login as Direktur** → Buka permintaan
2. Click **Tolak**
3. Enter alasan: "Anggaran tidak mencukupi"
4. Submit
5. **Expected**: Status = ditolak, workflow dihentikan

### Scenario 3: Request Revision
1. **Login as Direktur** → Buka permintaan
2. Click **Minta Revisi**
3. Enter catatan: "Mohon lengkapi spesifikasi teknis"
4. Submit
5. **Login as Kabid** → ✅ Permintaan muncul dengan status revisi
6. Kabid bisa perbaiki dan submit ulang

## Data Testing Lengkap

Untuk data testing lengkap dengan permintaan yang sudah sampai di Direktur, gunakan:

```bash
php artisan db:seed --class=DirekturWorkflowSeeder
```

Seeder ini akan membuat:
- 3 permintaan dari berbagai instalasi
- Workflow lengkap sampai Direktur
- Status: Menunggu Review Direktur

## Verification Queries

Gunakan query di `VERIFY_DIREKTUR_WORKFLOW.sql` untuk cek:

```sql
-- Cek permintaan di Direktur
SELECT permintaan_id, status, pic_pimpinan, klasifikasi_permintaan, kabid_tujuan
FROM permintaan
WHERE pic_pimpinan LIKE '%Direktur%'
  AND status = 'proses';

-- Cek disposisi dari Direktur ke Kabid (after approve)
SELECT p.permintaan_id, p.klasifikasi_permintaan, p.kabid_tujuan, 
       d.jabatan_tujuan, d.catatan
FROM permintaan p
JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE d.catatan LIKE '%Disetujui oleh Direktur%'
  AND p.status = 'proses';
```

## Related Files

### Seeder
- `database/seeders/DirekturSeeder.php` - User Direktur only
- `database/seeders/DirekturWorkflowSeeder.php` - Full workflow data
- `database/seeders/UserSeeder.php` - All users

### Controller
- `app/Http/Controllers/DirekturController.php`

### Views
- `resources/js/Pages/Direktur/Dashboard.vue`
- `resources/js/Pages/Direktur/Index.vue`
- `resources/js/Pages/Direktur/Show.vue`
- `resources/js/Pages/Direktur/CreateDisposisi.vue`

### Documentation
- `FIX_KABID_DISPOSISI_DAN_LOGOUT.md` - Fix untuk disposisi Direktur → Kabid
- `ROUTING_FIX_DIREKTUR_TO_KABID.md` - Routing otomatis explanation
- `VERIFY_DIREKTUR_WORKFLOW.sql` - Verification queries

## Troubleshooting

### Issue: Login redirect ke dashboard lain
**Solution**: Clear cache dan logout/login ulang
```bash
php artisan cache:clear
php artisan config:clear
```

### Issue: Permintaan tidak muncul di dashboard
**Possible causes**:
1. Tidak ada permintaan dengan `pic_pimpinan = 'Direktur'`
2. Status bukan `proses`

**Solution**: Gunakan DirekturWorkflowSeeder
```bash
php artisan db:seed --class=DirekturWorkflowSeeder
```

### Issue: Setelah Direktur approve, Kabid tidak menerima
**Solution**: Sudah diperbaiki di `FIX_KABID_DISPOSISI_DAN_LOGOUT.md`

Query Kabid sudah diupdate untuk include:
```php
->orWhereHas('notaDinas.disposisi', function($subQ) use ($user) {
    $subQ->where('jabatan_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
         ->where('catatan', 'LIKE', '%Disetujui oleh Direktur%');
});
```

### Issue: Error 419 saat logout
**Solution**: Sudah diperbaiki di `FIX_KABID_DISPOSISI_DAN_LOGOUT.md`

Enhanced logout dengan:
- CSRF token explicit dari meta tag
- Session storage cleanup
- Global error handler untuk 419

## Best Practices

1. **Selalu run UserSeeder dulu** sebelum DirekturSeeder
2. **Gunakan DirekturWorkflowSeeder** untuk testing lengkap
3. **Clear browser cache** (Ctrl+Shift+R) setelah rebuild frontend
4. **Logout properly** sebelum ganti user
5. **Cek database** dengan verification queries jika ada masalah

## Commands Summary

```bash
# Create Direktur user only
php artisan db:seed --class=DirekturSeeder

# Create Direktur user + workflow data
php artisan db:seed --class=DirekturWorkflowSeeder

# Create all users (including Direktur)
php artisan db:seed --class=UserSeeder

# Run all seeders
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

**Created**: 3 November 2025
**Purpose**: Quick reference for Direktur seeder
**Status**: ✅ Ready to use
