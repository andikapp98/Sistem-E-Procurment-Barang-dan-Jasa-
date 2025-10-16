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
        Schema::table('users', function (Blueprint $table) {
            // Add role column if not exists, with default value
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('password');
            } else {
                // If exists but no default, modify it
                $table->string('role')->default('user')->change();
            }
            
            // Add jabatan column if not exists
            if (!Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan')->nullable()->after('role');
            }
            
            // Add unit_kerja column if not exists
            if (!Schema::hasColumn('users', 'unit_kerja')) {
                $table->string('unit_kerja')->nullable()->after('jabatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'unit_kerja')) {
                $table->dropColumn('unit_kerja');
            }
            
            if (Schema::hasColumn('users', 'jabatan')) {
                $table->dropColumn('jabatan');
            }
            
            // Don't drop role, just remove default
            if (Schema::hasColumn('users', 'role')) {
                $table->string('role')->default(null)->change();
            }
        });
    }
};
