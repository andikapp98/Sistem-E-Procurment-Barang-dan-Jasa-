# QUICK FIX - Login Bug
# Jalankan script ini untuk memperbaiki masalah login

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "     LOGIN BUG - QUICK FIX SCRIPT      " -ForegroundColor Cyan  
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Update SESSION_DRIVER di .env
Write-Host "[1/3] Updating session driver..." -ForegroundColor Yellow
(Get-Content .env) -replace 'SESSION_DRIVER=database', 'SESSION_DRIVER=file' | Set-Content .env
Write-Host "      âœ… Changed to SESSION_DRIVER=file" -ForegroundColor Green
Write-Host ""

# 2. Clear session files
Write-Host "[2/3] Clearing session files..." -ForegroundColor Yellow
if (Test-Path "storage\framework\sessions") {
    Remove-Item "storage\framework\sessions\*" -Force -ErrorAction SilentlyContinue
    Write-Host "      âœ… Session files cleared" -ForegroundColor Green
} else {
    New-Item -ItemType Directory -Path "storage\framework\sessions" -Force | Out-Null
    Write-Host "      âœ… Session directory created" -ForegroundColor Green
}
Write-Host ""

# 3. Clear config cache only (skip others that need DB)
Write-Host "[3/3] Clearing config cache..." -ForegroundColor Yellow
if (Test-Path "bootstrap\cache\config.php") {
    Remove-Item "bootstrap\cache\config.php" -Force
    Write-Host "      âœ… Config cache cleared" -ForegroundColor Green
} else {
    Write-Host "      âœ… No config cache to clear" -ForegroundColor Green
}
Write-Host ""

# Summary
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "             FIX COMPLETE               " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Changes applied:" -ForegroundColor White
Write-Host "  âœ… Session driver changed to 'file'" -ForegroundColor Green
Write-Host "  âœ… Old session files removed" -ForegroundColor Green
Write-Host "  âœ… Config cache cleared" -ForegroundColor Green
Write-Host ""
Write-Host "IMPORTANT - Before testing:" -ForegroundColor Yellow
Write-Host "  1. Open XAMPP Control Panel" -ForegroundColor White
Write-Host "  2. Make sure MySQL is RUNNING (green)" -ForegroundColor White
Write-Host "  3. If MySQL stopped, click START" -ForegroundColor White
Write-Host ""
Write-Host "Then test login:" -ForegroundColor Yellow
Write-Host "  php artisan serve" -ForegroundColor Cyan
Write-Host "  Open: http://127.0.0.1:8000/login" -ForegroundColor Cyan
Write-Host ""
Write-Host "If still having issues, see:" -ForegroundColor Gray
Write-Host "  FIX_LOGIN_BUG_COMPLETE.md" -ForegroundColor Gray
Write-Host ""
