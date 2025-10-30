# Fix Alur Workflow Pengadaan - Disposisi Balik dari Direktur ke Kepala Bidang

## Status: ✅ SELESAI

## Masalah
Alur workflow tidak sesuai kebutuhan:
- **Alur Lama:** Admin → Kepala Instalasi → Kepala Bidang → Direktur → **Staff Perencanaan** (langsung)
- **Alur yang Diinginkan:** Admin → Kepala Instalasi → Kepala Bidang → Direktur → **Kepala Bidang** (disposisi balik) → Staff Perencanaan

## Solusi Diterapkan

### 1. Perbaikan DirekturController (`app/Http/Controllers/DirekturController.php`)

**Method `approve()` - Disposisi Balik ke Kepala Bidang**

**Sebelum:**
```php
// Buat disposisi otomatis ke Staff Perencanaan
Disposisi::create([
    'nota_id' => $notaDinas->nota_id,
    'jabatan_tujuan' => 'Staff Perencanaan',
    'tanggal_disposisi' => Carbon::now(),
    'catatan' => $data['catatan'] ?? 'Disetujui oleh Direktur (Final Approval). Silakan lakukan perencanaan pengadaan.',
    'status' => 'disetujui',
]);

// Update status permintaan - teruskan ke Staff Perencanaan
$permintaan->update([
    'status' => 'disetujui',
    'pic_pimpinan' => 'Staff Perencanaan',
]);
```

**Sesudah:**
```php
// Buat disposisi BALIK ke Kepala Bidang
Disposisi::create([
    'nota_id' => $notaDinas->nota_id,
    'jabatan_tujuan' => 'Kepala Bidang',
    'tanggal_disposisi' => Carbon::now(),
    'catatan' => $data['catatan'] ?? 'Disetujui oleh Direktur (Final Approval). Silakan disposisi ke Staff Perencanaan untuk perencanaan pengadaan.',
    'status' => 'disetujui',
]);

// Update status permintaan - kembalikan ke Kepala Bidang
$permintaan->update([
    'status' => 'disetujui',
    'pic_pimpinan' => 'Kepala Bidang',
]);
```

### 2. Update KepalaBidangController (`app/Http/Controllers/KepalaBidangController.php`)

#### A. Method `approve()` - Dual Scenario Handling

Sekarang method ini menangani 2 skenario berbeda:

**Skenario 1: Permintaan Baru dari Kepala Instalasi**
- Cek: Belum ada disposisi dari Direktur
- Action: Teruskan ke Direktur
- Status: `proses`

**Skenario 2: Disposisi Balik dari Direktur**
- Cek: Ada disposisi dari Direktur ke Kepala Bidang dengan status `disetujui`
- Action: Teruskan ke Staff Perencanaan
- Status: `disetujui`

```php
public function approve(Request $request, Permintaan $permintaan)
{
    // ... validasi ...
    
    // Cek apakah ini disposisi balik dari Direktur
    $disposisiDariDirektur = Disposisi::where('nota_id', $notaDinas->nota_id)
        ->where('jabatan_tujuan', 'Kepala Bidang')
        ->where('status', 'disetujui')
        ->whereHas('notaDinas', function($q) {
            $q->whereHas('disposisi', function($query) {
                $query->where('jabatan_tujuan', 'Direktur');
            });
        })
        ->exists();

    // Skenario 1: Disposisi balik dari Direktur
    if ($disposisiDariDirektur || $permintaan->status === 'disetujui') {
        // Teruskan ke Staff Perencanaan
        Disposisi::create([
            'nota_id' => $notaDinas->nota_id,
            'jabatan_tujuan' => 'Staff Perencanaan',
            // ...
        ]);
        
        $permintaan->update([
            'status' => 'disetujui',
            'pic_pimpinan' => 'Staff Perencanaan',
        ]);
        
        return redirect()->with('success', 'Permintaan diteruskan ke Staff Perencanaan');
    }

    // Skenario 2: Permintaan baru
    // Teruskan ke Direktur
    Disposisi::create([
        'nota_id' => $notaDinas->nota_id,
        'jabatan_tujuan' => 'Direktur',
        // ...
    ]);
    
    $permintaan->update([
        'status' => 'proses',
        'pic_pimpinan' => 'Direktur',
    ]);
    
    return redirect()->with('success', 'Permintaan diteruskan ke Direktur');
}
```

#### B. Method `show()` - Pass Indicator to View

Menambahkan flag `isDisposisiDariDirektur` untuk view:

```php
public function show(Permintaan $permintaan)
{
    // ... load relations ...
    
    // Cek apakah ini disposisi balik dari Direktur
    $notaDinas = $permintaan->notaDinas()->latest('tanggal_nota')->first();
    $isDisposisiDariDirektur = false;
    
    if ($notaDinas) {
        $isDisposisiDariDirektur = Disposisi::where('nota_id', $notaDinas->nota_id)
            ->where('jabatan_tujuan', 'Kepala Bidang')
            ->where('status', 'disetujui')
            ->whereHas('notaDinas', function($q) {
                $q->whereHas('disposisi', function($query) {
                    $query->where('jabatan_tujuan', 'Direktur');
                });
            })
            ->exists();
    }
    
    return Inertia::render('KepalaBidang/Show', [
        'permintaan' => $permintaan,
        // ... other data ...
        'isDisposisiDariDirektur' => $isDisposisiDariDirektur || $permintaan->status === 'disetujui',
    ]);
}
```

### 3. Update View (`resources/js/Pages/KepalaBidang/Show.vue`)

#### A. Action Buttons Section

**Perubahan:**
- Tampilkan info box hijau jika disposisi dari Direktur
- Ubah text tombol berdasarkan skenario
- Sembunyikan tombol "Tolak" dan "Revisi" jika sudah disetujui Direktur

```vue
<!-- Action Buttons -->
<div v-if="permintaan.status === 'proses' || permintaan.status === 'disetujui'">
    <!-- Info Box Disposisi dari Direktur -->
    <div v-if="isDisposisiDariDirektur" class="bg-green-50 border border-green-200">
        Disposisi dari Direktur: Permintaan sudah disetujui Direktur. 
        Silakan teruskan ke Staff Perencanaan.
    </div>
    
    <button @click="showApproveModal = true">
        {{ isDisposisiDariDirektur ? 'Teruskan ke Staff Perencanaan' : 'Setujui' }}
    </button>
    
    <!-- Reject & Revisi - Hanya untuk permintaan baru -->
    <button v-if="!isDisposisiDariDirektur" @click="showRejectModal = true">
        Tolak
    </button>
    
    <button v-if="!isDisposisiDariDirektur" @click="showRevisiModal = true">
        Minta Revisi
    </button>
</div>
```

#### B. Approve Modal

**Perubahan:**
- Dynamic title dan message berdasarkan skenario
- Dynamic button text

```vue
<Modal :show="showApproveModal">
    <h3>
        {{ isDisposisiDariDirektur ? 'Teruskan ke Staff Perencanaan' : 'Setujui Permintaan' }}
    </h3>
    
    <div :class="isDisposisiDariDirektur ? 'bg-green-50' : 'bg-purple-50'">
        <span v-if="isDisposisiDariDirektur">
            Permintaan sudah disetujui Direktur. 
            Akan diteruskan ke <strong>Staff Perencanaan</strong>
        </span>
        <span v-else>
            Permintaan akan diteruskan ke <strong>Direktur</strong>
        </span>
    </div>
    
    <button type="submit">
        {{ isDisposisiDariDirektur ? 'Teruskan ke Staff Perencanaan' : 'Setujui & Teruskan ke Direktur' }}
    </button>
</Modal>
```

#### C. Script Setup

Menambahkan prop baru:

```javascript
const props = defineProps({
    permintaan: Object,
    trackingStatus: String,
    timeline: Array,
    progress: Number,
    isDisposisiDariDirektur: Boolean, // ← Baru
});
```

## Alur Workflow Lengkap

### ✅ Alur Baru (Sesuai Kebutuhan)

```
1. Admin → Membuat Permintaan
   ↓
2. Kepala Instalasi → Review & Buat Nota Dinas
   ↓
3. Kepala Bidang (Pertama) → Approve & Disposisi ke Direktur
   ↓
4. Direktur → Final Approval & Disposisi BALIK ke Kepala Bidang
   ↓
5. Kepala Bidang (Kedua) → Disposisi ke Staff Perencanaan
   ↓
6. Staff Perencanaan → Buat Perencanaan & Lanjut ke KSO/Pengadaan
```

### Penjelasan Tahapan

**Kepala Bidang (Pertama kali):**
- Status permintaan: `proses`
- Button: "Setujui"
- Action: Buat disposisi ke Direktur
- Update pic_pimpinan: `Direktur`

**Direktur:**
- Status permintaan: `proses`
- Button: "Setujui (Final)"
- Action: Buat disposisi BALIK ke Kepala Bidang
- Update status: `disetujui`
- Update pic_pimpinan: `Kepala Bidang`

**Kepala Bidang (Kedua kali - Disposisi Balik):**
- Status permintaan: `disetujui`
- Button: "Teruskan ke Staff Perencanaan"
- Info box: Hijau, "Disposisi dari Direktur"
- Tombol "Tolak" dan "Revisi": TIDAK DITAMPILKAN
- Action: Buat disposisi ke Staff Perencanaan
- Update pic_pimpinan: `Staff Perencanaan`

## Status dan PIC Changes

| Tahapan | Status Before | PIC Before | Status After | PIC After |
|---------|--------------|------------|--------------|-----------|
| Kepala Instalasi Approve | diajukan | Admin | proses | Kepala Bidang |
| Kepala Bidang Approve (1st) | proses | Kepala Bidang | proses | Direktur |
| Direktur Approve | proses | Direktur | **disetujui** | **Kepala Bidang** |
| Kepala Bidang Approve (2nd) | disetujui | Kepala Bidang | disetujui | Staff Perencanaan |
| Staff Perencanaan | disetujui | Staff Perencanaan | disetujui | KSO/Pengadaan |

## Testing Workflow

### Test Case 1: Permintaan Baru (Happy Path)
1. Login sebagai Kepala Bidang
2. Buka permintaan dengan status `proses` dari Kepala Instalasi
3. Verify: Tombol "Setujui", "Tolak", "Revisi" muncul
4. Verify: Modal text: "Permintaan akan diteruskan ke Direktur"
5. Klik "Setujui"
6. Verify: Redirect dengan message "diteruskan ke Direktur"
7. Verify DB: pic_pimpinan = 'Direktur', status = 'proses'

### Test Case 2: Disposisi Balik dari Direktur
1. Login sebagai Direktur
2. Buka permintaan dengan status `proses`
3. Klik "Setujui (Final)"
4. Verify: Redirect dengan message "didisposisi balik ke Kepala Bidang"
5. Verify DB: pic_pimpinan = 'Kepala Bidang', status = 'disetujui'
6. Login sebagai Kepala Bidang
7. Buka permintaan yang sama
8. Verify: Info box hijau muncul "Disposisi dari Direktur"
9. Verify: Tombol "Teruskan ke Staff Perencanaan" muncul
10. Verify: Tombol "Tolak" dan "Revisi" TIDAK muncul
11. Verify: Modal text: "sudah disetujui Direktur"
12. Klik "Teruskan"
13. Verify: Redirect dengan message "diteruskan ke Staff Perencanaan"
14. Verify DB: pic_pimpinan = 'Staff Perencanaan', status = 'disetujui'

## File yang Dimodifikasi

1. ✅ `app/Http/Controllers/DirekturController.php`
   - Method `approve()`: Disposisi balik ke Kepala Bidang

2. ✅ `app/Http/Controllers/KepalaBidangController.php`
   - Method `approve()`: Dual scenario handling
   - Method `show()`: Pass isDisposisiDariDirektur flag

3. ✅ `resources/js/Pages/KepalaBidang/Show.vue`
   - Action buttons: Conditional display
   - Approve modal: Dynamic content
   - Script: Add new prop

## Catatan Penting

- ✅ Kepala Bidang sekarang memiliki 2 peran: Review awal DAN eksekusi setelah approval Direktur
- ✅ Direktur tetap sebagai final approver tertinggi
- ✅ Staff Perencanaan hanya menerima permintaan yang SUDAH disetujui Direktur
- ✅ Workflow lebih terstruktur dengan check-and-balance yang jelas
- ✅ UI menampilkan status dengan jelas untuk user guidance

## Keunggulan Alur Baru

1. **Check and Balance:** Direktur approve, tapi Kepala Bidang yang eksekusi
2. **Clear Responsibility:** Kepala Bidang bertanggung jawab penuh atas disposisi ke Staff Perencanaan
3. **Audit Trail:** Setiap tahap tercatat dengan jelas di disposisi
4. **Flexibility:** Kepala Bidang bisa tambah catatan sebelum ke Staff Perencanaan
5. **User Friendly:** UI memberikan visual cue yang jelas untuk setiap skenario
