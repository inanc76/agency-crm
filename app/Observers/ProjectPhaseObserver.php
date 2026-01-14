<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Project;
use App\Models\ProjectPhase;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 📊 ProjectPhaseObserver - Faz Limit Kontrolü
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Bir proje altında maksimum 20 faz oluşturulabilir.
 *
 * @version Constitution V10
 * ═══════════════════════════════════════════════════════════════════════════
 */
class ProjectPhaseObserver
{
    /**
     * Faz oluşturulmadan önce - Max 20 limit kontrolü
     */
    public function creating(ProjectPhase $phase): bool
    {
        $project = Project::find($phase->project_id);

        if ($project && $project->phases()->count() >= 20) {
            throw new \RuntimeException(
                'Bir proje altında maksimum 20 faz oluşturulabilir.'
            );
        }

        return true;
    }
}
