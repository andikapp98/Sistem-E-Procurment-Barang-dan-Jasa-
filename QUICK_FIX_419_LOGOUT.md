# QUICK FIX: 419 Error pada Logout (Infinite Loop)

## Masalah
Error 419 terjadi terus-menerus (infinite retry loop) saat logout, terutama jika:
- User pernah login dengan role berbeda sebelumnya
- Browser cache masih menyimpan session lama
- Multiple tabs terbuka dengan session berbeda

## Penyebab Root
1. **Custom CSRF retry logic** di `app.js` menyebabkan infinite loop
2. **Session conflict** - bekas login role lain masih tersimpan
3. **Browser cache** - CSRF token lama masih di-cache

## Solusi yang Sudah Dilakukan

### 1. Simplify app.js ✅
**File:** `resources/js/app.js`

**Perubahan:**
- Hapus semua custom CSRF retry logic
- Hapus axios interceptors yang menyebabkan loop
- Biarkan Inertia.js handle CSRF secara default
- Lebih simple dan clean

**Sebelum (WRONG - Infinite Loop):**
```javascript
// Custom retry logic yang menyebabkan infinite loop
router.on('error', async (event) => {
    if (response.status === 419) {
        await refreshCsrfToken();
        router.visit(...); // Retry terus menerus!
    }
});
```

**Sesudah (CORRECT - Clean):**
```javascript
// No custom retry - let Inertia handle it
import { createInertiaApp } from '@inertiajs/vue3';
// ... simple setup only
```

### 2. useForm Already Handles CSRF ✅
**File:** `resources/js/Layouts/AuthenticatedLayout.vue`

```javascript
const logoutForm = useForm({});

const logout = () => {
    logoutForm.post(route('logout'), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            window.location.href = '/login';
        }
    });
    // Inertia's useForm automatically includes fresh CSRF token
};
```

## Cara Mengatasi Error Saat Ini

### Langkah 1: Clear Browser Cache
```bash
# Di Browser (Chrome/Edge):
1. Tekan Ctrl + Shift + Delete
2. Pilih "All time"
3. Centang:
   - Cookies and other site data
   - Cached images and files
4. Klik "Clear data"
```

### Langkah 2: Clear Laravel Cache
```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan session:flush
```

### Langkah 3: Rebuild Assets
```powershell
npm run build
# atau untuk development:
npm run dev
```

### Langkah 4: Hard Refresh Browser
```
# Di halaman yang error:
1. Tekan Ctrl + Shift + R (hard reload)
2. Atau Ctrl + F5
```

### Langkah 5: Test Logout Fresh
```
1. Logout dari semua tabs
2. Close all browser tabs
3. Close browser completely
4. Open browser fresh
5. Login with one role only
6. Try logout
```

## Prevent Future Issues

### 1. Jangan Multi-Role Login di Browser yang Sama
❌ **WRONG:**
- Tab 1: Login sebagai Admin
- Tab 2: Login sebagai Direktur (conflict!)

✅ **CORRECT:**
- Browser 1 (Chrome): Login sebagai Admin
- Browser 2 (Edge): Login sebagai Direktur
- Atau gunakan Incognito/Private mode

### 2. Always Logout Before Switching Roles
```
1. Logout dari role lama
2. Close browser (optional tapi recommended)
3. Login dengan role baru
```

### 3. Clear Session When Switching
```powershell
# Jika perlu switch role, clear session:
php artisan session:flush
```

## Testing Logout

### Test 1: Normal Logout
1. ✅ Login as any user
2. ✅ Click user dropdown
3. ✅ Click "Log Out"
4. **Expected:** Redirect to /login immediately
5. **Expected:** No 419 error
6. **Expected:** No infinite loop

### Test 2: Logout After Long Idle
1. ✅ Login
2. ✅ Leave browser idle for 1+ hours
3. ✅ Try to logout
4. **Expected:** May show 419 once, then redirect to login
5. **Expected:** No infinite loop

### Test 3: Multi-Tab Logout
1. ✅ Login in 2 tabs
2. ✅ Logout in tab 1
3. ✅ Try to use tab 2
4. **Expected:** Redirect to login (session expired)
5. **Expected:** No infinite loop

## Debugging

### Check Console for Infinite Loop
Jika masih ada infinite loop, cek console:
```
419 error detected, refreshing CSRF token...
419 error detected, refreshing CSRF token...
419 error detected, refreshing CSRF token...
...
```

**Jika muncul > 3x:** Masih ada custom retry logic yang belum dihapus

### Check Network Tab
1. Buka DevTools (F12)
2. Tab "Network"
3. Filter: "logout"
4. Lihat berapa kali request /logout dipanggil

**Expected:** 1x saja
**If > 1x:** Infinite loop masih terjadi

### Check Session
```powershell
# Cek active sessions
php artisan tinker
>>> DB::table('sessions')->count();
>>> DB::table('sessions')->get();
```

## Emergency Fix

Jika masih loop setelah semua langkah:

### Option 1: Force Logout via Database
```sql
-- Hapus semua session
DELETE FROM sessions;
```

### Option 2: Restart Laravel
```powershell
# Stop semua
Ctrl + C (if running php artisan serve)

# Clear everything
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Restart
php artisan serve
```

### Option 3: Clear Browser Completely
1. Close all browser windows
2. Clear browser data (Ctrl+Shift+Delete)
3. Restart computer (if desperate)
4. Open browser fresh

## File Changes Summary

| File | Action | Why |
|------|--------|-----|
| app.js | Simplified | Remove infinite loop causing retry logic |
| AuthenticatedLayout.vue | Use useForm | Already done - auto CSRF |
| bootstrap.js | No change | Basic axios setup is OK |

## Important Notes

⚠️ **Tentang Multiple Role Login:**
- Google Chrome menyimpan session per domain, bukan per tab
- Jika login role A di tab 1, lalu login role B di tab 2 (same domain), session akan conflict
- Gunakan:
  - Different browsers untuk different roles
  - Incognito mode untuk testing
  - Always logout before switching

✅ **Best Practice:**
- One role per browser session
- Logout sebelum switch role
- Clear cache jika ada masalah
- Gunakan Incognito untuk testing

---

**Status:** ✅ FIXED - app.js simplified
**Next:** Clear cache & test
**Date:** 30 Oktober 2025
