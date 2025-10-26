# Ringkasan Workflow, CRUD, dan Views untuk Setiap Role

## 📋 Daftar Role dalam Sistem

1. **Admin** (Super User)
2. **Kepala Instalasi** 
3. **Kepala Bidang**
4. **Wakil Direktur**
5. **Direktur**
6. **Staff Perencanaan**
7. **KSO** (Komite Standar Operasional)
8. **Pengadaan**
9. **Serah Terima**

---

## 🔄 WORKFLOW LENGKAP SISTEM PENGADAAN

```
1. KEPALA INSTALASI
   └── Membuat Permintaan
       └── Status: "diajukan"
       
2. ADMIN (Opsional)
   └── Review & Edit Permintaan
       └── Dapat Edit/Delete jika status "ditolak"
       
3. KEPALA INSTALASI
   └── Approve Permintaan
       └── Status: "disetujui_instalasi"
       └── Forward ke KEPALA BIDANG
       
4. KEPALA BIDANG
   └── Review Permintaan
   └── Buat Disposisi
       └── Pilihan:
           ├── Teruskan ke DIREKTUR
           ├── Teruskan ke WAKIL DIREKTUR
           └── Teruskan ke STAFF PERENCANAAN
       └── Status: "disetujui_kabid"
       
5. DIREKTUR / WAKIL DIREKTUR
   └── Review & Approve
       └── Pilihan:
           ├── Approve → Status: "disetujui_direktur"
           ├── Reject → Status: "ditolak"
           └── Minta Revisi → Kembali ke KEPALA BIDANG
       └── Jika Approve:
           └── Buat Disposisi ke STAFF PERENCANAAN
       
6. STAFF PERENCANAAN
   └── Proses Perencanaan
       ├── Buat Nota Dinas Usulan
       ├── Buat Nota Dinas Pembelian
       ├── Upload Scan Berkas
       ├── Buat DPP (Dokumen Persiapan Pengadaan)
       └── Buat HPS (Harga Perkiraan Satuan)
       └── Status: "perencanaan_selesai"
       └── Forward ke KSO atau PENGADAAN
       
7. KSO
   └── Input data KSO
       └── Status: "kso_selesai"
       
8. PENGADAAN
   └── Proses Pengadaan
       └── Status: "pengadaan_selesai"
       
9. SERAH TERIMA
   └── Nota Penerimaan
   └── Serah Terima ke Kepala Instalasi
       └── Status: "selesai"
```

---

## 👥 DETAIL SETIAP ROLE

### 1. ADMIN (Super User)

**Dashboard:** `/dashboard`

**CRUD Capabilities:**
- ✅ **Create** - Buat permintaan baru
- ✅ **Read** - Lihat semua permintaan
- ✅ **Update** - Edit permintaan (jika status "ditolak")
- ✅ **Delete** - Hapus permintaan (jika status "ditolak")

**Routes:**
```php
GET     /dashboard
GET     /permintaan                  → index
GET     /permintaan/create          → create
POST    /permintaan                 → store
GET     /permintaan/{id}            → show
GET     /permintaan/{id}/edit       → edit
PUT     /permintaan/{id}            → update
DELETE  /permintaan/{id}            → destroy
GET     /permintaan/{id}/tracking   → tracking
GET     /permintaan/{id}/cetak-nota-dinas → cetak
GET     /nota-dinas/{id}/lampiran   → lihat lampiran
```

**Views:**
- `Dashboard.vue` - Dashboard utama dengan statistik
- `Permintaan/Index.vue` - List semua permintaan
- `Permintaan/Create.vue` - Form buat permintaan
- `Permintaan/Edit.vue` - Form edit permintaan
- `Permintaan/Show.vue` - Detail permintaan
- `Permintaan/Tracking.vue` - Timeline tracking

**Fitur Khusus:**
- Akses penuh ke semua permintaan
- Dapat edit/delete permintaan yang ditolak
- Lihat tracking lengkap
- Cetak nota dinas dan lihat lampiran

---

### 2. KEPALA INSTALASI

**Dashboard:** `/kepala-instalasi/dashboard`

**CRUD Capabilities:**
- ✅ **Create** - Buat permintaan untuk unit-nya
- ✅ **Read** - Lihat permintaan unit-nya saja
- ❌ **Update** - Tidak bisa edit (hanya approve/reject/revisi)
- ❌ **Delete** - Tidak bisa delete

**Routes:**
```php
GET     /kepala-instalasi/dashboard
GET     /kepala-instalasi/               → index
GET     /kepala-instalasi/permintaan/{id} → show
GET     /kepala-instalasi/permintaan/{id}/tracking
POST    /kepala-instalasi/permintaan/{id}/approve
POST    /kepala-instalasi/permintaan/{id}/reject
POST    /kepala-instalasi/permintaan/{id}/revisi
GET     /kepala-instalasi/permintaan/{id}/review-rejected
POST    /kepala-instalasi/permintaan/{id}/resubmit
GET     /kepala-instalasi/permintaan/{id}/cetak-nota-dinas
GET     /kepala-instalasi/nota-dinas/{id}/lampiran
```

**Views:**
- `KepalaInstalasi/Dashboard.vue` - Dashboard dengan statistik unit
- `KepalaInstalasi/Index.vue` - List permintaan unit
- `KepalaInstalasi/Show.vue` - Detail permintaan dengan actions

**Actions:**
- **Approve** - Setujui permintaan → Status: "disetujui_instalasi" → Forward ke Kepala Bidang
- **Reject** - Tolak permintaan → Status: "ditolak"
- **Revisi** - Minta revisi → Status: "revisi"
- **Resubmit** - Ajukan ulang permintaan yang ditolak

**Filter Data:**
- Hanya melihat permintaan dari unit_kerja sendiri
- Matching berdasarkan bidang di tabel permintaan

---

### 3. KEPALA BIDANG

**Dashboard:** `/kepala-bidang/dashboard`

**CRUD Capabilities:**
- ❌ **Create** - Tidak bisa buat permintaan
- ✅ **Read** - Lihat permintaan yang sudah disetujui instalasi
- ❌ **Update** - Tidak bisa edit (hanya buat disposisi)
- ❌ **Delete** - Tidak bisa delete

**Routes:**
```php
GET     /kepala-bidang/dashboard
GET     /kepala-bidang/index            → index (pending)
GET     /kepala-bidang/approved         → approved list
GET     /kepala-bidang/permintaan/{id}  → show
GET     /kepala-bidang/permintaan/{id}/tracking
GET     /kepala-bidang/permintaan/{id}/disposisi/create
POST    /kepala-bidang/permintaan/{id}/disposisi
POST    /kepala-bidang/permintaan/{id}/approve
POST    /kepala-bidang/permintaan/{id}/reject
POST    /kepala-bidang/permintaan/{id}/revisi
```

**Views:**
- `KepalaBidang/Dashboard.vue` - Dashboard dengan statistik
- `KepalaBidang/Index.vue` - List permintaan pending review
- `KepalaBidang/Approved.vue` - List permintaan yang sudah disetujui
- `KepalaBidang/Show.vue` - Detail permintaan
- `KepalaBidang/CreateDisposisi.vue` - Form disposisi
- `KepalaBidang/Tracking.vue` - Timeline tracking

**Actions:**
- **Create Disposisi** - Teruskan ke:
  - Direktur
  - Wakil Direktur
  - Staff Perencanaan (langsung jika < budget tertentu)
- **Approve** - Setujui permintaan → Status: "disetujui_kabid"
- **Reject** - Tolak permintaan
- **Revisi** - Minta revisi

**Filter Data:**
- Permintaan dengan status "disetujui_instalasi"
- Permintaan yang sudah di-disposisi

---

### 4. WAKIL DIREKTUR

**Dashboard:** `/wakil-direktur/dashboard`

**CRUD Capabilities:**
- ❌ **Create** - Tidak bisa buat permintaan
- ✅ **Read** - Lihat permintaan yang di-disposisi ke dirinya
- ❌ **Update** - Tidak bisa edit
- ❌ **Delete** - Tidak bisa delete

**Routes:**
```php
GET     /wakil-direktur/dashboard
GET     /wakil-direktur/                → index
GET     /wakil-direktur/approved        → approved list
GET     /wakil-direktur/permintaan/{id} → show
GET     /wakil-direktur/permintaan/{id}/tracking
GET     /wakil-direktur/permintaan/{id}/disposisi/create
POST    /wakil-direktur/permintaan/{id}/disposisi
POST    /wakil-direktur/permintaan/{id}/approve
POST    /wakil-direktur/permintaan/{id}/reject
POST    /wakil-direktur/permintaan/{id}/revisi
```

**Views:**
- `WakilDirektur/Dashboard.vue` - Dashboard
- `WakilDirektur/Index.vue` - List permintaan pending
- `WakilDirektur/Show.vue` - Detail permintaan
- `WakilDirektur/CreateDisposisi.vue` - Form disposisi

**Actions:**
- **Approve** - Setujui → Status: "disetujui_wadir" → Disposisi ke Staff Perencanaan
- **Reject** - Tolak permintaan
- **Revisi** - Minta revisi ke Kepala Bidang
- **Create Disposisi** - Teruskan ke Staff Perencanaan

**Filter Data:**
- Permintaan dengan wadir_tujuan = nama wakil direktur
- Status "disetujui_kabid"

---

### 5. DIREKTUR

**Dashboard:** `/direktur/dashboard`

**CRUD Capabilities:**
- ❌ **Create** - Tidak bisa buat permintaan
- ✅ **Read** - Lihat permintaan yang di-disposisi ke dirinya
- ❌ **Update** - Tidak bisa edit
- ❌ **Delete** - Tidak bisa delete

**Routes:**
```php
GET     /direktur/dashboard
GET     /direktur/                      → index
GET     /direktur/approved              → approved list
GET     /direktur/permintaan/{id}       → show
GET     /direktur/permintaan/{id}/tracking
GET     /direktur/permintaan/{id}/disposisi/create
POST    /direktur/permintaan/{id}/disposisi
POST    /direktur/permintaan/{id}/setujui
POST    /direktur/permintaan/{id}/tolak
POST    /direktur/permintaan/{id}/revisi
```

**Views:**
- `Direktur/Dashboard.vue` - Dashboard
- `Direktur/Index.vue` - List permintaan pending
- `Direktur/Approved.vue` - List permintaan approved
- `Direktur/Show.vue` - Detail permintaan
- `Direktur/Tracking.vue` - Timeline tracking

**Actions:**
- **Approve (Setujui)** - Setujui → Status: "disetujui_direktur" → Disposisi ke Staff Perencanaan
- **Reject (Tolak)** - Tolak permintaan
- **Revisi** - Minta revisi ke Kepala Bidang
- **Create Disposisi** - Teruskan ke Staff Perencanaan

**Filter Data:**
- Permintaan dengan disposisi_tujuan = "Direktur"
- Status "disetujui_kabid"

---

### 6. STAFF PERENCANAAN

**Dashboard:** `/staff-perencanaan/dashboard`

**CRUD Capabilities:**
- ❌ **Create** - Tidak bisa buat permintaan
- ✅ **Read** - Lihat permintaan yang sudah disetujui direktur/wadir
- ✅ **Update** - Buat dokumen perencanaan (Nota Dinas, DPP, HPS)
- ❌ **Delete** - Tidak bisa delete

**Routes:**
```php
GET     /staff-perencanaan/dashboard
GET     /staff-perencanaan/             → index
GET     /staff-perencanaan/approved     → approved list
GET     /staff-perencanaan/permintaan/{id} → show
GET     /staff-perencanaan/permintaan/{id}/tracking

# Perencanaan
GET     /staff-perencanaan/permintaan/{id}/perencanaan/create
POST    /staff-perencanaan/permintaan/{id}/perencanaan

# Nota Dinas Usulan
GET     /staff-perencanaan/permintaan/{id}/nota-dinas/create
POST    /staff-perencanaan/permintaan/{id}/nota-dinas

# Nota Dinas Pembelian
GET     /staff-perencanaan/permintaan/{id}/nota-dinas-pembelian/create
POST    /staff-perencanaan/permintaan/{id}/nota-dinas-pembelian

# Upload Dokumen
GET     /staff-perencanaan/permintaan/{id}/scan-berkas
POST    /staff-perencanaan/permintaan/{id}/dokumen

# DPP (Dokumen Persiapan Pengadaan)
GET     /staff-perencanaan/permintaan/{id}/dpp/create
POST    /staff-perencanaan/permintaan/{id}/dpp

# HPS (Harga Perkiraan Satuan)
GET     /staff-perencanaan/permintaan/{id}/hps/create
POST    /staff-perencanaan/permintaan/{id}/hps

# Disposisi
GET     /staff-perencanaan/permintaan/{id}/disposisi/create
POST    /staff-perencanaan/permintaan/{id}/disposisi
```

**Views:**
- `StaffPerencanaan/Dashboard.vue` - Dashboard
- `StaffPerencanaan/Index.vue` - List permintaan pending
- `StaffPerencanaan/Show.vue` - Detail permintaan
- `StaffPerencanaan/CreatePerencanaan.vue` - Form perencanaan
- `StaffPerencanaan/CreateNotaDinas.vue` - Form Nota Dinas Usulan
- `StaffPerencanaan/CreateNotaDinasPembelian.vue` - Form Nota Dinas Pembelian
- `StaffPerencanaan/UploadDokumen.vue` - Upload scan berkas
- `StaffPerencanaan/CreateDPP.vue` - Form DPP
- `StaffPerencanaan/CreateHPS.vue` - Form HPS
- `StaffPerencanaan/CreateDisposisi.vue` - Form disposisi

**Actions:**
- **Create Perencanaan** - Input data perencanaan
- **Create Nota Dinas Usulan** - Buat nota dinas ke Direktur
- **Create Nota Dinas Pembelian** - Buat nota dinas pembelian
- **Upload Scan Berkas** - Upload dokumen scan
- **Create DPP** - Buat dokumen persiapan pengadaan
- **Create HPS** - Buat harga perkiraan satuan
- **Create Disposisi** - Teruskan ke KSO atau Pengadaan

**Filter Data:**
- Permintaan dengan status "disetujui_direktur" atau "disetujui_wadir"
- Permintaan yang di-disposisi ke Staff Perencanaan

---

### 7. KSO (Komite Standar Operasional)

**Dashboard:** `/kso/dashboard`

**CRUD Capabilities:**
- ✅ **Create** - Buat data KSO
- ✅ **Read** - Lihat permintaan yang di-forward ke KSO
- ✅ **Update** - Edit data KSO
- ✅ **Delete** - Hapus data KSO

**Routes:**
```php
GET     /kso/dashboard
GET     /kso/                           → index
GET     /kso/permintaan/{id}            → show
GET     /kso/permintaan/{id}/create     → create KSO
POST    /kso/permintaan/{id}            → store
GET     /kso/permintaan/{id}/kso/{kso_id}/edit → edit
PUT     /kso/permintaan/{id}/kso/{kso_id} → update
DELETE  /kso/permintaan/{id}/kso/{kso_id} → destroy
```

**Views:**
- `KSO/Dashboard.vue` - Dashboard
- `KSO/Index.vue` - List permintaan
- `KSO/Show.vue` - Detail permintaan
- `KSO/Create.vue` - Form create KSO
- `KSO/Edit.vue` - Form edit KSO

**Actions:**
- **Create KSO** - Input data komite
- **Edit KSO** - Update data
- **Delete KSO** - Hapus data
- **Complete** - Tandai selesai → Forward ke Pengadaan

**Filter Data:**
- Permintaan yang di-disposisi ke KSO
- Status "perencanaan_selesai"

---

### 8. PENGADAAN

**Dashboard:** `/pengadaan/dashboard`

**CRUD Capabilities:**
- ✅ **Create** - Buat data pengadaan
- ✅ **Read** - Lihat permintaan yang siap proses pengadaan
- ❌ **Update** - (Belum implementasi lengkap)
- ❌ **Delete** - (Belum implementasi lengkap)

**Routes:**
```php
GET     /pengadaan/dashboard
GET     /pengadaan/                     → index
GET     /pengadaan/permintaan/{id}      → show
GET     /pengadaan/permintaan/{id}/create
POST    /pengadaan/permintaan/{id}      → store
GET     /pengadaan/permintaan/{id}/pengadaan/{pengadaan_id}/edit
PUT     /pengadaan/permintaan/{id}/pengadaan/{pengadaan_id}
DELETE  /pengadaan/permintaan/{id}/pengadaan/{pengadaan_id}
```

**Views:**
- `Pengadaan/Dashboard.vue` - Dashboard
- `Pengadaan/Index.vue` - List permintaan

**Actions:**
- **Create Pengadaan** - Input data pengadaan
- **Complete** - Tandai selesai → Forward ke Serah Terima

**Filter Data:**
- Permintaan dengan status "kso_selesai" atau "perencanaan_selesai"

---

### 9. SERAH TERIMA

**Dashboard:** `/serah-terima/dashboard`

**CRUD Capabilities:**
- ✅ **Create** - Buat nota penerimaan & serah terima
- ✅ **Read** - Lihat data pengadaan yang selesai
- ✅ **Update** - Edit nota penerimaan
- ❌ **Delete** - Tidak bisa delete

**Routes:**
```php
GET     /serah-terima/dashboard
GET     /serah-terima/                  → index
GET     /serah-terima/penerimaan/{id}   → show

# Nota Penerimaan
GET     /serah-terima/pengadaan/{id}/penerimaan/create
POST    /serah-terima/pengadaan/{id}/penerimaan
GET     /serah-terima/penerimaan/{id}/edit
PUT     /serah-terima/penerimaan/{id}

# Serah Terima
GET     /serah-terima/penerimaan/{id}/serah-terima/create
POST    /serah-terima/penerimaan/{id}/serah-terima
GET     /serah-terima/serah-terima/{id}/edit
PUT     /serah-terima/serah-terima/{id}
```

**Views:**
- `SerahTerima/Dashboard.vue` - Dashboard
- `SerahTerima/Index.vue` - List pengadaan selesai

**Actions:**
- **Create Nota Penerimaan** - Buat nota penerimaan barang
- **Edit Penerimaan** - Update nota
- **Create Serah Terima** - Serah terima ke Kepala Instalasi
- **Complete** - Status: "selesai"

**Filter Data:**
- Pengadaan dengan status "pengadaan_selesai"

---

## 📊 STATUS WORKFLOW

| Status | Deskripsi | Role Terkait |
|--------|-----------|--------------|
| `diajukan` | Permintaan baru dibuat | Kepala Instalasi |
| `disetujui_instalasi` | Disetujui Kepala Instalasi | Kepala Bidang |
| `disetujui_kabid` | Disetujui Kepala Bidang | Direktur/Wadir |
| `disetujui_direktur` | Disetujui Direktur | Staff Perencanaan |
| `disetujui_wadir` | Disetujui Wakil Direktur | Staff Perencanaan |
| `perencanaan_selesai` | Perencanaan selesai | KSO/Pengadaan |
| `kso_selesai` | KSO selesai | Pengadaan |
| `pengadaan_selesai` | Pengadaan selesai | Serah Terima |
| `selesai` | Serah terima selesai | - |
| `ditolak` | Permintaan ditolak | Admin (delete) |
| `revisi` | Diminta revisi | Role sebelumnya |

---

## 🔐 MIDDLEWARE & AUTHORIZATION

### Middleware yang Digunakan:
1. `auth` - User harus login
2. `verified` - Email harus verified
3. `redirect.role` - Redirect ke dashboard sesuai role

### Authorization di Controller:
- Setiap controller memeriksa role user
- Filter data berdasarkan:
  - Role user
  - Unit kerja (untuk Kepala Instalasi)
  - Disposisi tujuan (untuk Direktur/Wadir)
  - Status permintaan

---

## 📁 STRUKTUR MODEL

### Tabel Utama:
1. **users** - Data user dengan role
2. **permintaan** - Data permintaan pengadaan
3. **nota_dinas** - Nota dinas (usulan & pembelian)
4. **disposisi** - Data disposisi
5. **perencanaan** - Data perencanaan
6. **dokumen_pengadaan** - Scan berkas & dokumen
7. **hps** - Harga Perkiraan Satuan
8. **hps_items** - Detail item HPS
9. **kso** - Data KSO
10. **pengadaan** - Data pengadaan
11. **nota_penerimaan** - Nota penerimaan barang
12. **serah_terima** - Data serah terima

### Relasi Penting:
```
permintaan (1) → (N) nota_dinas
permintaan (1) → (N) dokumen_pengadaan
permintaan (1) → (1) perencanaan
permintaan (1) → (1) hps
hps (1) → (N) hps_items
permintaan (1) → (N) kso
permintaan (1) → (N) pengadaan
pengadaan (1) → (1) nota_penerimaan
nota_penerimaan (1) → (1) serah_terima
```

---

## ✅ CHECKLIST FITUR LENGKAP

### Admin
- [x] CRUD Permintaan
- [x] View tracking
- [x] Cetak nota dinas
- [x] Lihat lampiran
- [x] Delete permintaan ditolak

### Kepala Instalasi
- [x] Create permintaan
- [x] View permintaan unit
- [x] Approve/Reject/Revisi
- [x] Resubmit permintaan ditolak
- [x] Cetak nota dinas
- [x] Lihat lampiran

### Kepala Bidang
- [x] View permintaan
- [x] Create disposisi
- [x] Approve/Reject/Revisi
- [x] Tracking

### Wakil Direktur
- [x] View permintaan
- [x] Approve/Reject/Revisi
- [x] Create disposisi ke Staff Perencanaan

### Direktur
- [x] View permintaan
- [x] Approve/Reject/Revisi
- [x] Create disposisi ke Staff Perencanaan
- [x] Riwayat keputusan

### Staff Perencanaan
- [x] View permintaan approved
- [x] Create perencanaan
- [x] Create Nota Dinas Usulan
- [x] Create Nota Dinas Pembelian
- [x] Upload scan berkas
- [x] Create DPP
- [x] Create HPS
- [x] Create disposisi ke KSO/Pengadaan

### KSO
- [x] View permintaan
- [x] CRUD data KSO
- [x] Forward ke Pengadaan

### Pengadaan
- [x] View permintaan
- [x] Create pengadaan
- [x] Forward ke Serah Terima

### Serah Terima
- [x] View pengadaan selesai
- [x] Create nota penerimaan
- [x] Create serah terima
- [x] Complete workflow

---

## 🚀 CARA MENGGUNAKAN SISTEM

### 1. Login sebagai Kepala Instalasi
```
1. Login ke sistem
2. Klik "Buat Permintaan"
3. Isi form permintaan
4. Submit
5. Review permintaan
6. Klik "Setujui" untuk forward ke Kepala Bidang
```

### 2. Kepala Bidang Review
```
1. Login sebagai Kepala Bidang
2. Lihat permintaan pending di dashboard
3. Klik detail permintaan
4. Review data
5. Buat disposisi ke Direktur/Wadir/Staff Perencanaan
6. Setujui permintaan
```

### 3. Direktur/Wadir Approve
```
1. Login sebagai Direktur/Wadir
2. Lihat permintaan yang di-disposisi
3. Review permintaan
4. Pilih: Setujui / Tolak / Revisi
5. Jika setujui, buat disposisi ke Staff Perencanaan
```

### 4. Staff Perencanaan Proses
```
1. Login sebagai Staff Perencanaan
2. Lihat permintaan approved
3. Buat dokumen perencanaan:
   - Nota Dinas Usulan
   - Nota Dinas Pembelian
   - Upload Scan Berkas
   - DPP
   - HPS
4. Forward ke KSO atau langsung ke Pengadaan
```

### 5. KSO & Pengadaan
```
1. Login sebagai KSO/Pengadaan
2. Proses permintaan
3. Input data
4. Forward ke tahap selanjutnya
```

### 6. Serah Terima
```
1. Login sebagai Serah Terima
2. Buat nota penerimaan
3. Buat serah terima ke Kepala Instalasi
4. Complete - Status: selesai
```

---

## 📝 CATATAN PENTING

1. **Role-Based Access Control (RBAC)** - Setiap role hanya bisa akses fitur sesuai kewenangannya
2. **Unit-Based Filtering** - Kepala Instalasi hanya lihat data unit-nya
3. **Status-Based Workflow** - Setiap tahap memiliki status spesifik
4. **Disposisi System** - Routing dokumen menggunakan sistem disposisi
5. **Audit Trail** - Semua aksi tercatat di timeline tracking
6. **Document Management** - Nota dinas dan dokumen dapat dicetak dan diunduh

---

**Dokumen ini dibuat pada:** 2025-10-26
**Versi:** 1.0.0
