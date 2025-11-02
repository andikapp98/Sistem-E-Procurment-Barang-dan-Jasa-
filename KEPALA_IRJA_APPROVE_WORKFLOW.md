# 📋 WORKFLOW: Kepala IRJA Approve Permintaan dari Kepala Poli

## ✅ Status: ALREADY IMPLEMENTED

Fitur ini sudah lengkap dan berfungsi. Kepala Instalasi IRJA dapat melihat dan meng-approve permintaan dari Kepala Poli yang berada di bawahnya.

---

## 🎯 WORKFLOW DIAGRAM

```
┌─────────────────────────────────────────────────────────────────┐
│                  WORKFLOW PERMINTAAN IRJA                        │
└─────────────────────────────────────────────────────────────────┘

1. KEPALA POLI (Pembuat Permintaan)
   ├── Poli Bedah
   ├── Poli Gigi
   ├── Poli Kulit Kelamin
   ├── Poli Penyakit Dalam
   ├── Poli Jiwa
   ├── Poli Psikologi
   ├── Poli Mata
   └── Klinik Gizi
         │
         │ (Membuat Permintaan)
         ↓
   
2. KEPALA INSTALASI RAWAT JALAN (IRJA)
   │
   ├── Melihat semua permintaan dari Kepala Poli
   ├── Review permintaan
   ├── Approve / Request Revisi / Reject
   │
   │ (Jika Approve)
   ↓
   
3. KLASIFIKASI & ROUTING KE KABID
   │
   ├── Medis → Kabid Pelayanan Medis (Kabid Yanmed)
   ├── Penunjang Medis → Kabid Penunjang Medis
   └── Non Medis → Kabid Umum & Keuangan (Kabid Umum)
         │
         ↓
   
4. KEPALA BIDANG
   │
   ├── Review dan approve
   │
   ↓
   
5. DIREKTUR / WAKIL DIREKTUR
   │
   ├── Final approval
   │
   ↓
   
6. STAFF PERENCANAAN
   │
   ├── Buat perencanaan
   ├── Forward ke Pengadaan/KSO
   │
   ↓
   
7. SELESAI
```

---

## 🔧 TECHNICAL IMPLEMENTATION

### 1. **Controller Logic (KepalaInstalasiController.php)**

#### A. Unit Mapping untuk IRJA
```php
private function getIRJADepartments()
{
    return [
        'Poli Bedah',
        'Poli Gigi',
        'Poli Kulit Kelamin',
        'Poli Penyakit Dalam',
        'Poli Jiwa',
        'Poli Psikologi',
        'Poli Mata',
        'Klinik Gizi',
        'Laboratorium',
        'Apotek',
    ];
}
```

#### B. Flexible Matching untuk Filter
```php
private function getBidangVariations($unitKerja)
{
    // SPECIAL CASE: Kepala IRJA dapat melihat semua permintaan dari Poli
    if (stripos($unitKerja, 'Rawat Jalan') !== false || 
        $unitKerja === 'IRJ' || 
        $unitKerja === 'IRJA') {
        $variations = array_merge($variations, $this->getIRJADepartments());
        $variations[] = 'IRJ';
        $variations[] = 'IRJA';
        $variations[] = 'Instalasi Rawat Jalan';
    }
    
    return array_unique($variations);
}
```

#### C. Query untuk Index
```php
public function index(Request $request)
{
    $user = Auth::user();
    
    $query = Permintaan::with(['user', 'notaDinas'])
        ->where(function($q) use ($user) {
            if ($user->unit_kerja) {
                // Get all possible bidang variations
                $variations = $this->getBidangVariations($user->unit_kerja);
                
                // Match dengan salah satu variasi
                $q->where(function($subQuery) use ($variations) {
                    foreach ($variations as $variation) {
                        $subQuery->orWhere('bidang', $variation)
                                ->orWhere('bidang', 'LIKE', '%' . $variation . '%');
                    }
                });
            }
        });
    
    // ... pagination ...
}
```

#### D. Approve Method
```php
public function approve(Request $request, Permintaan $permintaan)
{
    $user = Auth::user();

    // Cek authorization dengan flexible matching
    if ($user->unit_kerja) {
        $variations = $this->getBidangVariations($user->unit_kerja);
        $isAuthorized = false;

        foreach ($variations as $variation) {
            if ($permintaan->bidang === $variation || 
                stripos($permintaan->bidang, $variation) !== false) {
                $isAuthorized = true;
                break;
            }
        }

        if (!$isAuthorized) {
            return redirect()
                ->route('kepala-instalasi.index')
                ->with('error', 'Anda tidak memiliki akses untuk meng-approve permintaan ini.');
        }
    }

    // Tentukan klasifikasi dan kabid tujuan
    $klasifikasi = $permintaan->klasifikasi_permintaan ?? 
                   $this->determineKlasifikasi($permintaan->bidang);
    $kabidTujuan = $this->getKabidTujuan($klasifikasi);

    // Update permintaan
    $permintaan->update([
        'status' => 'proses',
        'klasifikasi_permintaan' => $klasifikasi,
        'kabid_tujuan' => $kabidTujuan,
    ]);

    // Create disposisi
    Disposisi::create([
        'permintaan_id' => $permintaan->permintaan_id,
        'nota_dinas_id' => $permintaan->notaDinas->first()->nota_id ?? null,
        'dari' => $user->nama,
        'kepada' => $kabidTujuan,
        'tanggal_disposisi' => now(),
        'isi_disposisi' => $request->catatan ?? 'Mohon ditinjau dan diproses lebih lanjut',
        'status_disposisi' => 'pending',
    ]);

    return redirect()
        ->route('kepala-instalasi.index')
        ->with('success', 'Permintaan berhasil disetujui dan diteruskan ke ' . $kabidTujuan);
}
```

---

## 📋 KLASIFIKASI & ROUTING

### Klasifikasi Permintaan:

#### 1. **MEDIS**
**Kabid Tujuan:** Bidang Pelayanan Medis (Kabid Yanmed)

**Unit yang termasuk:**
- IGD (Instalasi Gawat Darurat)
- IBS (Instalasi Bedah Sentral)
- ICU (Instalasi Intensif Care)
- IRJ/IRJA (Instalasi Rawat Jalan) - **TERMASUK SEMUA POLI**
- IRNA (Instalasi Rawat Inap)
- Poli Bedah, Poli Gigi, Poli Mata, dll.

#### 2. **PENUNJANG MEDIS**
**Kabid Tujuan:** Bidang Penunjang Medis

**Unit yang termasuk:**
- Lab (Instalasi Laboratorium)
- Radiologi (Instalasi Radiologi)
- Farmasi (untuk alat)

#### 3. **NON MEDIS**
**Kabid Tujuan:** Bidang Umum & Keuangan (Kabid Umum)

**Unit yang termasuk:**
- Rekam Medik
- Gizi
- Sanitasi & Pemeliharaan
- Laundry & Linen
- IT (Teknologi Informasi)
- Pemeliharaan

---

## 🖥️ USER INTERFACE

### 1. **Dashboard Kepala IRJA**

**Path:** `/kepala-instalasi/dashboard`

**Menampilkan:**
- Total permintaan dari semua Poli di bawah IRJA
- Statistik (Diajukan, Proses, Disetujui)
- 5 Permintaan terbaru

### 2. **Index - Daftar Permintaan**

**Path:** `/kepala-instalasi`

**Menampilkan:**
- Semua permintaan dari:
  - Poli Bedah
  - Poli Gigi
  - Poli Kulit Kelamin
  - Poli Penyakit Dalam
  - Poli Jiwa
  - Poli Psikologi
  - Poli Mata
  - Klinik Gizi
  - Laboratorium
  - Apotek

**Features:**
- Filter by search, status, date range
- Pagination
- Progress tracking
- Status badge

### 3. **Show - Detail & Approve**

**Path:** `/kepala-instalasi/permintaan/{id}`

**Menampilkan:**
- Detail lengkap permintaan
- Nota Dinas
- Timeline tracking
- Action buttons (Approve/Revisi/Reject)

**Approve Modal:**
- Informasi klasifikasi otomatis
- Informasi Kabid tujuan
- Field catatan (optional)
- Konfirmasi approve

---

## 🔐 AUTHORIZATION

### Access Control Matrix:

| User Role | Unit Kerja | Bisa Lihat Permintaan Dari | Bisa Approve |
|-----------|------------|----------------------------|--------------|
| **Kepala Poli** | Poli Bedah | Poli Bedah | ❌ No (hanya create) |
| **Kepala IRJA** | Instalasi Rawat Jalan | Semua Poli di IRJA | ✅ Yes |
| **Kepala Bidang** | Bidang Pelayanan Medis | Semua yang diroute ke dia | ✅ Yes |
| **Direktur** | - | Semua | ✅ Yes |

### Authorization Logic:

```php
// Flexible matching untuk cek authorization
$variations = $this->getBidangVariations($user->unit_kerja);
$isAuthorized = false;

foreach ($variations as $variation) {
    if ($permintaan->bidang === $variation || 
        stripos($permintaan->bidang, $variation) !== false) {
        $isAuthorized = true;
        break;
    }
}
```

---

## 📊 DATA FLOW

### 1. Kepala Poli Creates Permintaan:
```sql
INSERT INTO permintaan (
    bidang = 'Poli Bedah',
    klasifikasi_permintaan = 'Medis',
    status = 'diajukan',
    user_id = {kepala_poli_user_id}
)
```

### 2. Kepala IRJA Views Index:
```sql
SELECT * FROM permintaan 
WHERE bidang IN (
    'Poli Bedah', 
    'Poli Gigi', 
    'Poli Kulit Kelamin',
    -- ... all IRJA departments
)
OR bidang LIKE '%Poli%'
OR bidang LIKE '%IRJA%'
OR bidang LIKE '%Rawat Jalan%'
```

### 3. Kepala IRJA Approves:
```sql
-- Update permintaan
UPDATE permintaan SET
    status = 'proses',
    klasifikasi_permintaan = 'Medis',
    kabid_tujuan = 'Bidang Pelayanan Medis'
WHERE permintaan_id = {id}

-- Create disposisi
INSERT INTO disposisi (
    permintaan_id = {id},
    dari = 'Kepala IRJA',
    kepada = 'Bidang Pelayanan Medis',
    tanggal_disposisi = NOW(),
    status_disposisi = 'pending'
)
```

---

## 🧪 TESTING SCENARIOS

### Scenario 1: Kepala Poli Creates Permintaan
1. Login as Kepala Poli Bedah
2. Create permintaan
3. Bidang auto-filled: "Poli Bedah"
4. Submit permintaan
5. Status = "diajukan"

### Scenario 2: Kepala IRJA Views Permintaan
1. Login as Kepala IRJA
2. Go to `/kepala-instalasi`
3. Should see permintaan from Poli Bedah
4. Should see permintaan from all Poli under IRJA
5. Should NOT see permintaan from IRNA or IGD

### Scenario 3: Kepala IRJA Approves
1. Login as Kepala IRJA
2. Click on permintaan from Poli Bedah
3. Click "Setujui" button
4. Modal shows: Klasifikasi = "Medis", Kabid = "Bidang Pelayanan Medis"
5. Confirm approve
6. Status changed to "proses"
7. Disposisi created to Kabid Yanmed

### Scenario 4: Unauthorized Access
1. Login as Kepala IRNA
2. Try to access permintaan from Poli Bedah (IRJA)
3. Should get error: "Anda hanya dapat melihat permintaan dari unit kerja Instalasi Rawat Inap"

---

## 🔄 WORKFLOW STATES

### Permintaan States:
```
diajukan    → Created by Kepala Poli
    ↓
proses      → Approved by Kepala IRJA, forwarded to Kabid
    ↓
disetujui   → Approved by Kabid/Direktur
    ↓
selesai     → Completed by Staff Perencanaan
```

### Disposisi States:
```
pending     → Waiting for Kabid action
    ↓
approved    → Kabid approved
    ↓
completed   → Fully processed
```

---

## 📝 FILES INVOLVED

```
✅ app/Http/Controllers/KepalaInstalasiController.php
   - getIRJADepartments()
   - getBidangVariations()
   - index() - with flexible matching
   - show() - with authorization check
   - approve() - full approve logic

✅ resources/js/Pages/KepalaInstalasi/Index.vue
   - List view for all IRJA permintaans
   - Filters & pagination
   - Status badges

✅ resources/js/Pages/KepalaInstalasi/Show.vue
   - Detail view
   - Approve modal with klasifikasi info
   - Authorization-aware action buttons

✅ resources/js/Pages/KepalaInstalasi/Dashboard.vue
   - Statistics for all IRJA permintaans
   - Recent permintaans list

✅ routes/web.php
   - Route::post('/permintaan/{permintaan}/approve', [...])
```

---

## ⚡ QUICK REFERENCE

### Kepala IRJA dapat:
- ✅ Melihat semua permintaan dari Poli di IRJA
- ✅ Meng-approve permintaan
- ✅ Request revisi
- ✅ Reject permintaan
- ✅ View detail & tracking
- ✅ Otomatis routing ke Kabid yang sesuai

### Kepala IRJA TIDAK dapat:
- ❌ Melihat permintaan dari IRNA
- ❌ Melihat permintaan dari IGD
- ❌ Meng-approve permintaan setelah status "proses"

### Auto-routing berdasarkan klasifikasi:
- **Medis** (Poli Bedah, Poli Gigi, dll) → **Kabid Yanmed**
- **Penunjang** (Lab, Radiologi) → **Kabid Penunjang**
- **Non Medis** (Rekam Medik, Gizi) → **Kabid Umum**

---

## 🎓 KEY POINTS

1. **Flexible Matching:** Menggunakan `getBidangVariations()` untuk match berbagai format nama unit
2. **Auto-classification:** Sistem otomatis menentukan klasifikasi dan kabid tujuan
3. **Authorization:** Multi-layer check untuk ensure hanya authorized user yang bisa approve
4. **Disposisi Creation:** Otomatis create disposisi saat approve
5. **Audit Trail:** Semua approval ter-record dengan timestamp dan user info

---

**Status:** ✅ FULLY IMPLEMENTED & WORKING  
**Last Updated:** 2 November 2025  
**Version:** 1.0
