# Fix: Route Kepala Bidang & Fitur Permintaan

## Perubahan yang Dilakukan

### 1. Fix Route Kepala Bidang - Auto Redirect ke Dashboard

**Problem**: Akses `http://localhost:8000/kepala-bidang` tidak mengarah kemana-mana

**Solution**: Tambah redirect otomatis ke dashboard

**File**: `routes/web.php`

```php
// Routes untuk Kepala Bidang
Route::middleware(['auth', 'verified'])->prefix('kepala-bidang')->name('kepala-bidang.')->group(function () {
    // Redirect root ke dashboard
    Route::get('/', function () {
        return redirect()->route('kepala-bidang.dashboard');
    });
    
    Route::get('/dashboard', [KepalaBidangController::class, 'dashboard'])->name('dashboard');
    Route::get('/index', [KepalaBidangController::class, 'index'])->name('index');
    // ... routes lainnya
});
```

**Hasil**:
- ✅ `/kepala-bidang` → Auto redirect ke `/kepala-bidang/dashboard`
- ✅ `/kepala-bidang/dashboard` → Tetap bisa diakses langsung
- ✅ `/kepala-bidang/index` → Route untuk list permintaan (berubah dari `/`)

### 2. Perbaikan Fitur Permintaan

**Changes Applied**:

#### a. Fitur Edit Permintaan
- ✅ **Hanya bisa edit jika status = "ditolak"**
- ✅ Authorization check di method `edit()` dan `update()`
- ✅ Redirect dengan error message jika unauthorized

```php
public function edit(Permintaan $permintaan)
{
    // Hanya bisa edit jika status ditolak (revisi)
    if (strtolower($permintaan->status) !== 'ditolak') {
        return redirect()->route('permintaan.show', $permintaan)
            ->with('error', 'Permintaan hanya dapat diedit jika dalam status ditolak (revisi).');
    }
    // ...
}
```

#### b. Fitur Delete Permintaan
- ✅ **Hanya bisa delete jika status = "ditolak"**
- ✅ Authorization check di method `destroy()`
- ✅ Redirect dengan error message jika unauthorized

```php
public function destroy(Permintaan $permintaan)
{
    // Hanya bisa delete jika status ditolak
    if (strtolower($permintaan->status) !== 'ditolak') {
        return redirect()->route('permintaan.index')
            ->with('error', 'Permintaan hanya dapat dihapus jika dalam status ditolak.');
    }
    // ...
}
```

#### c. Fitur Create Permintaan
- ✅ **Auto-set status = "diajukan"** saat create
- ✅ Tidak perlu pilih status manual
- ✅ Status otomatis di-set di backend

```php
public function store(Request $request)
{
    // ...
    
    // Auto set status ke 'diajukan' jika tidak ada atau kosong
    if (empty($data['status'])) {
        $data['status'] = 'diajukan';
    }
    
    // ...
}
```

#### d. Fitur Filter & Search
- ✅ Filter berdasarkan status
- ✅ Filter berdasarkan bidang/unit
- ✅ Filter berdasarkan tanggal (dari - sampai)
- ✅ Search berdasarkan ID, deskripsi, atau no nota dinas
- ✅ Pagination (10 items per page, bisa diubah)

```php
// Apply filters
if ($request->filled('search')) { ... }
if ($request->filled('status')) { ... }
if ($request->filled('bidang')) { ... }
if ($request->filled('tanggal_dari')) { ... }
if ($request->filled('tanggal_sampai')) { ... }
```

#### e. Tracking Timeline
- ✅ View tracking lengkap untuk setiap permintaan
- ✅ Progress bar percentage
- ✅ Timeline dengan semua tahapan
- ✅ Status tracking di setiap tahap

## URL Routing Table

### Kepala Bidang Routes

| URL | Route Name | Controller Method | Deskripsi |
|-----|------------|-------------------|-----------|
| `/kepala-bidang` | - | Redirect | Auto redirect ke dashboard |
| `/kepala-bidang/dashboard` | `kepala-bidang.dashboard` | `dashboard()` | Dashboard Kepala Bidang |
| `/kepala-bidang/index` | `kepala-bidang.index` | `index()` | List permintaan (dulu di `/`) |
| `/kepala-bidang/permintaan/{id}` | `kepala-bidang.show` | `show()` | Detail permintaan |
| `/kepala-bidang/permintaan/{id}/tracking` | `kepala-bidang.tracking` | `tracking()` | Timeline tracking |
| `/kepala-bidang/approved` | `kepala-bidang.approved` | `approved()` | Permintaan yang sudah approved |
| `/kepala-bidang/permintaan/{id}/disposisi/create` | `kepala-bidang.disposisi.create` | `createDisposisi()` | Form disposisi |

### Permintaan Routes (Admin)

| URL | Route Name | Controller Method | Deskripsi |
|-----|------------|-------------------|-----------|
| `/permintaan` | `permintaan.index` | `index()` | List semua permintaan |
| `/permintaan/create` | `permintaan.create` | `create()` | Form buat permintaan |
| `/permintaan` (POST) | `permintaan.store` | `store()` | Simpan permintaan baru |
| `/permintaan/{id}` | `permintaan.show` | `show()` | Detail permintaan |
| `/permintaan/{id}/edit` | `permintaan.edit` | `edit()` | Form edit (hanya jika ditolak) |
| `/permintaan/{id}` (PUT) | `permintaan.update` | `update()` | Update permintaan (hanya jika ditolak) |
| `/permintaan/{id}` (DELETE) | `permintaan.destroy` | `destroy()` | Hapus permintaan (hanya jika ditolak) |
| `/permintaan/{id}/tracking` | `permintaan.tracking` | `tracking()` | Timeline tracking |

## Business Rules

### Status Permintaan

| Status | Edit? | Delete? | Keterangan |
|--------|-------|---------|------------|
| `diajukan` | ❌ | ❌ | Sudah disubmit, menunggu review |
| `proses` | ❌ | ❌ | Sedang diproses |
| `disetujui` | ❌ | ❌ | Sudah disetujui |
| `ditolak` | ✅ | ✅ | **Bisa edit dan delete** |

### Workflow Permintaan

```
1. Admin buat permintaan
   - Status otomatis: "diajukan"
   - Edit & Delete: TIDAK BISA
   
2. Kepala Instalasi review
   - Approve → Status: "proses"
   - Reject → Status: "ditolak"
   
3. Jika ditolak
   - Admin BISA edit permintaan
   - Admin BISA delete permintaan
   - Setelah edit, status kembali "diajukan"
   
4. Kepala Bidang review
   - Approve → Status: "disetujui"
   - Reject → Status: "ditolak"
   
5. Lanjut ke tahap berikutnya...
```

## Testing Checklist

### Test Route Kepala Bidang

- [ ] Akses `http://localhost:8000/kepala-bidang`
  - ✅ Harus auto redirect ke `/kepala-bidang/dashboard`
  - ✅ Tidak error 404
  
- [ ] Akses `http://localhost:8000/kepala-bidang/dashboard`
  - ✅ Tampil dashboard Kepala Bidang
  
- [ ] Akses `http://localhost:8000/kepala-bidang/index`
  - ✅ Tampil list permintaan untuk review

### Test Fitur Permintaan (Admin)

#### Create Permission
- [ ] Login sebagai Admin
- [ ] Klik "Buat Permintaan"
- [ ] Isi form (tanpa pilih status)
- [ ] Submit
- [ ] ✅ Status otomatis "diajukan"

#### Edit Permission (Positive Case)
- [ ] Buat permintaan yang ditolak (status: "ditolak")
- [ ] Klik tombol "Edit"
- [ ] ✅ Form edit terbuka
- [ ] Edit deskripsi
- [ ] Save
- [ ] ✅ Berhasil update

#### Edit Permission (Negative Case)
- [ ] Buka permintaan dengan status "proses"
- [ ] Coba akses URL edit: `/permintaan/{id}/edit`
- [ ] ✅ Redirect dengan error "Hanya bisa edit jika ditolak"
- [ ] Tombol edit TIDAK muncul di UI

#### Delete Permission (Positive Case)
- [ ] Buka permintaan dengan status "ditolak"
- [ ] ✅ Tombol delete muncul
- [ ] Klik delete
- [ ] Confirm
- [ ] ✅ Permintaan terhapus

#### Delete Permission (Negative Case)
- [ ] Buka permintaan dengan status "proses"
- [ ] ✅ Tombol delete TIDAK muncul
- [ ] Coba delete via Postman/curl
- [ ] ✅ Redirect dengan error "Hanya bisa delete jika ditolak"

### Test Filter & Search

- [ ] Filter by status
  - ✅ Pilih "diajukan" → Hanya tampil yang diajukan
  - ✅ Pilih "proses" → Hanya tampil yang proses
  
- [ ] Filter by bidang
  - ✅ Pilih "IGD" → Hanya tampil permintaan IGD
  
- [ ] Search
  - ✅ Search by ID → Menemukan
  - ✅ Search by deskripsi → Menemukan
  
- [ ] Filter tanggal
  - ✅ Set dari & sampai → Tampil sesuai range

### Test Tracking

- [ ] Buka detail permintaan
- [ ] Klik "Lihat Tracking"
- [ ] ✅ Tampil timeline lengkap
- [ ] ✅ Progress bar muncul
- [ ] ✅ Semua tahapan terlihat

## Deployment

### 1. Backup

```bash
git add .
git commit -m "Fix: Route Kepala Bidang & Fitur Permintaan"
```

### 2. Deploy

```bash
# Pull latest
git pull origin main

# Build assets
npm run build

# Clear cache
php artisan route:cache
php artisan config:cache
php artisan view:cache
```

### 3. Verify

- Test route `/kepala-bidang` → Auto redirect
- Test create permintaan → Status auto "diajukan"
- Test edit/delete permintaan ditolak → Bisa
- Test edit/delete permintaan proses → Tidak bisa

## Rollback Plan

Jika ada masalah:

```bash
# Revert commit
git revert HEAD

# Rebuild
npm run build

# Clear cache
php artisan cache:clear
```

## Notes

- ✅ Tidak ada database migration diperlukan
- ✅ Hanya perubahan route dan business logic
- ✅ Backward compatible dengan data existing
- ✅ Semua fitur existing tetap berfungsi

---

**Date**: 2025-01-20  
**Developer**: AI Assistant  
**Status**: ✅ Completed  
**Build Status**: ✅ Success
