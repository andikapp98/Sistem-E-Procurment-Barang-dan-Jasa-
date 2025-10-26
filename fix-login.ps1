# Quick Fix for Login Bug
# Run this script to fix login issues

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "  LOGIN BUG FIX - Quick Script   " -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Check if in correct directory
$currentDir = Get-Location
if (-not (Test-Path "artisan")) {
    Write-Host "❌ Error: Please run this script from the project root directory" -ForegroundColor Red
    Write-Host "   Current directory: $currentDir" -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ Directory: $currentDir" -ForegroundColor Green
Write-Host ""

# Step 2: Update .env file
Write-Host "Step 1: Updating .env file..." -ForegroundColor Yellow
$envContent = Get-Content .env
$envUpdated = $envContent -replace 'SESSION_DRIVER=database', 'SESSION_DRIVER=file'
$envUpdated | Set-Content .env
Write-Host "✅ Session driver changed to 'file'" -ForegroundColor Green
Write-Host ""

# Step 3: Clear session files
Write-Host "Step 2: Clearing old session files..." -ForegroundColor Yellow
$sessionPath = "storage\framework\sessions"
if (Test-Path $sessionPath) {
    $sessionFiles = Get-ChildItem -Path $sessionPath -File
    if ($sessionFiles.Count -gt 0) {
        Remove-Item "$sessionPath\*" -Force -ErrorAction SilentlyContinue
        Write-Host "✅ Cleared $($sessionFiles.Count) session file(s)" -ForegroundColor Green
    } else {
        Write-Host "✅ No session files to clear" -ForegroundColor Green
    }
} else {
    Write-Host "⚠️  Session directory not found, creating..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $sessionPath -Force | Out-Null
    Write-Host "✅ Session directory created" -ForegroundColor Green
}
Write-Host ""

# Step 4: Clear Laravel caches
Write-Host "Step 3: Clearing Laravel caches..." -ForegroundColor Yellow

Write-Host "  - Config cache..." -NoNewline
php artisan config:clear 2>&1 | Out-Null
if ($LASTEXITCODE -eq 0) {
    Write-Host " ✅" -ForegroundColor Green
} else {
    Write-Host " ⚠️" -ForegroundColor Yellow
}

Write-Host "  - Application cache..." -NoNewline
php artisan cache:clear 2>&1 | Out-Null
if ($LASTEXITCODE -eq 0) {
    Write-Host " ✅" -ForegroundColor Green
} else {
    Write-Host " ⚠️" -ForegroundColor Yellow
}

Write-Host "  - Route cache..." -NoNewline
php artisan route:clear 2>&1 | Out-Null
if ($LASTEXITCODE -eq 0) {
    Write-Host " ✅" -ForegroundColor Green
} else {
    Write-Host " ⚠️" -ForegroundColor Yellow
}

Write-Host "  - View cache..." -NoNewline
php artisan view:clear 2>&1 | Out-Null
if ($LASTEXITCODE -eq 0) {
    Write-Host " ✅" -ForegroundColor Green
} else {
    Write-Host " ⚠️" -ForegroundColor Yellow
}

Write-Host ""

# Step 5: Check MySQL
Write-Host "Step 4: Checking MySQL status..." -ForegroundColor Yellow
$mysqlProcess = Get-Process -Name mysqld -ErrorAction SilentlyContinue
if ($mysqlProcess) {
    Write-Host "✅ MySQL is running (PID: $($mysqlProcess.Id))" -ForegroundColor Green
    
    # Test port
    $portTest = Test-NetConnection -ComputerName 127.0.0.1 -Port 3306 -WarningAction SilentlyContinue -InformationLevel Quiet
    if ($portTest) {
        Write-Host "✅ MySQL is listening on port 3306" -ForegroundColor Green
    } else {
        Write-Host "⚠️  MySQL process running but not listening on port 3306" -ForegroundColor Yellow
        Write-Host "   Please restart MySQL from XAMPP Control Panel" -ForegroundColor Yellow
    }
} else {
    Write-Host "❌ MySQL is NOT running!" -ForegroundColor Red
    Write-Host "   Please start MySQL from XAMPP Control Panel" -ForegroundColor Yellow
}
Write-Host ""

# Step 6: Summary
Write-Host "==================================" -ForegroundColor Cyan
Write-Host "  FIX COMPLETED                  " -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Changes applied:" -ForegroundColor White
Write-Host "  ✅ Session driver: file (was: database)" -ForegroundColor Green
Write-Host "  ✅ Session files: cleared" -ForegroundColor Green
Write-Host "  ✅ Laravel caches: cleared" -ForegroundColor Green
Write-Host "  ✅ Database timeout: added (5 seconds)" -ForegroundColor Green
Write-Host ""

# Step 7: Next steps
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "  1. Make sure MySQL is running in XAMPP" -ForegroundColor White
Write-Host "  2. Open browser and go to login page" -ForegroundColor White
Write-Host "  3. Try to login" -ForegroundColor White
Write-Host ""

# Step 8: Test command suggestion
Write-Host "To test immediately, run:" -ForegroundColor Yellow
Write-Host "  php artisan serve" -ForegroundColor Cyan
Write-Host "  Then open: http://127.0.0.1:8000/login" -ForegroundColor Cyan
Write-Host ""

Write-Host "Documentation: See FIX_LOGIN_BUG_COMPLETE.md" -ForegroundColor Gray
Write-Host ""
