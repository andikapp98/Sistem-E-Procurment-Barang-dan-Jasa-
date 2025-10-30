# Changelog - E-Procurement RSUD Ibnu Sina Gresik

## [2.0] - 30 Oktober 2025

### 🔧 Fixed
- **CSRF Token Issues** - Fixed 419 Page Expired errors
  - ✅ Approve/Reject/Revisi menggunakan `useForm` dari Inertia.js
  - ✅ Logout menggunakan `useForm` 
  - ✅ Hapus infinite loop pada CSRF retry
  - Files: `Direktur/Show.vue`, `KepalaBidang/Show.vue`, `WakilDirektur/Show.vue`, `KepalaInstalasi/Show.vue`, `AuthenticatedLayout.vue`

- **Routing Fixes** - Routing otomatis berdasarkan klasifikasi
  - ✅ Direktur → Kabid sesuai klasifikasi permintaan
    - Medis → Kabid Yanmed
    - Penunjang Medis → Kabid Penunjang
    - Non Medis → Kabid Umum
  - File: `DirekturController.php`

- **Logout Infinite Loop** - Simplified `app.js`
  - ✅ Hapus custom CSRF retry logic
  - ✅ Biarkan Inertia handle CSRF secara default
  - File: `resources/js/app.js`

### 📚 Documentation
- ✅ Cleanup 120+ duplicate MD files
- ✅ Reorganized documentation structure
- ✅ Created `DOCS_CLEANUP_SUMMARY.md`
- ✅ Created `CHANGELOG.md`

### 🎯 Key Files
- `CSRF_FIX_SUMMARY.md` - CSRF fix documentation
- `QUICK_FIX_419_LOGOUT.md` - Logout infinite loop fix
- `ROUTING_FIX_DIREKTUR_TO_KABID.md` - Routing fix documentation

---

## [1.9] - 29 Oktober 2025

### ✨ Features
- **Kabid Actions** - Enhanced approval workflow
  - ✅ Hide/show approve button based on status
  - ✅ Reject to Kepala Instalasi with revisi
  - ✅ Tracking feature for Kabid
  - ✅ Actions verification

### 🔧 Fixed
- **Duplicate Kabid Tujuan** - Fixed duplicate entries
- **Bagian Umum User** - Fixed user access

---

## [1.8] - 28 Oktober 2025

### ✨ Features
- **Klasifikasi Permintaan** - Classification system
  - ✅ Medis, Penunjang Medis, Non Medis
  - ✅ Auto-routing based on classification
  - ✅ View improvements

### 🔧 Fixed
- **419 Errors** - Multiple fixes for page expired errors
- **Routing** - Fixed classification-based routing
- **Modal** - Fixed Kepala Instalasi approve modal
- **Syntax Errors** - Fixed Kepala Instalasi controller

### 📚 Documentation
- ✅ Comprehensive testing guide
- ✅ Admin seeder summary
- ✅ Multiple fix summaries

---

## [1.7] - 27 Oktober 2025

### ✨ Features
- **Staff Perencanaan CRUD** - Complete CRUD operations
- **Login Validation** - Enhanced login validation

### 🔧 Fixed
- **419 CSRF Errors** - Login/logout fixes for all roles
- **Staff Perencanaan** - Logout fix

---

## [1.6] - 25-26 Oktober 2025

### ✨ Features
- **Admin Features**
  - ✅ Lampiran Nota Dinas
  - ✅ Edit/Delete permintaan
  - ✅ Tracking complete

- **Kepala Instalasi**
  - ✅ Nota Dinas view
  - ✅ Cetak lampiran
  - ✅ All features complete

- **Clean Deskripsi** - Remove revision notes from Kabid/Direktur

### 🔧 Fixed
- **Kepala Instalasi** - Complete fixes
- **Admin to IGD** - Permintaan routing fix

---

## [1.5] - 23 Oktober 2025

### ✨ Features
- **Cetak Nota Dinas** - Print functionality
  - ✅ Template nota dinas
  - ✅ View cetak updated
  - ✅ Quick guide

- **Disposisi** - Nota dinas & disposisi system
  - ✅ V2 update
  - ✅ Implementation complete

---

## [1.4] - 21 Oktober 2025

### ✨ Features
- **Staff Perencanaan**
  - ✅ Nota Dinas generator
  - ✅ DPP (Daftar Pagu Per-item)
  - ✅ HPS (Harga Perkiraan Sendiri)
  - ✅ Simplified nota dinas
  - ✅ Dual type nota dinas

- **Direktur Workflow**
  - ✅ Approve/Reject/Revisi
  - ✅ Riwayat keputusan
  - ✅ Fixed approved data
  - ✅ To Kabid workflow fixed

### 🔧 Fixed
- **Comprehensive Fixes** - Multiple bug fixes
- **NPM Dev** - Use Yarn solution
- **Timeline** - Next steps complete
- **Sidebar** - Improvements

---

## [1.3] - 20 Oktober 2025

### ✨ Features
- **Kepala Instalasi** - All features complete
- **Kepala Bidang** - Complete workflow
- **Admin** - Edit/delete features

### 🔧 Fixed
- **Login/Logout** - Issues fixed
- **Pagination** - Fix applied
- **Route** - Kepala Bidang routing
- **Workflow** - Kabid to Direktur

---

## [1.2] - 19 Oktober 2025

### ✨ Features
- **Staff Perencanaan** - Activated
- **Feature Tracking** - Kabid tracking
- **Lihat Semua** - Kabid update

### 🔧 Fixed
- **403 Errors** - Removed unnecessary checks
- **Permintaan Admin** - Updates applied

---

## [1.1] - Initial Release

### ✨ Features
- Basic workflow implementation
- Role-based access control
- Nota dinas system
- Disposisi system

---

## Upgrade Guide

### From 1.x to 2.0

1. **Clear cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. **Rebuild assets:**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

3. **Clear browser cache:**
   - Press `Ctrl + Shift + Delete`
   - Select "All time"
   - Clear cookies and cached files

4. **Test critical features:**
   - Login/Logout
   - Approve/Reject/Revisi
   - Routing workflow

---

## Breaking Changes

### 2.0
- **CSRF Handling:** Changed from `router.post()` to `useForm.post()`
- **Routing:** Direktur now routes to specific Kabid based on classification
- **app.js:** Simplified, removed custom CSRF retry logic

---

## Known Issues

### 2.0
- None currently

### 1.x
- ❌ 419 CSRF errors (FIXED in 2.0)
- ❌ Logout infinite loop (FIXED in 2.0)
- ❌ Generic routing from Direktur (FIXED in 2.0)

---

**For detailed changes, see:**
- `CSRF_FIX_SUMMARY.md`
- `QUICK_FIX_419_LOGOUT.md`
- `ROUTING_FIX_DIREKTUR_TO_KABID.md`
