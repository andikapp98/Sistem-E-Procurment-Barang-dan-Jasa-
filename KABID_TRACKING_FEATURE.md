# TRACKING UNTUK KABID - DOKUMENTASI

## ✅ FITUR TRACKING SUDAH TERSEDIA

### 1. Tracking di Halaman Detail Permintaan

**Route:** `/kepala-bidang/permintaan/{id}`

**Fitur:**
- ✅ Progress bar menampilkan % completion
- ✅ Timeline singkat menampilkan tahapan yang sudah dilalui
- ✅ Status setiap tahapan (tanggal, keterangan, status)
- ✅ **NEW:** Button "Lihat Full Tracking" di header (untuk permintaan proses/disetujui)

### 2. Full Tracking Page

**Route:** `/kepala-bidang/permintaan/{id}/tracking`

**Fitur:**
- ✅ Timeline lengkap semua tahapan
- ✅ Progress percentage
- ✅ Completed steps vs Pending steps
- ✅ Detail setiap tahapan dengan tanggal dan keterangan

### 3. Approved/History Page

**Route:** `/kepala-bidang/approved`

**Fitur:**
- ✅ List semua permintaan yang sudah pernah di-approve Kabid
- ✅ Filter dan search
- ✅ Tracking status untuk setiap permintaan
- ✅ Timeline count

---

## CARA MENGGUNAKAN

### A. Lihat Tracking dari Detail Permintaan

```
1. Login sebagai kabid.umum@rsud.id
2. Buka Dashboard atau Index
3. Klik salah satu permintaan
4. Di halaman detail, scroll ke bawah lihat "Progress Tracking"
5. Atau klik button "Lihat Full Tracking" di header
```

### B. Lihat Full Tracking

```
1. Dari halaman detail permintaan
2. Klik button "Lihat Full Tracking" (purple button)
3. Akan redirect ke: /kepala-bidang/permintaan/{id}/tracking
4. Lihat timeline lengkap, completed vs pending steps
```

### C. Lihat History Approved

```
1. Dari dashboard Kabid
2. Klik menu "Approved" atau akses: /kepala-bidang/approved
3. Lihat semua permintaan yang sudah pernah di-approve
4. Klik tracking icon untuk lihat progress
```

---

## FLOW TRACKING

### Tahapan Tracking:

1. **Permintaan** - Admin buat permintaan
2. **Nota Dinas** - Kepala Instalasi buat nota dinas
3. **Disposisi** - Disposisi ke Kabid/Direktur
4. **Perencanaan** - Staff Perencanaan upload dokumen
5. **KSO** - Proses KSO (jika ada)
6. **Pengadaan** - Proses pengadaan
7. **Nota Penerimaan** - Penerimaan barang
8. **Serah Terima** - Serah terima final

### Progress Calculation:

```php
$progress = ($completedSteps / $totalSteps) * 100;
```

---

## ROUTES TRACKING

```
# View Detail dengan Tracking
GET /kepala-bidang/permintaan/{id}

# View Full Tracking
GET /kepala-bidang/permintaan/{id}/tracking

# List Approved/History
GET /kepala-bidang/approved
```

---

## BUTTON "LIHAT FULL TRACKING"

**Lokasi:** Header di Show.vue (Detail Permintaan)

**Kondisi Tampil:**
- Status: `proses` atau `disetujui`
- Tersedia untuk semua permintaan yang sudah masuk tahap approval

**Styling:**
- Purple button (`bg-purple-600`)
- Icon chart bar
- Text: "Lihat Full Tracking"

---

## TESTING

### Test Tracking Feature:

```bash
# 1. Login sebagai Kabid Umum
Email: kabid.umum@rsud.id
Password: password

# 2. Pilih permintaan Non Medis (ID: 78-86)
# 3. Buka detail permintaan
# 4. Check:
   - ✅ Progress bar visible
   - ✅ Timeline short visible
   - ✅ Button "Lihat Full Tracking" visible

# 5. Klik "Lihat Full Tracking"
# 6. Check:
   - ✅ Full timeline displayed
   - ✅ Completed vs Pending steps shown
   - ✅ Progress percentage accurate

# 7. Test Approved page
   - Navigate to /kepala-bidang/approved
   - ✅ See all approved permintaan
   - ✅ Click tracking icon on each row
```

---

## EXPECTED OUTPUT

### Progress Tracking (Short - di Show.vue):

```
Progress Tracking                           [45%] Complete
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✓ Permintaan         15 Okt 2025
  Permintaan dibuat oleh Admin
  [Selesai]

✓ Nota Dinas         16 Okt 2025
  Nota dinas dibuat oleh Kepala Instalasi
  [Selesai]

✓ Disposisi          17 Okt 2025
  Disetujui oleh Kabid, diteruskan ke Direktur
  [Selesai]
```

### Full Tracking (di Tracking.vue):

```
Timeline Permintaan #84                     Progress: 45%

COMPLETED STEPS (3/8):
✓ Permintaan
✓ Nota Dinas  
✓ Disposisi

PENDING STEPS (5/8):
○ Perencanaan
○ KSO
○ Pengadaan
○ Nota Penerimaan
○ Serah Terima
```

---

## BENEFITS

1. **Transparansi** - Kabid bisa lihat progress permintaan yang sudah di-approve
2. **Monitoring** - Track sampai mana tahapan pengadaan
3. **History** - Lihat semua permintaan yang pernah di-approve
4. **Accountability** - Jelas siapa approve di tahap mana
5. **Decision Making** - Bisa lihat pattern approval untuk improvement

---

## NEXT ENHANCEMENTS (Optional)

- [ ] Add filter by status di Approved page
- [ ] Export tracking report to PDF
- [ ] Email notification when tracking updated
- [ ] Dashboard widget untuk tracking summary
- [ ] Add notes/comments per tahapan
