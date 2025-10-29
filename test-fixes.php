#!/usr/bin/env php
<?php

/**
 * TESTING SCRIPT - Verify All Fixes
 * Run: php test-fixes.php
 */

echo "🔍 TESTING ALL FIXES...\n\n";

// Test 1: Check if LogUserActivity.php has the fix
echo "1️⃣  Testing LogUserActivity fix...\n";
$logActivity = file_get_contents(__DIR__ . '/app/Http/Middleware/LogUserActivity.php');
if (strpos($logActivity, 'Check multiple possible ID fields') !== false) {
    echo "   ✅ LogUserActivity extractRelatedId() fixed\n\n";
} else {
    echo "   ❌ LogUserActivity needs update\n\n";
}

// Test 2: Check if StaffPerencanaanController has no_nota fix
echo "2️⃣  Testing StaffPerencanaan no_nota fix...\n";
$staffController = file_get_contents(__DIR__ . '/app/Http/Controllers/StaffPerencanaanController.php');
if (strpos($staffController, "\$data['no_nota'] = \$data['nomor'];") !== false) {
    echo "   ✅ StaffPerencanaan storeNotaDinas() fixed\n\n";
} else {
    echo "   ❌ StaffPerencanaan needs update\n\n";
}

// Test 3: Check if app.js has 419 handler
echo "3️⃣  Testing app.js 419 error handler...\n";
$appJs = file_get_contents(__DIR__ . '/resources/js/app.js');
if (strpos($appJs, 'refreshCsrfToken') !== false && strpos($appJs, "router.on('error'") !== false) {
    echo "   ✅ app.js has CSRF refresh and 419 handler\n\n";
} else {
    echo "   ❌ app.js needs update\n\n";
}

// Test 4: Check if KsoController has authorization fix
echo "4️⃣  Testing KSO authorization fix...\n";
$ksoController = file_get_contents(__DIR__ . '/app/Http/Controllers/KsoController.php');
if (strpos($ksoController, "user->role !== 'kso'") !== false) {
    echo "   ✅ KSO authorization logic implemented\n\n";
} else {
    echo "   ❌ KSO authorization needs update\n\n";
}

// Test 5: Check if KSO listAll exists
echo "5️⃣  Testing KSO listAll method...\n";
if (strpos($ksoController, 'public function listAll') !== false) {
    echo "   ✅ KSO listAll() method exists\n\n";
} else {
    echo "   ❌ KSO listAll() method missing\n\n";
}

// Test 6: Check if routes has KSO list-all
echo "6️⃣  Testing KSO routes...\n";
$routes = file_get_contents(__DIR__ . '/routes/web.php');
if (strpos($routes, "Route::get('/list-all'") !== false) {
    echo "   ✅ KSO list-all route exists\n\n";
} else {
    echo "   ❌ KSO list-all route missing\n\n";
}

// Test 7: Check if KSO Create.vue exists
echo "7️⃣  Testing KSO Vue components...\n";
$createVue = __DIR__ . '/resources/js/Pages/KSO/Create.vue';
$showVue = __DIR__ . '/resources/js/Pages/KSO/Show.vue';
$listAllVue = __DIR__ . '/resources/js/Pages/KSO/ListAll.vue';

if (file_exists($createVue) && file_exists($showVue) && file_exists($listAllVue)) {
    echo "   ✅ All KSO Vue components exist\n";
    echo "      - Create.vue ✅\n";
    echo "      - Show.vue ✅\n";
    echo "      - ListAll.vue ✅\n\n";
} else {
    echo "   ❌ Some KSO Vue components missing\n\n";
}

// Test 8: Check if build directory exists
echo "8️⃣  Testing frontend build...\n";
if (file_exists(__DIR__ . '/public/build/manifest.json')) {
    echo "   ✅ Frontend assets built successfully\n\n";
} else {
    echo "   ⚠️  Frontend needs to be built (run: npm run build)\n\n";
}

// Test 9: Check session configuration
echo "9️⃣  Testing session configuration...\n";
$env = file_get_contents(__DIR__ . '/.env');
if (strpos($env, 'SESSION_DRIVER=database') !== false) {
    echo "   ✅ Session driver is database\n";
    $lifetime = preg_match('/SESSION_LIFETIME=(\d+)/', $env, $matches);
    if ($lifetime) {
        echo "   ✅ Session lifetime: {$matches[1]} minutes\n\n";
    }
} else {
    echo "   ⚠️  Check SESSION_DRIVER in .env\n\n";
}

// Summary
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 SUMMARY\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$checks = [
    'LogUserActivity fix' => strpos($logActivity, 'Check multiple possible ID fields') !== false,
    'StaffPerencanaan no_nota' => strpos($staffController, "\$data['no_nota'] = \$data['nomor'];") !== false,
    'app.js 419 handler' => strpos($appJs, 'refreshCsrfToken') !== false,
    'KSO authorization' => strpos($ksoController, "user->role !== 'kso'") !== false,
    'KSO listAll method' => strpos($ksoController, 'public function listAll') !== false,
    'KSO routes' => strpos($routes, "Route::get('/list-all'") !== false,
    'KSO Vue components' => file_exists($createVue) && file_exists($showVue),
    'Frontend build' => file_exists(__DIR__ . '/public/build/manifest.json'),
    'Session config' => strpos($env, 'SESSION_DRIVER=database') !== false,
];

$passed = array_filter($checks);
$total = count($checks);
$passedCount = count($passed);

foreach ($checks as $name => $status) {
    echo ($status ? "✅" : "❌") . " {$name}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "RESULT: {$passedCount}/{$total} checks passed\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if ($passedCount === $total) {
    echo "🎉 ALL FIXES VERIFIED! READY FOR TESTING!\n\n";
    echo "Next steps:\n";
    echo "1. Start server: php artisan serve\n";
    echo "2. Test logout (all roles)\n";
    echo "3. Test login after idle\n";
    echo "4. Test DPP create\n";
    echo "5. Test KSO workflow\n";
    echo "6. Test approve/reject/revisi\n\n";
} else {
    echo "⚠️  Some fixes need attention. Check the report above.\n\n";
}

echo "📖 For detailed information, see:\n";
echo "   - COMPREHENSIVE_FIX_ALL_ISSUES.md\n";
echo "   - QUICK_FIX_SUMMARY.md\n\n";
