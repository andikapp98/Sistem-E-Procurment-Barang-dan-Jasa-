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
            $table->text('link_scan_perencanaan')->nullable()->after('anggaran');
            $table->enum('metode_pengadaan', ['E-Purchasing', 'Tender', 'Penunjukan Langsung', 'Lelang'])->nullable()->after('link_scan_perencanaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perencanaan', function (Blueprint $table) {
            $table->dropColumn(['link_scan_perencanaan', 'metode_pengadaan']);
        });
    }
};
