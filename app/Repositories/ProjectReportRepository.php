<?php

namespace App\Repositories;

use App\Models\ProjectReport;
use Illuminate\Support\Facades\DB;

class ProjectReportRepository
{
    /**
     * Raporları toplu olarak kaydeder.
     */
    public function saveReports(array $data, array $reportLines): void
    {
        DB::transaction(function () use ($data, $reportLines) {
            foreach ($reportLines as $line) {
                ProjectReport::create([
                    'customer_id' => $data['customer_id'],
                    'report_type' => $data['report_type'],
                    'project_id' => $data['project_id'],
                    'service_id' => $data['service_id'] ?? null,
                    'task_id' => $data['task_id'] ?? null,
                    'hours' => $line['hours'],
                    'minutes' => $line['minutes'],
                    'content' => $line['content'],
                    'created_by' => auth()->id(),
                ]);
            }
        });
    }

    /**
     * Mevcut bir raporu günceller.
     */
    public function updateReport(ProjectReport $report, array $data, array $line): void
    {
        DB::transaction(function () use ($report, $data, $line) {
            $report->update([
                'customer_id' => $data['customer_id'],
                'report_type' => $data['report_type'],
                'project_id' => $data['project_id'],
                'service_id' => $data['service_id'] ?? null,
                'task_id' => $data['task_id'] ?? null,
                'hours' => $line['hours'],
                'minutes' => $line['minutes'],
                'content' => $line['content'],
            ]);
        });
    }
}
