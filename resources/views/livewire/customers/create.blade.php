<?php
/**
 * 🚀 CUSTOMER CREATE/VIEW COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Single File Component)
 * TRAITS:
 *  - HasCustomerActions: Save, Delete, ToggleEdit logic.
 *  - HasCustomerData: Load, Init, Reference Data logic.
 *
 * HİYERARŞİ:
 *  - Ana Dosya: Layout, State ve Tab Routing yönetir.
 *  - Partials: Form kartları (Basic Info, Address, etc.) ve Sekme içerikleri (_tab-*.blade.php).
 * ---------------------------------------------------------
 */

use App\Livewire\Traits\HasCustomerActions;
use App\Livewire\Traits\HasCustomerData;
use App\Models\Customer;
use App\Models\ReferenceItem;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Müşteri Ekle'])]
    class extends Component {
    use HasCustomerActions, HasCustomerData, Toast, WithFileUploads;

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

    public string $created_by_name = '';

    public string $activeTab = 'info';

    public array $counts = [
        'contacts' => 0,
        'assets' => 0,
        'services' => 0,
        'offers' => 0,
        'sales' => 0,
        'messages' => 0,
        'notes' => 0,
        'projects' => 0,
    ];

    // Related Data for Tabs
    public $relatedContacts = [];

    public $relatedAssets = [];

    public $relatedServices = [];

    public $relatedOffers = [];

    public $relatedSales = [];

    public $relatedMessages = [];

    public $relatedNotes = [];

    public $relatedProjects = [];

    // Tab Filters
    public string $servicesStatusFilter = '';

    public string $offersStatusFilter = '';

    public string $projectsStatusFilter = '';

    // Reference Data
    public $customerTypes = [];

    public $countries = [];

    public $cities = [];

    public $existingCustomers = [];

    public $projectStatuses = [];

    public function mount(?string $customer = null): void
    {
        $this->customerTypes = ReferenceItem::where('category_key', 'CUSTOMER_TYPE')->where('is_active', true)
            ->orderBy('sort_order')->get()->map(fn($item) => ['id' => $item->key, 'name' => $item->display_label])->toArray();

        $this->countries = DB::table('countries')->where('is_active', true)->orderBy('sort_order')
            ->get(['id', 'name'])->map(fn($item) => ['id' => $item->id, 'name' => $item->name])->toArray();

        $this->existingCustomers = Customer::orderBy('name')->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray();

        if ($customer) {
            $this->authorize('customers.view');
            $this->customerId = $customer;
            $this->loadCustomerData();

            // Set active tab from URL if present
            $this->activeTab = request()->query('tab', 'info');
        } else {
            $this->authorize('customers.create');
            $this->initNewCustomer();
        }
    }

    private function loadCustomerData(): void
    {
        try {
            $customer = Customer::with('relatedCustomers')->findOrFail($this->customerId);
            $this->name = $customer->name;
            $this->customer_type = $customer->customer_type;
            $this->emails = (array) ($customer->emails ?: ['']);
            $this->phones = (array) ($customer->phones ?: ['']);
            $this->websites = (array) ($customer->websites ?: ['']);
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

            $this->loadCities();
            $this->counts = [
                'contacts' => $customer->contacts()->count(),
                'assets' => $customer->assets()->count(),
                'services' => $customer->services()->count(),
                'offers' => $customer->offers()->count(),
                'sales' => $customer->sales()->count(),
                'messages' => $customer->messages()->count(),
                'notes' => $customer->notes()->count(),
            ];

            $this->relatedContacts = $customer->contacts()->orderBy('name')->get()->toArray();
            $this->relatedAssets = $customer->assets()->orderBy('name')->get()->toArray();
            $this->relatedServices = $customer->services()->orderBy('created_at', 'desc')->get()->toArray();
            $this->relatedOffers = $customer->offers()->orderBy('created_at', 'desc')->get()->toArray();
            $this->relatedSales = $customer->sales()->orderBy('created_at', 'desc')->get()->toArray();
            $this->relatedMessages = $customer->messages()->orderBy('created_at', 'desc')->get()->toArray();
            $this->relatedNotes = $customer->notes()->orderBy('created_at', 'desc')->get()->toArray();

            // Load Projects
            $this->counts['projects'] = $customer->projects()->count();
            $this->relatedProjects = $customer->projects()
                ->with('status')
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();

            // Load Project Statuses for Filter
            $this->projectStatuses = ReferenceItem::where('category_key', 'PROJECT_STATUS')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'display_label', 'key', 'metadata'])
                ->toArray();

            $this->registration_date = $customer->created_at?->format('d.m.Y H:i') ?? '-';
            $this->created_by_name = $customer->creator?->name ?? 'Admin';
            $this->isViewMode = true;
        } catch (\Exception $e) {
            $this->error('Müşteri Bulunamadı', 'İstenilen müşteri kaydı bulunamadı.');
            $this->redirect('/dashboard/customers?tab=customers', navigate: true);
        }
    }

    // Lifecycle Hooks for formatting
    // Neden: Volt bileşeninde array tabanlı inputları dinlemek için key-value parçalanır.
    public function updatedWebsites($v, $k): void
    {
        $parts = explode('.', $k);
        if (count($parts) === 2) {
            $this->websites[(int) $parts[1]] = $this->normalizeUrl($v);
        }
    }

    public function updatedPhones($v, $k): void
    {
        $parts = explode('.', $k);
        if (count($parts) === 2) {
            $this->phones[(int) $parts[1]] = $this->normalizePhone($v);
        }
    }

    // Neden: Veri tabanı tutarlılığı için kullanıcı girişi anında Title Case formatına sokulur.
    public function updatedName($v): void
    {
        $this->name = $this->formatTitleCase($v);
    }

    public function updatedTitle($v): void
    {
        $this->title = $this->formatTitleCase($v);
    }

    public function updatedTaxOffice($v): void
    {
        $this->tax_office = $this->formatTitleCase($v);
    }

    public function updatedAddress($v): void
    {
        $this->address = $this->formatTitleCase($v);
    }

    public function updatedCurrentCode($v): void
    {
        $this->current_code = $this->formatTitleCase($v);
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        @include('livewire.customers.parts._create-header')


        {{-- Main Layout: 8/12 Left, 4/12 Right --}}
        <div class="grid grid-cols-12 gap-6">
            {{-- Left Column (8/12) --}}
            <div class="col-span-8">
                {{-- Info Tab --}}
                @if($activeTab === 'info' || !$isViewMode)
                    <div class="space-y-6">
                        @include('livewire.customers.parts.basic-info-card')
                        @include('livewire.customers.parts.address-card')
                        @include('livewire.customers.parts.financial-card')
                        @include('livewire.customers.parts.related-companies-card')

                    </div>
                @endif

                @if($activeTab === 'projects' && $isViewMode)
                    @include('livewire.customers.parts._tab-projects')
                @endif

                @if($activeTab === 'contacts' && $isViewMode)
                    @include('livewire.customers.parts._tab-contacts')
                @endif

                {{-- Assets Tab --}}
                @if($activeTab === 'assets' && $isViewMode)
                    @include('livewire.customers.parts._tab-assets')
                @endif

                {{-- Services Tab --}}
                @if($activeTab === 'services' && $isViewMode)
                    @include('livewire.customers.parts._tab-services')
                @endif

                {{-- Offers Tab --}}
                @if($activeTab === 'offers' && $isViewMode)
                    @include('livewire.customers.parts._tab-offers')
                @endif

                @if($activeTab === 'sales' && $isViewMode)
                    @include('livewire.customers.parts._tab-sales')
                @endif

                @if($activeTab === 'messages' && $isViewMode)
                    @include('livewire.customers.parts._tab-messages')
                @endif

                @if($activeTab === 'notes' && $isViewMode)
                    @include('livewire.customers.parts._tab-notes')
                @endif
            </div>

            {{-- Right Column (4/12) --}}
            <div class="col-span-4">
                @include('livewire.customers.parts.logo-card')
            </div>
        </div>
    </div>
</div>