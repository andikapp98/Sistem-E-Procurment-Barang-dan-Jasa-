# Perbaikan Button Revisi Kepala Instalasi

## 🔧 Masalah
Button "Minta Revisi" di halaman Kepala Instalasi tidak berfungsi dengan baik.

## ✅ Perbaikan yang Dilakukan

### 1. **Controller: KepalaInstalasiController.php**

#### Perbaikan Method `requestRevision()`

**Perubahan:**
- ✅ Mengubah validasi dari `min:10` menjadi `min:5` karakter agar lebih fleksibel
- ✅ Menambahkan custom error messages untuk validasi
- ✅ Menambahkan try-catch block untuk menangani error
- ✅ Menambahkan field `detail` di NotaDinas untuk menyimpan catatan revisi lengkap
- ✅ Menambahkan fallback untuk unit_kerja dan jabatan jika null

**Kode:**
```php
public function requestRevision(Request $request, Permintaan $permintaan)
{
    $user = Auth::user();
    
    // Validasi dengan min 5 karakter agar lebih fleksibel
    $data = $request->validate([
        'catatan_revisi' => 'required|string|min:5',
    ], [
        'catatan_revisi.required' => 'Catatan revisi wajib diisi',
        'catatan_revisi.min' => 'Catatan revisi minimal 5 karakter',
    ]);

    try {
        // Update status permintaan ke revisi
        $permintaan->update([
            'status' => 'revisi',
            'pic_pimpinan' => $permintaan->user->name ?? 'Staff Unit',
            'deskripsi' => $permintaan->deskripsi . "\n\n[CATATAN REVISI dari {$user->jabatan} - " . Carbon::now()->format('d/m/Y H:i') . "] " . $data['catatan_revisi'],
        ]);

        // Buat Nota Dinas untuk dokumentasi permintaan revisi
        NotaDinas::create([
            'permintaan_id' => $permintaan->permintaan_id,
            'no_nota' => 'ND/REVISI/' . date('Y/m/d') . '/' . $permintaan->permintaan_id,
            'dari' => $user->unit_kerja ?? $user->jabatan ?? 'Kepala Instalasi',
            'kepada' => $permintaan->user->jabatan ?? $permintaan->user->name ?? 'Staff Unit',
            'tanggal_nota' => Carbon::now(),
            'perihal' => 'Permintaan Revisi - ' . substr($data['catatan_revisi'], 0, 100),
            'detail' => $data['catatan_revisi'],
        ]);

        return redirect()
            ->route('kepala-instalasi.index')
            ->with('success', 'Permintaan revisi telah dikirim ke ' . ($permintaan->user->name ?? 'pemohon') . ' untuk diperbaiki');
    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
```

---

### 2. **View: KepalaInstalasi/Show.vue**

#### Perbaikan Modal Revisi

**Perubahan:**
- ✅ Menambahkan indicator required (*) di label
- ✅ Menambahkan character counter untuk feedback real-time
- ✅ Menambahkan validasi error display
- ✅ Menambahkan disabled state yang lebih jelas
- ✅ Menambahkan placeholder yang informatif

**Kode Modal:**
```vue
<!-- Modal Revisi -->
<div v-if="showRevisiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Minta Revisi</h3>
            <div class="mt-2 px-7 py-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Revisi <span class="text-red-500">*</span>
                </label>
                <textarea
                    v-model="revisiForm.catatan_revisi"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                    placeholder="Masukkan catatan revisi (minimal 5 karakter)..."
                ></textarea>
                <p v-if="revisiForm.catatan_revisi && revisiForm.catatan_revisi.length < 5" class="text-xs text-red-500 mt-1">
                    Minimal 5 karakter ({{ revisiForm.catatan_revisi.length }}/5)
                </p>
                <p v-if="$page.props.errors?.catatan_revisi" class="text-xs text-red-500 mt-1">
                    {{ $page.props.errors.catatan_revisi }}
                </p>
            </div>
            <div class="flex gap-3 px-4 py-3">
                <button
                    @click="requestRevision"
                    :disabled="!revisiForm.catatan_revisi || revisiForm.catatan_revisi.length < 5"
                    class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Kirim Revisi
                </button>
                <button
                    @click="showRevisiModal = false"
                    class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                >
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
```

#### Perbaikan JavaScript Function

**Perubahan:**
- ✅ Menambahkan validasi client-side sebelum submit
- ✅ Menambahkan alert untuk feedback instant
- ✅ Menambahkan error handling dengan console.error
- ✅ Menambahkan reset form setelah sukses
- ✅ Modal tetap terbuka jika ada error

**Kode:**
```javascript
const requestRevision = () => {
    // Validasi minimal 5 karakter sebelum submit
    if (!revisiForm.value.catatan_revisi || revisiForm.value.catatan_revisi.trim().length < 5) {
        alert('Catatan revisi minimal 5 karakter');
        return;
    }
    
    router.post(route('kepala-instalasi.revisi', props.permintaan.permintaan_id), revisiForm.value, {
        onSuccess: () => {
            showRevisiModal.value = false;
            revisiForm.value.catatan_revisi = ''; // Reset form
        },
        onError: (errors) => {
            console.error('Error:', errors);
            // Modal tetap terbuka jika ada error
        }
    });
};
```

#### Perbaikan Error Notifications

**Perubahan:**
- ✅ Menambahkan error notification untuk flash error
- ✅ Menambahkan validation errors display
- ✅ Menampilkan semua error dalam list

**Kode:**
```vue
<!-- Success notification -->
<div v-if="$page.props.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
    <span class="block sm:inline">{{ $page.props.flash.success }}</span>
</div>

<!-- Error notification -->
<div v-if="$page.props.flash?.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
    <span class="block sm:inline">{{ $page.props.flash.error }}</span>
</div>

<!-- Validation errors -->
<div v-if="Object.keys($page.props.errors || {}).length > 0" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
    <strong class="font-bold">Terdapat kesalahan!</strong>
    <ul class="list-disc list-inside mt-2">
        <li v-for="(error, key) in $page.props.errors" :key="key">{{ error }}</li>
    </ul>
</div>
```

---

## 🔍 Route yang Digunakan

**Route Name:** `kepala-instalasi.revisi`

**URL Pattern:** `POST /kepala-instalasi/permintaan/{permintaan}/revisi`

**Controller Method:** `KepalaInstalasiController@requestRevision`

**Validasi:**
- ✅ Route sudah terdaftar dengan benar
- ✅ Middleware: `auth`, `verified`

---

## 📋 Alur Kerja Button Revisi

### 1. User Flow
```
1. Kepala Instalasi buka detail permintaan
2. Klik button "Minta Revisi" (orange)
3. Modal terbuka
4. Input catatan revisi (min 5 karakter)
5. Character counter menampilkan jumlah karakter
6. Button "Kirim Revisi" disabled sampai valid
7. Klik "Kirim Revisi"
8. Validasi client-side (alert jika < 5 karakter)
9. Submit ke server
10. Server validasi dengan Laravel validation
11. Jika sukses:
    - Update status permintaan → "revisi"
    - Update deskripsi dengan catatan revisi
    - Buat nota dinas dokumentasi
    - Redirect ke index dengan success message
12. Jika error:
    - Tampilkan error message
    - Modal tetap terbuka untuk perbaikan
```

### 2. Data Flow
```
Frontend (Vue) → Router (Inertia) → Route → Controller → Model → Database

revisiForm.catatan_revisi
    ↓
route('kepala-instalasi.revisi', permintaan_id)
    ↓
POST /kepala-instalasi/permintaan/{id}/revisi
    ↓
KepalaInstalasiController@requestRevision()
    ↓
Validate: catatan_revisi required|min:5
    ↓
Update Permintaan:
    - status = 'revisi'
    - pic_pimpinan = user pemohon
    - deskripsi += catatan revisi
    ↓
Create NotaDinas:
    - no_nota = ND/REVISI/date/id
    - dari = kepala instalasi
    - kepada = staff pemohon
    - detail = catatan revisi
    ↓
Redirect to index with success message
```

---

## 🧪 Testing

### Manual Testing Steps:

1. **Login sebagai Kepala Instalasi**
   ```
   Email: kepala.igd@hospital.com
   Password: password
   ```

2. **Buka Permintaan**
   - Pergi ke Dashboard Kepala Instalasi
   - Pilih permintaan dengan status "diajukan"
   - Klik untuk melihat detail

3. **Test Button Revisi**
   
   **Test Case 1: Input kosong**
   - Klik "Minta Revisi"
   - Klik "Kirim Revisi" tanpa input
   - ✅ Hasil: Button disabled, tidak bisa klik

   **Test Case 2: Input < 5 karakter**
   - Input: "abc"
   - Character counter: "3/5"
   - ✅ Hasil: Button disabled, ada warning merah

   **Test Case 3: Input valid (>= 5 karakter)**
   - Input: "Harap perbaiki deskripsi"
   - Character counter tidak muncul (sudah valid)
   - Klik "Kirim Revisi"
   - ✅ Hasil: 
     - Success message muncul
     - Redirect ke index
     - Status permintaan = "revisi"
     - Nota dinas dibuat

4. **Verifikasi Database**
   ```sql
   -- Cek status permintaan
   SELECT permintaan_id, status, pic_pimpinan, deskripsi 
   FROM permintaan 
   WHERE permintaan_id = [ID];
   
   -- Cek nota dinas
   SELECT * FROM nota_dinas 
   WHERE permintaan_id = [ID] 
   AND no_nota LIKE 'ND/REVISI%';
   ```

---

## 📊 Status Field di Database

### Table: `permintaan`
| Field | Sebelum | Sesudah |
|-------|---------|---------|
| status | `diajukan` | `revisi` |
| pic_pimpinan | `Kepala Instalasi` | `[Nama Staff Pemohon]` |
| deskripsi | `[Deskripsi awal]` | `[Deskripsi awal]\n\n[CATATAN REVISI dari Kepala Instalasi IGD - 26/10/2025 10:30] Harap perbaiki deskripsi` |

### Table: `nota_dinas`
| Field | Value |
|-------|-------|
| permintaan_id | `[ID Permintaan]` |
| no_nota | `ND/REVISI/2025/10/26/[ID]` |
| dari | `Instalasi Gawat Darurat` |
| kepada | `Staff IGD` atau `[Nama Pemohon]` |
| tanggal_nota | `2025-10-26 10:30:00` |
| perihal | `Permintaan Revisi - Harap perbaiki...` |
| detail | `Harap perbaiki deskripsi` |

---

## 🔧 Cache Clearing

Setelah perubahan, clear cache:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## ✅ Checklist Perbaikan

- [x] Validasi min karakter diubah dari 10 → 5
- [x] Custom error messages ditambahkan
- [x] Try-catch block untuk error handling
- [x] Field `detail` di NotaDinas ditambahkan
- [x] Fallback untuk null values ditambahkan
- [x] Character counter di modal
- [x] Required indicator (*)
- [x] Disabled state yang jelas
- [x] Client-side validation
- [x] Error notifications display
- [x] Form reset setelah sukses
- [x] Modal tetap buka jika error
- [x] Console error logging

---

## 📝 Catatan Tambahan

### Perbedaan dengan Approve & Reject:

| Action | Status | PIC Pimpinan | Workflow |
|--------|--------|--------------|----------|
| **Approve** | `proses` | `Kepala Bidang` | Forward ke Kepala Bidang |
| **Reject** | `ditolak` | `[Nama Kepala Instalasi]` | Akhiri workflow |
| **Revisi** | `revisi` | `[Nama Staff Pemohon]` | Kembali ke pemohon untuk diperbaiki |

### Workflow Revisi:
1. Kepala Instalasi minta revisi
2. Status → `revisi`
3. PIC → Pemohon (staff yang buat permintaan)
4. Staff perbaiki permintaan
5. Staff submit ulang
6. Status → `diajukan`
7. Kepala Instalasi review lagi

---

**Tanggal Perbaikan:** 2025-10-26  
**Status:** ✅ SELESAI  
**Testing:** Menunggu manual testing
