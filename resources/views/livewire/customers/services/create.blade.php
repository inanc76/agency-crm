<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Asset;
use App\Models\Service;
use App\Models\PriceDefinition;
use Mary\Traits\Toast;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Hizmet Ekle'])]
    class extends Component {
    use Toast;

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
        $this->validate([
            'customer_id' => 'required',
            'asset_id' => 'required',
            'start_date' => 'required|date',
            'services.*.category' => 'required',
            'services.*.service_name' => 'required',
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
        $this->isViewMode = false;
    }

    public function delete(): void
    {
        if ($this->serviceId) {
            $service = Service::findOrFail($this->serviceId);
            $customer_id = $service->customer_id;
            $service->delete();
            $this->success('Hizmet Silindi', 'Hizmet kaydı başarıyla silindi.');
            $this->redirect('/dashboard/customers/' . $customer_id . '?tab=services');
        }
    }

}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=services"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Hizmet Listesi</span>
        </a>

        {{-- Header --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight" class="text-skin-heading">
                    @if($isViewMode)
                        {{ $services[0]['service_name'] ?? 'Hizmet' }}
                    @elseif($serviceId)
                        Düzenle: {{ $services[0]['service_name'] ?? 'Hizmet' }}
                    @else
                        Yeni Hizmet Ekle
                    @endif
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($isViewMode)
                        <span
                            class="text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">Hizmet</span>
                        <span class="text-[11px] font-mono text-slate-400">ID: {{ $serviceId }}</span>
                    @else
                        <p class="text-sm opacity-60">
                            {{ count($services) }} hizmet kaydı oluşturun
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($isViewMode)
                    <button type="button" wire:click="delete" wire:confirm="Bu hizmeti silmek istediğinize emin misiniz?"
                        wire:key="btn-delete-{{ $serviceId }}"
                        class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                        Sil
                    </button>
                    <button type="button" wire:click="toggleEditMode" wire:key="btn-edit-{{ $serviceId }}"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                        Düzenle
                    </button>
                @else
                    <button type="button" wire:click="cancel" wire:key="btn-cancel-{{ $serviceId ?: 'new' }}"
                        class="theme-btn-cancel">
                        İptal
                    </button>
                    <button type="button" wire:click="save" wire:loading.attr="disabled"
                        wire:key="btn-save-{{ $serviceId ?: 'new' }}" class="theme-btn-save">
                        <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                        <x-mary-icon name="o-check" class="w-4 h-4" />
                        @if($serviceId) Güncelle @else Kaydet @endif
                    </button>
                @endif
            </div>
        </div>

        {{-- Tab Navigation --}}
        @if($isViewMode)
            <div class="flex items-center border-b border-slate-200 mb-8 overflow-x-auto scrollbar-hide">
                <button wire:click="$set('activeTab', 'info')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'info' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Hizmet Bilgileri
                </button>
                <button wire:click="$set('activeTab', 'messages')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'messages' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Mesajlar (0)
                </button>
                <button wire:click="$set('activeTab', 'notes')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'notes' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Notlar (0)
                </button>
            </div>
        @else
            <div class="mb-8"></div>
        @endif

        <div class="flex gap-6">
            {{-- Left Column (80%) --}}
            <div class="w-4/5">
                @if($activeTab === 'info')
                    <div class="space-y-6">
                        {{-- Varlık Seçimi Card --}}
                        <div class="theme-card p-6 shadow-sm">
                            <h2 class="text-base font-bold mb-4" class="text-skin-heading">Varlık Seçimi
                            </h2>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60">Müşteri *</label>
                                    @if($isViewMode)
                                        @php $customerName = collect($customers)->firstWhere('id', $customer_id)['name'] ?? '-'; @endphp
                                        <div class="text-sm font-medium">
                                            {{ $customerName }}
                                        </div>
                                    @else
                                        <select wire:model.live="customer_id" class="select w-full">
                                            <option value="">Müşteri Seçin</option>
                                            @foreach($customers as $c)
                                                <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('customer_id') <span class="text-skin-danger text-xs">{{ $message }}</span>
                                        @enderror
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60">Varlık *</label>
                                    @if($isViewMode)
                                        @php $assetName = \App\Models\Asset::find($asset_id)?->name ?? '-'; @endphp
                                        <div class="text-sm font-medium">{{ $assetName }}
                                        </div>
                                    @else
                                        <select wire:model="asset_id" class="select w-full" @if(!$customer_id) disabled @endif>
                                            <option value="">{{ $customer_id ? 'Varlık seçin' : 'Önce müşteri seçin' }}</option>
                                            @foreach($assets as $a)
                                                <option value="{{ $a['id'] }}">{{ $a['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('asset_id') <span class="text-skin-danger text-xs">{{ $message }}</span>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Başlangıç Tarihi Card --}}
                        <div class="theme-card p-6 shadow-sm border border-purple-100 bg-purple-50/50">
                            <h2 class="text-base font-bold mb-4" class="text-skin-heading">Başlangıç Tarihi
                            </h2>
                            <div>
                                <label class="block text-xs font-medium mb-1 opacity-60">Başlangıç Tarihi *</label>
                                <div class="text-xs text-slate-400 mb-2">Bitiş tarihi seçilen süreye göre otomatik
                                    hesaplanacaktır.
                                </div>

                                @if($isViewMode)
                                    <div class="text-sm font-medium">
                                        {{ \Carbon\Carbon::parse($start_date)->format('d.m.Y') }}
                                    </div>
                                @else
                                    <input type="date" wire:model="start_date" class="input w-full bg-white">
                                    @error('start_date') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
                                @endif
                            </div>
                        </div>

                        {{-- Hizmet Bilgileri Cards (Dynamic) --}}
                        @foreach($services as $index => $service)
                            <div class="theme-card p-6 shadow-sm border border-green-100 bg-green-50/50"
                                wire:key="service-{{ $index }}">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-base font-bold" class="text-skin-heading">
                                        Hizmet Bilgileri @if(count($services) > 1) #{{ $index + 1 }} @endif
                                    </h2>
                                    @if(!$isViewMode && $index > 0)
                                        <button type="button" wire:click="removeService({{ $index }})"
                                            class="text-skin-danger hover:opacity-80 text-xs font-medium flex items-center gap-1">
                                            <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                                            Kaldır
                                        </button>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-medium mb-1 opacity-60">Kategori *</label>
                                        @if($isViewMode)
                                            <div class="text-sm font-medium">
                                                {{ $service['category'] }}
                                            </div>
                                        @else
                                            <select wire:model.live="services.{{ $index }}.category" class="select w-full">
                                                <option value="">Kategori Seçin</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                                @endforeach
                                            </select>
                                            @error("services.{$index}.category") <span
                                            class="text-skin-danger text-xs">{{ $message }}</span> @enderror
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1 opacity-60">Hizmet *</label>
                                        @if($isViewMode)
                                            <div class="text-sm font-medium">
                                                {{ $service['service_name'] }}
                                            </div>
                                        @else
                                            <select wire:model.live="services.{{ $index }}.service_name" class="select w-full"
                                                @if(empty($service['category'])) disabled @endif>
                                                <option value="">
                                                    {{ !empty($service['category']) ? 'Hizmet seçin' : 'Kategori seçin' }}
                                                </option>
                                                @foreach($service['services_list'] ?? [] as $s)
                                                    <option value="{{ $s['name'] }}">{{ $s['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error("services.{$index}.service_name") <span
                                            class="text-skin-danger text-xs">{{ $message }}</span> @enderror
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1 opacity-60">Durum *</label>
                                        @if($isViewMode)
                                            <div
                                                class="badge {{ $service['status'] === 'ACTIVE' ? 'badge-success' : 'badge-ghost' }} gap-2">
                                                {{ $service['status'] === 'ACTIVE' ? 'Aktif' : 'Pasif' }}
                                            </div>
                                        @else
                                            <select wire:model="services.{{ $index }}.status" class="select w-full">
                                                <option value="ACTIVE">Aktif</option>
                                                <option value="PASSIVE">Pasif</option>
                                            </select>
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1 opacity-60">Özel Fiyat (Opsiyonel)</label>
                                        @if($isViewMode)
                                            <div class="text-sm font-medium">
                                                {{ number_format($service['service_price'], 2, ',', '.') }}
                                                {{ $service['service_currency'] }}
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <input type="number" step="0.01" wire:model="services.{{ $index }}.service_price"
                                                    class="input w-full">
                                                <span
                                                    class="text-sm font-bold text-slate-500">{{ $service['service_currency'] }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium mb-1 opacity-60">Açıklama</label>
                                        @if($isViewMode)
                                            <div class="text-sm font-medium">
                                                {{ $service['description'] ?: '-' }}
                                            </div>
                                        @else
                                            <textarea wire:model="services.{{ $index }}.description" class="textarea w-full"
                                                placeholder="Açıklama..."></textarea>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Add Service Button --}}
                        @if(!$isViewMode && count($services) < 5)
                            <button type="button" wire:click="addService"
                                class="w-full theme-card p-4 shadow-sm border-2 border-dashed border-slate-300 hover:border-slate-400 transition-colors flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900">
                                <x-mary-icon name="o-plus-circle" class="w-5 h-5" />
                                <span class="font-bold text-sm" style="color: var(--action-link-color);">+ Hizmet Ekle</span>
                            </button>
                        @endif
                    </div>
                @endif

                @if($activeTab === 'messages')
                    <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                        <x-mary-icon name="o-chat-bubble-left-right" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Henüz mesaj bulunmuyor</div>
                    </div>
                @endif

                @if($activeTab === 'notes')
                    <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                        <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Henüz not bulunmuyor</div>
                    </div>
                @endif
            </div>

            {{-- Right Column (20%) --}}
            <div class="w-1/5">
                <div class="theme-card p-6 shadow-sm text-center">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Hizmet Görseli</h3>

                    <div
                        class="w-32 h-32 mx-auto border-2 border-dashed border-slate-200 rounded-lg flex items-center justify-center mb-4 bg-white/50 overflow-hidden">
                        @php
                            $svcName = $services[0]['service_name'] ?? 'H';
                            $initials = mb_substr($svcName, 0, 1) ?: 'H';
                        @endphp
                        <div
                            class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400 font-bold text-5xl uppercase">
                            {{ $initials }}
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-400">PNG, JPG, GIF (Max 5MB)</div>
                </div>
            </div>
        </div>
    </div>
</div>