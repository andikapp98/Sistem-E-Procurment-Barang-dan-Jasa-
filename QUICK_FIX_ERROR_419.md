# Quick Fix: Error 419 Page Expired

## Problem
Error 419 "Page Expired" saat akses view atau submit form di semua role.

## Solution (3 Files)

### 1. `resources/views/app.blade.php`
Tambahkan CSRF meta tag:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### 2. `resources/js/bootstrap.js`
Setup axios untuk auto-send CSRF token:
```javascript
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}
```

### 3. `app/Http/Middleware/HandleInertiaRequests.php`
Share CSRF token ke semua page:
```php
'csrf_token' => csrf_token(),
```

## After Fix

```bash
# Clear cache
php artisan config:clear
php artisan route:clear

# Rebuild frontend
npm run build

# Test login dan aksi
```

## Status
✅ **FIXED** - CSRF token configured in 3 places
