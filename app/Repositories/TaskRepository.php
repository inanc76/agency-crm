<?php

namespace App\Repositories;

use App\Models\ProjectTask;
use App\Models\ReferenceItem;
use Illuminate\Support\Collection;

class TaskRepository
{
    /**
     * Müşteriye ait devam eden (TASK_CONTINUES) görevleri getirir.
     */
    public function getInProgressTasksForCustomer(string $customerId): Collection
    {
        $statusId = ReferenceItem::where('key', 'TASK_CONTINUES')
            ->where('category_key', 'TASK_STATUS')
            ->value('id');

        return ProjectTask::query()
            ->join('projects', 'project_tasks.project_id', '=', 'projects.id')
            ->where('projects.customer_id', $customerId)
            ->where('project_tasks.status_id', $statusId)
            ->select('project_tasks.id', 'project_tasks.name')
            ->orderBy('project_tasks.name')
            ->get();
    }
}
