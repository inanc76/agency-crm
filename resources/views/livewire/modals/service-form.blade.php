<?php
/**
 * 🛡️ ZIRHLI BELGELEME KARTI (V12.2)
 * -------------------------------------------------------------------------
 * COMPONENT   : ServiceForm (Orchestra Shell)
 * SORUMLULUK  : Müşteri hizmetlerini (Service) yöneten ViewModel.
 *               Toplu hizmet ekleme ve proje entegrasyonu sağlar.
 *
 * BAĞIMLILIKLAR:
 * - App\Livewire\Customers\Services\Traits\HasServiceActions
 * - App\Livewire\Traits\HasServiceCalculations
 * - Mary\Traits\Toast
 * -------------------------------------------------------------------------
 */
use App\Livewire\Customers\Services\Traits\HasServiceActions;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use App\Models\Customer;
use App\Models\ReferenceItem;
use Carbon\Carbon;

new class extends Component
{
    use HasServiceActions, Toast;

    // --- Hizmet Verileri (State Management) ---
    public string $customer_id = '';
    public string $asset_id = '';
    public ?string $start_date = null;
    public array $services = [];
    public array $projectSummary = [];

    // --- UI ve Sistem Durumu ---
    public bool $isViewMode = false;
    public ?string $serviceId = null;
    public string $activeTab = 'info';

    // --- Referans Verileri (ReferenceData) ---
    public array $customers = [];
    public array $assets = [];
    public array $projects = [];
    public array $categories = [];
    public array $serviceStatuses = [];

    /**
     * Bileşen yaşam döngüsü başlangıcı.
     * Gerekli tüm referans verileri hazırlar ve başlangıç durumunu belirler.
     */
    public function mount(?string $service = null): void
    {
        // Temel referans verileri yükle
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        $this->categories = ReferenceItem::where('category_key', 'SERVICE_CATEGORY')
            ->orderBy('sort_order')
            ->get(['key', 'display_label'])
            ->map(fn($item) => ['key' => $item->key, 'label' => $item->display_label])
            ->toArray();

        $this->serviceStatuses = ReferenceItem::where('category_key', 'SERVICE_STATUS')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key', 'metadata'])
            ->map(fn($i) => ['id' => $i->id, 'display_label' => $i->display_label, 'key' => $i->key])
            ->toArray();

        $this->start_date = Carbon::now()->format('Y-m-d');

        if ($service) {
            $this->serviceId = $service;
            $this->loadServiceData();
            $this->activeTab = request()->query('tab', 'info');
        } else {
            // Yeni kayıt için ön hazırlık
            $customerId = request()->query('customer');
            if ($customerId && collect($this->customers)->firstWhere('id', $customerId)) {
                $this->customer_id = $customerId;
                $this->loadAssets();
                $this->loadProjects();
            }
            $this->addService();
        }
    }
}; ?>

<div>
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=services"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Hizmet Listesi</span>
        </a>

        {{-- Header Section --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
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
                        <span class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--badge-bg)] text-[var(--badge-text)] border border-[var(--badge-border)]">Hizmet</span>
                        <span class="text-[11px] font-mono text-[var(--color-text-muted)]">ID: {{ $serviceId }}</span>
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
            <div class="flex items-center border-b border-[var(--card-border)] mb-8 overflow-x-auto scrollbar-hide">
                <button wire:click="$set('activeTab', 'info')"
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'info' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Hizmet Bilgileri
                </button>
                <button wire:click="$set('activeTab', 'messages')"
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'messages' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Mesajlar (0)
                </button>
                <button wire:click="$set('activeTab', 'notes')"
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'notes' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Notlar (0)
                </button>
            </div>
        @else
            <div class="mb-8"></div>
        @endif

        {{-- Main Layout: Full Width --}}
        <div>
            {{-- Content --}}
            <div>
                @if($activeTab === 'info')
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-8 space-y-6">
                            @include('livewire.modals.parts.service._service-core', [
                                'customer_id' => $customer_id,
                                'asset_id' => $asset_id,
                                'start_date' => $start_date,
                                'isViewMode' => $isViewMode,
                                'customers' => $customers,
                                'assets' => $assets
                            ])

                            <div class="mt-6">
                                @include('livewire.modals.parts.service._service-pricing-logic', [
                                    'services' => $services,
                                    'categories' => $categories,
                                    'isViewMode' => $isViewMode
                                ])
                            </div>
                        </div>
                        <div class="col-span-4 space-y-6">
                            <div class="theme-card p-6 shadow-sm sticky top-6">
                                <h2 class="text-base font-bold mb-4 text-center text-skin-heading">Kayıt Bilgileri</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base text-center">Hizmet ID</label>
                                        <div class="flex items-center justify-center gap-2">
                                            <code class="text-[10px] font-mono bg-[var(--dropdown-hover-bg)] px-2 py-1 rounded text-skin-base">{{ $serviceId ?? 'YENİ' }}</code>
                                        </div>
                                    </div>
                                    @if($customer_id && $isViewMode)
                                         @php $customerName = collect($customers)->firstWhere('id', $customer_id)['name'] ?? '-'; @endphp
                                         <div>
                                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base text-center">Bağlı Müşteri</label>
                                            <div class="text-sm font-medium text-center">
                                                <a href="/dashboard/customers/{{ $customer_id }}" class="text-skin-primary hover:underline">{{ $customerName }}</a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($activeTab === 'messages')
                    <div class="theme-card p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-base font-bold text-skin-heading">Mesajlar</h2>
                        </div>

                        <div class="bg-white rounded-xl border border-skin-light shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="agency-table">
                                    <thead>
                                        <tr>
                                            <th class="w-10">
                                                <input type="checkbox" disabled
                                                    class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                                            </th>
                                            <th>Konu</th>
                                            <th>Alıcı</th>
                                            <th>Durum</th>
                                            <th class="text-right">Tarih</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-skin-muted">
                                                <div class="flex flex-col items-center justify-center">
                                                    <x-mary-icon name="o-chat-bubble-left-right" class="w-12 h-12 opacity-20 mb-4" />
                                                    <div class="font-medium">Henüz mesaj bulunmuyor</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Footer --}}
                            <div class="px-6 py-4 border-t border-skin-light flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-skin-muted">Göster:</span>
                                    <div class="px-2 py-1 border border-skin-light rounded text-xs bg-white">25</div>
                                </div>

                                <div class="text-[10px] text-skin-muted font-mono">
                                    0 kayıt listelendi
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($activeTab === 'notes')
                    @if($serviceId)
                        @livewire('shared.notes-tab', [
                            'entityType' => 'SERVICE',
                            'entityId' => $serviceId
                        ], key('notes-tab-' . $serviceId))
                    @else
                        <div class="theme-card p-6 shadow-sm text-center text-[var(--color-text-muted)] py-12">
                            <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                            <div class="font-medium">Hizmeti kaydedin, ardından not ekleyebilirsiniz</div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>