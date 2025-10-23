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
        Schema::table('permintaan', function (Blueprint $table) {
            $table->string('disposisi_tujuan')->nullable()->after('link_scan');
            $table->text('catatan_disposisi')->nullable()->after('disposisi_tujuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaan', function (Blueprint $table) {
            $table->dropColumn(['disposisi_tujuan', 'catatan_disposisi']);
        });
    }
};
