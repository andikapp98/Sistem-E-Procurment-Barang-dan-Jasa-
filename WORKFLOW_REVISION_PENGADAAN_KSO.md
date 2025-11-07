# 🔄 REVISI WORKFLOW - PENGADAAN DULU, KEMUDIAN KSO

## 📋 Perubahan Urutan Workflow

### ❌ Urutan Lama:
```
1. Permintaan
2. Nota Dinas
3. Disposisi
4. Perencanaan
5. KSO          ← Step 5
6. Pengadaan    ← Step 6
7. Nota Penerimaan
8. Serah Terima
```

### ✅ Urutan Baru (REVISI):
```
1. Permintaan
2. Nota Dinas
3. Disposisi
4. Perencanaan
5. Pengadaan    ← Step 5 (REVISI: Pengadaan dulu)
6. KSO          ← Step 6 (REVISI: KSO setelah Pengadaan)
7. Nota Penerimaan
8. Serah Terima
```

---

## 🔧 Perubahan Relasi Database

### Schema Baru:

```
Perencanaan
    ↓ (1:N)
Pengadaan      ← NEW: Pengadaan langsung dari Perencanaan
    ↓ (1:N)
KSO            ← REVISED: KSO dari Pengadaan
    ↓ (1:N)
NotaPenerimaan ← REVISED: Nota Penerimaan dari KSO
    ↓ (1:N)
SerahTerima
```

### Detail Perubahan:

#### 1. Table `pengadaan`
```sql
-- ADD new column
ALTER TABLE pengadaan ADD COLUMN perencanaan_id INT AFTER pengadaan_id;
ALTER TABLE pengadaan ADD INDEX idx_perencanaan_id (perencanaan_id);
ALTER TABLE pengadaan ADD FOREIGN KEY (perencanaan_id) REFERENCES perencanaan(perencanaan_id);

-- kso_id is now DEPRECATED (keep for backward compatibility)
```

#### 2. Table `kso`
```sql
-- ADD new column
ALTER TABLE kso ADD COLUMN pengadaan_id INT AFTER kso_id;
ALTER TABLE kso ADD INDEX idx_pengadaan_id (pengadaan_id);
ALTER TABLE kso ADD FOREIGN KEY (pengadaan_id) REFERENCES pengadaan(pengadaan_id);

-- perencanaan_id is now DEPRECATED (keep for backward compatibility)
```

#### 3. Table `nota_penerimaan`
```sql
-- ADD new column
ALTER TABLE nota_penerimaan ADD COLUMN kso_id INT AFTER nota_penerimaan_id;
ALTER TABLE nota_penerimaan ADD INDEX idx_kso_id (kso_id);
ALTER TABLE nota_penerimaan ADD FOREIGN KEY (kso_id) REFERENCES kso(kso_id);

-- pengadaan_id is now DEPRECATED (keep for backward compatibility)
```

---

## 📝 Migration Script

File: `database/migrations/2025_11_06_000001_revise_workflow_pengadaan_kso.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * REVISI WORKFLOW: Pengadaan → KSO → Nota Penerimaan
     */
    public function up(): void
    {
        // 1. Add perencanaan_id to pengadaan table
        Schema::table('pengadaan', function (Blueprint $table) {
            $table->unsignedBigInteger('perencanaan_id')->nullable()->after('pengadaan_id');
            $table->index('perencanaan_id');
            $table->foreign('perencanaan_id')
                  ->references('perencanaan_id')
                  ->on('perencanaan')
                  ->onDelete('cascade');
        });

        // 2. Add pengadaan_id to kso table
        Schema::table('kso', function (Blueprint $table) {
            $table->unsignedBigInteger('pengadaan_id')->nullable()->after('kso_id');
            $table->index('pengadaan_id');
            $table->foreign('pengadaan_id')
                  ->references('pengadaan_id')
                  ->on('pengadaan')
                  ->onDelete('cascade');
        });

        // 3. Add kso_id to nota_penerimaan table
        Schema::table('nota_penerimaan', function (Blueprint $table) {
            $table->unsignedBigInteger('kso_id')->nullable()->after('nota_penerimaan_id');
            $table->index('kso_id');
            $table->foreign('kso_id')
                  ->references('kso_id')
                  ->on('kso')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove new foreign keys
        Schema::table('nota_penerimaan', function (Blueprint $table) {
            $table->dropForeign(['kso_id']);
            $table->dropIndex(['kso_id']);
            $table->dropColumn('kso_id');
        });

        Schema::table('kso', function (Blueprint $table) {
            $table->dropForeign(['pengadaan_id']);
            $table->dropIndex(['pengadaan_id']);
            $table->dropColumn('pengadaan_id');
        });

        Schema::table('pengadaan', function (Blueprint $table) {
            $table->dropForeign(['perencanaan_id']);
            $table->dropIndex(['perencanaan_id']);
            $table->dropColumn('perencanaan_id');
        });
    }
};
```

---

## 🔄 Model Updates

### ✅ Updated Models:

1. **Permintaan.php**
   - ✅ `getTimelineTracking()` - Urutan: Perencanaan → Pengadaan → KSO
   - ✅ `getNextStep()` - Step 5: Pengadaan, Step 6: KSO
   - ✅ `getCompleteTracking()` - Step 5: Pengadaan, Step 6: KSO
   - ✅ `getRemainingSteps()` - Order updated

2. **Perencanaan.php**
   - ✅ Added: `pengadaan()` relation (hasMany)
   - ⚠️ Kept: `kso()` relation (deprecated, for backward compatibility)

3. **Pengadaan.php**
   - ✅ Added: `perencanaan_id` field
   - ✅ Added: `perencanaan()` relation (belongsTo)
   - ✅ Updated: `kso()` relation (hasMany instead of belongsTo)
   - ⚠️ Kept: `kso_id` field (deprecated)

4. **Kso.php**
   - ✅ Added: `pengadaan_id` field
   - ✅ Updated: `pengadaan()` relation (belongsTo instead of hasMany)
   - ✅ Updated: `notaPenerimaan()` relation (hasMany)
   - ⚠️ Kept: `perencanaan_id` field (deprecated)

5. **NotaPenerimaan.php**
   - ✅ Added: `kso_id` field
   - ✅ Added: `kso()` relation (belongsTo)
   - ⚠️ Kept: `pengadaan_id` field (deprecated)

---

## 🚀 Cara Migrasi

### Step 1: Run Migration
```bash
php artisan migrate
```

### Step 2: Data Migration (Optional)
Jika ada data existing, migrate data dari old schema ke new schema:

```php
<?php
// File: database/seeders/MigrateWorkflowDataSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengadaan;
use App\Models\Kso;
use App\Models\NotaPenerimaan;
use Illuminate\Support\Facades\DB;

class MigrateWorkflowDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Update Pengadaan: Copy perencanaan_id from related KSO
        DB::statement("
            UPDATE pengadaan p
            JOIN kso k ON k.kso_id = p.kso_id
            SET p.perencanaan_id = k.perencanaan_id
            WHERE p.perencanaan_id IS NULL
        ");

        // 2. Update KSO: Set pengadaan_id from pengadaan table
        DB::statement("
            UPDATE kso k
            JOIN pengadaan p ON p.kso_id = k.kso_id
            SET k.pengadaan_id = p.pengadaan_id
            WHERE k.pengadaan_id IS NULL
        ");

        // 3. Update NotaPenerimaan: Copy kso_id from related Pengadaan
        DB::statement("
            UPDATE nota_penerimaan np
            JOIN pengadaan p ON p.pengadaan_id = np.pengadaan_id
            SET np.kso_id = p.kso_id
            WHERE np.kso_id IS NULL
        ");

        echo "Data migration completed!\n";
    }
}
```

Run seeder:
```bash
php artisan db:seed --class=MigrateWorkflowDataSeeder
```

---

## ✅ Testing Checklist

### Manual Testing:

1. **Test Timeline Tracking**
   - [ ] Buat permintaan baru
   - [ ] Progress through: Permintaan → Nota Dinas → Disposisi → Perencanaan
   - [ ] Buat Pengadaan (should be step 5, 62.5%)
   - [ ] Buat KSO (should be step 6, 75%)
   - [ ] Verify tracking shows correct order

2. **Test Model Relations**
   ```php
   $perencanaan = Perencanaan::find(1);
   $pengadaan = $perencanaan->pengadaan()->first(); // Should work
   
   $kso = $pengadaan->kso()->first(); // Should work
   
   $notaPenerimaan = $kso->notaPenerimaan()->first(); // Should work
   ```

3. **Test Progress Calculation**
   - [ ] Progress 50% after Perencanaan
   - [ ] Progress 62.5% after Pengadaan
   - [ ] Progress 75% after KSO
   - [ ] Progress 87.5% after Nota Penerimaan
   - [ ] Progress 100% after Serah Terima

---

## 📊 Impact Analysis

### Breaking Changes:
- ❌ None for existing data (backward compatible fields kept)
- ⚠️ Controllers need update if directly querying old relations

### Non-Breaking Changes:
- ✅ New fields added as nullable
- ✅ Old fields kept for backward compatibility
- ✅ Tracking methods updated to use new workflow

### Future Cleanup (Optional):
After confirming all data migrated and system stable:
```sql
-- Remove deprecated columns (CAREFUL!)
ALTER TABLE pengadaan DROP COLUMN kso_id;
ALTER TABLE kso DROP COLUMN perencanaan_id;
ALTER TABLE nota_penerimaan DROP COLUMN pengadaan_id;
```

---

## 📝 Documentation Updates Required:

1. **Update README.md** - New workflow diagram
2. **Update WORKFLOW_DIAGRAM.md** - Step 5 & 6 swapped
3. **Update API docs** - If any endpoints affected
4. **Update user guides** - New workflow order

---

## ✅ Verification

After migration, verify:

```sql
-- Check Pengadaan has perencanaan_id
SELECT COUNT(*) FROM pengadaan WHERE perencanaan_id IS NOT NULL;

-- Check KSO has pengadaan_id
SELECT COUNT(*) FROM kso WHERE pengadaan_id IS NOT NULL;

-- Check NotaPenerimaan has kso_id
SELECT COUNT(*) FROM nota_penerimaan WHERE kso_id IS NOT NULL;

-- Verify complete chain
SELECT 
    p.permintaan_id,
    pr.perencanaan_id,
    pg.pengadaan_id,
    k.kso_id,
    np.nota_penerimaan_id
FROM permintaan p
LEFT JOIN nota_dinas nd ON p.permintaan_id = nd.permintaan_id
LEFT JOIN disposisi d ON nd.nota_id = d.nota_id
LEFT JOIN perencanaan pr ON d.disposisi_id = pr.disposisi_id
LEFT JOIN pengadaan pg ON pr.perencanaan_id = pg.perencanaan_id
LEFT JOIN kso k ON pg.pengadaan_id = k.pengadaan_id
LEFT JOIN nota_penerimaan np ON k.kso_id = np.kso_id
WHERE p.permintaan_id = 1;
```

---

**Status:** ✅ Code Updated  
**Migration:** ⏳ Pending  
**Testing:** ⏳ Pending  
**Date:** 2025-11-06
