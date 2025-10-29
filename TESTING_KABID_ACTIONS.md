# TESTING GUIDE - KABID ACTIONS (APPROVE, REVISI, REJECT)

## ✅ CHECKLIST TESTING

### Prerequisites:
- ✅ Login sebagai: kabid.umum@rsud.id / password
- ✅ Ada permintaan Non Medis dengan status: proses atau diajukan
- ✅ Browser: http://localhost:8000

---

## TEST 1: APPROVE (Skenario 1 - Ke Direktur)

### Setup:
```
Permintaan: #84 (atau Non Medis lainnya)
Status awal: proses
Belum ada disposisi dari Direktur
```

### Steps:
```
1. Login sebagai kabid.umum@rsud.id
2. Buka: /kepala-bidang/permintaan/84
3. Klik button "Setujui" (green button)
4. Modal "Setujui Permintaan" muncul
5. Isi catatan (opsional): "Disetujui untuk pembelian peralatan laundry"
6. Klik "Setujui & Teruskan ke Direktur"
```

### Expected Result:
```
✅ Modal close
✅ Redirect ke: /kepala-bidang/index
✅ Flash message: "Permintaan disetujui dan diteruskan ke Direktur"
✅ Database:
   - permintaan.status = 'proses'
   - permintaan.pic_pimpinan = 'Direktur'
   - disposisi created with:
     * jabatan_tujuan = 'Direktur'
     * catatan = 'Disetujui oleh Kepala Bidang, diteruskan ke Direktur'
     * status = 'disetujui'
```

### Verify in Database:
```sql
SELECT p.permintaan_id, p.status, p.pic_pimpinan,
       d.jabatan_tujuan, d.catatan, d.status as disposisi_status
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE p.permintaan_id = 84
ORDER BY d.tanggal_disposisi DESC
LIMIT 1;
```

---

## TEST 2: APPROVE (Skenario 2 - Ke Staff Perencanaan)

### Setup:
```
Permintaan: #84 (yang sudah di-approve Direktur)
Status: proses
Ada disposisi balik dari Direktur ke Kepala Bidang
```

### Steps:
```
1. Login sebagai kabid.umum@rsud.id
2. Buka: /kepala-bidang/permintaan/84
3. Notice: Info box hijau "Disposisi dari Direktur"
4. Button text: "Teruskan ke Staff Perencanaan"
5. Klik button tersebut
6. Modal muncul
7. Isi catatan (opsional): "Sudah final approved"
8. Klik "Teruskan ke Staff Perencanaan"
```

### Expected Result:
```
✅ Modal close
✅ Redirect ke: /kepala-bidang/index
✅ Flash message: "Permintaan diteruskan ke Staff Perencanaan untuk perencanaan pengadaan"
✅ Database:
   - permintaan.status = 'disetujui'
   - permintaan.pic_pimpinan = 'Staff Perencanaan'
   - disposisi created with:
     * jabatan_tujuan = 'Staff Perencanaan'
     * catatan = 'Sudah disetujui Direktur. Mohon lakukan perencanaan pengadaan.'
     * status = 'disetujui'
```

---

## TEST 3: REJECT (Tolak Permintaan)

### Setup:
```
Permintaan: #85 (atau Non Medis lainnya)
Status: proses
Belum ada disposisi dari Direktur
```

### Steps:
```
1. Login sebagai kabid.umum@rsud.id
2. Buka: /kepala-bidang/permintaan/85
3. Klik button "Tolak" (red button)
4. Modal "Tolak Permintaan" muncul
5. Isi alasan penolakan (REQUIRED): 
   "Budget tidak tersedia untuk tahun ini. Mohon ajukan kembali tahun depan."
6. Klik "Tolak Permintaan"
```

### Expected Result:
```
✅ Modal close
✅ Redirect ke: /kepala-bidang/index
✅ Flash message: "Permintaan ditolak dan dikembalikan ke unit pemohon"
✅ Database:
   - permintaan.status = 'ditolak'
   - permintaan.pic_pimpinan = 'Ir. Bambang Sutrisno, M.M' (atau jabatan)
   - permintaan.deskripsi += "[DITOLAK oleh Kepala Bidang] Budget tidak tersedia..."
   - disposisi created with:
     * jabatan_tujuan = jabatan pemohon (dari user)
     * catatan = alasan penolakan
     * status = 'ditolak'
```

### Verify:
```sql
SELECT permintaan_id, status, pic_pimpinan, 
       SUBSTRING(deskripsi, LENGTH(deskripsi)-100, 100) as last_100_chars
FROM permintaan
WHERE permintaan_id = 85;

-- Should show status = 'ditolak' and deskripsi contains "[DITOLAK..."
```

---

## TEST 4: REVISI (Minta Revisi)

### Setup:
```
Permintaan: #86 (atau Non Medis lainnya)
Status: proses
Belum ada disposisi dari Direktur
```

### Steps:
```
1. Login sebagai kabid.umum@rsud.id
2. Buka: /kepala-bidang/permintaan/86
3. Klik button "Minta Revisi" (yellow button)
4. Modal "Minta Revisi" muncul
5. Isi catatan revisi (REQUIRED):
   "Mohon lengkapi dengan spesifikasi teknis detail dan estimasi biaya per item"
6. Klik "Kirim Permintaan Revisi"
```

### Expected Result:
```
✅ Modal close
✅ Redirect ke: /kepala-bidang/index
✅ Flash message: "Permintaan revisi telah dikirim ke pemohon"
✅ Database:
   - permintaan.status = 'revisi'
   - permintaan.deskripsi += "[CATATAN REVISI dari Kepala Bidang] Mohon lengkapi..."
```

### Verify Admin Can Edit:
```
1. Login sebagai admin atau kepala instalasi (pemohon)
2. Go to: /permintaan
3. Permintaan #86 should show status "revisi"
4. Klik "Edit" button (should be available)
5. Perbaiki permintaan sesuai catatan revisi
6. Submit → Status kembali "diajukan"
```

---

## TEST 5: VALIDATION & ERROR HANDLING

### Test 5.1: Reject tanpa alasan
```
1. Klik "Tolak"
2. Leave "Alasan Penolakan" empty
3. Klik "Tolak Permintaan"
Expected: ✅ Form validation error (field required)
```

### Test 5.2: Revisi tanpa catatan
```
1. Klik "Minta Revisi"
2. Leave "Catatan Revisi" empty
3. Klik "Kirim Permintaan Revisi"
Expected: ✅ Form validation error (field required)
```

### Test 5.3: Action pada permintaan yang sudah ditolak
```
1. Buka permintaan dengan status 'ditolak'
2. Action buttons (Approve, Reject, Revisi) should NOT appear
Expected: ✅ No action buttons visible
```

### Test 5.4: Access permintaan bukan untuk Kabid Umum
```
1. Login sebagai kabid.umum@rsud.id
2. Try access permintaan Medis (e.g., ID yang klasifikasi = 'Medis')
Expected: ✅ 403 Forbidden error
```

---

## TEST 6: BUTTON VISIBILITY

### Kondisi 1: Permintaan Baru (Belum Disposisi Direktur)
```
Status: proses
isDisposisiDariDirektur: false

Expected Buttons:
✅ Setujui (green)
✅ Tolak (red)  
✅ Minta Revisi (yellow)
```

### Kondisi 2: Disposisi Balik dari Direktur
```
Status: proses
isDisposisiDariDirektur: true

Expected Buttons:
✅ Teruskan ke Staff Perencanaan (green)
❌ Tolak (hidden)
❌ Minta Revisi (hidden)
```

### Kondisi 3: Status Ditolak/Revisi
```
Status: ditolak atau revisi

Expected:
❌ No action buttons (hidden)
```

---

## ROUTES TESTING

### Check All Routes Work:
```bash
# Approve
POST /kepala-bidang/permintaan/84/approve

# Reject
POST /kepala-bidang/permintaan/85/reject

# Revisi
POST /kepala-bidang/permintaan/86/revisi
```

### Test with CURL (Optional):
```bash
# Get CSRF token first from the page

# Test Approve
curl -X POST http://localhost:8000/kepala-bidang/permintaan/84/approve \
  -H "Cookie: your_session_cookie" \
  -d "catatan=Test approve"

# Test Reject
curl -X POST http://localhost:8000/kepala-bidang/permintaan/85/reject \
  -H "Cookie: your_session_cookie" \
  -d "alasan=Test reject"

# Test Revisi
curl -X POST http://localhost:8000/kepala-bidang/permintaan/86/revisi \
  -H "Cookie: your_session_cookie" \
  -d "catatan_revisi=Test revisi"
```

---

## COMMON ISSUES & FIXES

### Issue 1: "Undefined variable $klasifikasi"
**Fix:** ✅ Already fixed (added `$klasifikasi = $permintaan->klasifikasi_permintaan;`)

### Issue 2: "Undefined property: nama"
**Fix:** ✅ Already fixed (changed `$user->nama` to `$user->name`)

### Issue 3: 403 Forbidden
**Fix:** ✅ Check klasifikasi_permintaan and kabid_tujuan match

### Issue 4: Modal not closing
**Fix:** Check `showApproveModal.value = false` in submitApprove

### Issue 5: Flash message not showing
**Fix:** Check Inertia shared data in HandleInertiaRequests.php

---

## SUCCESS CRITERIA

All tests should pass:
- ✅ Approve ke Direktur works
- ✅ Approve ke Staff Perencanaan works (after Direktur disposisi)
- ✅ Reject works and returns to admin
- ✅ Revisi works and allows admin to edit
- ✅ Validations prevent empty submissions
- ✅ Button visibility correct based on conditions
- ✅ Flash messages display correctly
- ✅ Database updates correctly
- ✅ Redirects to correct pages

---

## QUICK TEST COMMANDS

```sql
-- Reset permintaan for testing
UPDATE permintaan SET status = 'proses', pic_pimpinan = 'Kepala Bidang' 
WHERE permintaan_id IN (84, 85, 86);

-- Delete test disposisi
DELETE FROM disposisi WHERE nota_id IN (
  SELECT nota_id FROM nota_dinas WHERE permintaan_id IN (84, 85, 86)
) AND created_at > '2025-10-29 00:00:00';

-- Check current status
SELECT permintaan_id, status, pic_pimpinan, klasifikasi_permintaan, kabid_tujuan
FROM permintaan
WHERE permintaan_id IN (84, 85, 86);
```

---

## FINAL CHECKLIST

Before declaring "ALL WORKING":

- [ ] Approve button works (Skenario 1 & 2)
- [ ] Reject button works
- [ ] Revisi button works
- [ ] Validations prevent invalid submissions
- [ ] Flash messages display correctly
- [ ] Redirects work properly
- [ ] Database updates correctly
- [ ] Button visibility based on conditions
- [ ] 403 protection works
- [ ] Can see tracking after approve

**Happy Testing!** 🎉
