# Update: Dual Type Nota Dinas (Usulan & Pembelian)

## Status: ✅ SELESAI

## Overview

Staff Perencanaan sekarang bisa membuat 2 jenis Nota Dinas:

1. **Nota Dinas Usulan** - Untuk mengusulkan rencana pengadaan (tahap awal)
2. **Nota Dinas Pembelian** - Untuk proses pembelian yang sudah disetujui (tahap eksekusi)

## Perbedaan Kedua Jenis

### 📝 Nota Dinas Usulan

**Tujuan:** Mengajukan usulan/rencana pengadaan ke Direktur untuk mendapat persetujuan

**Fields:**
- ✅ Nomor Nota Dinas
- ✅ Kepada
- ✅ Dari
- ✅ Perihal
- ✅ Dasar
- ✅ Uraian
- ✅ Rekomendasi

**Contoh:**
```
NOTA DINAS USULAN

Nomor   : ND/PEREN/001/X/2025
Kepada  : Direktur
Dari    : Staff Perencanaan
Perihal : Rencana Pengadaan Alat Kesehatan IGD

Uraian:
- Analisis kebutuhan
- Estimasi anggaran
- Jadwal perencanaan

Rekomendasi:
- Mohon persetujuan rencana pengadaan
- Mohon alokasi anggaran
```

**Warna:** 🔵 Biru

---

### 🛒 Nota Dinas Pembelian

**Tujuan:** Instruksi untuk eksekusi pembelian yang sudah disetujui

**Fields:**
- ✅ Nomor Nota Dinas
- ✅ **Usulan Ruangan** ⭐ (Baru)
- ✅ **Sifat** ⭐ (Baru: Segera/Biasa/Sangat Segera)
- ✅ Kepada
- ✅ Dari
- ✅ Perihal
- ✅ Dasar
- ✅ Uraian
- ✅ Rekomendasi

**Contoh:**
```
NOTA DINAS PEMBELIAN

Nomor          : ND/PEMB/001/X/2025
Usulan Ruangan : IGD
Sifat          : Sangat Segera
Kepada         : Bagian Pengadaan
Dari           : Staff Perencanaan
Perihal        : Pembelian Alat Kesehatan IGD

Uraian:
- Spesifikasi detail alat
- Vendor yang sudah disetujui
- Timeline pengiriman

Rekomendasi:
- Segera lakukan proses pembelian
- Koordinasi dengan supplier
```

**Warna:** 🟢 Hijau

---

## UI/UX Design

### Pilihan Jenis Nota Dinas (Step 1)

```
┌──────────────────────────────────────────────────────┐
│   Jenis Nota Dinas *                                 │
├──────────────────────────────────────────────────────┤
│                                                      │
│  ┌──────────────────┐  ┌──────────────────┐        │
│  │    📄 [Icon]     │  │    🛒 [Icon]     │        │
│  │                  │  │                  │        │
│  │ Nota Dinas      │  │ Nota Dinas       │        │
│  │ Usulan          │  │ Pembelian        │        │
│  │                  │  │                  │        │
│  │ Untuk mengusul- │  │ Untuk proses     │        │
│  │ kan rencana     │  │ pembelian yang   │        │
│  │ pengadaan       │  │ disetujui        │        │
│  └──────────────────┘  └──────────────────┘        │
│     [Active: Blue]      [Active: Green]            │
└──────────────────────────────────────────────────────┘
```

### Form Fields (Step 2)

**Fields Umum (Kedua Jenis):**
- Tujuan Nota Dinas
- Tanggal
- Nomor Nota Dinas
- Kepada
- Dari
- Perihal
- Dasar (optional)
- Uraian
- Rekomendasi
- Penutup
- TTD

**Fields Khusus Pembelian (Conditional):**

```
┌──────────────────────────────────────────────────────┐
│   Informasi Pembelian                                │
│   [Background: Green-50]                             │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Usulan Ruangan *        Sifat *                    │
│  [IGD              ]     [Sangat Segera  ▼]        │
│                                                      │
│  Options Sifat:                                      │
│  - Biasa                                            │
│  - Segera                                            │
│  - Sangat Segera                                     │
└──────────────────────────────────────────────────────┘
```

## Implementation Details

### Component Updated

**File:** `resources/js/Components/GenerateNotaDinas.vue`

### New State Variables

```javascript
const jenisNotaDinas = ref('usulan'); // 'usulan' or 'pembelian'

const form = reactive({
    // ... existing fields
    usulan_ruangan: '', // Khusus pembelian
    sifat: '',          // Khusus pembelian
});
```

### Conditional Rendering

```vue
<!-- Pilih Jenis -->
<div class="grid grid-cols-2 gap-4">
    <button @click="jenisNotaDinas = 'usulan'" 
            :class="jenisNotaDinas === 'usulan' ? 'border-blue-500 bg-blue-50' : ''">
        Nota Dinas Usulan
    </button>
    <button @click="jenisNotaDinas = 'pembelian'"
            :class="jenisNotaDinas === 'pembelian' ? 'border-green-500 bg-green-50' : ''">
        Nota Dinas Pembelian
    </button>
</div>

<!-- Fields Conditional -->
<div v-if="jenisNotaDinas === 'pembelian'" class="bg-green-50">
    <input v-model="form.usulan_ruangan" required />
    <select v-model="form.sifat" required>
        <option value="Biasa">Biasa</option>
        <option value="Segera">Segera</option>
        <option value="Sangat Segera">Sangat Segera</option>
    </select>
</div>
```

### Dynamic Title & Header

```javascript
// Title dinamis
const title = jenisNotaDinas.value === 'pembelian' 
    ? 'NOTA DINAS PEMBELIAN' 
    : 'NOTA DINAS USULAN';

// Header table conditional
if (jenisNotaDinas.value === 'pembelian') {
    headerTable += `
        <tr><td>Usulan Ruangan</td><td>:</td><td>${form.usulan_ruangan}</td></tr>
        <tr><td>Sifat</td><td>:</td><td><strong>${form.sifat}</strong></td></tr>
    `;
}
```

### Color Coding for Sifat

```javascript
const sifatColor = {
    'Sangat Segera': '#dc2626', // Red
    'Segera': '#ea580c',        // Orange
    'Biasa': '#000'             // Black
};

style="color: ${sifatColor[form.sifat]}"
```

## Workflow

### Scenario 1: Nota Dinas Usulan

```
Step 1: Staff Perencanaan terima permintaan dari unit
         ↓
Step 2: Analisis kebutuhan & estimasi budget
         ↓
Step 3: Buat Nota Dinas USULAN ke Direktur
         ↓
Step 4: Tunggu approval Direktur
         ↓
Step 5: Jika disetujui → lanjut ke Nota Dinas Pembelian
```

**Nota Dinas Usulan berisi:**
- Analisis kebutuhan
- Justifikasi pengadaan
- Estimasi anggaran
- Rencana timeline
- **Minta persetujuan prinsip**

---

### Scenario 2: Nota Dinas Pembelian

```
Step 1: Nota Dinas Usulan sudah disetujui Direktur
         ↓
Step 2: Budget sudah dialokasikan
         ↓
Step 3: Buat Nota Dinas PEMBELIAN ke Bagian Pengadaan
         ↓
Step 4: Bagian Pengadaan eksekusi pembelian
         ↓
Step 5: Tracking sampai barang diterima
```

**Nota Dinas Pembelian berisi:**
- Spesifikasi detail (sudah final)
- Vendor/supplier (sudah dipilih)
- Nominal budget pasti
- Timeline delivery
- **Instruksi untuk segera eksekusi**

---

## Field Mapping

| Field | Usulan | Pembelian | Required | Notes |
|-------|--------|-----------|----------|-------|
| Nomor | ✅ | ✅ | Yes | Format berbeda: ND/PEREN/... vs ND/PEMB/... |
| **Usulan Ruangan** | ❌ | ✅ | Yes | Khusus pembelian |
| **Sifat** | ❌ | ✅ | Yes | Segera/Biasa/Sangat Segera |
| Kepada | ✅ | ✅ | Yes | Usulan→Direktur, Pembelian→Pengadaan |
| Dari | ✅ | ✅ | Yes | Selalu Staff Perencanaan |
| Perihal | ✅ | ✅ | Yes | Topik singkat |
| Dasar | ✅ | ✅ | No | Regulasi/SPT/Rapat |
| Uraian | ✅ | ✅ | Yes | Usulan: analisis, Pembelian: spesifikasi |
| Rekomendasi | ✅ | ✅ | Yes | Usulan: minta approval, Pembelian: instruksi |
| Penutup | ✅ | ✅ | No | Default text |
| TTD | ✅ | ✅ | Yes | Nama & Jabatan |

## Format Nomor

### Usulan
```
ND/PEREN/001/X/2025
ND/PEREN/002/X/2025
ND/PEREN/003/XI/2025
```

- ND = Nota Dinas
- PEREN = Perencanaan
- 001 = Nomor urut
- X = Bulan Romawi (Oktober)
- 2025 = Tahun

### Pembelian
```
ND/PEMB/001/X/2025
ND/PEMB/002/X/2025
ND/PEMB/003/XI/2025
```

- ND = Nota Dinas
- PEMB = Pembelian
- 001 = Nomor urut
- X = Bulan Romawi (Oktober)
- 2025 = Tahun

## Download Filename

**Format:**
```
Nota_Dinas_[Jenis]_[Nomor]_[Timestamp].html
```

**Contoh:**
- `Nota_Dinas_Usulan_ND_PEREN_001_X_2025_1729497600000.html`
- `Nota_Dinas_Pembelian_ND_PEMB_001_X_2025_1729497600000.html`

## Preview Feature

**Preview menampilkan:**
- Title sesuai jenis (USULAN / PEMBELIAN)
- Header table dengan fields conditional
- Sifat dengan color coding (jika pembelian)
- Full content preview sebelum download

**Button Preview:**
```
[ Preview ] [ Generate & Download ]
```

## Validation Rules

### Nota Dinas Usulan
```javascript
required: [
    'tujuan',
    'tanggal',
    'nomor',
    'kepada',
    'perihal',
    'uraian',
    'rekomendasi',
    'ttd_nama',
    'ttd_jabatan'
]

optional: [
    'dasar',
    'penutup' // ada default value
]
```

### Nota Dinas Pembelian
```javascript
required: [
    // All from Usulan +
    'usulan_ruangan', // Tambahan
    'sifat'           // Tambahan
]
```

## Contoh Use Case

### Use Case 1: Request Baru dari IGD

**Phase 1 - Nota Dinas Usulan:**
```
Staff Perencanaan:
1. Terima permintaan IGD untuk 5 Monitor Pasien
2. Analisis kebutuhan
3. Buat Nota Dinas USULAN
   - Nomor: ND/PEREN/015/X/2025
   - Kepada: Direktur
   - Perihal: Rencana Pengadaan Monitor Pasien IGD
   - Uraian: Analisis kebutuhan + estimasi budget
   - Rekomendasi: Mohon persetujuan & alokasi budget

Direktur:
4. Review & Approve Nota Dinas Usulan
5. Alokasikan budget
```

**Phase 2 - Nota Dinas Pembelian:**
```
Staff Perencanaan:
6. Nota Dinas Usulan sudah disetujui
7. Buat Nota Dinas PEMBELIAN
   - Nomor: ND/PEMB/008/X/2025
   - Usulan Ruangan: IGD
   - Sifat: Sangat Segera
   - Kepada: Bagian Pengadaan
   - Perihal: Pembelian Monitor Pasien IGD
   - Uraian: Spesifikasi detail + vendor + budget final
   - Rekomendasi: Segera proses pembelian

Bagian Pengadaan:
8. Eksekusi pembelian
9. Koordinasi dengan vendor
10. Track delivery
```

---

### Use Case 2: Budget Rutin Tahunan

**Nota Dinas Usulan:**
```
Nomor   : ND/PEREN/020/I/2026
Sifat   : -
Kepada  : Direktur
Perihal : Rencana Pengadaan Alat Kesehatan Tahun 2026

Uraian:
- Daftar kebutuhan 12 unit
- Estimasi total Rp 2.5M
- Jadwal per quarter

Rekomendasi:
- Approve RKAP 2026
- Alokasi budget di Q1
```

**Nota Dinas Pembelian (Q1):**
```
Nomor          : ND/PEMB/003/III/2026
Usulan Ruangan : Poli Umum, IGD, Rawat Inap
Sifat          : Biasa
Kepada         : Bagian Pengadaan
Perihal        : Pembelian Batch Q1 - Alat Kesehatan

Uraian:
- Item batch Q1: 4 unit (sesuai RKAP)
- Vendor: PT XYZ (sudah approval)
- Budget: Rp 800K

Rekomendasi:
- Eksekusi sesuai jadwal Q1
- Target delivery akhir Maret
```

## Testing Checklist

### UI Testing:
- [x] Button pilih jenis berfungsi
- [x] Active state correct (blue/green)
- [x] Fields conditional muncul saat pembelian dipilih
- [x] Fields hidden saat usulan dipilih
- [x] Validation required fields
- [x] Color coding sifat correct

### Functional Testing:
- [x] Generate Nota Dinas Usulan
- [x] Generate Nota Dinas Pembelian
- [x] Preview usulan correct
- [x] Preview pembelian correct (with extra fields)
- [x] Download filename correct per jenis
- [x] HTML output valid
- [x] Print-ready format

### Data Validation:
- [x] Required fields validated
- [x] Usulan ruangan required only for pembelian
- [x] Sifat required only for pembelian
- [x] Form reset after submit
- [x] Modal close after download

## Benefits

### ✅ Clear Separation of Stages
- **Usulan** = Planning phase (request approval)
- **Pembelian** = Execution phase (do the purchase)

### ✅ Better Tracking
- Different nomor format (PEREN vs PEMB)
- Clear audit trail
- Easy to identify document type

### ✅ Context-Specific Fields
- Usulan fokus pada justifikasi & approval
- Pembelian fokus pada eksekusi & urgency

### ✅ Priority Management
- Sifat field untuk prioritas pembelian
- Color coding untuk visual cue
- Urgent items lebih terlihat (red)

### ✅ Room/Department Attribution
- Usulan Ruangan untuk tracking asal permintaan
- Memudahkan koordinasi dengan unit terkait

## Summary

**Changes Made:**
- ✅ Tambah pilihan jenis Nota Dinas (Usulan vs Pembelian)
- ✅ Tambah field "Usulan Ruangan" untuk pembelian
- ✅ Tambah field "Sifat" (Segera/Biasa/Sangat Segera) untuk pembelian
- ✅ Conditional rendering fields berdasarkan jenis
- ✅ Dynamic title & header table
- ✅ Color coding untuk sifat
- ✅ Different filename per jenis
- ✅ Watch jenisNotaDinas untuk auto-reset fields

**Workflow:**
```
Permintaan Unit → 
  Nota Dinas USULAN (ke Direktur) → 
    Approval → 
      Nota Dinas PEMBELIAN (ke Pengadaan) → 
        Eksekusi Pembelian → 
          Delivery → 
            Selesai
```

**Ready to Use! 🎉**

Staff Perencanaan sekarang bisa membuat 2 jenis Nota Dinas sesuai tahapan proses pengadaan!
