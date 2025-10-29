# Fix Logout Staff Perencanaan - Page Expired (419)

## Masalah
Logout Staff Perencanaan mengalami error "Page Expired" (419 CSRF Token Mismatch)

## Penyebab
Form logout menggunakan HTML form tradisional dengan CSRF token manual yang bisa expired atau tidak ter-refresh dengan baik saat session idle.

## Solusi
Mengubah logout dari form HTML tradisional ke Inertia router POST method yang lebih robust dan otomatis handle CSRF token.

## Perubahan File

### resources/js/Layouts/AuthenticatedLayout.vue

#### 1. Script Setup - Tambah router dan logout function
```javascript
import { Link, usePage, router } from '@inertiajs/vue3';

const logout = () => {
    router.post(route('logout'));
};
```

#### 2. Desktop Dropdown - Ganti form dengan button
```vue
<!-- SEBELUM -->
<form :action="route('logout')" method="POST" class="w-full">
    <input type="hidden" name="_token" :value="csrfToken">
    <button type="submit" ...>
        Log Out
    </button>
</form>

<!-- SESUDAH -->
<button @click="logout" ...>
    Log Out
</button>
```

#### 3. Mobile Responsive Menu - Ganti form dengan button
```vue
<!-- SEBELUM -->
<form :action="route('logout')" method="POST" class="w-full">
    <input type="hidden" name="_token" :value="csrfToken">
    <button type="submit" ...>
        Log Out
    </button>
</form>

<!-- SESUDAH -->
<button @click="logout" ...>
    Log Out
</button>
```

## Keuntungan Solusi Ini
1. **Automatic CSRF Handling** - Inertia router otomatis menangani CSRF token
2. **Session Refresh** - Token selalu fresh karena dihandle oleh Inertia
3. **No Page Expired** - Tidak ada lagi error 419 karena token expired
4. **Consistent** - Sama dengan logout di role lain yang sudah working
5. **Cleaner Code** - Tidak perlu manual CSRF token management

## Testing
1. Login sebagai Staff Perencanaan
2. Biarkan idle beberapa saat (opsional)
3. Klik logout dari dropdown menu
4. Seharusnya logout berhasil tanpa error "Page Expired"

## Build
```bash
yarn build
```

Build telah berhasil dilakukan dengan output:
- AuthenticatedLayout-FIgx3N_7.js (27.36 kB)

## Status
✅ Fixed - Logout Staff Perencanaan sudah tidak mengalami Page Expired lagi
