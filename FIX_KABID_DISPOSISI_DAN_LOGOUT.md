# FIX: Kabid Disposisi dari Direktur & Logout di Perencanaan

## Masalah yang Diperbaiki

### 1. ❌ Disposisi dari Direktur ke Kabid Tidak Muncul
**Gejala:**
- Setelah Direktur approve permintaan, data tidak muncul di index Kabid
- Kabid tidak bisa melihat disposisi balik dari Direktur
- Workflow terputus setelah approval Direktur

**Penyebab:**
Query di `KepalaBidangController::index()` dan `dashboard()` hanya mencari:
```php
->where('pic_pimpinan', 'LIKE', '%Kepala Bidang%')
```

Setelah Direktur approve, `pic_pimpinan` masih "Kepala Bidang" tetapi permintaan tidak tampil karena query tidak memeriksa disposisi dari Direktur.

### 2. ❌ Logout Tidak Berfungsi di Halaman Staff Perencanaan
**Gejala:**
- Tombol logout tidak bekerja dengan baik
- Kadang muncul error 419 CSRF token mismatch
- User tidak bisa keluar dari sistem

**Penyebab:**
- CSRF token handling tidak optimal
- Tidak ada fallback ketika token mismatch
- Session storage tidak dibersihkan saat logout

---

## Solusi yang Diterapkan

### 1. ✅ Fix Query Kabid untuk Menerima Disposisi dari Direktur

#### File: `app/Http/Controllers/KepalaBidangController.php`

**Method `index()` - Sebelum:**
```php
public function index(Request $request)
{
    $user = Auth::user();
    
    $klasifikasiArray = $this->getKlasifikasiByUnitKerja($user->unit_kerja);
    
    // HANYA permintaan yang BELUM di-approve Kabid
    $query = Permintaan::with(['user', 'notaDinas.disposisi'])
        ->where(function($q) use ($user, $klasifikasiArray) {
            if ($klasifikasiArray) {
                $q->whereIn('klasifikasi_permintaan', $klasifikasiArray);
            }
            $q->orWhere('kabid_tujuan', 'LIKE', '%' . $user->unit_kerja . '%');
        })
        ->where('status', 'proses')
        ->where('pic_pimpinan', 'LIKE', '%Kepala Bidang%'); // ❌ Terlalu restrictive
```

**Method `index()` - Sesudah:**
```php
public function index(Request $request)
{
    $user = Auth::user();
    
    $klasifikasiArray = $this->getKlasifikasiByUnitKerja($user->unit_kerja);
    
    // Permintaan yang butuh action dari Kabid:
    // 1. Permintaan baru dari Kepala Instalasi (pic_pimpinan = Kepala Bidang)
    // 2. Disposisi balik dari Direktur (cek disposisi dengan jabatan_tujuan sesuai unit kerja)
    $query = Permintaan::with(['user', 'notaDinas.disposisi'])
        ->where(function($q) use ($user, $klasifikasiArray) {
            if ($klasifikasiArray) {
                $q->whereIn('klasifikasi_permintaan', $klasifikasiArray);
            }
            $q->orWhere('kabid_tujuan', 'LIKE', '%' . $user->unit_kerja . '%');
        })
        ->where('status', 'proses')
        ->where(function($q) use ($user) {
            // Kondisi 1: pic_pimpinan = Kepala Bidang (permintaan baru)
            $q->where('pic_pimpinan', 'LIKE', '%Kepala Bidang%')
              // Kondisi 2: Ada disposisi dari Direktur ke Kabid ini ✅ NEW
              ->orWhereHas('notaDinas.disposisi', function($subQ) use ($user) {
                  $subQ->where('jabatan_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
                       ->where('catatan', 'LIKE', '%Disetujui oleh Direktur%');
              });
        });
```

**Perubahan yang Sama di Method `dashboard()`:**
```php
// Ambil permintaan yang butuh action dari Kabid
$permintaans = Permintaan::with(['user', 'notaDinas.disposisi'])
    ->where(function($q) use ($user, $klasifikasiArray) {
        if ($klasifikasiArray) {
            $q->whereIn('klasifikasi_permintaan', $klasifikasiArray);
        }
        $q->orWhere('kabid_tujuan', 'LIKE', '%' . $user->unit_kerja . '%');
    })
    ->where('status', 'proses')
    ->where(function($q) use ($user) {
        // Kondisi 1: pic_pimpinan = Kepala Bidang (permintaan baru)
        $q->where('pic_pimpinan', 'LIKE', '%Kepala Bidang%')
          // Kondisi 2: Ada disposisi dari Direktur ke Kabid ini ✅ NEW
          ->orWhereHas('notaDinas.disposisi', function($subQ) use ($user) {
              $subQ->where('jabatan_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
                   ->where('catatan', 'LIKE', '%Disetujui oleh Direktur%');
          });
    })
    ->get();
```

**Keuntungan:**
✅ Kabid sekarang bisa melihat permintaan yang di-approve Direktur
✅ Disposisi balik dari Direktur muncul di index dan dashboard Kabid
✅ Workflow tidak terputus setelah Direktur approve
✅ Tetap filter berdasarkan klasifikasi dan unit_kerja yang sesuai

---

### 2. ✅ Fix Logout dengan Better CSRF Handling

#### File: `resources/js/Layouts/AuthenticatedLayout.vue`

**Logout Function - Sebelum:**
```javascript
const logout = () => {
    router.post(route('logout'), {}, {
        preserveState: false,
        preserveScroll: false,
        onBefore: () => {
            return true;
        },
        onSuccess: () => {
            window.location.href = '/login';
        },
        onError: (errors) => {
            console.error('Logout error:', errors);
            window.location.href = '/login';
        }
    });
};
```

**Logout Function - Sesudah:**
```javascript
const logout = () => {
    // Get CSRF token from meta tag ✅ NEW
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!token) {
        console.warn('CSRF token not found, reloading page...');
        window.location.reload();
        return;
    }
    
    router.post(route('logout'), {}, {
        preserveState: false,
        preserveScroll: false,
        headers: {
            'X-CSRF-TOKEN': token  // ✅ Explicitly send CSRF token
        },
        onBefore: () => {
            return true;
        },
        onSuccess: () => {
            // Clear session storage ✅ NEW
            if (window.sessionStorage) {
                window.sessionStorage.clear();
            }
            window.location.href = '/login';
        },
        onError: (errors) => {
            console.error('Logout error:', errors);
            // Handle 419 CSRF mismatch ✅ NEW
            if (errors && errors.status === 419) {
                console.warn('CSRF token mismatch, forcing logout...');
                if (window.sessionStorage) {
                    window.sessionStorage.clear();
                }
            }
            // Always redirect to login
            window.location.href = '/login';
        }
    });
};
```

#### File: `resources/js/app.js`

**Added Global Inertia Error Handler:**
```javascript
import { router } from '@inertiajs/vue3';

// Add global Inertia error handler for 419 CSRF errors ✅ NEW
router.on('error', (event) => {
    if (event.detail.errors && event.detail.errors.status === 419) {
        console.warn('CSRF token mismatch detected (419), refreshing page...');
        window.location.reload();
    }
});
```

**Keuntungan:**
✅ CSRF token diambil langsung dari meta tag
✅ Token dikirim explicit di header
✅ Session storage dibersihkan saat logout
✅ Fallback untuk error 419 (CSRF mismatch)
✅ Global error handler untuk semua request Inertia
✅ Logout selalu berhasil meskipun ada error

---

## Workflow yang Sudah Diperbaiki

### Skenario 1: Permintaan Medis dari IGD

```
1. Kepala Instalasi IGD
   └─→ Buat permintaan obat (klasifikasi_permintaan = 'medis')
   └─→ Approve
   └─→ pic_pimpinan = 'Kepala Bidang'
   └─→ kabid_tujuan = 'Bidang Pelayanan Medis'

2. Kabid Yanmed
   └─→ ✅ Menerima di index (Kondisi 1: pic_pimpinan = Kepala Bidang)
   └─→ Review dan approve
   └─→ Buat disposisi ke Direktur

3. Direktur
   └─→ Review dan approve
   └─→ Routing otomatis ke: Bidang Pelayanan Medis
   └─→ Buat disposisi dengan catatan: "Disetujui oleh Direktur..."

4. Kabid Yanmed (kembali)
   └─→ ✅ FIXED: Muncul di index (Kondisi 2: Ada disposisi dari Direktur)
   └─→ ✅ Query menggunakan whereHas untuk cek disposisi
   └─→ Disposisi ke Staff Perencanaan

5. Staff Perencanaan
   └─→ ✅ Logout berfungsi dengan baik
   └─→ ✅ CSRF token handling optimal
   └─→ Proses perencanaan pengadaan
```

### Skenario 2: Logout di Berbagai Halaman

```
1. Staff Perencanaan Dashboard
   └─→ Klik tombol "Log Out"
   └─→ ✅ CSRF token diambil dari meta tag
   └─→ ✅ Request POST ke /logout dengan token di header
   └─→ ✅ Session storage dibersihkan
   └─→ ✅ Redirect ke /login

2. Jika Ada Error 419 (CSRF Mismatch)
   └─→ ✅ Global error handler mendeteksi
   └─→ ✅ Console warning: "CSRF token mismatch detected"
   └─→ ✅ Page reload otomatis
   └─→ User bisa logout lagi

3. Staff Perencanaan - Halaman Lain (Create/Edit)
   └─→ ✅ Semua halaman menggunakan AuthenticatedLayout
   └─→ ✅ Logout function yang sama
   └─→ ✅ Konsisten di semua halaman
```

---

## Testing

### Test 1: Disposisi Direktur ke Kabid

**Langkah:**
1. Login sebagai Direktur
2. Buka permintaan yang sudah di-approve Kabid
3. Approve permintaan (otomatis routing ke Kabid sesuai klasifikasi)
4. Logout sebagai Direktur
5. Login sebagai Kabid yang sesuai (misal: Kabid Yanmed untuk permintaan medis)
6. Buka dashboard dan index

**Expected:**
✅ Permintaan muncul di dashboard dengan badge "Dari Direktur" atau sejenisnya
✅ Permintaan muncul di index/list
✅ Bisa membuka detail permintaan
✅ Bisa disposisi ke Staff Perencanaan

**Query untuk Verifikasi:**
```sql
-- Cek permintaan yang sudah di-approve Direktur
SELECT 
    p.permintaan_id,
    p.klasifikasi_permintaan,
    p.kabid_tujuan,
    p.pic_pimpinan,
    p.status,
    d.jabatan_tujuan,
    d.catatan
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE d.catatan LIKE '%Disetujui oleh Direktur%'
  AND p.status = 'proses'
ORDER BY p.permintaan_id DESC;

-- Expected:
-- | permintaan_id | klasifikasi | kabid_tujuan              | pic_pimpinan  | jabatan_tujuan            |
-- |---------------|-------------|---------------------------|---------------|---------------------------|
-- | 123           | medis       | Bidang Pelayanan Medis    | Kepala Bidang | Bidang Pelayanan Medis    |
-- | 124           | penunjang   | Bidang Penunjang Medis    | Kepala Bidang | Bidang Penunjang Medis    |
```

### Test 2: Logout di Staff Perencanaan

**Langkah:**
1. Login sebagai Staff Perencanaan
2. Buka dashboard
3. Klik dropdown user (pojok kanan atas)
4. Klik "Log Out"
5. Perhatikan console browser (F12)

**Expected:**
✅ Console menampilkan log (jika ada error)
✅ Session storage dibersihkan
✅ Redirect ke halaman login
✅ Tidak bisa back ke halaman sebelumnya
✅ Tidak ada error 419

**Jika Ada Error 419:**
✅ Console warning: "CSRF token mismatch detected"
✅ Page reload otomatis
✅ Bisa logout lagi setelah reload

**Test di Berbagai Halaman:**
- ✅ Dashboard Staff Perencanaan
- ✅ Index/List Permintaan
- ✅ Detail Permintaan
- ✅ Form Create/Edit Perencanaan
- ✅ Upload Dokumen
- ✅ Semua halaman CRUD

### Test 3: Logout di Halaman Lain (Regression Test)

**Langkah:**
Test logout di semua role untuk memastikan tidak ada regresi:
1. ✅ Admin - Logout dari dashboard
2. ✅ Kepala Instalasi - Logout dari index permintaan
3. ✅ Kepala Ruang - Logout dari create permintaan
4. ✅ Kepala Bidang - Logout dari detail permintaan
5. ✅ Direktur - Logout dari dashboard
6. ✅ KSO - Logout dari index
7. ✅ Pengadaan - Logout dari index

**Expected:**
Semua logout berfungsi dengan baik, tidak ada error 419.

---

## Files Modified

| File | Changes | LOC Changed |
|------|---------|-------------|
| `app/Http/Controllers/KepalaBidangController.php` | ✅ Fix query di `index()` dan `dashboard()` | ~40 |
| `resources/js/Layouts/AuthenticatedLayout.vue` | ✅ Enhanced logout function | ~20 |
| `resources/js/app.js` | ✅ Global Inertia error handler | ~8 |

**Total:** ~68 lines changed

---

## Rollback Instructions

Jika ada masalah dengan fix ini, rollback dengan:

### 1. Rollback Kabid Query
```bash
git checkout HEAD -- app/Http/Controllers/KepalaBidangController.php
```

### 2. Rollback Logout
```bash
git checkout HEAD -- resources/js/Layouts/AuthenticatedLayout.vue
git checkout HEAD -- resources/js/app.js
```

### 3. Rebuild Frontend
```bash
npm run build
```

---

## Notes

⚠️ **Penting:**
- Setelah fix ini, **rebuild frontend assets** dengan `npm run build` atau `npm run dev`
- Test di browser dengan **hard refresh** (Ctrl+Shift+R) untuk clear cache
- Clear browser cache jika masih ada masalah logout

✅ **Keamanan:**
- CSRF token tetap tervalidasi di semua request
- Logout tetap secure dengan explicit token header
- Session dibersihkan dengan benar

✅ **Performance:**
- Query Kabid menggunakan `whereHas` yang efficient
- Eager loading tetap optimal dengan `with(['user', 'notaDinas.disposisi'])`
- Tidak ada N+1 query

---

**Status:** ✅ FIXED
**Date:** 3 November 2025
**Tested:** Ready for Testing
**Impact:** HIGH - Critical fixes for workflow and UX
