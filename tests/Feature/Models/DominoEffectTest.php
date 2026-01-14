<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\ProjectPhase;
use App\Models\ReferenceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🎯 Domino Effect Tests - ReferenceData Entegrasyonu
 * ═══════════════════════════════════════════════════════════════════════════
 */
describe('Domino Effect - Status Propagation', function () {

    it('sets phase to IN_PROGRESS when module becomes IN_PROGRESS', function () {
        $phase = ProjectPhase::factory()->create();
        $module = ProjectModule::factory()->create(['phase_id' => $phase->id]);

        // Modülü IN_PROGRESS yap
        $inProgressId = ReferenceItem::where('category_key', 'MODULE_STATUS')
            ->where('key', 'module_in_progress')->value('id');
        $module->update(['status_id' => $inProgressId]);

        // Faz da IN_PROGRESS olmalı
        $phase->refresh();
        expect($phase->status_key)->toBe('phase_in_progress');
    });

    it('sets phase to COMPLETED when all modules are terminal', function () {
        $phase = ProjectPhase::factory()->inProgress()->create();

        // İlk modülü oluştur
        $module1 = ProjectModule::factory()->inProgress()->create(['phase_id' => $phase->id]);
        $module2 = ProjectModule::factory()->inProgress()->create(['phase_id' => $phase->id]);

        // Şimdi modülleri sırayla terminal duruma getir
        $completedId = ReferenceItem::where('category_key', 'MODULE_STATUS')
            ->where('key', 'module_completed')->value('id');
        $cancelledId = ReferenceItem::where('category_key', 'MODULE_STATUS')
            ->where('key', 'module_cancelled')->value('id');

        $module1->update(['status_id' => $completedId]);

        // Henüz tamamlanmamalı
        $phase->refresh();
        expect($phase->status_key)->toBe('phase_in_progress');

        // Son modülü de terminal yap
        $module2->update(['status_id' => $cancelledId]);

        // Artık tüm modüller terminal, Faz COMPLETED olmalı
        $phase->refresh();
        expect($phase->status_key)->toBe('phase_completed');
    });

    it('does not complete phase if any module is still active', function () {
        $phase = ProjectPhase::factory()->inProgress()->create();

        $module1 = ProjectModule::factory()->completed()->create(['phase_id' => $phase->id]);
        $module2 = ProjectModule::factory()->inProgress()->create(['phase_id' => $phase->id]);

        $phase->refresh();
        expect($phase->status_key)->toBe('phase_in_progress');
    });
});

describe('Date Synchronization', function () {

    it('syncs phase dates from module dates', function () {
        $phase = ProjectPhase::factory()->create();

        ProjectModule::factory()->create([
            'phase_id' => $phase->id,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-15',
        ]);

        ProjectModule::factory()->create([
            'phase_id' => $phase->id,
            'start_date' => '2026-01-15',
            'end_date' => '2026-04-01',
        ]);

        $phase->refresh();

        // En erken başlangıç: 2026-01-15
        expect($phase->start_date->format('Y-m-d'))->toBe('2026-01-15');
        // En geç bitiş: 2026-04-01
        expect($phase->end_date->format('Y-m-d'))->toBe('2026-04-01');
    });
});

describe('Validation Limits', function () {

    it('throws exception when creating more than 20 phases', function () {
        $project = Project::factory()->create();

        // 20 faz oluştur
        ProjectPhase::factory()->count(20)->create(['project_id' => $project->id]);

        // 21. faz hata vermeli
        expect(fn () => ProjectPhase::factory()->create(['project_id' => $project->id]))
            ->toThrow(\RuntimeException::class, 'Bir proje altında maksimum 20 faz oluşturulabilir.');
    });

    it('throws exception when creating more than 50 modules', function () {
        $phase = ProjectPhase::factory()->create();

        // 50 modül oluştur
        ProjectModule::factory()->count(50)->create(['phase_id' => $phase->id]);

        // 51. modül hata vermeli
        expect(fn () => ProjectModule::factory()->create(['phase_id' => $phase->id]))
            ->toThrow(\RuntimeException::class, 'Bir faz altında maksimum 50 modül oluşturulabilir.');
    });
});

describe('ReferenceData Integration', function () {

    it('assigns default status on project creation', function () {
        $project = Project::factory()->create();

        expect($project->status)->not->toBeNull();
        expect($project->status->is_default)->toBeTrue();
    });

    it('assigns default status on phase creation', function () {
        $phase = ProjectPhase::factory()->create();

        expect($phase->status)->not->toBeNull();
        expect($phase->status->is_default)->toBeTrue();
    });

    it('assigns default status on module creation', function () {
        $module = ProjectModule::factory()->create();

        expect($module->status)->not->toBeNull();
        expect($module->status->is_default)->toBeTrue();
    });

    it('reference categories exist for projects', function () {
        expect(ReferenceItem::where('category_key', 'PROJECT_STATUS')->count())->toBe(5);
        expect(ReferenceItem::where('category_key', 'PHASE_STATUS')->count())->toBe(3);
        expect(ReferenceItem::where('category_key', 'MODULE_STATUS')->count())->toBe(5);
    });
});
