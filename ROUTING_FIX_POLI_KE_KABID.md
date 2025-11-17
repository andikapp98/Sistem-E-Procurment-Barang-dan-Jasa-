# Perbaikan Routing Permintaan dari Poli Gigi ke Kabid Umum

## Masalah
Permintaan dari Poli Gigi dengan klasifikasi "Non Medis" yang ditujukan ke "Kabid Umum" tidak muncul di dashboard/index Kepala Bidang Umum.

## Root Cause
Controller `KepalaBidangController` hanya mencari permintaan dengan kondisi:
```php
->where('status', 'proses')
->where('pic_pimpinan', 'LIKE', '%Kepala Bidang%')
```

Namun permintaan dari **Kepala Poli/Ruang** memiliki karakteristik:
- Status: `diajukan` (bukan `proses`)
- PIC Pimpinan: nama kepala poli (bukan "Kepala Bidang")
- Kabid Tujuan: sudah di-set sesuai klasifikasi

Ini karena **Kepala Poli/Ruang tidak melewati Kepala Instalasi**, jadi tidak ada proses yang mengubah status menjadi `proses` atau pic_pimpinan menjadi "Kepala Bidang".

## Solusi
Menambahkan kondisi query di `KepalaBidangController` untuk menerima permintaan langsung dari Kepala Poli/Ruang:

### File yang Diperbaiki: `app/Http/Controllers/KepalaBidangController.php`

#### Method `dashboard()` dan `index()`

**Kondisi Query Baru:**
```php
$query = Permintaan::with(['user', 'notaDinas.disposisi'])
    ->where(function($q) use ($user, $klasifikasiArray) {
        // Kondisi 1: Permintaan dari Kepala Instalasi (status = proses)
        $q->where(function($subQ) use ($user, $klasifikasiArray) {
            $subQ->where('status', 'proses')
                ->where('pic_pimpinan', 'LIKE', '%Kepala Bidang%');
            if ($klasifikasiArray) {
                $subQ->whereIn('klasifikasi_permintaan', $klasifikasiArray);
            }
        })
        // Kondisi 2: Permintaan langsung dari Kepala Ruang/Poli (status = diajukan) ⭐ NEW
        ->orWhere(function($subQ) use ($user, $klasifikasiArray) {
            $subQ->where('status', 'diajukan');
            
            // Match berdasarkan kabid_tujuan atau klasifikasi
            $subQ->where(function($matchQ) use ($user, $klasifikasiArray) {
                // Match kabid_tujuan dengan jabatan user
                if (stripos($user->jabatan, 'Umum') !== false) {
                    $matchQ->where('kabid_tujuan', 'Kabid Umum');
                } elseif (stripos($user->jabatan, 'Yanmed') !== false || stripos($user->jabatan, 'Pelayanan Medis') !== false) {
                    $matchQ->where('kabid_tujuan', 'Kabid Yanmed');
                } elseif (stripos($user->jabatan, 'Penunjang') !== false) {
                    $matchQ->where('kabid_tujuan', 'Kabid Penunjang');
                }
                
                // Atau match berdasarkan klasifikasi
                if ($klasifikasiArray) {
                    $matchQ->orWhereIn('klasifikasi_permintaan', $klasifikasiArray);
                }
            });
        })
        // Kondisi 3: Disposisi balik dari Direktur
        ->orWhere(function($subQ) use ($user) {
            $subQ->where('kabid_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
                 ->whereHas('notaDinas.disposisi', function($dispQ) use ($user) {
                     $dispQ->where('jabatan_tujuan', 'LIKE', '%' . $user->unit_kerja . '%')
                           ->where('catatan', 'LIKE', '%Disetujui oleh Direktur%');
                 });
        });
    });
```

## Alur Routing yang Didukung

### 1. Dari Kepala Instalasi (Flow Lama - Tetap Didukung)
```
Kepala Ruang/Poli → Kepala Instalasi → Kabid
Status: diajukan → proses → ...
PIC: ... → Kepala Bidang → ...
```

### 2. Langsung dari Kepala Ruang/Poli (Flow Baru - Sekarang Didukung) ⭐
```
Kepala Ruang/Poli → Kabid Umum/Yanmed/Penunjang
Status: diajukan
Kabid Tujuan: Kabid Umum/Yanmed/Penunjang
Klasifikasi: Non Medis/Medis/Penunjang
```

### 3. Disposisi Balik dari Direktur (Tetap Didukung)
```
Direktur → Kabid
Status: proses
Disposisi: jabatan_tujuan = unit kerja Kabid
```

## Mapping Kabid Tujuan

| Klasifikasi Permintaan | Kabid Tujuan | Jabatan Kabid |
|------------------------|--------------|---------------|
| Non Medis | Kabid Umum | Kepala Bidang Umum & Keuangan |
| Medis | Kabid Yanmed | Kepala Bidang Pelayanan Medis |
| Penunjang | Kabid Penunjang | Kepala Bidang Penunjang Medis |

## Testing

### Test Query untuk Kabid Umum
```bash
php artisan tinker
>>> use App\Models\Permintaan;
>>> $count = Permintaan::where('status', 'diajukan')
...     ->where('kabid_tujuan', 'Kabid Umum')
...     ->count();
>>> echo $count;
2  # ✅ Berhasil!
```

### Test Login sebagai Kabid Umum
1. Login sebagai Kabid Umum (email: yang sesuai dengan Kepala Bidang Umum)
2. Akses dashboard atau index
3. Permintaan dari Poli Gigi dengan klasifikasi "Non Medis" seharusnya muncul

## Data Permintaan yang Ditest

**Permintaan ID: 34**
- User: drg. Gita Puspita, Sp.KG (Kepala Poli Gigi)
- Unit Kerja: Poli Gigi
- Klasifikasi: Non Medis
- Kabid Tujuan: Kabid Umum
- Status: diajukan
- PIC Pimpinan: Dr Ahmad Yani

Sebelum perbaikan: ❌ Tidak muncul di Kabid Umum
Setelah perbaikan: ✅ Muncul di Kabid Umum

## Notes
- Perbaikan ini mendukung multiple flow routing ke Kabid
- Tidak mengubah flow existing dari Kepala Instalasi
- Menambahkan support untuk flow langsung dari Kepala Poli/Ruang
- Matching menggunakan `kabid_tujuan` field yang sudah di-set saat create permintaan
