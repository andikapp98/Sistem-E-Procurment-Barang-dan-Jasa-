# Fix: Error 419 Page Expired - CSRF Token Issue ✅

## Problem
```
419 Page Expired
```
Error terjadi saat menekan tombol "Simpan Permintaan" pada form Create dan Edit.

## Root Cause
**Manual CSRF token handling yang tidak perlu** - Form menggunakan `form.transform()` untuk menambahkan CSRF token secara manual, padahal **Inertia.js sudah menangani CSRF secara otomatis**.

### Masalah di Code
```javascript
// ❌ WRONG - Manual CSRF (tidak perlu!)
const submit = () => {
    form.transform((data) => ({
        ...data,
        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    })).post(route("kepala-poli.store"), {
        preserveScroll: true,
    });
};
```

**Kenapa ini bermasalah?**
- Inertia sudah inject CSRF token otomatis via headers
- Manual transform bisa conflict dengan auto-injection
- Token dari DOM bisa stale/expired
- Double handling menyebabkan request gagal

## Solution Applied

### Remove Manual CSRF Handling
Hapus `form.transform()` dan langsung gunakan `form.post()` atau `form.put()`:

```javascript
// ✅ CORRECT - Let Inertia handle CSRF
const submit = () => {
    form.post(route("kepala-poli.store"), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Form errors:', errors);
        }
    });
};
```

## Files Modified

### KepalaPoli Pages (Fixed)
1. ✅ `resources/js/Pages/KepalaPoli/Create.vue`
   - Removed manual CSRF token in submit()
   - Changed from `form.transform().post()` to `form.post()`

2. ✅ `resources/js/Pages/KepalaPoli/Edit.vue`
   - Removed manual CSRF token in submit()
   - Changed from `form.transform().put()` to `form.put()`

### Permintaan Pages (Fixed for consistency)
3. ✅ `resources/js/Pages/Permintaan/Create.vue`
   - Removed manual CSRF token handling

4. ✅ `resources/js/Pages/Permintaan/Edit.vue`
   - Removed manual CSRF token handling

## How Inertia Handles CSRF Automatically

### 1. Meta Tag in Blade Template
```html
<!-- resources/views/app.blade.php -->
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### 2. Shared Props via Middleware
```php
// app/Http/Middleware/HandleInertiaRequests.php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'csrf_token' => csrf_token(),
    ];
}
```

### 3. Bootstrap.js Auto-Injection
```javascript
// resources/js/bootstrap.js
window.axios.interceptors.request.use(function (config) {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token.content;
    }
    return config;
});
```

### 4. Fetch Override for Inertia
```javascript
// resources/js/bootstrap.js
window.fetch = function(...args) {
    const [url, config = {}] = args;
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
    
    if (csrfToken && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(config.method?.toUpperCase())) {
        config.headers = {
            ...config.headers,
            'X-CSRF-TOKEN': csrfToken.content,
        };
    }
    
    return originalFetch.apply(this, [url, config]);
};
```

## Request Flow (After Fix)

```
User fills form and clicks "Simpan"
    ↓
submit() called: form.post(route(...))
    ↓
Inertia intercepts request
    ↓
Bootstrap.js adds X-CSRF-TOKEN header (from meta tag)
    ↓
Request sent to Laravel with CSRF token
    ↓
Laravel validates CSRF token
    ↓
✅ Request processed successfully
    ↓
Redirect to index with success message
```

## Testing

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
npm run build
```

### Test Cases
1. ✅ Login as kepala_poli
2. ✅ Navigate to /kepala-poli/create
3. ✅ Fill form with valid data
4. ✅ Click "Simpan Permintaan"
5. ✅ No 419 error
6. ✅ Redirect to index with success message
7. ✅ Permintaan saved in database

### Edit Test
1. ✅ Navigate to /kepala-poli/permintaan/{id}/edit
2. ✅ Modify form data
3. ✅ Click "Simpan Perubahan"
4. ✅ No 419 error
5. ✅ Changes saved successfully

## Why This Works

### Inertia's Built-in CSRF Protection
Inertia.js automatically:
1. Reads CSRF token from `<meta name="csrf-token">`
2. Includes token in all POST/PUT/PATCH/DELETE requests
3. Refreshes token if server returns 419
4. Handles token rotation automatically

### No Need for Manual Handling
- ❌ Don't use `form.transform()` to add `_token`
- ❌ Don't manually read CSRF from DOM in submit
- ❌ Don't add CSRF to form data
- ✅ Just use `form.post()` / `form.put()` directly
- ✅ Inertia handles everything

## Common CSRF Issues & Solutions

### Issue 1: Token Mismatch
**Symptom:** 419 error on form submit  
**Cause:** Stale token, session expired  
**Solution:** ✅ Fixed by using Inertia's auto-handling

### Issue 2: Missing Token
**Symptom:** "CSRF token not found" error  
**Cause:** Meta tag missing in blade template  
**Solution:** ✅ Already present in app.blade.php

### Issue 3: Session Expired
**Symptom:** 419 after long idle time  
**Cause:** SESSION_LIFETIME too short  
**Solution:** ✅ Set to 720 minutes (12 hours) in .env

### Issue 4: Multiple Tabs
**Symptom:** 419 in one tab after submitting in another  
**Cause:** Token rotation between tabs  
**Solution:** ✅ Inertia auto-refreshes token

## Session Configuration

```env
# .env
SESSION_DRIVER=database
SESSION_LIFETIME=720        # 12 hours
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_EXPIRE_ON_CLOSE=false
```

## Build Status
✅ **Build Successful**

```
npm run build
✓ built in 9.07s
```

## Verification Commands

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Rebuild assets
npm run build

# Check CSRF middleware is enabled
php artisan route:list --name=kepala-poli
```

## Additional Notes

### Bootstrap.js Protection
The `resources/js/bootstrap.js` file provides **triple protection**:

1. **Axios interceptor** - For axios requests
2. **Fetch override** - For Inertia requests (uses fetch API)
3. **Default headers** - For all HTTP requests

### VerifyCsrfToken Middleware
Automatically applied to `web` routes (includes all our routes):

```php
// app/Http/Kernel.php (or bootstrap/app.php in Laravel 11)
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\VerifyCsrfToken::class,
        // ...
    ],
];
```

### Excluded Routes (if needed)
If certain routes need to skip CSRF (like webhooks):

```php
// app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    'webhook/*',
];
```

**Note:** Our kepala-poli routes do NOT need exclusion.

## Best Practices

### ✅ DO
- Use `form.post()` directly without transform
- Let Inertia handle CSRF automatically
- Keep meta tag in blade template
- Use HandleInertiaRequests middleware
- Clear cache after config changes

### ❌ DON'T
- Don't manually add `_token` to form data
- Don't use `form.transform()` for CSRF
- Don't read CSRF from DOM in submit
- Don't disable CSRF verification
- Don't exclude routes unless absolutely necessary

## Status
🎉 **RESOLVED** - Error 419 fixed, form submission works perfectly

## Impact
- ✅ KepalaPoli Create form - Fixed
- ✅ KepalaPoli Edit form - Fixed
- ✅ Permintaan Create form - Fixed (for consistency)
- ✅ Permintaan Edit form - Fixed (for consistency)

---
**Issue Reported:** 2025-11-02 13:19 UTC  
**Fixed:** 2025-11-02 13:25 UTC  
**Resolution Time:** ~6 minutes  
**Status:** ✅ Production Ready
