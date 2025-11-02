# Fix Login Issues Script
# This script fixes common login problems

Write-Host "`n🔧 FIXING LOGIN ISSUES..." -ForegroundColor Cyan
Write-Host "=" -NoNewline
1..60 | ForEach-Object { Write-Host "=" -NoNewline }
Write-Host "`n"

# 1. Clear all caches
Write-Host "1. Clearing caches..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
Write-Host "   ✓ Caches cleared" -ForegroundColor Green

# 2. Check session table exists
Write-Host "`n2. Checking session table..." -ForegroundColor Yellow
php artisan migrate:status

# 3. Create session table if not exists
Write-Host "`n3. Running migrations..." -ForegroundColor Yellow
php artisan migrate --force

# 4. Regenerate session table (optional - uncomment if needed)
# Write-Host "`n4. Regenerating session table..." -ForegroundColor Yellow
# php artisan session:table
# php artisan migrate:fresh --path=database/migrations/*_create_sessions_table.php

# 5. Check if .env file has correct session config
Write-Host "`n5. Checking .env configuration..." -ForegroundColor Yellow
$envPath = ".env"
if (Test-Path $envPath) {
    $envContent = Get-Content $envPath
    $sessionDriver = $envContent | Select-String "SESSION_DRIVER"
    Write-Host "   Current: $sessionDriver" -ForegroundColor White
    
    if ($sessionDriver -notmatch "SESSION_DRIVER=database") {
        Write-Host "   ⚠️  Warning: SESSION_DRIVER should be 'database'" -ForegroundColor Yellow
    } else {
        Write-Host "   ✓ SESSION_DRIVER correct" -ForegroundColor Green
    }
} else {
    Write-Host "   ✗ .env file not found!" -ForegroundColor Red
}

# 6. Test database connection
Write-Host "`n6. Testing database connection..." -ForegroundColor Yellow
php artisan db:show

# 7. Clear compiled files
Write-Host "`n7. Clearing compiled files..." -ForegroundColor Yellow
if (Test-Path "bootstrap/cache/config.php") {
    Remove-Item "bootstrap/cache/config.php" -Force
    Write-Host "   ✓ config.php removed" -ForegroundColor Green
}
if (Test-Path "bootstrap/cache/routes-v7.php") {
    Remove-Item "bootstrap/cache/routes-v7.php" -Force
    Write-Host "   ✓ routes cache removed" -ForegroundColor Green
}

# 8. Rebuild assets
Write-Host "`n8. Checking if assets need rebuild..." -ForegroundColor Yellow
$answer = Read-Host "Do you want to rebuild assets now? (y/n)"
if ($answer -eq 'y') {
    Write-Host "   Building assets..." -ForegroundColor Cyan
    npm run build
    Write-Host "   ✓ Assets rebuilt" -ForegroundColor Green
} else {
    Write-Host "   Skipped asset rebuild" -ForegroundColor Gray
    Write-Host "   Note: Run 'npm run build' or 'npm run dev' manually" -ForegroundColor Yellow
}

Write-Host "`n=" -NoNewline
1..60 | ForEach-Object { Write-Host "=" -NoNewline }
Write-Host ""
Write-Host "✅ LOGIN FIX COMPLETED!" -ForegroundColor Green
Write-Host ""
Write-Host "NEXT STEPS:" -ForegroundColor Cyan
Write-Host "  1. Clear browser cache and cookies" -ForegroundColor White
Write-Host "  2. Try logging in with a valid user" -ForegroundColor White
Write-Host "  3. Check storage/logs/laravel.log if still fails" -ForegroundColor White
Write-Host "  4. Ensure database is running" -ForegroundColor White
Write-Host ""
Write-Host "DEFAULT TEST CREDENTIALS:" -ForegroundColor Yellow
Write-Host "  Email: admin@example.com" -ForegroundColor White
Write-Host "  Password: password" -ForegroundColor White
Write-Host ""
Write-Host "If login still fails, check:" -ForegroundColor Yellow
Write-Host "  • Database connection" -ForegroundColor White
Write-Host "  • Users table has data" -ForegroundColor White
Write-Host "  • Session table exists" -ForegroundColor White
Write-Host "  • Browser console for errors" -ForegroundColor White
Write-Host "`n"
