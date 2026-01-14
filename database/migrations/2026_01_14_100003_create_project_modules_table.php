<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * ⚙️ Project Modules Table - Operasyonel Katman
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Faz içindeki modüller - tarih aralığı ve durum yönetimi.
 * Module → Tasks ilişkisi
 *
 * @see \App\Models\ProjectModule
 * ═══════════════════════════════════════════════════════════════════════════
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_modules', function (Blueprint $table) {
            // Primary Key - UUID
            $table->uuid('id')->primary();

            // Parent Relation
            $table->foreignUuid('phase_id')->constrained('project_phases')->onDelete('cascade');

            // Core Fields
            $table->string('name');
            $table->text('description')->nullable();

            // Date Range
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Status & Order
            $table->string('status')->default('planned'); // planned, in_progress, paused, completed, cancelled
            $table->integer('order')->default(0);

            // Audit & Soft Deletes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_modules');
    }
};
