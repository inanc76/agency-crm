<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * ✅ Project Tasks Table - Eylem Katmanı
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Modül içindeki işler - öncelik ve süre takibi.
 * En alt seviye - gerçek iş birimleri.
 *
 * @see \App\Models\ProjectTask
 * ═══════════════════════════════════════════════════════════════════════════
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            // Primary Key - UUID
            $table->uuid('id')->primary();

            // Parent Relation
            $table->foreignUuid('module_id')->constrained('project_modules')->onDelete('cascade');

            // Core Fields
            $table->string('name');
            $table->text('description')->nullable();

            // Status & Priority
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->string('priority')->default('medium'); // low, medium, high, urgent

            // Time Tracking
            $table->date('due_date')->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();

            // Order
            $table->integer('order')->default(0);

            // Audit & Soft Deletes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};
