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
        Schema::create('dokumen_pengadaan', function (Blueprint $table) {
            $table->id('dokumen_id');
            $table->unsignedBigInteger('permintaan_id');
            $table->enum('jenis_dokumen', [
                'Nota Dinas',
                'DPP',
                'KAK',
                'SP',
                'Kuitansi',
                'BAST'
            ]);
            $table->string('nama_file');
            $table->text('link_file');
            $table->date('tanggal_upload');
            $table->string('uploaded_by')->nullable(); // nama user yang upload
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->foreign('permintaan_id')->references('permintaan_id')->on('permintaan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pengadaan');
    }
};
