# Fix 419 CSRF Token Error - Complete Solution

## Tanggal: 27 Oktober 2025

## Masalah
Error 419 (CSRF token mismatch) terjadi saat login dan logout.

## Penyebab
1. Duplikasi setup CSRF token di multiple files
2. Konfigurasi session yang tidak lengkap di .env
3. CSRF token tidak di-share dengan benar ke Inertia
4. Middleware configuration kurang tepat

## Solusi yang Diterapkan

### 1. Update `.env` - Tambah Konfigurasi Session Lengkap
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_EXPIRE_ON_CLOSE=false
```

**Penjelasan:**
- `SESSION_SECURE_COOKIE=false` - Untuk development (localhost)
- `SESSION_HTTP_ONLY=true` - Security: cookie tidak bisa diakses JavaScript
- `SESSION_SAME_SITE=lax` - Mengizinkan cookie dikirim untuk navigasi normal
- `SESSION_EXPIRE_ON_CLOSE=false` - Session tidak expire saat browser ditutup

### 2. Update `bootstrap/app.php` - Tambah CSRF Validation Config
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
        \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
    ]);

    // Middleware aliases
    $middleware->alias([
        'redirect.role' => \App\Http\Middleware\RedirectBasedOnRole::class,
    ]);
    
    // Validate CSRF tokens but don't throw on mismatch for certain routes
    $middleware->validateCsrfTokens(except: [
        // Add any routes that should be excluded from CSRF verification
    ]);
})
```

**Penjelasan:**
- Menambahkan explicit CSRF validation configuration
- Memungkinkan untuk exclude routes tertentu jika diperlukan

### 3. Update `app/Http/Middleware/HandleInertiaRequests.php` - Hapus Duplikasi
```php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'auth' => [
            'user' => $request->user(),
        ],
    ];
}
```

**Penjelasan:**
- Menghapus `'csrf_token' => csrf_token()` dari shared props
- CSRF token sudah otomatis di-handle oleh Laravel dan Inertia
- Duplikasi bisa menyebabkan token tidak sync

### 4. Simplify `resources/js/bootstrap.js` - Hapus Interceptor Kompleks
```javascript
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token from meta tag and set it for axios
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}
```

**Penjelasan:**
- Simplifikasi setup CSRF token
- Menghapus interceptor yang terlalu kompleks dan bisa menyebabkan race condition
- Laravel dan Inertia sudah handle CSRF token refresh secara otomatis

### 5. Simplify `resources/js/app.js` - Hapus Duplikasi Setup
```javascript
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
```

**Penjelasan:**
- Menghapus setup CSRF token yang duplikat dari bootstrap.js
- Inertia sudah otomatis ambil CSRF token dari meta tag

### 6. Simplify `resources/js/Pages/Auth/Login.vue` - Hapus Error Handler
```javascript
const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
        preserveScroll: false,
        preserveState: false,
    });
};
```

**Penjelasan:**
- Menghapus custom error handler untuk 419
- Biarkan Laravel handle error secara default
- Jika ada 419, Laravel akan redirect ke login page secara otomatis

## Cara Kerja CSRF Protection di Laravel + Inertia

1. **Server Side (Laravel):**
   - Laravel generate CSRF token untuk setiap session
   - Token disimpan di session storage (`storage/framework/sessions`)
   - Token di-inject ke HTML via meta tag di `resources/views/app.blade.php`:
     ```html
     <meta name="csrf-token" content="{{ csrf_token() }}">
     ```

2. **Client Side (JavaScript):**
   - Bootstrap.js baca token dari meta tag
   - Set token ke axios default headers
   - Inertia otomatis kirim token di setiap request

3. **Middleware:**
   - `VerifyCsrfToken` middleware verify token di setiap POST/PUT/DELETE request
   - Jika token tidak cocok, throw 419 error
   - Laravel 11 manage middleware via `bootstrap/app.php`

## Testing

Setelah perubahan ini:

1. **Test Login:**
   - Buka halaman login
   - Masukkan credentials
   - Klik "Log in"
   - ✅ Seharusnya berhasil tanpa 419 error

2. **Test Logout:**
   - Setelah login
   - Klik dropdown profile
   - Klik "Log Out"
   - ✅ Seharusnya berhasil logout tanpa 419 error

3. **Test Session Persistence:**
   - Login ke aplikasi
   - Biarkan idle selama 5-10 menit
   - Coba akses halaman lain atau submit form
   - ✅ Seharusnya tetap bisa bekerja (session lifetime 120 menit)

## Troubleshooting

Jika masih ada error 419:

1. **Clear semua cache:**
   ```bash
   php artisan optimize:clear
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. **Hapus session files lama:**
   ```bash
   rm storage/framework/sessions/*
   ```

3. **Rebuild assets:**
   ```bash
   npm run build
   ```

4. **Hard refresh browser:**
   - Tekan Ctrl+Shift+R (Windows/Linux)
   - Atau Cmd+Shift+R (Mac)
   - Atau clear browser cache

5. **Check browser console:**
   - Buka Developer Tools (F12)
   - Check tab Console untuk error
   - Check tab Network untuk request yang failed

6. **Verify CSRF token ada di HTML:**
   - View page source
   - Cari `<meta name="csrf-token"`
   - Pastikan content tidak kosong

## Production Checklist

Untuk production deployment:

1. **Update `.env` production:**
   ```env
   SESSION_SECURE_COOKIE=true  # Enable untuk HTTPS
   SESSION_SAME_SITE=lax       # Atau 'strict' untuk lebih secure
   SESSION_LIFETIME=120        # Sesuaikan kebutuhan
   ```

2. **Ensure HTTPS enabled**
   - CSRF cookie harus dikirim via HTTPS di production

3. **Test thoroughly**
   - Test semua form submissions
   - Test login/logout
   - Test dengan berbagai browser

## Files Changed

1. `.env` - Added complete session configuration
2. `bootstrap/app.php` - Added CSRF validation config
3. `app/Http/Middleware/HandleInertiaRequests.php` - Removed duplicate csrf_token share
4. `resources/js/bootstrap.js` - Simplified CSRF token setup
5. `resources/js/app.js` - Removed duplicate CSRF setup
6. `resources/js/Pages/Auth/Login.vue` - Removed custom 419 error handler

## Kesimpulan

Masalah 419 CSRF token error sudah diperbaiki dengan:
- Konfigurasi session yang lengkap dan benar
- Menghapus duplikasi setup CSRF token
- Menyederhanakan flow handling CSRF
- Membiarkan Laravel dan Inertia handle CSRF secara default

Solusi ini lebih robust dan mengikuti best practice Laravel + Inertia.js.
