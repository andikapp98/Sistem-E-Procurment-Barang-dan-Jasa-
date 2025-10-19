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
        // Tambah 'revisi' ke enum status di tabel permintaan
        DB::statement("ALTER TABLE permintaan MODIFY COLUMN status ENUM('diajukan', 'proses', 'disetujui', 'ditolak', 'revisi') DEFAULT 'diajukan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum sebelumnya (tanpa 'revisi')
        DB::statement("ALTER TABLE permintaan MODIFY COLUMN status ENUM('diajukan', 'proses', 'disetujui', 'ditolak') DEFAULT 'diajukan'");
    }
};
