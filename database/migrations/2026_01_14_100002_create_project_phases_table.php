<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 📊 Project Phases Table - Stratejik Katman
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Proje fazları - max 20 kayıt sınırı uygulama seviyesinde.
 * Phase → Modules ilişkisi
 *
 * @see \App\Models\ProjectPhase
 * ═══════════════════════════════════════════════════════════════════════════
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_phases', function (Blueprint $table) {
            // Primary Key - UUID
            $table->uuid('id')->primary();

            // Parent Relation
            $table->foreignUuid('project_id')->constrained('projects')->onDelete('cascade');

            // Core Fields
            $table->string('name');
            $table->text('description')->nullable();

            // Status & Order
            $table->string('status')->default('planned'); // planned, in_progress, completed, cancelled
            $table->integer('order')->default(0);

            // Audit & Soft Deletes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_phases');
    }
};
