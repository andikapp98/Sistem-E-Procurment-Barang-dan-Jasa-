# Workflow Lengkap Staff Perencanaan ke Pengadaan

## Overview
Dokumen ini menjelaskan workflow lengkap dari Staff Perencanaan hingga data masuk ke Bagian Pengadaan.

## Status Validasi Form
✅ Semua form sudah bisa disave dengan validasi lengkap:

### 1. Nota Dinas (CreateNotaDinas.vue)
- **Route**: `staff-perencanaan.nota-dinas.store`
- **Method**: `storeNotaDinas()`
- **Status**: ✅ Form submit berhasil
- **Validasi**: 
  - nomor_nota_dinas (required)
  - tanggal_nota (required)
  - dari (required)
  - kepada (required)
  - perihal (required)

### 2. DPP - Dokumen Persiapan Pengadaan (CreateDPP.vue)
- **Route**: `staff-perencanaan.dpp.store`
- **Method**: `storeDPP()`
- **Status**: ✅ Form submit berhasil dengan validasi lengkap
- **Validasi**:
  - ppk_ditunjuk (required)
  - nama_paket (required)
  - lokasi (required)
  - uraian_program (required)
  - uraian_kegiatan (required)
  - kode_rekening (required)
  - sumber_dana (required)
  - pagu_paket (required, numeric, min:0)
  - nilai_hps (required, numeric, min:0)
  - sumber_data_survei_hps (required)
  - nama_kegiatan (required)
  - jenis_pengadaan (required)

### 3. HPS - Harga Perkiraan Sendiri (CreateHPS.vue)
- **Route**: `staff-perencanaan.hps.store`
- **Method**: `storeHPS()`
- **Status**: ✅ Form submit berhasil
- **Validasi**:
  - Items HPS dengan nama_item, satuan, jumlah, harga_satuan

### 4. Nota Dinas Pembelian (CreateNotaDinasPembelian.vue)
- **Route**: `staff-perencanaan.nota-dinas-pembelian.store`
- **Method**: `storeNotaDinasPembelian()`
- **Status**: ✅ Form submit berhasil
- **Validasi**:
  - tanggal_nota (required)
  - usulan_ruangan (required)
  - sifat (required, in:Sangat Segera,Segera,Biasa,Rahasia)
  - perihal (required)
  - dari (required)
  - kepada (required)
- **Auto Generate**: Nomor nota otomatis jika kosong

### 5. Spesifikasi Teknis (CreateSpesifikasiTeknis.vue)
- **Route**: `staff-perencanaan.spesifikasi-teknis.store`
- **Method**: `storeSpesifikasiTeknis()`
- **Status**: ✅ Form submit berhasil dengan validasi lengkap
- **Validasi**:
  - latar_belakang (required)
  - maksud_tujuan (required)
  - target_sasaran (required)
  - pejabat_pengadaan (required)
  - sumber_dana (required)
  - perkiraan_biaya (required)
  - jenis_barang_jasa (required)
  - fungsi_manfaat (required)
  - kegiatan_rutin (required, in:Ya,Tidak)
  - jangka_waktu (required)
  - estimasi_waktu_datang (required)
  - tenaga_diperlukan (required)
  - pelaku_usaha (required)
  - pengadaan_sejenis (required, in:Ya,Tidak)
  - indikasi_konsolidasi (required, in:Ya,Tidak)

### 6. Disposisi (CreateDisposisi.vue)
- **Route**: `staff-perencanaan.disposisi.store`
- **Method**: `storeDisposisi()`
- **Status**: ✅ Form submit berhasil
- **Validasi**:
  - jabatan_tujuan (required)
  - tanggal_disposisi (required)
  - catatan (optional)
  - status (optional, default: dalam_proses)

## Workflow Step by Step

### Step 1: Staff Perencanaan Menerima Permintaan
- Permintaan datang dari Direktur dengan status 'disetujui'
- `pic_pimpinan` = 'Staff Perencanaan'
- Permintaan muncul di dashboard Staff Perencanaan

### Step 2: Membuat Semua Dokumen (Urutan Bebas)
Staff Perencanaan membuat 5 dokumen wajib:

1. **Nota Dinas** - Surat pengantar resmi
2. **DPP** - Dokumen Persiapan Pengadaan (detail lengkap)
3. **HPS** - Harga Perkiraan Sendiri (item-item dan harga)
4. **Nota Dinas Pembelian** - Surat pembelian resmi
5. **Spesifikasi Teknis** - Spesifikasi detail barang/jasa

### Step 3: Buat Disposisi (Optional)
- Disposisi bisa dibuat untuk internal tracking
- Tidak wajib sebelum forward ke Pengadaan

### Step 4: Validasi Kelengkapan Dokumen
System otomatis mengecek kelengkapan di `Show.vue`:

```javascript
const allDocumentsComplete = computed(() => {
    return hasNotaDinas.value && 
           hasDPP.value && 
           hasHPS.value && 
           hasNotaDinasPembelian.value && 
           hasSpesifikasiTeknis.value;
});
```

### Step 5: Forward ke Bagian Pengadaan
**Method**: `forwardToPengadaan()`

**Validasi di Backend**:
```php
// Cek semua dokumen lengkap
$hasNotaDinas = $permintaan->notaDinas()->exists();
$hasDPP = Perencanaan::whereHas('disposisi.notaDinas', ...)->exists();
$hasHPS = $permintaan->hps()->exists();
$hasNotaDinasPembelian = NotaDinas::where('permintaan_id', ...)
    ->where('tipe_nota', 'pembelian')->exists();
$hasSpesifikasiTeknis = $permintaan->spesifikasiTeknis()->exists();

if (!$hasNotaDinas || !$hasDPP || !$hasHPS || 
    !$hasNotaDinasPembelian || !$hasSpesifikasiTeknis) {
    return redirect()->back()->withErrors([
        'error' => 'Semua dokumen harus lengkap...'
    ]);
}
```

**Proses Forward**:
1. Buat disposisi ke "Bagian Pengadaan"
2. Log activity
3. Update permintaan:
   - `status` = 'proses'
   - `pic_pimpinan` = 'Bagian Pengadaan'
   - Tambahkan keterangan di deskripsi

**Data yang Dikirim ke Pengadaan**:
- ✅ Permintaan dengan status 'proses'
- ✅ Nota Dinas (tipe: default)
- ✅ DPP (via Perencanaan model)
- ✅ HPS dengan items lengkap
- ✅ Nota Dinas Pembelian (tipe: pembelian)
- ✅ Spesifikasi Teknis lengkap
- ✅ Disposisi ke Bagian Pengadaan
- ✅ Activity logs lengkap

## Struktur Data yang Masuk ke Pengadaan

### 1. Tabel `permintaans`
```sql
permintaan_id: 123
status: 'proses'
pic_pimpinan: 'Bagian Pengadaan'
deskripsi: '... [DOKUMEN LENGKAP - DIKIRIM KE PENGADAAN] ...'
```

### 2. Tabel `nota_dinas`
```sql
-- Nota Dinas Utama
nota_id: 1
permintaan_id: 123
tipe_nota: NULL atau 'default'
nomor: '001/ND-SP/2025'
dari: 'Staff Perencanaan'
kepada: '...'

-- Nota Dinas Pembelian
nota_id: 2
permintaan_id: 123
tipe_nota: 'pembelian'
nomor: '001/ND-PEM/SP/2025'
```

### 3. Tabel `disposisis`
```sql
disposisi_id: 1
nota_id: 1 (atau 2)
jabatan_tujuan: 'Bagian Pengadaan'
tanggal_disposisi: '2025-11-05'
catatan: 'Semua dokumen perencanaan telah lengkap...'
status: 'dalam_proses'
```

### 4. Tabel `perencanaans` (DPP)
```sql
perencanaan_id: 1
disposisi_id: ...
ppk_ditunjuk: 'Nama PPK'
nama_paket: 'Pengadaan Alat Kesehatan...'
pagu_paket: 500000000
nilai_hps: 480000000
jenis_pengadaan: 'Barang'
... (semua field DPP)
```

### 5. Tabel `hps`
```sql
hps_id: 1
permintaan_id: 123
tanggal_hps: '2025-11-05'
total_hps: 480000000
keterangan: '...'
```

### 6. Tabel `hps_items`
```sql
-- Multiple items
item_id: 1, hps_id: 1, nama_item: 'Item 1', jumlah: 10, harga_satuan: 100000
item_id: 2, hps_id: 1, nama_item: 'Item 2', jumlah: 5, harga_satuan: 200000
...
```

### 7. Tabel `spesifikasi_teknis`
```sql
spesifikasi_id: 1
permintaan_id: 123
latar_belakang: '...'
maksud_tujuan: '...'
jenis_barang_jasa: '...'
... (semua field spesifikasi)
```

### 8. Tabel `user_activity_logs`
```sql
-- Log forward ke pengadaan
action: 'forward'
module: 'permintaan'
description: 'Mengirim permintaan #123 ke Bagian Pengadaan...'
related_type: 'Permintaan'
related_id: 123
```

## Akses Bagian Pengadaan

Setelah data masuk, Bagian Pengadaan dapat:
1. Melihat permintaan di dashboard mereka
2. Filter berdasarkan `pic_pimpinan` = 'Bagian Pengadaan'
3. Akses semua dokumen lengkap:
   - Nota Dinas
   - DPP
   - HPS + items
   - Nota Dinas Pembelian
   - Spesifikasi Teknis
4. Melanjutkan workflow ke tahap pengadaan

## Testing Checklist

### ✅ Test Form Save
- [x] Nota Dinas bisa disave
- [x] DPP bisa disave dengan validasi
- [x] HPS bisa disave dengan items
- [x] Nota Dinas Pembelian bisa disave
- [x] Spesifikasi Teknis bisa disave
- [x] Disposisi bisa disave

### ✅ Test Validasi
- [x] Form tidak bisa submit jika field required kosong
- [x] Validasi angka untuk pagu_paket dan nilai_hps
- [x] Validasi enum untuk sifat, kegiatan_rutin, dll
- [x] Auto generate nomor nota jika kosong

### ✅ Test Forward ke Pengadaan
- [x] Tidak bisa forward jika dokumen belum lengkap
- [x] Tombol "Kirim ke Bagian Pengadaan" muncul jika semua dokumen lengkap
- [x] Disposisi otomatis dibuat ke Bagian Pengadaan
- [x] Status permintaan berubah menjadi 'proses'
- [x] pic_pimpinan berubah menjadi 'Bagian Pengadaan'
- [x] Activity log tercatat
- [x] Redirect ke index dengan success message

### ✅ Test Data Integrity
- [x] Semua data tersimpan di database
- [x] Relasi antar tabel terjaga (foreign keys)
- [x] Data bisa diakses dari Bagian Pengadaan

## Error Handling

### Jika Dokumen Belum Lengkap
```php
return redirect()->back()->withErrors([
    'error' => 'Semua dokumen harus lengkap sebelum dikirim ke Bagian Pengadaan. 
               Pastikan Nota Dinas, DPP, HPS, Nota Dinas Pembelian, 
               dan Spesifikasi Teknis sudah dibuat.'
]);
```

### Jika Nota Dinas Tidak Ditemukan
```php
return redirect()->back()->withErrors([
    'error' => 'Nota dinas tidak ditemukan.'
]);
```

### Jika Tidak Punya Akses
```php
if ($permintaan->pic_pimpinan !== 'Staff Perencanaan' && 
    $permintaan->pic_pimpinan !== $user->nama) {
    abort(403, 'Anda tidak memiliki akses untuk memforward permintaan ini.');
}
```

## Kesimpulan

✅ **Semua form bisa disave** dengan validasi lengkap
✅ **Data lengkap masuk ke Pengadaan** setelah forward
✅ **Workflow terintegrasi** dengan activity logging
✅ **Error handling** lengkap untuk berbagai skenario
✅ **UI indikator** jelas untuk kelengkapan dokumen

Workflow dari Staff Perencanaan ke Bagian Pengadaan sudah **LENGKAP dan TERUJI**.
