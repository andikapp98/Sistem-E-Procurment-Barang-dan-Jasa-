<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking KSO Table Structure\n";
echo "================================\n\n";

$columns = DB::select('DESCRIBE kso');

echo "All columns:\n";
foreach($columns as $col) {
    echo "- {$col->Field} ({$col->Type})\n";
}

echo "\n\nStatus column detail:\n";
foreach($columns as $col) {
    if(stripos($col->Field, 'status') !== false) {
        echo "Field: {$col->Field}\n";
        echo "Type: {$col->Type}\n";
        echo "Null: {$col->Null}\n";
        echo "Default: {$col->Default}\n";
    }
}
