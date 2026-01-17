<?php

namespace App\Livewire\Customers\Services\Traits;

use App\Livewire\Traits\HasServiceCalculations;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasServiceActions Trait (Execution & Persistence)                                          ║
 * ║  🎯 ANA GÖREV: Hizmetlerin kaydedilmesi, güncellenmesi ve silinmesi sürecini kontrol eder                       ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasServiceActions
{
    use HasServiceCalculations, HasServiceDataLoader;

    // State Fields
    public string $customer_id = '';
    public string $asset_id = '';
    public ?string $start_date = null;
    public array $services = [];
    public array $projectSummary = [];
    public bool $isViewMode = false;
    public ?string $serviceId = null;
    public string $activeTab = 'info';

    // Reference Data
    public $customers = [];
    public $assets = [];
    public $projects = [];
    public $categories = [];
    public $serviceStatuses = [];

    /**
     * @purpose Hizmet kaydetme (UPSERT)
     */
    public function save(): void
    {
        $this->authorize($this->serviceId ? 'services.edit' : 'services.create');

        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'asset_id' => 'required|exists:assets,id',
            'start_date' => 'required|date',
            'services' => 'required|array|min:1',
            'services.*.category' => 'required|string',
            'services.*.service_name' => 'required|string|max:200',
            'services.*.service_price' => 'required|numeric|min:0',
            'services.*.service_currency' => 'required|string|size:3',
            'services.*.service_duration' => 'required|string',
            'services.*.status' => 'required|string',
        ]);

        $startDate = Carbon::parse($this->start_date);

        if ($this->serviceId) {
            $this->updateSingleService($startDate);
        } else {
            $this->createMultipleServices($startDate);
        }
    }

    private function updateSingleService(Carbon $startDate): void
    {
        $service = Service::findOrFail($this->serviceId);
        $endDate = $this->calculateEndDate($startDate, $this->services[0]['service_duration']);

        $service->update([
            'customer_id' => $this->customer_id,
            'asset_id' => $this->asset_id,
            'project_id' => $this->services[0]['project_id'] ?: null,
            'project_phase_id' => $this->services[0]['project_phase_id'] ?: null,
            'price_definition_id' => $this->services[0]['price_definition_id'],
            'service_name' => $this->services[0]['service_name'],
            'service_category' => $this->services[0]['category'],
            'service_duration' => $this->services[0]['service_duration'],
            'service_price' => $this->services[0]['service_price'],
            'service_currency' => $this->services[0]['service_currency'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'description' => $this->services[0]['description'],
            'status' => $this->services[0]['status'],
            'is_active' => $this->services[0]['status'] === 'ACTIVE',
        ]);

        $this->success('Başarılı', 'Hizmet güncellendi.');
        $this->dispatch('service-saved');
        $this->isViewMode = true;
    }

    private function createMultipleServices(Carbon $startDate): void
    {
        DB::transaction(function () use ($startDate) {
            $servicesToInsert = [];

            foreach ($this->services as $serviceData) {
                $endDate = $this->calculateEndDate($startDate, $serviceData['service_duration']);

                $servicesToInsert[] = [
                    'id' => Str::uuid()->toString(),
                    'customer_id' => $this->customer_id,
                    'asset_id' => $this->asset_id,
                    'project_id' => ($serviceData['project_id'] ?? '') ?: null,
                    'project_phase_id' => ($serviceData['project_phase_id'] ?? '') ?: null,
                    'price_definition_id' => $serviceData['price_definition_id'],
                    'service_name' => $serviceData['service_name'],
                    'service_category' => $serviceData['category'],
                    'service_duration' => $serviceData['service_duration'],
                    'service_price' => $serviceData['service_price'],
                    'service_currency' => $serviceData['service_currency'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'description' => $serviceData['description'],
                    'status' => $serviceData['status'],
                    'is_active' => $serviceData['status'] === 'ACTIVE',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Service::insert($servicesToInsert);
        });

        $this->success('Başarılı', count($this->services) . ' hizmet oluşturuldu.');
        $this->dispatch('service-saved');
        $this->redirect('/dashboard/customers?tab=services');
    }

    private function calculateEndDate(Carbon $startDate, string $duration): Carbon
    {
        return str_contains(strtolower($duration), 'month')
            ? $startDate->copy()->addMonth()
            : $startDate->copy()->addYear();
    }

    public function cancel(): void
    {
        if ($this->serviceId) {
            $this->loadServiceData();
        } else {
            $this->redirect('/dashboard/customers?tab=services', navigate: true);
        }
    }

    public function toggleEditMode(): void
    {
        $this->authorize('services.edit');
        $this->isViewMode = false;
    }

    public function delete(): void
    {
        $this->authorize('services.delete');
        if ($this->serviceId) {
            Service::findOrFail($this->serviceId)->delete();
            $this->success('Silindi', 'Hizmet arşive taşındı.');
            $this->redirect('/dashboard/customers?tab=services');
        }
    }
}
