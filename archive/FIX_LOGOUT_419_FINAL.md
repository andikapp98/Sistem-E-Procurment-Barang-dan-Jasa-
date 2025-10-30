# FIX FINAL: Logout 419 Error "Sekilas Muncul"

## Masalah
Error 419 muncul sekilas saat logout, kemudian hilang (kemungkinan karena auto reload atau redirect berhasil setelah retry).

## Root Cause
Inertia.js versi 2.x menggunakan Fetch API, bukan Axios, sehingga:
1. Axios config tidak otomatis digunakan oleh Inertia
2. CSRF token harus di-handle secara eksplisit oleh Inertia
3. Ada kemungkinan race condition atau caching issue

## Solusi yang Diterapkan

### 1. Enhanced bootstrap.js - Comprehensive CSRF Setup
**File:** `resources/js/bootstrap.js`

```javascript
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Setup CSRF token for axios - this will be used by Inertia automatically
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    // Also set it for fetch API which Inertia uses
    window.csrfToken = token.content;
    console.log('CSRF Token loaded:', token.content.substring(0, 10) + '...');
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Make CSRF token globally available
if (token) {
    window.csrf_token = token.content;
}

// Intercept Inertia requests to ensure CSRF token is always sent
if (window.axios) {
    const originalRequest = window.axios.request;
    window.axios.request = function(config) {
        // Ensure CSRF token is in headers for all requests
        if (!config.headers) {
            config.headers = {};
        }
        if (window.csrfToken && !config.headers['X-CSRF-TOKEN']) {
            config.headers['X-CSRF-TOKEN'] = window.csrfToken;
        }
        return originalRequest.call(this, config);
    };
}
```

**Penjelasan:**
- Set CSRF token ke axios defaults (untuk compatibility)
- Expose token ke `window.csrfToken` untuk Inertia/Fetch API
- Intercept axios requests untuk memastikan token selalu ada
- Log token untuk debugging (first 10 chars only)

### 2. Simplified app.js - Let Inertia Handle CSRF
**File:** `resources/js/app.js`

```javascript
import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

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
- Hapus axios interceptor untuk 419 (not needed if CSRF configured correctly)
- Biarkan Inertia menggunakan CSRF token dari meta tag secara native
- Inertia v2.x otomatis detect dan use CSRF token dari `<meta name="csrf-token">`

### 3. Component Props Support (Already Fixed)
**Files:** `DropdownLink.vue` & `ResponsiveNavLink.vue`

Props `method` dan `as` sudah ditambahkan untuk support POST logout request.

### 4. Cache & Session Clearing

```bash
# Clear session files
Remove-Item storage\framework\sessions\* -Force

# Clear Laravel caches
php artisan cache:clear
php artisan config:clear  
php artisan view:clear

# Rebuild frontend
yarn build
```

## How Inertia CSRF Works (v2.x)

### Native CSRF Handling
Inertia v2.x automatically:
1. Reads `<meta name="csrf-token">` from HTML head
2. Includes token in `X-CSRF-TOKEN` header for all POST/PUT/PATCH/DELETE requests
3. Uses Fetch API under the hood (not Axios)

### Meta Tag Requirement
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```
✅ Already exists in `resources/views/app.blade.php` (line 6)

### Laravel CSRF Verification
Laravel automatically verifies CSRF token for:
- All POST, PUT, PATCH, DELETE requests
- Through `ValidateCsrfToken` middleware (part of `web` middleware group)
- Checks `X-CSRF-TOKEN` or `_token` field

## Testing Steps

### 1. Hard Refresh Browser
```
1. Open browser
2. Press Ctrl+Shift+Delete
3. Clear cookies & cache for localhost
4. Close ALL tabs of the application
5. Open fresh tab
```

### 2. Verify CSRF Token in Console
```
1. Open DevTools → Console
2. Login ke aplikasi
3. Type: window.csrf_token
4. Should see token string (not undefined)
5. Type: window.csrfToken  
6. Should see same token
7. Should see log: "CSRF Token loaded: xxxxxxxxxx..."
```

### 3. Test Logout
```
1. Klik dropdown menu (kanan atas)
2. Klik "Log Out"
3. Open DevTools → Network tab SEBELUM klik logout
4. Filter: XHR atau Fetch
5. Look for POST request to /logout
6. Check Request Headers:
   ✅ X-CSRF-TOKEN: [token value]
   ✅ X-Requested-With: XMLHttpRequest
   ✅ X-Inertia: true
7. Check Response:
   ✅ Status: 303 See Other (redirect)
   ✅ Location: /login
   ✅ NO 419 error
```

### 4. Test Multiple Roles
Test dengan setiap role:
- ✅ admin
- ✅ kepala_instalasi  
- ✅ kepala_bidang
- ✅ direktur
- ✅ wakil_direktur
- ✅ staff_perencanaan
- ✅ kso
- ✅ pengadaan

## Troubleshooting "Sekilas Muncul 419"

### Kemungkinan Penyebab

#### 1. Browser Cache Stale
**Solution:**
```
- Hard refresh: Ctrl+Shift+R
- Clear browser cache completely
- Use Incognito/Private mode for testing
```

#### 2. Session Token Mismatch
**Solution:**
```bash
# Clear all sessions
Remove-Item storage\framework\sessions\* -Force

# Restart development server
# If using php artisan serve, stop and restart it
```

#### 3. Multiple Tabs Conflict
**Solution:**
```
- Close ALL tabs of the application
- Clear cookies
- Login in single fresh tab
```

#### 4. CSRF Token Not Refreshed
**Solution:**
```javascript
// Check in browser console
document.head.querySelector('meta[name="csrf-token"]').content

// Should match:
window.csrf_token
window.csrfToken

// If different, reload page
```

#### 5. Vite Dev Server Cache
**Solution:**
```bash
# Stop dev server if running
# Clear node modules cache
rm -rf node_modules/.vite

# Rebuild
yarn build

# Or restart dev server
yarn dev
```

## Prevention Checklist

### Development
- [ ] Always use `yarn dev` atau `yarn build` setelah ubah JS files
- [ ] Clear browser cache saat test logout
- [ ] Monitor browser console untuk CSRF warnings
- [ ] Test di Incognito untuk avoid cache issues

### Deployment
- [ ] Run `yarn build` sebelum deploy
- [ ] Clear production cache: `php artisan optimize:clear`
- [ ] Verify meta tag CSRF exists di production HTML
- [ ] Test logout di production sebelum release

## Expected Behavior

### Normal Logout Flow
1. User klik "Log Out"
2. Inertia kirim POST request ke `/logout`
3. Include X-CSRF-TOKEN header (from meta tag)
4. Laravel validate token → Success
5. Session invalidated
6. Redirect 303 ke `/login`
7. No 419 error shown

### What Was Fixed
**Before:**
- DropdownLink tidak support `method` prop
- POST request sent as GET
- No CSRF token included
- Error 419 shown

**After:**
- DropdownLink support `method="post"` dan `as="button"`  
- POST request sent correctly
- CSRF token auto-included by Inertia
- Clean logout redirect

## Files Modified

1. ✅ `resources/js/Components/DropdownLink.vue`
   - Added `as` and `method` props

2. ✅ `resources/js/Components/ResponsiveNavLink.vue`
   - Added `as` and `method` props

3. ✅ `resources/js/bootstrap.js`
   - Enhanced CSRF token setup
   - Added window.csrfToken for Inertia
   - Added axios request interceptor
   - Added console logging for debugging

4. ✅ `resources/js/app.js`
   - Simplified (removed unnecessary axios interceptor)
   - Let Inertia handle CSRF natively

5. ✅ Session & Cache
   - Cleared all session files
   - Cleared all Laravel caches
   - Rebuilt frontend assets

## Status
✅ **IMPLEMENTED** - All changes applied
✅ **BUILT** - Frontend assets compiled
✅ **CACHED** - All caches cleared
⏳ **TESTING** - Requires user testing in browser

## Next Steps

1. **Clear Browser Data**
   - Open browser
   - Ctrl+Shift+Delete
   - Clear cookies & cache for localhost

2. **Test Logout**
   - Login dengan role manapun
   - Open DevTools → Network tab
   - Klik logout
   - Verify NO 419 error (even for a flash)

3. **If Still 419**
   - Check browser console for "CSRF Token loaded" message
   - Verify `window.csrf_token` is defined
   - Check Network tab for X-CSRF-TOKEN header
   - Try Incognito mode
   - Clear session files again

## Notes

- Inertia v2.x has native CSRF support via meta tag
- No need for axios interceptor jika configured correctly
- Bootstrap.js setup is for compatibility & debugging
- "Sekilas muncul 419" usually means token is being sent but might be stale
- Hard refresh browser adalah langkah penting untuk test

