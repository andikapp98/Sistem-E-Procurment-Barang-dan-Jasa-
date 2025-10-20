# Direktur Workflow Seeder

## Overview

Seeder ini membuat data testing lengkap untuk fitur Direktur, termasuk workflow dari Kepala Instalasi → Kepala Bidang → **DIREKTUR**.

## File Location

```
database/seeders/DirekturWorkflowSeeder.php
```

## Data yang Dibuat

### 3 Permintaan Testing

| # | Instalasi | Via Kepala Bidang | Item | Status |
|---|-----------|-------------------|------|--------|
| 1 | IGD | Kabid Pelayanan Medis | Defibrillator Portabel | Menunggu Review Direktur |
| 2 | Farmasi | Kabid Keperawatan | Sistem Otomasi Farmasi | Menunggu Review Direktur |
| 3 | Laboratorium | Kabid Penunjang Medis | Chemistry Analyzer | Menunggu Review Direktur |

### Workflow untuk Setiap Permintaan

```
Kepala Instalasi (buat permintaan)
    ↓
Nota Dinas → Kepala Bidang
    ↓
Disposisi #1: Kepala Instalasi → Kepala Bidang
    ↓
Kepala Bidang Approve (skip Wakil Direktur)
    ↓
Disposisi #2: Kepala Bidang → DIREKTUR (status: disetujui)
    ↓
Update Permintaan:
  - status: proses
  - pic_pimpinan: Direktur
    ↓
READY FOR DIREKTUR TO REVIEW ✅
```

## Cara Menggunakan

### 1. Run Seeder

```bash
php artisan db:seed --class=DirekturWorkflowSeeder
```

### 2. Login sebagai Direktur

```
Email    : direktur@rsud.id
Password : password
```

### 3. Testing Fitur Direktur

#### Dashboard
- ✅ Stats menampilkan: Total = 3, Menunggu = 3
- ✅ Recent list menampilkan 3 permintaan
- ✅ Click "Review" untuk buka detail

#### Test Approve (Permintaan #1 - Defibrillator)
1. Click "Setujui (Final)"
2. Add optional catatan: "Disetujui untuk pengadaan segera"
3. Submit
4. **Expected Result**:
   - Status: `disetujui`
   - PIC: `Staff Perencanaan`
   - Disposisi created ke Staff Perencanaan
   - Success message: "Permintaan disetujui (Final Approval)..."

#### Test Reject (Permintaan #2 - Sistem Otomasi)
1. Click "Tolak"
2. Enter alasan < 10 chars → **button disabled** ✅
3. Enter alasan: "Anggaran tidak mencukupi untuk tahun ini"
4. Submit
5. **Expected Result**:
   - Status: `ditolak`
   - PIC: `Unit Pemohon`
   - Disposisi created
   - Deskripsi updated dengan formatted rejection note
   - Success message: "Permintaan ditolak. Proses dihentikan..."

#### Test Revisi (Permintaan #3 - Chemistry Analyzer)
1. Click "Minta Revisi"
2. Enter catatan < 10 chars → **button disabled** ✅
3. Enter catatan: "Mohon lengkapi spesifikasi teknis dan perkiraan biaya detail"
4. Submit
5. **Expected Result**:
   - Status: `revisi`
   - PIC: `Kepala Bidang` ✅ (CRITICAL FIX!)
   - Disposisi created ke Kepala Bidang ✅
   - Deskripsi updated dengan formatted revision note
   - Success message: "Permintaan revisi telah dikirim ke Kepala Bidang..."

### 4. Verifikasi Workflow

#### Cek dari Kepala Bidang (after revisi)
1. Logout dari Direktur
2. Login sebagai Kepala Bidang Penunjang Medis:
   ```
   Email    : kabid.penunjang@rsud.id
   Password : password
   ```
3. **Expected**: Permintaan #3 (Chemistry Analyzer) muncul di dashboard Kepala Bidang ✅
4. Status: `revisi`
5. Dapat melihat catatan revisi dari Direktur

#### Cek Database
Gunakan query dari `VERIFY_DIREKTUR_WORKFLOW.sql`:

```sql
-- Query #6: Cek revisi ke Kepala Bidang
SELECT 
    p.permintaan_id,
    p.status,
    p.pic_pimpinan,
    d.jabatan_tujuan,
    d.catatan
FROM permintaan p
JOIN nota_dinas n ON p.permintaan_id = n.permintaan_id
JOIN disposisi d ON n.nota_id = d.nota_id
WHERE p.status = 'revisi'
  AND d.jabatan_tujuan = 'Kepala Bidang'
  AND d.catatan LIKE '%REVISI dari Direktur%';
```

## Features Tested

### ✅ Approve (Final Approval)
- [x] Optional catatan
- [x] Create disposisi ke Staff Perencanaan
- [x] Update status: disetujui
- [x] Update PIC: Staff Perencanaan
- [x] Success message
- [x] Redirect to index

### ✅ Reject
- [x] Min 10 chars validation
- [x] Button disabled when invalid
- [x] Create disposisi ke Unit Pemohon
- [x] Update status: ditolak
- [x] Update PIC: Unit Pemohon
- [x] Formatted deskripsi with timestamp
- [x] Success message
- [x] Redirect to index

### ✅ Revisi (CRITICAL FIX)
- [x] Min 10 chars validation
- [x] Button disabled when invalid
- [x] **Create disposisi ke Kepala Bidang** ✅
- [x] **Update PIC: Kepala Bidang** ✅
- [x] Update status: revisi
- [x] Formatted deskripsi with timestamp
- [x] Success message
- [x] Redirect to index
- [x] **Kepala Bidang can see in dashboard** ✅

## Clean Up

Seeder ini secara otomatis menghapus data testing lama sebelum membuat data baru. Data yang dihapus:
- Permintaan testing dengan deskripsi yang match
- Nota Dinas terkait
- Disposisi terkait

## Troubleshooting

### Seeder Error: "User tidak ditemukan"
**Solution**: Jalankan UserSeeder terlebih dahulu
```bash
php artisan db:seed --class=UserSeeder
```

### Dashboard tidak menampilkan permintaan
**Possible causes**:
1. Login menggunakan email yang salah
2. Data seeder belum dijalankan
3. Browser cache (hard refresh: Ctrl+F5)

**Solution**:
```bash
# Re-run seeder
php artisan db:seed --class=DirekturWorkflowSeeder

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Permintaan tidak bisa di-approve/reject/revisi
**Possible causes**:
1. Status bukan 'proses'
2. PIC bukan 'Direktur'
3. Routes belum di-clear

**Solution**:
```bash
# Clear routes
php artisan route:clear

# Check database
SELECT permintaan_id, status, pic_pimpinan 
FROM permintaan 
WHERE pic_pimpinan LIKE '%Direktur%';
```

## Related Files

- **Controller**: `app/Http/Controllers/DirekturController.php`
- **Views**: `resources/js/Pages/Direktur/Dashboard.vue`, `Show.vue`
- **Routes**: `routes/web.php` (direktur.* routes)
- **Verification**: `VERIFY_DIREKTUR_WORKFLOW.sql`
- **Documentation**: `DIREKTUR_ACTIONS_FIXED.md`

## Example Output

```
🚀 Mulai membuat data workflow untuk Direktur...

⚠️  Menghapus data testing Direktur yang lama...
   - Disposisi testing dihapus
   - Nota Dinas testing dihapus
   - Permintaan testing dihapus

📝 Membuat Permintaan #1: IGD → Kabid Yanmed → Direktur (MENUNGGU REVIEW)
📝 Membuat Permintaan #2: Farmasi → Kabid Keperawatan → Direktur (MENUNGGU REVIEW)
📝 Membuat Permintaan #3: Lab → Kabid Penunjang → Direktur (MENUNGGU REVIEW)

✅ Data workflow Direktur berhasil dibuat!

📊 RINGKASAN DATA TESTING DIREKTUR
═══════════════════════════════════════════════════════════════
ID    | Dari           | Via Kepala Bidang   | Item            | Status
──────────────────────────────────────────────────────────────
1     | IGD            | Kabid Yanmed        | Defibrillator   | Menunggu Review
2     | Farmasi        | Kabid Keperawatan   | Sistem Otomasi  | Menunggu Review
3     | Laboratorium   | Kabid Penunjang     | Chemistry       | Menunggu Review
═══════════════════════════════════════════════════════════════

🎯 TESTING CHECKLIST:
...
```

---

**Created**: 2025-10-20
**Purpose**: Testing Direktur workflow & features
**Status**: ✅ Ready to use
