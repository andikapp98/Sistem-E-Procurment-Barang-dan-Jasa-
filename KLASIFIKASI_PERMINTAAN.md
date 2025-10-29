# klasifikasi permintaan PERMINTAAN - DOKUMENTASI

## ✅ FITUR BARU: klasifikasi permintaan

Sistem permintaan sekarang memiliki klasifikasi permintaan yang menentukan routing approval ke Kepala Bidang yang sesuai.

---

## 📊 klasifikasi permintaan

### 1. MEDIS
**Tujuan:** Kepala Bidang Pelayanan Medis (`kabid.yanmed@rsud.id`)

**Kriteria:**
- Alat kesehatan langsung untuk pasien
- Obat-obatan
- Alat medis emergency
- Consumable medis (APD, sarung tangan steril, dll)
- Peralatan untuk pelayanan pasien langsung

**Unit yang mengajukan:**
- IGD (alat emergency, obat, APD)
- Farmasi (obat-obatan)
- Bedah Sentral (alat operasi, instrumen)
- Rawat Inap (alat medis, monitor)
- Rawat Jalan (alat diagnostik)
- ICU/ICCU (ventilator, alat kritis)

**Total:** 10 permintaan

---

### 2. PENUNJANG MEDIS
**Tujuan:** Kepala Bidang Penunjang Medis (`kabid.penunjang@rsud.id`)

**Kriteria:**
- Peralatan laboratorium dan reagen
- Peralatan radiologi, kontras, film
- Peralatan farmasi (bukan obat)
- Alat diagnostik penunjang

**Unit yang mengajukan:**
- Laboratorium (reagen, alat habis pakai lab)
- Radiologi (kontras, film, chemical)
- Farmasi (lemari es khusus, freezer vaksin, rak penyimpanan)

**Total:** 4 permintaan

---

### 3. NON MEDIS
**Tujuan:** Kepala Bidang Keperawatan / Bagian Umum / Direktur

**Kriteria:**
- Peralatan IT dan administrasi
- Bahan makanan dan peralatan dapur
- Peralatan kebersihan dan sanitasi
- Linen dan laundry
- Peralatan pemeliharaan gedung

**Unit yang mengajukan:**
- Rekam Medis (komputer, filing cabinet)
- Gizi (bahan makanan, peralatan dapur)
- Sanitasi (alat kebersihan, maintenance)
- Laundry (mesin cuci, linen)
- Rawat Inap (linen pasien)

**Total:** 8 permintaan

---

## 🔄 ALUR PERSETUJUAN BERDASARKAN KLASIFIKASI

### Flow 1: MEDIS
```
Kepala Instalasi (unit)
    ↓
Kepala Bidang Pelayanan Medis
    ↓
Direktur
    ↓
Staff Perencanaan
```

### Flow 2: PENUNJANG MEDIS
```
Kepala Instalasi (unit)
    ↓
Kepala Bidang Penunjang Medis
    ↓
Direktur
    ↓
Staff Perencanaan
```

### Flow 3: NON MEDIS
```
Kepala Instalasi (unit)
    ↓
Kepala Bidang Keperawatan / Bagian Umum
    ↓
Direktur
    ↓
Staff Perencanaan
```

---

## 📋 DISTRIBUSI DATA (22 Permintaan)

### Per Klasifikasi:
- **MEDIS:** 10 permintaan (45%)
- **PENUNJANG MEDIS:** 4 permintaan (18%)
- **NON MEDIS:** 8 permintaan (36%)

### Per Kabid Tujuan:
- **Bidang Pelayanan Medis:** 10 permintaan
- **Bidang Penunjang Medis:** 4 permintaan
- **Bidang Keperawatan:** 1 permintaan
- **Bagian Umum:** 7 permintaan

---

## 🗃️ STRUKTUR DATABASE

### Kolom Baru di Tabel `permintaan`:

```sql
klasifikasi_permintaan ENUM('medis', 'non_medis', 'penunjang_medis') 
    DEFAULT 'medis'
    COMMENT 'Klasifikasi jenis barang'

kabid_tujuan VARCHAR(255) NULLABLE
    COMMENT 'Kepala Bidang tujuan berdasarkan klasifikasi'
```

### Migration File:
`2025_10_28_160031_add_klasifikasi_to_permintaan_table.php`

---

## 🚀 CARA MENGGUNAKAN

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Jalankan Seeder
```bash
# Hapus data lama
php artisan tinker --execute="DB::table('permintaan')->delete();"

# Jalankan seeder baru dengan klasifikasi
php artisan db:seed --class=AdminPermintaanKlasifikasiSeeder
```

### 3. Verifikasi Data
```bash
php artisan db:table permintaan
```

---

## 🔐 TESTING

### Login sebagai Kabid Pelayanan Medis:
```
Email: kabid.yanmed@rsud.id
Password: password
```
**Akan melihat:** 10 permintaan dengan `klasifikasi_permintaan = 'medis'`

### Login sebagai Kabid Penunjang Medis:
```
Email: kabid.penunjang@rsud.id
Password: password
```
**Akan melihat:** 4 permintaan dengan `klasifikasi_permintaan = 'penunjang_medis'`

### Login sebagai Kabid Keperawatan:
```
Email: kabid.keperawatan@rsud.id
Password: password
```
**Akan melihat:** 1 permintaan (linen rawat inap) dengan `klasifikasi_permintaan = 'non_medis'`

---

## 💡 CONTOH DATA

### Permintaan MEDIS (IGD):
```php
[
    'bidang' => 'Gawat Darurat',
    'klasifikasi_permintaan' => 'medis',
    'kabid_tujuan' => 'Bidang Pelayanan Medis',
    'deskripsi' => 'Permintaan alat emergency IGD: Defibrillator, Ventilator...',
    'no_nota_dinas' => 'ND/IGD/2025/M-001/X',
]
```

### Permintaan PENUNJANG MEDIS (Lab):
```php
[
    'bidang' => 'Laboratorium',
    'klasifikasi_permintaan' => 'penunjang_medis',
    'kabid_tujuan' => 'Bidang Penunjang Medis',
    'deskripsi' => 'Permintaan reagen lab: Reagen hematologi, kimia klinik...',
    'no_nota_dinas' => 'ND/LAB/2025/P-001/X',
]
```

### Permintaan NON MEDIS (Gizi):
```php
[
    'bidang' => 'Gizi',
    'klasifikasi_permintaan' => 'non_medis',
    'kabid_tujuan' => 'Bagian Umum',
    'deskripsi' => 'Permintaan bahan makanan: Beras, Gula, Minyak...',
    'no_nota_dinas' => 'ND/GIZI/2025/N-001/X',
]
```

---

## 📝 FORMAT NOMOR NOTA DINAS

Format: `ND/[UNIT]/2025/[KODE]-[NOMOR]/X`

**Kode Klasifikasi:**
- `M` = MEDIS
- `P` = PENUNJANG MEDIS
- `N` = NON MEDIS

**Contoh:**
- `ND/IGD/2025/M-001/X` - IGD, Medis, nomor 1
- `ND/LAB/2025/P-001/X` - Lab, Penunjang Medis, nomor 1
- `ND/GIZI/2025/N-001/X` - Gizi, Non Medis, nomor 1

---

## 🎯 IMPLEMENTASI DI CONTROLLER

### Kepala Bidang Controller
```php
public function index()
{
    $klasifikasi = $this->getKlasifikasiByRole(Auth::user()->role);
    
    $permintaans = Permintaan::where('klasifikasi_permintaan', $klasifikasi)
                              ->where('kabid_tujuan', Auth::user()->unit_kerja)
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);
    
    return view('kepala-bidang.permintaan.index', compact('permintaans'));
}

private function getKlasifikasiByRole($role)
{
    // Kabid Pelayanan Medis
    if (Auth::user()->unit_kerja == 'Bidang Pelayanan Medis') {
        return 'medis';
    }
    
    // Kabid Penunjang Medis
    if (Auth::user()->unit_kerja == 'Bidang Penunjang Medis') {
        return 'penunjang_medis';
    }
    
    // Kabid lainnya
    return 'non_medis';
}
```

---

## 📚 FILES CREATED

1. **Migration:** `database/migrations/2025_10_28_160031_add_klasifikasi_to_permintaan_table.php`
2. **Seeder:** `database/seeders/AdminPermintaanKlasifikasiSeeder.php`
3. **Documentation:** `klasifikasi_permintaan_PERMINTAAN.md`

---

## ✨ KEUNGGULAN SISTEM

1. **Routing Otomatis:** Permintaan otomatis diarahkan ke Kabid yang sesuai
2. **Clear Separation:** Jelas pembagian tanggung jawab per bidang
3. **Audit Trail:** Mudah tracking permintaan per klasifikasi
4. **Scalable:** Mudah menambah klasifikasi baru jika diperlukan
5. **Data Integrity:** Klasifikasi tersimpan di database, tidak hardcode

---

**Created:** 28 Oktober 2025  
**Status:** ✅ READY TO USE  
**Migration:** ✅ APPLIED  
**Seeder:** ✅ TESTED
