# WORKFLOW KABID UMUM - NON MEDIS

## Summary Fix
✅ Syntax error di KepalaBidangController.php sudah diperbaiki (line 82 duplicate)
✅ Routing klasifikasi Non Medis sudah benar ke Kabid Umum

## User Kabid Umum
- **Email:** kabid.umum@rsud.id
- **Password:** password
- **Nama:** Ir. Bambang Sutrisno, M.M
- **Jabatan:** Kepala Bidang Umum & Keuangan
- **Unit Kerja:** Bidang Umum & Keuangan
- **Role:** kepala_bidang

## Routing Berdasarkan Klasifikasi

### 1. **Non Medis** → Bidang Umum & Keuangan
   - Linen (sprei, selimut, handuk)
   - Perlengkapan IT (komputer, printer)
   - Alat Tulis Kantor (ATK)
   - Makanan dan konsumsi
   - Peralatan rumah tangga
   - Furnitur

### 2. **Medis** → Bidang Pelayanan Medis
   - Alat medis
   - Obat-obatan
   - Alat kesehatan

### 3. **Penunjang** → Bidang Penunjang Medis
   - Reagen laboratorium
   - Film radiologi
   - Alat penunjang diagnostik

## Workflow Lengkap

### A. APPROVE (Disetujui)
```
1. Admin/Kepala Instalasi → Membuat permintaan dengan klasifikasi "Non Medis"
   ↓
2. Kepala Instalasi → Approve permintaan
   - System auto set: kabid_tujuan = "Bidang Umum & Keuangan"
   ↓
3. Kabid Umum (kabid.umum@rsud.id) → Terima permintaan
   - Review permintaan
   - Bisa approve/reject/revisi
   ↓
4. Kabid Umum → APPROVE
   - Permintaan diteruskan ke DIREKTUR
   - Status: proses
   - PIC: Direktur
   ↓
5. Direktur → Review dan approve
   ↓
6. Staff Perencanaan → Upload dokumen dan proses pengadaan
```

### B. REVISI (Perlu Perbaikan)
```
1. Kabid Umum → REVISI
   - Status berubah: revisi
   - Catatan revisi ditambahkan ke deskripsi
   - Kembali ke: ADMIN/Kepala Instalasi (unit pemohon)
   ↓
2. Admin → Edit permintaan
   - Perbaiki sesuai catatan revisi
   - Submit ulang → Status kembali: diajukan
   ↓
3. Kembali ke Kepala Instalasi untuk approve ulang
   ↓
4. Workflow normal (ke Kabid Umum lagi)
```

### C. DITOLAK (Rejected)
```
1. Kabid Umum → TOLAK
   - Status berubah: ditolak
   - Alasan penolakan ditambahkan ke deskripsi
   - Kembali ke: ADMIN/Kepala Instalasi (unit pemohon)
   ↓
2. Admin → Hanya bisa DELETE permintaan
   - Tidak bisa edit
   - Buat permintaan baru jika masih diperlukan
```

## Controller Updates

### KepalaBidangController.php
**Updated Methods:**
1. `getKlasifikasiByUnitKerja()` - Support "Bidang Umum & Keuangan"
2. `dashboard()` - Filter permintaan berdasarkan array klasifikasi
3. `index()` - Query dengan whereIn untuk klasifikasi
4. `show()` - Validasi dengan in_array untuk klasifikasi
5. `approve()` - Teruskan ke Direktur
6. `reject()` - Status = ditolak, kembali ke unit pemohon
7. `requestRevision()` - Status = revisi, kembali ke pemohon

### KepalaInstalasiController.php
**Updated Method:**
- `getKabidTujuan()` - Mapping Non Medis → "Bidang Umum & Keuangan"

## Testing Workflow

### Test 1: Approve Normal
```bash
# Login sebagai admin atau kepala instalasi
1. Buat permintaan baru dengan klasifikasi "Non Medis"
2. Approve permintaan
3. Login sebagai kabid.umum@rsud.id
4. Cek dashboard - permintaan harus muncul
5. Approve permintaan
6. Permintaan diteruskan ke Direktur
```

### Test 2: Revisi
```bash
1. Login sebagai kabid.umum@rsud.id
2. Buka permintaan Non Medis
3. Klik "Revisi" dengan catatan
4. Login sebagai admin/kepala instalasi
5. Edit permintaan yang berstatus "revisi"
6. Submit ulang
7. Workflow kembali normal
```

### Test 3: Ditolak
```bash
1. Login sebagai kabid.umum@rsud.id
2. Buka permintaan Non Medis
3. Klik "Tolak" dengan alasan
4. Login sebagai admin
5. Lihat permintaan dengan status "ditolak"
6. Hanya bisa delete, tidak bisa edit
```

## Routes Kabid Umum
```
GET    /kepala-bidang/dashboard          - Dashboard dengan stats
GET    /kepala-bidang/index              - List semua permintaan
GET    /kepala-bidang/permintaan/{id}    - Detail permintaan
POST   /kepala-bidang/permintaan/{id}/approve  - Approve (ke Direktur)
POST   /kepala-bidang/permintaan/{id}/reject   - Tolak (ke Admin)
POST   /kepala-bidang/permintaan/{id}/revisi   - Revisi (ke Admin)
```

## Database Check
```sql
-- Check kabid umum user
SELECT id, name, email, role, jabatan, unit_kerja 
FROM users 
WHERE email = 'kabid.umum@rsud.id';

-- Check permintaan Non Medis
SELECT permintaan_id, bidang, klasifikasi_permintaan, kabid_tujuan, status
FROM permintaan
WHERE klasifikasi_permintaan IN ('Non Medis', 'non_medis');

-- Check workflow
SELECT p.permintaan_id, p.klasifikasi_permintaan, p.kabid_tujuan, p.status,
       nd.kepada, d.jabatan_tujuan, d.status as disposisi_status
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE p.klasifikasi_permintaan = 'Non Medis'
ORDER BY p.permintaan_id DESC;
```

## Status Permintaan
- **diajukan** - Baru dibuat, menunggu approve Kepala Instalasi
- **proses** - Sedang diproses (di Kabid/Direktur/Staff Perencanaan)
- **disetujui** - Sudah disetujui semua, siap pengadaan
- **revisi** - Perlu diperbaiki, dikembalikan ke admin
- **ditolak** - Ditolak, hanya bisa dihapus

## Important Notes
1. ✅ Kabid Umum hanya menerima permintaan dengan klasifikasi "Non Medis"
2. ✅ Jika approve → diteruskan ke Direktur
3. ✅ Jika revisi/tolak → kembali ke Admin (unit pemohon)
4. ✅ Support format klasifikasi lama & baru (medis/Medis, non_medis/Non Medis)
5. ✅ Kabid_tujuan = "Bidang Umum & Keuangan" sesuai unit_kerja user
