# 🔄 Workflow Persetujuan - Admin → Kepala Instalasi → Kepala Bidang

Dokumentasi workflow persetujuan permintaan pengadaan dengan approval bertingkat.

---

## 📋 Alur Workflow

### Flow Chart:
```
Admin (Unit) 
    ↓ (Buat Permintaan)
Kepala Instalasi
    ↓ (Approve/Reject)
    ├─→ [REJECT] → Kembali ke Admin
    └─→ [APPROVE] → Kepala Bidang
            ↓ (Approve/Reject)
            ├─→ [REJECT] → Kembali ke Admin
            └─→ [APPROVE] → Bagian Perencanaan/Pengadaan
                    ↓
                Disposisi → Perencanaan → KSO → Pengadaan → Penerimaan → Serah Terima
```

---

## 👥 Role & Tanggung Jawab

### 1. Admin/Unit
**Tugas:**
- Membuat permintaan pengadaan
- Mengisi detail kebutuhan
- Menentukan bagian tujuan (`bidang`)

**Aksi:**
- ✅ Create permintaan
- ✅ Edit permintaan (jika masih diajukan)
- ✅ Revisi permintaan (jika diminta)

---

### 2. Kepala Instalasi
**Tugas:**
- Review permintaan dari unit
- Validasi kebutuhan bagiannya
- Approve atau reject permintaan

**Aksi:**
- ✅ View permintaan (hanya bagiannya)
- ✅ **Approve** → Teruskan ke Kepala Bidang
- ✅ **Reject** → Kembali ke unit pemohon
- ✅ Request revisi
- ✅ Buat nota dinas (opsional)

**Aturan:**
- Hanya bisa lihat & kelola permintaan bagiannya sendiri
- Kepala Farmasi TIDAK bisa lihat permintaan IGD
- Auto-filter berdasarkan `bidang` = `unit_kerja`

---

### 3. Kepala Bidang (NEW!)
**Tugas:**
- Review permintaan yang sudah disetujui Kepala Instalasi
- Validasi dari sisi kebijakan dan anggaran
- Approve atau reject permintaan

**Aksi:**
- ✅ View permintaan (yang pic_pimpinan = 'Kepala Bidang')
- ✅ **Approve** → Teruskan ke Bagian Perencanaan/Pengadaan
- ✅ **Reject** → Kembali ke unit pemohon
- ✅ Request revisi
- ✅ Buat disposisi

**Aturan:**
- Melihat SEMUA permintaan dari semua bagian yang sudah approved Kepala Instalasi
- Tidak ada filter per bagian (karena supervisi semua unit)

---

## 🔄 Detail Workflow Step-by-Step

### Step 1: Admin Membuat Permintaan

```php
POST /permintaan
{
    "bidang": "Instalasi Farmasi",
    "deskripsi": "Pengadaan obat-obatan",
    "tanggal_permintaan": "2025-10-17"
}

// Result:
// - status: "diajukan"
// - pic_pimpinan: null
```

**Tracking:** Tahap 1/8 - Permintaan (12.5%)

---

### Step 2: Kepala Instalasi Review

#### A. Kepala Instalasi Login
```
Email: kepala_instalasi@rsud.id
Dashboard: Melihat permintaan dengan bidang = "Instalasi Farmasi"
```

#### B. Pilihan Aksi:

**Option 1: Approve**
```php
POST /kepala-instalasi/permintaan/{id}/approve
{
    "catatan": "Disetujui untuk proses lebih lanjut"
}

// Result:
// - status: "proses"
// - pic_pimpinan: "Kepala Bidang"
// - Buat nota dinas ke Kepala Bidang
```

**Option 2: Reject**
```php
POST /kepala-instalasi/permintaan/{id}/reject
{
    "alasan": "Stok masih mencukupi"
}

// Result:
// - status: "ditolak"
// - Kembali ke unit pemohon
```

**Tracking setelah Approve:** Tahap 2/8 - Nota Dinas (25%)

---

### Step 3: Kepala Bidang Review (NEW!)

#### A. Kepala Bidang Login
```
Email: kepala_bidang@rsud.id
Dashboard: Melihat permintaan dengan pic_pimpinan = "Kepala Bidang"
```

#### B. Pilihan Aksi:

**Option 1: Approve**
```php
POST /kepala-bidang/permintaan/{id}/approve
{
    "tujuan": "Bagian Perencanaan", // atau "Bagian Pengadaan"
    "catatan": "Disetujui, lanjutkan ke perencanaan"
}

// Result:
// - status: "disetujui"
// - pic_pimpinan: "Bagian Perencanaan"
// - Buat disposisi otomatis
```

**Option 2: Reject**
```php
POST /kepala-bidang/permintaan/{id}/reject
{
    "alasan": "Tidak sesuai anggaran"
}

// Result:
// - status: "ditolak"
// - Kembali ke unit pemohon
```

**Option 3: Buat Disposisi Manual**
```php
POST /kepala-bidang/permintaan/{id}/disposisi
{
    "jabatan_tujuan": "Bagian Perencanaan",
    "tanggal_disposisi": "2025-10-17",
    "catatan": "Lanjutkan ke tahap perencanaan"
}
```

**Tracking setelah Approve:** Tahap 3/8 - Disposisi (37.5%)

---

### Step 4: Proses Selanjutnya

Setelah disetujui Kepala Bidang, permintaan masuk ke tahapan:
- Perencanaan
- KSO
- Pengadaan
- Nota Penerimaan
- Serah Terima

---

## 🎯 Skenario Lengkap

### Skenario 1: Happy Path (Semua Approve)

```
1. Admin Farmasi → Buat permintaan obat
   Status: diajukan

2. Kepala Instalasi Farmasi → Login & Approve
   Status: proses → ke Kepala Bidang
   Tracking: Nota Dinas (25%)

3. Kepala Bidang → Login & Approve
   Status: disetujui → ke Bagian Perencanaan
   Tracking: Disposisi (37.5%)

4. Bagian Perencanaan → Proses
   Tracking: Perencanaan (50%)

5. ... dst sampai Serah Terima (100%)
```

---

### Skenario 2: Reject di Kepala Instalasi

```
1. Admin IGD → Buat permintaan alat medis
   Status: diajukan

2. Kepala Instalasi IGD → Review & Reject
   Alasan: "Sudah ada alat serupa"
   Status: ditolak
   
3. STOP - Kembali ke Admin untuk revisi atau dibatalkan
```

---

### Skenario 3: Reject di Kepala Bidang

```
1. Admin Farmasi → Buat permintaan
   Status: diajukan

2. Kepala Instalasi Farmasi → Approve
   Status: proses → ke Kepala Bidang

3. Kepala Bidang → Review & Reject
   Alasan: "Melebihi anggaran yang tersedia"
   Status: ditolak
   
4. STOP - Kembali ke Admin
```

---

### Skenario 4: Request Revisi

```
1. Admin → Buat permintaan
   Status: diajukan

2. Kepala Instalasi → Request Revisi
   Catatan: "Tolong detailkan spesifikasi"
   Status: revisi

3. Admin → Update permintaan dengan detail lengkap
   Status: diajukan

4. Kepala Instalasi → Review ulang → Approve
   Status: proses → ke Kepala Bidang

5. Kepala Bidang → Approve
   Status: disetujui
```

---

## 🔐 Authorization Matrix

| Aksi | Admin | Kepala Instalasi | Kepala Bidang |
|------|-------|------------------|---------------|
| Create Permintaan | ✅ | ❌ | ❌ |
| View Permintaan | ✅ Own | ✅ Bagiannya | ✅ All (pic=KB) |
| Edit Permintaan | ✅ Own | ❌ | ❌ |
| Approve Level 1 | ❌ | ✅ | ❌ |
| Approve Level 2 | ❌ | ❌ | ✅ |
| Reject | ❌ | ✅ | ✅ |
| Buat Nota Dinas | ❌ | ✅ | ❌ |
| Buat Disposisi | ❌ | ❌ | ✅ |

---

## 📊 Status Permintaan

| Status | Keterangan | PIC |
|--------|------------|-----|
| `diajukan` | Permintaan baru dari admin | - |
| `proses` | Sedang diproses oleh pejabat | Kepala Instalasi/Bidang |
| `disetujui` | Sudah disetujui semua level | Bagian terkait |
| `ditolak` | Ditolak oleh pejabat | - |
| `revisi` | Butuh revisi dari admin | Admin |

---

## 🔗 Routes

### Kepala Instalasi
```
GET  /kepala-instalasi/dashboard
GET  /kepala-instalasi
GET  /kepala-instalasi/permintaan/{id}
POST /kepala-instalasi/permintaan/{id}/approve
POST /kepala-instalasi/permintaan/{id}/reject
POST /kepala-instalasi/permintaan/{id}/revisi
```

### Kepala Bidang (NEW!)
```
GET  /kepala-bidang/dashboard
GET  /kepala-bidang
GET  /kepala-bidang/permintaan/{id}
POST /kepala-bidang/permintaan/{id}/approve
POST /kepala-bidang/permintaan/{id}/reject
POST /kepala-bidang/permintaan/{id}/disposisi
```

---

## 💻 Code Implementation

### Kepala Instalasi Approve
```php
// KepalaInstalasiController@approve
$permintaan->update([
    'status' => 'proses',
    'pic_pimpinan' => 'Kepala Bidang'
]);

NotaDinas::create([
    'permintaan_id' => $permintaan->permintaan_id,
    'dari_unit' => $user->unit_kerja,
    'ke_jabatan' => 'Kepala Bidang',
    'status' => 'dikirim'
]);
```

### Kepala Bidang Approve
```php
// KepalaBidangController@approve
Disposisi::create([
    'nota_id' => $notaDinas->nota_id,
    'jabatan_tujuan' => $request->tujuan,
    'status' => 'disetujui'
]);

$permintaan->update([
    'status' => 'disetujui',
    'pic_pimpinan' => $request->tujuan
]);
```

---

## 🧪 Testing

### Test Data (dari Seeder)
```
Total Users: 5
- 1 Admin/Staff Farmasi
- 1 Admin/Staff IGD
- 1 Kepala Instalasi Farmasi
- 1 Kepala Instalasi IGD
- 1 Kepala Bidang

Total Permintaan: 6
- 2 Diajukan (menunggu Kepala Instalasi)
- 1 Proses (di Kepala Bidang) ← NEW!
- 1 Disetujui
- 1 Ditolak
- 1 IGD (testing isolasi)
```

### Test Workflow
```bash
# 1. Login Kepala Instalasi
Email: kepala_instalasi@rsud.id
Action: Approve permintaan #1
Expected: Status = proses, PIC = Kepala Bidang

# 2. Login Kepala Bidang
Email: kepala_bidang@rsud.id
Dashboard: Harus melihat permintaan #1 & #3
Action: Approve permintaan #3
Expected: Status = disetujui, PIC = Bagian Perencanaan

# 3. Tracking
URL: /kepala-instalasi/permintaan/3/tracking
Expected: Timeline 3 tahap (Permintaan, Nota Dinas, Disposisi)
```

---

## 📈 Benefits

### Untuk Organisasi:
- ✅ **Kontrol Berlapis** - Approval 2 tingkat
- ✅ **Akuntabilitas** - Jelas siapa yang approve
- ✅ **Audit Trail** - Semua keputusan tercatat
- ✅ **Segregation of Duty** - Pemisahan tugas jelas

### Untuk Kepala Instalasi:
- ✅ Fokus hanya ke bagiannya
- ✅ Tidak overwhelmed dengan data semua bagian
- ✅ Tracking progress permintaan

### Untuk Kepala Bidang:
- ✅ Supervisi semua bagian
- ✅ Kontrol anggaran & kebijakan
- ✅ Bisa reject/revisi jika tidak sesuai

---

## 📝 Next Steps

### Frontend (TODO)
- [ ] UI untuk Kepala Bidang dashboard
- [ ] Form approve dengan pilihan tujuan
- [ ] Form reject dengan alasan
- [ ] Form disposisi manual

### Enhancement (Future)
- [ ] Multi-level approval (3+ level)
- [ ] Parallel approval (butuh 2+ approver)
- [ ] Delegation (temporary assignment)
- [ ] Auto-approval (based on amount/category)

---

**Version:** 1.3.0  
**Last Updated:** 17 Oktober 2025  
**Status:** ✅ Implemented
