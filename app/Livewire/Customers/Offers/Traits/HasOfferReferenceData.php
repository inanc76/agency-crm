<?php

namespace App\Livewire\Customers\Offers\Traits;

use App\Models\Customer;
use App\Models\PriceDefinition;
use App\Models\ReferenceItem;
use App\Models\Service;
use Carbon\Carbon;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasOfferReferenceData Trait                                                               ║
 * ║  🎯 ANA GÖREV: Teklif referans verilerinin (müşteriler, kategoriler, KDV, para birimi) yüklenmesi                ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasOfferReferenceData
{
    // Helper to get active currency label
    public function getCurrencyLabelProperty(): string
    {
        return collect($this->currencies)->firstWhere('id', $this->currency)['name'] ?? $this->currency;
    }
    // Reference Data Properties
    public $customers = [];
    public $customerServices = [];
    public $priceDefinitions = [];
    public $categories = [];
    public $vatRates = [];
    public $offerStatuses = [];
    public $currencies = [];

    /**
     * @purpose Referans verilerinin yüklenmesi (müşteriler, kategoriler, KDV oranları, para birimleri)
     */
    protected function initReferenceData(): void
    {
        // Load Customers
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // Load Categories with Display Labels
        $usedCategoryKeys = PriceDefinition::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->toArray();

        $categoryDefinitions = ReferenceItem::where('category_key', 'SERVICE_CATEGORY')
            ->whereIn('key', $usedCategoryKeys)
            ->get()
            ->keyBy('key');

        $this->categories = collect($usedCategoryKeys)->map(function ($key) use ($categoryDefinitions) {
            return [
                'id' => $key,
                'name' => $categoryDefinitions[$key]->display_label ?? $key,
            ];
        })->sortBy('name')->values()->toArray();

        // Load all price definitions
        $this->priceDefinitions = PriceDefinition::where('is_active', true)
            ->get()
            ->toArray();

        // Load VAT Rates
        $this->loadVatRates();

        // Load Offer Statuses
        $this->offerStatuses = ReferenceItem::where('category_key', 'OFFER_STATUS')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key', 'metadata'])
            ->map(fn($i) => [
                'id' => $i->id,
                'display_label' => $i->display_label,
                'key' => $i->key,
                'color_class' => $i->color_class
            ])
            ->toArray();

        // Load Currencies
        $this->currencies = ReferenceItem::where('category_key', 'CURRENCY')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key'])
            ->map(fn($i) => ['id' => $i->key, 'name' => $i->display_label])
            ->toArray();
    }

    /**
     * @purpose KDV oranlarını yükler ve varsayılanı ayarlar
     */
    protected function loadVatRates(): void
    {
        $this->vatRates = ReferenceItem::where('category_key', 'VAT_RATES')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($item) {
                $rate = 0;
                if (preg_match('/(\d+)/', $item->display_label, $matches)) {
                    $rate = (float) $matches[1];
                }
                return [
                    'rate' => $rate,
                    'label' => $item->display_label,
                ];
            })
            ->toArray();

        // Default VAT assignment
        if (empty($this->offerId)) {
            $defaultVat = ReferenceItem::where('category_key', 'VAT_RATES')
                ->where('is_active', true)
                ->where('is_default', true)
                ->first();

            if ($defaultVat && preg_match('/(\d+)/', $defaultVat->display_label, $matches)) {
                $this->vat_rate = (float) $matches[1];
            }
        }
    }

    /**
     * @purpose Seçili müşterinin aktif servislerini yükleme
     */
    protected function loadCustomerServices(): void
    {
        if ($this->customer_id) {
            $this->customerServices = Service::where('customer_id', $this->customer_id)
                ->where('status', 'ACTIVE')
                ->whereYear('start_date', $this->selectedYear ?? Carbon::now()->year)
                ->get(['id', 'service_name', 'service_category', 'service_price', 'service_duration', 'service_currency', 'description', 'start_date', 'end_date'])
                ->toArray();
        } else {
            $this->customerServices = [];
        }
    }
}
