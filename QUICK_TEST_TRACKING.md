# 🎯 QUICK TEST GUIDE - REALTIME TRACKING

## 📋 Prerequisites
1. Database running (XAMPP/MySQL)
2. Laravel dev server running (`php artisan serve`)
3. User accounts untuk testing

---

## 🧪 MANUAL TESTING STEPS

### Scenario 1: Fresh Permintaan (Progress 12.5%)

**Setup:**
```bash
# Login sebagai Admin/Kepala Ruang
Email: kepalairna@rsud.id
Password: password
```

**Steps:**
1. ✅ Buat permintaan baru
2. ✅ Access URL: `/permintaan/{id}/tracking`

**Expected Result:**
```
Progress: 12.5% (1/8 steps)
████░░░░░░░░░░░░░░░░ 12.5%

Timeline:
✅ 1. Permintaan - COMPLETED
⏳ 2. Nota Dinas - PENDING
⏳ 3. Disposisi - PENDING
⏳ 4. Perencanaan - PENDING
⏳ 5. KSO - PENDING
⏳ 6. Pengadaan - PENDING
⏳ 7. Nota Penerimaan - PENDING
⏳ 8. Serah Terima - PENDING

Next Step:
  Tahapan: Nota Dinas
  Action: Kepala Instalasi membuat nota dinas
  By: Kepala Instalasi
```

---

### Scenario 2: After Nota Dinas (Progress 25%)

**Setup:**
```bash
# Login sebagai Kepala Instalasi
Email: kepalainstalasi@rsud.id (or kepalairna/kepalairja/kepalapoli)
Password: password
```

**Steps:**
1. ✅ Approve permintaan
2. ✅ Buat Nota Dinas
3. ✅ Access tracking

**Expected Result:**
```
Progress: 25% (2/8 steps)
█████░░░░░░░░░░░░░░░ 25%

Timeline:
✅ 1. Permintaan - COMPLETED (01/11/2025)
✅ 2. Nota Dinas - COMPLETED (01/11/2025)
⏳ 3. Disposisi - PENDING
⏳ 4. Perencanaan - PENDING
⏳ 5. KSO - PENDING
⏳ 6. Pengadaan - PENDING
⏳ 7. Nota Penerimaan - PENDING
⏳ 8. Serah Terima - PENDING

Next Step:
  Tahapan: Disposisi
  Action: Disposisi oleh pimpinan
  By: Kepala Bidang / Direktur
```

---

### Scenario 3: After Kepala Bidang Approve (Progress 37.5%)

**Setup:**
```bash
# Login sebagai Kepala Bidang
Email: kabidyanmed@rsud.id (or kabidpenunjang/kabidumum)
Password: password
```

**Steps:**
1. ✅ Approve permintaan (first approval)
2. ✅ Create disposisi ke Direktur
3. ✅ Access tracking

**Expected Result:**
```
Progress: 37.5% (3/8 steps)
███████░░░░░░░░░░░░░ 37.5%

Timeline:
✅ 1. Permintaan - COMPLETED
✅ 2. Nota Dinas - COMPLETED
✅ 3. Disposisi - COMPLETED
⏳ 4. Perencanaan - PENDING
⏳ 5. KSO - PENDING
⏳ 6. Pengadaan - PENDING
⏳ 7. Nota Penerimaan - PENDING
⏳ 8. Serah Terima - PENDING

Next Step:
  Tahapan: Perencanaan
  Action: Staff Perencanaan membuat rencana pengadaan
  By: Staff Perencanaan
```

---

### Scenario 4: After Direktur & Kabid 2nd Approve (Progress 37.5%)

**Setup:**
```bash
# 1. Login sebagai Direktur
Email: direktur@rsud.id
Password: password

# 2. Direktur approve & disposisi balik ke Kabid

# 3. Login lagi sebagai Kepala Bidang
Email: kabidyanmed@rsud.id
Password: password

# 4. Kabid approve kedua kali → forward ke Staff Perencanaan
```

**Note:** Progress masih 37.5% karena disposisi ketiga tidak dihitung sebagai tahapan baru

---

### Scenario 5: After DPP Created (Progress 50%)

**Setup:**
```bash
# Login sebagai Staff Perencanaan
Email: staff_perencanaan@rsud.id
Password: password
```

**Steps:**
1. ✅ Buka permintaan
2. ✅ Klik "Buat DPP"
3. ✅ Isi semua field DPP
4. ✅ Submit
5. ✅ Access tracking

**Expected Result:**
```
Progress: 50% (4/8 steps)
██████████░░░░░░░░░░ 50%

Timeline:
✅ 1. Permintaan - COMPLETED
✅ 2. Nota Dinas - COMPLETED
✅ 3. Disposisi - COMPLETED
✅ 4. Perencanaan - COMPLETED ← NEW!
⏳ 5. KSO - PENDING
⏳ 6. Pengadaan - PENDING
⏳ 7. Nota Penerimaan - PENDING
⏳ 8. Serah Terima - PENDING

Next Step:
  Tahapan: KSO
  Action: Kerja Sama Operasional dengan vendor
  By: Bagian KSO
```

---

### Scenario 6: Workflow Complete (Progress 100%)

**Setup:**
Complete all 8 steps through the system

**Expected Result:**
```
Progress: 100% (8/8 steps)
████████████████████ 100%

Timeline:
✅ 1. Permintaan - COMPLETED
✅ 2. Nota Dinas - COMPLETED
✅ 3. Disposisi - COMPLETED
✅ 4. Perencanaan - COMPLETED
✅ 5. KSO - COMPLETED
✅ 6. Pengadaan - COMPLETED
✅ 7. Nota Penerimaan - COMPLETED
✅ 8. Serah Terima - COMPLETED

🎉 All steps completed!
   Semua tahapan pengadaan telah selesai dilaksanakan.
```

---

## 🔗 Tracking URLs

### Public/General
```
/permintaan/{id}/tracking
```

### Role-Specific
```
/kepala-instalasi/permintaan/{id}/tracking
/kepala-poli/permintaan/{id}/tracking
/kepala-ruang/permintaan/{id}/tracking
/kepala-bidang/permintaan/{id}/tracking
/direktur/permintaan/{id}/tracking
/wakil-direktur/permintaan/{id}/tracking
/staff-perencanaan/permintaan/{id}/tracking
```

---

## 📊 Testing Checklist

### ✅ Visual Elements to Check

**Progress Bar:**
- [ ] Shows correct percentage
- [ ] Color changes (red → yellow → blue → green)
- [ ] Smooth animation
- [ ] Responsive on mobile

**Timeline:**
- [ ] All 8 steps displayed
- [ ] Completed steps have checkmark ✅
- [ ] Pending steps have clock ⏳
- [ ] Connecting lines (green for completed, gray for pending)
- [ ] Dates shown for completed steps
- [ ] Keterangan/notes displayed

**Next Step Alert:**
- [ ] Blue alert for pending
- [ ] Green alert when completed
- [ ] Correct next step info
- [ ] Correct responsible person

**Detail Permintaan:**
- [ ] All fields displayed correctly
- [ ] Current PIC shown
- [ ] Status badge with correct color

---

## 🧪 Data Validation Tests

### Test 1: Progress Calculation
```javascript
// In browser console on tracking page
console.log('Progress:', props.progress);
console.log('Expected:', (completedSteps.length / 8) * 100);
console.assert(props.progress === (completedSteps.length / 8) * 100);
```

### Test 2: Next Step Logic
```javascript
console.log('Current steps:', completedSteps.length);
console.log('Next step number:', nextStep.step);
console.assert(nextStep.step === completedSteps.length + 1);
```

### Test 3: Timeline Integrity
```javascript
console.log('Timeline count:', completedSteps.length);
console.log('Complete tracking:', completeTracking.length);
console.assert(completeTracking.length === 8); // Always 8
```

---

## 🔍 Debugging Tips

### Issue: Progress shows 0%
**Check:**
```sql
SELECT * FROM permintaan WHERE permintaan_id = X;
-- Ensure tanggal_permintaan is not null
```

### Issue: Timeline not showing Nota Dinas
**Check:**
```sql
SELECT * FROM nota_dinas WHERE permintaan_id = X;
-- Ensure nota dinas exists
```

### Issue: Disposisi not in timeline
**Check:**
```sql
SELECT d.* FROM disposisi d
JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = X;
-- Ensure disposisi linked to nota_dinas
```

### Issue: Perencanaan not showing
**Check:**
```sql
SELECT p.* FROM perencanaan p
JOIN disposisi d ON p.disposisi_id = d.disposisi_id
JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = X;
-- Ensure perencanaan linked to disposisi
```

---

## 🎯 Multi-User Testing

### Test Concurrent Access
1. Open tracking in 3 different browsers:
   - Browser 1: Admin view
   - Browser 2: Kepala Bidang view
   - Browser 3: Staff Perencanaan view

2. All should show **SAME tracking data**

3. Update in one browser → refresh others → all updated

---

## 📱 Responsive Testing

### Desktop (1920x1080)
- [ ] Full timeline visible
- [ ] Progress bar full width
- [ ] All cards side-by-side

### Tablet (768x1024)
- [ ] Timeline scrollable
- [ ] Cards stack vertically
- [ ] Progress bar responsive

### Mobile (375x667)
- [ ] Compact timeline
- [ ] Touch-friendly
- [ ] Readable text

---

## ✅ Success Criteria

Tracking system is working if:

1. ✅ Progress percentage accurate (0-100%)
2. ✅ Timeline shows only completed steps
3. ✅ Next step correctly identified
4. ✅ Complete view shows all 8 steps
5. ✅ Dates shown for completed steps
6. ✅ Status badges correct colors
7. ✅ Multi-role access works
8. ✅ Data updates in realtime
9. ✅ Responsive on all devices
10. ✅ No console errors

---

## 🚀 Quick Start Commands

```bash
# Start database
# Start XAMPP MySQL

# Start Laravel server
php artisan serve

# Clear cache (if needed)
php artisan optimize:clear

# Access tracking (replace {id} with actual ID)
# Open browser: http://localhost:8000/permintaan/{id}/tracking
```

---

## 📝 Test Report Template

```
=== TRACKING TEST REPORT ===

Permintaan ID: ___
Tested By: ___
Date: ___

✅ Progress Calculation: PASS / FAIL
✅ Timeline Display: PASS / FAIL
✅ Next Step Detection: PASS / FAIL
✅ Complete Tracking: PASS / FAIL
✅ Multi-Role Access: PASS / FAIL
✅ Data Integrity: PASS / FAIL
✅ Responsive Design: PASS / FAIL
✅ Performance: PASS / FAIL

Issues Found: ___

Notes: ___
```

---

**Status:** Ready for Testing  
**Estimated Test Time:** 15-20 minutes per scenario  
**Total Test Time:** ~2 hours for complete testing
