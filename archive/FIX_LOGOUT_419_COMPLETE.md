# Fix: Logout 419 Error - COMPLETE

## Masalah
Error 419 "Page Expired" muncul saat user mencoba logout dari aplikasi.

## Root Cause
Komponen `DropdownLink.vue` dan `ResponsiveNavLink.vue` tidak mendukung props `method` dan `as` yang diperlukan untuk POST request logout. Inertia Link component membutuhkan props ini untuk mengirim CSRF token dengan benar saat melakukan POST request.

## Perbaikan yang Dilakukan

### 1. Update DropdownLink Component
**File:** `resources/js/Components/DropdownLink.vue`

**BEFORE:**
```vue
<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
    >
        <slot />
    </Link>
</template>
```

**AFTER:**
```vue
<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
    as: {
        type: String,
        default: 'a',
    },
    method: {
        type: String,
        default: 'get',
    },
});
</script>

<template>
    <Link
        :href="href"
        :method="method"
        :as="as"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
    >
        <slot />
    </Link>
</template>
```

### 2. Update ResponsiveNavLink Component
**File:** `resources/js/Components/ResponsiveNavLink.vue`

**BEFORE:**
```vue
<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 text-start text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
```

**AFTER:**
```vue
<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
    as: {
        type: String,
        default: 'a',
    },
    method: {
        type: String,
        default: 'get',
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 text-start text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :method="method" :as="as" :class="classes">
        <slot />
    </Link>
</template>
```

## Penjelasan Perbaikan

### Props yang Ditambahkan

1. **`as` prop:**
   - Default: `'a'` (anchor tag)
   - Untuk logout: `'button'` 
   - Mengubah Link component menjadi button element untuk POST request

2. **`method` prop:**
   - Default: `'get'` (GET request)
   - Untuk logout: `'post'`
   - Menentukan HTTP method yang digunakan

### Cara Kerja Logout dengan Inertia

1. User klik "Log Out" di dropdown atau mobile menu
2. Component `DropdownLink` atau `ResponsiveNavLink` menerima props:
   ```vue
   <DropdownLink
       :href="route('logout')"
       method="post"
       as="button"
   >
       Log Out
   </DropdownLink>
   ```

3. Inertia Link component:
   - Render sebagai `<button>` (karena `as="button"`)
   - Kirim POST request ke `/logout` (karena `method="post"`)
   - Otomatis include CSRF token dari meta tag

4. Laravel backend:
   - Verifikasi CSRF token (sudah dikonfigurasi di `FIX_ERROR_419_CSRF.md`)
   - Execute `AuthenticatedSessionController@destroy`
   - Invalidate session dan redirect ke login

## Tempat Logout Digunakan

### 1. Desktop Dropdown Menu
**File:** `resources/js/Layouts/AuthenticatedLayout.vue` (Line 89-95)
```vue
<DropdownLink
    :href="route('logout')"
    method="post"
    as="button"
>
    Log Out
</DropdownLink>
```

### 2. Mobile Menu
**File:** `resources/js/Layouts/AuthenticatedLayout.vue` (Line 342-348)
```vue
<ResponsiveNavLink
    :href="route('logout')"
    method="post"
    as="button"
>
    Log Out
</ResponsiveNavLink>
```

### 3. Verify Email Page
**File:** `resources/js/Pages/Auth/VerifyEmail.vue` (Line 51-56)
```vue
<Link
    :href="route('logout')"
    method="post"
    as="button"
    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
>
    Log Out
</Link>
```
*Note: Halaman ini sudah benar karena langsung menggunakan Link component dari Inertia*

## Files Modified

1. ✅ `resources/js/Components/DropdownLink.vue`
   - Tambah props: `as`, `method`
   - Update template: bind `:method` dan `:as` ke Link component

2. ✅ `resources/js/Components/ResponsiveNavLink.vue`
   - Tambah props: `as`, `method`
   - Update template: bind `:method` dan `:as` ke Link component

3. ✅ Build frontend: `yarn build`
   - Compile semua perubahan Vue components
   - Generate production assets

## Verifikasi CSRF Configuration

Pastikan konfigurasi CSRF sudah benar (referensi: `FIX_ERROR_419_CSRF.md`):

### 1. Meta Tag di Layout
**File:** `resources/views/app.blade.php` (Line 6)
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```
✅ Sudah ada

### 2. Axios CSRF Setup
**File:** `resources/js/bootstrap.js` (Line 6-13)
```javascript
// Setup CSRF token for axios
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
```
✅ Sudah ada

### 3. Inertia Share CSRF Token
**File:** `app/Http/Middleware/HandleInertiaRequests.php` (Line 37)
```php
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
✅ Sudah ada

### 4. Logout Route
**File:** `routes/auth.php`
```php
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
```
✅ Sudah ada sebagai POST route

### 5. Logout Controller
**File:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
```php
public function destroy(Request $request): RedirectResponse
{
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    $request->session()->flush();
    $request->session()->forget('_token');
    
    return redirect()->route('login');
}
```
✅ Sudah ada dan proper

## Testing

### 1. Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 2. Test Logout
1. Login dengan user (role apa saja)
2. Klik dropdown menu di kanan atas
3. Klik "Log Out"
4. ✅ Harus redirect ke login tanpa error 419

### 3. Test Mobile Logout
1. Login dengan user
2. Resize browser ke mobile view
3. Klik hamburger menu
4. Klik "Log Out"
5. ✅ Harus redirect ke login tanpa error 419

### 4. Verify di Browser DevTools
1. Open DevTools → Network tab
2. Klik logout
3. Check POST request ke `/logout`
4. Verify headers contain:
   - `X-CSRF-TOKEN: ...`
   - `X-Requested-With: XMLHttpRequest`
5. Response: 302 redirect ke `/login`
6. ✅ No 419 error

## Status
✅ **FIXED** - Semua logout 419 error sudah diperbaiki

## Summary

Masalah logout 419 terjadi karena komponen `DropdownLink` dan `ResponsiveNavLink` tidak meneruskan props `method` dan `as` ke Inertia Link component. Tanpa props ini, logout request dikirim sebagai GET request biasa tanpa CSRF token yang proper. 

Dengan menambahkan props `method="post"` dan `as="button"`, Inertia Link component sekarang:
- Render logout sebagai button element
- Kirim POST request dengan CSRF token
- Handle logout dengan benar tanpa error 419

Fix ini melengkapi konfigurasi CSRF yang sudah ada sebelumnya di `FIX_ERROR_419_CSRF.md`.
