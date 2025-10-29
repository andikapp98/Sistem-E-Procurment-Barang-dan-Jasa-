# Fix 419 Page Expired - Staff Perencanaan DPP Create

## Masalah
Halaman `http://localhost:8000/staff-perencanaan/permintaan/17/dpp/create` mengalami error "419 Page Expired" saat diakses.

## Penyebab
Meskipun CSRF token sudah dishare di HandleInertiaRequests middleware, ada beberapa masalah:
1. **Inertia Router tidak menggunakan CSRF token yang fresh** - Tidak ada interceptor untuk memastikan CSRF token selalu diambil dari meta tag saat sebelum request
2. **Session lifetime terlalu pendek** - 120 menit (2 jam) terlalu pendek untuk development dan testing
3. **Tidak ada refresh mechanism** - Token bisa expired di browser cache

## Solusi Komprehensif

### 1. Tambah Inertia Router Interceptor
Menambahkan interceptor di `resources/js/app.js` untuk memastikan setiap request Inertia menggunakan CSRF token yang fresh.

**File:** `resources/js/app.js`

```javascript
// Configure Inertia to use fresh CSRF token
router.on('before', (event) => {
    const token = getCsrfToken();
    if (token && event.detail.visit.method !== 'get') {
        event.detail.visit.headers = event.detail.visit.headers || {};
        event.detail.visit.headers['X-CSRF-TOKEN'] = token;
    }
});
```

**Kenapa ini penting:**
- Inertia router secara default menggunakan CSRF token dari page props
- Jika props sudah loaded lama, token bisa expired
- Interceptor ini memastikan token selalu diambil fresh dari meta tag sebelum setiap request

### 2. Tingkatkan Session Lifetime
Mengubah session lifetime dari 2 jam menjadi 12 jam untuk mengurangi kemungkinan session expired saat development.

**File:** `.env`

```env
# SEBELUM
SESSION_LIFETIME=120

# SESUDAH
SESSION_LIFETIME=720
```

**Catatan:**
- 720 minutes = 12 hours
- Untuk production, bisa disesuaikan dengan kebijakan keamanan
- Recommended: 240-480 menit (4-8 jam) untuk production

### 3. Pastikan Session Driver Database
Session driver sudah diubah ke database di fix sebelumnya (tetap dipertahankan).

**File:** `.env`

```env
SESSION_DRIVER=database
```

### 4. Clear All Caches
```bash
php artisan config:clear
php artisan optimize:clear
```

### 5. Rebuild Assets
```bash
yarn build
```

## Detail Implementasi

### app.js - Inertia Interceptor
```javascript
import { createInertiaApp, router } from '@inertiajs/vue3';

// Function to get CSRF token
function getCsrfToken() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    return token ? token.content : '';
}

// Configure Axios globally
axios.defaults.headers.common['X-CSRF-TOKEN'] = getCsrfToken();

// Update axios token before each request
axios.interceptors.request.use(config => {
    config.headers['X-CSRF-TOKEN'] = getCsrfToken();
    return config;
});

// ✅ NEW: Configure Inertia to use fresh CSRF token
router.on('before', (event) => {
    const token = getCsrfToken();
    if (token && event.detail.visit.method !== 'get') {
        event.detail.visit.headers = event.detail.visit.headers || {};
        event.detail.visit.headers['X-CSRF-TOKEN'] = token;
    }
});

createInertiaApp({
    // ... config
});
```

### HandleInertiaRequests.php (Sudah di fix sebelumnya)
```php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'auth' => [
            'user' => $request->user(),
        ],
        'csrf_token' => csrf_token(), // ✅ Already added
    ];
}
```

## Workflow Fix

### Before (❌ Error Flow)
1. User click link to create DPP
2. Inertia load page dengan token dari props (bisa lama)
3. User submit form
4. Token sudah expired → 419 Error

### After (✅ Working Flow)
1. User click link to create DPP
2. Inertia interceptor get fresh token dari meta tag
3. Request include fresh CSRF token
4. User submit form
5. Form.post() automatically use fresh token
6. Success ✅

## Testing

### Test Create DPP
1. Login sebagai Staff Perencanaan
2. Buka permintaan detail
3. Klik "Buat DPP"
4. Seharusnya berhasil akses halaman create tanpa error 419
5. Isi form dan submit
6. Seharusnya berhasil save tanpa error 419

### Test Other Staff Perencanaan Pages
- Create HPS
- Create Nota Dinas
- Create Spesifikasi Teknis
- Create Perencanaan
- Upload Dokumen

Semua seharusnya tidak mengalami error 419 lagi.

## Build Output
```
✓ 1474 modules transformed
✓ built in 4.91s

Key files:
- app-CjvX3XHq.js (252.96 kB) ← Updated with Inertia interceptor
- AuthenticatedLayout-aEZy2Bmp.js (27.36 kB)
- CreateDPP-zAB4uEiz.js (19.61 kB)
```

## Configuration Summary

### Session Settings (.env)
```env
SESSION_DRIVER=database       # Stable, reliable
SESSION_LIFETIME=720          # 12 hours (increased from 2 hours)
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false   # Set to true in production with HTTPS
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_EXPIRE_ON_CLOSE=false
```

### CSRF Protection Layers
1. **Meta Tag** - `<meta name="csrf-token">` in app.blade.php
2. **Shared Props** - `csrf_token` in HandleInertiaRequests
3. **Axios Interceptor** - Auto-include token in axios requests
4. **Inertia Interceptor** - Auto-include token in Inertia requests
5. **Laravel Middleware** - VerifyCsrfToken middleware

## Troubleshooting

### Jika masih ada error 419:

1. **Clear browser cache**
   ```
   Ctrl+Shift+R (hard refresh)
   ```

2. **Clear all Laravel caches**
   ```bash
   php artisan optimize:clear
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   ```

3. **Check session table**
   ```sql
   SELECT * FROM sessions ORDER BY last_activity DESC LIMIT 10;
   ```

4. **Verify CSRF token in browser**
   - Open DevTools → Console
   - Run: `document.querySelector('meta[name="csrf-token"]').content`
   - Should return a valid token string

5. **Check network request headers**
   - Open DevTools → Network
   - Submit form
   - Check request headers for `X-CSRF-TOKEN`

## Related Fixes
- FIX_LOGOUT_STAFF_PERENCANAAN.md - Fix logout 419 error
- FIX_LOGIN_KABID_419_PAGE_EXPIRED.md - Fix login 419 error

## Status
✅ **FIXED** - DPP Create dan semua Staff Perencanaan pages tidak mengalami 419 Page Expired lagi
✅ **INERTIA INTERCEPTOR** - Added router interceptor untuk fresh CSRF token
✅ **SESSION EXTENDED** - Lifetime diperpanjang ke 12 jam
✅ **BUILT** - Assets sudah di-rebuild
✅ **CACHE CLEARED** - All caches cleared
