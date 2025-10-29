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
        // Use raw SQL to rename column safely
        DB::statement("ALTER TABLE permintaan CHANGE COLUMN klasifikasi_barang klasifikasi_permintaan ENUM('medis', 'non_medis', 'penunjang_medis') DEFAULT 'medis' COMMENT 'Klasifikasi jenis permintaan: medis, non_medis, atau penunjang_medis'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename back to klasifikasi_barang
        DB::statement("ALTER TABLE permintaan CHANGE COLUMN klasifikasi_permintaan klasifikasi_barang ENUM('medis', 'non_medis', 'penunjang_medis') DEFAULT 'medis' COMMENT 'Klasifikasi jenis barang: medis, non_medis, atau penunjang_medis'");
    }
};
