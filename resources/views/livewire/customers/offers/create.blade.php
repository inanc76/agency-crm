<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Service;
use App\Models\PriceDefinition;
use App\Models\Offer;
use App\Models\OfferItem;
use Mary\Traits\Toast;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ReferenceItem;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Teklif Oluştur'])]
    class extends Component {
    use Toast;

    // Offer Fields
    public $customer_id = '';
    public $title = '';
    public $status = 'DRAFT';
    public $description = '';
    public $valid_days = 30;
    public $valid_until = null;
    public $discount_value = 0;
    public $discount_type = 'AMOUNT'; // PERCENTAGE or AMOUNT
    public $vat_rate = 20;
    public $currency = 'USD';

    // Offer Items
    public $items = [];

    // Modal State
    public $showServiceModal = false;
    public $selectedYear = 0;
    public $modalCategory = '';
    public $modalServiceName = '';
    public $showItemDescriptionModal = false;
    public $editingItemIndex = null;
    public $itemDescriptionTemp = '';

    // State Management
    public $isViewMode = false;
    public $offerId = null;
    public string $activeTab = 'info';

    // Manual Entry Modal State
    public $showManualEntryModal = false;
    public $manualItems = [];

    public function openManualEntryModal(): void
    {
        $this->manualItems = [
            [
                'service_name' => '',
                'description' => '',
                'duration' => null,
                'price' => 0,
                'quantity' => 1
            ]
        ];
        $this->showManualEntryModal = true;
    }

    public function addManualItemRow(): void
    {
        $this->manualItems[] = [
            'service_name' => '',
            'description' => '',
            'duration' => null,
            'price' => 0,
            'quantity' => 1
        ];
    }

    public function removeManualItemRow(int $index): void
    {
        unset($this->manualItems[$index]);
        $this->manualItems = array_values($this->manualItems);
    }

    public function saveManualItems(): void
    {
        $this->validate([
            'manualItems.*.service_name' => 'required|string|max:255',
            'manualItems.*.description' => 'nullable|string',
            'manualItems.*.duration' => 'nullable|integer|min:1',
            'manualItems.*.price' => 'required|numeric|min:0',
            'manualItems.*.quantity' => 'required|integer|min:1',
        ], [
            'manualItems.*.service_name.required' => 'Hizmet adı zorunludur.',
            'manualItems.*.price.required' => 'Fiyat zorunludur.',
        ]);

        $count = count($this->manualItems);

        foreach ($this->manualItems as $item) {
            $this->items[] = [
                'service_id' => null, // Manual item
                'service_name' => $item['service_name'],
                'description' => $item['description'] ?? '',
                'price' => (float) $item['price'],
                'currency' => $this->currency,
                'duration' => $item['duration'] ? (int) $item['duration'] : null,
                'quantity' => (int) $item['quantity'],
            ];
        }

        $this->showManualEntryModal = false;
        $this->manualItems = [];
        $this->success('Başarılı', $count . ' kalem eklendi.');
    }

    // Reference Data
    public $customers = [];
    public $customerServices = [];
    public $priceDefinitions = [];
    public $categories = [];
    public $vatRates = [];

    public function mount(?string $offer = null): void
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
                'name' => $categoryDefinitions[$key]->display_label ?? $key
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
                    'label' => $item->display_label
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
            // Check for customer query parameter
            $customerId = request()->query('customer');
            if ($customerId && collect($this->customers)->firstWhere('id', $customerId)) {
                $this->customer_id = $customerId;
                $this->loadCustomerServices();
            }
        }
    }

    private function loadOfferData(): void
    {
        $offer = Offer::with('items')->findOrFail($this->offerId);

        $this->customer_id = $offer->customer_id;
        $this->loadCustomerServices();

        $this->title = $offer->title ?? '';
        $this->status = $offer->status;
        $this->description = $offer->description ?? '';
        $this->discount_value = $offer->discount_percentage > 0 ? $offer->discount_percentage : ($offer->original_amount - $offer->total_amount);
        $this->discount_type = $offer->discount_percentage > 0 ? 'PERCENTAGE' : 'AMOUNT';
        $this->vat_rate = (float) $offer->vat_rate;
        $this->currency = $offer->currency;
        $this->valid_until = Carbon::parse($offer->valid_until)->format('Y-m-d');

        // Load items
        $this->items = $offer->items->map(fn($item) => [
            'service_id' => $item->service_id,
            'service_name' => $item->service_name,
            'description' => $item->description,
            'price' => $item->price,
            'currency' => $item->currency,
            'duration' => $item->duration,
            'quantity' => $item->quantity,
        ])->toArray();

        $this->isViewMode = true;
    }

    public function updatedCustomerId(): void
    {
        $this->loadCustomerServices();
    }

    private function loadCustomerServices(): void
    {
        if ($this->customer_id) {
            $this->customerServices = Service::where('customer_id', $this->customer_id)
                ->where('status', 'ACTIVE')
                ->whereYear('start_date', $this->selectedYear)
                ->get(['id', 'service_name', 'service_price', 'service_duration', 'service_currency', 'description', 'end_date'])
                ->toArray();
        } else {
            $this->customerServices = [];
        }
    }

    public function updatedValidDays(): void
    {
        $this->valid_until = Carbon::now()->addDays($this->valid_days)->format('Y-m-d');
    }

    public function updatedSelectedYear(): void
    {
        $this->loadCustomerServices();
    }

    public function updatedModalCategory(): void
    {
        $this->modalServiceName = '';
    }

    public function updatedDiscountValue(): void
    {
        $totals = $this->calculateTotals();
        $original = $totals['original'];

        if ($this->discount_type === 'PERCENTAGE') {
            if ($this->discount_value > 100) {
                $this->discount_value = 100;
                $this->warning('Uyarı', 'İndirim oranı %100\'ü geçemez.');
            }
        } else {
            if ($this->discount_value > $original) {
                $this->discount_value = $original;
                $this->warning('Uyarı', 'İndirim tutarı teklif tutarını geçemez.');
            }
        }

        if ($this->discount_value < 0) {
            $this->discount_value = 0;
        }
    }

    public function updatedDiscountType(): void
    {
        $this->discount_value = 0;
    }

    public function openServiceModal(): void
    {
        if (!$this->customer_id) {
            $this->error('Uyarı', 'Lütfen önce bir müşteri seçin.');
            return;
        }
        $this->showServiceModal = true;
        $this->loadCustomerServices();
    }

    public function closeServiceModal(): void
    {
        $this->showServiceModal = false;
        $this->modalCategory = '';
        $this->modalServiceName = '';
    }

    public function addServiceFromExisting(string $serviceId): void
    {
        $service = collect($this->customerServices)->firstWhere('id', $serviceId);

        if ($service) {
            // Currency sync & validation
            if (count($this->items) > 0) {
                if ($service['service_currency'] !== $this->currency) {
                    $this->error('Para Birimi Uyumsuzluğu', "Bu teklif {$this->currency} cinsindendir. {$service['service_currency']} birimli bir hizmet ekleyemezsiniz.");
                    return;
                }
            } else {
                $this->currency = $service['service_currency'];
            }

            $this->items[] = [
                'service_id' => $service['id'],
                'service_name' => $service['service_name'],
                'description' => ($service['description'] ?? '') . " (Uzatma)",
                'price' => $service['service_price'],
                'currency' => $service['service_currency'],
                'duration' => $service['service_duration'] ?? 1,
                'quantity' => 1,
            ];

            $this->success('Başarılı', 'Hizmet uzatma kalemi eklendi.');
            $this->closeServiceModal();
        }
    }

    public function addServiceFromPriceDefinition(): void
    {
        if (!$this->modalServiceName) {
            $this->error('Uyarı', 'Lütfen bir hizmet seçin.');
            return;
        }

        $priceDef = collect($this->priceDefinitions)
            ->where('category', $this->modalCategory)
            ->firstWhere('name', $this->modalServiceName);

        if ($priceDef) {
            // Currency sync & validation
            if (count($this->items) > 0) {
                if ($priceDef['currency'] !== $this->currency) {
                    $this->error('Para Birimi Uyumsuzluğu', "Bu teklif {$this->currency} cinsindendir. {$priceDef['currency']} birimli bir hizmet ekleyemezsiniz.");
                    return;
                }
            } else {
                $this->currency = $priceDef['currency'];
            }

            $this->items[] = [
                'service_id' => null,
                'service_name' => $priceDef['name'],
                'description' => $priceDef['description'] ?? '',
                'price' => $priceDef['price'],
                'currency' => $priceDef['currency'],
                'duration' => $priceDef['duration'] ?? 1,
                'quantity' => 1,
            ];

            $this->success('Başarılı', 'Hizmet eklendi.');
            $this->closeServiceModal();
        }
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function openItemDescriptionModal(int $index): void
    {
        $this->editingItemIndex = $index;
        $this->itemDescriptionTemp = $this->items[$index]['description'] ?? '';
        $this->showItemDescriptionModal = true;
    }

    public function saveItemDescription(): void
    {
        if ($this->editingItemIndex !== null) {
            $this->items[$this->editingItemIndex]['description'] = Str::limit($this->itemDescriptionTemp, 50, '');
            $this->showItemDescriptionModal = false;
            $this->editingItemIndex = null;
            $this->itemDescriptionTemp = '';
            $this->success('Başarılı', 'Açıklama güncellendi.');
        }
    }

    public function calculateTotals(): array
    {
        $original = collect($this->items)->sum(fn($item) => $item['price'] * $item['quantity']);

        $discountAmount = 0;
        if ($this->discount_type === 'PERCENTAGE') {
            $discountAmount = $original * (min(100, $this->discount_value) / 100);
        } else {
            $discountAmount = min($original, $this->discount_value);
        }

        $totalAfterDiscount = max(0, $original - $discountAmount);
        $vatAmount = $totalAfterDiscount * ($this->vat_rate / 100);
        $grandTotal = max(0, $totalAfterDiscount + $vatAmount);

        return [
            'original' => $original,
            'discount' => $discountAmount,
            'vat' => $vatAmount,
            'total' => $grandTotal,
        ];
    }

    public function save(): void
    {
        $this->validate([
            'customer_id' => 'required',
            'title' => 'required|string|max:255',
            'valid_until' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        $totals = $this->calculateTotals();

        DB::transaction(function () use ($totals) {
            $offerData = [
                'customer_id' => $this->customer_id,
                'number' => $this->generateOfferNumber(),
                'title' => $this->title,
                'status' => $this->status,
                'description' => $this->description,
                'original_amount' => $totals['original'],
                'discount_percentage' => $this->discount_type === 'PERCENTAGE' ? $this->discount_value : 0,
                'discounted_amount' => $totals['discount'],
                'total_amount' => $totals['total'],
                'currency' => $this->currency,
                'vat_rate' => $this->vat_rate,
                'vat_amount' => $totals['vat'],
                'valid_until' => $this->valid_until,
            ];

            if ($this->offerId) {
                $offer = Offer::findOrFail($this->offerId);
                $offer->update($offerData);
                $offer->items()->delete();
            } else {
                $this->offerId = Str::uuid()->toString();
                $offerData['id'] = $this->offerId;
                $offer = Offer::create($offerData);
            }

            // Create items
            foreach ($this->items as $item) {
                OfferItem::create([
                    'id' => Str::uuid()->toString(),
                    'offer_id' => $offer->id,
                    'service_id' => $item['service_id'],
                    'service_name' => $item['service_name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'currency' => $item['currency'],
                    'duration' => $item['duration'],
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        $this->success('İşlem Başarılı', 'Teklif başarıyla kaydedildi.');
        $this->redirect('/dashboard/customers?tab=offers');
    }

    private function generateOfferNumber(): string
    {
        $year = Carbon::now()->year;
        $lastOffer = Offer::whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->first();

        $sequence = $lastOffer ? (int) substr($lastOffer->number, -4) + 1 : 1;

        return sprintf('TKL-%d-%04d', $year, $sequence);
    }

    public function cancel(): void
    {
        if ($this->offerId) {
            $this->loadOfferData();
        } else {
            $this->redirect('/dashboard/customers?tab=offers');
        }
    }

    public function toggleEditMode(): void
    {
        $this->isViewMode = false;
    }

    public function delete(): void
    {
        if ($this->offerId) {
            Offer::findOrFail($this->offerId)->delete();
            $this->success('Teklif Silindi', 'Teklif başarıyla silindi.');
            $this->redirect('/dashboard/customers?tab=offers');
        }
    }

}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=offers"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Teklif Listesi</span>
        </a>

        {{-- Header --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight" style="color: var(--color-text-heading);">
                    @if($isViewMode) {{ $title }} @else Yeni Teklif Oluştur @endif
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($isViewMode)
                        <span
                            class="text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">Teklif</span>
                        <span class="text-[11px] font-mono text-slate-400">ID: {{ $offerId }}</span>
                    @else
                        <p class="text-sm opacity-60" style="color: var(--color-text-base);">
                            Müşteri için yeni bir teklif hazırlayın
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($isViewMode)
                    <button type="button" wire:click="delete" wire:confirm="Bu teklifi silmek istediğinize emin misiniz?"
                        wire:key="btn-delete-{{ $offerId }}"
                        class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                        Sil
                    </button>
                    <button type="button" wire:click="toggleEditMode" wire:key="btn-edit-{{ $offerId }}"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                        Düzenle
                    </button>
                @else
                    <button type="button" wire:click="cancel" wire:key="btn-cancel-{{ $offerId ?: 'new' }}"
                        class="theme-btn-cancel">
                        İptal
                    </button>
                    <button type="button" wire:click="save" wire:loading.attr="disabled"
                        wire:key="btn-save-{{ $offerId ?: 'new' }}" class="theme-btn-save">
                        <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                        <x-mary-icon name="o-check" class="w-4 h-4" />
                        @if($offerId) Güncelle @else Teklif Oluştur @endif
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
                    Teklif Bilgileri
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
                <button wire:click="$set('activeTab', 'downloads')"
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'downloads' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    İndirmeler (0)
                </button>
            </div>
        @else
            <div class="mb-8"></div>
        @endif

        <div class="grid grid-cols-12 gap-6">
            {{-- Left Column (8/12) --}}
            <div class="col-span-8">
                @if($activeTab === 'info')
                    <div class="space-y-6">
                        {{-- Müşteri Bilgileri Card --}}
                        <div class="theme-card p-6 shadow-sm">
                            <h2 class="text-base font-bold mb-4" style="color: var(--color-text-heading);">Müşteri Bilgileri
                            </h2>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60"
                                        style="color: var(--color-text-base);">Müşteri *</label>
                                    @if($isViewMode)
                                        @php $customerName = collect($customers)->firstWhere('id', $customer_id)['name'] ?? '-'; @endphp
                                        <div class="text-sm font-medium" style="color: var(--color-text-base);">
                                            {{ $customerName }}
                                        </div>
                                    @else
                                        <select wire:model.live="customer_id" class="select w-full">
                                            <option value="">Müşteri Seçin</option>
                                            @foreach($customers as $c)
                                                <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('customer_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60"
                                        style="color: var(--color-text-base);">Teklif Durumu</label>
                                    @if($isViewMode)
                                        <div class="text-sm font-medium" style="color: var(--color-text-base);">
                                            @if($status === 'DRAFT') Taslak
                                            @elseif($status === 'SENT') Gönderildi
                                            @elseif($status === 'ACCEPTED') Kabul Edildi
                                            @else Reddedildi
                                            @endif
                                        </div>
                                    @else
                                        <select wire:model="status" class="select w-full">
                                            <option value="DRAFT">Taslak</option>
                                            <option value="SENT">Gönderildi</option>
                                            <option value="ACCEPTED">Kabul Edildi</option>
                                            <option value="REJECTED">Reddedildi</option>
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Teklif Ayarları Card --}}
                        <div class="theme-card p-6 shadow-sm border border-blue-100 bg-blue-50/50">
                            <h2 class="text-base font-bold mb-4" style="color: var(--color-text-heading);">Teklif Ayarları
                            </h2>
                            <div class="grid grid-cols-2 gap-6">
                                {{-- Title moved to Description card --}}

                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60"
                                        style="color: var(--color-text-base);">Geçerlilik Süresi (Gün)</label>
                                    @if($isViewMode)
                                        <div class="text-sm font-medium" style="color: var(--color-text-base);">
                                            {{ $valid_days }}
                                            gün
                                        </div>
                                    @else
                                        <input type="number" wire:model.live="valid_days" class="input w-full bg-white" min="1">
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60"
                                        style="color: var(--color-text-base);">Para Birimi</label>
                                    @if($isViewMode)
                                        <div class="text-sm font-medium" style="color: var(--color-text-base);">{{ $currency }}
                                        </div>
                                    @else
                                        <select wire:model.live="currency" class="select w-full bg-white">
                                            <option value="TRY">TRY</option>
                                            <option value="USD">USD</option>
                                            <option value="EUR">EUR</option>
                                        </select>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60"
                                        style="color: var(--color-text-base);">İndirim</label>
                                    @if($isViewMode)
                                        <div class="text-sm font-medium" style="color: var(--color-text-base);">
                                            @if($discount_type === 'PERCENTAGE') %{{ $discount_value }} @else
                                            {{ number_format($discount_value, 0, ',', '.') }} {{ $currency }} @endif
                                        </div>
                                    @else
                                        <div class="flex items-center gap-[5px]">
                                            <select wire:model.live="discount_type"
                                                class="select select-sm w-24 bg-white border-slate-200 focus:outline-none">
                                                <option value="PERCENTAGE">%</option>
                                                <option value="AMOUNT">Tutar</option>
                                            </select>
                                            <input type="number" wire:model.live="discount_value"
                                                class="input input-sm flex-1 bg-white border-slate-200 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                min="0" step="0.01" placeholder="0.00">
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60"
                                        style="color: var(--color-text-base);">KDV Oranı</label>
                                    @if($isViewMode)
                                        <div class="text-sm font-medium" style="color: var(--color-text-base);">
                                            %{{ $vat_rate }}
                                        </div>
                                    @else
                                        <select wire:model.live="vat_rate"
                                            class="select select-sm w-full bg-white border-slate-200 group-hover:border-slate-300">
                                            @foreach($vatRates as $rate)
                                                <option value="{{ $rate['rate'] }}">{{ $rate['label'] }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>

                                {{-- Validity date moved to summary sidebar --}}
                            </div>
                        </div>

                        {{-- Teklif Başlığı ve Açıklaması Card --}}
                        <div class="theme-card p-6 shadow-sm border border-purple-100 bg-purple-50/50">
                            <h2 class="text-base font-bold mb-4" style="color: var(--color-text-heading);">Teklif Başlığı ve
                                Açıklaması</h2>

                            <div class="mb-4">
                                <label class="block text-xs font-medium mb-1 opacity-60"
                                    style="color: var(--color-text-base);">Teklif Başlığı *</label>
                                @if($isViewMode)
                                    <div class="text-sm font-medium" style="color: var(--color-text-base);">{{ $title }}</div>
                                @else
                                    <input type="text" wire:model="title" placeholder="Örn: Web Sitesi Bakım Teklifi"
                                        class="input w-full bg-white">
                                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @endif
                            </div>

                            <div>
                                <label class="block text-xs font-medium mb-1 opacity-60"
                                    style="color: var(--color-text-base);">Teklif Açıklaması</label>
                                @if($isViewMode)
                                    <div class="text-sm font-medium whitespace-pre-wrap" style="color: var(--color-text-base);">
                                        {{ $description ?: '-' }}
                                    </div>
                                @else
                                    <textarea wire:model="description" class="textarea w-full bg-white" rows="4"
                                        placeholder="Teklif hakkında detaylı açıklama yazabilirsiniz..."></textarea>
                                @endif
                            </div>
                        </div>

                        {{-- Teklif Kalemleri Card --}}
                        <div class="theme-card p-6 shadow-sm border border-green-100 bg-green-50/50">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-base font-bold" style="color: var(--color-text-heading);">Teklif Kalemleri *
                                </h2>
                                @if(!$isViewMode)
                                    <div class="flex gap-2">
                                        <button type="button" wire:click="openManualEntryModal"
                                            class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700">
                                            <x-mary-icon name="o-pencil" class="w-4 h-4" />
                                            Manuel Ekle
                                        </button>
                                        <button type="button" wire:click="openServiceModal"
                                            class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700">
                                            <x-mary-icon name="o-plus" class="w-4 h-4" />
                                            Hizmet Ekle
                                        </button>
                                    </div>
                                @endif
                            </div>

                            @if(count($items) > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-slate-200">
                                                <th class="text-left py-2 px-2 font-medium opacity-60"
                                                    style="color: var(--color-text-base);">Hizmet Adı</th>
                                                <th class="text-left py-2 px-2 font-medium opacity-60"
                                                    style="color: var(--color-text-base);">Açıklama</th>
                                                <th class="text-center py-2 px-2 font-medium opacity-60"
                                                    style="color: var(--color-text-base);">Süre</th>
                                                <th class="text-right py-2 px-2 font-medium opacity-60"
                                                    style="color: var(--color-text-base);">Fiyat</th>
                                                <th class="text-center py-2 px-2 font-medium opacity-60"
                                                    style="color: var(--color-text-base);">Adet</th>
                                                <th class="text-right py-2 px-2 font-medium opacity-60"
                                                    style="color: var(--color-text-base);">Toplam</th>
                                                @if(!$isViewMode)
                                                    <th class="w-10"></th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $index => $item)
                                                <tr class="border-b border-slate-100" wire:key="item-{{ $index }}">
                                                    <td class="py-3 px-2 font-normal text-xs"
                                                        style="color: var(--color-text-base);">
                                                        {{ $item['service_name'] }}
                                                    </td>
                                                    <td class="py-3 px-2 text-xs opacity-70" style="color: var(--color-text-base);">
                                                        <div class="flex items-center gap-1 text-slate-500">
                                                            <span>{{ Str::limit($item['description'], 40) }}</span>
                                                            @if(!$isViewMode)
                                                                <button type="button"
                                                                    wire:click="openItemDescriptionModal({{ $index }})"
                                                                    class="p-1 hover:bg-slate-200 rounded text-slate-400 hover:text-blue-600 transition-colors cursor-pointer"
                                                                    title="Açıklamayı Düzenle">
                                                                    <x-mary-icon name="o-pencil-square" class="w-3.5 h-3.5" />
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-2 text-center text-xs">{{ $item['duration'] }} Yıl</td>
                                                    <td class="py-3 px-2 text-right text-xs">
                                                        {{ number_format($item['price'], 0, ',', '.') }}
                                                        {{ $item['currency'] }}
                                                    </td>
                                                    <td class="py-3 px-2 text-center">
                                                        @if(!$isViewMode)
                                                            <input type="number" wire:model.live="items.{{ $index }}.quantity"
                                                                class="input w-16 text-center bg-white" min="1">
                                                        @else
                                                            {{ $item['quantity'] }}
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-2 text-right text-xs font-normal"
                                                        style="color: var(--color-text-heading);">
                                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                                        {{ $item['currency'] }}
                                                    </td>
                                                    @if(!$isViewMode)
                                                        <td class="py-3 px-2">
                                                            <button type="button" wire:click="removeItem({{ $index }})"
                                                                class="text-red-500 hover:text-red-700">
                                                                <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                                                            </button>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8 text-slate-400">
                                    <x-mary-icon name="o-inbox" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                                    <p class="text-sm">Henüz kalem eklenmemiş</p>
                                    <p class="text-xs mt-1">Yukarıdaki "+ Hizmet Ekle" butonuna tıklayarak başlayın</p>
                                </div>
                            @endif
                            @error('items') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
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

                @if($activeTab === 'downloads')
                    <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                        <x-mary-icon name="o-arrow-down-tray" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Henüz indirme bulunmuyor</div>
                    </div>
                @endif
            </div>

            {{-- Right Column (4/12) - Summary --}}
            <div class="col-span-4">
                <div class="theme-card p-6 shadow-sm sticky top-6">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Teklif Özeti</h3>

                    @php
                        $totals = $this->calculateTotals();
                    @endphp

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="opacity-60">Ara Toplam:</span>
                            <span class="font-medium">{{ number_format($totals['original'], 0, ',', '.') }}
                                {{ $currency }}</span>
                        </div>

                        @if($totals['discount'] > 0)
                            <div class="flex justify-between text-red-600">
                                <span>İndirim (@if($discount_type === 'PERCENTAGE') %{{ $discount_value }} @else Tutar
                                @endif):</span>
                                <span class="font-medium">-{{ number_format($totals['discount'], 0, ',', '.') }}
                                    {{ $currency }}</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-slate-200">
                                <span class="opacity-60">İndirimli Toplam:</span>
                                <span
                                    class="font-medium">{{ number_format($totals['original'] - $totals['discount'], 0, ',', '.') }}
                                    {{ $currency }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between">
                            <span class="opacity-60">KDV (%{{ (int) $vat_rate }}):</span>
                            <span class="font-medium">{{ number_format($totals['vat'], 0, ',', '.') }}
                                {{ $currency }}</span>
                        </div>

                        <div class="flex justify-between pt-2 border-t border-slate-200">
                            <span class="opacity-60 text-[11px] uppercase tracking-wider">Geçerlilik Tarihi:</span>
                            <span
                                class="font-medium text-[11px]">{{ \Carbon\Carbon::parse($valid_until)->format('d.m.Y') }}</span>
                        </div>

                        <div class="flex justify-between pt-3 border-t-2 border-slate-300 text-base font-bold"
                            style="color: var(--color-text-heading);">
                            <span>Genel Toplam:</span>
                            <span>{{ number_format($totals['total'], 0, ',', '.') }} {{ $currency }}</span>
                        </div>
                    </div>

                    @if(!$isViewMode && count($items) > 0)
                        <div class="mt-4 text-xs text-slate-400 text-center">
                            Fiyatlar KDV dahil değildir
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Service Selection Modal --}}
    <x-mary-modal wire:model="showServiceModal" title="Hizmet Ekle" class="backdrop-blur" box-class="!max-w-5xl">
        <div class="grid grid-cols-2 gap-6">
            {{-- Left Panel: Existing Services --}}
            <div class="border-r border-slate-200 pr-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-bold text-sm" style="color: var(--color-text-heading);">Mevcut Hizmetleri Uzat</h4>
                    <select wire:model.live="selectedYear" class="select select-sm bg-white border-slate-200">
                        @for($year = date('Y'); $year >= date('Y') - 2; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>

                @if(count($customerServices) > 0)
                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
                        @foreach($customerServices as $service)
                            <div
                                class="p-4 border border-slate-100 rounded-xl bg-slate-50/50 hover:bg-white hover:border-blue-200 hover:shadow-sm transition-all group">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-sm text-slate-800 truncate">{{ $service['service_name'] }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span
                                                class="text-xs font-semibold text-blue-600">{{ number_format($service['service_price'], 0, ',', '.') }}
                                                {{ $service['service_currency'] }}</span>
                                            <span
                                                class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $service['service_duration'] }}
                                                Yıl</span>
                                            @if($service['end_date'])
                                                <span
                                                    class="text-[10px] px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-100 font-medium">Bitiş:
                                                    {{ \Carbon\Carbon::parse($service['end_date'])->format('d.m.Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button wire:click="addServiceFromExisting('{{ $service['id'] }}')"
                                        class="theme-btn-cancel !py-1 !px-4 !text-xs !shadow-none hover:!bg-blue-600 hover:!text-white hover:!border-blue-600 transition-colors">
                                        Uzat
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <x-mary-icon name="o-clock" class="w-8 h-8 mx-auto mb-2 opacity-20" />
                        <p class="text-sm text-slate-400">Bu yıl için süreli hizmet bulunamadı</p>
                    </div>
                @endif
            </div>

            {{-- Right Panel: Price Definitions --}}
            <div class="pl-6">
                <h4 class="font-bold text-sm mb-4" style="color: var(--color-text-heading);">Yeni Hizmet Ekle</h4>
                <p class="text-xs text-slate-500 mb-6 font-medium">Fiyat tanımlarından yeni hizmet ekleyebilirsiniz</p>

                <div class="space-y-5">
                    <x-mary-select label="Kategori Seçin" icon="o-tag" wire:model.live="modalCategory"
                        :options="$categories" placeholder="Kategori Seçin" />

                    @if($modalCategory)
                        <x-mary-select label="Hizmet Adı" icon="o-briefcase" wire:model.live="modalServiceName"
                            :options="collect($priceDefinitions)->where('category', $modalCategory)->map(fn($pd) => ['id' => $pd['name'], 'name' => $pd['name']])->toArray()" placeholder="Hizmet Seçin" />
                    @endif

                    @if($modalServiceName)
                        @php
                            $selectedPD = collect($priceDefinitions)
                                ->where('category', $modalCategory)
                                ->firstWhere('name', $modalServiceName);
                        @endphp
                        @if($selectedPD)
                            <div
                                class="p-5 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 rounded-xl shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $selectedPD['name'] }}</p>
                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ $selectedPD['description'] ?? 'Tanımlı hizmet içeriği' }}
                                        </p>
                                    </div>
                                    <span
                                        class="bg-white px-2 py-1 rounded text-[10px] font-bold text-green-600 border border-green-100 shadow-sm">{{ $selectedPD['duration'] ?? 1 }}
                                        Yıl</span>
                                </div>
                                <div class="mt-4 flex items-end justify-between">
                                    <span
                                        class="text-lg font-black text-slate-900">{{ number_format($selectedPD['price'], 0, ',', '.') }}
                                        <span class="text-sm font-medium">{{ $selectedPD['currency'] }}</span></span>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <x-slot:actions>
            <button wire:click="closeServiceModal" class="theme-btn-cancel">
                Vazgeç
            </button>
            <button wire:click="addServiceFromPriceDefinition" class="theme-btn-save" @if(!$modalServiceName) disabled
            @endif>
                <x-mary-icon name="o-check" class="w-4 h-4" />
                Hizmeti Ekle
            </button>
        </x-slot:actions>
    </x-mary-modal>

    {{-- Item Description Modal --}}
    <x-mary-modal wire:model="showItemDescriptionModal" title="Teklif Kalem Açıklaması" class="backdrop-blur"
        box-class="!max-w-md">
        <div class="space-y-4">
            <div class="relative">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-xs font-bold opacity-70" style="color: var(--color-text-base);">Kalem
                        Açıklaması</label>
                    <span
                        class="text-[10px] font-black px-2 py-0.5 rounded-lg {{ strlen($itemDescriptionTemp) >= 50 ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }}">
                        {{ 50 - strlen($itemDescriptionTemp) }} Karakter Kaldı
                    </span>
                </div>
                <textarea wire:model.live="itemDescriptionTemp"
                    class="textarea textarea-bordered w-full bg-white border-slate-200 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition-all text-sm leading-relaxed"
                    placeholder="Bu kalem için özel bir not ekleyin..." rows="3" maxlength="50"
                    style="border-radius: var(--input-radius, 0.375rem);"></textarea>
            </div>
            <p class="text-[11px] opacity-50 italic leading-relaxed" style="color: var(--color-text-base);">
                * Bu açıklama teklif dökümanında ilgili hizmet kalemi altında gösterilecektir.
            </p>
        </div>

        <x-slot:actions>
            <button wire:click="showItemDescriptionModal = false" class="theme-btn-cancel">
                Vazgeç
            </button>
            <button wire:click="saveItemDescription" class="theme-btn-save">
                <x-mary-icon name="o-check" class="w-4 h-4" />
                Kaydet
            </button>
        </x-slot:actions>
    </x-mary-modal>

    {{-- Manual Entry Modal --}}
    <x-mary-modal wire:model="showManualEntryModal" title="Manuel Kalem Ekle" class="backdrop-blur"
        box-class="!max-w-6xl">
        <div class="overflow-x-auto min-h-[300px]">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-2 w-[25%]">Hizmet Adı *</th>
                        <th class="text-left py-2 px-2 w-[25%]">Açıklama</th>
                        <th class="text-center py-2 px-2 w-[10%]">Süre (Yıl)</th>
                        <th class="text-right py-2 px-2 w-[15%]">Fiyat ({{ $currency }}) *</th>
                        <th class="text-center py-2 px-2 w-[10%]">Adet *</th>
                        <th class="text-right py-2 px-2 w-[10%]">Toplam</th>
                        <th class="w-[5%]"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($manualItems as $index => $mItem)
                        <tr wire:key="manual-item-{{ $index }}">
                            <td class="p-2 align-top">
                                <input type="text" wire:model="manualItems.{{ $index }}.service_name"
                                    class="input input-sm w-full bg-white border-slate-200 focus:border-blue-400"
                                    placeholder="Hizmet Adı">
                                @error("manualItems.{$index}.service_name")
                                    <span class="text-red-500 text-[10px] block mt-1">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="p-2 align-top">
                                <textarea wire:model="manualItems.{{ $index }}.description"
                                    class="textarea textarea-sm w-full bg-white border-slate-200 focus:border-blue-400"
                                    rows="1" placeholder="Açıklama"></textarea>
                            </td>
                            <td class="p-2 align-top">
                                <input type="number" wire:model="manualItems.{{ $index }}.duration"
                                    class="input input-sm w-full bg-white border-slate-200 focus:border-blue-400 text-center"
                                    placeholder="Opsiyonel" min="1">
                            </td>
                            <td class="p-2 align-top">
                                <input type="number" wire:model.live="manualItems.{{ $index }}.price"
                                    class="input input-sm w-full bg-white border-slate-200 focus:border-blue-400 text-right"
                                    min="0" step="0.01">
                                @error("manualItems.{$index}.price")
                                    <span class="text-red-500 text-[10px] block mt-1">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="p-2 align-top">
                                <input type="number" wire:model.live="manualItems.{{ $index }}.quantity"
                                    class="input input-sm w-full bg-white border-slate-200 focus:border-blue-400 text-center"
                                    min="1">
                            </td>
                            <td class="p-2 align-top text-right font-medium pt-3 text-slate-700">
                                {{ number_format(((float) ($mItem['price'] ?? 0)) * ((int) ($mItem['quantity'] ?? 1)), 0, ',', '.') }}
                            </td>
                            <td class="p-2 align-top pt-2 text-center">
                                @if(count($manualItems) > 1)
                                    <button type="button" wire:click="removeManualItemRow({{ $index }})"
                                        class="text-red-500 hover:text-red-700 p-1">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                <button type="button" wire:click="addManualItemRow"
                    class="flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                    <x-mary-icon name="o-plus-circle" class="w-4 h-4" />
                    Yeni Satır Ekle
                </button>
            </div>
        </div>

        <x-slot:actions>
            <button wire:click="$set('showManualEntryModal', false)" class="theme-btn-cancel">
                Vazgeç
            </button>
            <button wire:click="saveManualItems" class="theme-btn-save">
                <x-mary-icon name="o-check" class="w-4 h-4" />
                Listeye Ekle
            </button>
        </x-slot:actions>
    </x-mary-modal>
</div>