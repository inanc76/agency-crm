<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🔧 V2 Enhancement - custom_fields ve tarih kolonları
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * - project_phases: custom_fields, start_date, end_date eklendi
 * - project_modules: custom_fields eklendi
 * - project_tasks: custom_fields eklendi
 *
 * @version Constitution V10
 * ═══════════════════════════════════════════════════════════════════════════
 */
return new class extends Migration
{
    public function up(): void
    {
        // Project Phases - tarih alanları ve custom_fields ekle
        Schema::table('project_phases', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('order');
            $table->date('end_date')->nullable()->after('start_date');
            $table->jsonb('custom_fields')->nullable()->after('end_date');
        });

        // Project Modules - custom_fields ekle
        Schema::table('project_modules', function (Blueprint $table) {
            $table->jsonb('custom_fields')->nullable()->after('order');
        });

        // Project Tasks - custom_fields ekle
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->jsonb('custom_fields')->nullable()->after('order');
        });
    }

    public function down(): void
    {
        Schema::table('project_phases', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'custom_fields']);
        });

        Schema::table('project_modules', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });

        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
    }
};
