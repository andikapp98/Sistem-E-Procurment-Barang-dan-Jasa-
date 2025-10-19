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
        // Create permintaan table
        Schema::create('permintaan', function (Blueprint $table) {
            $table->id('permintaan_id');
            $table->unsignedBigInteger('user_id');
            $table->string('bidang')->nullable();
            $table->date('tanggal_permintaan')->nullable();
            $table->text('deskripsi');
            $table->enum('status', ['diajukan', 'proses', 'disetujui', 'ditolak', 'revisi'])->default('diajukan');
            $table->string('pic_pimpinan')->nullable();
            $table->string('no_nota_dinas')->nullable();
            $table->text('link_scan')->nullable();
            $table->timestamps();
            
            // Foreign key references id column in users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create nota_dinas table
        Schema::create('nota_dinas', function (Blueprint $table) {
            $table->id('nota_id');
            $table->unsignedBigInteger('permintaan_id');
            $table->string('no_nota');
            $table->date('tanggal_nota');
            $table->string('dari');
            $table->string('kepada');
            $table->text('perihal');
            $table->timestamps();
            
            $table->foreign('permintaan_id')->references('permintaan_id')->on('permintaan')->onDelete('cascade');
        });

        // Create disposisi table
        Schema::create('disposisi', function (Blueprint $table) {
            $table->id('disposisi_id');
            $table->unsignedBigInteger('nota_id');
            $table->string('no_disposisi');
            $table->date('tanggal_disposisi');
            $table->string('dari');
            $table->string('kepada');
            $table->text('isi_disposisi');
            $table->enum('status', ['pending', 'diproses', 'selesai', 'ditolak'])->default('pending');
            $table->timestamps();
            
            $table->foreign('nota_id')->references('nota_id')->on('nota_dinas')->onDelete('cascade');
        });

        // Create perencanaan table
        Schema::create('perencanaan', function (Blueprint $table) {
            $table->id('perencanaan_id');
            $table->unsignedBigInteger('disposisi_id');
            $table->text('rencana_kegiatan');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('anggaran', 15, 2)->nullable();
            $table->timestamps();
            
            $table->foreign('disposisi_id')->references('disposisi_id')->on('disposisi')->onDelete('cascade');
        });

        // Create kso table
        Schema::create('kso', function (Blueprint $table) {
            $table->id('kso_id');
            $table->unsignedBigInteger('perencanaan_id');
            $table->string('no_kso');
            $table->date('tanggal_kso');
            $table->string('pihak_pertama');
            $table->string('pihak_kedua');
            $table->text('isi_kerjasama');
            $table->enum('status', ['draft', 'aktif', 'selesai', 'batal'])->default('draft');
            $table->timestamps();
            
            $table->foreign('perencanaan_id')->references('perencanaan_id')->on('perencanaan')->onDelete('cascade');
        });

        // Create pengadaan table
        Schema::create('pengadaan', function (Blueprint $table) {
            $table->id('pengadaan_id');
            $table->unsignedBigInteger('kso_id');
            $table->string('no_pengadaan');
            $table->date('tanggal_pengadaan');
            $table->string('vendor')->nullable();
            $table->decimal('total_harga', 15, 2)->nullable();
            $table->enum('status', ['persiapan', 'pembelian', 'pengiriman', 'diterima'])->default('persiapan');
            $table->string('no_tracking')->nullable();
            $table->timestamps();
            
            $table->foreign('kso_id')->references('kso_id')->on('kso')->onDelete('cascade');
        });

        // Create nota_penerimaan table
        Schema::create('nota_penerimaan', function (Blueprint $table) {
            $table->id('penerimaan_id');
            $table->unsignedBigInteger('pengadaan_id');
            $table->string('no_penerimaan');
            $table->date('tanggal_penerimaan');
            $table->string('penerima');
            $table->text('keterangan')->nullable();
            $table->enum('kondisi', ['baik', 'rusak', 'tidak lengkap'])->default('baik');
            $table->timestamps();
            
            $table->foreign('pengadaan_id')->references('pengadaan_id')->on('pengadaan')->onDelete('cascade');
        });

        // Create serah_terima table
        Schema::create('serah_terima', function (Blueprint $table) {
            $table->id('serah_terima_id');
            $table->unsignedBigInteger('penerimaan_id');
            $table->string('no_serah_terima');
            $table->date('tanggal_serah_terima');
            $table->string('penyerah');
            $table->string('penerima');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->foreign('penerimaan_id')->references('penerimaan_id')->on('nota_penerimaan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serah_terima');
        Schema::dropIfExists('nota_penerimaan');
        Schema::dropIfExists('pengadaan');
        Schema::dropIfExists('kso');
        Schema::dropIfExists('perencanaan');
        Schema::dropIfExists('disposisi');
        Schema::dropIfExists('nota_dinas');
        Schema::dropIfExists('permintaan');
    }
};
