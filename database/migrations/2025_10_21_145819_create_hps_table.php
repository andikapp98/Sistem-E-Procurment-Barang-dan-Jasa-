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
        // Table HPS (Header)
        Schema::create('hps', function (Blueprint $table) {
            $table->id('hps_id');
            $table->unsignedBigInteger('permintaan_id');
            $table->string('ppk');
            $table->string('surat_penawaran_harga');
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('permintaan_id')->references('permintaan_id')->on('permintaan')->onDelete('cascade');
        });

        // Table HPS Items (Detail)
        Schema::create('hps_items', function (Blueprint $table) {
            $table->id('hps_item_id');
            $table->unsignedBigInteger('hps_id');
            $table->string('nama_item');
            $table->integer('volume');
            $table->string('satuan');
            $table->decimal('harga_satuan', 15, 2);
            $table->string('type')->nullable();
            $table->string('merk')->nullable();
            $table->decimal('total', 15, 2);
            $table->timestamps();

            $table->foreign('hps_id')->references('hps_id')->on('hps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hps_items');
        Schema::dropIfExists('hps');
    }
};
