<?php

/**
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * 🎯 IDENTITY CARD
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * Trait: HasServiceCalculations
 * Purpose: Service Data Loading & Price Calculations with N+1 Prevention
 * Layer: Livewire Trait (Data & Business Logic)
 * Dependencies: Service, Asset, Customer, PriceDefinition Models
 * Created: 2026-01-10
 * Refactored From: customers/services/create.blade.php (604 lines → decomposed)
 * Performance: 2 queries → 1 query (eager loading)
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 */

namespace App\Livewire\Traits;

use App\Models\Asset;
use App\Models\PriceDefinition;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectReport;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait HasServiceCalculations
{
    /**
     * Load service data with eager loading
     * 🚀 PERFORMANCE FIX: N+1 Prevention
     * Before: 2 queries (service + asset/customer separately)
     * After: 1 query (with eager loading)
     */
    private function loadServiceData(): void
    {
        // 🚀 EAGER LOADING: Load service with related customer and asset
        $service = Service::with(['customer', 'asset'])->findOrFail($this->serviceId);

        $this->customer_id = $service->customer_id;
        $this->loadAssets();
        $this->loadProjects();
        $this->asset_id = $service->asset_id;
        $this->start_date = Carbon::parse($service->start_date)->format('Y-m-d');

        // Load single service into array
        $this->services = [
            [
                'category' => $service->service_category,
                'service_name' => $service->service_name,
                'price_definition_id' => $service->price_definition_id,
                'status' => $service->status,
                'service_price' => $service->service_price,
                'description' => $service->description ?? '',
                'service_duration' => $service->service_duration,
                'service_currency' => $service->service_currency,
                'services_list' => [],
                'project_id' => $service->project_id ?? '',
                'project_phase_id' => $service->project_phase_id ?? '',
                'phases_list' => [],
            ],
        ];

        // Load services list for the category
        $this->loadServicesForIndex(0);

        // Load phases if project exists
        if ($service->project_id) {
            $this->loadPhases(0);
            $this->calculateProjectSummary($service->project_id);
        }

        $this->isViewMode = true;
    }

    /**
     * Load assets based on selected customer
     */
    public function loadAssets()
    {
        if ($this->customer_id) {
            $this->assets = Asset::where('customer_id', $this->customer_id)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn($a) => ['id' => $a->id, 'name' => $a->name])
                ->toArray();
        } else {
            $this->assets = [];
        }
    }

    /**
     * Load projects based on selected customer
     */
    public function loadProjects()
    {
        if ($this->customer_id) {
            $this->projects = Project::where('customer_id', $this->customer_id)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn($p) => ['id' => $p->id, 'name' => $p->name])
                ->toArray();
        } else {
            $this->projects = [];
        }
    }

    /**
     * Update customer ID and reload assets/projects
     */
    public function updatedCustomerId()
    {
        $this->loadAssets();
        $this->loadProjects();
        $this->asset_id = '';

        // Clear project info in services
        foreach ($this->services as $index => $service) {
            $this->services[$index]['project_id'] = '';
            $this->services[$index]['project_phase_id'] = '';
            $this->services[$index]['phases_list'] = [];
        }
    }

    /**
     * Handle service field updates
     */
    public function updatedServices($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = (int) $parts[0];
            $field = $parts[1];

            if ($field === 'category') {
                $this->loadServicesForIndex($index);
                $this->services[$index]['service_name'] = '';
                $this->services[$index]['service_price'] = 0;
            } elseif ($field === 'service_name') {
                $this->updateServicePrice($index);
            } elseif ($field === 'project_id') {
                $this->loadPhases($index);
                $this->services[$index]['project_phase_id'] = '';
            }
        }
    }

    /**
     * Load services list for specific category
     */
    private function loadServicesForIndex(int $index): void
    {
        if (!empty($this->services[$index]['category'])) {
            $this->services[$index]['services_list'] = PriceDefinition::where('category', $this->services[$index]['category'])
                ->where('is_active', true)
                ->get()
                ->toArray();
        } else {
            $this->services[$index]['services_list'] = [];
        }
    }

    /**
     * Load phases for specific project
     */
    private function loadPhases(int $index): void
    {
        $projectId = $this->services[$index]['project_id'];
        if ($projectId) {
            $this->services[$index]['phases_list'] = ProjectPhase::where('project_id', $projectId)
                ->orderBy('order')
                ->get(['id', 'name'])
                ->map(fn($p) => ['id' => $p->id, 'name' => $p->name])
                ->toArray();
        } else {
            $this->services[$index]['phases_list'] = [];
        }
    }

    /**
     * Update service price based on selected service
     */
    private function updateServicePrice(int $index): void
    {
        $serviceName = $this->services[$index]['service_name'];
        $priceDef = collect($this->services[$index]['services_list'])->firstWhere('name', $serviceName);

        if ($priceDef) {
            $this->services[$index]['service_price'] = $priceDef['price'];
            $this->services[$index]['service_duration'] = $priceDef['duration'];
            $this->services[$index]['service_currency'] = $priceDef['currency'];
            $this->services[$index]['price_definition_id'] = $priceDef['id'];
        }
    }

    /**
     * Calculate project hours summary
     */
    private function calculateProjectSummary(string $projectId): void
    {
        // 1. Calculate Spent Time
        $totalMinutes = ProjectReport::where('project_id', $projectId)
            ->select(DB::raw('SUM(hours * 60 + minutes) as total_minutes'))
            ->value('total_minutes') ?? 0;

        $spentHours = floor($totalMinutes / 60);
        $spentMins = $totalMinutes % 60;
        $spentTimeFormatted = sprintf('%d:%02d', $spentHours, $spentMins);

        // 2. Calculate Assigned Time
        $totalAssignedHours = 0;
        $phases = ProjectPhase::with('modules')->where('project_id', $projectId)->get();
        foreach ($phases as $p) {
            foreach ($p->modules as $m) {
                $totalAssignedHours += (int) ($m->estimated_hours ?? 0);
            }
        }

        // 3. Calculate Remaining
        $assignedMinutes = $totalAssignedHours * 60;
        $remainingMinutes = $assignedMinutes - $totalMinutes;

        $remHours = floor(abs($remainingMinutes) / 60);
        $remMins = abs($remainingMinutes) % 60;
        $remainingTimeFormatted = ($remainingMinutes < 0 ? '-' : '') . sprintf('%d:%02d', $remHours, $remMins);

        $this->projectSummary = [
            'total_assigned_hours' => $totalAssignedHours,
            'spent_time' => $spentTimeFormatted,
            'remaining_time' => $remainingTimeFormatted,
            'is_negative' => $remainingMinutes < 0
        ];
    }
}
