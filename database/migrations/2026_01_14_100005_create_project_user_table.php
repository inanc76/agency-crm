<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 👥 Project User Pivot Table - Proje Üyeleri
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Projeye atanan genel üyeler.
 * Rol: member, reviewer, observer
 *
 * ═══════════════════════════════════════════════════════════════════════════
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->foreignUuid('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role')->default('member'); // member, reviewer, observer
            $table->timestamps();

            // Composite Primary Key
            $table->primary(['project_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
