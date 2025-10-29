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
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('role', 50)->index(); // Role user saat melakukan aksi
            $table->string('action', 100)->index(); // login, logout, create, update, delete, view, approve, reject, dll
            $table->string('module', 50)->index(); // permintaan, disposisi, nota_dinas, perencanaan, kso, dll
            $table->string('description')->nullable(); // Deskripsi detail
            $table->string('url')->nullable(); // URL yang diakses
            $table->string('method', 10)->nullable(); // GET, POST, PUT, DELETE
            $table->string('ip_address', 45)->nullable(); // IP Address
            $table->text('user_agent')->nullable(); // Browser/Device info
            $table->json('request_data')->nullable(); // Data request (filtered)
            $table->json('response_data')->nullable(); // Response data (optional)
            $table->integer('status_code')->nullable(); // HTTP status code
            $table->unsignedBigInteger('related_id')->nullable(); // ID dari record terkait (permintaan_id, disposisi_id, dll)
            $table->string('related_type')->nullable(); // Type dari record terkait
            $table->decimal('duration', 8, 2)->nullable(); // Durasi eksekusi dalam detik
            $table->timestamps();
            
            // Indexes untuk query performance
            $table->index(['user_id', 'created_at']);
            $table->index(['role', 'action']);
            $table->index(['module', 'created_at']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
    }
};
