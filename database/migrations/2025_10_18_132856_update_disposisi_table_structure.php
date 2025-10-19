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
        Schema::table('disposisi', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['no_disposisi', 'dari', 'kepada', 'isi_disposisi']);
            
            // Add new columns
            $table->string('jabatan_tujuan')->after('nota_id');
            $table->text('catatan')->nullable()->after('tanggal_disposisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disposisi', function (Blueprint $table) {
            // Restore old columns
            $table->string('no_disposisi')->after('nota_id');
            $table->string('dari')->after('tanggal_disposisi');
            $table->string('kepada')->after('dari');
            $table->text('isi_disposisi')->after('kepada');
            
            // Drop new columns
            $table->dropColumn(['jabatan_tujuan', 'catatan']);
        });
    }
};
