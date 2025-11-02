# 📋 KEPALA IRJA: APPROVE, REJECT, REVISI - COMPLETE GUIDE

## ✅ Status: FULLY IMPLEMENTED

Kepala Instalasi IRJA memiliki **3 opsi lengkap** untuk mengelola permintaan dari Kepala Poli:
1. ✅ **APPROVE** (Setujui)
2. ❌ **REJECT** (Tolak)
3. 🔄 **REVISI** (Minta Revisi)

---

## 🎯 3 AKSI YANG TERSEDIA

### 1. ✅ APPROVE (Setujui Permintaan)

**Fungsi:**
- Menyetujui permintaan dan meneruskan ke Kepala Bidang
- Auto-routing berdasarkan klasifikasi

**Flow:**
```
Status: diajukan → proses
Routing: Kepala IRJA → Kabid (sesuai klasifikasi)
```

**Klasifikasi Auto-routing:**
- **Medis** → Kabid Pelayanan Medis (Kabid Yanmed)
- **Penunjang Medis** → Kabid Penunjang Medis
- **Non Medis** → Kabid Umum & Keuangan

**UI:**
- Button: Hijau dengan icon checkmark
- Modal menampilkan:
  - Konfirmasi approval
  - Informasi klasifikasi
  - Kabid tujuan
  - Catatan (optional)

**Route:** `POST /kepala-instalasi/permintaan/{id}/approve`

**Validation:** None (langsung proses)

---

### 2. ❌ REJECT (Tolak Permintaan)

**Fungsi:**
- Menolak permintaan dengan alasan yang jelas
- Permintaan tidak diteruskan

**Flow:**
```
Status: diajukan → ditolak
Action: Permintaan stopped, tidak ke Kabid
```

**UI:**
- Button: Merah dengan icon X
- Modal dengan form:
  - Textarea untuk alasan penolakan
  - Wajib diisi
  - Placeholder: "Masukkan alasan penolakan..."

**Route:** `POST /kepala-instalasi/permintaan/{id}/reject`

**Validation:**
```javascript
rejectForm = {
    alasan: required
}
```

**Database Update:**
```php
$permintaan->update([
    'status' => 'ditolak',
    'catatan_reject' => $request->alasan,
    'rejected_by' => $user->nama,
    'rejected_at' => now(),
]);
```

---

### 3. 🔄 REVISI (Minta Revisi)

**Fungsi:**
- Mengembalikan permintaan ke Kepala Poli untuk diperbaiki
- Memberikan catatan apa yang perlu direvisi

**Flow:**
```
Status: diajukan → revisi
Action: Kembali ke Kepala Poli untuk perbaikan
```

**UI:**
- Button: Orange dengan icon edit
- Modal dengan form:
  - Textarea untuk catatan revisi
  - Wajib minimal 5 karakter
  - Validasi real-time
  - Counter karakter
  - Placeholder: "Masukkan catatan revisi (minimal 5 karakter)..."

**Route:** `POST /kepala-instalasi/permintaan/{id}/revisi`

**Validation:**
```javascript
revisiForm = {
    catatan_revisi: {
        required: true,
        min: 5 characters
    }
}

// Real-time validation
<p v-if="revisiForm.catatan_revisi && revisiForm.catatan_revisi.length < 5">
    Minimal 5 karakter ({{ revisiForm.catatan_revisi.length }}/5)
</p>

// Button disabled until valid
:disabled="!revisiForm.catatan_revisi || revisiForm.catatan_revisi.length < 5"
```

**Database Update:**
```php
$permintaan->update([
    'status' => 'revisi',
    'catatan_revisi' => $request->catatan_revisi,
    'revision_requested_by' => $user->nama,
    'revision_requested_at' => now(),
]);
```

---

## 🔄 COMPLETE WORKFLOW DIAGRAM

```
┌────────────────────────────────────────────────────────────────┐
│                 KEPALA IRJA WORKFLOW OPTIONS                    │
└────────────────────────────────────────────────────────────────┘

KEPALA POLI
   │
   │ (Membuat Permintaan)
   │ Status: diajukan
   ↓

KEPALA IRJA (Review)
   │
   ├─────────────┬─────────────┬─────────────┐
   │             │             │             │
   ▼             ▼             ▼             ▼
APPROVE       REJECT       REVISI       (Abaikan)
   │             │             │
   │             │             │
   ▼             ▼             ▼
Status:       Status:       Status:
proses        ditolak       revisi
   │             │             │
   ▼             ▼             ▼
Ke Kabid    Permintaan    Kembali ke
Yanmed/     berhenti      Kepala Poli
Penunjang/                untuk
Umum                      perbaikan
   │             
   ▼             
KEPALA BIDANG
   │
   ▼
DIREKTUR
   │
   ▼
STAFF PERENCANAAN
   │
   ▼
SELESAI
```

---

## 🖥️ USER INTERFACE DETAILS

### Show Page (`/kepala-instalasi/permintaan/{id}`)

**Action Buttons Section:**
```vue
<div v-if="permintaan.status === 'diajukan'" class="bg-white shadow-sm rounded-lg">
    <div class="p-6">
        <h3>Tindakan</h3>
        <div class="flex gap-3">
            <!-- Approve Button -->
            <button @click="showApproveModal = true" 
                    class="bg-green-600 text-white">
                <svg>checkmark</svg>
                Setujui
            </button>

            <!-- Request Revisi Button -->
            <button @click="showRevisiModal = true"
                    class="bg-orange-600 text-white">
                <svg>edit</svg>
                Minta Revisi
            </button>

            <!-- Reject Button -->
            <button @click="showRejectModal = true"
                    class="bg-red-600 text-white">
                <svg>x-circle</svg>
                Tolak
            </button>
        </div>
    </div>
</div>
```

### Modal Approve

**Features:**
- Informasi klasifikasi otomatis
- Informasi Kabid tujuan
- Konfirmasi action
- No form input (langsung approve)

```vue
<div v-if="showApproveModal">
    <h3>Setujui Permintaan</h3>
    <p>Apakah Anda yakin ingin menyetujui permintaan ini?</p>
    
    <!-- Info Box -->
    <div class="bg-blue-50">
        <h4>Informasi Routing:</h4>
        <div>
            <span>Klasifikasi:</span>
            <span>{{ formatKlasifikasi(klasifikasi) }}</span>
        </div>
        <div>
            <span>Akan diteruskan ke:</span>
            <span>{{ kabidTujuan }}</span>
        </div>
    </div>
    
    <p class="text-xs italic">
        Permintaan akan otomatis dikirim ke {{ kabidTujuan }} 
        untuk review dan persetujuan selanjutnya.
    </p>
    
    <div class="flex gap-3">
        <button @click="approve">Ya, Setujui</button>
        <button @click="showApproveModal = false">Batal</button>
    </div>
</div>
```

### Modal Reject

**Features:**
- Wajib isi alasan
- Textarea untuk penjelasan
- Button disabled jika kosong

```vue
<div v-if="showRejectModal">
    <h3>Tolak Permintaan</h3>
    
    <label>Alasan Penolakan</label>
    <textarea
        v-model="rejectForm.alasan"
        rows="4"
        placeholder="Masukkan alasan penolakan..."
    ></textarea>
    
    <div class="flex gap-3">
        <button 
            @click="reject"
            :disabled="!rejectForm.alasan"
        >
            Tolak
        </button>
        <button @click="showRejectModal = false">Batal</button>
    </div>
</div>
```

### Modal Revisi

**Features:**
- Wajib minimal 5 karakter
- Real-time validation
- Character counter
- Button disabled until valid

```vue
<div v-if="showRevisiModal">
    <h3>Minta Revisi</h3>
    
    <label>Catatan Revisi <span class="text-red-500">*</span></label>
    <textarea
        v-model="revisiForm.catatan_revisi"
        rows="4"
        placeholder="Masukkan catatan revisi (minimal 5 karakter)..."
    ></textarea>
    
    <!-- Real-time validation message -->
    <p v-if="revisiForm.catatan_revisi && revisiForm.catatan_revisi.length < 5" 
       class="text-red-500">
        Minimal 5 karakter ({{ revisiForm.catatan_revisi.length }}/5)
    </p>
    
    <div class="flex gap-3">
        <button 
            @click="requestRevision"
            :disabled="!revisiForm.catatan_revisi || revisiForm.catatan_revisi.length < 5"
        >
            Kirim Revisi
        </button>
        <button @click="showRevisiModal = false">Batal</button>
    </div>
</div>
```

---

## 🔧 CONTROLLER METHODS

### File: `KepalaInstalasiController.php`

#### 1. Approve Method
```php
public function approve(Request $request, Permintaan $permintaan)
{
    $user = Auth::user();

    // Authorization check
    $this->checkAuthorization($user, $permintaan);

    // Determine klasifikasi & kabid tujuan
    $klasifikasi = $permintaan->klasifikasi_permintaan ?? 
                   $this->determineKlasifikasi($permintaan->bidang);
    $kabidTujuan = $this->getKabidTujuan($klasifikasi);

    // Update permintaan
    $permintaan->update([
        'status' => 'proses',
        'klasifikasi_permintaan' => $klasifikasi,
        'kabid_tujuan' => $kabidTujuan,
        'approved_by' => $user->nama,
        'approved_at' => now(),
    ]);

    // Create disposisi
    Disposisi::create([
        'permintaan_id' => $permintaan->permintaan_id,
        'dari' => $user->nama,
        'kepada' => $kabidTujuan,
        'tanggal_disposisi' => now(),
        'isi_disposisi' => 'Mohon ditinjau dan diproses lebih lanjut',
        'status_disposisi' => 'pending',
    ]);

    // Log activity
    ActivityLogger::log(
        'Approved Permintaan',
        'Kepala Instalasi approved permintaan #' . $permintaan->permintaan_id,
        $permintaan->permintaan_id
    );

    return redirect()
        ->route('kepala-instalasi.index')
        ->with('success', 'Permintaan berhasil disetujui dan diteruskan ke ' . $kabidTujuan);
}
```

#### 2. Reject Method
```php
public function reject(Request $request, Permintaan $permintaan)
{
    $user = Auth::user();

    // Authorization check
    $this->checkAuthorization($user, $permintaan);

    // Validation
    $request->validate([
        'alasan' => 'required|string|min:5',
    ], [
        'alasan.required' => 'Alasan penolakan harus diisi.',
        'alasan.min' => 'Alasan penolakan minimal 5 karakter.',
    ]);

    // Update permintaan
    $permintaan->update([
        'status' => 'ditolak',
        'catatan_reject' => $request->alasan,
        'rejected_by' => $user->nama,
        'rejected_at' => now(),
    ]);

    // Log activity
    ActivityLogger::log(
        'Rejected Permintaan',
        'Kepala Instalasi rejected permintaan #' . $permintaan->permintaan_id . 
        ' - Alasan: ' . $request->alasan,
        $permintaan->permintaan_id
    );

    return redirect()
        ->route('kepala-instalasi.index')
        ->with('success', 'Permintaan berhasil ditolak.');
}
```

#### 3. Request Revision Method
```php
public function requestRevision(Request $request, Permintaan $permintaan)
{
    $user = Auth::user();

    // Authorization check
    $this->checkAuthorization($user, $permintaan);

    // Validation
    $request->validate([
        'catatan_revisi' => 'required|string|min:5',
    ], [
        'catatan_revisi.required' => 'Catatan revisi harus diisi.',
        'catatan_revisi.min' => 'Catatan revisi minimal 5 karakter.',
    ]);

    // Update permintaan
    $permintaan->update([
        'status' => 'revisi',
        'catatan_revisi' => $request->catatan_revisi,
        'revision_requested_by' => $user->nama,
        'revision_requested_at' => now(),
    ]);

    // Log activity
    ActivityLogger::log(
        'Requested Revision',
        'Kepala Instalasi requested revision for permintaan #' . 
        $permintaan->permintaan_id . ' - Catatan: ' . $request->catatan_revisi,
        $permintaan->permintaan_id
    );

    return redirect()
        ->route('kepala-instalasi.index')
        ->with('success', 'Permintaan dikembalikan untuk revisi.');
}
```

---

## 📊 STATUS TRANSITIONS

### Status Flow Diagram:
```
diajukan ──┬──> proses (Approve)
           ├──> ditolak (Reject)
           └──> revisi (Request Revision)

revisi ────> diajukan (Kepala Poli re-submit after revision)

proses ───> disetujui (Kabid/Direktur approve)
         └> ditolak (Kabid/Direktur reject)
```

### Status Colors:
```javascript
const statusColors = {
    diajukan: 'yellow',   // Pending review
    proses: 'blue',       // In progress
    disetujui: 'green',   // Approved
    ditolak: 'red',       // Rejected
    revisi: 'orange',     // Needs revision
};
```

---

## 🔐 AUTHORIZATION

### Access Control:
```php
private function checkAuthorization($user, $permintaan)
{
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
            abort(403, 'Anda tidak memiliki akses untuk tindakan ini.');
        }
    }

    // Check if status is 'diajukan' (can only act on submitted requests)
    if ($permintaan->status !== 'diajukan') {
        abort(403, 'Hanya permintaan dengan status "diajukan" yang dapat di-review.');
    }
}
```

---

## 🧪 TESTING SCENARIOS

### Scenario 1: Approve Permintaan
```
1. Login as Kepala IRJA
2. Go to /kepala-instalasi
3. Click permintaan from Poli Bedah (status: diajukan)
4. Click "Setujui" button
5. Modal shows:
   - Klasifikasi: Medis
   - Kabid Tujuan: Bidang Pelayanan Medis
6. Click "Ya, Setujui"
7. ✅ Success message shown
8. ✅ Status changed to "proses"
9. ✅ Permintaan appears in Kabid Yanmed's list
10. ✅ Disposisi created
```

### Scenario 2: Reject Permintaan
```
1. Login as Kepala IRJA
2. Click permintaan (status: diajukan)
3. Click "Tolak" button
4. Modal opens with textarea
5. Try to submit without alasan → ❌ Button disabled
6. Enter alasan: "Tidak sesuai kebutuhan"
7. Click "Tolak"
8. ✅ Success message shown
9. ✅ Status changed to "ditolak"
10. ✅ Permintaan tidak muncul di Kabid
11. ✅ Kepala Poli can see rejection reason
```

### Scenario 3: Request Revision
```
1. Login as Kepala IRJA
2. Click permintaan (status: diajukan)
3. Click "Minta Revisi" button
4. Try enter "Test" (4 chars) → ❌ Shows "Minimal 5 karakter (4/5)"
5. Button disabled
6. Enter "Mohon lengkapi spesifikasi alat"
7. Button enabled
8. Click "Kirim Revisi"
9. ✅ Success message shown
10. ✅ Status changed to "revisi"
11. ✅ Permintaan kembali ke Kepala Poli dengan catatan
```

### Scenario 4: Authorization Test
```
1. Login as Kepala IRNA (Rawat Inap)
2. Try to access permintaan from Poli Bedah (IRJA)
3. ❌ Error 403: "Anda tidak memiliki akses"
4. Cannot see action buttons
```

### Scenario 5: Status Validation
```
1. Login as Kepala IRJA
2. Approve permintaan #123 (status: diajukan → proses)
3. Try to access /kepala-instalasi/permintaan/123
4. Action buttons NOT shown (status = proses)
5. ✅ Cannot approve/reject/revisi again
```

---

## 📝 FILES INVOLVED

```
✅ app/Http/Controllers/KepalaInstalasiController.php
   - approve()
   - reject()
   - requestRevision()
   - checkAuthorization()

✅ resources/js/Pages/KepalaInstalasi/Show.vue
   - 3 action buttons
   - 3 modals (approve, reject, revisi)
   - Form handling
   - Validation

✅ routes/web.php
   - POST /kepala-instalasi/permintaan/{id}/approve
   - POST /kepala-instalasi/permintaan/{id}/reject
   - POST /kepala-instalasi/permintaan/{id}/revisi

✅ database/migrations (if needed)
   - Add columns: approved_by, approved_at
   - Add columns: rejected_by, rejected_at, catatan_reject
   - Add columns: revision_requested_by, revision_requested_at, catatan_revisi
```

---

## 💡 BEST PRACTICES

### For Kepala IRJA:
1. **Approve:** Gunakan untuk permintaan yang sudah lengkap dan sesuai
2. **Reject:** Gunakan untuk permintaan yang tidak layak atau tidak sesuai kebijakan
3. **Revisi:** Gunakan untuk permintaan yang bagus tapi perlu perbaikan minor

### For Developers:
1. Always validate user input (especially for reject & revisi)
2. Log all actions for audit trail
3. Check authorization before any action
4. Provide clear feedback messages
5. Disable buttons when form invalid

---

## 🎓 KEY POINTS

1. **3 Options Available:** Approve, Reject, Revisi - giving full control
2. **Validation:** Reject & Revisi require input, Approve doesn't
3. **Authorization:** Multi-layer check ensures security
4. **Status Flow:** Clear state machine for permintaan lifecycle
5. **User Feedback:** Success/error messages for all actions
6. **Audit Trail:** All actions logged with user & timestamp

---

**Status:** ✅ FULLY IMPLEMENTED & WORKING  
**Last Updated:** 2 November 2025  
**Version:** 2.0 (Complete with Approve, Reject, Revisi)
