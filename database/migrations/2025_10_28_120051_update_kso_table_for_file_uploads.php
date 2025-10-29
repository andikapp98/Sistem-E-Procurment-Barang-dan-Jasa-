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
        Schema::table('kso', function (Blueprint $table) {
            // Ubah isi_kerjasama menjadi nullable
            $table->text('isi_kerjasama')->nullable()->change();
            
            // Tambah kolom untuk file uploads
            $table->string('file_pks')->nullable()->after('isi_kerjasama');
            $table->string('file_mou')->nullable()->after('file_pks');
            $table->text('keterangan')->nullable()->after('file_mou');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kso', function (Blueprint $table) {
            // Kembalikan isi_kerjasama ke NOT NULL
            $table->text('isi_kerjasama')->nullable(false)->change();
            
            // Hapus kolom file uploads
            $table->dropColumn(['file_pks', 'file_mou', 'keterangan']);
        });
    }
};
