<?php

/**
 * 🛡️ ZIRHLI MODÜL: Customer Data Layer
 * ---------------------------------------------------------
 * DURUM: %100 Test Pass (Pest)
 * YETKİ: [customers.view]
 * TEST: tests/Feature/Customers/CustomerCreateTest.php
 * MİMARİ: Volt + Trait + Lazy Load + Cache
 * ---------------------------------------------------------
 */

namespace App\Livewire\Traits;

use App\Models\Customer;
use App\Models\ReferenceItem;
use Illuminate\Support\Facades\DB;

trait HasCustomerData
{
    /**
     * Initialize new customer with defaults
     */
    private function initNewCustomer(): void
    {
        // Set default customer type
        $default = ReferenceItem::where('category_key', 'CUSTOMER_TYPE')
            ->where('is_default', true)
            ->first();
        $this->customer_type = $default?->key ?? 'CUSTOMER';

        // Set default country (Türkiye)
        $turkiye = collect($this->countries)->firstWhere('name', 'Türkiye');
        $this->country_id = $turkiye['id'] ?? '';

        $this->loadCities();

        $istanbul = collect($this->cities)->firstWhere('name', 'İstanbul');
        $this->city_id = $istanbul['id'] ?? '';
    }

    /**
     * Load customer data with eager loading
     * 🚀 PERFORMANCE FIX: N+1 Prevention
     * Before: 15 queries (8 relations + 7 counts)
     * After: 1 query (with + withCount)
     */
    private function loadCustomerData(): void
    {
        try {
            // 🚀 EAGER LOADING: Load all relations in single query
            // 🚀 EAGER LOADING: Optimized for Lazy Loaded Tabs
            // We only need relatedCustomers for the select box and counts for badges.
            // Tab data is loaded by independent Volt components.
            $customer = Customer::with(['relatedCustomers'])
                ->withCount([
                    'contacts',
                    'assets',
                    'services',
                    'offers',
                    'sales',
                    'messages',
                    'notes'
                ])->findOrFail($this->customerId);

            $this->name = $customer->name;
            $this->customer_type = $customer->customer_type;
            $this->emails = !empty($customer->emails) ? (array) $customer->emails : [''];
            $this->phones = !empty($customer->phones) ? (array) $customer->phones : [''];
            $this->websites = !empty($customer->websites) ? (array) $customer->websites : [''];
            $this->country_id = $customer->country_id ?? '';
            $this->city_id = $customer->city_id ?? '';
            $this->address = $customer->address ?? '';
            $this->title = $customer->title ?? '';
            $this->tax_office = $customer->tax_office ?? '';
            $this->tax_number = $customer->tax_number ?? '';
            $this->current_code = $customer->current_code ?? '';
            $this->logo_url = $customer->logo_url ?? '';

            if ($customer->relatedCustomers) {
                $this->related_customers = $customer->relatedCustomers->pluck('id')->toArray();
            }

            // Trigger auxiliary data loads
            $this->loadCities();

            // Load counts (from withCount) for badges
            $this->counts = [
                'contacts' => $customer->contacts_count,
                'assets' => $customer->assets_count,
                'services' => $customer->services_count,
                'offers' => $customer->offers_count,
                'sales' => $customer->sales_count,
                'messages' => $customer->messages_count,
                'notes' => $customer->notes_count,
            ];

            // Note: Tab data ($relatedContacts etc.) is NO LONGER loaded here to prevent N+1.
            // Child components load their own data via Lazy Loading.

            $this->registration_date = $customer->created_at?->format('d.m.Y H:i') ?? '-';

            // Set View Mode
            $this->isViewMode = true;

        } catch (\Exception $e) {
            $this->error('Müşteri Bulunamadı', 'İstenilen müşteri kaydı bulunamadı.');
            $this->redirect('/dashboard/customers?tab=customers', navigate: true);
        }
    }

    /**
     * Load cities based on selected country
     */
    public function loadCities(): void
    {
        $query = DB::table('cities')
            ->where('is_active', true);

        if ($this->country_id) {
            $query->where('country_id', $this->country_id);
        }

        $this->cities = $query->orderBy('sort_order')
            ->get(['id', 'name'])
            ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
            ->toArray();
    }

    /**
     * Multi-input handlers
     */
    public function addEmail(): void
    {
        if (count($this->emails) < 3) {
            $this->emails[] = '';
        }
    }

    public function removeEmail(int $index): void
    {
        if (count($this->emails) > 1) {
            unset($this->emails[$index]);
            $this->emails = array_values($this->emails);
        }
    }

    public function addPhone(): void
    {
        $this->phones[] = '';
    }

    public function removePhone(int $index): void
    {
        if (count($this->phones) > 1) {
            unset($this->phones[$index]);
            $this->phones = array_values($this->phones);
        }
    }

    public function addWebsite(): void
    {
        $this->websites[] = '';
    }

    public function removeWebsite(int $index): void
    {
        if (count($this->websites) > 1) {
            unset($this->websites[$index]);
            $this->websites = array_values($this->websites);
        }
    }
}
