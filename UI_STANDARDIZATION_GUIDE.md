# 🎨 UI STANDARDIZATION - CONSISTENT APPROVE/REJECT/REVISI COMPONENTS

## ✅ Created Date: 2 November 2025

Komponen UI yang konsisten untuk semua role yang memiliki fungsi approve, reject, dan revisi permintaan.

---

## 📦 KOMPONEN YANG DIBUAT

### 1. **PermintaanActionButtons.vue**
Komponen untuk menampilkan 3 tombol aksi (Approve, Reject, Revisi)

**Location:** `resources/js/Components/PermintaanActionButtons.vue`

**Features:**
- ✅ 3 button dengan warna konsisten (Hijau, Orange, Merah)
- ✅ Icon SVG untuk setiap button
- ✅ Hover effects & transitions
- ✅ Responsive layout
- ✅ Event emitters

**Props:**
```javascript
{
    canTakeAction: Boolean // true = show buttons, false = hide
}
```

**Events:**
```javascript
@show-approve  // Emit saat tombol "Setujui" diklik
@show-revisi   // Emit saat tombol "Minta Revisi" diklik
@show-reject   // Emit saat tombol "Tolak" diklik
```

**Usage:**
```vue
<PermintaanActionButtons
    :can-take-action="permintaan.status === 'diajukan'"
    @show-approve="showApproveModal = true"
    @show-revisi="showRevisiModal = true"
    @show-reject="showRejectModal = true"
/>
```

---

### 2. **ApproveModal.vue**
Modal untuk approve permintaan dengan informasi routing

**Location:** `resources/js/Components/ApproveModal.vue`

**Features:**
- ✅ Modal dengan backdrop
- ✅ Info klasifikasi & routing (optional)
- ✅ Field catatan optional
- ✅ Close button (X)
- ✅ Click outside to close
- ✅ Responsive design

**Props:**
```javascript
{
    show: Boolean,              // true = show modal
    klasifikasi: String,        // 'medis', 'penunjang_medis', 'non_medis'
    kabidTujuan: String,        // Nama Kabid tujuan
    showCatatanField: Boolean   // true = show catatan textarea
}
```

**Events:**
```javascript
@close     // Emit saat modal ditutup
@approve   // Emit saat tombol "Ya, Setujui" diklik (params: catatan)
```

**Usage:**
```vue
<ApproveModal
    :show="showApproveModal"
    :klasifikasi="klasifikasi"
    :kabid-tujuan="kabidTujuan"
    :show-catatan-field="false"
    @close="showApproveModal = false"
    @approve="handleApprove"
/>
```

---

### 3. **RejectModal.vue**
Modal untuk reject permintaan dengan alasan penolakan

**Location:** `resources/js/Components/RejectModal.vue`

**Features:**
- ✅ Modal dengan backdrop
- ✅ Warning box (red alert)
- ✅ Textarea untuk alasan penolakan
- ✅ Real-time validation (min 5 characters)
- ✅ Character counter
- ✅ Button disabled jika invalid
- ✅ Error messages support

**Props:**
```javascript
{
    show: Boolean,    // true = show modal
    errors: Object    // Laravel validation errors
}
```

**Events:**
```javascript
@close    // Emit saat modal ditutup
@reject   // Emit saat tombol "Tolak Permintaan" diklik (params: alasan)
```

**Usage:**
```vue
<RejectModal
    :show="showRejectModal"
    :errors="$page.props.errors"
    @close="showRejectModal = false"
    @reject="handleReject"
/>
```

---

### 4. **RevisiModal.vue**
Modal untuk request revisi dengan catatan

**Location:** `resources/js/Components/RevisiModal.vue`

**Features:**
- ✅ Modal dengan backdrop
- ✅ Info box (orange alert)
- ✅ Textarea untuk catatan revisi
- ✅ Real-time validation (min 5 characters)
- ✅ Character counter
- ✅ Button disabled jika invalid
- ✅ Tips box
- ✅ Error messages support

**Props:**
```javascript
{
    show: Boolean,    // true = show modal
    errors: Object    // Laravel validation errors
}
```

**Events:**
```javascript
@close    // Emit saat modal ditutup
@revisi   // Emit saat tombol "Kirim Revisi" diklik (params: catatan_revisi)
```

**Usage:**
```vue
<RevisiModal
    :show="showRevisiModal"
    :errors="$page.props.errors"
    @close="showRevisiModal = false"
    @revisi="handleRevisi"
/>
```

---

## 🎯 COMPLETE IMPLEMENTATION EXAMPLE

### Example: Update KepalaInstalasi/Show.vue

```vue
<template>
    <AuthenticatedLayout>
        <!-- ... existing code ... -->

        <!-- Action Buttons - USING COMPONENT -->
        <PermintaanActionButtons
            :can-take-action="permintaan.status === 'diajukan'"
            @show-approve="showApproveModal = true"
            @show-revisi="showRevisiModal = true"
            @show-reject="showRejectModal = true"
        />

        <!-- Modals - USING COMPONENTS -->
        <ApproveModal
            :show="showApproveModal"
            :klasifikasi="klasifikasi"
            :kabid-tujuan="kabidTujuan"
            @close="showApproveModal = false"
            @approve="approve"
        />

        <RejectModal
            :show="showRejectModal"
            :errors="$page.props.errors"
            @close="showRejectModal = false"
            @reject="reject"
        />

        <RevisiModal
            :show="showRevisiModal"
            :errors="$page.props.errors"
            @close="showRevisiModal = false"
            @revisi="requestRevision"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PermintaanActionButtons from '@/Components/PermintaanActionButtons.vue';
import ApproveModal from '@/Components/ApproveModal.vue';
import RejectModal from '@/Components/RejectModal.vue';
import RevisiModal from '@/Components/RevisiModal.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    permintaan: Object,
    klasifikasi: String,
    kabidTujuan: String,
});

const showApproveModal = ref(false);
const showRejectModal = ref(false);
const showRevisiModal = ref(false);

// Approve handler
const approve = (catatan) => {
    const form = useForm({ catatan });
    form.post(route('kepala-instalasi.approve', props.permintaan.permintaan_id), {
        onSuccess: () => {
            showApproveModal.value = false;
        }
    });
};

// Reject handler
const reject = (alasan) => {
    const form = useForm({ alasan });
    form.post(route('kepala-instalasi.reject', props.permintaan.permintaan_id), {
        onSuccess: () => {
            showRejectModal.value = false;
        }
    });
};

// Revisi handler
const requestRevision = (catatan_revisi) => {
    const form = useForm({ catatan_revisi });
    form.post(route('kepala-instalasi.revisi', props.permintaan.permintaan_id), {
        onSuccess: () => {
            showRevisiModal.value = false;
        }
    });
};
</script>
```

---

## 🔄 MIGRATION GUIDE - Update Existing Pages

### Pages to Update:

1. ✅ **KepalaInstalasi/Show.vue** ← Already has approve/reject/revisi
2. ✅ **KepalaBidang/Show.vue** ← Already has approve/reject/revisi
3. ✅ **Direktur/Show.vue** ← Already has approve/reject/revisi
4. ✅ **WakilDirektur/Show.vue** ← Already has approve/reject/revisi
5. ✅ **KepalaPoli/Show.vue** ← May need to add
6. ✅ **KepalaRuang/Show.vue** ← May need to add

### Step-by-Step Migration:

#### Step 1: Import Components
```vue
<script setup>
import PermintaanActionButtons from '@/Components/PermintaanActionButtons.vue';
import ApproveModal from '@/Components/ApproveModal.vue';
import RejectModal from '@/Components/RejectModal.vue';
import RevisiModal from '@/Components/RevisiModal.vue';
</script>
```

#### Step 2: Replace Existing Action Buttons Section
**Remove:**
```vue
<!-- Old code -->
<div v-if="permintaan.status === 'diajukan'">
    <button @click="showApproveModal = true">Setujui</button>
    <button @click="showRevisiModal = true">Minta Revisi</button>
    <button @click="showRejectModal = true">Tolak</button>
</div>
```

**Replace with:**
```vue
<!-- New code -->
<PermintaanActionButtons
    :can-take-action="permintaan.status === 'diajukan'"
    @show-approve="showApproveModal = true"
    @show-revisi="showRevisiModal = true"
    @show-reject="showRejectModal = true"
/>
```

#### Step 3: Replace Existing Modals
**Remove:**
```vue
<!-- Old approve modal -->
<div v-if="showApproveModal">...</div>

<!-- Old reject modal -->
<div v-if="showRejectModal">...</div>

<!-- Old revisi modal -->
<div v-if="showRevisiModal">...</div>
```

**Replace with:**
```vue
<!-- New modals -->
<ApproveModal
    :show="showApproveModal"
    :klasifikasi="klasifikasi"
    :kabid-tujuan="kabidTujuan"
    @close="showApproveModal = false"
    @approve="approve"
/>

<RejectModal
    :show="showRejectModal"
    :errors="$page.props.errors"
    @close="showRejectModal = false"
    @reject="reject"
/>

<RevisiModal
    :show="showRevisiModal"
    :errors="$page.props.errors"
    @close="showRevisiModal = false"
    @revisi="requestRevision"
/>
```

#### Step 4: Update Event Handlers
```vue
<script setup>
import { useForm } from '@inertiajs/vue3';

// Approve
const approve = (catatan) => {
    const form = useForm({ catatan });
    form.post(route('YOUR-ROLE.approve', props.permintaan.permintaan_id), {
        onSuccess: () => showApproveModal.value = false
    });
};

// Reject
const reject = (alasan) => {
    const form = useForm({ alasan });
    form.post(route('YOUR-ROLE.reject', props.permintaan.permintaan_id), {
        onSuccess: () => showRejectModal.value = false
    });
};

// Revisi
const requestRevision = (catatan_revisi) => {
    const form = useForm({ catatan_revisi });
    form.post(route('YOUR-ROLE.revisi', props.permintaan.permintaan_id), {
        onSuccess: () => showRevisiModal.value = false
    });
};
</script>
```

---

## 🎨 UI DESIGN SPECIFICATIONS

### Color Palette:
```css
/* Approve/Success */
--color-approve-bg: #16a34a;      /* green-600 */
--color-approve-hover: #15803d;   /* green-700 */

/* Reject/Danger */
--color-reject-bg: #dc2626;       /* red-600 */
--color-reject-hover: #b91c1c;    /* red-700 */

/* Revisi/Warning */
--color-revisi-bg: #ea580c;       /* orange-600 */
--color-revisi-hover: #c2410c;    /* orange-700 */

/* Info Boxes */
--color-info-approve: #dbeafe;    /* blue-50 */
--color-info-reject: #fee2e2;     /* red-50 */
--color-info-revisi: #ffedd5;     /* orange-50 */
```

### Typography:
```css
/* Headings */
Modal Title: text-lg font-medium (18px, 500 weight)
Section Title: text-sm font-semibold (14px, 600 weight)

/* Body Text */
Normal Text: text-sm (14px)
Small Text: text-xs (12px)
Help Text: text-xs italic text-gray-500

/* Labels */
Field Label: text-sm font-medium text-gray-700
Required Mark: text-red-500
```

### Spacing:
```css
/* Padding */
Modal Outer: p-5
Modal Content: px-7 py-3
Button Group: gap-3
Sections: space-y-4

/* Margins */
Section Bottom: mb-4
Label Bottom: mb-2
Element Top: mt-1, mt-2
```

### Border Radius:
```css
Modal: rounded-md (6px)
Button: rounded-md (6px)
Input/Textarea: rounded-md (6px)
Info Box: rounded-lg (8px)
```

---

## 📊 COMPARISON: BEFORE vs AFTER

### BEFORE (Inconsistent):
```
❌ Different button styles per page
❌ Different modal layouts
❌ Inconsistent validation messages
❌ Different color schemes
❌ Duplicate code in each page
❌ Hard to maintain
❌ Different UX per role
```

### AFTER (Standardized):
```
✅ Consistent button styles across all pages
✅ Unified modal layouts
✅ Standard validation messages
✅ Consistent color scheme
✅ Reusable components (DRY principle)
✅ Easy to maintain
✅ Consistent UX for all roles
✅ Professional look & feel
```

---

## 🧪 TESTING CHECKLIST

### Visual Testing:
- [ ] Buttons display correctly with icons
- [ ] Button hover effects work
- [ ] Modals open/close smoothly
- [ ] Modal backdrop works
- [ ] Click outside closes modal
- [ ] Close (X) button works
- [ ] Responsive on mobile
- [ ] Colors match design spec

### Functional Testing:
- [ ] Approve modal shows klasifikasi info
- [ ] Reject validation works (min 5 chars)
- [ ] Revisi validation works (min 5 chars)
- [ ] Character counter updates real-time
- [ ] Buttons disable when invalid
- [ ] Form submission works
- [ ] Success messages show
- [ ] Error messages show
- [ ] Modal resets after submit

### Cross-Role Testing:
- [ ] Test in Kepala Instalasi page
- [ ] Test in Kepala Bidang page
- [ ] Test in Direktur page
- [ ] Test in Wakil Direktur page
- [ ] Test in Kepala Poli page (if applicable)
- [ ] Test in Kepala Ruang page (if applicable)

---

## 🚀 DEPLOYMENT CHECKLIST

### Before Deployment:
1. ✅ Create all 4 component files
2. ✅ Update all Show.vue pages to use components
3. ✅ Test each page manually
4. ✅ Check responsive design
5. ✅ Verify all routes work
6. ✅ Test validation messages

### After Deployment:
1. Clear cache: `php artisan cache:clear`
2. Rebuild assets: `npm run build`
3. Test in production
4. Monitor for errors
5. Gather user feedback

---

## 📝 MAINTENANCE NOTES

### To Update Button Style:
Edit: `PermintaanActionButtons.vue`
- All pages will automatically use new style

### To Update Modal Layout:
Edit respective modal component:
- `ApproveModal.vue`
- `RejectModal.vue`
- `RevisiModal.vue`

### To Add New Validation:
Edit modal component's `handleApprove/handleReject/handleRevisi` method

### To Change Colors:
Update Tailwind classes in component files

---

## 💡 BENEFITS

### For Users:
1. **Consistent Experience** - Same UI across all roles
2. **Intuitive** - Once learned, works everywhere
3. **Clear Visual Cues** - Colors indicate action type
4. **Better Validation** - Real-time feedback
5. **Professional** - Polished look & feel

### For Developers:
1. **DRY Principle** - No code duplication
2. **Easy Maintenance** - Update once, apply everywhere
3. **Type Safety** - Props validation
4. **Reusable** - Can be used in new pages
5. **Testable** - Components can be unit tested

### For Project:
1. **Brand Consistency** - Unified design language
2. **Scalability** - Easy to add new features
3. **Quality** - Professional standard
4. **Reduced Bugs** - Less duplicate code
5. **Faster Development** - Just import & use

---

## 🎓 BEST PRACTICES

### DO:
✅ Use components for all approve/reject/revisi functionality
✅ Keep event handlers in parent component
✅ Pass only necessary props
✅ Handle errors in parent component
✅ Maintain consistent naming conventions

### DON'T:
❌ Copy-paste component code to pages
❌ Modify component props directly
❌ Skip validation
❌ Ignore error messages
❌ Override component styles inline

---

## 📚 RELATED DOCUMENTATION

- `KEPALA_IRJA_APPROVE_REJECT_REVISI.md` - Workflow details
- `KEPALA_IRJA_APPROVE_WORKFLOW.md` - Routing logic
- Tailwind CSS Documentation - For styling reference
- Vue 3 Documentation - For component patterns

---

**Created By:** GitHub Copilot CLI  
**Date:** 2 November 2025  
**Status:** ✅ READY FOR IMPLEMENTATION  
**Version:** 1.0
