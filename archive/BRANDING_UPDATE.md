# Branding Update - E-Procurement RSUD Ibnu Sina Gresik

## Status: ✅ COMPLETED

## Changes Made

### 1. **Application Name**
Updated application name from "Laravel" to **"E-Procurement RSUD Ibnu Sina Gresik"**

#### Files Modified:
- **.env** - `APP_NAME="E-Procurement RSUD Ibnu Sina Gresik"`
- **config/app.php** - Default name updated
- **resources/views/app.blade.php** - Title tag updated

### 2. **Logout Redirect**
Changed logout behavior to redirect to login page using named route

#### File Modified:
**app/Http/Controllers/Auth/AuthenticatedSessionController.php**

**Before:**
```php
return redirect('/login');
```

**After:**
```php
return redirect()->route('login');
```

**Benefits:**
- ✅ Uses named route (more Laravel-like)
- ✅ Consistent with other redirects
- ✅ Easier to maintain if route changes

### 3. **Favicon/Icon Setup**
Added favicon configuration in main layout

#### File Modified:
**resources/views/app.blade.php**

**Added:**
```html
<!-- Favicon -->
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">
```

**Next Steps for Favicon:**
1. Create or place `favicon.png` in `public/` folder
2. Recommended size: 64x64 or 32x32 pixels
3. Use hospital logo or "EP" initials with #028174 background color

**Temporary Favicon Generator:**
A simple HTML file can be created to generate basic favicon:
- Background: #028174 (app primary color)
- Text: "EP" in white
- Size: 64x64 px

## Browser Tab Display

### Before:
```
Laravel - Dashboard
```

### After:
```
Dashboard - E-Procurement RSUD Ibnu Sina Gresik
```

## Environment Variables

**.env file:**
```env
APP_NAME="E-Procurement RSUD Ibnu Sina Gresik"
VITE_APP_NAME="${APP_NAME}"
```

This ensures:
- Laravel uses the correct name
- Vite build uses the correct name
- Inertia pages show correct title

## Title Format

**Dynamic Title Pattern:**
```
[Page Name] - E-Procurement RSUD Ibnu Sina Gresik
```

**Examples:**
- `Dashboard - E-Procurement RSUD Ibnu Sina Gresik`
- `Permintaan - E-Procurement RSUD Ibnu Sina Gresik`
- `Login - E-Procurement RSUD Ibnu Sina Gresik`

## Favicon Instructions

### Option 1: Use Existing Hospital Logo
1. Get hospital logo in PNG format
2. Resize to 64x64 or 32x32 pixels
3. Save as `public/favicon.png`

### Option 2: Create Simple Icon
1. Background color: #028174 (teal - app primary color)
2. Text: "EP" or hospital initials
3. Font: White, bold, centered
4. Save as `public/favicon.png`

### Option 3: Online Generator
Use free favicon generators:
- https://favicon.io/
- https://realfavicongenerator.net/

**Recommended:**
- Upload hospital logo
- Or use text "RSUD IS" or "EP"
- Download and place in `public/` folder

## Testing

### 1. Check Application Name
```bash
php artisan config:clear
php artisan cache:clear
```

Refresh browser and check:
- Browser tab title
- Page titles across all pages

### 2. Check Logout Redirect
1. Login to application
2. Click Logout
3. Verify redirect to `/login` page
4. Check no errors in console

### 3. Check Favicon
1. Place favicon.png in public folder
2. Hard refresh browser (Ctrl+F5)
3. Check browser tab icon
4. Check bookmark icon

## Files Summary

### Modified Files:
1. `.env` - APP_NAME updated
2. `config/app.php` - Default app name
3. `resources/views/app.blade.php` - Title and favicon
4. `app/Http/Controllers/Auth/AuthenticatedSessionController.php` - Logout redirect

### No Changes Needed:
- `resources/js/app.js` - Already uses VITE_APP_NAME
- Logo in navbar - Already shows "Sistem e-Procurement"

## Cache Clear Commands

After making changes, run:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
```

## Browser Refresh

After cache clear:
1. Hard refresh browser: `Ctrl + Shift + R` or `Ctrl + F5`
2. Clear browser cache
3. Close and reopen browser tab

## Verification Checklist

- [x] .env APP_NAME updated
- [x] config/app.php default name updated
- [x] app.blade.php title updated
- [x] app.blade.php favicon links added
- [x] Logout redirect uses named route
- [ ] favicon.png placed in public folder (manual step)

## Notes

**Favicon Placeholder:**
Until a proper favicon is created and placed in `public/favicon.png`, browsers will show default icon. This is normal and doesn't affect functionality.

**Title Format:**
The format `[Page] - [App Name]` is standard web practice and helps users identify:
- Which page they're on
- Which application it is
- Useful when multiple tabs are open

**Date:** 2025-10-21  
**Status:** Completed  
**Remaining:** Add actual favicon.png file to public folder
