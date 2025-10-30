# User Activity Logging System - Complete Documentation

## Overview
Sistem logging otomatis untuk mencatat semua aktivitas user berdasarkan role mereka di aplikasi pengadaan RSUD.

## ✅ Features

### 1. **Automatic Logging**
- Setiap request HTTP tercatat otomatis
- Login/Logout tracking
- CRUD operations
- Approval/Reject/Revisi actions
- File uploads/downloads
- Print/Cetak documents

### 2. **Detailed Information**
Setiap log mencatat:
- User ID & Role
- Action (login, create, update, delete, approve, reject, dll)
- Module (permintaan, disposisi, kso, dll)
- Description (human-readable)
- URL & HTTP Method
- IP Address & User Agent
- Request Data (filtered)
- Status Code
- Related Model ID & Type
- Duration (execution time)
- Timestamp

### 3. **Filter & Search**
- Filter by Role
- Filter by Action
- Filter by Module
- Filter by Date Range
- Search by keyword

### 4. **Statistics Dashboard**
- Total logs today
- Total logs this week
- Total logs this month
- Total all logs

### 5. **Export & Cleanup**
- Export to CSV
- Automatic cleanup old logs

## 📁 Files Created

### 1. Migration
**File:** `database/migrations/2025_10_28_131808_create_user_activity_logs_table.php`

**Table Structure:**
```sql
CREATE TABLE user_activity_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NULLABLE,
    role VARCHAR(50) INDEXED,
    action VARCHAR(100) INDEXED,
    module VARCHAR(50) INDEXED,
    description VARCHAR(255),
    url VARCHAR(255),
    method VARCHAR(10),
    ip_address VARCHAR(45),
    user_agent TEXT,
    request_data JSON,
    response_data JSON,
    status_code INT,
    related_id BIGINT,
    related_type VARCHAR(255),
    duration DECIMAL(8,2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX (user_id, created_at),
    INDEX (role, action),
    INDEX (module, created_at),
    INDEX (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

### 2. Model
**File:** `app/Models/UserActivityLog.php`

**Features:**
- Fillable fields
- JSON casting for request/response data
- Relationship with User model
- Scopes: byRole, byAction, byModule, dateRange
- Helper method: `log()`

### 3. Middleware
**File:** `app/Http/Middleware/LogUserActivity.php`

**Functionality:**
- Automatic logging on every HTTP request
- Smart action detection (login, logout, approve, reject, etc.)
- Module detection from URL
- Filtered sensitive data (passwords, tokens)
- Skip logging for assets, static files
- Duration tracking
- Error handling (silent fail)

### 4. Helper Class
**File:** `app/Helpers/ActivityLogger.php`

**Methods:**
```php
// Authentication
ActivityLogger::logLogin($user)
ActivityLogger::logLogout($user)

// Generic logging
ActivityLogger::log($action, $module, $description, $relatedId, $relatedType)

// CRUD Operations
ActivityLogger::logCreate($module, $relatedId, $description)
ActivityLogger::logUpdate($module, $relatedId, $description)
ActivityLogger::logDelete($module, $relatedId, $description)
ActivityLogger::logView($module, $relatedId, $description)

// Workflow Actions
ActivityLogger::logApproval($module, $relatedId, $description)
ActivityLogger::logRejection($module, $relatedId, $description)
ActivityLogger::logRevision($module, $relatedId, $description)

// File Operations
ActivityLogger::logUpload($module, $filename, $relatedId)
ActivityLogger::logDownload($module, $filename, $relatedId)
ActivityLogger::logPrint($module, $relatedId, $description)
```

### 5. Controller
**File:** `app/Http/Controllers/Admin/ActivityLogController.php`

**Methods:**
- `index()` - Display paginated logs with filters
- `show($id)` - Show single log detail
- `export()` - Export logs to CSV
- `cleanup()` - Delete old logs

### 6. Middleware Registration
**File:** `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \App\Http\Middleware\LogUserActivity::class, // ← Added
    ]);
})
```

## 🔧 Configuration

### Autoload Helper
**File:** `composer.json`

```json
"autoload": {
    "files": [
        "app/Helpers/ActivityLogger.php"
    ]
}
```

Then run:
```bash
composer dump-autoload
```

## 📊 Usage Examples

### 1. Manual Logging in Controllers

```php
use App\Helpers\ActivityLogger;

// Login
public function login(Request $request)
{
    $user = Auth::user();
    ActivityLogger::logLogin($user);
    // ...
}

// Create Permintaan
public function store(Request $request)
{
    $permintaan = Permintaan::create($data);
    
    ActivityLogger::logCreate(
        'permintaan',
        $permintaan->permintaan_id,
        'Kepala Instalasi membuat permintaan baru: ' . $permintaan->jenis_pengadaan
    );
    
    return redirect()->route('permintaan.index');
}

// Approve Disposisi
public function approve($id)
{
    $disposisi = Disposisi::findOrFail($id);
    $disposisi->update(['status' => 'disetujui']);
    
    ActivityLogger::logApproval(
        'disposisi',
        $id,
        'Kepala Bidang menyetujui disposisi #' . $id
    );
    
    return back();
}

// Upload File
public function uploadDokumen(Request $request, $id)
{
    $file = $request->file('dokumen');
    $filename = $file->store('dokumen');
    
    ActivityLogger::logUpload(
        'perencanaan',
        $filename,
        $id
    );
    
    return back();
}
```

### 2. Automatic Logging (via Middleware)

Middleware akan otomatis log setiap request:

```
GET /kepala-instalasi/permintaan
→ Log: "Kepala Instalasi melihat halaman permintaan"

POST /kepala-bidang/disposisi
→ Log: "Kepala Bidang membuat data baru di disposisi"

PUT /direktur/permintaan/17/approve
→ Log: "Direktur menyetujui permintaan"
```

## 🎯 Tracked Actions

### Authentication
- `login` - User login
- `logout` - User logout

### CRUD Operations
- `view` - View/access page
- `create` - Create new data
- `update` - Update existing data
- `delete` - Delete data

### Workflow
- `approve` - Approve request
- `reject` - Reject request
- `revisi` - Request revision
- `disposisi` - Create disposition

### File Operations
- `upload` - Upload file
- `download` - Download file
- `cetak` - Print document
- `print` - Print document

## 📋 Tracked Modules

- `auth` - Authentication
- `dashboard` - Dashboard pages
- `permintaan` - Permintaan module
- `disposisi` - Disposisi module
- `nota_dinas` - Nota Dinas module
- `perencanaan` - Perencanaan module
- `kso` - KSO module
- `dpp` - DPP module
- `hps` - HPS module
- `spesifikasi_teknis` - Spesifikasi Teknis module
- `tracking` - Tracking module
- `profile` - User profile
- `general` - General/other

## 🎨 Admin View (To Be Created)

### Route (Add to routes/web.php):
```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs.index');
    Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show'])->name('admin.activity-logs.show');
    Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('admin.activity-logs.export');
    Route::post('/activity-logs/cleanup', [ActivityLogController::class, 'cleanup'])->name('admin.activity-logs.cleanup');
});
```

### View Features:
1. **Stats Cards** - Today, Week, Month, All time
2. **Filters** - Role, Action, Module, Date Range
3. **Search** - Description, URL, IP, User name/email
4. **Table** - All log data with pagination
5. **Actions** - View detail, Export CSV
6. **Cleanup** - Delete logs older than X days

## 📊 Sample Log Data

```json
{
    "id": 1,
    "user_id": 5,
    "role": "kepala_instalasi",
    "action": "create",
    "module": "permintaan",
    "description": "Kepala Instalasi membuat permintaan baru: Alat Kesehatan",
    "url": "http://localhost:8000/kepala-instalasi/permintaan",
    "method": "POST",
    "ip_address": "127.0.0.1",
    "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)...",
    "request_data": {
        "jenis_pengadaan": "Alat Kesehatan",
        "bidang": "Instalasi Gawat Darurat"
    },
    "status_code": 302,
    "related_id": 17,
    "related_type": "App\\Models\\Permintaan",
    "duration": 0.45,
    "created_at": "2025-10-28 13:30:15"
}
```

## 🔒 Security & Privacy

### Sensitive Data Filtering:
```php
// These fields are NEVER logged:
- password
- password_confirmation
- _token
- _method
```

### Access Control:
- Only **Admin** can view all logs
- Only **Admin** can export logs
- Only **Admin** can cleanup logs
- Other roles: 403 Forbidden

### Performance:
- Indexed columns for fast queries
- Silent fail on logging errors (won't break app)
- Optional: Limit request_data size (max 10KB)

## 📈 Query Examples

### Get logs by role:
```php
UserActivityLog::byRole('kepala_instalasi')->get();
```

### Get logs by action:
```php
UserActivityLog::byAction('approve')->get();
```

### Get logs by module:
```php
UserActivityLog::byModule('permintaan')->get();
```

### Get logs by date range:
```php
UserActivityLog::dateRange('2025-10-01', '2025-10-31')->get();
```

### Get today's logs:
```php
UserActivityLog::whereDate('created_at', today())->get();
```

### Get user's logs:
```php
$user->activityLogs()->latest()->get();
```

### Complex query:
```php
UserActivityLog::with('user')
    ->byRole('direktur')
    ->byAction('approve')
    ->byModule('permintaan')
    ->dateRange(now()->subDays(7), now())
    ->latest()
    ->paginate(25);
```

## 🧹 Maintenance

### Cleanup Old Logs (Manual):
```php
// Delete logs older than 90 days
UserActivityLog::where('created_at', '<', now()->subDays(90))->delete();
```

### Cleanup Old Logs (via Controller):
```bash
POST /admin/activity-logs/cleanup?days=90
```

### Schedule Automatic Cleanup (Optional):
Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Delete logs older than 90 days, run monthly
    $schedule->call(function () {
        UserActivityLog::where('created_at', '<', now()->subDays(90))->delete();
    })->monthly();
}
```

## ✅ Status

### Completed:
- ✅ Migration created & run
- ✅ Model created with relationships & scopes
- ✅ Middleware created & registered
- ✅ Helper class created
- ✅ Controller created (Admin)
- ✅ Autoloader configured
- ✅ Database indexed for performance

### Pending:
- ⏳ Vue components for Admin view
- ⏳ Routes for Admin logs
- ⏳ Integration with existing controllers (login/logout)
- ⏳ Manual logging in critical actions

## 🚀 Next Steps

1. **Add Routes** for Admin Activity Logs view
2. **Create Vue Components** for logs listing & detail
3. **Integrate with Auth Controllers** (login/logout)
4. **Add Manual Logging** in critical actions:
   - Approve/Reject disposisi
   - Create permintaan
   - Upload files
   - Print documents
5. **Test** logging for all roles
6. **Create Scheduled Task** for automatic cleanup

---

## 📌 Important Notes

1. **Automatic Logging** works for ALL authenticated requests
2. **Manual Logging** should be added for important business logic
3. **Middleware** is already active (registered in bootstrap/app.php)
4. **Helper** is auto-loaded via composer
5. **Database** migration is complete
6. **Performance** is optimized with indexes
7. **Privacy** is protected (sensitive data filtered)

**Migration Run:** ✅ Success (202ms)
**Autoloader:** ✅ Configured
**Middleware:** ✅ Registered
**Status:** ✅ READY TO USE

Test dengan login/logout dan lihat tabel `user_activity_logs`!
