<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE disposisi MODIFY COLUMN status ENUM('pending', 'diproses', 'selesai', 'ditolak', 'disetujui', 'dalam_proses') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE disposisi MODIFY COLUMN status ENUM('pending', 'diproses', 'selesai', 'ditolak') DEFAULT 'pending'");
    }
};
