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
    public string $logo_url = '';

    // State Management
    public bool $isViewMode = false;
    public ?string $customerId = null;
    public string $registration_date = '';
    public string $activeTab = 'info';
    public array $counts = [
        'contacts' => 0,
        'assets' => 0,
        'services' => 0,
        'offers' => 0,
        'sales' => 0,
        'messages' => 0,
        'notes' => 0,
    ];

    // Related Data for Tabs
    public $relatedContacts = [];
    public $relatedAssets = [];
    public $relatedServices = [];
    public $relatedOffers = [];
    public $relatedSales = [];
    public $relatedMessages = [];
    public $relatedNotes = [];

    // Tab Filters
    public string $servicesStatusFilter = '';
    public string $offersStatusFilter = '';

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
            $this->logo_url = $customer->logo_url ?? '';

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

            // Load related data for tabs
            $this->relatedContacts = $customer->contacts()->orderBy('name')->get()->toArray();
            $this->relatedAssets = $customer->assets()->orderBy('name')->get()->toArray();
            $this->relatedServices = $customer->services()->orderBy('created_at', 'desc')->get()->toArray();
            $this->relatedOffers = $customer->offers()->orderBy('created_at', 'desc')->get()->toArray();
            $this->relatedSales = $customer->sales()->orderBy('created_at', 'desc')->get()->toArray();
            $this->relatedMessages = $customer->messages()->orderBy('created_at', 'desc')->get()->toArray();
            $this->relatedNotes = $customer->notes()->orderBy('created_at', 'desc')->get()->toArray();

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

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=customers"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Müşteri Listesi</span>
        </a>

        {{-- Header with Action Buttons --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
                    @if($isViewMode)
                        {{ $name ?: 'Müşteri Bilgileri' }}
                    @else
                        Yeni Müşteri Ekle
                    @endif
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($isViewMode)
                        <span
                            class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--dropdown-hover-bg)] text-[var(--color-text-base)] border border-[var(--card-border)]">Müşteri</span>
                        <span class="text-[11px] font-mono text-[var(--color-text-muted)]">ID: {{ $customerId }}</span>
                    @else
                        <p class="text-sm opacity-60 text-skin-base">
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
            <div class="flex items-center border-b border-[var(--card-border)] mb-8 overflow-x-auto scrollbar-hide">
                <button wire:click="$set('activeTab', 'info')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'info' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
                    Müşteri Bilgileri
                </button>
                <button wire:click="$set('activeTab', 'contacts')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'contacts' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
                    Kişiler ({{ $counts['contacts'] }})
                </button>
                <button wire:click="$set('activeTab', 'assets')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'assets' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
                    Varlıklar ({{ $counts['assets'] }})
                </button>
                <button wire:click="$set('activeTab', 'services')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'services' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
                    Hizmetler ({{ $counts['services'] }})
                </button>
                <button wire:click="$set('activeTab', 'offers')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'offers' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
                    Teklifler ({{ $counts['offers'] }})
                </button>
                <button wire:click="$set('activeTab', 'sales')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'sales' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
                    Satışlar ({{ $counts['sales'] }})
                </button>
                <button wire:click="$set('activeTab', 'messages')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'messages' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
                    Mesajlar ({{ $counts['messages'] }})
                </button>
                <button wire:click="$set('activeTab', 'notes')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors {{ $activeTab === 'notes' ? 'border-[var(--active-tab-color)] text-skin-heading' : 'border-transparent text-skin-base opacity-60' }}">
                    Notlar ({{ $counts['notes'] }})
                </button>
            </div>
        @else
            <div class="mb-8"></div>
        @endif

        {{-- Main Layout: 80% Left, 20% Right --}}
        <div class="flex gap-6">
            {{-- Left Column (80%) --}}
            <div class="w-4/5">
                {{-- Info Tab --}}
                @if($activeTab === 'info' || !$isViewMode)
                    <div class="space-y-6">
                        @include('livewire.customers.parts.basic-info-card')
                        @include('livewire.customers.parts.address-card')
                        @include('livewire.customers.parts.financial-card')
                        @include('livewire.customers.parts.related-companies-card')

                        @if($isViewMode)
                            @include('livewire.customers.parts.registration-info-card')
                        @endif
                    </div>
                @endif

                {{-- Contacts Tab --}}
                @if($activeTab === 'contacts' && $isViewMode)
                    <div class="theme-card p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-base font-bold text-skin-heading">Kişiler</h2>
                            <a href="/dashboard/customers/contacts/create?customer={{ $customerId }}"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg border border-[var(--card-border)] bg-[var(--card-bg)] hover:bg-[var(--dropdown-hover-bg)] transition-colors text-skin-primary">
                                + Yeni Kişi
                            </a>
                        </div>
                        @if(count($relatedContacts) > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-[var(--card-border)]">
                                            <th class="text-left py-2 px-2 font-medium opacity-60">Ad Soyad</th>
                                            <th class="text-left py-2 px-2 font-medium opacity-60">Pozisyon</th>
                                            <th class="text-left py-2 px-2 font-medium opacity-60">Email</th>
                                            <th class="text-left py-2 px-2 font-medium opacity-60">Telefon</th>
                                            <th class="text-center py-2 px-2 font-medium opacity-60">Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($relatedContacts as $contact)
                                            <tr class="border-b border-[var(--card-border)]/50 hover:bg-[var(--dropdown-hover-bg)] cursor-pointer transition-colors"
                                                onclick="window.location.href='/dashboard/customers/contacts/{{ $contact['id'] }}'">
                                                <td class="py-3 px-2 font-medium">{{ $contact['name'] }}</td>
                                                <td class="py-3 px-2 opacity-70">{{ $contact['position'] ?? '-' }}</td>
                                                <td class="py-3 px-2 opacity-70">{{ $contact['emails'][0] ?? '-' }}</td>
                                                <td class="py-3 px-2 opacity-70">{{ $contact['phones'][0] ?? '-' }}</td>
                                                <td class="py-3 px-2 text-center">
                                                    <span
                                                        class="px-2 py-0.5 rounded text-xs font-medium {{ $contact['status'] === 'WORKING' ? 'bg-[var(--color-success)]/10 text-[var(--color-success)]' : 'bg-[var(--dropdown-hover-bg)] text-[var(--color-text-muted)]' }}">
                                                        {{ $contact['status'] === 'WORKING' ? 'Çalışıyor' : 'Ayrıldı' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-[var(--color-text-muted)]">
                                <x-mary-icon name="o-users" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                <p class="text-sm">Henüz kişi kaydı bulunmuyor</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Assets Tab --}}
                @if($activeTab === 'assets' && $isViewMode)
                    <div class="theme-card p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-base font-bold text-skin-heading">Varlıklar</h2>
                            <a href="/dashboard/customers/assets/create?customer={{ $customerId }}"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg border border-[var(--card-border)] bg-[var(--card-bg)] hover:bg-[var(--dropdown-hover-bg)] transition-colors text-skin-primary">
                                + Yeni Varlık
                            </a>
                        </div>
                        @if(count($relatedAssets) > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-[var(--card-border)]">
                                            <th class="text-left py-2 px-2 font-medium opacity-60">Varlık Adı</th>
                                            <th class="text-left py-2 px-2 font-medium opacity-60">Tür</th>
                                            <th class="text-left py-2 px-2 font-medium opacity-60">URL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($relatedAssets as $asset)
                                            <tr class="border-b border-[var(--card-border)]/50 hover:bg-[var(--dropdown-hover-bg)] cursor-pointer transition-colors"
                                                onclick="window.location.href='/dashboard/customers/assets/{{ $asset['id'] }}'">
                                                <td class="py-3 px-2 font-medium">{{ $asset['name'] }}</td>
                                                <td class="py-3 px-2 opacity-70">{{ $asset['type'] }}</td>
                                                <td class="py-3 px-2 opacity-70">
                                                    @if($asset['url'])
                                                        <a href="{{ $asset['url'] }}" target="_blank"
                                                            class="text-skin-primary hover:underline"
                                                            onclick="event.stopPropagation();">{{ Str::limit($asset['url'], 40) }}</a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-[var(--color-text-muted)]">
                                <x-mary-icon name="o-globe-alt" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                <p class="text-sm">Henüz varlık kaydı bulunmuyor</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Services Tab --}}
                @if($activeTab === 'services' && $isViewMode)
                    <div class="theme-card p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <h2 class="text-base font-bold text-skin-heading">Hizmetler</h2>
                                <select wire:model.live="servicesStatusFilter" class="select select-xs bg-[var(--card-bg)] border-[var(--card-border)]">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="ACTIVE">Aktif</option>
                                    <option value="PASSIVE">Pasif</option>
                                </select>
                            </div>
                            <a href="/dashboard/customers/services/create?customer={{ $customerId }}"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg border border-[var(--card-border)] bg-[var(--card-bg)] hover:bg-[var(--dropdown-hover-bg)] transition-colors text-skin-primary">
                                + Yeni Hizmet
                            </a>
                        </div>
                        @php
                            $filteredServices = collect($relatedServices)->when($servicesStatusFilter, function ($collection) {
                                return $collection->where('status', $this->servicesStatusFilter);
                            });
                        @endphp
                        @if($filteredServices->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-[var(--card-border)]">
                                            <th class="text-left py-2 px-2 font-medium opacity-60">Hizmet Adı</th>
                                            <th class="text-left py-2 px-2 font-medium opacity-60">Kategori</th>
                                            <th class="text-center py-2 px-2 font-medium opacity-60">Kalan Gün</th>
                                            <th class="text-center py-2 px-2 font-medium opacity-60">Bitiş</th>
                                            <th class="text-right py-2 px-2 font-medium opacity-60">Fiyat</th>
                                            <th class="text-center py-2 px-2 font-medium opacity-60">Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($filteredServices as $service)
                                            @php
                                                $endDate = \Carbon\Carbon::parse($service['end_date']);
                                                $daysLeft = now()->diffInDays($endDate, false);
                                            @endphp
                                            <tr class="border-b border-[var(--card-border)]/50 hover:bg-[var(--dropdown-hover-bg)] cursor-pointer transition-colors"
                                                onclick="window.location.href='/dashboard/customers/services/{{ $service['id'] }}'">
                                                <td class="py-3 px-2 font-medium">{{ $service['service_name'] }}</td>
                                                <td class="py-3 px-2 opacity-70">{{ $service['service_category'] ?? '-' }}</td>
                                                <td class="py-3 px-2 text-center">
                                                    @if($daysLeft < 0)
                                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)]">
                                                            {{ abs((int)$daysLeft) }} gün geçti
                                                        </span>
                                                    @elseif($daysLeft <= 30)
                                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)]">
                                                            {{ (int)$daysLeft }} gün
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-success)]/10 text-[var(--color-success)]">
                                                            {{ (int)$daysLeft }} gün
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-2 text-center opacity-70 text-xs font-mono">
                                                    {{ $endDate->format('d.m.Y') }}</td>
                                                <td class="py-3 px-2 text-right font-medium">
                                                    {{ number_format($service['service_price'], 2) }}
                                                    {{ $service['service_currency'] }}</td>
                                                <td class="py-3 px-2 text-center">
                                                    <span
                                                        class="px-2 py-0.5 rounded text-xs font-medium {{ $service['status'] === 'ACTIVE' ? 'bg-[var(--color-success)]/10 text-[var(--color-success)]' : 'bg-[var(--dropdown-hover-bg)] text-[var(--color-text-muted)]' }}">
                                                        {{ $service['status'] === 'ACTIVE' ? 'Aktif' : 'Pasif' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-[var(--color-text-muted)]">
                                <x-mary-icon name="o-cog-6-tooth" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                <p class="text-sm">{{ $servicesStatusFilter ? 'Filtreye uygun hizmet bulunamadı' : 'Henüz hizmet kaydı bulunmuyor' }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Offers Tab --}}
                @if($activeTab === 'offers' && $isViewMode)
                    <div class="theme-card p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <h2 class="text-base font-bold text-skin-heading">Teklifler</h2>
                                <select wire:model.live="offersStatusFilter" class="select select-xs bg-[var(--card-bg)] border-[var(--card-border)]">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="DRAFT">Taslak</option>
                                    <option value="SENT">Gönderildi</option>
                                    <option value="ACCEPTED">Kabul Edildi</option>
                                    <option value="REJECTED">Reddedildi</option>
                                </select>
                            </div>
                            <a href="/dashboard/customers/offers/create?customer={{ $customerId }}"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg border border-[var(--card-border)] bg-[var(--card-bg)] hover:bg-[var(--dropdown-hover-bg)] transition-colors text-skin-primary">
                                + Yeni Teklif
                            </a>
                        </div>
                        @php
                            $filteredOffers = collect($relatedOffers)->when($offersStatusFilter, function ($collection) {
                                return $collection->where('status', $this->offersStatusFilter);
                            });
                        @endphp
                        @if($filteredOffers->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-[var(--card-border)]">
                                            <th class="text-left py-2 px-2 font-medium opacity-60">Teklif Başlığı</th>
                                            <th class="text-center py-2 px-2 font-medium opacity-60">Tarih</th>
                                            <th class="text-center py-2 px-2 font-medium opacity-60">Kalan Gün</th>
                                            <th class="text-right py-2 px-2 font-medium opacity-60">Tutar</th>
                                            <th class="text-center py-2 px-2 font-medium opacity-60">Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($filteredOffers as $offer)
                                            @php
                                                $validUntil = \Carbon\Carbon::parse($offer['valid_until']);
                                                $daysLeft = now()->diffInDays($validUntil, false);
                                                $statusColors = [
                                                    'DRAFT' => 'bg-[var(--dropdown-hover-bg)] text-[var(--color-text-muted)]',
                                                    'SENT' => 'bg-[var(--brand-primary)]/10 text-[var(--brand-primary)]',
                                                    'ACCEPTED' => 'bg-[var(--color-success)]/10 text-[var(--color-success)]',
                                                    'REJECTED' => 'bg-[var(--color-danger)]/10 text-[var(--color-danger)]',
                                                ];
                                                $statusLabels = ['DRAFT' => 'Taslak', 'SENT' => 'Gönderildi', 'ACCEPTED' => 'Kabul', 'REJECTED' => 'Ret'];
                                            @endphp
                                            <tr class="border-b border-[var(--card-border)]/50 hover:bg-[var(--dropdown-hover-bg)] cursor-pointer transition-colors"
                                                onclick="window.location.href='/dashboard/customers/offers/{{ $offer['id'] }}'">
                                                <td class="py-3 px-2 font-medium">{{ $offer['title'] }}</td>
                                                <td class="py-3 px-2 text-center opacity-70 text-xs font-mono">
                                                    {{ \Carbon\Carbon::parse($offer['created_at'])->format('d.m.Y') }}</td>
                                                <td class="py-3 px-2 text-center">
                                                    @if($daysLeft < 0)
                                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)]">
                                                            {{ abs((int)$daysLeft) }} gün geçti
                                                        </span>
                                                    @elseif($daysLeft <= 7)
                                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)]">
                                                            {{ (int)$daysLeft }} gün
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-success)]/10 text-[var(--color-success)]">
                                                            {{ (int)$daysLeft }} gün
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-2 text-right font-medium">
                                                    {{ number_format($offer['total_amount'], 2) }} {{ $offer['currency'] }}</td>
                                                <td class="py-3 px-2 text-center">
                                                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$offer['status']] ?? 'bg-[var(--dropdown-hover-bg)] text-[var(--color-text-muted)]' }}">
                                                        {{ $statusLabels[$offer['status']] ?? $offer['status'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-[var(--color-text-muted)]">
                                <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                <p class="text-sm">{{ $offersStatusFilter ? 'Filtreye uygun teklif bulunamadı' : 'Henüz teklif kaydı bulunmuyor' }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Sales Tab --}}
                @if($activeTab === 'sales' && $isViewMode)
                    <div class="theme-card p-6 shadow-sm">
                        <h2 class="text-base font-bold mb-4 text-skin-heading">Satışlar</h2>
                        @if(count($relatedSales) > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-[var(--card-border)]">
                                            <th class="text-left py-2 px-2 font-medium opacity-60">Satış No</th>
                                            <th class="text-center py-2 px-2 font-medium opacity-60">Tarih</th>
                                            <th class="text-right py-2 px-2 font-medium opacity-60">Tutar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($relatedSales as $sale)
                                            <tr class="border-b border-[var(--card-border)]/50 hover:bg-[var(--dropdown-hover-bg)] transition-colors">
                                                <td class="py-3 px-2 font-medium">{{ $sale['number'] ?? $sale['id'] }}</td>
                                                <td class="py-3 px-2 text-center opacity-70 text-xs font-mono">
                                                    {{ \Carbon\Carbon::parse($sale['created_at'])->format('d.m.Y') }}</td>
                                                <td class="py-3 px-2 text-right font-medium">
                                                    {{ number_format($sale['total_amount'] ?? 0, 2) }}
                                                    {{ $sale['currency'] ?? 'TRY' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-[var(--color-text-muted)]">
                                <x-mary-icon name="o-banknotes" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                <p class="text-sm">Henüz satış kaydı bulunmuyor</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Messages Tab --}}
                @if($activeTab === 'messages' && $isViewMode)
                    <div class="theme-card p-6 shadow-sm">
                        <h2 class="text-base font-bold mb-4 text-skin-heading">Mesajlar</h2>
                        @if(count($relatedMessages) > 0)
                            <div class="space-y-3">
                                @foreach($relatedMessages as $message)
                                    <div class="p-3 border border-[var(--card-border)]/60 rounded-lg bg-[var(--card-bg)]/50">
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="text-xs font-mono opacity-50">{{ \Carbon\Carbon::parse($message['created_at'])->format('d.m.Y H:i') }}</span>
                                        </div>
                                        <p class="text-sm">{{ $message['content'] ?? $message['message'] ?? '-' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-[var(--color-text-muted)]">
                                <x-mary-icon name="o-chat-bubble-left-right" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                <p class="text-sm">Henüz mesaj bulunmuyor</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Notes Tab --}}
                @if($activeTab === 'notes' && $isViewMode)
                    <div class="theme-card p-6 shadow-sm">
                        <h2 class="text-base font-bold mb-4 text-skin-heading">Notlar</h2>
                        @if(count($relatedNotes) > 0)
                            <div class="space-y-3">
                                @foreach($relatedNotes as $note)
                                    <div class="p-3 border border-[var(--card-border)]/60 rounded-lg bg-[var(--card-bg)]/50">
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="text-xs font-mono opacity-50">{{ \Carbon\Carbon::parse($note['created_at'])->format('d.m.Y H:i') }}</span>
                                        </div>
                                        <p class="text-sm">{{ $note['content'] ?? $note['note'] ?? '-' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-[var(--color-text-muted)]">
                                <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                <p class="text-sm">Henüz not bulunmuyor</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Right Column (20%) --}}
            <div class="w-1/5">
                @include('livewire.customers.parts.logo-card')
            </div>
        </div>
    </div>
</div>