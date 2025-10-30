# Quick Fix Script untuk 419 Logout Error
# Mengatasi infinite loop CSRF error

Write-Host "`n╔════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║     QUICK FIX: 419 LOGOUT ERROR (Infinite Loop)       ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════╝`n" -ForegroundColor Cyan

Write-Host "🔧 Langkah 1: Clear Laravel Cache..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

Write-Host "`n🗑️  Langkah 2: Clear Sessions..." -ForegroundColor Yellow
php artisan session:flush

Write-Host "`n📦 Langkah 3: Rebuild JavaScript Assets..." -ForegroundColor Yellow
Write-Host "   Pilih metode rebuild:`n" -ForegroundColor Gray

Write-Host "   [1] npm run build (Production - Recommended)" -ForegroundColor Green
Write-Host "   [2] npm run dev (Development)" -ForegroundColor Yellow
Write-Host "   [3] Skip rebuild`n" -ForegroundColor Red

$choice = Read-Host "   Pilih (1/2/3)"

switch ($choice) {
    "1" {
        Write-Host "`n   Building for production..." -ForegroundColor Green
        npm run build
    }
    "2" {
        Write-Host "`n   Building for development..." -ForegroundColor Yellow
        npm run dev
    }
    "3" {
        Write-Host "`n   Skipped rebuild." -ForegroundColor Red
    }
    default {
        Write-Host "`n   Invalid choice. Skipping rebuild." -ForegroundColor Red
    }
}

Write-Host "`n✅ SELESAI! Cache cleared dan assets rebuilt.`n" -ForegroundColor Green

Write-Host "📋 LANGKAH SELANJUTNYA:" -ForegroundColor Yellow
Write-Host "   1. ✅ Close ALL browser tabs" -ForegroundColor White
Write-Host "   2. ✅ Clear browser cache (Ctrl+Shift+Delete)" -ForegroundColor White
Write-Host "   3. ✅ Open browser fresh" -ForegroundColor White
Write-Host "   4. ✅ Login with ONE role only" -ForegroundColor White
Write-Host "   5. ✅ Test logout`n" -ForegroundColor White

Write-Host "⚠️  PENTING:" -ForegroundColor Red
Write-Host "   • Jangan login multi-role di browser yang sama" -ForegroundColor Gray
Write-Host "   • Gunakan Incognito atau browser berbeda untuk testing" -ForegroundColor Gray
Write-Host "   • Always logout sebelum switch role`n" -ForegroundColor Gray

Write-Host "🧪 Test dengan:" -ForegroundColor Cyan
Write-Host "   http://localhost:8000`n" -ForegroundColor White

$restart = Read-Host "Restart development server? (y/n)"

if ($restart -eq "y" -or $restart -eq "Y") {
    Write-Host "`n🚀 Starting development server..." -ForegroundColor Green
    Write-Host "   Press Ctrl+C to stop`n" -ForegroundColor Gray
    php artisan serve
} else {
    Write-Host "`n✅ Done! Server belum direstart." -ForegroundColor Yellow
    Write-Host "   Jalankan manual: php artisan serve`n" -ForegroundColor Gray
}
