<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permintaan;

echo "Testing Tracking Functionality\n";
echo "================================\n\n";

$permintaan = Permintaan::first();

if (!$permintaan) {
    echo "❌ No permintaan found in database\n";
    exit(1);
}

echo "✅ Permintaan found\n";
echo "ID: " . $permintaan->permintaan_id . "\n";
echo "Bidang: " . $permintaan->bidang . "\n";
echo "Status: " . $permintaan->status . "\n\n";

echo "Testing Methods:\n";
echo "----------------\n";

try {
    $trackingStatus = $permintaan->trackingStatus;
    echo "✅ trackingStatus: " . $trackingStatus . "\n";
} catch (\Exception $e) {
    echo "❌ trackingStatus ERROR: " . $e->getMessage() . "\n";
}

try {
    $progress = $permintaan->getProgressPercentage();
    echo "✅ getProgressPercentage(): " . $progress . "%\n";
} catch (\Exception $e) {
    echo "❌ getProgressPercentage() ERROR: " . $e->getMessage() . "\n";
}

try {
    $timeline = $permintaan->getTimelineTracking();
    echo "✅ getTimelineTracking(): " . count($timeline) . " items\n";
    
    if (count($timeline) > 0) {
        echo "\nTimeline Items:\n";
        foreach ($timeline as $item) {
            echo "  - " . $item['tahapan'] . " (" . $item['status'] . ")\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ getTimelineTracking() ERROR: " . $e->getMessage() . "\n";
}

try {
    $completeTracking = $permintaan->getCompleteTracking();
    echo "\n✅ getCompleteTracking(): " . count($completeTracking) . " items\n";
    
    $completed = array_filter($completeTracking, fn($item) => $item['completed']);
    $pending = array_filter($completeTracking, fn($item) => !$item['completed']);
    
    echo "   Completed: " . count($completed) . " steps\n";
    echo "   Pending: " . count($pending) . " steps\n";
} catch (\Exception $e) {
    echo "❌ getCompleteTracking() ERROR: " . $e->getMessage() . "\n";
}

try {
    $nextStep = $permintaan->getNextStep();
    echo "\n✅ getNextStep(): " . $nextStep['tahapan'] . "\n";
    echo "   Responsible: " . $nextStep['responsible'] . "\n";
} catch (\Exception $e) {
    echo "❌ getNextStep() ERROR: " . $e->getMessage() . "\n";
}

echo "\n================================\n";
echo "✅ All tests completed\n";
