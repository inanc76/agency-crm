<?php

namespace App\Livewire\Customers\Offers\Traits;

use App\Models\Customer;
use App\Models\Offer;
use App\Models\PriceDefinition;
use App\Models\ReferenceItem;
use App\Models\Service;
use Carbon\Carbon;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasOfferDataLoader Trait (Data Fetching & Initialization)                                 ║
 * ║  🎯 ANA GÖREV: Teklif verisi yükleme, referans verileri ve müşteri servisleri                                   ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • mount(): Bileşen başlatma ve URL parametrelerinden veri yükleme                                             ║
 * ║  • initReferenceData(): Müşteriler, kategoriler, KDV oranları                                                   ║
 * ║  • loadOfferData(): Mevcut teklif verilerinin DB'den yüklenmesi                                                 ║
 * ║  • loadCustomerServices(): Seçili müşterinin aktif servisleri                                                   ║
 * ║  • Property Watchers: updatedCustomerId, updatedSelectedYear                                                    ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK: Bu trait veri yükleme işlemleri yapar, yetki kontrolü gerektirmez                                ║
 * ║  📊 N+1 Prevention: Eager loading ile optimize edilmiş sorgular                                                 ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasOfferDataLoader
{
    // Offer Fields
    public $customer_id = '';

    public $number = '';

    public $title = '';

    public $status = 'DRAFT';

    public $description = '';

    public $valid_days = 30;

    public $valid_until = null;

    public $discount_value = 0;

    public $discount_type = 'AMOUNT'; // PERCENTAGE or AMOUNT

    public $vat_rate = 20;

    public $currency = 'USD';

    // State Management
    public $isViewMode = false;

    public $offerId = null;

    public string $activeTab = 'info';

    // Reference Data
    public $customers = [];

    public $customerServices = [];

    public $priceDefinitions = [];

    public $categories = [];

    public $vatRates = [];

    // Service Modal State
    public $showServiceModal = false;

    public $selectedYear = 0;

    /**
     * @purpose Livewire bileşeninin başlatılması ve başlangıç verilerinin yüklenmesi
     *
     * @param  string|null  $offer  Düzenlenecek teklif ID'si (opsiyonel)
     * @return void
     *              🔐 Security: Genel erişim - özel yetki kontrolü yok
     *              📢 Events: Sayfa yönlendirmesi yok, sadece veri yükleme
     *
     * State Dependencies: $this->customers, $this->customerServices, $this->offerId
     */
    public function mount(?string $offer = null): void
    {
        $this->initReferenceData();

        // Set default valid_until
        $this->valid_until = Carbon::now()->addDays($this->valid_days)->format('Y-m-d');
        $this->selectedYear = Carbon::now()->year;

        // If offer ID is provided, load data
        if ($offer) {
            $this->offerId = $offer;
            $this->loadOfferData();

            // Set active tab from URL if present
            $this->activeTab = request()->query('tab', 'info');
        } else {
            // New offer defaults
            $this->title = '';
            $this->sections = [
                [
                    'id' => null,
                    'title' => 'Teklif Bölümü - 1',
                    'description' => '',
                    'items' => [],
                ],
            ];
            // Check for customer query parameter
            $customerId = request()->query('customer');
            if ($customerId && collect($this->customers)->firstWhere('id', $customerId)) {
                $this->customer_id = $customerId;
                $this->loadCustomerServices();
            }
        }
    }

    /**
     * @purpose Referans verilerinin yüklenmesi (müşteriler, kategoriler, KDV oranları)
     *
     * @return void
     *              🔐 Security: Private metot - sadece trait içinden erişilebilir
     *              📢 Events: Veri yükleme işlemi, UI güncellemesi yok
     *
     * State Dependencies: $this->customers, $this->categories, $this->priceDefinitions, $this->vatRates
     */
    private function initReferenceData(): void
    {
        // Load Customers
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])
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

        // Ensure initial vat_rate is set from default variable if available
        $defaultVat = ReferenceItem::where('category_key', 'VAT_RATES')
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();

        if ($defaultVat && preg_match('/(\d+)/', $defaultVat->display_label, $matches)) {
            $this->vat_rate = (float) $matches[1];
        }
    }

    /**
     * @purpose Mevcut teklif verilerinin veritabanından yüklenmesi ve form alanlarına doldurulması
     *
     * @return void
     *              🔐 Security: Private metot - $this->offerId kontrolü ile güvenli erişim
     *              📢 Events: $this->isViewMode = true ile görüntüleme moduna geçiş
     *
     * State Dependencies: $this->offerId, $this->items, $this->attachments, $this->customer_id
     */
    private function loadOfferData(): void
    {
        $offer = Offer::with(['sections.items', 'attachments'])->findOrFail($this->offerId);

        $this->customer_id = $offer->customer_id;
        $this->number = $offer->number;
        $this->loadCustomerServices();

        $this->title = $offer->title ?? '';
        $this->status = $offer->status;
        $this->description = $offer->description ?? '';

        // Correct Discount Loading: If PERCENTAGE, use discount_percentage. If AMOUNT, use stored discounted_amount.
        if ($offer->discount_percentage > 0) {
            $this->discount_value = (float) $offer->discount_percentage;
            $this->discount_type = 'PERCENTAGE';
        } else {
            $this->discount_value = (float) $offer->discounted_amount;
            $this->discount_type = 'AMOUNT';
        }
        $this->vat_rate = (float) $offer->vat_rate;
        $this->currency = $offer->currency;
        $this->valid_until = Carbon::parse($offer->valid_until)->format('Y-m-d');

        $this->vat_rate = (float) $offer->vat_rate;
        $this->currency = $offer->currency;

        // Load sections and their items
        $this->sections = $offer->sections->map(fn ($section) => [
            'id' => $section->id,
            'title' => $section->title,
            'description' => $section->description,
            'items' => $section->items->map(fn ($item) => [
                'service_id' => $item->service_id,
                'service_name' => $item->service_name,
                'description' => $item->description,
                'price' => (float) $item->price,
                'currency' => $item->currency,
                'duration' => $item->duration,
                'quantity' => (int) $item->quantity,
            ])->toArray(),
        ])->toArray();

        // Fallback for old data or migration edge cases
        if (empty($this->sections)) {
            $this->sections = [
                [
                    'id' => null,
                    'title' => 'Teklif Bölümü - 1',
                    'description' => '',
                    'items' => $offer->items->map(fn ($item) => [
                        'service_id' => $item->service_id,
                        'service_name' => $item->service_name,
                        'description' => $item->description,
                        'price' => (float) $item->price,
                        'currency' => $item->currency,
                        'duration' => $item->duration,
                        'quantity' => (int) $item->quantity,
                    ])->toArray(),
                ],
            ];
        }

        // Load attachments
        $this->attachments = $offer->attachments->map(fn ($att) => [
            'id' => $att->id,
            'title' => $att->title,
            'description' => $att->description,
            'price' => $att->price,
            'currency' => $att->currency,
            'file_name' => $att->file_name,
            'file_type' => $att->file_type,
            'file_size' => $att->file_size,
            'file_path' => $att->file_path,
        ])->toArray();

        $this->isViewMode = true;
    }

    /**
     * @purpose Müşteri değiştiğinde otomatik servis listesi güncelleme
     *
     * @return void
     *              🔐 Security: Livewire property watcher - otomatik tetiklenir
     *              📢 Events: loadCustomerServices() çağrısı ile UI güncelleme
     *
     * State Dependencies: $this->customer_id, $this->customerServices
     */
    public function updatedCustomerId(): void
    {
        $this->loadCustomerServices();
    }

    /**
     * @purpose Seçili müşterinin aktif servislerini yıla göre filtreleyerek yükleme
     *
     * @return void
     *              🔐 Security: Private metot - customer_id kontrolü ile güvenli erişim
     *              📢 Events: $this->customerServices array'inin güncellenmesi
     *
     * State Dependencies: $this->customer_id, $this->selectedYear, $this->customerServices
     */
    private function loadCustomerServices(): void
    {
        if ($this->customer_id) {
            $this->customerServices = Service::where('customer_id', $this->customer_id)
                ->where('status', 'ACTIVE')
                ->whereYear('start_date', $this->selectedYear)
                ->get(['id', 'service_name', 'service_category', 'service_price', 'service_duration', 'service_currency', 'description', 'start_date', 'end_date'])
                ->toArray();
        } else {
            $this->customerServices = [];
        }
    }

    /**
     * @purpose Yıl değiştiğinde müşteri servislerini yeniden yükleme
     *
     * @return void
     *              🔐 Security: Livewire property watcher - otomatik tetiklenir
     *              📢 Events: loadCustomerServices() çağrısı ile UI güncelleme
     *
     * State Dependencies: $this->selectedYear, $this->customerServices
     */
    public function updatedSelectedYear(): void
    {
        $this->loadCustomerServices();
    }
}
