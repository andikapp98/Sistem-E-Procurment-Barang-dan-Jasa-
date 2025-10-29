# KSO Show Page - Quick Reference

## ✅ View Sudah Tersedia!

### URL
```
http://localhost:8000/kso/permintaan/17
```

### Route
```php
GET  /kso/permintaan/{permintaan}  →  KSOController@show
Name: kso.show
```

### File Location
```
resources/js/Pages/KSO/Show.vue (281 lines)
```

---

## 🚀 Quick Start

### 1. Login sebagai KSO
```
URL: http://localhost:8000/login
Email: kso@example.com
Password: password
```

### 2. Set Permintaan PIC
```sql
UPDATE permintaan 
SET pic_pimpinan = 'Bagian KSO'
WHERE permintaan_id = 17;
```

### 3. Akses Show Page
```
http://localhost:8000/kso/permintaan/17
```

---

## 📋 What You'll See

### Jika KSO Belum Ada:
```
┌─────────────────────────────────────┐
│ Detail Permintaan #17               │
├─────────────────────────────────────┤
│ Informasi Permintaan                │
│ - ID, Tanggal, Bidang, dll          │
├─────────────────────────────────────┤
│ Data KSO                            │
│ 📄 Belum Ada Data KSO               │
│ [+ Buat Dokumen KSO]                │
└─────────────────────────────────────┘
```

### Jika KSO Sudah Ada:
```
┌─────────────────────────────────────┐
│ Detail Permintaan #17               │
├─────────────────────────────────────┤
│ Informasi Permintaan                │
│ - ID, Tanggal, Bidang, dll          │
├─────────────────────────────────────┤
│ Data KSO                            │
│ - No KSO, Tanggal, Pihak 1 & 2     │
│ - Status: [aktif]                   │
├─────────────────────────────────────┤
│ 📄 Dokumen KSO                      │
│ ┌──────────┐  ┌──────────┐         │
│ │ 📕 PKS   │  │ 📘 MoU   │         │
│ │ Download │  │ Download │         │
│ └──────────┘  └──────────┘         │
├─────────────────────────────────────┤
│ 💬 Keterangan (if any)              │
├─────────────────────────────────────┤
│ 🕐 Timeline                         │
│ - Dibuat: 28 Okt 2025              │
│ - Update: 28 Okt 2025              │
└─────────────────────────────────────┘
```

---

## 🎨 Features

### ✅ Responsive Design
- Mobile: Cards stack vertically
- Desktop: 2-column grid

### ✅ File Downloads
- PKS: Red PDF icon + Download button
- MoU: Blue PDF icon + Download button
- If not uploaded: Disabled button (gray)

### ✅ Status Badges
- Draft: Gray
- Aktif: Blue
- Selesai: Green
- Batal: Red

### ✅ Actions
- [Edit] button → Edit KSO
- [Hapus] button → Delete KSO (with confirmation)
- [← Kembali] → Back to KSO Index

---

## 🔧 Troubleshooting

### Error 403 - Access Denied
```sql
-- Fix: Update pic_pimpinan
UPDATE permintaan 
SET pic_pimpinan = 'Bagian KSO'
WHERE permintaan_id = 17;
```

### Error 404 - Not Found
```bash
# Check if permintaan exists
php artisan tinker --execute="App\Models\Permintaan::find(17)"
```

### Files Can't Download
```bash
# Create storage link
php artisan storage:link
```

### Page Expired (419)
```bash
# Clear cache and rebuild
php artisan optimize:clear
yarn build
```

---

## 📱 Mobile View

```
┌────────────────────┐
│ Detail #17         │
├────────────────────┤
│ Informasi          │
│ (stacked)          │
├────────────────────┤
│ Data KSO           │
│ (full width)       │
├────────────────────┤
│ 📕 PKS             │
│ [Download]         │
├────────────────────┤
│ 📘 MoU             │
│ [Download]         │
└────────────────────┘
```

---

## 💻 Desktop View

```
┌─────────────────────────────────────────┐
│ Detail Permintaan #17      [← Kembali]  │
├──────────────┬──────────────────────────┤
│ ID: #17      │ Bidang: IGD              │
│ Tanggal: ... │ Unit: ...                │
├──────────────┴──────────────────────────┤
│ No KSO: ...  │ Status: [aktif]          │
├──────────────┴──────────────────────────┤
│ 📕 PKS          │ 📘 MoU                 │
│ [Download]      │ [Download]             │
└─────────────────────────────────────────┘
```

---

## 🎯 Test Checklist

- [ ] Page loads without error
- [ ] Informasi Permintaan displays correctly
- [ ] Data KSO displays (or "Belum Ada" message)
- [ ] If KSO exists:
  - [ ] No KSO, Tanggal, Pihak shown
  - [ ] Status badge correct color
  - [ ] PKS file shown with download button
  - [ ] MoU file shown with download button
  - [ ] Keterangan shown (if any)
  - [ ] Timeline shown
  - [ ] Edit button works
  - [ ] Delete button works (with confirmation)
- [ ] If KSO not exists:
  - [ ] "Belum Ada Data KSO" message
  - [ ] "Buat Dokumen KSO" button works
- [ ] Back button navigates to /kso
- [ ] File downloads work
- [ ] Responsive on mobile
- [ ] No console errors

---

## 📚 Documentation Files

- `KSO_SIMPLIFIED_PKS_MOU_ONLY.md` - Feature overview
- `KSO_VIEW_DOCUMENTATION.md` - Complete view docs
- `KSO_SHOW_TESTING_GUIDE.md` - Detailed testing guide
- `KSO_SHOW_QUICK_REFERENCE.md` - This file

---

## ⚡ One-Liner Commands

```bash
# Quick test if page works
curl http://localhost:8000/kso/permintaan/17

# Check route exists
php artisan route:list | grep "kso.show"

# Check controller method
grep -n "public function show" app/Http/Controllers/KSOController.php

# Check view file
ls -lh resources/js/Pages/KSO/Show.vue

# Full reset and test
php artisan optimize:clear && yarn build && php artisan serve
```

---

## 🎉 Status: READY TO USE!

View sudah dibuat dan siap digunakan. Silakan test dengan mengakses:
**http://localhost:8000/kso/permintaan/17**
