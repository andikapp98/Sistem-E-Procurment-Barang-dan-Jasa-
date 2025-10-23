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
        // Update nota_dinas table
        Schema::table('nota_dinas', function (Blueprint $table) {
            // Add new fields if not exists
            if (!Schema::hasColumn('nota_dinas', 'sifat')) {
                $table->string('sifat')->nullable()->after('tanggal_nota');
            }
            if (!Schema::hasColumn('nota_dinas', 'lampiran')) {
                $table->text('lampiran')->nullable()->after('sifat');
            }
            if (!Schema::hasColumn('nota_dinas', 'detail')) {
                $table->text('detail')->nullable()->after('perihal');
            }
            if (!Schema::hasColumn('nota_dinas', 'mengetahui')) {
                $table->string('mengetahui')->nullable()->after('detail');
            }
        });

        // Update permintaan table - remove disposisi fields (will use separate disposisi records)
        Schema::table('permintaan', function (Blueprint $table) {
            if (!Schema::hasColumn('permintaan', 'wadir_tujuan')) {
                $table->string('wadir_tujuan')->nullable()->after('catatan_disposisi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->dropColumn(['sifat', 'lampiran', 'detail', 'mengetahui']);
        });
        
        Schema::table('permintaan', function (Blueprint $table) {
            $table->dropColumn('wadir_tujuan');
        });
    }
};
