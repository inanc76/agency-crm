<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Customer;
use App\Models\ReferenceItem;
use Mary\Traits\Toast;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Müşteri Ekle'])]
    class extends Component {
    use Toast;
    use WithFileUploads;

    // Temel Bilgiler
    public string $customer_type = 'CUSTOMER';
    public string $name = '';
    public array $emails = [''];
    public array $phones = [''];
    public array $websites = [''];

    // Adres Bilgileri
    public string $country_id = '';
    public string $city_id = '';
    public string $address = '';

    // Cari Bilgiler
    public string $title = '';
    public string $tax_office = '';
    public string $tax_number = '';
    public string $current_code = '';

    // İlişkili Firmalar
    public array $related_customers = [];

    // Logo
    public $logo;

    // State Management
    public bool $isViewMode = false;
    public ?string $customerId = null;

    // Reference Data
    public $customerTypes = [];
    public $countries = [];
    public $cities = [];
    public $existingCustomers = [];

    public function mount(?string $customer = null): void
    {
        // Load CUSTOMER_TYPE reference items
        $this->customerTypes = ReferenceItem::where('category_key', 'CUSTOMER_TYPE')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($item) => ['id' => $item->key, 'name' => $item->display_label])
            ->toArray();

        // Load COUNTRIES from countries table
        $this->countries = DB::table('countries')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name'])
            ->map(fn($item) => ['id' => $item->id, 'name' => $item->name])
            ->toArray();

        // Load existing customers for related companies
        $this->existingCustomers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // If customer ID is provided, load data
        if ($customer) {
            $this->customerId = $customer;
            $this->loadCustomerData();
        } else {
            // Default initialization for new customer
            $this->initNewCustomer();
        }
    }

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

    private function loadCustomerData(): void
    {
        try {
            $customer = Customer::with('relatedCustomers')->findOrFail($this->customerId);

            $this->name = $customer->name;
            $this->customer_type = $customer->customer_type;
            $this->emails = !empty($customer->emails) ? (array)$customer->emails : [''];
            $this->phones = !empty($customer->phones) ? (array)$customer->phones : [''];
            $this->websites = !empty($customer->websites) ? (array)$customer->websites : [''];
            $this->country_id = $customer->country_id ?? '';
            $this->city_id = $customer->city_id ?? '';
            $this->address = $customer->address ?? '';
            $this->title = $customer->title ?? '';
            $this->tax_office = $customer->tax_office ?? '';
            $this->tax_number = $customer->tax_number ?? '';
            $this->current_code = $customer->current_code ?? '';

            if ($customer->relatedCustomers) {
                $this->related_customers = $customer->relatedCustomers->pluck('id')->toArray();
            }

            // Trigger auxiliary data loads
            $this->loadCities();

            // Set View Mode
            $this->isViewMode = true;

        } catch (\Exception $e) {
            $this->error('Müşteri Bulunamadı', 'İstenilen müşteri kaydı bulunamadı.');
            $this->redirect('/dashboard/customers?tab=customers', navigate: true);
        }
    }

    public function addRelatedCustomer(string $customerId): void
    {
        if (!in_array($customerId, $this->related_customers) && count($this->related_customers) < 10) {
            $this->related_customers[] = $customerId;
        }
    }

    public function removeRelatedCustomer(string $customerId): void
    {
        $this->related_customers = array_values(
            array_filter($this->related_customers, fn($id) => $id !== $customerId)
        );
    }

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

    // Multi-input handlers
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

    public function updatedWebsites($value, $key): void
    {
        // Extract index from key (e.g. "websites.0" -> 0)
        $parts = explode('.', $key);
        if (count($parts) === 2 && is_numeric($parts[1])) {
            $index = (int) $parts[1];
            $this->websites[$index] = $this->normalizeUrl($value);
        }
    }

    // Normalize URL to https:// format
    private function normalizeUrl(string $url): string
    {
        if (empty($url)) {
            return $url;
        }

        // Check if protocol exists
        if (!preg_match('#^https?://#i', $url)) {
            return 'https://' . $url;
        }

        return $url;
    }

    public function save(): void
    {
        $this->validate([
            'customer_type' => 'required|string',
            'name' => 'required|string|max:255',
            'emails.*' => 'nullable|email',
            'phones.*' => 'nullable|string|max:50',
            'websites.*' => 'nullable|url',
            'country_id' => 'required|string',
            'city_id' => 'required|string',
            'address' => 'nullable|string|max:1000',
            'title' => 'nullable|string|max:255',
            'tax_office' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:20',
            'current_code' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:5120',
        ]);

        // Filter empty values
        $emails = array_filter($this->emails, fn($e) => !empty($e));
        $phones = array_filter($this->phones, fn($p) => !empty($p));
        $websites = array_filter($this->websites, fn($w) => !empty($w));

        // Normalize website URLs
        $websites = array_map(fn($url) => $this->normalizeUrl($url), $websites);

        $data = [
            'name' => $this->name,
            'customer_type' => $this->customer_type,
            'email' => $emails[0] ?? null,
            'emails' => array_values($emails),
            'phone' => $phones[0] ?? null,
            'phones' => array_values($phones),
            'website' => $websites[0] ?? null,
            'websites' => array_values($websites),
            'country_id' => $this->country_id ?: null,
            'city_id' => $this->city_id ?: null,
            'address' => $this->address ?: null,
            'title' => $this->title ?: null,
            'tax_office' => $this->tax_office ?: null,
            'tax_number' => $this->tax_number ?: null,
            'current_code' => $this->current_code ?: null,
        ];

        $wasCreating = empty($this->customerId);

        if ($this->customerId) {
            // Update existing
            $customer = Customer::find($this->customerId);
            $customer->update($data);
            $message = 'Müşteri bilgileri güncellendi.';
        } else {
            // Create new
            $this->customerId = Str::uuid()->toString();
            $data['id'] = $this->customerId;
            $customer = Customer::create($data);
            $message = 'Yeni müşteri başarıyla oluşturuldu.';
        }

        // Handle logo upload
        if ($this->logo) {
            $path = $this->logo->store('uploads/customer-logo', 'public');
            $customer->update(['logo_url' => '/' . $path]);
            $this->logo = null; // Reset upload input
        }

        // Attach related customers
        $customer->relatedCustomers()->sync($this->related_customers);

        $this->success('İşlem Başarılı', $message);

        if ($wasCreating) {
            $this->redirect('/dashboard/customers/' . $this->customerId, navigate: true);
        } else {
            $this->isViewMode = true;
        }
    }

    public function toggleEditMode(): void
    {
        $this->isViewMode = false;
    }

    public function delete(): void
    {
        if ($this->customerId) {
            Customer::findOrFail($this->customerId)->delete();
            $this->success('Müşteri Silindi', 'Müşteri kaydı başarıyla silindi.');
            $this->redirect('/dashboard/customers?tab=customers');
        }
    }


    public function createNew(): void
    {
        $this->redirect('/dashboard/customers/create', navigate: true);
    }
}; ?>

<div class="p-6 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=customers"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Müşteri Listesi</span>
        </a>

        {{-- Header with Action Buttons --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    @if($isViewMode)
                        Müşteri Bilgileri
                    @else
                        Yeni Müşteri Ekle
                    @endif
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    @if($isViewMode)
                        Kaydedilen müşteri bilgileri
                    @else
                        Yeni müşteri bilgilerini girin
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if($isViewMode)
                    {{-- View Mode Actions --}}
                    <button type="button" wire:click="delete" wire:confirm="Bu müşteriyi silmek istediğinize emin misiniz?"
                        class="btn btn-error text-white">
                        <x-mary-icon name="o-trash" class="w-4 h-4 mr-1" />
                        Sil
                    </button>
                    <button type="button" wire:click="toggleEditMode" class="btn btn-warning">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4 mr-1" />
                        Düzenle
                    </button>
                    <a href="/dashboard/customers/create" class="btn btn-outline" wire:navigate>
                        Yeni Ekle
                    </a>
                @else
                    {{-- Edit Mode Actions --}}
                    <a href="/dashboard/customers?tab=customers" class="btn-secondary">
                        İptal
                    </a>
                    <button type="button" wire:click="save" wire:loading.attr="disabled" class="btn-primary">
                        <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                        @if($customerId) Güncelle @else Kaydet @endif
                    </button>
                @endif
            </div>
        </div>

        {{-- Main Layout: 80% Left, 20% Right --}}
        <div class="flex gap-6">
            {{-- Left Column (80%) --}}
            <div class="w-4/5 space-y-6">
                @include('livewire.customers.parts.basic-info-card')
                @include('livewire.customers.parts.address-card')
                @include('livewire.customers.parts.financial-card')
                @include('livewire.customers.parts.related-companies-card')
            </div>

            {{-- Right Column (20%) --}}
            <div class="w-1/5">
                @include('livewire.customers.parts.logo-card')
            </div>
        </div>
    </div>
</div>