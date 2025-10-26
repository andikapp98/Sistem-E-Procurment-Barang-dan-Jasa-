# Quick Fix: Admin - Ditolak Hanya Delete

## ✅ Masalah Diperbaiki
Admin sekarang **TIDAK BISA** edit permintaan yang sudah ditolak, hanya bisa **HAPUS**.

## 📋 Perubahan

### Show.vue (Detail Permintaan)
- Status **Revisi** → Tombol "Edit & Ajukan Ulang" ✏️
- Status **Ditolak** → Tombol "Hapus" saja 🗑️

### Index.vue (Daftar Permintaan)
```javascript
canEdit() → return status === 'revisi' (bukan 'ditolak')
canDelete() → return status === 'ditolak'
```

## 🎯 Behavior Baru

| Status | Edit? | Delete? | Warna |
|--------|-------|---------|-------|
| **revisi** | ✅ Ya | ❌ Tidak | 🟠 Orange |
| **ditolak** | ❌ Tidak | ✅ Ya | 🔴 Merah |

**Ditolak = Final, tidak bisa diajukan ulang, hanya hapus!**
