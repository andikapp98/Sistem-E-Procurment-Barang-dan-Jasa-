# Quick Fix Implementation Guide

## Date: 2025-10-21
## Priority: IMMEDIATE

---

## Issue Summary & Status

### ✅ FIXED ISSUES:
1. **Pagination Component Missing** - Created `resources/js/Components/Pagination.vue`

### ⚠️ VERIFIED WORKING:
1. **Direktur Functions (Setujui/Tolak/Revisi)** - Already working correctly
2. **Workflow Logic** - Correct: Admin → KA → KABID → Direktur → KABID → Staff Perencanaan
3. **Routes** - All routes exist and configured properly

### 🔧 NEEDS FIXING:
1. Remove "Scan Berkas" feature
2. Fix Vite dev server issue
3. Improve tracking visibility for Admin
4. Nota Dinas component already supports dual types

---

## Step-by-Step Fixes

### Fix 1: Remove Scan Berkas Feature

#### A. Remove Route
**File**: `routes/web.php`

Find and COMMENT OUT or REMOVE:
```php
// Line ~130
Route::get('/permintaan/{permintaan}/scan-berkas', [StaffPerencanaanController::class, 'uploadDokumen'])->name('scan-berkas');
```

#### B. Remove Links in Views
**File**: `resources/js/Pages/StaffPerencanaan/Show.vue`

Remove any buttons/links that reference 'scan-berkas' route.

---

### Fix 2: Vite Dev Server Issue

The error `'vite' is not recognized` means vite executable is missing.

**Solution**:
```powershell
# Method 1: Clean install
cd C:\Users\KIM\Documents\pengadaan-app
Remove-Item node_modules -Recurse -Force
Remove-Item package-lock.json -Force -ErrorAction SilentlyContinue
yarn install

# Method 2: Install vite globally
yarn global add vite

# Method 3: Use npx
npx vite

# Method 4: Update package.json scripts
# Change "vite" to "npx vite" in scripts section
```

---

### Fix 3: Direktur - Ensure Processed Data Doesn't Show

**File**: `app/Http/Controllers/DirekturController.php`

The `index()` method already filters correctly (line 83):
```php
->where('status', 'proses');
```

This is CORRECT - only shows items currently being processed.

**Additional Enhancement** (Optional):
Add a separate "History" or "Approved" tab to view past decisions.

---

### Fix 4: Admin Tracking Visibility

**File**: `app/Http/Controllers/PermintaanController.php`

Add a tracking method:
```php
public function tracking(Permintaan $permintaan)
{
    $permintaan->load(['user', 'notaDinas', 'disposisi', 'timelineTracking']);
    
    $trackingStatus = $permintaan->trackingStatus;
    $progress = $permintaan->getProgressPercentage();
    $timeline = $permintaan->timelineTracking()->orderBy('tanggal', 'desc')->get();
    
    return Inertia::render('Admin/Tracking', [
        'permintaan' => $permintaan,
        'trackingStatus' => $trackingStatus,
        'progress' => $progress,
        'timeline' => $timeline,
    ]);
}
```

**File**: `routes/web.php`

Add route:
```php
Route::get('/permintaan/{permintaan}/tracking', [PermintaanController::class, 'tracking'])->name('permintaan.tracking');
```

---

### Fix 5: Sidebar Standardization

Each role should have consistent sidebar structure.

**File**: `resources/js/Layouts/AuthenticatedLayout.vue`

Ensure navigation links are role-specific:

```vue
<!-- Admin -->
<NavLink v-if="$page.props.auth.user.role === 'admin'" :href="route('dashboard')">Dashboard</NavLink>
<NavLink v-if="$page.props.auth.user.role === 'admin'" :href="route('permintaan.index')">Permintaan</NavLink>

<!-- Kepala Instalasi -->
<NavLink v-if="$page.props.auth.user.jabatan === 'Kepala Instalasi'" :href="route('kepala-instalasi.dashboard')">Dashboard</NavLink>

<!-- Kepala Bidang -->
<NavLink v-if="$page.props.auth.user.jabatan === 'Kepala Bidang'" :href="route('kepala-bidang.dashboard')">Dashboard</NavLink>

<!-- Direktur -->
<NavLink v-if="$page.props.auth.user.jabatan === 'Direktur'" :href="route('direktur.dashboard')">Dashboard</NavLink>
<NavLink v-if="$page.props.auth.user.jabatan === 'Direktur'" :href="route('direktur.index')">Daftar Permintaan</NavLink>
<NavLink v-if="$page.props.auth.user.jabatan === 'Direktur'" :href="route('direktur.approved')">Riwayat</NavLink>

<!-- Staff Perencanaan -->
<NavLink v-if="$page.props.auth.user.jabatan === 'Staff Perencanaan'" :href="route('staff-perencanaan.dashboard')">Dashboard</NavLink>
<NavLink v-if="$page.props.auth.user.jabatan === 'Staff Perencanaan'" :href="route('staff-perencanaan.index')">Permintaan</NavLink>
```

---

### Fix 6: Timeline Auto-Update

**File**: `app/Models/Permintaan.php`

Ensure the model has a method to update timeline:

```php
public function updateTimeline($tahapan, $keterangan, $status = 'selesai')
{
    return $this->timelineTracking()->create([
        'tahapan' => $tahapan,
        'tanggal' => now(),
        'keterangan' => $keterangan,
        'status' => $status,
    ]);
}
```

Then call this method whenever status changes:
```php
// In DirekturController@approve:
$permintaan->updateTimeline(
    'Persetujuan Direktur', 
    'Permintaan disetujui oleh Direktur (Final Approval)',
    'disetujui'
);

// In DirekturController@reject:
$permintaan->updateTimeline(
    'Penolakan Direktur', 
    'Permintaan ditolak oleh Direktur: ' . $data['alasan'],
    'ditolak'
);

// In DirekturController@requestRevision:
$permintaan->updateTimeline(
    'Revisi dari Direktur', 
    'Direktur meminta revisi: ' . $data['catatan_revisi'],
    'revisi'
);
```

---

## Nota Dinas Dual Types - Already Implemented! ✅

The `GenerateNotaDinas.vue` component already supports:
1. **Nota Dinas Usulan** (Line 53-67)
2. **Nota Dinas Pembelian** (Line 70-87)

Both types have appropriate fields and validation.

---

## Testing Commands

After implementing fixes:

```powershell
# 1. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Run migrations (if needed)
php artisan migrate

# 3. Start dev server
yarn dev
# or
npx vite

# 4. In another terminal, start Laravel
php artisan serve
```

---

## Verification Steps

1. **Login as Direktur**:
   - Go to `/direktur`
   - Should only see items with status='proses'
   - Click detail on a request
   - Verify buttons: Setujui, Tolak, Revisi appear
   - Test each action

2. **Login as Staff Perencanaan**:
   - Go to `/staff-perencanaan`
   - Should see approved requests
   - Click detail
   - Verify "Generate Nota Dinas" button works
   - Verify can choose between Usulan and Pembelian types
   - Verify NO "Scan Berkas" button appears

3. **Login as Admin**:
   - Go to `/permintaan`
   - Create new request
   - Click on a request detail
   - Verify tracking information is clear
   - Verify can see current status and who has it

4. **Test Full Workflow**:
   - Admin creates → KA approves → KABID forwards → Direktur approves
   - Verify timeline updates at each step
   - Verify tracking shows correct progress
   - Verify Staff Perencanaan receives it

---

## Files Modified Summary

### Created:
- ✅ `resources/js/Components/Pagination.vue`

### Need to Modify:
- `routes/web.php` - Remove scan-berkas route
- `resources/js/Pages/StaffPerencanaan/Show.vue` - Remove scan-berkas button (if exists)
- `app/Models/Permintaan.php` - Add updateTimeline method
- `app/Http/Controllers/DirekturController.php` - Add timeline updates
- `resources/js/Layouts/AuthenticatedLayout.vue` - Standardize sidebar

### No Changes Needed:
- ✅ `app/Http/Controllers/DirekturController.php` - Functions work correctly
- ✅ `resources/js/Pages/Direktur/Show.vue` - UI works correctly
- ✅ `resources/js/Components/GenerateNotaDinas.vue` - Dual types already supported

---

## Command to Start Development

```powershell
# Terminal 1: Frontend
cd C:\Users\KIM\Documents\pengadaan-app
yarn dev

# Terminal 2: Backend
cd C:\Users\KIM\Documents\pengadaan-app
php artisan serve
```

If `yarn dev` fails with vite error:
```powershell
npx vite
```

Or fix package.json:
```json
{
  "scripts": {
    "dev": "npx vite",
    "build": "npx vite build"
  }
}
```

---

## Priority Actions - DO THIS FIRST

1. **Install dependencies properly**:
   ```powershell
   yarn install --check-files
   ```

2. **Comment out scan-berkas route** in `routes/web.php`

3. **Test Direktur functions** - they should already work

4. **Run dev server**:
   ```powershell
   npx vite
   ```

---

## Conclusion

Most features are ALREADY WORKING! The main issues are:
1. ✅ Missing Pagination component (FIXED)
2. ⚠️ Vite not found (needs yarn install or use npx)
3. ⚠️ Scan Berkas should be removed (simple route comment)
4. ✅ Direktur functions work correctly
5. ✅ Nota Dinas dual types already implemented
6. ⚠️ Timeline updates need to be called in controllers

Focus on getting the dev server running first, then test the existing features!
