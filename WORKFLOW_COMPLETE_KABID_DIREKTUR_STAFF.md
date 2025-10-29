# WORKFLOW COMPLETE - KABID TO DIREKTUR TO KABID TO STAFF PERENCANAAN

## ✅ UPDATED & FIXED

### Perubahan Penting:
1. ✅ Kabid_tujuan permintaan #84 diupdate: "Bagian Umum" → "Bidang Umum & Keuangan"
2. ✅ Syntax error KepalaBidangController fixed
3. ✅ Workflow lengkap: Kabid → Direktur → Kabid → Staff Perencanaan

---

## WORKFLOW LENGKAP

### Skenario 1: Pertama Kali (Kepala Instalasi → Kabid → Direktur)

```
1. Admin/Kepala Instalasi
   - Buat permintaan dengan klasifikasi "Non Medis"
   - Status: diajukan
   ↓
2. Kepala Instalasi
   - Approve permintaan
   - System set: kabid_tujuan = "Bidang Umum & Keuangan"
   - Buat Nota Dinas ke Kabid
   - Status: proses
   - PIC: Kabid Umum
   ↓
3. Kabid Umum (kabid.umum@rsud.id)
   - Login dan lihat permintaan di dashboard
   - Review permintaan
   - Pilihan:
     a) APPROVE → Lanjut ke step 4
     b) REVISI → Kembali ke Admin
     c) TOLAK → Kembali ke Admin
   ↓
4. Kabid Umum - APPROVE (Skenario 1)
   - Klik "Approve"
   - System create Disposisi:
     * jabatan_tujuan: "Direktur"
     * catatan: "Disetujui oleh Kepala Bidang, diteruskan ke Direktur"
     * status: disetujui
   - Update Permintaan:
     * status: proses
     * pic_pimpinan: "Direktur"
   ↓
5. Direktur
   - Terima permintaan
   - Review dan pilihan:
     a) APPROVE → Lanjut ke Skenario 2
     b) DISPOSISI BALIK KE KABID → Lanjut ke Skenario 2
```

### Skenario 2: Disposisi Balik (Direktur → Kabid → Staff Perencanaan)

```
1. Direktur
   - Approve permintaan yang sudah direview
   - Disposisi BALIK ke "Kepala Bidang"
   - System create Disposisi:
     * jabatan_tujuan: "Kepala Bidang"
     * catatan: "Disetujui oleh Direktur, mohon review lanjutan"
     * status: selesai
   ↓
2. Kabid Umum (kabid.umum@rsud.id) - TERIMA DISPOSISI BALIK
   - Login dan lihat permintaan yang sudah di-approve Direktur
   - Review final
   - Klik "Approve" lagi
   ↓
3. Kabid Umum - APPROVE (Skenario 2)
   - System deteksi: Ada disposisi dari Direktur
   - System create Disposisi:
     * jabatan_tujuan: "Staff Perencanaan"
     * catatan: "Sudah disetujui Direktur. Mohon lakukan perencanaan pengadaan."
     * status: disetujui
   - Update Permintaan:
     * status: disetujui
     * pic_pimpinan: "Staff Perencanaan"
   ↓
4. Staff Perencanaan
   - Terima permintaan yang sudah fully approved
   - Upload dokumen dan lanjutkan proses pengadaan
```

---

## LOGIC DETECTION - Skenario 1 vs Skenario 2

Di KepalaBidangController@approve(), system mengecek:

```php
// Cek disposisi balik dari Direktur
$disposisiDariDirektur = Disposisi::where('nota_id', $notaDinas->nota_id)
    ->where('jabatan_tujuan', 'Kepala Bidang')
    ->where(function($q) {
        $q->where('catatan', 'like', '%Disetujui oleh Direktur%')
          ->orWhere('status', 'selesai');
    })
    ->exists();

if ($disposisiDariDirektur) {
    // SKENARIO 2: Teruskan ke Staff Perencanaan
} else {
    // SKENARIO 1: Teruskan ke Direktur
}
```

---

## FILTER PERMINTAAN DI KABID

### Query Filter berdasarkan Klasifikasi & Unit Kerja:

```php
// Di KepalaBidangController
$klasifikasiArray = [
    'Bidang Umum & Keuangan' => ['Non Medis', 'non_medis']
];

$permintaans = Permintaan::with(['user', 'notaDinas.disposisi'])
    ->where(function($q) use ($user, $klasifikasiArray) {
        // Filter by klasifikasi
        if ($klasifikasiArray) {
            $q->whereIn('klasifikasi_permintaan', $klasifikasiArray);
        }
        // Filter by kabid_tujuan
        $q->orWhere('kabid_tujuan', 'LIKE', '%' . $user->unit_kerja . '%');
    })
    ->whereIn('status', ['proses', 'disetujui', 'diajukan'])
    ->get();
```

---

## TESTING WORKFLOW

### Test Complete Flow:

```bash
# 1. Login sebagai Kepala Instalasi
# 2. Buat permintaan:
   - Klasifikasi: Non Medis
   - Bidang: Laundry & Linen (atau unit lain)

# 3. Approve permintaan
   - System set kabid_tujuan = "Bidang Umum & Keuangan"

# 4. Login sebagai kabid.umum@rsud.id
   - Password: password
   - Dashboard harus muncul permintaan Non Medis
   - Klik detail permintaan

# 5. Kabid Approve (Pertama kali)
   - Klik "Approve"
   - Permintaan diteruskan ke Direktur
   - Lihat pesan: "Permintaan disetujui dan diteruskan ke Direktur"

# 6. Login sebagai Direktur
   - Lihat permintaan yang masuk
   - Approve dan disposisi balik ke "Kepala Bidang"

# 7. Login lagi sebagai kabid.umum@rsud.id
   - Lihat permintaan yang sudah di-approve Direktur
   - Klik "Approve" lagi

# 8. Kabid Approve (Kedua kali)
   - System deteksi disposisi dari Direktur
   - Permintaan diteruskan ke Staff Perencanaan
   - Lihat pesan: "Permintaan diteruskan ke Staff Perencanaan"

# 9. Login sebagai Staff Perencanaan
   - Lihat permintaan fully approved
   - Upload dokumen dan proses pengadaan
```

---

## DATABASE CHECK

```sql
-- 1. Cek permintaan #84
SELECT permintaan_id, bidang, klasifikasi_permintaan, 
       kabid_tujuan, status, pic_pimpinan
FROM permintaan 
WHERE permintaan_id = 84;

-- Expected:
-- klasifikasi_permintaan: Non Medis
-- kabid_tujuan: Bidang Umum & Keuangan

-- 2. Cek disposisi workflow
SELECT d.disposisi_id, d.jabatan_tujuan, d.catatan, 
       d.status, d.tanggal_disposisi
FROM disposisi d
JOIN nota_dinas nd ON d.nota_id = nd.nota_id
WHERE nd.permintaan_id = 84
ORDER BY d.tanggal_disposisi ASC;

-- Expected flow:
-- 1. jabatan_tujuan: Direktur (dari Kabid approve pertama)
-- 2. jabatan_tujuan: Kepala Bidang (disposisi balik dari Direktur)
-- 3. jabatan_tujuan: Staff Perencanaan (dari Kabid approve kedua)
```

---

## ROUTES YANG DIGUNAKAN

```
# Kabid Dashboard
GET  /kepala-bidang/dashboard

# List Permintaan
GET  /kepala-bidang/index

# Detail Permintaan
GET  /kepala-bidang/permintaan/84

# Approve (POST)
POST /kepala-bidang/permintaan/84/approve

# Reject (POST)
POST /kepala-bidang/permintaan/84/reject

# Revisi (POST)
POST /kepala-bidang/permintaan/84/revisi
```

---

## STATUS TRACKING

| Step | PIC | Status | Keterangan |
|------|-----|--------|------------|
| 1 | Admin | diajukan | Permintaan dibuat |
| 2 | Kepala Instalasi | proses | Menunggu approve |
| 3 | Kabid Umum | proses | Review Kabid |
| 4 | Direktur | proses | Review Direktur |
| 5 | Kabid Umum | proses | Review final Kabid |
| 6 | Staff Perencanaan | disetujui | Fully approved, siap pengadaan |

---

## IMPORTANT NOTES

1. ✅ **Kabid_tujuan harus sesuai unit_kerja** user Kabid ("Bidang Umum & Keuangan")
2. ✅ **Disposisi balik** dari Direktur memicu Skenario 2 (langsung ke Staff Perencanaan)
3. ✅ **Filter klasifikasi** support format lama & baru (medis/Medis, non_medis/Non Medis)
4. ✅ **Workflow fleksibel**: Direktur bisa disposisi balik atau langsung approve
5. ✅ **REVISI/TOLAK** dari Kabid akan kembalikan ke Admin untuk perbaikan/delete

---

## NEXT STEPS

Jika ada permintaan baru:
1. Buat permintaan dengan klasifikasi yang benar
2. System akan auto-route ke Kabid yang sesuai
3. Follow workflow di atas
4. Monitor progress via tracking timeline
