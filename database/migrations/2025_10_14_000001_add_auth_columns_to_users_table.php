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
            if (! Schema::hasColumn('users', 'email')) {
                $table->string('email')->unique()->after('nama');
            }

            if (! Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'password')) {
                $table->string('password')->nullable()->after('email_verified_at');
            }

            if (! Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('password');
            }

            if (! Schema::hasColumn('users', 'created_at') && ! Schema::hasColumn('users', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }

            if (Schema::hasColumn('users', 'password')) {
                $table->dropColumn('password');
            }

            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }

            if (Schema::hasColumn('users', 'email')) {
                $table->dropUnique(['email']);
                $table->dropColumn('email');
            }

            if (Schema::hasColumn('users', 'created_at') && Schema::hasColumn('users', 'updated_at')) {
                $table->dropTimestamps();
            }
        });
    }
};
