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
        Schema::table('nota_dinas', function (Blueprint $table) {
            // Menambahkan field untuk nota dinas usulan
            $table->string('nomor')->nullable()->after('no_nota');
            $table->string('penerima')->nullable()->after('kepada');
            $table->enum('sifat', ['Sangat Segera', 'Segera', 'Biasa', 'Rahasia'])->nullable()->after('penerima');
            $table->string('kode_program')->nullable()->after('sifat');
            $table->string('kode_kegiatan')->nullable()->after('kode_program');
            $table->string('kode_rekening')->nullable()->after('kode_kegiatan');
            $table->text('uraian')->nullable()->after('kode_rekening');
            $table->decimal('pagu_anggaran', 15, 2)->nullable()->after('uraian');
            $table->decimal('pph', 15, 2)->nullable()->after('pagu_anggaran');
            $table->decimal('ppn', 15, 2)->nullable()->after('pph');
            $table->decimal('pph_21', 15, 2)->nullable()->after('ppn');
            $table->decimal('pph_4_2', 15, 2)->nullable()->after('pph_21');
            $table->decimal('pph_22', 15, 2)->nullable()->after('pph_4_2');
            $table->string('unit_instalasi')->nullable()->after('pph_22');
            $table->string('no_faktur_pajak')->nullable()->after('unit_instalasi');
            $table->string('no_kwitansi')->nullable()->after('no_faktur_pajak');
            $table->date('tanggal_faktur_pajak')->nullable()->after('no_kwitansi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->dropColumn([
                'nomor',
                'penerima',
                'sifat',
                'kode_program',
                'kode_kegiatan',
                'kode_rekening',
                'uraian',
                'pagu_anggaran',
                'pph',
                'ppn',
                'pph_21',
                'pph_4_2',
                'pph_22',
                'unit_instalasi',
                'no_faktur_pajak',
                'no_kwitansi',
                'tanggal_faktur_pajak',
            ]);
        });
    }
};
