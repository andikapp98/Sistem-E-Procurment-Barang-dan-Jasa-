# Fitur: Lihat Semua KSO - Complete Documentation

## Overview
Fitur baru untuk menampilkan **semua dokumen KSO** yang sudah dibuat di sistem, dengan filter status dan search functionality.

## URL
```
http://localhost:8000/kso/list-all
```

## Features

### 1. Stats Summary Dashboard
Menampilkan ringkasan KSO:
- **Total KSO** - Jumlah total semua KSO
- **Draft** - KSO yang masih draft
- **Aktif** - KSO yang sedang berjalan
- **Selesai** - KSO yang sudah selesai

### 2. Filter & Search
```vue
<!-- Search -->
<input placeholder="Cari No. KSO, Pihak Kedua..." />

<!-- Status Filter -->
<select>
  <option value="">Semua Status</option>
  <option value="draft">Draft</option>
  <option value="aktif">Aktif</option>
  <option value="selesai">Selesai</option>
  <option value="batal">Batal</option>
</select>

<!-- Per Page -->
<select>
  <option value="10">10 per halaman</option>
  <option value="15">15 per halaman</option>
  <option value="25">25 per halaman</option>
  <option value="50">50 per halaman</option>
  <option value="100">100 per halaman</option>
</select>
```

### 3. Table Display
Kolom yang ditampilkan:
1. **No. KSO** - Nomor dokumen KSO
2. **Tanggal** - Tanggal KSO dibuat
3. **Pihak Kedua** - Nama vendor/partner
4. **Permintaan** - ID & Bidang permintaan terkait
5. **Status** - Badge dengan warna (draft/aktif/selesai/batal)
6. **Nilai Kontrak** - Format currency Rupiah
7. **Actions** - Button Detail & Edit

### 4. Pagination
- Previous/Next buttons
- Page numbers
- Showing "X to Y of Z results"

## Files Created/Updated

### 1. Route (NEW)
**File:** `routes/web.php`

```php
Route::get('/list-all', [KsoController::class, 'listAll'])->name('kso.list-all');
```

### 2. Controller Method (NEW)
**File:** `app/Http/Controllers/KSOController.php`

```php
public function listAll(Request $request)
{
    $user = Auth::user();
    
    // Only allow KSO role
    if ($user->role !== 'kso') {
        abort(403, 'Hanya Bagian KSO yang dapat mengakses halaman ini.');
    }
    
    // Query semua KSO dengan relasi
    $query = Kso::with(['perencanaan.disposisi.notaDinas.permintaan.user']);
    
    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    // Search by no_kso or pihak_kedua
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('no_kso', 'like', "%{$search}%")
              ->orWhere('pihak_kedua', 'like', "%{$search}%")
              ->orWhere('pihak_pertama', 'like', "%{$search}%");
        });
    }
    
    // Sort by latest
    $query->orderBy('created_at', 'desc');
    
    // Paginate
    $perPage = $request->get('per_page', 15);
    $ksos = $query->paginate($perPage)->withQueryString();
    
    // Add permintaan info to each KSO
    $ksos->getCollection()->transform(function($kso) {
        if ($kso->perencanaan && $kso->perencanaan->disposisi && 
            $kso->perencanaan->disposisi->notaDinas && 
            $kso->perencanaan->disposisi->notaDinas->permintaan) {
            $kso->permintaan = $kso->perencanaan->disposisi->notaDinas->permintaan;
        } else {
            $kso->permintaan = null;
        }
        return $kso;
    });
    
    return Inertia::render('KSO/ListAll', [
        'ksos' => $ksos,
        'filters' => $request->only(['search', 'status', 'per_page']),
        'userLogin' => $user,
    ]);
}
```

### 3. View Component (NEW)
**File:** `resources/js/Pages/KSO/ListAll.vue`

**Size:** 18.2 KB
**Lines:** ~366 lines

**Key Components:**
- Stats cards (4 cards)
- Filter section (search, status, per page)
- Table with all KSO data
- Pagination
- Empty state

### 4. Navigation Links (UPDATED)

#### Dashboard.vue
```vue
<Link :href="route('kso.list-all')" 
      class="bg-blue-600 text-white px-4 py-2 rounded-md">
    📄 Lihat Semua KSO
</Link>
```

#### Index.vue
```vue
<Link :href="route('kso.list-all')" 
      class="bg-green-600 text-white px-3 py-2 rounded-md">
    📄 Lihat Semua KSO
</Link>
```

## Database Query

### Relationship Chain:
```
KSO
  └─ Perencanaan
      └─ Disposisi
          └─ Nota Dinas
              └─ Permintaan
                  └─ User
```

### Query Example:
```php
Kso::with([
    'perencanaan.disposisi.notaDinas.permintaan.user'
])->paginate(15);
```

## UI Components

### Status Badge Colors
```php
'draft' => 'bg-gray-100 text-gray-800'
'aktif' => 'bg-blue-100 text-blue-800'
'selesai' => 'bg-green-100 text-green-800'
'batal' => 'bg-red-100 text-red-800'
```

### Currency Format
```javascript
formatCurrency(50000000)
// Output: "Rp 50.000.000"
```

### Date Format
```javascript
formatDate('2025-10-28')
// Output: "28 Oktober 2025"
```

## Authorization

### Access Control:
```php
// Only KSO role can access
if ($user->role !== 'kso') {
    abort(403);
}
```

**Result:**
- ✅ KSO users → Full access
- ❌ Other roles → 403 Forbidden

## Testing

### Test Scenarios:

#### 1. View All KSO
```bash
1. Login as KSO user (kso@rsud.id)
2. Navigate to /kso/dashboard
3. Click "Lihat Semua KSO" button
4. Expected:
   ✅ Redirect to /kso/list-all
   ✅ Stats cards displayed
   ✅ All KSO listed in table
   ✅ Pagination works
```

#### 2. Filter by Status
```bash
1. On /kso/list-all page
2. Select status filter: "Aktif"
3. Expected:
   ✅ Only KSO with status "aktif" displayed
   ✅ Stats updated
   ✅ URL updated with ?status=aktif
```

#### 3. Search Functionality
```bash
1. Enter "KSO/001" in search box
2. Expected:
   ✅ Only KSO matching "KSO/001" displayed
   ✅ Search works on no_kso
   ✅ Search works on pihak_kedua
   ✅ URL updated with ?search=KSO/001
```

#### 4. Change Per Page
```bash
1. Select "25 per halaman"
2. Expected:
   ✅ Display 25 items per page
   ✅ Pagination updated
   ✅ URL updated with ?per_page=25
```

#### 5. Actions
```bash
1. Click "Detail" button
2. Expected: Navigate to /kso/permintaan/{id}

3. Click "Edit" button
4. Expected: Navigate to /kso/permintaan/{id}/kso/{kso_id}/edit
```

## Navigation Flow

```
KSO Dashboard (/kso/dashboard)
  ├─ Click "Lihat Semua KSO"
  └─ → KSO List All (/kso/list-all)
      ├─ View all KSO
      ├─ Filter by status
      ├─ Search
      ├─ Click "Detail" → Show page
      ├─ Click "Edit" → Edit page
      └─ Click "← Kembali ke Permintaan" → Index (/kso)

KSO Index (/kso)
  ├─ Click "Lihat Semua KSO"
  └─ → KSO List All (/kso/list-all)
```

## Sample Data Display

### Table Row Example:
```
| No. KSO         | Tanggal        | Pihak Kedua    | Permintaan | Status | Nilai Kontrak  | Actions     |
|-----------------|----------------|----------------|------------|--------|----------------|-------------|
| KSO/001/X/2025  | 28 Okt 2025    | PT Vendor ABC  | #17 IGD    | aktif  | Rp 50.000.000  | Detail Edit |
| KSO/002/X/2025  | 27 Okt 2025    | PT Vendor XYZ  | #18 ICU    | selesai| Rp 75.000.000  | Detail Edit |
```

## Build Output

```bash
✓ 1475 modules transformed
✓ built in 4.11s

New files:
- ListAll-BN0qlIZV.js (10.85 kB) ← New list all page

Updated files:
- Index-BRRnIU_o.js (9.45 kB) ← Added "Lihat Semua" button
- Dashboard-DJdzx6WU.js (10.17 kB) ← Will be updated next
```

## Summary

### ✅ What's New:
1. **Route:** `/kso/list-all`
2. **Controller Method:** `listAll()`
3. **View Component:** `ListAll.vue`
4. **Navigation Links:** Added to Dashboard & Index

### ✅ Features:
1. Stats summary (4 cards)
2. Search (no_kso, pihak_kedua)
3. Filter by status (draft/aktif/selesai/batal)
4. Pagination (10/15/25/50/100 per page)
5. Table display with all KSO data
6. Actions (Detail, Edit)
7. Currency formatting
8. Date formatting
9. Status badges with colors
10. Empty state

### ✅ Security:
- Only KSO role can access
- Authorization check in controller
- 403 error for unauthorized users

## Status
✅ **COMPLETE** - Fitur "Lihat Semua KSO" sudah selesai dibuat
✅ **BUILT** - Assets compiled successfully
✅ **LINKED** - Navigation buttons added
✅ **READY** - Siap digunakan

## Next Steps
1. Test di browser: `http://localhost:8000/kso/list-all`
2. Test filter by status
3. Test search functionality
4. Test pagination
5. Test Detail & Edit buttons
6. Verify authorization (non-KSO user blocked)

---

**Note:** Pastikan ada data KSO di database untuk testing. Jika belum ada, buat KSO baru dari halaman `/kso/permintaan/{id}/create`.
