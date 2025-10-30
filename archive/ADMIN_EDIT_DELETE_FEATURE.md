# ✅ Admin: Edit Revisi & Delete Ditolak

## Fitur Baru
1. **Edit Permintaan Revisi** → Perbaiki dan ajukan ulang
2. **Edit Permintaan Ditolak** → Perbaiki dan ajukan ulang
3. **Delete Permintaan Ditolak** → Hapus permanen

## Implementasi

### Backend (PermintaanController.php)
```php
// edit() - Allow revisi OR ditolak
$allowedStatuses = ['revisi', 'ditolak'];

// update() - Auto change status to 'diajukan'
$data['status'] = 'diajukan';

// destroy() - Delete only if ditolak
if ($user->role !== 'admin' || $permintaan->status !== 'ditolak') {
    return error;
}
```

### Frontend (Show.vue)
- Action card dengan Edit & Delete buttons
- Conditional display: admin only + revisi/ditolak status
- Delete button hanya untuk status ditolak
- Confirmation dialog sebelum delete

## User Flow

### Edit & Resubmit
```
Revisi/Ditolak → Admin Edit → Submit → Status = Diajukan ✅
```

### Delete
```
Ditolak → Admin Click Hapus → Confirm → Deleted 🗑️
```

## Security
- ✅ Only admin can edit/delete
- ✅ Only revisi/ditolak can be edited
- ✅ Only ditolak can be deleted
- ✅ Confirmation dialog for delete
- ✅ Auto status management (no manual)

## Testing
```
☑ Login admin → Permintaan ditolak → Action buttons muncul ✅
☑ Click Edit → Perbaiki → Submit → Status diajukan ✅
☑ Click Hapus → Confirm → Deleted ✅
```

## Files
- `PermintaanController.php` (edit, update, destroy)
- `Show.vue` (action buttons, confirmDelete, deletePermintaan)

---
**Status**: ✅ DONE | **Build**: ✅ Success | **Date**: 2025-10-20
