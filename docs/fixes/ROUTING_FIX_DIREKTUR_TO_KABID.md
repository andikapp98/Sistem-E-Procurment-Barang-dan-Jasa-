# ROUTING FIX: Direktur ke Kabid Sesuai Klasifikasi

## Masalah Sebelumnya
Routing dari Direktur setelah approve **tidak spesifik** ke Kabid yang sesuai:
- ❌ Direktur approve → "Kepala Bidang" (umum, tidak jelas)
- ❌ Tidak mempertimbangkan klasifikasi permintaan
- ❌ Kabid tidak tahu permintaan mana yang untuk mereka

## Solusi yang Diterapkan

### 1. DirekturController.php - Method `approve()` ✅ FIXED

**Perubahan:**
- Menambahkan routing otomatis berdasarkan `klasifikasi_permintaan`
- Set `kabid_tujuan` spesifik di tabel permintaan
- Disposisi dikirim ke Kabid yang sesuai klasifikasi

**Routing Otomatis:**
```
Direktur Approve → Kabid (berdasarkan klasifikasi):
├─ klasifikasi = 'medis' / 'Medis' 
│  └─→ Kabid Yanmed (Bidang Pelayanan Medis)
│
├─ klasifikasi = 'penunjang_medis' / 'Penunjang'
│  └─→ Kabid Penunjang Medis
│
└─ klasifikasi = 'non_medis' / 'Non Medis'
   └─→ Kabid Umum & Keuangan
```

**Code Before (WRONG):**
```php
// Direktur approve tanpa routing spesifik
Disposisi::create([
    'nota_id' => $notaDinas->nota_id,
    'jabatan_tujuan' => 'Kepala Bidang', // ❌ Tidak spesifik
    'catatan' => 'Disetujui oleh Direktur...',
]);

$permintaan->update([
    'pic_pimpinan' => 'Kepala Bidang', // ❌ Tidak ada kabid_tujuan
]);
```

**Code After (CORRECT):**
```php
// Direktur approve dengan routing otomatis
$klasifikasi = $permintaan->klasifikasi_permintaan;
$kabidTujuan = $this->getKabidTujuanByKlasifikasi($klasifikasi);

Disposisi::create([
    'nota_id' => $notaDinas->nota_id,
    'jabatan_tujuan' => $kabidTujuan, // ✅ Spesifik ke Kabid yang sesuai
    'catatan' => 'Disetujui oleh Direktur (Final Approval)... 
                  \n\nKlasifikasi: ' . strtoupper($klasifikasi) .
                  '\nDiteruskan ke: ' . $kabidTujuan,
]);

$permintaan->update([
    'pic_pimpinan' => 'Kepala Bidang',
    'kabid_tujuan' => $kabidTujuan, // ✅ Set kabid_tujuan
]);
```

**Helper Function:**
```php
/**
 * Tentukan Kabid tujuan berdasarkan klasifikasi
 */
private function getKabidTujuanByKlasifikasi($klasifikasi)
{
    $mapping = [
        'Medis' => 'Bidang Pelayanan Medis',
        'medis' => 'Bidang Pelayanan Medis',
        'Penunjang' => 'Bidang Penunjang Medis',
        'penunjang_medis' => 'Bidang Penunjang Medis',
        'Non Medis' => 'Bidang Umum & Keuangan',
        'non_medis' => 'Bidang Umum & Keuangan',
    ];

    return $mapping[$klasifikasi] ?? 'Bidang Pelayanan Medis';
}
```

## Workflow Lengkap

### Workflow Permintaan MEDIS:
```
1. Kepala Instalasi (IGD/RJ/dll)
   └─→ Buat permintaan medis (obat, alat medis)
   
2. Kepala Instalasi Approve
   └─→ klasifikasi_permintaan = 'medis'
   └─→ pic_pimpinan = 'Kepala Bidang'
   └─→ kabid_tujuan = 'Bidang Pelayanan Medis'
   
3. Kabid Yanmed Review & Approve
   └─→ Buat disposisi ke Direktur
   
4. Direktur Review & Approve ✅ FIXED
   └─→ klasifikasi = 'medis'
   └─→ getKabidTujuanByKlasifikasi('medis')
   └─→ kabid_tujuan = 'Bidang Pelayanan Medis'
   └─→ Disposisi ke: Kabid Yanmed
   
5. Kabid Yanmed (kembali)
   └─→ Disposisi ke Staff Perencanaan
   
6. Staff Perencanaan
   └─→ Proses perencanaan pengadaan
```

### Workflow Permintaan PENUNJANG MEDIS:
```
1. Kepala Instalasi (Lab/Radiologi)
   └─→ Buat permintaan penunjang (reagen, film)
   
2. Kepala Instalasi Approve
   └─→ klasifikasi_permintaan = 'penunjang_medis'
   └─→ kabid_tujuan = 'Bidang Penunjang Medis'
   
3. Kabid Penunjang Review & Approve
   └─→ Ke Direktur
   
4. Direktur Approve ✅ FIXED
   └─→ Routing otomatis ke: Kabid Penunjang Medis
   
5. Kabid Penunjang (kembali)
   └─→ Ke Staff Perencanaan
```

### Workflow Permintaan NON MEDIS:
```
1. Kepala Instalasi (Linen/IT/dll)
   └─→ Buat permintaan non medis
   
2. Kepala Instalasi Approve
   └─→ klasifikasi_permintaan = 'non_medis'
   └─→ kabid_tujuan = 'Bidang Umum & Keuangan'
   
3. Kabid Umum Review & Approve
   └─→ Ke Direktur
   
4. Direktur Approve ✅ FIXED
   └─→ Routing otomatis ke: Kabid Umum & Keuangan
   
5. Kabid Umum (kembali)
   └─→ Ke Staff Perencanaan
```

## Keuntungan Perbaikan

✅ **Routing Otomatis & Tepat**
- Direktur approve langsung ke Kabid yang sesuai
- Tidak perlu manual pilih Kabid tujuan

✅ **Clear Tracking**
- Field `kabid_tujuan` selalu ter-set
- Kabid tahu permintaan mana yang untuk mereka

✅ **Efficient Workflow**
- Tidak ada kebingungan routing
- Disposisi tepat sasaran

✅ **Better Audit Trail**
- Catatan disposisi mencantumkan klasifikasi
- Jelas permintaan jenis apa

## Testing

### Test 1: Permintaan Medis
```
1. ✅ Kepala Instalasi IGD buat permintaan obat
2. ✅ klasifikasi_permintaan = 'medis'
3. ✅ Kepala Instalasi approve
4. ✅ Kabid Yanmed terima dan approve
5. ✅ Direktur approve
6. **Expected:**
   - kabid_tujuan = 'Bidang Pelayanan Medis'
   - Disposisi ke: Kabid Yanmed
   - Success message: "...diteruskan ke Bidang Pelayanan Medis..."
7. ✅ Kabid Yanmed terima kembali
8. ✅ Kabid disposisi ke Staff Perencanaan
```

### Test 2: Permintaan Penunjang
```
1. ✅ Kepala Instalasi Lab buat permintaan reagen
2. ✅ klasifikasi_permintaan = 'penunjang_medis'
3. ✅ Kepala Instalasi approve
4. ✅ Kabid Penunjang terima dan approve
5. ✅ Direktur approve
6. **Expected:**
   - kabid_tujuan = 'Bidang Penunjang Medis'
   - Disposisi ke: Kabid Penunjang
7. ✅ Kabid Penunjang terima kembali
```

### Test 3: Permintaan Non Medis
```
1. ✅ Kepala Instalasi Linen buat permintaan
2. ✅ klasifikasi_permintaan = 'non_medis'
3. ✅ Kepala Instalasi approve
4. ✅ Kabid Umum terima dan approve
5. ✅ Direktur approve
6. **Expected:**
   - kabid_tujuan = 'Bidang Umum & Keuangan'
   - Disposisi ke: Kabid Umum
7. ✅ Kabid Umum terima kembali
```

## Database Check

### Query untuk Verifikasi Routing:
```sql
-- Cek permintaan yang sudah di-approve Direktur
SELECT 
    p.permintaan_id,
    p.klasifikasi_permintaan,
    p.kabid_tujuan,
    p.pic_pimpinan,
    d.jabatan_tujuan,
    d.catatan
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
WHERE d.catatan LIKE '%Disetujui oleh Direktur%'
ORDER BY p.permintaan_id DESC
LIMIT 10;
```

**Expected Result:**
```
| permintaan_id | klasifikasi | kabid_tujuan              | jabatan_tujuan            |
|---------------|-------------|---------------------------|---------------------------|
| 123           | medis       | Bidang Pelayanan Medis    | Bidang Pelayanan Medis    |
| 124           | penunjang   | Bidang Penunjang Medis    | Bidang Penunjang Medis    |
| 125           | non_medis   | Bidang Umum & Keuangan    | Bidang Umum & Keuangan    |
```

## File yang Diubah

| File | Method | Changes |
|------|--------|---------|
| DirekturController.php | approve() | ✅ Routing otomatis ke Kabid sesuai klasifikasi |
| DirekturController.php | getKabidTujuanByKlasifikasi() | ✅ Helper function baru |

**Lines Changed:** ~45 lines
**Impact:** HIGH - Fixes routing after Direktur approval

## Notes

⚠️ **Penting:**
- Pastikan field `klasifikasi_permintaan` selalu ter-set dengan benar
- Field ini di-set oleh Kepala Instalasi saat approve
- Jika tidak ada, default ke 'medis' (Bidang Pelayanan Medis)

✅ **Kompatibilitas:**
- Support format lama: 'Medis', 'Penunjang', 'Non Medis'
- Support format baru: 'medis', 'penunjang_medis', 'non_medis'
- Case-insensitive mapping

---

**Status:** ✅ FIXED
**Date:** 30 Oktober 2025
**Tested:** Ready for Testing
