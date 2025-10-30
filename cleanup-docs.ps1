# Script untuk Cleanup dan Reorganisasi Dokumentasi
# Jalankan dengan: .\cleanup-docs.ps1

Write-Host "`n╔════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║     CLEANUP & REORGANISASI DOKUMENTASI MD             ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════╝`n" -ForegroundColor Cyan

Write-Host "⚠️  PERINGATAN:" -ForegroundColor Yellow
Write-Host "   Script ini akan memindahkan dan menghapus file MD" -ForegroundColor Gray
Write-Host "   Pastikan Anda sudah backup jika diperlukan!`n" -ForegroundColor Gray

$confirm = Read-Host "Lanjutkan? (y/n)"
if ($confirm -ne "y" -and $confirm -ne "Y") {
    Write-Host "`n❌ Dibatalkan`n" -ForegroundColor Red
    exit
}

Write-Host "`n📦 Membuat struktur folder..." -ForegroundColor Yellow
New-Item -ItemType Directory -Force -Path "docs", "docs\features", "docs\fixes", "docs\guides", "docs\technical", "archive" | Out-Null
Write-Host "   ✅ Folder dibuat`n" -ForegroundColor Green

# ============================================================
# PINDAHKAN FILE PENTING KE FOLDER YANG SESUAI
# ============================================================

Write-Host "📂 Memindahkan file ke folder yang sesuai..." -ForegroundColor Yellow

# FIXES - File fix terbaru dan penting
$fixFiles = @(
    "CSRF_FIX_SUMMARY.md",
    "QUICK_FIX_419_LOGOUT.md", 
    "ROUTING_FIX_DIREKTUR_TO_KABID.md"
)

foreach ($file in $fixFiles) {
    if (Test-Path $file) {
        Copy-Item $file "docs\fixes\" -Force
        Write-Host "   ✅ Copied: $file → docs\fixes\" -ForegroundColor Green
    }
}

# FEATURES - Dokumentasi fitur
$featureFiles = @(
    "WORKFLOW_PERENCANAAN_PENGADAAN_KSO.md",
    "WORKFLOW_COMPLETE_KABID_DIREKTUR_STAFF.md",
    "USER_ACTIVITY_LOGGING_SYSTEM.md",
    "KLASIFIKASI_PERMINTAAN.md"
)

foreach ($file in $featureFiles) {
    if (Test-Path $file) {
        Copy-Item $file "docs\features\" -Force
        Write-Host "   ✅ Copied: $file → docs\features\" -ForegroundColor Green
    }
}

# GUIDES - Panduan penggunaan
$guideFiles = @(
    "QUICK_START_DEV_SERVER.md",
    "LOGIN_TESTING_GUIDE.md",
    "QUICK_GUIDE_LOGGING.md"
)

foreach ($file in $guideFiles) {
    if (Test-Path $file) {
        Copy-Item $file "docs\guides\" -Force
        Write-Host "   ✅ Copied: $file → docs\guides\" -ForegroundColor Green
    }
}

# Copy ke docs root
Copy-Item "README.md" "docs\" -Force
Copy-Item "CHANGELOG.md" "docs\" -Force
Copy-Item "DOCS_CLEANUP_SUMMARY.md" "docs\" -Force

Write-Host "`n✅ File penting sudah dipindahkan`n" -ForegroundColor Green

# ============================================================
# ARSIPKAN FILE LAMA
# ============================================================

Write-Host "📦 Mengarsipkan file duplikat dan obsolete..." -ForegroundColor Yellow

$archivePatterns = @(
    "FIX_419_*.md",
    "FIX_LOGOUT_*.md",
    "COMPREHENSIVE_FIX_*.md",
    "QUICK_SUMMARY_*.md",
    "FINAL_SUMMARY_*.md",
    "QUICK_FIX_*.md",
    "FIX_*_COMPLETE.md",
    "TESTING_*.md",
    "WORKFLOW_DIREKTUR_*.md",
    "DIREKTUR_*.md",
    "KABID_*.md",
    "KEPALA_*.md",
    "STAFF_*.md",
    "ADMIN_*.md",
    "FIX_*.md",
    "PERBAIKAN_*.md",
    "SUMMARY_*.md",
    "INSTRUKSI_*.md",
    "LIHAT_*.md",
    "REMOVE_*.md",
    "FIX_*.md",
    "NOTA_DINAS_*.md",
    "KSO_*.md",
    "LOGGING_*.md",
    "DPP_*.md",
    "HPS_*.md",
    "PERMINTAAN_*.md",
    "FEATURE_*.md",
    "TIMELINE_*.md",
    "SIDEBAR_*.md",
    "PAGINATION_*.md",
    "VALIDASI_*.md",
    "LOGIN_*.md",
    "BRANDING_*.md",
    "UPDATE_*.md",
    "IMPLEMENTASI_*.md",
    "FITUR_*.md",
    "VIEW_*.md",
    "CLEAN_*.md",
    "PERUBAHAN_*.md",
    "SEEDER_*.md",
    "TRACKING_*.md",
    "WORKFLOW_*.md",
    "NPM_*.md",
    "TEMPLATE_*.html"
)

$archivedCount = 0
foreach ($pattern in $archivePatterns) {
    $files = Get-ChildItem -Path "." -Filter $pattern
    foreach ($file in $files) {
        # Jangan arsipkan file yang sudah dicopy ke docs
        $skipFiles = $fixFiles + $featureFiles + $guideFiles
        if ($skipFiles -notcontains $file.Name) {
            Move-Item $file.FullName "archive\" -Force
            $archivedCount++
        }
    }
}

Write-Host "   ✅ Diarsipkan: $archivedCount files → archive\`n" -ForegroundColor Green

# ============================================================
# BUAT INDEX FILE
# ============================================================

Write-Host "📝 Membuat index file..." -ForegroundColor Yellow

$indexContent = @"
# Dokumentasi Index

## 📁 Struktur Folder

### /docs
- README.md - Panduan utama aplikasi
- CHANGELOG.md - Riwayat perubahan
- DOCS_CLEANUP_SUMMARY.md - Ringkasan cleanup

### /docs/fixes
- CSRF_FIX_SUMMARY.md - Fix CSRF & 419 errors
- QUICK_FIX_419_LOGOUT.md - Fix logout infinite loop
- ROUTING_FIX_DIREKTUR_TO_KABID.md - Fix routing Direktur

### /docs/features
- WORKFLOW_PERENCANAAN_PENGADAAN_KSO.md - Workflow lengkap
- WORKFLOW_COMPLETE_KABID_DIREKTUR_STAFF.md - Workflow approval
- USER_ACTIVITY_LOGGING_SYSTEM.md - Logging system
- KLASIFIKASI_PERMINTAAN.md - Klasifikasi system

### /docs/guides
- QUICK_START_DEV_SERVER.md - Cara run development
- LOGIN_TESTING_GUIDE.md - Testing login
- QUICK_GUIDE_LOGGING.md - Logging guide

### /archive
File-file lama yang sudah tidak relevan (backup)

## 🎯 Quick Links

**Start Here:**
1. docs/README.md - Baca ini dulu!
2. docs/CHANGELOG.md - Update terbaru
3. docs/guides/QUICK_START_DEV_SERVER.md - Cara run app

**Latest Fixes:**
1. docs/fixes/CSRF_FIX_SUMMARY.md
2. docs/fixes/QUICK_FIX_419_LOGOUT.md
3. docs/fixes/ROUTING_FIX_DIREKTUR_TO_KABID.md

**Workflows:**
1. docs/features/WORKFLOW_COMPLETE_KABID_DIREKTUR_STAFF.md
2. docs/features/WORKFLOW_PERENCANAAN_PENGADAAN_KSO.md

---
Last Updated: $(Get-Date -Format "dd MMMM yyyy")
"@

$indexContent | Out-File "docs\INDEX.md" -Encoding UTF8
Write-Host "   ✅ Created: docs\INDEX.md`n" -ForegroundColor Green

# ============================================================
# SUMMARY
# ============================================================

Write-Host "`n╔════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║                  CLEANUP SELESAI!                      ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════╝`n" -ForegroundColor Green

Write-Host "📊 RINGKASAN:" -ForegroundColor Yellow
Write-Host "   ✅ Folder dibuat: docs/ (dengan subfolders)" -ForegroundColor Green
Write-Host "   ✅ File penting dipindahkan ke docs/" -ForegroundColor Green
Write-Host "   ✅ File duplikat diarsipkan: $archivedCount files" -ForegroundColor Green
Write-Host "   ✅ Index file dibuat: docs/INDEX.md`n" -ForegroundColor Green

Write-Host "📁 STRUKTUR BARU:" -ForegroundColor Yellow
Write-Host "   docs/" -ForegroundColor White
Write-Host "   ├── README.md" -ForegroundColor Gray
Write-Host "   ├── CHANGELOG.md" -ForegroundColor Gray
Write-Host "   ├── INDEX.md" -ForegroundColor Gray
Write-Host "   ├── fixes/" -ForegroundColor White
Write-Host "   │   ├── CSRF_FIX_SUMMARY.md" -ForegroundColor Gray
Write-Host "   │   ├── QUICK_FIX_419_LOGOUT.md" -ForegroundColor Gray
Write-Host "   │   └── ROUTING_FIX_DIREKTUR_TO_KABID.md" -ForegroundColor Gray
Write-Host "   ├── features/" -ForegroundColor White
Write-Host "   │   └── (4 files)" -ForegroundColor Gray
Write-Host "   ├── guides/" -ForegroundColor White
Write-Host "   │   └── (3 files)" -ForegroundColor Gray
Write-Host "   └── technical/" -ForegroundColor White
Write-Host "       └── (kosong - untuk future use)`n" -ForegroundColor Gray

Write-Host "📖 MULAI DARI SINI:" -ForegroundColor Yellow
Write-Host "   1. docs/README.md" -ForegroundColor Cyan
Write-Host "   2. docs/CHANGELOG.md" -ForegroundColor Cyan
Write-Host "   3. docs/INDEX.md`n" -ForegroundColor Cyan

Write-Host "🗑️  ARSIP:" -ForegroundColor Yellow
Write-Host "   File lama ada di folder: archive/" -ForegroundColor Gray
Write-Host "   Bisa dihapus jika sudah yakin tidak diperlukan`n" -ForegroundColor Gray

Write-Host "✅ DONE! Dokumentasi lebih rapi sekarang!`n" -ForegroundColor Green
