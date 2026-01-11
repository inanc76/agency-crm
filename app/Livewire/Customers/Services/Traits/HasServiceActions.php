<?php

namespace App\Livewire\Customers\Services\Traits;

use App\Models\Asset;
use App\Models\Customer;
use App\Models\PriceDefinition;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait HasServiceActions
{
    // Varlık Seçimi
    public string $customer_id = '';
    public string $asset_id = '';

    // Tarih (Shared)
    public ?string $start_date = null;

    // Services Array (Multiple Services)
    public array $services = [];

    // State Management
    public bool $isViewMode = false;
    public ?string $serviceId = null;
    public string $activeTab = 'info';

    // Reference Data
    public $customers = [];
    public $assets = [];
    public $categories = [];

    public function mount(?string $service = null): void
    {
        // Load Customers
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // Load Categories from PriceDefinition
        $this->categories = PriceDefinition::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->toArray();

        // Default start date
        $this->start_date = Carbon::now()->format('Y-m-d');

        // If service ID is provided, load data (edit mode)
        if ($service) {
            $this->serviceId = $service;
            $this->loadServiceData();

            // Set active tab from URL if present
            $this->activeTab = request()->query('tab', 'info');
        } else {
            // Check for customer query parameter
            $customerId = request()->query('customer');
            if ($customerId && collect($this->customers)->firstWhere('id', $customerId)) {
                $this->customer_id = $customerId;
                $this->loadAssets();
            }
            // Initialize with one empty service
            $this->addService();
        }
    }

    private function loadServiceData(): void
    {
        $service = Service::findOrFail($this->serviceId);

        $this->customer_id = $service->customer_id;
        $this->loadAssets();
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
            ]
        ];

        // Load services list for the category
        $this->loadServicesForIndex(0);

        $this->isViewMode = true;
    }

    public function addService(): void
    {
        if (count($this->services) < 5) {
            $this->services[] = [
                'category' => '',
                'service_name' => '',
                'price_definition_id' => '',
                'status' => 'ACTIVE',
                'service_price' => 0.0,
                'description' => '',
                'service_duration' => '',
                'service_currency' => 'TRY',
                'services_list' => [],
            ];
        }
    }

    public function removeService(int $index): void
    {
        if (count($this->services) > 1) {
            unset($this->services[$index]);
            $this->services = array_values($this->services); // Re-index
        }
    }

    // Dynamic Loaders
    public function updatedCustomerId()
    {
        $this->loadAssets();
        $this->asset_id = '';
    }

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

    public function updatedServices($value, $key)
    {
        // Parse key to get index and field
        // Format: "0.category" or "1.service_name"
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
            }
        }
    }

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

    public function save(): void
    {
        if ($this->serviceId) {
            $this->authorize('services.edit');
        } else {
            $this->authorize('services.create');
        }

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
            'services.*.status' => 'required|in:ACTIVE,PASSIVE,EXPIRED',
            'services.*.description' => 'nullable|string|max:1000',
        ]);

        $startDate = Carbon::parse($this->start_date);

        if ($this->serviceId) {
            // Edit mode - update single service
            $service = Service::findOrFail($this->serviceId);
            $endDate = $this->calculateEndDate($startDate, $this->services[0]['service_duration']);

            $service->update([
                'customer_id' => $this->customer_id,
                'asset_id' => $this->asset_id,
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

            $this->success('İşlem Başarılı', 'Hizmet bilgileri güncellendi.');
            $this->dispatch('service-saved');
            $this->isViewMode = true;
        } else {
            // Create mode - create multiple services
            DB::transaction(function () use ($startDate) {
                foreach ($this->services as $serviceData) {
                    $endDate = $this->calculateEndDate($startDate, $serviceData['service_duration']);

                    Service::create([
                        'id' => Str::uuid()->toString(),
                        'customer_id' => $this->customer_id,
                        'asset_id' => $this->asset_id,
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
                    ]);
                }
            });

            $count = count($this->services);
            $this->success('İşlem Başarılı', "{$count} adet hizmet başarıyla oluşturuldu.");
            $this->dispatch('service-saved');
            $this->redirect('/dashboard/customers?tab=services');
        }
    }

    private function calculateEndDate(Carbon $startDate, string $duration): Carbon
    {
        $endDate = $startDate->copy()->addYear(); // Default fallback

        if (str_contains(strtolower($duration), 'month')) {
            $endDate = $startDate->copy()->addMonth();
        }

        return $endDate;
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
            $service = Service::findOrFail($this->serviceId);
            $customer_id = $service->customer_id;
            $service->delete();
            $this->success('Hizmet Silindi', 'Hizmet kaydı başarıyla silindi.');
            $this->redirect('/dashboard/customers/' . $customer_id . '?tab=services');
        }
    }
}
