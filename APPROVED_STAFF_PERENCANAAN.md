# Approved View - Staff Perencanaan

## Overview
View `Approved` untuk Staff Perencanaan menampilkan riwayat permintaan yang sudah diproses oleh Staff Perencanaan, memberikan visibilitas penuh atas semua permintaan yang pernah ditangani.

## File yang Dibuat/Diubah

### 1. View File
**File**: `resources/js/Pages/StaffPerencanaan/Approved.vue`
- Halaman untuk menampilkan daftar permintaan yang sudah diproses
- Dilengkapi dengan filter dan pagination
- Menampilkan progress dan status terkini

### 2. Controller
**File**: `app/Http/Controllers/StaffPerencanaanController.php`
- Method `approved()` sudah ada, diupdate untuk menambahkan:
  - Statistik (total, forwarded, processing, completed)
  - Field `last_stage` untuk menampilkan tahap terakhir

### 3. Dashboard
**File**: `resources/js/Pages/StaffPerencanaan/Dashboard.vue`
- Ditambahkan Quick Action untuk akses ke halaman Approved

### 4. Route
**File**: `routes/web.php`
- Route sudah ada: `staff-perencanaan.approved`

## Fitur Halaman Approved

### Statistics Cards
1. **Total Diproses** - Total permintaan yang pernah ditangani
2. **Sudah Diteruskan** - Permintaan yang sudah diteruskan ke bagian lain
3. **Dalam Proses** - Permintaan dengan status 'proses'
4. **Selesai** - Permintaan dengan status 'selesai'

### Filter & Search
- **Cari** - Pencarian berdasarkan ID, deskripsi, atau No. Nota Dinas
- **Bidang** - Filter berdasarkan bidang pemohon
- **Status** - Filter berdasarkan status (proses, disetujui, ditolak, revisi, selesai)
- **Tanggal Dari/Sampai** - Filter berdasarkan rentang tanggal

### Tabel Permintaan
Kolom yang ditampilkan:
- **ID** - Nomor permintaan
- **Bidang** - Bidang pemohon
- **Deskripsi** - Deskripsi permintaan (truncated)
- **Tahap Terakhir** - Tahap terakhir dalam workflow
- **Tanggal** - Tanggal permintaan dibuat
- **Status** - Status saat ini dengan warna badge
- **Progress** - Progress bar visual (0-100%)
- **Aksi** - Tombol Detail dan History

### Status Badge Colors
- **Proses** - Kuning (yellow)
- **Disetujui** - Hijau (green)
- **Ditolak** - Merah (red)
- **Revisi** - Biru (blue)
- **Selesai** - Ungu (purple)

### Pagination
- Navigasi halaman dengan previous/next
- Menampilkan jumlah data yang ditampilkan
- Link ke setiap halaman

## Cara Mengakses

### Dari Dashboard
1. Login sebagai Staff Perencanaan
2. Di Dashboard, klik tombol **"Riwayat yang Sudah Diproses"** (tombol indigo)

### URL
```
/staff-perencanaan/approved
```

### Route Name
```php
route('staff-perencanaan.approved')
```

## Data yang Ditampilkan

Halaman ini menampilkan permintaan dengan kriteria:
- Pernah melalui Staff Perencanaan (ada disposisi dengan jabatan_tujuan 'Staff Perencanaan')
- Status: proses, disetujui, ditolak, revisi, atau selesai
- Diurutkan dari ID terbaru

Setiap permintaan dilengkapi dengan:
- Tracking status
- Progress percentage
- Timeline count
- Tahap terakhir (last_stage)

## Aksi yang Tersedia

### 1. Detail
Membuka halaman detail permintaan lengkap dengan semua dokumen

### 2. History
Membuka halaman history aktivitas lengkap permintaan

## Controller Logic

```php
public function approved(Request $request)
{
    // Query permintaan yang sudah pernah melalui Staff Perencanaan
    $query = Permintaan::with(['user', 'notaDinas.disposisi'])
        ->whereHas('notaDinas.disposisi', function($q) use ($user) {
            $q->where('jabatan_tujuan', 'like', '%Staff Perencanaan%')
              ->orWhere('jabatan_tujuan', $user->jabatan);
        })
        ->whereIn('status', ['proses', 'disetujui', 'ditolak', 'revisi', 'selesai']);
    
    // Hitung statistik
    // Apply filters
    // Pagination dengan tracking info
    // Return ke view
}
```

## Kegunaan

Halaman Approved berguna untuk:
- **Audit Trail** - Melihat semua permintaan yang pernah ditangani
- **Monitoring** - Memantau progress permintaan yang sudah diteruskan
- **Reporting** - Mendapatkan data untuk laporan
- **History Tracking** - Melacak status dan tahapan permintaan

## Update Log
- 2025-11-05: Initial creation - Approved view untuk Staff Perencanaan
- 2025-11-05: Added stats calculation in controller
- 2025-11-05: Added Quick Action button in Dashboard
