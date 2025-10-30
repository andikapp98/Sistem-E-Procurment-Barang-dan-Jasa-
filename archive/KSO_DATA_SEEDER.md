# Seeder KSO - Dokumentasi

## ✅ Status: SELESAI

Seeder KSO berhasil dibuat dengan 3 data KSO lengkap beserta workflow.

## 📊 Data yang Dibuat

### KSO 1: Ventilator & Monitor ICU
- **No KSO**: KSO/2025/001
- **Nilai Kontrak**: Rp 475,000,000 (475 juta)
- **Status**: AKTIF
- **Pihak Pertama**: RSUD - Direktur
- **Pihak Kedua**: PT. Alat Medis Indonesia
- **Isi Kerjasama**: Pengadaan 5 unit Ventilator ICU dan 10 unit Monitor Pasien
- **Bidang**: Instalasi Gawat Darurat
- **Tanggal KSO**: 8 hari yang lalu

### KSO 2: Obat-obatan Tahunan
- **No KSO**: KSO/2025/002
- **Nilai Kontrak**: Rp 242,000,000 (242 juta)
- **Status**: AKTIF
- **Pihak Pertama**: RSUD - Direktur
- **Pihak Kedua**: PT. Kimia Farma Trading & Distribution
- **Isi Kerjasama**: Pengadaan obat-obatan untuk kebutuhan RSUD periode 2025
- **Bidang**: Instalasi Farmasi
- **Tanggal KSO**: 5 hari yang lalu

### KSO 3: Alat Laboratorium
- **No KSO**: KSO/2025/003
- **Nilai Kontrak**: Rp 335,000,000 (335 juta)
- **Status**: DRAFT
- **Pihak Pertama**: RSUD - Direktur
- **Pihak Kedua**: PT. Labora Medika
- **Isi Kerjasama**: Pengadaan Hematology Analyzer dan Chemistry Analyzer
- **Bidang**: Instalasi Laboratorium
- **Tanggal KSO**: 2 hari yang lalu

## 📋 Workflow Lengkap Setiap KSO

Semua 3 KSO sudah melalui alur lengkap:

```
Permintaan (Kepala Instalasi)
    ↓
Nota Dinas (ke Kepala Bidang)
    ↓
Disposisi (Kepala Bidang → Direktur)
    ↓
Nota Dinas (Direktur → Staff Perencanaan)
    ↓
Perencanaan (Staff Perencanaan)
    ↓
KSO (Bagian KSO) ← SEKARANG DI SINI
    ↓
Pengadaan (Next Step)
```

## 🎯 Status KSO

### Status ENUM Values:
1. **draft** - KSO dalam tahap penyusunan
2. **aktif** - KSO sudah ditandatangani dan aktif (berlaku)
3. **selesai** - KSO sudah selesai dilaksanakan
4. **batal** - KSO dibatalkan

### Distribusi Status:
- ✅ **Aktif**: 2 KSO (Ventilator & Obat-obatan)
- 📝 **Draft**: 1 KSO (Alat Lab)

## 💰 Total Nilai Kontrak

**Rp 1,052,000,000** (1.052 Miliar)
- KSO 1: Rp 475 juta (45.1%)
- KSO 2: Rp 242 juta (23.0%)
- KSO 3: Rp 335 juta (31.9%)

## 🔐 Login Info

**Email**: kso@rsud.id  
**Password**: password  
**Role**: kso

## 📁 File Seeder

**Path**: `database/seeders/KsoDataSeeder.php`

**Cara Run**:
```bash
php artisan db:seed --class=KsoDataSeeder
```

**Cara Run Ulang** (jika perlu):
```bash
# Hapus data KSO yang ada terlebih dahulu
# atau jalankan seeder lagi (akan tambah data baru)
php artisan db:seed --class=KsoDataSeeder
```

## 🗃️ Database Structure

### Table: kso
| Field | Type | Description |
|-------|------|-------------|
| kso_id | bigint | Primary Key |
| perencanaan_id | bigint | Foreign Key ke perencanaan |
| no_kso | varchar(255) | Nomor KSO (unique) |
| tanggal_kso | date | Tanggal pembuatan KSO |
| pihak_pertama | varchar(255) | Pihak pertama (RSUD) |
| pihak_kedua | varchar(255) | Pihak kedua (Vendor/Partner) |
| isi_kerjasama | text | Detail isi kerjasama |
| nilai_kontrak | decimal(15,2) | Nilai kontrak dalam rupiah |
| status | enum | draft, aktif, selesai, batal |
| created_at | timestamp | |
| updated_at | timestamp | |

## 🔄 Relasi Database

```
Permintaan (1)
    ↓
NotaDinas (N)
    ↓
Disposisi (N)
    ↓
Perencanaan (1) ← Relasi ke KSO
    ↓
KSO (N) ← Data yang dibuat seeder ini
    ↓
Pengadaan (N) ← Next workflow
```

## ✅ Data yang Ter-create

Untuk setiap KSO, seeder membuat:

1. **1 Permintaan** dengan status 'proses' dan PIC 'Bagian KSO'
2. **2 Nota Dinas**:
   - Kepala Instalasi → Kepala Bidang
   - Direktur → Staff Perencanaan
3. **1 Disposisi**: Kepala Bidang → Direktur
4. **1 Perencanaan**: Dengan anggaran, metode pengadaan, HPS, dll
5. **1 KSO**: Dengan nomor, nilai kontrak, pihak-pihak, dll

**Total per KSO**: 6 records (1 permintaan + 2 nota dinas + 1 disposisi + 1 perencanaan + 1 kso)

**Total untuk 3 KSO**: 18 records

## 🧪 Testing

### 1. Check Data KSO
```sql
SELECT * FROM kso ORDER BY kso_id DESC LIMIT 3;
```

### 2. Check dengan Perencanaan
```sql
SELECT k.*, p.nama_paket, p.anggaran 
FROM kso k
JOIN perencanaan p ON k.perencanaan_id = p.perencanaan_id
ORDER BY k.kso_id DESC;
```

### 3. Check dengan Permintaan
```sql
SELECT k.no_kso, k.nilai_kontrak, k.status,
       perm.bidang, perm.deskripsi
FROM kso k
JOIN perencanaan p ON k.perencanaan_id = p.perencanaan_id
JOIN disposisi d ON p.disposisi_id = d.disposisi_id
JOIN nota_dinas nd ON d.nota_id = nd.nota_id
JOIN permintaan perm ON nd.permintaan_id = perm.permintaan_id
ORDER BY k.kso_id DESC;
```

### 4. Total Nilai Kontrak
```sql
SELECT 
    COUNT(*) as total_kso,
    SUM(nilai_kontrak) as total_nilai,
    AVG(nilai_kontrak) as rata_rata_nilai,
    status
FROM kso
GROUP BY status;
```

## 📝 Catatan Penting

### 1. Nilai Kontrak vs HPS
- Nilai kontrak biasanya sedikit lebih rendah dari HPS (Harga Perkiraan Satuan)
- Contoh:
  - KSO 1: HPS Rp 480 juta → Kontrak Rp 475 juta (nego 1.04%)
  - KSO 2: HPS Rp 245 juta → Kontrak Rp 242 juta (nego 1.22%)
  - KSO 3: HPS Rp 340 juta → Kontrak Rp 335 juta (nego 1.47%)

### 2. Pihak Pertama & Kedua
- **Pihak Pertama**: Selalu RSUD (diwakili Direktur)
- **Pihak Kedua**: Vendor/Partner yang menang tender/dipilih

### 3. Status Flow
```
draft → aktif → selesai
         ↓
       batal (jika dibatalkan)
```

### 4. PIC Pimpinan
- Semua permintaan yang ada KSO sudah set `pic_pimpinan = 'Bagian KSO'`
- Ini menandakan permintaan sedang di tahap KSO

## 🎯 Use Cases

### Use Case 1: KSO User Login
1. Login dengan `kso@rsud.id`
2. Lihat dashboard KSO
3. Akan muncul 3 KSO (2 aktif, 1 draft)

### Use Case 2: Aktivasi KSO Draft
1. Pilih KSO #3 (draft)
2. Edit dan ubah status ke 'aktif'
3. KSO siap untuk lanjut ke Pengadaan

### Use Case 3: Create Pengadaan dari KSO
1. Pilih KSO dengan status 'aktif'
2. Buat data Pengadaan baru
3. Link dengan `kso_id` dari KSO terpilih

## 🔜 Next Steps

Setelah KSO dibuat, tahap selanjutnya adalah:

1. **Pengadaan** - Proses pembelian/tender
2. **Nota Penerimaan** - Penerimaan barang/jasa
3. **Serah Terima** - Serah terima ke unit pemohon

## 📊 Query Monitoring

### Monitor KSO Aktif
```sql
SELECT no_kso, pihak_kedua, nilai_kontrak, tanggal_kso
FROM kso
WHERE status = 'aktif'
ORDER BY tanggal_kso DESC;
```

### KSO per Vendor
```sql
SELECT pihak_kedua, 
       COUNT(*) as jumlah_kso,
       SUM(nilai_kontrak) as total_nilai
FROM kso
GROUP BY pihak_kedua
ORDER BY total_nilai DESC;
```

### KSO Menunggu Aktivasi
```sql
SELECT no_kso, pihak_kedua, nilai_kontrak, tanggal_kso
FROM kso
WHERE status = 'draft'
ORDER BY tanggal_kso ASC;
```

---

**Created**: 2025-10-26  
**Seeder**: KsoDataSeeder.php  
**Total KSO**: 3  
**Total Nilai**: Rp 1,052,000,000
