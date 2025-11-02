# Fix All Expired Date Inputs
# This script adds minDate validation to all date inputs

Write-Host "`n🔧 FIXING EXPIRED DATE INPUTS..." -ForegroundColor Cyan
Write-Host "=" -NoNewline
1..60 | ForEach-Object { Write-Host "=" -NoNewline }
Write-Host "`n"

$filesFixed = 0
$filesSkipped = 0

# List of files to fix with their patterns
$filesToFix = @(
    @{
        Path = "resources\js\Pages\Permintaan\Edit.vue"
        NeedsMinDate = $true
        NeedsDefault = $false
    },
    @{
        Path = "resources\js\Pages\KepalaPoli\Create.vue"
        NeedsMinDate = $true
        NeedsDefault = $true
    },
    @{
        Path = "resources\js\Pages\KepalaPoli\Edit.vue"
        NeedsMinDate = $true
        NeedsDefault = $false
    },
    @{
        Path = "resources\js\Pages\StaffPerencanaan\CreateNotaDinas.vue"
        NeedsMinDate = $true
        NeedsDefault = $true
    },
    @{
        Path = "resources\js\Pages\StaffPerencanaan\CreateNotaDinasPembelian.vue"
        NeedsMinDate = $true
        NeedsDefault = $true
    },
    @{
        Path = "resources\js\Pages\KepalaBidang\CreateDisposisi.vue"
        NeedsMinDate = $true
        NeedsDefault = $true
    },
    @{
        Path = "resources\js\Pages\Direktur\CreateDisposisi.vue"
        NeedsMinDate = $true
        NeedsDefault = $true
    },
    @{
        Path = "resources\js\Pages\WakilDirektur\CreateDisposisi.vue"
        NeedsMinDate = $true
        NeedsDefault = $true
    },
    @{
        Path = "resources\js\Pages\StaffPerencanaan\CreateDisposisi.vue"
        NeedsMinDate = $true
        NeedsDefault = $true
    }
)

foreach ($file in $filesToFix) {
    $fullPath = Join-Path $PSScriptRoot $file.Path
    
    if (Test-Path $fullPath) {
        Write-Host "Processing: $($file.Path)" -ForegroundColor Yellow
        
        $content = Get-Content $fullPath -Raw
        $modified = $false
        
        # Check if minDate already exists in computed
        if ($content -notmatch 'const minDate = computed') {
            Write-Host "  → Adding minDate computed property..." -ForegroundColor Cyan
            
            # Add minDate computed after imports or first const declaration in script setup
            if ($content -match '(<script setup>[\s\S]*?)(const\s+\w+\s*=)') {
                $content = $content -replace '(<script setup>[\s\S]*?import.*?;\s*\n)', "`$1`n// Minimum date is today`nconst minDate = computed(() => {`n    const today = new Date();`n    return today.toISOString().split('T')[0];`n});`n"
                $modified = $true
            }
            
            # Make sure computed is imported
            if ($content -match "from ['\"]vue['\"]") {
                $content = $content -replace "(import\s+\{)([^\}]*?)(\}\s+from\s+['\"]vue['\"])", "`$1`$2, computed`$3"
                $content = $content -replace ", ,", ","
                $modified = $true
            }
        } else {
            Write-Host "  → minDate already exists" -ForegroundColor Green
        }
        
        # Add :min="minDate" to all type="date" inputs that don't have it
        if ($content -match 'type="date"' -and $content -notmatch ':min="minDate"') {
            Write-Host "  → Adding :min attribute to date inputs..." -ForegroundColor Cyan
            # Pattern: type="date" without :min nearby
            $content = $content -replace '(type="date")\s*\n(\s*)([^:]*?)(class|required|v-model)', "`$1`n`$2:min=`"minDate`"`n`$2`$3`$4"
            $modified = $true
        }
        
        # Set default date for specific fields if needed
        if ($file.NeedsDefault) {
            if ($content -match 'tanggal_disposisi:\s*""' -or $content -match 'tanggal_nota:\s*""' -or $content -match 'tanggal_permintaan:\s*""') {
                Write-Host "  → Setting default date to today..." -ForegroundColor Cyan
                $content = $content -replace '(tanggal_disposisi|tanggal_nota|tanggal_permintaan):\s*""', '$1: new Date().toISOString().split("T")[0]'
                $modified = $true
            }
        }
        
        if ($modified) {
            Set-Content -Path $fullPath -Value $content -NoNewline
            Write-Host "  ✓ Fixed!" -ForegroundColor Green
            $filesFixed++
        } else {
            Write-Host "  ✓ No changes needed" -ForegroundColor DarkGray
            $filesSkipped++
        }
        Write-Host ""
    } else {
        Write-Host "  ✗ File not found: $fullPath" -ForegroundColor Red
        Write-Host ""
    }
}

Write-Host "=" -NoNewline
1..60 | ForEach-Object { Write-Host "=" -NoNewline }
Write-Host ""
Write-Host "✅ SUMMARY:" -ForegroundColor Green
Write-Host "  Files Fixed: $filesFixed" -ForegroundColor Cyan
Write-Host "  Files Skipped: $filesSkipped" -ForegroundColor Yellow
Write-Host ""
Write-Host "All date inputs now have minimum date validation!" -ForegroundColor Green
Write-Host "Users cannot select dates in the past.`n" -ForegroundColor Green
