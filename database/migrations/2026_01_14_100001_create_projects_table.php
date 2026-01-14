<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🏗️ Projects Table - Ana Proje Gövdesi
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * 4 katmanlı hiyerarşinin en üst seviyesi.
 * Project → Phases → Modules → Tasks
 *
 * @see \App\Models\Project
 * ═══════════════════════════════════════════════════════════════════════════
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            // Primary Key - UUID
            $table->uuid('id')->primary();

            // Foreign Keys
            $table->foreignUuid('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignUuid('leader_id')->constrained('users')->onDelete('restrict');

            // Auto-generated project code (e.g., PRJ-2026-001)
            $table->string('project_id_code')->unique();

            // Core Fields
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('timezone')->default('Europe/Istanbul');

            // Status & Dates
            $table->string('status')->default('draft'); // draft, active, completed, on_hold, cancelled
            $table->date('start_date')->nullable();
            $table->date('target_end_date')->nullable();

            // Dynamic Fields (JSONB for PostgreSQL)
            $table->jsonb('custom_fields')->nullable();

            // Audit & Soft Deletes
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
