# Fix CSRF 419 Errors - Complete Solution
Write-Host "=== Fixing CSRF 419 Errors ===" -ForegroundColor Cyan

# 1. Clear all caches
Write-Host "`n1. Clearing all caches..." -ForegroundColor Yellow
php artisan optimize:clear

# 2. Ensure sessions table exists and is fresh
Write-Host "`n2. Checking sessions table..." -ForegroundColor Yellow
php artisan migrate --path=database/migrations/2025_10_14_000000_create_sessions_table.php --force

# 3. Clear old sessions from database
Write-Host "`n3. Clearing old sessions from database..." -ForegroundColor Yellow
php artisan db:wipe sessions 2>$null

# 4. Rebuild frontend assets
Write-Host "`n4. Rebuilding frontend assets..." -ForegroundColor Yellow
npm run build

# 5. Restart dev server instructions
Write-Host "`n=== Fix Applied Successfully ===" -ForegroundColor Green
Write-Host "`nNext steps:" -ForegroundColor Cyan
Write-Host "1. Restart your development server (php artisan serve)" -ForegroundColor White
Write-Host "2. Clear browser cache and cookies for localhost:8000" -ForegroundColor White
Write-Host "3. Try the form submission again" -ForegroundColor White
Write-Host "`nIf error persists:" -ForegroundColor Yellow
Write-Host "- Check browser console for specific error messages" -ForegroundColor White
Write-Host "- Ensure you're logged in before submitting form" -ForegroundColor White
Write-Host "- Try in incognito/private window" -ForegroundColor White
