# 🚀 Quick Guide - Workflow Approval 2 Tingkat

**Admin → Kepala Instalasi → Kepala Bidang → Selesai**

---

## ⚡ Ringkasan Cepat

### Workflow Baru:
```
1. Admin membuat permintaan
   ↓
2. Kepala Instalasi review
   ├─ Approve → ke Kepala Bidang
   └─ Reject → kembali ke Admin
       ↓
3. Kepala Bidang review
   ├─ Approve → ke Bagian terkait
   └─ Reject → kembali ke Admin
       ↓
4. Proses lanjut (Perencanaan, Pengadaan, dst)
```

---

## 👥 Akun Testing

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| Kepala Farmasi | kepala_instalasi@rsud.id | password123 | 5 permintaan Farmasi |
| Kepala IGD | kepala_igd@rsud.id | password123 | 1 permintaan IGD |
| **Kepala Bidang** | **kepala_bidang@rsud.id** | **password123** | **Semua yang approved KI** |

---

## 🎯 Skenario Testing

### Scenario 1: Happy Path
```
1. Login: kepala_instalasi@rsud.id
   → Lihat permintaan #1 (Diajukan)
   → Klik "Approve"
   → Status: Proses (ke Kepala Bidang)

2. Login: kepala_bidang@rsud.id
   → Dashboard: Ada permintaan #1 & #3
   → Klik permintaan #1
   → Klik "Approve"
   → Pilih tujuan: "Bagian Perencanaan"
   → Status: Disetujui

3. Tracking:
   → Progress: 37.5% (3/8 tahap)
   → Timeline: Permintaan → Nota Dinas → Disposisi
```

### Scenario 2: Reject di Kepala Instalasi
```
1. Login: kepala_instalasi@rsud.id
   → Pilih permintaan #2
   → Klik "Reject"
   → Alasan: "Stok masih ada"
   → Status: Ditolak
   → Kembali ke Admin
```

### Scenario 3: Reject di Kepala Bidang
```
1. Login: kepala_bidang@rsud.id
   → Pilih permintaan #3
   → Klik "Reject"
   → Alasan: "Melebihi anggaran"
   → Status: Ditolak
   → Kembali ke Admin
```

---

## 🔑 Key Features

### Kepala Instalasi:
- ✅ Filter otomatis per bagian
- ✅ Approve → teruskan ke Kepala Bidang
- ✅ Reject → kembali ke pemohon
- ✅ Tracking status

### Kepala Bidang (NEW!):
- ✅ Lihat semua yang sudah approved KI
- ✅ Approve → teruskan ke bagian terkait
- ✅ Reject → kembali ke pemohon
- ✅ Buat disposisi manual
- ✅ Tracking status

---

## 📊 Status Permintaan

| Status | Artinya | PIC |
|--------|---------|-----|
| `diajukan` | Baru dari admin | - |
| `proses` | Di Kepala Instalasi/Bidang | Pejabat |
| `disetujui` | Sudah approved semua | Bagian terkait |
| `ditolak` | Rejected | - |
| `revisi` | Perlu perbaikan | Admin |

---

## 🔗 URLs

### Kepala Instalasi
- Dashboard: `/kepala-instalasi/dashboard`
- List: `/kepala-instalasi`
- Detail: `/kepala-instalasi/permintaan/{id}`

### Kepala Bidang (NEW!)
- Dashboard: `/kepala-bidang/dashboard`
- List: `/kepala-bidang`
- Detail: `/kepala-bidang/permintaan/{id}`

---

## 💡 Tips

**Untuk Kepala Instalasi:**
- Hanya lihat permintaan bagian sendiri
- Setelah approve, permintaan pindah ke Kepala Bidang
- Bisa reject kapan saja

**Untuk Kepala Bidang:**
- Lihat semua permintaan dari semua bagian
- Filter: `pic_pimpinan = 'Kepala Bidang'`
- Tentukan tujuan saat approve (Perencanaan/Pengadaan)

---

## 📈 Progress Tracking

**Setelah Kepala Instalasi Approve:**
- Progress: 25% (2/8 tahap)
- Timeline: Permintaan → Nota Dinas

**Setelah Kepala Bidang Approve:**
- Progress: 37.5% (3/8 tahap)
- Timeline: Permintaan → Nota Dinas → Disposisi

---

## 🐛 Troubleshooting

**Q: Kepala Instalasi tidak lihat permintaan?**  
A: Cek `bidang` permintaan harus sama dengan `unit_kerja` user

**Q: Kepala Bidang tidak lihat permintaan?**  
A: Permintaan harus sudah di-approve dulu oleh Kepala Instalasi

**Q: Error 403 saat approve?**  
A: Cek otorisasi, mungkin bukan PIC permintaan tersebut

---

## ✅ Checklist Testing

- [ ] Login Kepala Instalasi Farmasi
- [ ] Approve permintaan #1
- [ ] Cek status jadi "proses"
- [ ] Cek `pic_pimpinan` = "Kepala Bidang"
- [ ] Login Kepala Bidang
- [ ] Dashboard ada permintaan #1 & #3
- [ ] Approve permintaan #3
- [ ] Cek status jadi "disetujui"
- [ ] View tracking: 3 tahap selesai
- [ ] Test reject di kedua level

---

**Version:** 1.3.0  
**Workflow:** 2-Level Approval  
**Status:** ✅ Ready to Test
