# Perbaikan Error 419 CSRF Token Mismatch

## Masalah
Setiap input form mengalami error 419 (Page Expired) karena CSRF token mismatch.

## Perbaikan yang Dilakukan

### 1. Update Session Configuration (`config/session.php`)
- Mengubah default session lifetime dari 120 menit menjadi 720 menit (sesuai .env)
- Memastikan session configuration konsisten dengan .env

### 2. Update Inertia Middleware (`app/Http/Middleware/HandleInertiaRequests.php`)
- Mengubah `csrf_token` menjadi closure function untuk memastikan token selalu fresh
- Menambahkan flash message support

### 3. Perbaikan Frontend CSRF Handling (`resources/js/app.js`)
- Menambahkan `router.on('before')` handler untuk inject CSRF token di setiap request Inertia
- Memperbaiki `router.on('error')` handler untuk handle 419 errors dengan benar
- Menghapus duplicate error handling yang tidak perlu

### 4. Bootstrap Axios (`resources/js/bootstrap.js`)
- Sudah ada interceptor untuk handle CSRF token
- Sudah ada error handler untuk auto-reload saat 419 error

### 5. Route Logout (`routes/auth.php`)
- Menambahkan GET route untuk logout yang redirect back
- Mempertahankan POST route sebagai method utama untuk logout

### 6. Clear Cache & Rebuild
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
npm run build
```

### 7. Clear Old Sessions
- Menghapus semua session lama dari database untuk fresh start

## Cara Kerja CSRF Protection Sekarang

1. **Meta Tag**: CSRF token disertakan di `<meta name="csrf-token">` di app.blade.php
2. **Inertia Shared Props**: Token di-share ke semua Vue components via `$page.props.csrf_token`
3. **Axios**: Otomatis inject CSRF token via interceptor di setiap request
4. **Inertia Router**: Inject CSRF token via `before` hook di setiap Inertia request
5. **Error Handling**: Auto-reload page saat terjadi 419 error untuk get fresh token

## Testing
1. Clear browser cookies/cache
2. Login fresh
3. Coba submit form (Create, Update, Delete)
4. Pastikan tidak ada error 419

## Troubleshooting
Jika masih terjadi error 419:

1. **Clear semua cache**:
   ```bash
   php artisan optimize:clear
   ```

2. **Restart development server**:
   ```bash
   php artisan serve
   ```

3. **Clear browser cache dan cookies**

4. **Check session driver di .env**:
   ```
   SESSION_DRIVER=database
   SESSION_LIFETIME=720
   ```

5. **Pastikan session table exists**:
   ```bash
   php artisan migrate
   ```

6. **Check storage permissions** (Linux/Mac):
   ```bash
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```

## Catatan Penting
- Session lifetime sekarang 720 menit (12 jam)
- CSRF token akan auto-refresh di setiap request Inertia
- Error 419 akan trigger auto page reload untuk get fresh token
- Logout sekarang support both GET (redirect) dan POST (actual logout)
