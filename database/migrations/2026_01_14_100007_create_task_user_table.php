<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 📋 Task User Pivot Table - İş Atamaları
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * İşe atanan spesifik kişiler.
 * assigned_at: Atama zamanı
 *
 * ═══════════════════════════════════════════════════════════════════════════
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_user', function (Blueprint $table) {
            $table->foreignUuid('project_task_id')->constrained('project_tasks')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            // Composite Primary Key
            $table->primary(['project_task_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_user');
    }
};
