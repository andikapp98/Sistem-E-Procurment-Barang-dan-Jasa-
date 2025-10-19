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
            // Cek apakah kolom sudah ada sebelum menambahkan
            if (!Schema::hasColumn('kso', 'no_kso')) {
                $table->string('no_kso', 100)->unique()->after('perencanaan_id');
            }
            if (!Schema::hasColumn('kso', 'pihak_pertama')) {
                $table->string('pihak_pertama')->nullable()->after('tanggal_kso');
            }
            if (!Schema::hasColumn('kso', 'pihak_kedua')) {
                $table->string('pihak_kedua')->nullable()->after('pihak_pertama');
            }
            if (!Schema::hasColumn('kso', 'isi_kerjasama')) {
                $table->text('isi_kerjasama')->nullable()->after('pihak_kedua');
            }
            if (!Schema::hasColumn('kso', 'nilai_kontrak')) {
                $table->decimal('nilai_kontrak', 15, 2)->nullable()->after('isi_kerjasama');
            }
            
            // Drop kolom deskripsi jika ada karena diganti dengan isi_kerjasama
            if (Schema::hasColumn('kso', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kso', function (Blueprint $table) {
            $table->dropColumn([
                'no_kso',
                'pihak_pertama',
                'pihak_kedua',
                'isi_kerjasama',
                'nilai_kontrak'
            ]);
            
            // Kembalikan kolom deskripsi
            $table->text('deskripsi')->nullable();
        });
    }
};
