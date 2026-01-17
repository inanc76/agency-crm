<?php

namespace App\Livewire\Customers\Services\Traits;

use App\Models\Customer;
use Carbon\Carbon;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasServiceDataLoader Trait                                                                ║
 * ║  🎯 ANA GÖREV: Hizmet verilerinin yüklenmesi ve başlatılma süreçleri                                            ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasServiceDataLoader
{
    /**
     * @purpose Livewire bileşeninin başlatılması
     */
    public function mount(?string $service = null): void
    {
        // Load Customers
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // Load Categories from ReferenceItem
        $this->categories = \App\Models\ReferenceItem::where('category_key', 'SERVICE_CATEGORY')
            ->orderBy('sort_order')
            ->get(['key', 'display_label'])
            ->map(fn($item) => ['key' => $item->key, 'label' => $item->display_label])
            ->toArray();

        // Load Service Statuses
        $this->serviceStatuses = \App\Models\ReferenceItem::where('category_key', 'SERVICE_STATUS')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key', 'metadata'])
            ->map(fn($i) => ['id' => $i->id, 'display_label' => $i->display_label, 'key' => $i->key, 'color_class' => $i->color_class])
            ->toArray();

        // Default start date
        $this->start_date = Carbon::now()->format('Y-m-d');

        if ($service) {
            $this->serviceId = $service;
            $this->loadServiceData();
            $this->activeTab = request()->query('tab', 'info');
        } else {
            $this->initNewService();
        }
    }

    protected function initNewService(): void
    {
        $customerId = request()->query('customer');
        if ($customerId && collect($this->customers)->firstWhere('id', $customerId)) {
            $this->customer_id = $customerId;
            $this->loadAssets();
            $this->loadProjects();
        }
        $this->addService();
    }

    public function addService(): void
    {
        if (count($this->services) < 5) {
            $this->services[] = [
                'category' => '',
                'service_name' => '',
                'price_definition_id' => '',
                'status' => !empty($this->serviceStatuses) ? $this->serviceStatuses[0]['key'] : 'ACTIVE',
                'service_price' => 0.0,
                'description' => '',
                'service_duration' => '',
                'service_currency' => 'TRY',
                'services_list' => [],
                'project_id' => '',
                'project_phase_id' => '',
                'phases_list' => [],
            ];
        }
    }

    public function removeService(int $index): void
    {
        if (count($this->services) > 1) {
            unset($this->services[$index]);
            $this->services = array_values($this->services);
        }
    }
}
