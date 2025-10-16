<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration untuk menambahkan tabel tracking tahapan e-procurement
     * Tabel: nota_dinas, disposisi, perencanaan, kso, pengadaan, nota_penerimaan, serah_terima
     */
    public function up(): void
    {
        // 1. Tabel Nota Dinas
        if (!Schema::hasTable('nota_dinas')) {
            Schema::create('nota_dinas', function (Blueprint $table) {
            $table->id('nota_id');
            $table->unsignedBigInteger('permintaan_id');
            $table->string('dari_unit')->nullable();
            $table->string('ke_jabatan')->nullable();
            $table->date('tanggal_nota');
            $table->string('status')->default('draft'); // draft, proses, dikirim, ditolak, disetujui
            $table->timestamps();

            // Foreign key
            $table->foreign('permintaan_id')
                  ->references('permintaan_id')
                  ->on('permintaan')
                  ->onDelete('cascade');
                  
            // Index
            $table->index('permintaan_id');
            $table->index('status');
            });
        }

        // 2. Tabel Disposisi
        if (!Schema::hasTable('disposisi')) {
            Schema::create('disposisi', function (Blueprint $table) {
            $table->id('disposisi_id');
            $table->unsignedBigInteger('nota_id');
            $table->string('jabatan_tujuan');
            $table->date('tanggal_disposisi');
            $table->text('catatan')->nullable();
            $table->string('status')->default('menunggu'); // menunggu, dalam_proses, ditolak, disetujui
            $table->timestamps();

            // Foreign key
            $table->foreign('nota_id')
                  ->references('nota_id')
                  ->on('nota_dinas')
                  ->onDelete('cascade');
                  
            // Index
            $table->index('nota_id');
            $table->index('status');
            });
        }

        // 3. Tabel Perencanaan
        if (!Schema::hasTable('perencanaan')) {
            Schema::create('perencanaan', function (Blueprint $table) {
            $table->id('perencanaan_id');
            $table->unsignedBigInteger('disposisi_id');
            $table->date('tanggal_perencanaan');
            $table->text('rincian')->nullable();
            $table->string('status')->default('draft'); // draft, review, revisi, disetujui
            $table->timestamps();

            // Foreign key
            $table->foreign('disposisi_id')
                  ->references('disposisi_id')
                  ->on('disposisi')
                  ->onDelete('cascade');
                  
            // Index
            $table->index('disposisi_id');
            $table->index('status');
            });
        }

        // 4. Tabel KSO (Kerja Sama Operasional)
        if (!Schema::hasTable('kso')) {
            Schema::create('kso', function (Blueprint $table) {
            $table->id('kso_id');
            $table->unsignedBigInteger('perencanaan_id');
            $table->date('tanggal_kso');
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('draft'); // draft, negosiasi, proses_kontrak, aktif, selesai
            $table->timestamps();

            // Foreign key
            $table->foreign('perencanaan_id')
                  ->references('perencanaan_id')
                  ->on('perencanaan')
                  ->onDelete('cascade');
                  
            // Index
            $table->index('perencanaan_id');
            $table->index('status');
            });
        }

        // 5. Tabel Pengadaan
        if (!Schema::hasTable('pengadaan')) {
            Schema::create('pengadaan', function (Blueprint $table) {
            $table->id('pengadaan_id');
            $table->unsignedBigInteger('kso_id');
            $table->date('tanggal_pengadaan');
            $table->string('vendor');
            $table->string('tracking')->nullable();
            $table->string('status')->default('tender'); // tender, pembelian, pengiriman, diterima, ditolak
            $table->timestamps();

            // Foreign key
            $table->foreign('kso_id')
                  ->references('kso_id')
                  ->on('kso')
                  ->onDelete('cascade');
                  
            // Index
            $table->index('kso_id');
            $table->index('status');
            $table->index('vendor');
            });
        }

        // 6. Tabel Nota Penerimaan
        if (!Schema::hasTable('nota_penerimaan')) {
            Schema::create('nota_penerimaan', function (Blueprint $table) {
            $table->id('nota_penerimaan_id');
            $table->unsignedBigInteger('pengadaan_id');
            $table->date('tanggal_penerimaan');
            $table->text('catatan')->nullable();
            $table->string('status')->default('pending'); // pending, diterima_sebagian, diterima_lengkap, ditolak
            $table->timestamps();

            // Foreign key
            $table->foreign('pengadaan_id')
                  ->references('pengadaan_id')
                  ->on('pengadaan')
                  ->onDelete('cascade');
                  
            // Index
            $table->index('pengadaan_id');
            $table->index('status');
            });
        }

        // 7. Tabel Serah Terima
        if (!Schema::hasTable('serah_terima')) {
            Schema::create('serah_terima', function (Blueprint $table) {
            $table->id('serah_id');
            $table->unsignedBigInteger('nota_penerimaan_id');
            $table->date('tanggal_serah');
            $table->string('penerima');
            $table->string('status')->default('menunggu_penerima'); // menunggu_penerima, diterima_unit, selesai
            $table->timestamps();

            // Foreign key
            $table->foreign('nota_penerimaan_id')
                  ->references('nota_penerimaan_id')
                  ->on('nota_penerimaan')
                  ->onDelete('cascade');
                  
            // Index
            $table->index('nota_penerimaan_id');
            $table->index('status');
            });
        }
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
    }
};
