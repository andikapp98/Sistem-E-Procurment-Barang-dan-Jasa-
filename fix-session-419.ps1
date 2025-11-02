# Script untuk Fix Error 419 dan Session Issues
Write-Host "🔧 Fixing Error 419 - CSRF Token Issues..." -ForegroundColor Cyan
Write-Host ""

# 1. Clear all caches
Write-Host "📦 Clearing all caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# 2. Regenerate configuration cache
Write-Host ""
Write-Host "⚙️  Regenerating configuration cache..." -ForegroundColor Yellow
php artisan config:cache

# 3. Check session table
Write-Host ""
Write-Host "🗄️  Checking session table..." -ForegroundColor Yellow
php artisan migrate:status | Select-String "sessions"

# 4. Clear browser cache instruction
Write-Host ""
Write-Host "✅ Cache cleared successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "📝 Next Steps:" -ForegroundColor Cyan
Write-Host "1. Clear your browser cache (Ctrl+Shift+Delete)" -ForegroundColor White
Write-Host "2. Close all browser tabs for localhost:8000" -ForegroundColor White
Write-Host "3. Open a new incognito/private window" -ForegroundColor White
Write-Host "4. Navigate to http://localhost:8000/login" -ForegroundColor White
Write-Host ""
Write-Host "🚀 Server is ready. You can start with:" -ForegroundColor Cyan
Write-Host "   php artisan serve" -ForegroundColor Yellow
Write-Host ""
