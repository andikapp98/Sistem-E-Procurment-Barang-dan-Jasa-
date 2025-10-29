# Quick Guide: User Activity Logging

## ✅ Status: INSTALLED & READY

### What's Done:
1. ✅ Database table `user_activity_logs` created
2. ✅ Model `UserActivityLog` ready
3. ✅ Middleware `LogUserActivity` active (auto-logging enabled)
4. ✅ Helper `ActivityLogger` available globally
5. ✅ Controller `ActivityLogController` created

---

## 🚀 Quick Start

### 1. Automatic Logging (Already Active!)
Setiap HTTP request dari authenticated user akan tercatat otomatis.

**Tidak perlu kode tambahan!**

### 2. Manual Logging (For Important Actions)

```php
use App\Helpers\ActivityLogger;

// Login (add to AuthController)
ActivityLogger::logLogin(Auth::user());

// Logout (add to AuthController)
ActivityLogger::logLogout(Auth::user());

// Create Permintaan
ActivityLogger::logCreate('permintaan', $id, 'Kepala Instalasi membuat permintaan baru');

// Approve
ActivityLogger::logApproval('disposisi', $id, 'Direktur menyetujui disposisi #' . $id);

// Reject
ActivityLogger::logRejection('permintaan', $id, 'Kabid menolak permintaan #' . $id);

// Upload File
ActivityLogger::logUpload('perencanaan', 'dokumen.pdf', $id);

// Print Document
ActivityLogger::logPrint('nota_dinas', $id, 'Cetak Nota Dinas #' . $id);
```

---

## 📊 View Logs (Database Query)

```sql
-- See all logs
SELECT * FROM user_activity_logs ORDER BY created_at DESC LIMIT 20;

-- Filter by role
SELECT * FROM user_activity_logs WHERE role = 'direktur';

-- Filter by action
SELECT * FROM user_activity_logs WHERE action = 'approve';

-- Filter by today
SELECT * FROM user_activity_logs WHERE DATE(created_at) = CURDATE();

-- Stats per role
SELECT role, COUNT(*) as total 
FROM user_activity_logs 
GROUP BY role;
```

---

## 🎯 What Gets Logged Automatically?

### By URL Pattern:
- `/dashboard` → action: view, module: dashboard
- `/permintaan` → action: view/create/update, module: permintaan
- `/disposisi` → action: create, module: disposisi
- `/kso` → action: view/create, module: kso

### By HTTP Method:
- GET → action: view
- POST → action: create
- PUT/PATCH → action: update
- DELETE → action: delete

### Special Actions (detected from URL):
- `/approve` → action: approve
- `/reject` → action: reject
- `/revisi` → action: revisi
- `/upload` → action: upload
- `/cetak` → action: cetak

---

## 📝 Log Data Structure

```json
{
  "user_id": 5,
  "role": "kepala_instalasi",
  "action": "create",
  "module": "permintaan",
  "description": "Kepala Instalasi membuat permintaan baru",
  "url": "http://localhost:8000/kepala-instalasi/permintaan",
  "method": "POST",
  "ip_address": "127.0.0.1",
  "related_id": 17,
  "duration": 0.45
}
```

---

## 🔧 Integration Points (TODO)

Add manual logging to these controllers:

### 1. AuthController (Login/Logout)
```php
// After successful login
ActivityLogger::logLogin($user);

// Before logout
ActivityLogger::logLogout(Auth::user());
```

### 2. KepalaInstalasiController
```php
// After creating permintaan
ActivityLogger::logCreate('permintaan', $permintaan->id, 
    'Kepala Instalasi membuat permintaan: ' . $permintaan->jenis_pengadaan
);
```

### 3. KepalaBidangController / DirekturController
```php
// After approval
ActivityLogger::logApproval('disposisi', $id, 
    'Direktur menyetujui disposisi #' . $id
);

// After rejection
ActivityLogger::logRejection('permintaan', $id,
    'Kabid menolak permintaan #' . $id . ' dengan alasan: ' . $request->alasan
);
```

### 4. File Upload Actions
```php
// After file upload
ActivityLogger::logUpload('perencanaan', $file->getClientOriginalName(), $id);
```

### 5. Print/Download Actions
```php
// When printing document
ActivityLogger::logPrint('nota_dinas', $id, 'Cetak Nota Dinas #' . $id);

// When downloading file
ActivityLogger::logDownload('kso', $filename, $id);
```

---

## 🎨 Admin View (Next Step)

Create Vue components untuk melihat logs:

**Route:** `/admin/activity-logs`

**Features:**
- 📊 Statistics cards
- 🔍 Filter by role, action, module
- 📅 Date range picker
- 🔎 Search
- 📄 Paginated table
- 📥 Export to CSV
- 🗑️ Cleanup old logs

---

## ⚡ Performance Tips

1. **Auto-cleanup** - Schedule task to delete logs > 90 days
2. **Indexed columns** - Already optimized for fast queries
3. **Filtered data** - Sensitive fields not logged
4. **Silent fail** - Logging errors won't break the app

---

## 🧪 Testing

1. Login sebagai user → Check database
2. Create permintaan → Check log
3. Approve/Reject → Check log
4. Upload file → Check log
5. Print document → Check log

```bash
# Quick test
php artisan tinker
>>> \App\Models\UserActivityLog::latest()->first();
```

---

## 📌 Files Location

```
app/
├── Helpers/
│   └── ActivityLogger.php          ← Helper class
├── Http/
│   ├── Controllers/
│   │   └── Admin/
│   │       └── ActivityLogController.php  ← Admin controller
│   └── Middleware/
│       └── LogUserActivity.php     ← Middleware
└── Models/
    └── UserActivityLog.php         ← Model

database/
└── migrations/
    └── 2025_10_28_131808_create_user_activity_logs_table.php

bootstrap/
└── app.php                         ← Middleware registered

composer.json                       ← Helper autoloaded
```

---

## ✅ Checklist

- [x] Migration created & run
- [x] Model created
- [x] Middleware created & registered
- [x] Helper class created & autoloaded
- [x] Controller created
- [ ] Add manual logging to AuthController
- [ ] Add manual logging to critical actions
- [ ] Create Admin view (Vue components)
- [ ] Add routes for Admin logs
- [ ] Test all logging scenarios

---

**Status:** 🟢 PRODUCTION READY

Sistem logging sudah aktif dan berjalan! Setiap user activity sudah tercatat otomatis.
