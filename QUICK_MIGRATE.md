# QUICK COMMANDS - Workflow Revision Migration

## 🚀 Run Migration

```bash
# Backup database first
mysqldump -u root -p pengadaan_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Run migration
php artisan migrate

# Verify migration
php artisan migrate:status
```

## 🧪 Test After Migration

```bash
# Start Laravel
php artisan serve

# Test tracking in browser:
# http://localhost:8000/permintaan/{id}/tracking

# Or test in tinker:
php artisan tinker
```

### In Tinker:
```php
// Test model relations
$perencanaan = App\Models\Perencanaan::first();
$pengadaan = $perencanaan->pengadaan()->first();
dump($pengadaan);

$kso = $pengadaan->kso()->first();
dump($kso);

// Test tracking
$permintaan = App\Models\Permintaan::first();
$timeline = $permintaan->getTimelineTracking();
dump($timeline);

$progress = $permintaan->getProgressPercentage();
dump("Progress: {$progress}%");
```

## 🔍 Verify Data Migration

```sql
-- Check new columns populated
SELECT 
    COUNT(*) as total_pengadaan,
    COUNT(perencanaan_id) as with_perencanaan
FROM pengadaan;

SELECT 
    COUNT(*) as total_kso,
    COUNT(pengadaan_id) as with_pengadaan
FROM kso;

SELECT 
    COUNT(*) as total_nota,
    COUNT(kso_id) as with_kso
FROM nota_penerimaan;
```

## ⏪ Rollback (if needed)

```bash
# Rollback last migration
php artisan migrate:rollback --step=1

# Restore from backup
mysql -u root -p pengadaan_db < backup_YYYYMMDD_HHMMSS.sql
```

## ✅ Quick Check

```bash
# Check if migration ran successfully
php artisan migrate:status | grep "revise_workflow"

# Should show:
# Ran  2025_11_06_000001_revise_workflow_pengadaan_kso
```
