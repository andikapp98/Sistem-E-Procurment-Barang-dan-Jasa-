<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permintaan', function (Blueprint $table) {
            // Tambah kolom klasifikasi permintaan
            $table->enum('klasifikasi_barang', ['medis', 'non_medis', 'penunjang_medis'])
                  ->after('bidang')
                  ->default('medis')
                  ->comment('Klasifikasi jenis permintaan: medis, non_medis, atau penunjang_medis');
            
            // Tambah kolom kabid tujuan (nullable karena bisa langsung ke direktur)
            $table->string('kabid_tujuan')->nullable()->after('klasifikasi_barang')
                  ->comment('Kepala Bidang tujuan berdasarkan klasifikasi: Bidang Pelayanan Medis, Bidang Penunjang Medis, dll');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaan', function (Blueprint $table) {
            $table->dropColumn(['klasifikasi_barang', 'kabid_tujuan']);
        });
    }
};
