# 📊 DIAGRAM WORKFLOW SISTEM PENGADAAN RSUD

## 🎯 OVERVIEW ALUR LENGKAP

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    SISTEM PENGADAAN BARANG & JASA RSUD                       │
│                                                                               │
│  Kepala Instalasi → Kepala Bidang (approve 1) → Direktur                    │
│           → Kepala Bidang (approve 2) → Staff Perencanaan                   │
│                  → Pengadaan → KSO → Selesai                                │
└─────────────────────────────────────────────────────────────────────────────┘

PENTING: Kepala Bidang APPROVE 2 KALI:
1. Approve pertama → Forward ke Direktur
2. Setelah Direktur approve → Disposisi balik ke Kabid → Kabid approve kedua → Forward ke Staff Perencanaan
```

---

## 📋 FASE 1: PENGAJUAN & KLASIFIKASI

```
┌──────────────────────────┐
│  ADMIN / KEPALA UNIT     │
│                          │
│  1. Buat Permintaan      │
│  2. Isi Detail:          │
│     - Bidang/Unit        │
│     - Deskripsi          │
│     - Klasifikasi ✨     │
└──────────┬───────────────┘
           │
           │ Submit
           ▼
┌──────────────────────────┐
│   SISTEM KLASIFIKASI     │
│                          │
│  ┌────────────────────┐  │
│  │ MEDIS              │──┼──→ Bidang Pelayanan Medis
│  │ (Alat medis, obat) │  │
│  └────────────────────┘  │
│                          │
│  ┌────────────────────┐  │
│  │ PENUNJANG MEDIS    │──┼──→ Bidang Penunjang Medis
│  │ (Lab, Radiologi)   │  │
│  └────────────────────┘  │
│                          │
│  ┌────────────────────┐  │
│  │ NON MEDIS          │──┼──→ Bidang Umum & Keuangan
│  │ (IT, Gizi, Linen)  │  │
│  └────────────────────┘  │
└──────────┬───────────────┘
           │
           │ Auto Route
           ▼
┌──────────────────────────┐
│  KEPALA INSTALASI        │
│                          │
│  Status: diajukan        │
│  Action: Approve/Reject  │
└──────────┬───────────────┘
           │
           │ Approve
           ▼
     [FASE 2: APPROVAL]
```

---

## ✅ FASE 2: APPROVAL KEPALA BIDANG & DIREKTUR

### 🔄 SKENARIO 1: First Approval (Kepala Instalasi → Kabid → Direktur)

```
┌──────────────────────────────────────────────────────────────────────┐
│                        KEPALA INSTALASI                               │
│                                                                       │
│  ✓ Approve permintaan                                                │
│  ✓ Sistem set kabid_tujuan (otomatis berdasarkan klasifikasi)       │
│  ✓ Buat Nota Dinas ke Kabid                                          │
└─────────────────────────┬────────────────────────────────────────────┘
                          │
                          │ Status: proses
                          │ PIC: Kepala Bidang
                          ▼
┌──────────────────────────────────────────────────────────────────────┐
│                     KEPALA BIDANG (Sesuai Klasifikasi)               │
│                                                                       │
│  Kabid Yanmed (MEDIS) / Kabid Penunjang (PENUNJANG MEDIS) /         │
│  Kabid Umum (NON MEDIS)                                              │
│                                                                       │
│  Pilihan:                                                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                           │
│  │ APPROVE  │  │  REVISI  │  │  TOLAK   │                           │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘                           │
│       │             │             │                                  │
│       │             ▼             ▼                                  │
│       │        Kembali ke     Kembali ke                             │
│       │          Admin          Admin                                │
│       │                                                               │
└───────┼───────────────────────────────────────────────────────────────┘
        │
        │ APPROVE - First Time
        ▼
┌──────────────────────────────────────────────────────────────────────┐
│                          DIREKTUR                                     │
│                                                                       │
│  Status: proses                                                       │
│  PIC: Direktur                                                        │
│                                                                       │
│  Pilihan:                                                             │
│  ┌────────────────────────┐  ┌──────────────────────────┐            │
│  │ APPROVE & DISPOSISI    │  │  TOLAK / REVISI          │            │
│  │ BALIK KE KABID         │  │                          │            │
│  └───────────┬────────────┘  └────────────┬─────────────┘            │
└──────────────┼──────────────────────────────┼────────────────────────┘
               │                              │
               │                              ▼
               │                        Kembali ke
               │                        Kepala Bidang
               ▼
         [FASE 2 - SKENARIO 2]
```

### 🔄 SKENARIO 2: Disposisi Balik (Direktur → Kabid → Staff Perencanaan)

```
┌──────────────────────────────────────────────────────────────────────┐
│                          DIREKTUR                                     │
│                                                                       │
│  ✓ Review dan Approve permintaan (Final Approval)                   │
│  ✓ Buat Disposisi ke "Kepala Bidang"                                │
│  ✓ Catatan: "Disetujui oleh Direktur (Final Approval)"              │
│  ✓ Status disposisi: selesai                                         │
│                                                                       │
│  Update Permintaan:                                                   │
│  • status: proses                                                    │
│  • pic_pimpinan: "Kepala Bidang"                                     │
│  • kabid_tujuan: [sesuai klasifikasi]                               │
└─────────────────────────┬────────────────────────────────────────────┘
                          │
                          │ Disposisi balik ke Kabid
                          ▼
┌──────────────────────────────────────────────────────────────────────┐
│                   KEPALA BIDANG (Terima Disposisi Balik)             │
│                                                                       │
│  Status: proses                                                       │
│  PIC: Kepala Bidang                                                   │
│                                                                       │
│  ✓ Sistem deteksi OTOMATIS: Ada disposisi dari Direktur?            │
│    - Cek: catatan contains "Disetujui oleh Direktur"                │
│    - Cek: status = "selesai"                                         │
│                                                                       │
│  ✓ Kabid login dan lihat permintaan yang sudah di-approve Direktur  │
│  ✓ Review final                                                      │
│                                                                       │
│  ┌──────────────────────┐                                            │
│  │ APPROVE (Kedua Kali) │  ← Sistem otomatis route ke Staff         │
│  └──────────┬───────────┘                                            │
└─────────────┼────────────────────────────────────────────────────────┘
              │
              │ Sistem buat Disposisi:
              │ • jabatan_tujuan: "Staff Perencanaan"
              │ • catatan: "Sudah disetujui Direktur..."
              │ • status: disetujui
              │
              │ Update Permintaan:
              │ • status: disetujui
              │ • pic_pimpinan: "Staff Perencanaan"
              ▼
        [FASE 3: PERENCANAAN]
```

---

## 📝 FASE 3: PERENCANAAN & DOKUMENTASI

```
┌──────────────────────────────────────────────────────────────────────┐
│                      STAFF PERENCANAAN                                │
│                                                                       │
│  Buat Dokumen Lengkap (5 Dokumen Wajib):                             │
│                                                                       │
│  ┌────────────────────────────────────────────────────────────┐      │
│  │  ☐ 1. Nota Dinas Perencanaan                              │      │
│  │  ☐ 2. DPP (Dokumen Persiapan Pengadaan)                   │      │
│  │  ☐ 3. HPS (Harga Perkiraan Satuan)                        │      │
│  │  ☐ 4. Nota Dinas Pembelian                                │      │
│  │  ☐ 5. Spesifikasi Teknis                                  │      │
│  └────────────────────────────────────────────────────────────┘      │
│                                                                       │
│  ┌──────────────────────────────────────────────────────────┐        │
│  │  ⚠️  DOKUMEN BELUM LENGKAP                               │        │
│  │                                                           │        │
│  │  Silakan lengkapi dokumen berikut:                       │        │
│  │  • Nota Dinas                                             │        │
│  │  • DPP                                                    │        │
│  │                                                           │        │
│  │  ❌ Tombol "Kirim" TIDAK MUNCUL                          │        │
│  └──────────────────────────────────────────────────────────┘        │
│                                 OR                                    │
│  ┌──────────────────────────────────────────────────────────┐        │
│  │  ✅ SEMUA DOKUMEN SUDAH LENGKAP!                         │        │
│  │                                                           │        │
│  │  ✓ Nota Dinas                                            │        │
│  │  ✓ DPP                                                   │        │
│  │  ✓ HPS                                                   │        │
│  │  ✓ Nota Dinas Pembelian                                 │        │
│  │  ✓ Spesifikasi Teknis                                   │        │
│  │                                                           │        │
│  │                    [KIRIM KE PENGADAAN →]                │        │
│  └──────────────────────────────────────────────────────────┘        │
└─────────────────────────┬────────────────────────────────────────────┘
                          │
                          │ VALIDASI OTOMATIS ✅
                          │ Semua dokumen lengkap
                          ▼
                  [FASE 4: PENGADAAN]
```

---

## 🛒 FASE 4: BAGIAN PENGADAAN

```
┌──────────────────────────────────────────────────────────────────────┐
│                       BAGIAN PENGADAAN                                │
│                                                                       │
│  Status: disetujui                                                    │
│  PIC: Bagian Pengadaan                                                │
│                                                                       │
│  ✓ Terima permintaan dengan semua dokumen lengkap:                   │
│    • Nota Dinas Perencanaan                                           │
│    • DPP                                                              │
│    • HPS                                                              │
│    • Nota Dinas Pembelian                                             │
│    • Spesifikasi Teknis                                               │
│                                                                       │
│  ✓ Review dan verifikasi dokumen                                     │
│  ✓ Proses pengadaan sesuai prosedur                                  │
│                                                                       │
│  ┌──────────────────────────────────┐                                │
│  │  [FORWARD KE BAGIAN KSO →]       │                                │
│  └──────────────┬───────────────────┘                                │
└─────────────────┼────────────────────────────────────────────────────┘
                  │
                  │ Kirim ke KSO
                  │ PIC: Bagian KSO
                  ▼
            [FASE 5: KSO & SELESAI]
```

---

## 🏁 FASE 5: BAGIAN KSO & PENYELESAIAN

```
┌──────────────────────────────────────────────────────────────────────┐
│                         BAGIAN KSO                                    │
│                                                                       │
│  Status: proses                                                       │
│  PIC: Bagian KSO                                                      │
│                                                                       │
│  ✓ Terima dari Bagian Pengadaan                                      │
│  ✓ Proses kontrak dan kerjasama                                      │
│  ✓ Finalisasi pengadaan                                              │
│                                                                       │
│  ┌──────────────────────────────────┐                                │
│  │    [SELESAI & ARSIP]             │                                │
│  └──────────────────────────────────┘                                │
│                                                                       │
│  Status: selesai                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

---

## 🔀 DECISION TREE - ROUTING BERDASARKAN KLASIFIKASI

```
                        ┌─────────────────┐
                        │  PERMINTAAN     │
                        │  DIBUAT         │
                        └────────┬────────┘
                                 │
                    ┌────────────┼────────────┐
                    │                         │
          ┌─────────▼─────────┐    ┌─────────▼──────────┐
          │  Klasifikasi?     │    │  Unit Asal?        │
          └─────────┬─────────┘    └─────────┬──────────┘
                    │                        │
        ┌───────────┼───────────┐            │
        │           │           │            │
┌───────▼───────┐ ┌─▼────────┐ ┌▼─────────┐ │
│    MEDIS      │ │PENUNJANG │ │NON MEDIS │ │
│               │ │  MEDIS   │ │          │ │
└───────┬───────┘ └─┬────────┘ └┬─────────┘ │
        │            │           │           │
        │            │           │           │
┌───────▼───────────────────────────────────▼──────┐
│          AUTO-ROUTING KE KABID TUJUAN            │
└───────┬──────────────────┬──────────────┬────────┘
        │                  │              │
┌───────▼────────┐  ┌──────▼──────┐  ┌───▼──────────┐
│ Kabid Yanmed   │  │Kabid        │  │ Kabid Umum & │
│ (Pel. Medis)   │  │Penunjang    │  │  Keuangan    │
└───────┬────────┘  └──────┬──────┘  └───┬──────────┘
        │                  │              │
        └──────────────────┼──────────────┘
                           │
                  ┌────────▼────────┐
                  │   DIREKTUR      │
                  └────────┬────────┘
                           │
                  ┌────────▼────────┐
                  │ Staff Perencanaan│
                  └─────────────────┘
```

---

## 📊 STATUS & PIC TRACKING

### Status Permintaan Sepanjang Workflow

| Fase | Status | PIC | Deskripsi | Action |
|------|--------|-----|-----------|--------|
| 1 | `diajukan` | Admin/Kepala Unit | Permintaan baru dibuat | Submit |
| 2 | `proses` | Kepala Instalasi | Menunggu approval instalasi | Approve/Reject |
| 3 | `proses` | Kepala Bidang | Review pertama di Kepala Bidang | Approve → ke Direktur |
| 4 | `proses` | Direktur | Review di Direktur (Final Approval) | Approve → disposisi balik ke Kabid |
| 5 | `proses` | Kepala Bidang | Disposisi balik dari Direktur | Approve kedua → ke Staff Perencanaan |
| 6 | `disetujui` | Staff Perencanaan | Fully approved, buat 5 dokumen | Upload dokumen, forward jika lengkap |
| 7 | `disetujui` | Bagian Pengadaan | Proses pengadaan | Forward ke KSO |
| 8 | `proses` | Bagian KSO | Finalisasi kontrak | Selesaikan |
| 9 | `selesai` | - | Pengadaan selesai | Arsip |

---

## 🔐 ROLE & ACCESS MATRIX

```
┌─────────────────────────────────────────────────────────────────────┐
│                       ROLE & PERMISSIONS                             │
├──────────────────────┬──────────────────────────────────────────────┤
│ Admin/Kepala Unit    │ • Buat permintaan                            │
│                      │ • Pilih klasifikasi                          │
├──────────────────────┼──────────────────────────────────────────────┤
│ Kepala Instalasi     │ • Approve/Reject/Revisi permintaan           │
│                      │ • Buat Nota Dinas ke Kabid                   │
│                      │ • Set kabid_tujuan otomatis                  │
├──────────────────────┼──────────────────────────────────────────────┤
│ Kepala Bidang Yanmed │ • Review permintaan MEDIS                    │
│                      │ • Approve 1st → Forward ke Direktur          │
│                      │ • Terima disposisi balik dari Direktur       │
│                      │ • Approve 2nd → Forward ke Staff Perencanaan │
├──────────────────────┼──────────────────────────────────────────────┤
│ Kepala Bidang        │ • Review permintaan PENUNJANG MEDIS          │
│ Penunjang            │ • Approve 1st → Forward ke Direktur          │
│                      │ • Terima disposisi balik dari Direktur       │
│                      │ • Approve 2nd → Forward ke Staff Perencanaan │
├──────────────────────┼──────────────────────────────────────────────┤
│ Kepala Bidang Umum   │ • Review permintaan NON MEDIS                │
│ & Keuangan           │ • Approve 1st → Forward ke Direktur          │
│                      │ • Terima disposisi balik dari Direktur       │
│                      │ • Approve 2nd → Forward ke Staff Perencanaan │
├──────────────────────┼──────────────────────────────────────────────┤
│ Direktur             │ • Review semua permintaan (Final Approval)   │
│                      │ • Approve → Disposisi balik ke Kabid         │
│                      │ • Reject → Kembali ke pemohon                │
│                      │ • Revisi → Kembali ke Kabid                  │
├──────────────────────┼──────────────────────────────────────────────┤
│ Staff Perencanaan    │ • Terima dari Kabid (setelah Direktur)       │
│                      │ • Buat 5 dokumen wajib                       │
│                      │ • Validasi dokumen lengkap (otomatis)        │
│                      │ • Forward ke Pengadaan (jika lengkap)        │
├──────────────────────┼──────────────────────────────────────────────┤
│ Bagian Pengadaan     │ • Terima dari Staff Perencanaan              │
│                      │ • Review dokumen lengkap                     │
│                      │ • Proses pengadaan                           │
│                      │ • Forward ke KSO                             │
├──────────────────────┼──────────────────────────────────────────────┤
│ Bagian KSO           │ • Terima dari Pengadaan                      │
│                      │ • Finalisasi kontrak                         │
│                      │ • Selesaikan pengadaan                       │
└──────────────────────┴──────────────────────────────────────────────┘

CATATAN PENTING:
✨ Kepala Bidang melakukan APPROVE 2 KALI dalam workflow:
   1. APPROVE PERTAMA: Setelah review → Forward ke Direktur
   2. APPROVE KEDUA: Setelah terima disposisi dari Direktur → Forward ke Staff Perencanaan
   
   Sistem otomatis mendeteksi apakah ada disposisi dari Direktur untuk menentukan
   routing yang benar (ke Direktur atau ke Staff Perencanaan).
```

---

## 📧 NOTIFIKASI FLOW

```
Setiap perpindahan PIC:

┌──────────────────┐
│  Permintaan      │
│  di-approve      │
└────────┬─────────┘
         │
         ▼
┌──────────────────────────────┐
│  Sistem create Disposisi     │
│  + Update PIC                │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│  Kirim notifikasi ke         │
│  PIC baru                    │
│  (Email/Dashboard)           │
└──────────────────────────────┘
```

---

## 🎨 LEGEND & SYMBOLS

```
┌──────────────────────────────────────────────┐
│  ✅ Approved / Selesai                       │
│  ❌ Rejected / Gagal                         │
│  ⚠️  Warning / Perhatian                     │
│  📝 Dokumen / Form                           │
│  🔄 Proses / Workflow                        │
│  ✨ Fitur Baru / Penting                     │
│  ☐  Checkbox (belum selesai)                │
│  ✓  Checkbox (selesai)                      │
│  →  Forward / Lanjut                        │
│  ↓  Alur ke bawah                           │
│  ▼  Pilihan / Decision                      │
└──────────────────────────────────────────────┘
```

---

## 🔍 CONTOH REAL CASE

### Case 1: Permintaan Alat Medis IGD (MEDIS)

```
1. Admin IGD
   • Buat permintaan: Defibrillator
   • Klasifikasi: MEDIS
   • Submit
   ↓
2. Kepala Instalasi IGD
   • Review & Approve
   • Sistem set kabid_tujuan: "Bidang Pelayanan Medis"
   • Buat Nota Dinas
   • Status: proses, PIC: Kepala Bidang
   ↓
3. Kabid Pelayanan Medis (kabid.yanmed@rsud.id)
   • Login & lihat permintaan di dashboard
   • Review permintaan
   • Klik "Approve" (Pertama kali)
   • Sistem deteksi: Belum ada disposisi dari Direktur
   • Sistem buat Disposisi:
     - jabatan_tujuan: "Direktur"
     - catatan: "Disetujui oleh Kepala Bidang..."
   • Update: status = proses, PIC = Direktur
   ↓
4. Direktur (direktur@rsud.id)
   • Login & lihat permintaan di dashboard
   • Review & Approve (Final Approval)
   • Sistem buat Disposisi BALIK:
     - jabatan_tujuan: "Kepala Bidang"
     - catatan: "Disetujui oleh Direktur (Final Approval)..."
     - status: selesai
   • Update: status = proses, PIC = Kepala Bidang
   • Update: kabid_tujuan = "Bidang Pelayanan Medis"
   ↓
5. Kabid Pelayanan Medis (kabid.yanmed@rsud.id) - KEMBALI
   • Login lagi & lihat permintaan yang sudah di-approve Direktur
   • Sistem tandai dengan indicator khusus
   • Review final
   • Klik "Approve" (Kedua kali)
   • Sistem deteksi OTOMATIS: Ada disposisi dari Direktur!
   • Sistem buat Disposisi:
     - jabatan_tujuan: "Staff Perencanaan"
     - catatan: "Sudah disetujui Direktur..."
     - status: disetujui
   • Update: status = disetujui, PIC = Staff Perencanaan
   ↓
6. Staff Perencanaan (staff.perencanaan@rsud.id)
   • Login & lihat permintaan fully approved
   • Buat 5 dokumen satu per satu:
     1. Nota Dinas ✓
     2. DPP ✓
     3. HPS ✓
     4. Nota Dinas Pembelian ✓
     5. Spesifikasi Teknis ✓
   • Setelah semua lengkap → Alert hijau muncul
   • Klik "Kirim ke Pengadaan"
   • Sistem validasi semua dokumen
   • Forward ke Bagian Pengadaan
   ↓
7. Bagian Pengadaan (pengadaan@rsud.id)
   • Login & terima permintaan dengan semua dokumen
   • Review dokumen lengkap
   • Proses pengadaan
   • Klik "Forward ke KSO"
   ↓
8. Bagian KSO (kso@rsud.id)
   • Login & terima dari Pengadaan
   • Finalisasi kontrak
   • Selesai
   • Status: selesai
```

### Case 2: Permintaan Reagen Lab (PENUNJANG MEDIS)

```
1. Admin Laboratorium
   • Buat permintaan: Reagen Hematologi
   • Klasifikasi: PENUNJANG MEDIS
   • Submit
   ↓
2. Kepala Instalasi Lab
   • Approve
   • Auto-route ke: Kabid Penunjang Medis
   ↓
3. Kabid Penunjang Medis (kabid.penunjang@rsud.id)
   • Review & Approve
   • Forward ke Direktur
   ↓
4-8. [Flow sama seperti Case 1]
```

### Case 3: Permintaan Bahan Makanan (NON MEDIS)

```
1. Admin Gizi
   • Buat permintaan: Beras, Gula
   • Klasifikasi: NON MEDIS
   • Submit
   ↓
2. Kepala Instalasi Gizi
   • Approve
   • Auto-route ke: Bidang Umum & Keuangan
   ↓
3. Kabid Umum & Keuangan (kabid.umum@rsud.id)
   • Review & Approve
   • Forward ke Direktur
   ↓
4-8. [Flow sama seperti Case 1]
```

---

## ⏱️ ESTIMASI WAKTU PROSES

| Tahap | Estimasi | Keterangan |
|-------|----------|------------|
| Kepala Instalasi | 1-2 hari | Review awal |
| Kepala Bidang (Approve 1st) | 2-3 hari | Review teknis, forward ke Direktur |
| Direktur | 3-5 hari | Final approval, disposisi balik ke Kabid |
| Kepala Bidang (Approve 2nd) | 1 hari | Terima disposisi dari Direktur, forward ke Staff |
| Staff Perencanaan | 5-7 hari | Buat 5 dokumen lengkap |
| Bagian Pengadaan | 7-14 hari | Proses tender |
| Bagian KSO | 5-7 hari | Finalisasi kontrak |
| **TOTAL** | **24-39 hari** | **~1-1.5 bulan** |

---

## 💾 DATABASE SCHEMA SUMMARY

```sql
-- Tabel permintaan
permintaan
├── permintaan_id (PK)
├── bidang (unit pengaju)
├── klasifikasi_permintaan (medis/penunjang_medis/non_medis) ✨
├── kabid_tujuan (auto-set berdasarkan klasifikasi) ✨
├── status (diajukan/proses/disetujui/selesai)
├── pic_pimpinan (current PIC)
└── deskripsi

-- Tabel nota_dinas
nota_dinas
├── nota_id (PK)
├── permintaan_id (FK)
├── no_nota_dinas
└── file_path

-- Tabel disposisi
disposisi
├── disposisi_id (PK)
├── nota_id (FK)
├── jabatan_tujuan (target role)
├── catatan
├── status
└── tanggal_disposisi

-- Tabel users
users
├── id (PK)
├── email
├── role
├── unit_kerja ✨
└── password
```

---

## 🚀 QUICK START GUIDE

### Login Credentials untuk Testing

```bash
# 1. Kepala Bidang Pelayanan Medis (MEDIS)
Email: kabid.yanmed@rsud.id
Password: password

# 2. Kepala Bidang Penunjang Medis (PENUNJANG MEDIS)
Email: kabid.penunjang@rsud.id
Password: password

# 3. Kepala Bidang Umum & Keuangan (NON MEDIS)
Email: kabid.umum@rsud.id
Password: password

# 4. Direktur
Email: direktur@rsud.id
Password: password

# 5. Staff Perencanaan
Email: staff.perencanaan@rsud.id
Password: password

# 6. Bagian Pengadaan
Email: pengadaan@rsud.id
Password: password

# 7. Bagian KSO
Email: kso@rsud.id
Password: password
```

---

## 📱 DASHBOARD INDICATORS

```
┌─────────────────────────────────────────┐
│  DASHBOARD KEPALA BIDANG                │
├─────────────────────────────────────────┤
│                                         │
│  📊 STATISTIK                           │
│  • Total Permintaan: 10                 │
│  • Menunggu Review: 3                   │
│  • Sudah Disetujui: 5                   │
│  • Ditolak: 2                           │
│                                         │
│  📋 PERMINTAAN TERBARU                  │
│  ┌───────────────────────────────────┐  │
│  │ #84 - Alat Emergency IGD          │  │
│  │ Status: proses                    │  │
│  │ Klasifikasi: MEDIS                │  │
│  │ [REVIEW →]                        │  │
│  └───────────────────────────────────┘  │
│                                         │
└─────────────────────────────────────────┘
```

---

## 📚 RELATED DOCUMENTATION

1. `KLASIFIKASI_PERMINTAAN.md` - Detail klasifikasi sistem
2. `WORKFLOW_COMPLETE_KABID_DIREKTUR_STAFF.md` - Workflow approval detail
3. `WORKFLOW_PERENCANAAN_PENGADAAN_KSO.md` - Workflow perencanaan ke KSO
4. `QUICK_REFERENCE_*.md` - Quick reference per role
5. `USER_ACTIVITY_LOGGING_SYSTEM.md` - Logging & audit trail

---

## 🔧 MEKANISME DETEKSI OTOMATIS ROUTING KEPALA BIDANG

### Bagaimana Sistem Menentukan Routing?

Ketika Kepala Bidang klik tombol "Approve", sistem melakukan deteksi otomatis:

```php
// Di KepalaBidangController@approve()

// 1. Cari disposisi dari Direktur
$disposisiDariDirektur = Disposisi::where('nota_id', $notaDinas->nota_id)
    ->where('jabatan_tujuan', 'Kepala Bidang')
    ->where(function($q) {
        $q->where('catatan', 'like', '%Disetujui oleh Direktur%')
          ->orWhere('status', 'selesai');
    })
    ->exists();

// 2. DECISION LOGIC:
if ($disposisiDariDirektur) {
    // ✅ SKENARIO 2: Ada disposisi dari Direktur
    // → Forward ke Staff Perencanaan
    
    Disposisi::create([
        'jabatan_tujuan' => 'Staff Perencanaan',
        'catatan' => 'Sudah disetujui Direktur. Mohon lakukan perencanaan pengadaan.',
        'status' => 'disetujui',
    ]);
    
    $permintaan->update([
        'status' => 'disetujui',
        'pic_pimpinan' => 'Staff Perencanaan',
    ]);
    
} else {
    // ❌ SKENARIO 1: Belum ada disposisi dari Direktur
    // → Forward ke Direktur
    
    Disposisi::create([
        'jabatan_tujuan' => 'Direktur',
        'catatan' => 'Disetujui oleh Kepala Bidang, diteruskan ke Direktur untuk persetujuan final.',
        'status' => 'disetujui',
    ]);
    
    $permintaan->update([
        'status' => 'proses',
        'pic_pimpinan' => 'Direktur',
    ]);
}
```

### Indikator Visual untuk Kepala Bidang

Di halaman Show.vue, Kepala Bidang akan melihat:

**Jika belum ada disposisi dari Direktur:**
```
┌────────────────────────────────────────┐
│  STATUS: Menunggu Approval             │
│  ACTION: [APPROVE] → Ke Direktur       │
└────────────────────────────────────────┘
```

**Jika sudah ada disposisi dari Direktur:**
```
┌────────────────────────────────────────┐
│  ✅ SUDAH DISETUJUI DIREKTUR           │
│  STATUS: Disposisi balik dari Direktur │
│  ACTION: [APPROVE] → Ke Staff          │
└────────────────────────────────────────┘
```

### Direktur Workflow Logic

Di DirekturController@approve():

```php
// Direktur SELALU disposisi balik ke Kepala Bidang

// Buat disposisi ke Kepala Bidang yang sesuai
Disposisi::create([
    'nota_id' => $notaDinas->nota_id,
    'jabatan_tujuan' => $kabidTujuan, // Sesuai klasifikasi
    'tanggal_disposisi' => Carbon::now(),
    'catatan' => 'Disetujui oleh Direktur (Final Approval). ' . 
                'Silakan disposisi ke Staff Perencanaan untuk perencanaan pengadaan.' .
                "\n\nKlasifikasi: " . strtoupper($klasifikasi) .
                "\nDiteruskan ke: " . $kabidTujuan,
    'status' => 'selesai', // ← KEY: Status ini memicu deteksi
]);

// Update permintaan
$permintaan->update([
    'status' => 'proses',
    'pic_pimpinan' => 'Kepala Bidang',
    'kabid_tujuan' => $kabidTujuan,
]);
```

### Key Points Mekanisme Deteksi

1. **Catatan Disposisi**: Sistem cek kata kunci "Disetujui oleh Direktur"
2. **Status Disposisi**: Sistem cek status = "selesai"
3. **Jabatan Tujuan**: Disposisi harus menuju "Kepala Bidang"
4. **Nota ID**: Disposisi harus terkait dengan nota_id yang sama

Jika SEMUA kondisi terpenuhi → Sistem otomatis route ke Staff Perencanaan
Jika TIDAK → Sistem route ke Direktur

---

## 🎯 SUMMARY ALUR LENGKAP

```
ALUR NORMAL (HAPPY PATH):

1. Admin/Kepala Unit → Buat permintaan + pilih klasifikasi
2. Kepala Instalasi → Approve (sistem set kabid_tujuan otomatis)
3. Kepala Bidang → APPROVE 1 (sistem: belum ada disposisi Direktur → route ke Direktur)
4. Direktur → APPROVE (Final) + Disposisi balik ke Kabid
5. Kepala Bidang → APPROVE 2 (sistem: ADA disposisi Direktur → route ke Staff)
6. Staff Perencanaan → Buat 5 dokumen → Forward ke Pengadaan
7. Bagian Pengadaan → Proses → Forward ke KSO
8. Bagian KSO → Finalisasi → Selesai

TOTAL: 8 tahapan, estimasi 24-39 hari (1-1.5 bulan)
```

---

**Dibuat:** 2 November 2025  
**Status:** ✅ COMPLETE  
**Version:** 1.0  
**Dokumentasi Lengkap Workflow Sistem Pengadaan RSUD**
