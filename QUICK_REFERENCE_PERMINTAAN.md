# Quick Reference - Permintaan Business Rules

## Status Permintaan

| Status | Can Edit? | Can Delete? | Who Can Action? |
|--------|-----------|-------------|-----------------|
| `diajukan` | ❌ No | ❌ No | Kepala Instalasi (review) |
| `proses` | ❌ No | ❌ No | Kepala Bidang (review) |
| `disetujui` | ❌ No | ❌ No | Workflow continues |
| `ditolak` | ✅ **Yes** | ✅ **Yes** | **Admin can fix & resubmit** |

## Routes Quick Reference

### Kepala Bidang URLs
```
/kepala-bidang                  → Redirect to dashboard
/kepala-bidang/dashboard        → Dashboard view
/kepala-bidang/index            → List permintaan
/kepala-bidang/permintaan/{id}  → Detail view
```

### Admin Permintaan URLs
```
/permintaan                     → List all
/permintaan/create              → Create new
/permintaan/{id}                → View detail
/permintaan/{id}/edit           → Edit (only if ditolak)
/permintaan/{id}/tracking       → Timeline view
```

## Common Scenarios

### Scenario 1: Create New Permintaan
1. Admin login
2. Go to `/permintaan/create`
3. Fill form (don't select status)
4. Submit
5. ✅ Status automatically set to "diajukan"

### Scenario 2: Permintaan Ditolak (Need Revision)
1. Kepala Instalasi/Bidang reject permintaan
2. Status becomes "ditolak"
3. Admin can now:
   - ✅ Edit the permintaan
   - ✅ Delete the permintaan
4. After edit & save:
   - Status back to "diajukan"
   - Goes to review again

### Scenario 3: Cannot Edit/Delete
1. Try to edit permintaan with status "proses"
2. ❌ Redirect with error message
3. Error: "Permintaan hanya dapat diedit jika dalam status ditolak"

## Error Messages

| Action | Condition | Error Message |
|--------|-----------|---------------|
| Edit | Status ≠ ditolak | "Permintaan hanya dapat diedit jika dalam status ditolak (revisi)." |
| Update | Status ≠ ditolak | "Permintaan hanya dapat diupdate jika dalam status ditolak (revisi)." |
| Delete | Status ≠ ditolak | "Permintaan hanya dapat dihapus jika dalam status ditolak." |

## Testing Commands

```bash
# Test route redirect
curl -I http://localhost:8000/kepala-bidang
# Should redirect to /kepala-bidang/dashboard

# Test create (check status auto-set)
# Login as admin → Create permintaan → Check DB:
SELECT status FROM permintaan WHERE permintaan_id = {latest_id};
# Should be: diajukan

# Test edit authorization
# Try edit permintaan with status = 'proses'
# Should get error message
```

## Quick SQL Queries

```sql
-- Check status distribution
SELECT status, COUNT(*) as total 
FROM permintaan 
GROUP BY status;

-- Find editable permintaan (ditolak only)
SELECT permintaan_id, deskripsi, status 
FROM permintaan 
WHERE status = 'ditolak';

-- Check recent permintaan
SELECT permintaan_id, bidang, status, created_at 
FROM permintaan 
ORDER BY created_at DESC 
LIMIT 10;
```

---

**Quick tip**: Only permintaan with status "ditolak" can be edited or deleted!
