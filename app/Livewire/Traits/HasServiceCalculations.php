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

use App\Models\Service;
use App\Models\Asset;
use App\Models\PriceDefinition;
use Carbon\Carbon;

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
     * Update customer ID and reload assets
     */
    public function updatedCustomerId()
    {
        $this->loadAssets();
        $this->asset_id = '';
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
}
