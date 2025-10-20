#!/usr/bin/env php
<?php

/**
 * Clear Session Files Script
 * 
 * Run this script to clear all session files
 * Useful when experiencing login issues after logout
 */

$sessionPath = __DIR__ . '/../storage/framework/sessions';

if (!is_dir($sessionPath)) {
    echo "Session directory not found: {$sessionPath}\n";
    exit(1);
}

$files = glob($sessionPath . '/*');
$count = 0;

foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
        $count++;
    }
}

echo "✅ Cleared {$count} session file(s)\n";
echo "You can now login again.\n";
