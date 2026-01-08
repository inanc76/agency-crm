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
    public string $registration_date = '';
    public array $counts = [
        'contacts' => 0,
        'assets' => 0,
        'services' => 0,
        'offers' => 0,
        'sales' => 0,
        'messages' => 0,
        'notes' => 0,
    ];

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

            if ($customer->relatedCustomers) {
                $this->related_customers = $customer->relatedCustomers->pluck('id')->toArray();
            }

            // Trigger auxiliary data loads
            $this->loadCities();

            // Load counts
            $this->counts = [
                'contacts' => $customer->contacts()->count(),
                'assets' => $customer->assets()->count(),
                'services' => $customer->services()->count(),
                'offers' => $customer->offers()->count(),
                'sales' => $customer->sales()->count(),
                'messages' => $customer->messages()->count(),
                'notes' => $customer->notes()->count(),
            ];

            $this->registration_date = $customer->created_at?->format('d.m.Y H:i') ?? '-';

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
        $parts = explode('.', $key);
        if (count($parts) === 2 && is_numeric($parts[1])) {
            $index = (int) $parts[1];
            $this->websites[$index] = $this->normalizeUrl($value);
        }
    }

    public function updatedPhones($value, $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) === 2 && is_numeric($parts[1])) {
            $index = (int) $parts[1];
            $this->phones[$index] = $this->normalizePhone($value);
        }
    }

    public function updatedName($value): void
    {
        $this->name = $this->formatTitleCase($value);
    }

    public function updatedTitle($value): void
    {
        $this->title = $this->formatTitleCase($value);
    }

    public function updatedTaxOffice($value): void
    {
        $this->tax_office = $this->formatTitleCase($value);
    }

    public function updatedAddress($value): void
    {
        $this->address = $this->formatTitleCase($value);
    }

    public function updatedCurrentCode($value): void
    {
        $this->current_code = $this->formatTitleCase($value);
    }

    // Normalize URL to https:// format
    private function normalizeUrl(?string $url): string
    {
        if (empty($url)) {
            return '';
        }

        $url = trim($url);
        // Check if protocol exists
        if (!preg_match('#^https?://#i', $url)) {
            return 'https://' . $url;
        }

        return $url;
    }

    // Convert to Title Case (e.g. deneme -> Deneme, DENEME -> Deneme)
    private function formatTitleCase(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        return Str::title(trim($text));
    }

    // Allow only numbers, + and spaces in phone
    private function normalizePhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        return preg_replace('/[^0-9+ ]/', '', $phone);
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

        // Filter and Normalize
        $emails = array_filter($this->emails, fn($e) => !empty($e));
        $phones = array_map(fn($p) => $this->normalizePhone($p), array_filter($this->phones, fn($p) => !empty($p)));
        $websites = array_map(fn($url) => $this->normalizeUrl($url), array_filter($this->websites, fn($w) => !empty($w)));

        $data = [
            'name' => $this->formatTitleCase($this->name),
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
            'title' => $this->formatTitleCase($this->title),
            'tax_office' => $this->formatTitleCase($this->tax_office),
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

    public function cancel(): void
    {
        if ($this->customerId) {
            $this->loadCustomerData();
        } else {
            $this->redirect('/dashboard/customers?tab=customers', navigate: true);
        }
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
                <h1 class="text-2xl font-bold tracking-tight" style="color: var(--color-text-heading);">
                    @if($isViewMode)
                        {{ $name ?: 'Müşteri Bilgileri' }}
                    @else
                        Yeni Müşteri Ekle
                    @endif
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($isViewMode)
                        <span
                            class="text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">Müşteri</span>
                        <span class="text-[11px] font-mono text-slate-400">ID: {{ $customerId }}</span>
                    @else
                        <p class="text-sm opacity-60" style="color: var(--color-text-base);">
                            Yeni müşteri bilgilerini girin
                        </p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($isViewMode)
                    {{-- View Mode Actions --}}
                    <button type="button" wire:click="delete" wire:confirm="Bu müşteriyi silmek istediğinize emin misiniz?"
                        wire:key="btn-delete-{{ $customerId }}"
                        class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                        Sil
                    </button>
                    <button type="button" wire:click="toggleEditMode" wire:key="btn-edit-{{ $customerId }}"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                        Düzenle
                    </button>
                @else
                    {{-- Edit Mode Actions --}}
                    <button type="button" wire:click="cancel" wire:key="btn-cancel-{{ $customerId ?: 'new' }}"
                        class="theme-btn-cancel">
                        İptal
                    </button>
                    <button type="button" wire:click="save" wire:loading.attr="disabled"
                        wire:key="btn-save-{{ $customerId ?: 'new' }}" class="theme-btn-save">
                        <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                        <x-mary-icon name="o-check" class="w-4 h-4" />
                        @if($customerId) Güncelle @else Kaydet @endif
                    </button>
                @endif
            </div>
        </div>

        {{-- Tab Navigation --}}
        @if($isViewMode)
            <div class="flex items-center border-b border-slate-200 mb-8 overflow-x-auto scrollbar-hide">
                <button class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap"
                    style="border-color: var(--active-tab-color); color: var(--color-text-heading);">
                    Müşteri Bilgileri
                </button>
                <button class="px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">
                    Kişiler ({{ $counts['contacts'] }})
                </button>
                <button class="px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">
                    Varlıklar ({{ $counts['assets'] }})
                </button>
                <button class="px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">
                    Hizmetler ({{ $counts['services'] }})
                </button>
                <button class="px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">
                    Teklifler ({{ $counts['offers'] }})
                </button>
                <button class="px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">
                    Satışlar ({{ $counts['sales'] }})
                </button>
                <button class="px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">
                    Mesajlar ({{ $counts['messages'] }})
                </button>
                <button class="px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 whitespace-nowrap">
                    Notlar ({{ $counts['notes'] }})
                </button>
            </div>
        @else
            <div class="mb-8"></div>
        @endif

        {{-- Main Layout: 80% Left, 20% Right --}}
        <div class="flex gap-6">
            {{-- Left Column (80%) --}}
            <div class="w-4/5 space-y-6">
                @include('livewire.customers.parts.basic-info-card')
                @include('livewire.customers.parts.address-card')
                @include('livewire.customers.parts.financial-card')
                @include('livewire.customers.parts.related-companies-card')

                @if($isViewMode)
                    @include('livewire.customers.parts.registration-info-card')
                @endif
            </div>

            {{-- Right Column (20%) --}}
            <div class="w-1/5">
                @include('livewire.customers.parts.logo-card')
            </div>
        </div>
    </div>
</div>