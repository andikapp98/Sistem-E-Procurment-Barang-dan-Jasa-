# Fix: Tombol Edit/Delete untuk Permintaan Ditolak (Admin)

## Masalah
Sebelumnya, admin bisa melihat tombol "Edit & Ajukan Ulang" untuk permintaan dengan status `ditolak`, padahal seharusnya hanya ada tombol "Hapus" saja.

## Perbaikan yang Dilakukan

### 1. Halaman Detail (Show.vue)
**File**: `resources/js/Pages/Permintaan/Show.vue`

#### Perubahan:
- **Status Revisi** → Tombol: "Edit & Ajukan Ulang"
- **Status Ditolak** → Tombol: "Hapus" saja (tidak ada edit)

```vue
<!-- Edit Button - Hanya untuk status Revisi -->
<Link
    v-if="permintaan.status === 'revisi'"
    :href="route('permintaan.edit', permintaan.permintaan_id)"
    ...
>
    Edit & Ajukan Ulang
</Link>

<!-- Delete Button - Hanya untuk status Ditolak -->
<button
    v-if="permintaan.status === 'ditolak'"
    @click="confirmDelete"
    ...
>
    Hapus
</button>
```

#### Teks Informasi:
- **Status Revisi**: "Anda dapat mengedit permintaan ini untuk memperbaikinya dan mengajukan kembali."
- **Status Ditolak**: "Permintaan ini telah ditolak. Anda hanya dapat menghapus permintaan ini."

### 2. Halaman Daftar (Index.vue)
**File**: `resources/js/Pages\Permintaan\Index.vue`

#### Perbaikan Fungsi:
```javascript
const canEdit = (item) => {
    if (!userLogin) return false;
    // Hanya bisa edit jika status revisi (bukan ditolak)
    return isAdmin.value && item.status && item.status.toLowerCase() === 'revisi';
};

const canDelete = (item) => {
    if (!userLogin) return false;
    // Hanya bisa delete jika status ditolak
    return isAdmin.value && item.status && item.status.toLowerCase() === 'ditolak';
};
```

#### Tambahan Status Class:
```javascript
case "revisi":
    return "bg-orange-100 text-orange-800 border border-orange-300"; // Status revisi (orange)
case "ditolak":
    return "bg-red-100 text-red-800 border border-red-300"; // Status ditolak (merah)
```

## Behavior Setelah Perbaikan

### Status: REVISI (perlu perbaikan, bisa diajukan ulang)
✅ Tombol "Edit & Ajukan Ulang" muncul  
❌ Tombol "Hapus" tidak muncul  
🟠 Badge orange untuk status  

### Status: DITOLAK (tidak bisa diajukan ulang)
❌ Tombol "Edit & Ajukan Ulang" tidak muncul  
✅ Tombol "Hapus" muncul  
🔴 Badge merah untuk status  

## Testing

### Test Case 1: Permintaan dengan Status Revisi
1. Buka detail permintaan dengan status `revisi`
2. ✅ Harus ada tombol "Edit & Ajukan Ulang"
3. ❌ Tidak ada tombol "Hapus"

### Test Case 2: Permintaan dengan Status Ditolak
1. Buka detail permintaan dengan status `ditolak`
2. ❌ Tidak ada tombol "Edit & Ajukan Ulang"
3. ✅ Harus ada tombol "Hapus"

### Test Case 3: Daftar Permintaan (Index)
1. Lihat daftar permintaan
2. Permintaan dengan status `revisi` → icon edit hijau muncul
3. Permintaan dengan status `ditolak` → icon delete merah muncul

## Perbedaan Status

| Status | Arti | Warna Badge | Tombol yang Muncul |
|--------|------|-------------|-------------------|
| **revisi** | Perlu perbaikan, bisa diajukan ulang setelah diedit | 🟠 Orange | Edit & Ajukan Ulang |
| **ditolak** | Ditolak permanen, tidak bisa diajukan ulang | 🔴 Merah | Hapus |

## Catatan
- Hanya admin yang bisa mengedit atau menghapus permintaan
- Status `revisi` memberikan kesempatan kedua untuk memperbaiki permintaan
- Status `ditolak` bersifat final, hanya bisa dihapus dari sistem
- Perubahan ini memastikan workflow yang jelas antara revisi dan penolakan
