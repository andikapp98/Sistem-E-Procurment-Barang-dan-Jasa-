# ✅ Fitur Lihat Lampiran Nota Dinas - Complete

## 🎯 Implementasi

Admin dan Kepala Instalasi sekarang dapat **melihat dan download lampiran** dari nota dinas melalui route khusus yang aman.

## 📋 Perubahan

### 1. Route Baru

**File**: `routes/web.php`

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/permintaan/{permintaan}/tracking', [PermintaanController::class, 'tracking'])
        ->name('permintaan.tracking');
    Route::get('/permintaan/{permintaan}/cetak-nota-dinas', [PermintaanController::class, 'cetakNotaDinas'])
        ->name('permintaan.cetak-nota-dinas');
    
    // ✨ NEW: Route untuk lihat lampiran
    Route::get('/nota-dinas/{notaDinas}/lampiran', [PermintaanController::class, 'lihatLampiran'])
        ->name('nota-dinas.lampiran');
});
```

**Pattern**: `/nota-dinas/{nota_id}/lampiran`

**Contoh**: `/nota-dinas/123/lampiran`

### 2. Controller Method

**File**: `app/Http/Controllers/PermintaanController.php`

**Imports Tambahan**:
```php
use App\Models\NotaDinas;
use Illuminate\Support\Facades\Storage;
```

**Method Baru**:
```php
/**
 * Lihat/Download Lampiran Nota Dinas
 */
public function lihatLampiran(NotaDinas $notaDinas)
{
    // Validasi apakah nota dinas punya lampiran
    if (!$notaDinas->lampiran) {
        return redirect()->back()
            ->with('error', 'Lampiran tidak ditemukan untuk nota dinas ini.');
    }

    // Jika lampiran adalah URL (http/https), redirect ke URL tersebut
    if (filter_var($notaDinas->lampiran, FILTER_VALIDATE_URL)) {
        return redirect($notaDinas->lampiran);
    }

    // Jika lampiran adalah file path di storage
    if (Storage::exists($notaDinas->lampiran)) {
        return Storage::download($notaDinas->lampiran);
    }

    // Jika file tidak ditemukan
    return redirect()->back()
        ->with('error', 'File lampiran tidak ditemukan.');
}
```

**Logic Flow**:
1. **Validasi**: Cek apakah nota dinas punya field lampiran
2. **URL Check**: Jika lampiran adalah URL external → redirect
3. **File Check**: Jika lampiran adalah file di storage → download
4. **Error Handling**: Jika tidak ditemukan → redirect back dengan error

### 3. Frontend - Admin (Permintaan/Show.vue)

**Updated Section**:
```vue
<div v-if="nota.lampiran" class="md:col-span-2">
    <dt class="text-sm font-medium text-gray-500 mb-2">Lampiran</dt>
    <dd>
        <a 
            :href="route('nota-dinas.lampiran', nota.nota_id)" 
            target="_blank" 
            class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-lg text-indigo-700 hover:bg-indigo-100 hover:border-indigo-300 transition-colors duration-150"
        >
            <svg class="w-5 h-5 mr-2"><!-- Paperclip icon --></svg>
            <span class="font-medium">Lihat Lampiran</span>
            <svg class="w-4 h-4 ml-2"><!-- External link icon --></svg>
        </a>
    </dd>
</div>
```

**Sebelum**:
```vue
<a :href="nota.lampiran" target="_blank">Lihat Dokumen</a>
```

**Sesudah**:
```vue
<a :href="route('nota-dinas.lampiran', nota.nota_id)" target="_blank">
    Lihat Lampiran
</a>
```

### 4. Frontend - Kepala Instalasi (KepalaInstalasi/Show.vue)

**Updated Section**: Same as Admin

```vue
<div v-if="nota.lampiran" class="md:col-span-2">
    <dt class="text-sm font-medium text-gray-500 mb-2">Lampiran</dt>
    <dd>
        <a 
            :href="route('nota-dinas.lampiran', nota.nota_id)" 
            target="_blank" 
            class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-lg text-indigo-700 hover:bg-indigo-100 hover:border-indigo-300 transition-colors duration-150"
        >
            <svg class="w-5 h-5 mr-2"><!-- Paperclip icon --></svg>
            <span class="font-medium">Lihat Lampiran</span>
            <svg class="w-4 h-4 ml-2"><!-- External link icon --></svg>
        </a>
    </dd>
</div>
```

## 🎨 UI Improvements

### Tampilan Button Lampiran

**Before** (Simple link):
```
Lihat Dokumen
```

**After** (Styled button):
```
┌─────────────────────────────────────┐
│ 📎 Lihat Lampiran 🔗               │
└─────────────────────────────────────┘
```

**Features**:
- ✅ Background indigo-50 (soft blue)
- ✅ Border indigo-200
- ✅ Hover effects (darker background)
- ✅ Icon paperclip di kiri
- ✅ Icon external link di kanan
- ✅ Full width pada mobile (md:col-span-2)
- ✅ Smooth transitions

## 🔄 Workflow

### Scenario 1: Lampiran adalah URL External

```
User click "Lihat Lampiran"
       ↓
Route: /nota-dinas/123/lampiran
       ↓
Controller check: filter_var($lampiran, FILTER_VALIDATE_URL)
       ↓
Result: TRUE (e.g., https://example.com/file.pdf)
       ↓
Action: redirect($lampiran)
       ↓
User diarahkan ke URL external
```

### Scenario 2: Lampiran adalah File di Storage

```
User click "Lihat Lampiran"
       ↓
Route: /nota-dinas/123/lampiran
       ↓
Controller check: Storage::exists($lampiran)
       ↓
Result: TRUE (e.g., "uploads/nota-dinas/file.pdf")
       ↓
Action: Storage::download($lampiran)
       ↓
File downloaded ke browser user
```

### Scenario 3: Lampiran Tidak Ada

```
User click "Lihat Lampiran"
       ↓
Route: /nota-dinas/123/lampiran
       ↓
Controller check: !$notaDinas->lampiran
       ↓
Result: TRUE (field kosong)
       ↓
Action: redirect()->back()->with('error', ...)
       ↓
User kembali ke halaman sebelumnya
Flash message: "Lampiran tidak ditemukan"
```

## 🔒 Security

### Authorization
✅ Route protected dengan `auth` dan `verified` middleware
✅ Laravel automatic model binding untuk NotaDinas
✅ Tidak bisa akses file di luar storage (path traversal protection)

### Validation
✅ Check lampiran field exists
✅ Validate URL format untuk external links
✅ Check file exists di storage sebelum download
✅ Error handling untuk missing files

### Best Practices
✅ Tidak expose raw file path ke frontend
✅ Menggunakan route helper di frontend
✅ Open di tab baru (target="_blank")
✅ Proper error messages

## 📊 Data Types Support

| Lampiran Type | Storage | Example | Handler |
|---------------|---------|---------|---------|
| **External URL** | Cloud storage, CDN | `https://cdn.example.com/file.pdf` | Redirect |
| **Storage Path** | Local Laravel storage | `uploads/nota-dinas/file.pdf` | Download |
| **Public URL** | Public folder | `https://example.com/public/file.pdf` | Redirect |
| **Empty** | NULL or empty string | `null` | Error message |

## 🧪 Testing

### Test 1: Lihat Lampiran (URL External)
```bash
1. Admin create nota dinas dengan lampiran URL
   - lampiran: "https://drive.google.com/file/xyz"
2. Buka detail permintaan
3. Scroll ke section nota dinas
4. Click "Lihat Lampiran"
5. Verify:
   ✓ Redirect ke https://drive.google.com/file/xyz
   ✓ Opens in new tab
```

### Test 2: Download Lampiran (File Storage)
```bash
1. Admin create nota dinas dengan file upload
   - lampiran: "uploads/nota-dinas/document.pdf"
2. Buka detail permintaan
3. Click "Lihat Lampiran"
4. Verify:
   ✓ File downloaded
   ✓ Filename correct
   ✓ File can be opened
```

### Test 3: Lampiran Kosong
```bash
1. Create nota dinas tanpa lampiran
   - lampiran: null
2. Buka detail permintaan
3. Verify:
   ✓ Section "Lampiran" TIDAK muncul (v-if="nota.lampiran")
   ✓ No broken link
```

### Test 4: File Tidak Ditemukan
```bash
1. Manually update lampiran field
   - lampiran: "uploads/nota-dinas/deleted.pdf"
2. Delete file dari storage
3. Click "Lihat Lampiran"
4. Verify:
   ✓ Redirect back to previous page
   ✓ Flash error: "File lampiran tidak ditemukan"
```

### Test 5: Authorization
```bash
1. Copy link: /nota-dinas/123/lampiran
2. Logout
3. Try access link directly
4. Verify:
   ✓ Redirect to login page
   ✓ Cannot access without auth
```

## 🎯 Consistency with Cetak Nota Dinas

### Comparison

| Feature | Cetak Nota Dinas | Lihat Lampiran |
|---------|------------------|----------------|
| **Route Pattern** | `/permintaan/{id}/cetak-nota-dinas` | `/nota-dinas/{id}/lampiran` |
| **Parameter** | Permintaan model | NotaDinas model |
| **Target** | Same window | New tab (`target="_blank"`) |
| **Return** | Blade view | Redirect/Download |
| **Auth** | Required | Required |

### Why Different Pattern?

**Cetak Nota Dinas**:
- Takes `permintaan_id` karena render nota untuk permintaan tertentu
- Bisa ada multiple nota, ambil yang latest

**Lihat Lampiran**:
- Takes `nota_id` karena setiap nota punya lampiran berbeda
- Perlu spesifik nota mana yang lampirannya diakses

## 📦 Files Modified

### Backend
1. **`routes/web.php`**
   - Added route: `nota-dinas.lampiran`

2. **`app/Http/Controllers/PermintaanController.php`**
   - Added import: `NotaDinas`, `Storage`
   - Added method: `lihatLampiran()`

### Frontend
3. **`resources/js/Pages/Permintaan/Show.vue`**
   - Updated lampiran link to use route helper
   - Enhanced button styling

4. **`resources/js/Pages/KepalaInstalasi/Show.vue`**
   - Added lampiran section
   - Styled button matching Admin

## 🚀 Build Status

```bash
npm run build
✅ built in 6.07s
✅ 64 modules transformed
✅ All assets compiled successfully
```

## 📝 Usage Examples

### For Admin

```javascript
// Di Show.vue
<a :href="route('nota-dinas.lampiran', nota.nota_id)" target="_blank">
    Lihat Lampiran
</a>

// Generated URL
/nota-dinas/123/lampiran
```

### For Developers

```php
// Manual redirect
return redirect()->route('nota-dinas.lampiran', $notaDinas);

// Generate URL
$url = route('nota-dinas.lampiran', $notaDinas->nota_id);
```

## ✨ Key Benefits

| Benefit | Description |
|---------|-------------|
| **Consistent Routing** | Mengikuti pola yang sama dengan `cetak-nota-dinas` |
| **Security** | File access melalui controller, bukan direct link |
| **Flexibility** | Support URL external DAN file storage |
| **Error Handling** | Proper validation dan user-friendly messages |
| **Clean UI** | Styled button yang jelas dan menarik |
| **Maintainable** | Single source of truth untuk lampiran access |

## 🔄 Migration Path

**If lampiran was previously stored as direct URL**:
```php
// Old way (direct in DB)
lampiran: "https://example.com/file.pdf"

// New way (still works!)
lampiran: "https://example.com/file.pdf"
// Controller detects URL and redirects
```

**If lampiran is file path**:
```php
// Old way (might be public path)
lampiran: "public/uploads/file.pdf"

// New way (storage path)
lampiran: "uploads/nota-dinas/file.pdf"
// Controller uses Storage facade
```

**No database migration needed!** The method handles both cases.

---

**Status**: ✅ **COMPLETE**
**Build**: ✅ **Success**
**Date**: 2025-01-25
**Version**: 1.0.0

Admin dan Kepala Instalasi sekarang dapat melihat dan download lampiran nota dinas dengan aman melalui route khusus yang konsisten dengan pola cetak nota dinas!

**READY TO USE!** 🚀
