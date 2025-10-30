# Fix: Error 419 Page Expired di Semua Role

## Masalah
Setiap role mendapat error 419 "Page Expired" saat mengakses view atau melakukan aksi.

## Root Cause
CSRF token tidak dikonfigurasi dengan benar:
1. Tidak ada `<meta name="csrf-token">` di layout
2. Axios tidak dikonfigurasi untuk mengirim CSRF token
3. Inertia tidak share CSRF token ke frontend

## Perbaikan yang Dilakukan

### 1. Tambah CSRF Token Meta Tag di Layout
**File:** `resources/views/app.blade.php`

```blade
<!-- BEFORE -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title inertia>{{ config('app.name') }}</title>

<!-- AFTER -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title inertia>{{ config('app.name') }}</title>
```

### 2. Konfigurasi Axios untuk CSRF Token
**File:** `resources/js/bootstrap.js`

```javascript
// BEFORE
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// AFTER
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Setup CSRF token for axios
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
```

### 3. Share CSRF Token via Inertia
**File:** `app/Http/Middleware/HandleInertiaRequests.php`

```php
// BEFORE
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'auth' => [
            'user' => $request->user(),
        ],
    ];
}

// AFTER
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'auth' => [
            'user' => $request->user(),
        ],
        'csrf_token' => csrf_token(),
    ];
}
```

## Penjelasan CSRF Protection

### Apa itu Error 419?
Error 419 "Page Expired" terjadi karena Laravel CSRF (Cross-Site Request Forgery) protection mendeteksi request tanpa valid CSRF token atau token yang sudah expired.

### Cara Kerja CSRF di Laravel + Inertia
1. **Laravel** generate CSRF token untuk setiap session
2. **Meta tag** di HTML menyimpan token: `<meta name="csrf-token" content="...">`
3. **Axios** otomatis ambil token dari meta tag dan kirim di header setiap request
4. **Laravel middleware** verifikasi token di setiap POST/PUT/DELETE request

### Session Lifetime
- Default: 120 menit (2 jam)
- Konfigurasi di `.env`: `SESSION_LIFETIME=120`
- Jika session expired, CSRF token juga expired → Error 419

## Cara Mengatasi Session Expired

### Opsi 1: Perpanjang Session Lifetime
Edit `.env`:
```env
SESSION_LIFETIME=480  # 8 jam
```

### Opsi 2: Auto Refresh Token (Advanced)
Tambahkan interceptor di `resources/js/app.js`:
```javascript
import axios from 'axios';

// Intercept responses untuk detect session expired
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            // Session expired, reload page untuk refresh token
            window.location.reload();
        }
        return Promise.reject(error);
    }
);
```

### Opsi 3: Keep Session Alive
Tambahkan heartbeat di `resources/js/app.js`:
```javascript
// Ping server setiap 5 menit untuk keep session alive
setInterval(() => {
    axios.get('/api/ping').catch(() => {});
}, 5 * 60 * 1000);
```

## Testing

### 1. Clear Cache dan Session
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 2. Rebuild Frontend
```bash
npm run build
# atau untuk development
npm run dev
```

### 3. Test Login dan Aksi
1. Login dengan salah satu role
2. Coba akses dashboard
3. Coba submit form (approve, reject, dll)
4. Verify tidak ada error 419

### 4. Cek CSRF Token di Browser
1. Open DevTools → Network tab
2. Submit form
3. Check request headers, harus ada: `X-CSRF-TOKEN: ...`
4. Check response, tidak boleh 419

## Files Modified
1. `resources/views/app.blade.php` - Line 6: Tambah CSRF meta tag
2. `resources/js/bootstrap.js` - Line 6-13: Setup axios CSRF token
3. `app/Http/Middleware/HandleInertiaRequests.php` - Line 37: Share CSRF token

## Session Configuration
File: `config/session.php`
- Driver: `file` (dari .env)
- Lifetime: `120` minutes (dari .env)
- Path: `/`
- Domain: `null`
- Secure: `false` (untuk local development)
- HTTP Only: `true`
- Same Site: `lax`

## Additional Notes

### Development vs Production
- **Development:** Secure cookie = false, lifetime bisa lebih panjang
- **Production:** Secure cookie = true (HTTPS), lifetime sesuai kebutuhan

### Clear Sessions Manually
```bash
# Clear all sessions
rm -rf storage/framework/sessions/*

# Atau via artisan (Laravel 10+)
php artisan session:clear
```

## Status
✅ **FIXED** - CSRF token dikonfigurasi di 3 tempat (layout, axios, inertia)

## Troubleshooting Lanjutan

Jika masih error 419:
1. Cek permission folder `storage/framework/sessions` (harus writable)
2. Pastikan `.env` ada `APP_KEY`
3. Regenerate app key: `php artisan key:generate`
4. Clear browser cookies dan session
5. Cek console browser untuk error JavaScript
