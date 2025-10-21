# Implementasi Timeline Tahapan Berikutnya

## Status: ✅ SELESAI

## Deskripsi
Menambahkan fungsi timeline yang menampilkan tahapan yang sudah dilalui DAN tahapan berikutnya yang harus dilakukan dalam proses pengadaan.

## Fitur yang Ditambahkan

### 1. Method Baru di Model Permintaan

#### A. `getNextStep()`
Mendapatkan informasi tahap berikutnya yang perlu dilakukan.

**Return:**
```php
[
    'step' => 4,
    'tahapan' => 'Perencanaan',
    'description' => 'Staff Perencanaan membuat rencana pengadaan',
    'responsible' => 'Staff Perencanaan',
    'completed' => false
]
```

#### B. `getRemainingSteps()`
Mendapatkan array nama tahapan yang belum dilalui.

**Return:**
```php
['Perencanaan', 'KSO', 'Pengadaan', 'Nota Penerimaan', 'Serah Terima']
```

#### C. `getCompleteTracking()`
Mendapatkan timeline lengkap (8 tahapan) dengan status completed atau pending.

**Return:**
```php
[
    [
        'step' => 1,
        'tahapan' => 'Permintaan',
        'description' => 'Permintaan dibuat oleh unit',
        'responsible' => 'Unit/Admin',
        'icon' => 'document',
        'tanggal' => '2025-01-15',
        'status' => 'diajukan',
        'keterangan' => 'Permintaan diajukan',
        'completed' => true
    ],
    [
        'step' => 2,
        'tahapan' => 'Nota Dinas',
        'description' => 'Kepala Instalasi membuat nota dinas',
        'responsible' => 'Kepala Instalasi',
        'icon' => 'mail',
        'tanggal' => null,
        'status' => 'pending',
        'keterangan' => 'Belum dilaksanakan',
        'completed' => false
    ],
    // ... 6 tahapan lainnya
]
```

### 2. Update Controller

**File:** `app/Http/Controllers/DirekturController.php`

**Method `tracking()` - Sebelum:**
```php
public function tracking(Permintaan $permintaan)
{
    $timeline = $permintaan->getTimelineTracking();
    $progress = $permintaan->getProgressPercentage();
    
    $allSteps = ['Permintaan', 'Nota Dinas', ...];
    $completedSteps = array_column($timeline, 'tahapan');
    $pendingSteps = array_diff($allSteps, $completedSteps);
    
    return Inertia::render('Direktur/Tracking', [...]);
}
```

**Method `tracking()` - Sesudah:**
```php
public function tracking(Permintaan $permintaan)
{
    $completeTracking = $permintaan->getCompleteTracking();
    $progress = $permintaan->getProgressPercentage();
    $nextStep = $permintaan->getNextStep();
    
    $completedSteps = array_filter($completeTracking, fn($item) => $item['completed']);
    $pendingSteps = array_filter($completeTracking, fn($item) => !$item['completed']);
    
    return Inertia::render('Direktur/Tracking', [
        'completeTracking' => $completeTracking,
        'nextStep' => $nextStep,
        ...
    ]);
}
```

### 3. Halaman Tracking.vue Baru

**File:** `resources/js/Pages/Direktur/Tracking.vue`

#### Fitur UI:

**A. Progress Summary Card**
- Progress percentage dengan angka besar
- Progress bar visual (red theme untuk Direktur)
- Grid info: Tahap Selesai, Tahap Pending, Status

**B. Next Step Info Box (Blue)**
- Highlight tahap berikutnya yang perlu dilakukan
- Icon informasi
- Nama tahap, deskripsi, dan penanggung jawab
- Hanya muncul jika ada tahap yang belum selesai

**C. Complete Timeline**
Timeline vertikal dengan 8 tahapan lengkap:

**Tahap Selesai (Completed):**
- ✅ Icon checkmark hijau
- Connecting line hijau
- Background hijau di badge status
- Tanggal pelaksanaan ditampilkan
- Keterangan detail ditampilkan
- Text bold dan gelap

**Tahap Pending:**
- 🕐 Icon clock abu-abu
- Connecting line abu-abu
- Background abu-abu di badge status
- Tanggal tidak ditampilkan (null)
- Keterangan: "Belum dilaksanakan"
- Text abu-abu dan lighter

**Layout Timeline:**
```
[Icon] Step 1. Permintaan                     [DIAJUKAN]
       Permintaan dibuat oleh unit             15 Jan 2025
       Penanggung jawab: Unit/Admin
       ────────────────────────────────────
       Permintaan diajukan
│
│
[Icon] Step 2. Nota Dinas                     [PENDING]
       Kepala Instalasi membuat nota dinas
       Penanggung jawab: Kepala Instalasi
│
│
[Icon] Step 3. Disposisi                      [PENDING]
       ...
```

**D. Detail Permintaan Card**
Informasi lengkap permintaan di bagian bawah

## 8 Tahapan Lengkap

| Step | Tahapan | Description | Responsible |
|------|---------|-------------|-------------|
| 1 | Permintaan | Permintaan dibuat oleh unit | Unit/Admin |
| 2 | Nota Dinas | Kepala Instalasi membuat nota dinas | Kepala Instalasi |
| 3 | Disposisi | Disposisi oleh pimpinan | Kepala Bidang / Direktur |
| 4 | Perencanaan | Staff Perencanaan membuat rencana pengadaan | Staff Perencanaan |
| 5 | KSO | Kerja Sama Operasional dengan vendor | Bagian KSO |
| 6 | Pengadaan | Proses pengadaan dan pembelian | Bagian Pengadaan |
| 7 | Nota Penerimaan | Penerimaan barang/jasa dari vendor | Bagian Serah Terima |
| 8 | Serah Terima | Serah terima kepada unit pemohon | Bagian Serah Terima |

## Progress Calculation

Progress dihitung berdasarkan jumlah tahap yang selesai:
```javascript
progress = (completedSteps / 8) * 100
```

Contoh:
- 3 tahap selesai = 37.5%
- 5 tahap selesai = 62.5%
- 8 tahap selesai = 100%

## Skenario Penggunaan

### Skenario 1: Permintaan Baru (1 Tahap)
**Completed:** Permintaan
**Next Step:** Nota Dinas
**Progress:** 12.5%
**Display:**
- ✅ Permintaan (completed, hijau)
- 🕐 Nota Dinas - Kepala Instalasi (pending, abu-abu)
- 🕐 Disposisi (pending, abu-abu)
- ... 5 tahap lagi pending

### Skenario 2: Sudah Disposisi (3 Tahap)
**Completed:** Permintaan, Nota Dinas, Disposisi
**Next Step:** Perencanaan
**Progress:** 37.5%
**Display:**
- ✅ Permintaan (completed)
- ✅ Nota Dinas (completed)
- ✅ Disposisi (completed)
- 🕐 Perencanaan - Staff Perencanaan (next, highlighted)
- 🕐 KSO (pending)
- ... 3 tahap lagi pending

### Skenario 3: Selesai Semua (8 Tahap)
**Completed:** Semua 8 tahap
**Next Step:** "Selesai - Semua tahapan telah selesai"
**Progress:** 100%
**Display:**
- ✅ Semua 8 tahap dengan checkmark hijau
- No next step info box (sudah selesai)

## Keunggulan Implementasi

### 1. **User Clarity**
User langsung tahu:
- ✅ Tahap mana yang sudah selesai
- 🕐 Tahap mana yang belum
- ➡️ Tahap berikutnya yang perlu dilakukan
- 👤 Siapa yang bertanggung jawab

### 2. **Visual Feedback**
- Color coding jelas (hijau = selesai, abu-abu = pending)
- Progress bar visual
- Timeline vertikal yang mudah dibaca
- Icon yang intuitif

### 3. **Informasi Lengkap**
Setiap tahap menampilkan:
- Nama tahap
- Deskripsi singkat
- Penanggung jawab
- Status (completed/pending)
- Tanggal (jika completed)
- Keterangan detail (jika ada)

### 4. **Scalable**
Method di model bisa digunakan oleh role lain:
- Kepala Bidang
- Staff Perencanaan
- KSO
- Pengadaan
- Dll.

Tinggal copy tracking method di controller dan buat view serupa.

## File yang Dimodifikasi/Dibuat

1. ✅ `app/Models/Permintaan.php`
   - Method `getNextStep()` - NEW
   - Method `getRemainingSteps()` - NEW
   - Method `getCompleteTracking()` - NEW

2. ✅ `app/Http/Controllers/DirekturController.php`
   - Method `tracking()` - UPDATED

3. ✅ `resources/js/Pages/Direktur/Tracking.vue` - NEW
   - Complete timeline view
   - Next step highlight
   - Progress visualization

## Testing Checklist

### Functional Testing
- [x] Method `getCompleteTracking()` return 8 items
- [x] Method `getNextStep()` return correct next step
- [x] Method `getRemainingSteps()` return correct remaining
- [x] Controller pass correct data to view
- [x] View render timeline correctly

### Visual Testing
- [x] Progress bar shows correct percentage
- [x] Completed steps show green icon
- [x] Pending steps show gray icon
- [x] Next step info box appears (if not completed)
- [x] Timeline connecting lines correct color
- [x] Date format correct (Indonesia locale)
- [x] Responsive layout works

### Edge Cases
- [x] Permintaan baru (1 step completed)
- [x] In progress (multiple steps completed)
- [x] All completed (8 steps completed)
- [x] Empty/null dates handled correctly
- [x] Missing relations handled (no crash)

## Next Steps (Optional Enhancements)

Possible future improvements:
- [ ] Add estimated time for each step
- [ ] Add notifications when moving to next step
- [ ] Add step-by-step actions (buttons)
- [ ] Add comments/notes per step
- [ ] Add file attachments per step
- [ ] Export timeline as PDF
- [ ] Email notifications for responsible person

## Notes

- Timeline sekarang lebih informatif dan user-friendly
- Semua role bisa menggunakan tracking method yang sama
- Method terstruktur dan reusable
- UI consistent dengan design system (Tailwind)
- Color theme adaptif per role (red untuk Direktur)
