# Fitur Harga Perkiraan Satuan (HPS) - Staff Perencanaan

## Status: ✅ COMPLETED

## Overview

Fitur lengkap untuk Staff Perencanaan membuat **Harga Perkiraan Satuan (HPS)** dengan tabel item yang dinamis. Form ini memungkinkan input multiple items dengan perhitungan otomatis.

## Fitur Utama

### 1. **Header HPS**
- **PPK** (Required) - Pejabat Pembuat Komitmen
- **Surat Penawaran Harga** (Required) - Nomor surat penawaran

### 2. **Tabel Item Dinamis**

Setiap item memiliki kolom:
- **Item** (Required) - Nama barang/jasa
- **Volume** (Required) - Jumlah/quantity
- **Satuan** (Required) - Unit/Pcs/Set/Box/Pack/Kg/Liter/Meter/Buah/Lembar
- **Harga Satuan** (Required) - Harga per unit (format rupiah)
- **Type** (Optional) - Tipe/model
- **Merk** (Optional) - Merk/brand
- **Total** (Auto-calculated) - Volume × Harga Satuan

### 3. **Features**
- ✅ Add/Remove item rows dinamis
- ✅ Auto-calculate total per item
- ✅ Auto-calculate grand total
- ✅ Format rupiah dengan pemisah ribuan
- ✅ Validasi per field
- ✅ Minimum 1 item required
- ✅ Responsive table layout

## Files Created/Modified

### 1. New Vue Component
**File:** `resources/js/Pages/StaffPerencanaan/CreateHPS.vue`

Comprehensive form dengan:
- Header section (PPK, Surat Penawaran Harga)
- Dynamic table dengan add/remove rows
- Auto-calculation total & grand total
- Format rupiah untuk harga
- Validation per item
- Responsive design

### 2. Database Migrations
**File:** `database/migrations/2025_10_21_145819_create_hps_table.php`

Membuat 2 tables:

#### Table: `hps` (Header)
```sql
hps_id (PK)
permintaan_id (FK)
ppk
surat_penawaran_harga
grand_total (decimal 15,2)
timestamps
```

#### Table: `hps_items` (Detail)
```sql
hps_item_id (PK)
hps_id (FK)
nama_item
volume (integer)
satuan
harga_satuan (decimal 15,2)
type (nullable)
merk (nullable)
total (decimal 15,2)
timestamps
```

### 3. New Models

#### Model: `Hps.php`
```php
class Hps extends Model
{
    protected $fillable = [
        'permintaan_id', 'ppk', 'surat_penawaran_harga', 'grand_total'
    ];
    
    public function permintaan() { ... }
    public function items() { ... }
}
```

#### Model: `HpsItem.php`
```php
class HpsItem extends Model
{
    protected $fillable = [
        'hps_id', 'nama_item', 'volume', 'satuan', 
        'harga_satuan', 'type', 'merk', 'total'
    ];
    
    public function hps() { ... }
}
```

### 4. Controller Methods
**File:** `app/Http/Controllers/StaffPerencanaanController.php`

#### New Method: `createHPS()`
```php
public function createHPS(Permintaan $permintaan)
{
    $permintaan->load('user');
    return Inertia::render('StaffPerencanaan/CreateHPS', [
        'permintaan' => $permintaan,
    ]);
}
```

#### New Method: `storeHPS()`
```php
public function storeHPS(Request $request, Permintaan $permintaan)
{
    // Validasi header & items
    $data = $request->validate([...]);
    
    // Hitung grand total
    $grandTotal = array_sum(array_column($data['items'], 'total'));
    
    // Create HPS header
    $hps = Hps::create([...]);
    
    // Create HPS items (loop)
    foreach ($data['items'] as $item) {
        HpsItem::create([...]);
    }
    
    // Update permintaan
    $permintaan->update([...]);
    
    return redirect()->with('success', '...');
}
```

**Features:**
- Validasi lengkap header dan semua items
- Auto-calculate grand total
- Save to 2 tables (hps & hps_items)
- Update deskripsi permintaan
- Flash message dengan jumlah item

### 5. Routes
**File:** `routes/web.php`

```php
Route::get('/permintaan/{permintaan}/hps/create', [StaffPerencanaanController::class, 'createHPS'])->name('hps.create');
Route::post('/permintaan/{permintaan}/hps', [StaffPerencanaanController::class, 'storeHPS'])->name('hps.store');
```

### 6. Updated Show Page
**File:** `resources/js/Pages/StaffPerencanaan/Show.vue`

Added HPS button (5th button):
```vue
<!-- Buat HPS -->
<Link :href="route('staff-perencanaan.hps.create', permintaan.permintaan_id)"
    class="... bg-indigo-600 hover:bg-indigo-700">
    <svg>...</svg>
    <span>Buat HPS</span>
</Link>
```

**Updated Layout:**
- Changed from 4-column to **5-column grid**
- Indigo/purple color for HPS button
- Calculator icon for HPS

## Data Flow

```
Staff Perencanaan → Buat HPS
    ↓
Form Input (PPK, Surat Penawaran)
    ↓
Add Items (dinamis, min 1 item)
    ↓
Input per item (Item, Volume, Satuan, Harga, Type, Merk)
    ↓
Auto-calculate Total & Grand Total
    ↓
Submit → Validation
    ↓
Create HPS Header
    ↓
Create HPS Items (loop)
    ↓
Update Permintaan (deskripsi)
    ↓
Redirect dengan success message
```

## Validation Rules

### Header
```php
'ppk' => 'required|string'
'surat_penawaran_harga' => 'required|string'
'items' => 'required|array|min:1'
```

### Items (per item)
```php
'items.*.nama_item' => 'required|string'
'items.*.volume' => 'required|integer|min:1'
'items.*.satuan' => 'required|string'
'items.*.harga_satuan' => 'required|numeric|min:0'
'items.*.type' => 'nullable|string'
'items.*.merk' => 'nullable|string'
'items.*.total' => 'required|numeric|min:0'
```

## Usage Example

### 1. Access Form
1. Login sebagai Staff Perencanaan
2. Buka detail permintaan
3. Klik tombol **"Buat HPS"** (indigo, icon calculator)

### 2. Fill Header
```
PPK: Dr. Ahmad Fauzi, S.E., M.M.
Surat Penawaran Harga: SPH/001/2025
```

### 3. Add Items
**Item 1:**
```
Item: Ventilator ICU
Volume: 2
Satuan: Unit
Harga Satuan: Rp 150.000.000
Type: Portable
Merk: Philips
Total: Rp 300.000.000 (auto)
```

**Item 2:**
```
Item: Patient Monitor
Volume: 5
Satuan: Unit
Harga Satuan: Rp 25.000.000
Type: 12 Inch
Merk: GE Healthcare
Total: Rp 125.000.000 (auto)
```

**Item 3:**
```
Item: Syringe Pump
Volume: 10
Satuan: Unit
Harga Satuan: Rp 15.000.000
Type: Double Channel
Merk: Terumo
Total: Rp 150.000.000 (auto)
```

### 4. Grand Total
```
Grand Total: Rp 575.000.000 (auto-calculated)
```

### 5. Submit
- Klik **"Simpan HPS"**
- Loading: "Menyimpan..."
- Save to database
- Redirect dengan success message: "HPS berhasil dibuat dengan 3 item"

## Database Impact

### Table: `hps`
```sql
INSERT INTO hps SET
    permintaan_id = 1,
    ppk = 'Dr. Ahmad Fauzi, S.E., M.M.',
    surat_penawaran_harga = 'SPH/001/2025',
    grand_total = 575000000.00
```

### Table: `hps_items`
```sql
-- Item 1
INSERT INTO hps_items SET
    hps_id = 1,
    nama_item = 'Ventilator ICU',
    volume = 2,
    satuan = 'Unit',
    harga_satuan = 150000000.00,
    type = 'Portable',
    merk = 'Philips',
    total = 300000000.00

-- Item 2
INSERT INTO hps_items SET
    hps_id = 1,
    nama_item = 'Patient Monitor',
    volume = 5,
    satuan = 'Unit',
    harga_satuan = 25000000.00,
    type = '12 Inch',
    merk = 'GE Healthcare',
    total = 125000000.00

-- Item 3 (and so on...)
```

### Table: `permintaan`
```sql
UPDATE permintaan SET
    deskripsi = CONCAT(deskripsi, '

[HPS DIBUAT]
PPK: Dr. Ahmad Fauzi, S.E., M.M.
Surat Penawaran: SPH/001/2025
Total Item: 3
Grand Total: Rp 575.000.000')
```

## UI/UX Features

### Table Features
- **Dynamic Rows** - Add/remove item on-the-fly
- **Auto-calculation** - Total per row & grand total
- **Format Rupiah** - Pemisah ribuan otomatis
- **Validation Indicators** - Red border untuk error
- **Dropdown Satuan** - 10 pilihan satuan umum
- **Minimum 1 Item** - Tidak bisa hapus jika hanya 1 item

### Visual Elements
- **Indigo theme** untuk HPS (#4F46E5)
- **Table responsive** dengan horizontal scroll
- **Green button** "Tambah Item"
- **Red delete icon** per row
- **Green grand total** di footer table
- **Loading spinner** saat submit

### Input Components
- **Text inputs** untuk nama item, type, merk
- **Number input** untuk volume
- **Select dropdown** untuk satuan
- **Text input dengan format rupiah** untuk harga satuan
- **Read-only total** dengan format rupiah

## Satuan Options
1. Unit
2. Pcs
3. Set
4. Box
5. Pack
6. Kg
7. Liter
8. Meter
9. Buah
10. Lembar

## Testing Checklist

- [x] Migration berhasil create 2 tables
- [x] Models created dengan relationships
- [x] Form dapat diakses
- [x] Add item berfungsi
- [x] Remove item berfungsi (tidak bisa jika 1 item)
- [x] Auto-calculate total per item
- [x] Auto-calculate grand total
- [x] Format rupiah berfungsi
- [x] Validasi header berfungsi
- [x] Validasi items berfungsi
- [x] Submit berhasil save ke 2 tables
- [x] Permintaan ter-update
- [x] Redirect dengan success message
- [x] Responsive di mobile

## URLs

- **Create Form:** `/staff-perencanaan/permintaan/{id}/hps/create`
- **Submit:** POST `/staff-perencanaan/permintaan/{id}/hps`

## Route Names

- **Create:** `staff-perencanaan.hps.create`
- **Store:** `staff-perencanaan.hps.store`

## JavaScript Functions

```javascript
// Format rupiah
formatRupiah(value)

// Format display with "Rp" prefix
formatRupiahDisplay(value)

// Handle harga satuan input
handleHargaSatuanInput(index, event)

// Calculate total per item
calculateTotal(index)

// Computed grand total
grandTotal = sum of all item.total

// Add new item row
addItem()

// Remove item row
removeItem(index)
```

## Benefits

1. **Dynamic Entry** - Add unlimited items
2. **Auto-calculation** - No manual calculation needed
3. **Format Rupiah** - Easy to read numbers
4. **Comprehensive Data** - Include type & merk
5. **Validation** - Ensure data completeness
6. **Structured Data** - Normalized database design

## Summary

✅ Form HPS dengan tabel dinamis berhasil dibuat  
✅ Database migration (2 tables: hps & hps_items)  
✅ Models dengan relationships  
✅ Controller methods untuk create & store  
✅ Routes ditambahkan  
✅ Auto-calculation total & grand total  
✅ Format rupiah dengan pemisah ribuan  
✅ Add/remove items dinamis  
✅ Validasi lengkap  
✅ Button terintegrasi di halaman detail (5 tombol aksi)  

**Status: READY FOR PRODUCTION**

**Date:** 2025-10-21  
**Feature:** Harga Perkiraan Satuan (HPS)  
**Type:** Major New Feature  
**Impact:** Complete HPS with dynamic item table  
**Tables:** hps, hps_items  
**Migration:** 2025_10_21_145819_create_hps_table.php  

## Staff Perencanaan - 5 Action Buttons

1. **Nota Dinas Usulan** (Hijau) - Form lengkap dengan pagu & pajak
2. **Nota Dinas Pembelian** (Biru) - Form sederhana permintaan pembelian
3. **DPP** (Orange) - 19 fields dokumen persiapan pengadaan
4. **HPS** (Indigo) - Tabel dinamis harga perkiraan satuan
5. **Disposisi** (Ungu) - Form manual disposisi
