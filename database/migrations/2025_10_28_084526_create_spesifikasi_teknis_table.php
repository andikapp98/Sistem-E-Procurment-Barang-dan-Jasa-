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
        Schema::create('spesifikasi_teknis', function (Blueprint $table) {
            $table->id('spesifikasi_id');
            $table->unsignedBigInteger('permintaan_id');
            
            // Section 1: Latar Belakang & Tujuan
            $table->text('latar_belakang');
            $table->text('maksud_tujuan');
            $table->text('target_sasaran');
            
            // Section 2: Pejabat & Anggaran
            $table->string('pejabat_pengadaan');
            $table->string('sumber_dana');
            $table->string('perkiraan_biaya');
            
            // Section 3: Detail Barang/Jasa
            $table->text('jenis_barang_jasa');
            $table->text('fungsi_manfaat');
            $table->enum('kegiatan_rutin', ['Ya', 'Tidak']);
            
            // Section 4: Waktu & Tenaga
            $table->string('jangka_waktu');
            $table->string('estimasi_waktu_datang');
            $table->string('tenaga_diperlukan');
            
            // Section 5: Pelaku Usaha & Konsolidasi
            $table->text('pelaku_usaha');
            $table->enum('pengadaan_sejenis', ['Ya', 'Tidak']);
            $table->string('pengadaan_sejenis_keterangan')->nullable();
            $table->enum('indikasi_konsolidasi', ['Ya', 'Tidak']);
            $table->text('indikasi_konsolidasi_keterangan')->nullable();
            
            $table->timestamps();
            
            // Foreign key
            $table->foreign('permintaan_id')
                  ->references('permintaan_id')
                  ->on('permintaan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spesifikasi_teknis');
    }
};
