# Seeder Documentation - Permintaan Pengadaan

## Daftar Seeder yang Tersedia

### 1. KepalaBidangSeeder
Membuat 10 data user dengan role `kepala_bidang` untuk berbagai bidang di rumah sakit.

**Bidang yang tersedia:**
- Bidang Pelayanan Medis
- Bidang Penunjang Medis
- Bidang Keperawatan
- Bidang Umum & Keuangan
- Bidang SDM & Pendidikan
- Bidang Mutu & Keselamatan Pasien
- Bidang Sarana & Prasarana
- Bidang Rawat Jalan
- Bidang Rawat Inap
- Bidang Rekam Medis & Informasi

**Cara menjalankan:**
```bash
php artisan db:seed --class=KepalaBidangSeeder
```

### 2. KepalaInstalasiPermintaanSeeder
Membuat 15 data permintaan pengadaan dari berbagai Kepala Instalasi dengan status yang bervariasi.

**Instalasi yang membuat permintaan:**
- **Gawat Darurat (IGD)** - 3 permintaan
  - Ventilator portable
  - Obat-obatan emergency kit
  - Brankar emergency

- **Farmasi** - 3 permintaan
  - Sistem Informasi Manajemen Farmasi (SIMRS)
  - Obat-obatan rutin (Paracetamol, Amoxicillin, dll)
  - Lemari pendingin vaksin

- **Laboratorium** - 3 permintaan
  - Hematology Analyzer
  - Reagen dan bahan habis pakai
  - Mikroskop binokuler digital

- **Radiologi** - 3 permintaan
  - CT Scan 64 Slice
  - APD khusus radiologi (apron timbal, dll)
  - Sistem PACS (Picture Archiving)

- **Bedah Sentral** - 3 permintaan
  - Alat laparoskopi lengkap
  - Instrumen bedah set orthopedi
  - Mesin anestesi modern

**Status Permintaan:**
- Diajukan: 5 permintaan
- Proses: 7 permintaan
- Disetujui: 3 permintaan

**Cara menjalankan:**
```bash
php artisan db:seed --class=KepalaInstalasiPermintaanSeeder
```

### 3. PermintaanToKabidWorkflowSeeder
Membuat workflow lengkap dari Permintaan → Nota Dinas → Disposisi ke Kepala Bidang yang sesuai.

**Alur Workflow:**
1. **Nota Dinas**: Dibuat dari setiap permintaan dengan nomor terstruktur
2. **Disposisi**: Dikirim ke Kepala Bidang yang sesuai berdasarkan jenis instalasi

**Mapping Instalasi ke Kepala Bidang:**
- **Gawat Darurat & Bedah Sentral** → Kepala Bidang Pelayanan Medis
- **Laboratorium & Radiologi** → Kepala Bidang Penunjang Medis
- **Farmasi** → Kepala Bidang Keperawatan

**Fitur:**
- Otomatis membuat Nota Dinas untuk semua permintaan yang belum memilikinya
- Generate nomor nota dinas terstruktur (ND/{BIDANG}/{TAHUN}/{NO})
- Disposisi otomatis ke Kepala Bidang yang sesuai
- Update status permintaan dari 'diajukan' menjadi 'proses'

**Cara menjalankan:**
```bash
php artisan db:seed --class=PermintaanToKabidWorkflowSeeder
```

**Catatan:** Seeder ini membutuhkan data dari `UserSeeder`, `KepalaBidangSeeder`, dan `KepalaInstalasiPermintaanSeeder`.

## Menjalankan Semua Seeder

Untuk menjalankan semua seeder sekaligus:
```bash
php artisan db:seed
```

Atau dengan fresh migration:
```bash
php artisan migrate:fresh --seed
```

## Catatan Penting

1. **Dependency**: Seeder harus dijalankan dengan urutan:
   - `UserSeeder` (pertama)
   - `KepalaBidangSeeder` (kedua)
   - `KepalaInstalasiPermintaanSeeder` (ketiga)
   - `PermintaanToKabidWorkflowSeeder` (keempat)

2. **Password Default**: Semua user yang dibuat menggunakan password: `password`

3. **Email Kepala Instalasi**:
   - IGD: `kepala.igd@rsud.id`
   - Farmasi: `kepala.farmasi@rsud.id`
   - Laboratorium: `kepala.lab@rsud.id`
   - Radiologi: `kepala.radiologi@rsud.id`
   - Bedah Sentral: `kepala.bedah@rsud.id`

4. **Email Kepala Bidang**:
   - Pelayanan Medis: `kabid.yanmed@rsud.id`
   - Penunjang Medis: `kabid.penunjang@rsud.id`
   - Keperawatan: `kabid.keperawatan@rsud.id`

5. **Tanggal Permintaan**: Data permintaan dibuat dengan tanggal mundur (15-50 hari yang lalu) untuk simulasi yang lebih realistis.

## Struktur Data Permintaan

Setiap permintaan memiliki field:
- `user_id`: ID user pembuat (Kepala Instalasi)
- `bidang`: Nama instalasi/bidang
- `tanggal_permintaan`: Tanggal pengajuan
- `deskripsi`: Detail lengkap permintaan
- `status`: Status permintaan (diajukan/proses/disetujui/ditolak/revisi)
- `pic_pimpinan`: Nama dan gelar kepala instalasi
- `no_nota_dinas`: Nomor nota dinas (format: ND/{BIDANG}/2025/{NO})
- `link_scan`: Link scan dokumen (dummy URL)

## Testing

Setelah menjalankan seeder, verifikasi data dengan:

```bash
# Cek total data
php artisan tinker --execute="echo 'Total Permintaan: ' . \App\Models\Permintaan::count() . PHP_EOL; echo 'Total Nota Dinas: ' . \App\Models\NotaDinas::count() . PHP_EOL; echo 'Total Disposisi: ' . \App\Models\Disposisi::count() . PHP_EOL;"

# Login ke aplikasi sebagai Kepala Bidang untuk melihat disposisi
# Email: kabid.yanmed@rsud.id (untuk melihat disposisi dari IGD dan Bedah Sentral)
# Email: kabid.penunjang@rsud.id (untuk melihat disposisi dari Lab dan Radiologi)
# Email: kabid.keperawatan@rsud.id (untuk melihat disposisi dari Farmasi)
# Password: password
```

## Workflow Pengadaan

Alur lengkap sistem pengadaan:

1. **Kepala Instalasi** → Buat Permintaan
2. **Kepala Instalasi** → Buat Nota Dinas
3. **Sistem** → Disposisi otomatis ke Kepala Bidang
4. **Kepala Bidang** → Review dan Disposisi ke Wakil Direktur/Direktur
5. **Direktur** → Approve dan Disposisi ke Staff Perencanaan
6. **Staff Perencanaan** → Buat Perencanaan
7. **KSO** → Buat Kerja Sama Operasional
8. **Pengadaan** → Proses Pengadaan
9. **Penerimaan** → Nota Penerimaan Barang
10. **Serah Terima** → Serah Terima ke Kepala Instalasi

## Troubleshooting

**Error: "Kepala Instalasi users tidak ditemukan"**
- Jalankan `UserSeeder` terlebih dahulu
- Pastikan email kepala instalasi sudah terdaftar di database

**Data duplikat**
- Seeder menggunakan `create()` bukan `updateOrCreate()`
- Untuk menghindari duplikat, gunakan `migrate:fresh --seed`
