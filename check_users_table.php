<?php

use Illuminate\Support\Facades\Schema;

echo "=== USERS TABLE COLUMNS ===\n\n";
$columns = Schema::getColumnListing('users');

foreach ($columns as $col) {
    echo "- {$col}\n";
}

echo "\n=== SAMPLE USER DATA ===\n\n";
$user = \App\Models\User::first();
if ($user) {
    print_r($user->toArray());
}
