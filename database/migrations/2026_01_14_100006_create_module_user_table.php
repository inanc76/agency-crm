<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🔐 Module User Pivot Table - Modül Yetkileri
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Modüle erişim yetkisi olan kullanıcılar.
 * Bu pivot, kullanıcının modül detayına erişimini belirler.
 *
 * ═══════════════════════════════════════════════════════════════════════════
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_user', function (Blueprint $table) {
            $table->foreignUuid('project_module_id')->constrained('project_modules')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Composite Primary Key
            $table->primary(['project_module_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_user');
    }
};
