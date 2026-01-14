<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ProjectModule;
use App\Models\ProjectPhase;
use App\Models\ReferenceItem;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🎯 ProjectModuleObserver - Domino Effect Otomasyonu (ReferenceData)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Bu observer, modül durumları değiştiğinde faz durumlarını otomatik günceller:
 *
 * 1. Dinamik Devam: Bir modül module_in_progress olursa → Faz phase_in_progress
 * 2. Dinamik Tamamlanma: Tüm modüller terminal → Faz phase_completed
 * 3. Tarih Senkronizasyonu: Modül tarihleri değişince Faz tarihleri güncellenir
 *
 * ⚠️ Artık ReferenceItem key'leri kullanılıyor, Enum değil!
 *
 * @version Constitution V10 - ReferenceData Entegrasyonu
 * ═══════════════════════════════════════════════════════════════════════════
 */
class ProjectModuleObserver
{
    /**
     * Modül oluşturulmadan önce - Max 50 limit kontrolü
     */
    public function creating(ProjectModule $module): bool
    {
        $phase = ProjectPhase::find($module->phase_id);

        if ($phase && $phase->modules()->count() >= 50) {
            throw new \RuntimeException(
                'Bir faz altında maksimum 50 modül oluşturulabilir.'
            );
        }

        return true;
    }

    /**
     * Modül oluşturulduktan sonra
     */
    public function created(ProjectModule $module): void
    {
        $this->syncPhaseDates($module);
    }

    /**
     * Modül güncellendikten sonra - Domino Effect tetiklenir
     */
    public function updated(ProjectModule $module): void
    {
        // Status değişti mi kontrol et
        if ($module->wasChanged('status_id')) {
            $this->triggerDominoEffect($module);
        }

        // Tarihler değişti mi kontrol et
        if ($module->wasChanged(['start_date', 'end_date'])) {
            $this->syncPhaseDates($module);
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════════════════
     * 🎲 DOMINO EFFECT - Durum Otomasyonu
     * ═══════════════════════════════════════════════════════════════════════
     */
    private function triggerDominoEffect(ProjectModule $module): void
    {
        $phase = $module->phase;
        if (! $phase) {
            return;
        }

        // Modülün status key'ini al (module_in_progress, module_completed, etc.)
        $moduleStatusKey = $module->status_key;

        // 1. Dinamik Devam: Herhangi bir modül IN_PROGRESS ise Faz da IN_PROGRESS
        if ($moduleStatusKey === 'module_in_progress') {
            $phaseInProgressId = $this->getStatusIdByKey('PHASE_STATUS', 'phase_in_progress');

            if ($phase->status_id !== $phaseInProgressId) {
                $phase->update(['status_id' => $phaseInProgressId]);
            }

            return;
        }

        // 2. Dinamik Tamamlanma: Tüm modüller terminal durumda mı?
        $this->checkPhaseCompletion($phase);
    }

    /**
     * Fazın tamamlanma durumunu kontrol et
     */
    private function checkPhaseCompletion(ProjectPhase $phase): void
    {
        // Modülleri status ilişkisiyle birlikte al
        $modules = $phase->modules()->with('status')->get();

        if ($modules->isEmpty()) {
            return;
        }

        // Tüm modüller terminal durumda mı? (completed veya cancelled)
        $allTerminal = $modules->every(function ($module) {
            return $module->isTerminal();
        });

        if ($allTerminal) {
            $phaseCompletedId = $this->getStatusIdByKey('PHASE_STATUS', 'phase_completed');

            if ($phase->status_id !== $phaseCompletedId) {
                $phase->update(['status_id' => $phaseCompletedId]);
            }
        }
    }

    /**
     * ReferenceItem'dan status ID al
     */
    private function getStatusIdByKey(string $categoryKey, string $itemKey): ?string
    {
        return ReferenceItem::where('category_key', $categoryKey)
            ->where('key', $itemKey)
            ->value('id');
    }

    /**
     * ═══════════════════════════════════════════════════════════════════════
     * 📅 TARİH SENKRONİZASYONU
     * ═══════════════════════════════════════════════════════════════════════
     *
     * Fazın start_date = En erken modül başlangıcı
     * Fazın end_date = En geç modül bitişi
     */
    private function syncPhaseDates(ProjectModule $module): void
    {
        $phase = $module->phase;
        if (! $phase) {
            return;
        }

        $modules = $phase->modules()->get();

        if ($modules->isEmpty()) {
            return;
        }

        // En erken başlangıç tarihi
        $earliestStart = $modules
            ->whereNotNull('start_date')
            ->min('start_date');

        // En geç bitiş tarihi
        $latestEnd = $modules
            ->whereNotNull('end_date')
            ->max('end_date');

        // Fazın tarihlerini sessizce güncelle (observer loop'u önlemek için)
        $phase->updateQuietly([
            'start_date' => $earliestStart,
            'end_date' => $latestEnd,
        ]);
    }
}
