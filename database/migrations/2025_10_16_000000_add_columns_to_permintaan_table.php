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
            $table->string('pic_pimpinan')->nullable()->after('status');
            $table->string('no_nota_dinas')->nullable()->after('pic_pimpinan');
            $table->string('link_scan')->nullable()->after('no_nota_dinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaan', function (Blueprint $table) {
            $table->dropColumn(['pic_pimpinan', 'no_nota_dinas', 'link_scan']);
        });
    }
};
