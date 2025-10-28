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
            $table->enum('tipe_nota', ['usulan', 'pembelian'])->default('usulan')->after('perihal');
            $table->text('isi_nota')->nullable()->after('tipe_nota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->dropColumn(['tipe_nota', 'isi_nota']);
        });
    }
};
