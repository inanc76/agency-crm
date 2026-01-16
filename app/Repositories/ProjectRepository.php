<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\ReferenceItem;
use Illuminate\Support\Collection;

class ProjectRepository
{
    /**
     * Müşteriye ait aktif ve seçilebilir projeleri getirir.
     */
    public function getSelectableProjectsForCustomer(string $customerId): Collection
    {
        $allowedStatuses = ReferenceItem::whereIn('key', [
            'project_active',
            'project_draft',
            'project_on_hold',
        ])->pluck('id');

        return Project::query()
            ->where('customer_id', $customerId)
            ->whereIn('status_id', $allowedStatuses)
            ->orderBy('name')
            ->select(['id', 'name', 'project_id_code'])
            ->get();
    }

    /**
     * ID ile projeyi getirir (ilişkileriyle beraber)
     */
    public function findById(string $id): ?Project
    {
        return Project::query()->select(['*'])->findOrFail($id);
    }
}
