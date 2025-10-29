# Fix: KSO Create - 419 Page Expired Error

## Problem
Saat mengakses `/kso/permintaan/17/create`, muncul error **419 Page Expired**.

## Root Cause
Error 419 terjadi karena:
1. **CSRF Token Expired** - Session Laravel sudah timeout
2. **CSRF Token Mismatch** - Token tidak valid atau hilang
3. **Session Driver Issue** - Session tidak tersimpan dengan benar

## Solutions Applied

### 1. Enhanced Error Handling in Create.vue
**File:** `resources/js/Pages/KSO/Create.vue`

**Added:**
```javascript
const submit = () => {
    form.post(route("kso.store", props.permintaan.permintaan_id), {
        preserveScroll: true,
        forceFormData: true,  // ✅ Force multipart for file uploads
        onSuccess: (page) => {
            console.log('KSO berhasil dibuat');
        },
        onError: (errors) => {
            console.error('Error creating KSO:', errors);
            // ✅ Handle 419 error specifically
            if (errors && errors.message && errors.message.includes('419')) {
                alert('Session expired. Please refresh the page and try again.');
                window.location.reload();
            }
        },
        onFinish: () => {
            console.log('Form submission finished');
        },
    });
};
```

### 2. Error Message Display
**Added to template:**
```vue
<!-- Error Message Alert -->
<div v-if="form.hasErrors || form.recentlySuccessful === false" 
     class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
    <strong class="font-bold">Error!</strong>
    <span class="block sm:inline">
        <span v-if="form.errors.error">{{ form.errors.error }}</span>
        <span v-else>Terjadi kesalahan. Silakan periksa form Anda.</span>
    </span>
</div>
```

## Quick Fixes to Try

### Fix 1: Clear Cache & Restart Server
```bash
# Clear all caches
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Restart Laravel server
# Ctrl+C to stop, then:
php artisan serve
```

### Fix 2: Check Session Configuration
**File:** `config/session.php`

Ensure session driver is set correctly:
```php
'driver' => env('SESSION_DRIVER', 'file'),
'lifetime' => 120, // 120 minutes
'expire_on_close' => false,
```

### Fix 3: Verify CSRF Token in Blade
**File:** `resources/views/app.blade.php`

Check meta tag exists:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```
✅ Already present!

### Fix 4: Check Session Directory Permissions
```bash
# Make sure storage/framework/sessions is writable
chmod -R 775 storage/framework/sessions
# or on Windows:
# Right-click storage/framework/sessions → Properties → Security → Edit
```

### Fix 5: Increase Session Lifetime
**File:** `.env`

```env
SESSION_LIFETIME=120  # Increase to 240 or more
SESSION_DRIVER=file
```

Then:
```bash
php artisan config:cache
```

## Testing Steps

### Step 1: Clear Browser Data
1. Open Developer Tools (F12)
2. Application tab → Clear storage
3. Or use Incognito/Private window

### Step 2: Fresh Login
1. Go to `http://localhost:8000/login`
2. Login as KSO user
3. Immediately test create page

### Step 3: Test Form
1. Navigate to `/kso/permintaan/17/create`
2. Check console for errors (F12 → Console)
3. Fill form quickly (before session expires)
4. Submit

### Step 4: Monitor Network Tab
1. F12 → Network tab
2. Submit form
3. Check response:
   - Status 200 = Success
   - Status 419 = CSRF token issue
   - Status 302 = Redirect (check location)

## Debug Commands

### Check Session is Working
```bash
php artisan tinker
> session()->put('test', 'value')
> session()->get('test')
# Should return 'value'
```

### Check CSRF Token
```bash
php artisan tinker
> csrf_token()
# Should return a long string
```

### Verify Session Files
```bash
# On Linux/Mac
ls -la storage/framework/sessions/

# On Windows
dir storage\framework\sessions\
```

## Common Solutions

### Solution 1: SameSite Cookie Issue
**File:** `config/session.php`

```php
'same_site' => 'lax', // Try changing from 'strict' to 'lax'
```

### Solution 2: Domain Configuration
**File:** `config/session.php`

```php
'domain' => env('SESSION_DOMAIN', null), // Should be null for localhost
```

### Solution 3: Secure Cookie
**File:** `config/session.php`

```php
'secure' => env('SESSION_SECURE_COOKIE', false), // Should be false for localhost
```

### Solution 4: Add Exception to CSRF
**File:** `app/Http/Middleware/VerifyCsrfToken.php`

```php
protected $except = [
    // Add if absolutely necessary (NOT RECOMMENDED)
    // 'kso/permintaan/*/store',
];
```

## Laravel Configuration Check

### 1. Check .env File
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

### 2. Verify Storage Permissions
```bash
# Ensure these directories are writable
storage/framework/cache/
storage/framework/sessions/
storage/framework/views/
storage/logs/
```

### 3. Check APP_KEY
```bash
# If APP_KEY is missing
php artisan key:generate
```

## Inertia-Specific Fixes

### Fix 1: Inertia Version Check
```bash
# Check package.json
"@inertiajs/vue3": "^1.0.0",  # Should be latest

# Update if needed
npm update @inertiajs/vue3
# or
yarn upgrade @inertiajs/vue3
```

### Fix 2: Rebuild Assets
```bash
yarn build
# or for development
yarn dev
```

## Temporary Workaround

If error persists, user can:

1. **Open in new tab:** Right-click "Buat KSO" → Open in new tab
2. **Refresh before submit:** Load page, F5 to refresh, then fill form
3. **Quick submit:** Fill and submit within 2 minutes
4. **Use Incognito:** Test in private/incognito window

## Build Output
```bash
✓ 1474 modules transformed
✓ built in 4.27s

Updated:
- Create-v_nS5dMn.js (10.14 kB) ← Enhanced error handling
```

## Verification

### Check if Fix Works:
1. ✅ Clear browser cache
2. ✅ Clear Laravel cache: `php artisan optimize:clear`
3. ✅ Restart server: `php artisan serve`
4. ✅ Login fresh
5. ✅ Navigate to `/kso/permintaan/17/create`
6. ✅ Should load without 419 error

### If Still Getting 419:
- Check Laravel log: `storage/logs/laravel.log`
- Check browser console: F12 → Console
- Check session files exist: `storage/framework/sessions/`
- Try different browser
- Clear ALL cookies for localhost

## Status
✅ **ENHANCED** - Better error handling added
✅ **BUILT** - Assets compiled with fixes
⚠️ **REQUIRES** - Clear cache and restart server
🔄 **TESTING** - User should test after cache clear

## Next Steps
1. Clear all caches: `php artisan optimize:clear`
2. Restart Laravel server
3. Clear browser cache / use incognito
4. Test again: `/kso/permintaan/17/create`
5. If still fails, check `storage/logs/laravel.log`
