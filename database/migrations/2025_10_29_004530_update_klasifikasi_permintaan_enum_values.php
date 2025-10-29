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
        // First, convert enum to varchar to allow data updates
        DB::statement("ALTER TABLE permintaan MODIFY COLUMN klasifikasi_permintaan VARCHAR(50)");
        
        // Update existing data to match new enum values
        DB::statement("UPDATE permintaan SET klasifikasi_permintaan = 'Medis' WHERE klasifikasi_permintaan = 'medis'");
        DB::statement("UPDATE permintaan SET klasifikasi_permintaan = 'Non Medis' WHERE klasifikasi_permintaan = 'non_medis'");
        DB::statement("UPDATE permintaan SET klasifikasi_permintaan = 'Penunjang' WHERE klasifikasi_permintaan = 'penunjang_medis'");
        
        // Then convert back to enum with new values
        DB::statement("ALTER TABLE permintaan MODIFY COLUMN klasifikasi_permintaan ENUM('Medis', 'Non Medis', 'Penunjang') DEFAULT 'Medis' COMMENT 'Klasifikasi jenis permintaan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert to varchar first
        DB::statement("ALTER TABLE permintaan MODIFY COLUMN klasifikasi_permintaan VARCHAR(50)");
        
        // Update data back to old format
        DB::statement("UPDATE permintaan SET klasifikasi_permintaan = 'medis' WHERE klasifikasi_permintaan = 'Medis'");
        DB::statement("UPDATE permintaan SET klasifikasi_permintaan = 'non_medis' WHERE klasifikasi_permintaan = 'Non Medis'");
        DB::statement("UPDATE permintaan SET klasifikasi_permintaan = 'penunjang_medis' WHERE klasifikasi_permintaan = 'Penunjang'");
        
        // Revert back to old enum values
        DB::statement("ALTER TABLE permintaan MODIFY COLUMN klasifikasi_permintaan ENUM('medis', 'non_medis', 'penunjang_medis') DEFAULT 'medis' COMMENT 'Klasifikasi jenis permintaan: medis, non_medis, atau penunjang_medis'");
    }
};
