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
        Schema::table('perencanaan', function (Blueprint $table) {
            // PPK dan Identifikasi
            $table->string('ppk_ditunjuk')->nullable()->after('metode_pengadaan');
            $table->string('nama_paket')->nullable();
            $table->string('lokasi')->nullable();
            
            // Program dan Kegiatan
            $table->text('uraian_program')->nullable();
            $table->text('uraian_kegiatan')->nullable();
            $table->string('sub_kegiatan')->nullable();
            $table->string('sub_sub_kegiatan')->nullable();
            $table->string('kode_rekening')->nullable();
            
            // Anggaran dan HPS
            $table->string('sumber_dana')->nullable();
            $table->decimal('pagu_paket', 15, 2)->nullable();
            $table->decimal('nilai_hps', 15, 2)->nullable();
            $table->text('sumber_data_survei_hps')->nullable();
            
            // Kontrak dan Pelaksanaan
            $table->string('jenis_kontrak')->nullable();
            $table->string('kualifikasi')->nullable();
            $table->integer('jangka_waktu_pelaksanaan')->nullable(); // dalam hari
            
            // Detail Pengadaan
            $table->string('nama_kegiatan')->nullable();
            $table->string('jenis_pengadaan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perencanaan', function (Blueprint $table) {
            $table->dropColumn([
                'ppk_ditunjuk',
                'nama_paket',
                'lokasi',
                'uraian_program',
                'uraian_kegiatan',
                'sub_kegiatan',
                'sub_sub_kegiatan',
                'kode_rekening',
                'sumber_dana',
                'pagu_paket',
                'nilai_hps',
                'sumber_data_survei_hps',
                'jenis_kontrak',
                'kualifikasi',
                'jangka_waktu_pelaksanaan',
                'nama_kegiatan',
                'jenis_pengadaan',
            ]);
        });
    }
};
