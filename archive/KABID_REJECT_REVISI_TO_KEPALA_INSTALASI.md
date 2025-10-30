# UPDATE: PEMOHON DI KABID = KEPALA INSTALASI

## ✅ PERUBAHAN LOGIC

### **Sebelum (Old Logic):**
```
Reject/Revisi → Dikembalikan ke "Unit Pemohon" atau "Admin"
```

### **Sesudah (New Logic):**
```
Reject/Revisi → Dikembalikan ke "Kepala Instalasi" (Pemohon yang membuat permintaan)
```

---

## ✅ FLOW LENGKAP

### **Scenario 1: REJECT (Ditolak)**

```
1. Kabid Umum review permintaan dari Kepala Instalasi IGD
   ↓
2. Kabid klik "Tolak"
   - Isi alasan: "Budget tidak tersedia"
   ↓
3. System Update:
   - Status: ditolak
   - PIC: "Kepala Instalasi IGD" (nama pemohon dari user)
   - Deskripsi: tambah "[DITOLAK oleh Kepala Bidang] Budget tidak tersedia"
   ↓
4. System Create Disposisi:
   - jabatan_tujuan: "Kepala Instalasi Gawat Darurat" (jabatan pemohon)
   - catatan: "Budget tidak tersedia"
   - status: ditolak
   ↓
5. Kepala Instalasi IGD:
   - Lihat permintaan dengan status "ditolak"
   - Baca alasan penolakan
   - Hanya bisa DELETE permintaan
   - Bisa buat permintaan baru jika diperlukan
```

### **Scenario 2: REVISI (Minta Perbaikan)**

```
1. Kabid Umum review permintaan dari Kepala Instalasi Farmasi
   ↓
2. Kabid klik "Minta Revisi"
   - Isi catatan: "Mohon lengkapi spesifikasi teknis detail"
   ↓
3. System Update:
   - Status: revisi
   - PIC: "Kepala Instalasi Farmasi" (nama pemohon)
   - Deskripsi: tambah "[CATATAN REVISI dari Kepala Bidang] Mohon lengkapi..."
   ↓
4. System Create Disposisi:
   - jabatan_tujuan: "Kepala Instalasi Farmasi" (jabatan pemohon)
   - catatan: "Mohon lengkapi spesifikasi teknis detail"
   - status: revisi
   ↓
5. Kepala Instalasi Farmasi:
   - Lihat permintaan dengan status "revisi"
   - Baca catatan revisi
   - Bisa EDIT permintaan
   - Perbaiki sesuai catatan
   - Submit ulang → Status kembali "diajukan"
   - Workflow normal lagi (ke Kabid)
```

---

## ✅ CHANGES MADE

### 1. **Reject Method** (KepalaBidangController.php)

**Old:**
```php
'pic_pimpinan' => $user->name ?? $user->jabatan ?? 'Kepala Bidang',
'jabatan_tujuan' => $permintaan->user->jabatan ?? 'Unit Pemohon',
->with('success', 'Permintaan ditolak dan dikembalikan ke unit pemohon');
```

**New:**
```php
'pic_pimpinan' => $permintaan->user->name ?? 'Kepala Instalasi', // Nama pemohon
'jabatan_tujuan' => $permintaan->user->jabatan ?? 'Kepala Instalasi', // Jabatan pemohon
->with('success', 'Permintaan ditolak dan dikembalikan ke Kepala Instalasi (Pemohon)');
```

### 2. **Request Revision Method** (KepalaBidangController.php)

**Old:**
```php
'status' => 'revisi',
// Tidak update pic_pimpinan
// Tidak buat disposisi
->with('success', 'Permintaan revisi telah dikirim ke pemohon');
```

**New:**
```php
'status' => 'revisi',
'pic_pimpinan' => $permintaan->user->name ?? 'Kepala Instalasi', // Nama pemohon

// Buat disposisi revisi
Disposisi::create([
    'nota_id' => $notaDinas->nota_id,
    'jabatan_tujuan' => $permintaan->user->jabatan ?? 'Kepala Instalasi',
    'catatan' => $data['catatan_revisi'],
    'status' => 'revisi',
]);

->with('success', 'Permintaan revisi telah dikirim ke Kepala Instalasi (Pemohon)');
```

---

## ✅ EXPECTED BEHAVIOR

### **Test Reject:**
```
1. Kabid reject permintaan #84
   - Pemohon: Kepala Instalasi Laundry (Sri Wahyuni)
   
2. Expected:
   ✅ Status: ditolak
   ✅ PIC: Sri Wahyuni, S.T
   ✅ Disposisi: Ke "Kepala Instalasi Laundry & Linen"
   ✅ Flash: "dikembalikan ke Kepala Instalasi (Pemohon)"
   
3. Login sebagai kepala.laundry@rsud.id:
   ✅ Lihat permintaan status "ditolak"
   ✅ Lihat alasan penolakan di deskripsi
   ✅ Button "Delete" tersedia
   ❌ Button "Edit" tidak tersedia (karena ditolak)
```

### **Test Revisi:**
```
1. Kabid revisi permintaan #85
   - Pemohon: Kepala Instalasi Gizi
   
2. Expected:
   ✅ Status: revisi
   ✅ PIC: Nama Kepala Instalasi Gizi
   ✅ Disposisi: Ke "Kepala Instalasi Gizi"
   ✅ Flash: "dikirim ke Kepala Instalasi (Pemohon)"
   
3. Login sebagai kepala instalasi gizi:
   ✅ Lihat permintaan status "revisi"
   ✅ Lihat catatan revisi di deskripsi
   ✅ Button "Edit" tersedia
   ✅ Perbaiki dan submit ulang
   ✅ Status kembali "diajukan"
```

---

## ✅ USER RELATION

### Permintaan memiliki relasi user (pemohon):
```php
// In Permintaan model
public function user()
{
    return $this->belongsTo(User::class);
}
```

### Data pemohon diambil dari:
```php
$permintaan->user->name     // Nama pemohon (Sri Wahyuni, S.T)
$permintaan->user->jabatan  // Jabatan pemohon (Kepala Instalasi Laundry & Linen)
$permintaan->user->email    // Email pemohon (kepala.laundry@rsud.id)
```

---

## ✅ FLASH MESSAGES UPDATED

**Reject:**
```php
'Permintaan ditolak dan dikembalikan ke Kepala Instalasi (Pemohon)'
```

**Revisi:**
```php
'Permintaan revisi telah dikirim ke Kepala Instalasi (Pemohon)'
```

---

## ✅ DISPOSISI TRACKING

Disposisi akan tercatat dengan jelas:

**Reject Disposisi:**
```sql
INSERT INTO disposisi 
(nota_id, jabatan_tujuan, tanggal_disposisi, catatan, status)
VALUES 
(123, 'Kepala Instalasi Gawat Darurat', NOW(), 'Budget tidak tersedia', 'ditolak');
```

**Revisi Disposisi:**
```sql
INSERT INTO disposisi 
(nota_id, jabatan_tujuan, tanggal_disposisi, catatan, status)
VALUES 
(123, 'Kepala Instalasi Farmasi', NOW(), 'Mohon lengkapi spesifikasi', 'revisi');
```

---

## ✅ KEPALA INSTALASI DASHBOARD

Kepala Instalasi akan melihat:

**Status Ditolak:**
```
🔴 Permintaan #84 - DITOLAK
   Alasan: Budget tidak tersedia untuk tahun ini
   Action: [Delete]
```

**Status Revisi:**
```
🟡 Permintaan #85 - REVISI
   Catatan: Mohon lengkapi spesifikasi teknis detail
   Action: [Edit] [View]
```

---

## ✅ TESTING

### Test 1: Reject ke Kepala Instalasi
```bash
1. Login: kabid.umum@rsud.id
2. Buka permintaan dari Kepala Instalasi IGD
3. Klik "Tolak"
4. Isi alasan: "Budget tidak cukup"
5. Submit

Expected:
✅ Flash message: "dikembalikan ke Kepala Instalasi (Pemohon)"
✅ PIC berubah ke nama Kepala Instalasi IGD
✅ Disposisi tercatat ke jabatan Kepala Instalasi IGD

6. Login sebagai kepala instalasi IGD
7. Expected:
   ✅ Lihat permintaan status "ditolak"
   ✅ Bisa delete permintaan
```

### Test 2: Revisi ke Kepala Instalasi
```bash
1. Login: kabid.umum@rsud.id
2. Buka permintaan dari Kepala Instalasi Farmasi
3. Klik "Minta Revisi"
4. Isi catatan: "Lengkapi spesifikasi"
5. Submit

Expected:
✅ Flash message: "dikirim ke Kepala Instalasi (Pemohon)"
✅ PIC berubah ke nama Kepala Instalasi Farmasi
✅ Disposisi tercatat

6. Login sebagai kepala instalasi farmasi
7. Expected:
   ✅ Lihat permintaan status "revisi"
   ✅ Bisa edit dan perbaiki
   ✅ Submit ulang → Status "diajukan"
```

---

## ✅ BENEFITS

1. **Clear Accountability** - Jelas dikembalikan ke siapa (Kepala Instalasi)
2. **Better Tracking** - Disposisi tercatat dengan jelas
3. **Correct Workflow** - Sesuai SOP (ke pemohon, bukan admin)
4. **User Experience** - Kepala Instalasi langsung tahu permintaannya ditolak/revisi
5. **Audit Trail** - History disposisi lengkap

---

**STATUS: READY TO TEST** ✅

Silakan test reject/revisi. Sekarang akan kembali ke **Kepala Instalasi (Pemohon)** yang membuat permintaan!
