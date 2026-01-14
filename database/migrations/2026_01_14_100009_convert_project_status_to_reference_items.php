<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🔄 Proje Durum Sistemini ReferenceData'ya Bağla
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * - status kolonlarını status_id (FK) olarak değiştir
 * - ReferenceCategory ve ReferenceItem kayıtlarını oluştur
 *
 * @version Constitution V10
 * ═══════════════════════════════════════════════════════════════════════════
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // 1. REFERENCE CATEGORIES OLUŞTUR
        // ─────────────────────────────────────────────────────────────────────
        $categories = [
            [
                'id' => Str::uuid()->toString(),
                'key' => 'PROJECT_STATUS',
                'name' => 'Proje Durumları',
                'description' => 'Projelerin genel durumlarını belirler',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'key' => 'PHASE_STATUS',
                'name' => 'Faz Durumları',
                'description' => 'Proje fazlarının durumlarını belirler',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'key' => 'MODULE_STATUS',
                'name' => 'Modül Durumları',
                'description' => 'Proje modüllerinin durumlarını belirler',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            DB::table('reference_categories')->updateOrInsert(
                ['key' => $category['key']],
                $category
            );
        }

        // ─────────────────────────────────────────────────────────────────────
        // 2. PROJECT_STATUS ITEMS
        // ─────────────────────────────────────────────────────────────────────
        $projectStatuses = [
            ['key' => 'project_draft', 'display_label' => 'Taslak', 'sort_order' => 1, 'is_default' => true],
            ['key' => 'project_active', 'display_label' => 'Aktif', 'sort_order' => 2, 'is_default' => false],
            ['key' => 'project_on_hold', 'display_label' => 'Beklemede', 'sort_order' => 3, 'is_default' => false],
            ['key' => 'project_completed', 'display_label' => 'Tamamlandı', 'sort_order' => 4, 'is_default' => false],
            ['key' => 'project_cancelled', 'display_label' => 'İptal Edildi', 'sort_order' => 5, 'is_default' => false],
        ];

        foreach ($projectStatuses as $item) {
            DB::table('reference_items')->updateOrInsert(
                ['category_key' => 'PROJECT_STATUS', 'key' => $item['key']],
                array_merge($item, [
                    'id' => Str::uuid()->toString(),
                    'category_key' => 'PROJECT_STATUS',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // ─────────────────────────────────────────────────────────────────────
        // 3. PHASE_STATUS ITEMS
        // ─────────────────────────────────────────────────────────────────────
        $phaseStatuses = [
            ['key' => 'phase_planned', 'display_label' => 'Planlandı', 'sort_order' => 1, 'is_default' => true],
            ['key' => 'phase_in_progress', 'display_label' => 'Devam Ediyor', 'sort_order' => 2, 'is_default' => false],
            ['key' => 'phase_completed', 'display_label' => 'Tamamlandı', 'sort_order' => 3, 'is_default' => false],
        ];

        foreach ($phaseStatuses as $item) {
            DB::table('reference_items')->updateOrInsert(
                ['category_key' => 'PHASE_STATUS', 'key' => $item['key']],
                array_merge($item, [
                    'id' => Str::uuid()->toString(),
                    'category_key' => 'PHASE_STATUS',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // ─────────────────────────────────────────────────────────────────────
        // 4. MODULE_STATUS ITEMS
        // ─────────────────────────────────────────────────────────────────────
        $moduleStatuses = [
            ['key' => 'module_planned', 'display_label' => 'Planlandı', 'sort_order' => 1, 'is_default' => true],
            ['key' => 'module_in_progress', 'display_label' => 'Devam Ediyor', 'sort_order' => 2, 'is_default' => false],
            ['key' => 'module_paused', 'display_label' => 'Durduruldu', 'sort_order' => 3, 'is_default' => false],
            ['key' => 'module_completed', 'display_label' => 'Tamamlandı', 'sort_order' => 4, 'is_default' => false],
            ['key' => 'module_cancelled', 'display_label' => 'İptal Edildi', 'sort_order' => 5, 'is_default' => false],
        ];

        foreach ($moduleStatuses as $item) {
            DB::table('reference_items')->updateOrInsert(
                ['category_key' => 'MODULE_STATUS', 'key' => $item['key']],
                array_merge($item, [
                    'id' => Str::uuid()->toString(),
                    'category_key' => 'MODULE_STATUS',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // ─────────────────────────────────────────────────────────────────────
        // 5. TABLO YAPISINI DEĞİŞTİR: status → status_id (FK)
        // ─────────────────────────────────────────────────────────────────────

        // Projects tablosu
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignUuid('status_id')->nullable()->after('timezone')
                ->constrained('reference_items')->nullOnDelete();
        });

        // Project Phases tablosu
        Schema::table('project_phases', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('project_phases', function (Blueprint $table) {
            $table->foreignUuid('status_id')->nullable()->after('order')
                ->constrained('reference_items')->nullOnDelete();
        });

        // Project Modules tablosu
        Schema::table('project_modules', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('project_modules', function (Blueprint $table) {
            $table->foreignUuid('status_id')->nullable()->after('order')
                ->constrained('reference_items')->nullOnDelete();
        });

        // Project Tasks tablosu
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->foreignUuid('status_id')->nullable()->after('order')
                ->constrained('reference_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Geri al: status_id → status
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_id');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('timezone');
        });

        Schema::table('project_phases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_id');
        });
        Schema::table('project_phases', function (Blueprint $table) {
            $table->string('status')->default('planned')->after('order');
        });

        Schema::table('project_modules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_id');
        });
        Schema::table('project_modules', function (Blueprint $table) {
            $table->string('status')->default('planned')->after('order');
        });

        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_id');
        });
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('order');
        });

        // Reference kayıtlarını sil
        DB::table('reference_items')->whereIn('category_key', ['PROJECT_STATUS', 'PHASE_STATUS', 'MODULE_STATUS'])->delete();
        DB::table('reference_categories')->whereIn('key', ['PROJECT_STATUS', 'PHASE_STATUS', 'MODULE_STATUS'])->delete();
    }
};
