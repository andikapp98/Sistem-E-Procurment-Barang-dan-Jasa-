<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * REVISI WORKFLOW: Pengadaan → KSO → Nota Penerimaan
 * 
 * Urutan baru:
 * 1. Permintaan
 * 2. Nota Dinas
 * 3. Disposisi
 * 4. Perencanaan
 * 5. Pengadaan    ← Dulu step 6, sekarang step 5
 * 6. KSO          ← Dulu step 5, sekarang step 6
 * 7. Nota Penerimaan
 * 8. Serah Terima
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add perencanaan_id to pengadaan table
        if (!Schema::hasColumn('pengadaan', 'perencanaan_id')) {
            Schema::table('pengadaan', function (Blueprint $table) {
                $table->unsignedBigInteger('perencanaan_id')->nullable()->after('pengadaan_id');
                $table->index('perencanaan_id', 'idx_pengadaan_perencanaan');
                
                // Foreign key constraint
                $table->foreign('perencanaan_id', 'fk_pengadaan_perencanaan')
                      ->references('perencanaan_id')
                      ->on('perencanaan')
                      ->onDelete('cascade');
            });
        }

        // 2. Add pengadaan_id to kso table
        if (!Schema::hasColumn('kso', 'pengadaan_id')) {
            Schema::table('kso', function (Blueprint $table) {
                $table->unsignedBigInteger('pengadaan_id')->nullable()->after('kso_id');
                $table->index('pengadaan_id', 'idx_kso_pengadaan');
                
                // Foreign key constraint
                $table->foreign('pengadaan_id', 'fk_kso_pengadaan')
                      ->references('pengadaan_id')
                      ->on('pengadaan')
                      ->onDelete('cascade');
            });
        }

        // 3. Add kso_id to nota_penerimaan table
        if (!Schema::hasColumn('nota_penerimaan', 'kso_id')) {
            Schema::table('nota_penerimaan', function (Blueprint $table) {
                $table->unsignedBigInteger('kso_id')->nullable()->after('nota_penerimaan_id');
                $table->index('kso_id', 'idx_nota_penerimaan_kso');
                
                // Foreign key constraint
                $table->foreign('kso_id', 'fk_nota_penerimaan_kso')
                      ->references('kso_id')
                      ->on('kso')
                      ->onDelete('cascade');
            });
        }

        // 4. Migrate existing data (if any)
        // Copy perencanaan_id from related KSO to Pengadaan
        DB::statement("
            UPDATE pengadaan p
            JOIN kso k ON k.kso_id = p.kso_id
            SET p.perencanaan_id = k.perencanaan_id
            WHERE p.perencanaan_id IS NULL AND p.kso_id IS NOT NULL
        ");

        // Copy pengadaan_id from Pengadaan to KSO
        DB::statement("
            UPDATE kso k
            JOIN pengadaan p ON p.kso_id = k.kso_id
            SET k.pengadaan_id = p.pengadaan_id
            WHERE k.pengadaan_id IS NULL AND k.kso_id IS NOT NULL
        ");

        // Copy kso_id from Pengadaan to NotaPenerimaan
        DB::statement("
            UPDATE nota_penerimaan np
            JOIN pengadaan p ON p.pengadaan_id = np.pengadaan_id
            SET np.kso_id = p.kso_id
            WHERE np.kso_id IS NULL AND np.pengadaan_id IS NOT NULL AND p.kso_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove new foreign keys and columns
        if (Schema::hasColumn('nota_penerimaan', 'kso_id')) {
            Schema::table('nota_penerimaan', function (Blueprint $table) {
                $table->dropForeign('fk_nota_penerimaan_kso');
                $table->dropIndex('idx_nota_penerimaan_kso');
                $table->dropColumn('kso_id');
            });
        }

        if (Schema::hasColumn('kso', 'pengadaan_id')) {
            Schema::table('kso', function (Blueprint $table) {
                $table->dropForeign('fk_kso_pengadaan');
                $table->dropIndex('idx_kso_pengadaan');
                $table->dropColumn('pengadaan_id');
            });
        }

        if (Schema::hasColumn('pengadaan', 'perencanaan_id')) {
            Schema::table('pengadaan', function (Blueprint $table) {
                $table->dropForeign('fk_pengadaan_perencanaan');
                $table->dropIndex('idx_pengadaan_perencanaan');
                $table->dropColumn('perencanaan_id');
            });
        }
    }
};
