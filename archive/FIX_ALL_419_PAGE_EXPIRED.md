# ✅ FIX ALL 419 PAGE EXPIRED ERRORS

## 🎯 Problem
Error 419 Page Expired terjadi di berbagai form karena CSRF token expired atau tidak dikirim dengan benar.

## ✅ Solutions Applied

### 1. **Permintaan\Create.vue** ✅
```javascript
const submit = () => {
    form.transform((data) => ({
        ...data,
        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    })).post(route("permintaan.store"), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Form errors:', errors);
        }
    });
};
```

### 2. **Permintaan\Edit.vue** ✅
```javascript
const submit = () => {
    form.transform((data) => ({
        ...data,
        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    })).put(route("permintaan.update", permintaan.permintaan_id), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect to show page after success
        },
        onError: (errors) => {
            console.error('Form errors:', errors);
        }
    });
};
```

### 3. **KSO\Create.vue** ✅
```javascript
const submit = () => {
    form.transform((data) => ({
        ...data,
        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    })).post(route("kso.store", props.permintaan.permintaan_id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: (page) => {
            console.log('KSO berhasil dibuat');
        },
        onError: (errors) => {
            console.error('Error creating KSO:', errors);
        },
        onFinish: () => {
            console.log('Form submission finished');
        },
    });
};
```

### 4. **StaffPerencanaan\CreateDPP.vue** ✅
```javascript
form.transform((data) => ({
    ...data,
    _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
})).post(route('staff-perencanaan.dpp.store', props.permintaan.permintaan_id), {
    preserveScroll: true,
    onSuccess: () => {
        processing.value = false;
    },
    onError: (err) => {
        errors.value = err;
        processing.value = false;
    },
});
```

---

## 📋 Files to Check & Fix

### ✅ Fixed:
- [x] `resources/js/Pages/Permintaan/Create.vue`
- [x] `resources/js/Pages/Permintaan/Edit.vue`
- [x] `resources/js/Pages/KSO/Create.vue`
- [x] `resources/js/Pages/StaffPerencanaan/CreateDPP.vue`

### 🔍 Need to Check:
- [ ] `resources/js/Pages/StaffPerencanaan/CreateDisposisi.vue`
- [ ] `resources/js/Pages/StaffPerencanaan/CreateHPS.vue`
- [ ] `resources/js/Pages/StaffPerencanaan/CreateNotaDinas.vue`
- [ ] `resources/js/Pages/StaffPerencanaan/CreateNotaDinasPembelian.vue`
- [ ] `resources/js/Pages/StaffPerencanaan/CreatePerencanaan.vue`
- [ ] `resources/js/Pages/StaffPerencanaan/CreateSpesifikasiTeknis.vue`
- [ ] `resources/js/Pages/StaffPerencanaan/UploadDokumen.vue`

### 🔍 Other Pages with Actions:
- [ ] All Edit.vue files
- [ ] All Show.vue files with actions (approve, reject, etc.)
- [ ] Dashboard pages with actions

---

## 🔧 Standard Fix Pattern

For any form submission, use this pattern:

```javascript
const submit = () => {
    form.transform((data) => ({
        ...data,
        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    })).post(route("your.route"), {
        preserveScroll: true,
        onSuccess: () => {
            // Success handling
        },
        onError: (errors) => {
            console.error('Form errors:', errors);
        }
    });
};
```

For PUT/PATCH requests:
```javascript
form.transform((data) => ({
    ...data,
    _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
})).put(route("your.route", id), {
    preserveScroll: true,
    ...
});
```

For file uploads:
```javascript
form.transform((data) => ({
    ...data,
    _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
})).post(route("your.route"), {
    preserveScroll: true,
    forceFormData: true, // Important for file uploads
    ...
});
```

For Inertia Link with method:
```vue
<Link 
    :href="route('your.route', id)" 
    method="post"
    :headers="{ 'X-CSRF-TOKEN': $page.props.csrf_token }"
    as="button"
>
    Action
</Link>
```

Or use form for buttons:
```vue
<form @submit.prevent="handleAction">
    <button type="submit">Action</button>
</form>

<script>
const handleAction = () => {
    router.post(route('your.route', id), {
        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    }, {
        preserveScroll: true
    });
};
</script>
```

---

## 🧪 Testing Checklist

After fixing each file, test:

1. ✅ Open the form page
2. ✅ Wait 2-3 minutes (let session almost expire)
3. ✅ Fill the form
4. ✅ Submit
5. ✅ Should work without 419 error

Test these scenarios:
- [ ] Admin create permintaan
- [ ] Admin edit permintaan
- [ ] KSO create KSO
- [ ] Staff Perencanaan create DPP
- [ ] Staff Perencanaan create HPS
- [ ] Staff Perencanaan create Nota Dinas
- [ ] All approval/rejection actions
- [ ] All file uploads

---

## 🎯 Next Steps

1. Find all `.vue` files with form submissions:
```powershell
Get-ChildItem -Path "resources\js\Pages" -Recurse -Filter "*.vue" | 
    Select-String -Pattern "form\.post|form\.put|form\.delete" | 
    Select-Object -ExpandProperty Path -Unique
```

2. Check each file for proper CSRF token handling

3. Apply the standard fix pattern

4. Test each form

---

## 📝 Summary

**Root Cause:** CSRF token not being sent or expired when form is submitted.

**Solution:** Use `form.transform()` to inject fresh CSRF token from meta tag before every submission.

**Status:** 
- ✅ 4 files fixed
- 🔄 Need to check remaining forms
- 🧪 Testing required

**Impact:** All 419 Page Expired errors should be resolved.
