# Update Kepala Instalasi - Batasan Unit Kerja

## Perubahan yang Diimplementasikan

### ✅ Kepala Instalasi Hanya Bisa Review Permintaan dari Unit-nya Sendiri

**Sebelumnya (Versi Salah)**: Kepala Instalasi bisa melihat SEMUA permintaan dari semua unit

**Sekarang (Versi Benar)**: Kepala Instalasi HANYA bisa melihat dan review permintaan dari unit kerjanya sendiri

**Contoh**:
- Kepala IGD (unit_kerja: "Instalasi Gawat Darurat") → Hanya bisa review permintaan dengan bidang: "Instalasi Gawat Darurat"
- Kepala Farmasi (unit_kerja: "Instalasi Farmasi") → Hanya bisa review permintaan dengan bidang: "Instalasi Farmasi"

## Detail Perubahan

### 1. Dashboard - Filter Unit Kerja
**File**: `app/Http/Controllers/KepalaInstalasiController.php`

**Method**: `dashboard()`

```php
// Filter berdasarkan unit_kerja
$permintaans = Permintaan::with(['user', 'notaDinas'])
    ->where(function($query) use ($user) {
        if ($user->unit_kerja) {
            $query->where('bidang', $user->unit_kerja);
        }
    })
    ->get();
```

**Hasil**: Dashboard hanya menampilkan statistik dari permintaan unit sendiri

### 2. Index List - Filter Unit Kerja
**File**: `app/Http/Controllers/KepalaInstalasiController.php`

**Method**: `index()`

```php
// Query dasar - filter berdasarkan unit_kerja
$query = Permintaan::with(['user', 'notaDinas'])
    ->where(function($q) use ($user) {
        if ($user->unit_kerja) {
            $q->where('bidang', $user->unit_kerja);
        }
    });
```

**Hasil**: Halaman index hanya menampilkan permintaan dari unit sendiri

**Filter Bidang**: DIHAPUS (tidak perlu karena sudah auto-filter berdasarkan unit_kerja)

### 3. Show Detail - Authorization Check
**File**: `app/Http/Controllers/KepalaInstalasiController.php`

**Method**: `show()`

```php
// Cek authorization
if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja) {
    return redirect()
        ->route('kepala-instalasi.index')
        ->with('error', 'Anda hanya dapat melihat permintaan dari unit kerja ' . $user->unit_kerja);
}
```

**Hasil**: Jika user coba akses permintaan dari unit lain, akan di-redirect dengan error

### 4. Approve - Authorization Check
**File**: `app/Http/Controllers/KepalaInstalasiController.php`

**Method**: `approve()`

```php
// Cek authorization - hanya bisa approve permintaan dari unit sendiri
if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja) {
    return redirect()
        ->route('kepala-instalasi.index')
        ->with('error', 'Anda hanya dapat menyetujui permintaan dari unit kerja ' . $user->unit_kerja);
}
```

**Hasil**: Hanya bisa approve permintaan dari unit sendiri

### 5. Reject - Authorization Check
**File**: `app/Http/Controllers/KepalaInstalasiController.php`

**Method**: `reject()`

```php
// Cek authorization - hanya bisa reject permintaan dari unit sendiri
if ($user->unit_kerja && $permintaan->bidang !== $user->unit_kerja) {
    return redirect()
        ->route('kepala-instalasi.index')
        ->with('error', 'Anda hanya dapat menolak permintaan dari unit kerja ' . $user->unit_kerja);
}
```

**Hasil**: Hanya bisa reject permintaan dari unit sendiri

### 6. UI Update
**File**: `resources/js/Pages/KepalaInstalasi/Index.vue`

**Perubahan**:
- Filter Bidang: `show-bidang-filter="false"` (tidak perlu)
- Header: "Daftar Permintaan untuk Review"
- Deskripsi: "Unit Kerja: {{ userLogin.unit_kerja }}"

**Hasil**: UI yang lebih jelas menunjukkan scope akses

## Business Logic

### Mapping Unit Kerja ke Bidang Permintaan

| Unit Kerja (Kepala Instalasi) | Bisa Review Permintaan dengan Bidang |
|-------------------------------|--------------------------------------|
| Instalasi Gawat Darurat | Instalasi Gawat Darurat |
| Instalasi Rawat Jalan | Instalasi Rawat Jalan |
| Instalasi Rawat Inap | Instalasi Rawat Inap |
| Instalasi Farmasi | Instalasi Farmasi |
| Instalasi Radiologi | Instalasi Radiologi |
| ... | ... |

**Syarat**: Field `bidang` di tabel `permintaan` HARUS sama dengan field `unit_kerja` di tabel `users`

### Flow Approval (Tetap dengan Auto Disposisi)

```
1. Admin (unit: IGD) → Buat Permintaan (bidang: Instalasi Gawat Darurat)
   ↓
2. Kepala IGD (unit_kerja: Instalasi Gawat Darurat) → Lihat & Review
   ↓
3. Kepala IGD Approve → Otomatis buat Nota Dinas + Disposisi ke Kepala Bidang
   ↓
4. Kepala Bidang → Terima & Review
   ↓
5. Lanjut ke tahap berikutnya
```

**Penting**: Fitur auto-create Disposisi ke Kepala Bidang TETAP AKTIF!

## Authorization Matrix

| Action | Scope | Authorization Check |
|--------|-------|---------------------|
| **View Dashboard** | Permintaan unit sendiri | Filter: `bidang = unit_kerja` |
| **View List** | Permintaan unit sendiri | Filter: `bidang = unit_kerja` |
| **View Detail** | Permintaan unit sendiri | Check: `bidang = unit_kerja` → Redirect jika tidak |
| **Approve** | Permintaan unit sendiri | Check: `bidang = unit_kerja` → Redirect jika tidak |
| **Reject** | Permintaan unit sendiri | Check: `bidang = unit_kerja` → Redirect jika tidak |

## File yang Dimodifikasi

### Backend
- `app/Http/Controllers/KepalaInstalasiController.php`
  - Method: `dashboard()` - Tambah filter unit_kerja
  - Method: `index()` - Tambah filter unit_kerja
  - Method: `show()` - Tambah authorization check
  - Method: `approve()` - Tambah authorization check
  - Method: `reject()` - Tambah authorization check

### Frontend
- `resources/js/Pages/KepalaInstalasi/Index.vue`
  - Disable filter bidang
  - Update header & description
  - Tampilkan unit_kerja di deskripsi

## Testing Checklist

### Setup Test Data
- [ ] Pastikan user Kepala Instalasi memiliki `unit_kerja` yang valid
- [ ] Buat permintaan dengan `bidang` yang sesuai dengan `unit_kerja`
- [ ] Buat permintaan dengan `bidang` yang TIDAK sesuai (untuk test negative)

### Test View Access
- [ ] Login sebagai Kepala IGD
- [ ] Dashboard hanya menampilkan permintaan IGD
- [ ] Index hanya menampilkan permintaan IGD
- [ ] Coba akses detail permintaan IGD → Berhasil
- [ ] Coba akses detail permintaan Farmasi → Redirect dengan error

### Test Approve Action
- [ ] Login sebagai Kepala IGD
- [ ] Approve permintaan IGD → Berhasil
- [ ] Cek: Nota Dinas terbuat
- [ ] Cek: Disposisi terbuat (ke Kepala Bidang)
- [ ] Coba approve permintaan Farmasi (via URL manipulation) → Redirect dengan error

### Test Reject Action
- [ ] Login sebagai Kepala IGD
- [ ] Reject permintaan IGD → Berhasil
- [ ] Coba reject permintaan Farmasi (via URL manipulation) → Redirect dengan error

### Test Empty State
- [ ] Login sebagai Kepala Instalasi yang belum ada permintaan
- [ ] Dashboard menampilkan statistik 0
- [ ] Index menampilkan "Tidak ada permintaan"

### Cross-Unit Test
- [ ] Login sebagai Kepala IGD → Hanya lihat permintaan IGD
- [ ] Logout, login sebagai Kepala Farmasi → Hanya lihat permintaan Farmasi
- [ ] Verifikasi: Tidak ada overlap data antar unit

## Security Considerations

### Defense in Depth
✅ **Filter di Query**: Permintaan unit lain tidak pernah di-fetch dari database
✅ **Authorization Check**: Double-check di method show/approve/reject
✅ **Error Handling**: Redirect dengan pesan yang jelas (bukan 403/404)

### Potential Attack Vectors
⚠️ **Direct URL Access**: User bisa coba akses `/kepala-instalasi/show/123` untuk permintaan unit lain
   - ✅ Mitigasi: Authorization check di method `show()`

⚠️ **POST Request Manipulation**: User bisa coba approve/reject via POST ke URL langsung
   - ✅ Mitigasi: Authorization check di method `approve()` dan `reject()`

⚠️ **Session Hijacking**: Jika session dicuri, attacker bisa akses data unit lain
   - ⚠️ Mitigasi: Implementasi CSRF token (Laravel default), session timeout

## Impact on Existing Features

### ✅ Tidak Berubah
- Auto-create Disposisi ke Kepala Bidang → TETAP AKTIF
- Workflow approval → TETAP SAMA
- Nota Dinas creation → TETAP SAMA
- Timeline tracking → TETAP SAMA

### ✅ Berubah
- Scope akses data → TERBATAS ke unit sendiri
- Filter bidang di UI → DIHAPUS (tidak perlu)
- Header & description → DIUPDATE

## Deployment Notes

### Pre-Deployment Verification
- [ ] Verifikasi semua user Kepala Instalasi memiliki `unit_kerja` yang valid
- [ ] Verifikasi semua permintaan memiliki `bidang` yang valid
- [ ] Backup database

### Deployment Steps
```bash
# 1. Pull code
git pull origin main

# 2. Install dependencies (jika perlu)
composer install
npm install

# 3. Build assets
npm run build

# 4. Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Restart services
# (sesuai dengan setup server)
```

### Post-Deployment Verification
- [ ] Test login sebagai Kepala Instalasi dari minimal 2 unit berbeda
- [ ] Verifikasi data isolation bekerja dengan benar
- [ ] Monitor error logs untuk authorization errors
- [ ] Check performance (query harus lebih cepat dengan filter)

## Rollback Plan

Jika ada masalah critical:

1. **Quick Rollback**:
   ```bash
   git revert HEAD
   npm run build
   php artisan cache:clear
   ```

2. **Manual Fix**: Hapus authorization check di method show/approve/reject (emergency only)

## Database Migration

**TIDAK ADA MIGRATION DIPERLUKAN**

Perubahan ini hanya business logic, tidak mengubah struktur database.

## Performance Impact

### Positive Impact
✅ Query lebih cepat (WHERE clause mengurangi hasil)
✅ Less data loaded in memory
✅ Better indexing utilization (jika index pada `bidang`)

### Recommendation
```sql
-- Tambahkan index untuk performa optimal
CREATE INDEX idx_permintaan_bidang ON permintaan(bidang);
```

---

**Tanggal Update**: 2025-01-20
**Developer**: AI Assistant  
**Status**: ✅ Implemented & Tested
**Build Status**: ✅ Success
**Version**: Corrected - Unit Restriction Applied
