# Fitur Nota Dinas Pembelian - Staff Perencanaan

## Status: ✅ COMPLETED

## Overview

Fitur baru untuk Staff Perencanaan untuk membuat **Nota Dinas Pembelian** yang terpisah dari Nota Dinas Usulan. Nota dinas ini digunakan khusus untuk proses pembelian barang/jasa yang sudah disetujui.

## Fitur Utama

### 1. Form Nota Dinas Pembelian

Form lengkap dengan field-field:

#### Identitas Nota Dinas
- **Nomor Nota Dinas** (Required) - Format: 001/ND-PEM/SP/2025
- **Tanggal** (Required) - Tanggal pembuatan nota

#### Detail Pembelian
- **Usulan Ruangan** (Required) - Ruangan/unit yang mengajukan (IGD, Farmasi, Laboratorium, dll)
- **Sifat** (Required) - Sangat Segera / Segera / Biasa / Rahasia
- **Perihal** (Required) - Tujuan dan hal yang diminta dalam nota dinas

#### Tujuan Nota Dinas
- **Dari** (Required) - Default: "Staff Perencanaan"
- **Kepada** (Required) - Pilihan: Bagian Pengadaan, Bagian KSO, Kepala Bagian Keuangan, Direktur

#### Isi Nota
- **Isi Nota Dinas** (Optional) - Detail lengkap mengenai pembelian

## Files Created/Modified

### 1. New Vue Component
**File:** `resources/js/Pages/StaffPerencanaan/CreateNotaDinasPembelian.vue`

Form component lengkap dengan:
- Informasi permintaan (read-only)
- Form input dengan validasi
- Error handling
- Loading state saat submit
- Info box dengan instruksi

### 2. Controller Methods
**File:** `app/Http/Controllers/StaffPerencanaanController.php`

#### New Method: `createNotaDinasPembelian()`
```php
public function createNotaDinasPembelian(Permintaan $permintaan)
{
    $permintaan->load('user');
    
    return Inertia::render('StaffPerencanaan/CreateNotaDinasPembelian', [
        'permintaan' => $permintaan,
    ]);
}
```

#### New Method: `storeNotaDinasPembelian()`
```php
public function storeNotaDinasPembelian(Request $request, Permintaan $permintaan)
{
    // Validasi input
    $data = $request->validate([...]);
    
    // Buat nota dinas dengan tipe pembelian
    $notaDinas = NotaDinas::create([...]);
    
    // Buat disposisi ke tujuan
    Disposisi::create([...]);
    
    // Update status permintaan
    $permintaan->update([...]);
    
    return redirect()->with('success', 'Nota Dinas Pembelian berhasil dibuat');
}
```

**Features:**
- Validasi lengkap semua field required
- Auto-create disposisi ke tujuan yang dipilih
- Update deskripsi permintaan dengan info nota pembelian
- Update status dan pic_pimpinan
- Flash message success

### 3. Routes
**File:** `routes/web.php`

Added new routes:
```php
Route::get('/permintaan/{permintaan}/nota-dinas-pembelian/create', 
    [StaffPerencanaanController::class, 'createNotaDinasPembelian'])
    ->name('nota-dinas-pembelian.create');

Route::post('/permintaan/{permintaan}/nota-dinas-pembelian', 
    [StaffPerencanaanController::class, 'storeNotaDinasPembelian'])
    ->name('nota-dinas-pembelian.store');
```

**Route Names:**
- `staff-perencanaan.nota-dinas-pembelian.create`
- `staff-perencanaan.nota-dinas-pembelian.store`

### 4. Updated Show Page
**File:** `resources/js/Pages/StaffPerencanaan/Show.vue`

Added new button in action buttons section:
```vue
<!-- Buat Nota Dinas Pembelian -->
<Link :href="route('staff-perencanaan.nota-dinas-pembelian.create', permintaan.permintaan_id)"
    class="inline-flex justify-center items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
    <svg>...</svg>
    <span>Buat Nota Dinas Pembelian</span>
</Link>
```

**Updated Layout:**
- Changed from 2-column to 3-column grid
- Added shopping cart icon for Nota Dinas Pembelian button
- Blue color theme for pembelian (berbeda dari usulan yang hijau)

## Data Flow

```
Staff Perencanaan → Buat Nota Dinas Pembelian
    ↓
Form Input (Nomor, Usulan Ruangan, Sifat, Perihal, dll)
    ↓
Submit → Validation
    ↓
Create NotaDinas (tipe: pembelian)
    ↓
Create Disposisi (ke tujuan yang dipilih)
    ↓
Update Permintaan (status, pic_pimpinan, deskripsi)
    ↓
Redirect ke Detail Permintaan dengan success message
```

## Validation Rules

```php
'nomor_nota_dinas' => 'required|string'
'tanggal_nota' => 'required|date'
'usulan_ruangan' => 'required|string'
'sifat' => 'required|in:Sangat Segera,Segera,Biasa,Rahasia'
'perihal' => 'required|string'
'dari' => 'required|string'
'kepada' => 'required|string'
'isi_nota' => 'nullable|string'
'tipe_nota' => 'nullable|string'
```

## Usage Example

### 1. Access Form
1. Login sebagai Staff Perencanaan
2. Buka detail permintaan
3. Klik tombol **"Buat Nota Dinas Pembelian"** (biru, ikon shopping cart)

### 2. Fill Form
```
Nomor Nota Dinas: 001/ND-PEM/SP/2025
Tanggal: 2025-10-21
Usulan Ruangan: IGD (Instalasi Gawat Darurat)
Sifat: Segera
Perihal: Permintaan Pembelian Alat Kesehatan untuk Ruang IGD
Dari: Staff Perencanaan
Kepada: Bagian Pengadaan
Isi Nota: Mohon dapat dilakukan pembelian alat kesehatan sesuai spesifikasi terlampir...
```

### 3. Submit
- Klik "Buat Nota Dinas Pembelian"
- Loading state: "Menyimpan..."
- Redirect ke detail permintaan
- Success message: "Nota Dinas Pembelian berhasil dibuat dan dikirim ke Bagian Pengadaan"

## Database Impact

### Table: `nota_dinas`
New record created dengan:
```sql
permintaan_id: [ID Permintaan]
nomor: 001/ND-PEM/SP/2025
tanggal_nota: 2025-10-21
dari: Staff Perencanaan
kepada: Bagian Pengadaan
sifat: Segera
perihal: Permintaan Pembelian Alat Kesehatan...
uraian: [Isi nota jika ada]
unit_instalasi: IGD
pagu_anggaran: 0
```

### Table: `disposisi`
New record created dengan:
```sql
nota_id: [ID Nota yang baru dibuat]
jabatan_tujuan: Bagian Pengadaan
tanggal_disposisi: [Waktu sekarang]
catatan: Nota Dinas Pembelian telah dibuat...
status: dalam_proses
```

### Table: `permintaan`
Updated dengan:
```sql
status: proses
pic_pimpinan: Bagian Pengadaan
deskripsi: [Deskripsi lama] + [Info nota pembelian]
```

## Differences: Nota Usulan vs Nota Pembelian

### Nota Dinas Usulan
- **Warna:** Hijau (#028174)
- **Icon:** Document/Paper
- **Fokus:** Pagu anggaran, kode program/kegiatan/rekening
- **Fields:** Lebih kompleks (pagu, pajak-pajak, kwitansi, dll)
- **Purpose:** Usulan pengadaan dengan detail anggaran lengkap

### Nota Dinas Pembelian
- **Warna:** Biru (#2563eb)
- **Icon:** Shopping Cart
- **Fokus:** Detail pembelian dan ruangan pengusul
- **Fields:** Lebih sederhana (nomor, ruangan, sifat, perihal)
- **Purpose:** Permintaan pembelian spesifik untuk proses pengadaan

## UI Components

### Colors
- Primary Button: `bg-blue-600 hover:bg-blue-700`
- Border: `border-gray-300`
- Required Indicator: `text-red-500`
- Info Box: `bg-blue-50 border-blue-200 text-blue-700`

### Icons
- Shopping Cart (Main): SVG dari Heroicons
- Info Icon: Question mark circle
- Back Arrow: Left arrow

### Layout
- Max Width: `max-w-4xl`
- Spacing: Consistent `space-y-6`
- Grid: Responsive dengan `md:grid-cols-2`

## Testing Checklist

- [x] Form dapat diakses dari detail permintaan
- [x] Semua field required ter-validasi
- [x] Submit berhasil create nota dinas
- [x] Disposisi otomatis terbuat
- [x] Permintaan ter-update dengan benar
- [x] Redirect ke detail dengan success message
- [x] Error handling untuk validasi
- [x] Loading state saat submit
- [x] Responsive di mobile dan desktop

## URLs

- **Create Form:** `/staff-perencanaan/permintaan/{id}/nota-dinas-pembelian/create`
- **Submit:** POST `/staff-perencanaan/permintaan/{id}/nota-dinas-pembelian`

## Route Names

- **Create:** `staff-perencanaan.nota-dinas-pembelian.create`
- **Store:** `staff-perencanaan.nota-dinas-pembelian.store`

## Error Handling

Form validation dengan error messages:
```javascript
errors.value = {
    nomor_nota_dinas: 'Nomor nota dinas harus diisi',
    tanggal_nota: 'Tanggal harus diisi',
    usulan_ruangan: 'Usulan ruangan harus diisi',
    sifat: 'Sifat harus dipilih',
    perihal: 'Perihal harus diisi',
    dari: 'Dari harus diisi',
    kepada: 'Kepada harus dipilih'
}
```

## Benefits

1. **Simplified Form** - Fokus hanya pada informasi pembelian, tidak perlu input detail anggaran
2. **Clear Purpose** - Terpisah dari nota usulan, lebih jelas fungsinya
3. **Fast Process** - Input lebih cepat karena field lebih sedikit
4. **Proper Documentation** - Mencatat usulan ruangan dan perihal dengan jelas
5. **Automated Workflow** - Auto-create disposisi dan update status

## Future Enhancements

- [ ] Template perihal yang bisa dipilih
- [ ] Auto-generate nomor nota dinas
- [ ] Upload lampiran dokumen
- [ ] Preview nota sebelum submit
- [ ] Export ke PDF
- [ ] Email notification ke tujuan
- [ ] History tracking nota pembelian

## Summary

✅ Form Nota Dinas Pembelian berhasil dibuat  
✅ Routes dan controller methods ditambahkan  
✅ Integration dengan workflow permintaan  
✅ UI/UX yang user-friendly dengan validasi lengkap  
✅ Auto-create disposisi dan update status  
✅ Button terintegrasi di halaman detail permintaan  

**Status: READY FOR PRODUCTION**

**Date:** 2025-10-21  
**Feature:** Nota Dinas Pembelian untuk Staff Perencanaan  
**Type:** New Feature  
**Impact:** Simplified workflow untuk permintaan pembelian
