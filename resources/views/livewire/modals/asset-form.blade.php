<?php
/**
 * 🛡️ ZIRHLI BELGELEME KARTI (V12.2)
 * -------------------------------------------------------------------------
 * COMPONENT   : AssetForm (Orchestra Shell)
 * SORUMLULUK  : Müşteri varlıkları (Asset) için ViewModel görevi görür.
 *               Trait üzerindeki CRUD aksiyonlarını yönetir.
 *
 * BAĞIMLILIKLAR:
 * - App\Livewire\Customers\Assets\Traits\HasAssetActions
 * - Mary\Traits\Toast
 * -------------------------------------------------------------------------
 */
use App\Livewire\Customers\Assets\Traits\HasAssetActions;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Mary\Traits\Toast;
use App\Models\Customer;
use App\Models\ReferenceItem;

new 
#[Layout('components.layouts.app')]
class extends Component {
    use HasAssetActions, Toast;

    // --- Varlık Verileri (State Management) ---
    public string $customer_id = '';
    public string $name = '';
    public string $type = '';
    public string $url = '';

    // --- UI ve Sistem Durumu ---
    public bool $isViewMode = false;
    public ?string $assetId = null;
    public string $activeTab = 'info';

    // --- Referans Verileri (ReferenceData) ---
    public array $customers = [];
    public array $assetTypes = [];

    /**
     * Bileşen yaşam döngüsü başlangıcı.
     * Referans dataları hazırlar ve varsa mevcut varlığı yükler.
     */
    public function mount(?string $asset = null): void
    {
        // Müşteri listesini yükle
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // Sistemdeki Varlık Türlerini yükle
        $this->assetTypes = ReferenceItem::where('category_key', 'ASSET_TYPE')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key'])
            ->map(fn($i) => ['id' => $i->key, 'name' => $i->display_label])
            ->toArray();

        if ($asset) {
            $this->assetId = $asset;
            $this->loadAssetData();
            $this->activeTab = request()->query('tab', 'info');
        } else {
            // Query string ile gelen müşteri verisi varsa yakala
            $customerId = request()->query('customer');
            if ($customerId && collect($this->customers)->firstWhere('id', $customerId)) {
                $this->customer_id = $customerId;
            }
        }
    }
}; ?>

<div>
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=assets"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Varlık Listesi</span>
        </a>

        {{-- Header --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
                    @if($isViewMode)
                        {{ $name }}
                    @elseif($assetId)
                        Düzenle: {{ $name }}
                    @else
                        Yeni Varlık Ekle
                    @endif
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($isViewMode)
                        <span
                            class="text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">Varlık</span>
                        <span class="text-[11px] font-mono text-slate-400">ID: {{ $assetId }}</span>
                    @else
                        <p class="text-sm opacity-60">
                            Yeni varlık bilgilerini girin
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($isViewMode)
                    <button type="button" wire:click="delete" wire:confirm="Bu varlığı silmek istediğinize emin misiniz?"
                        wire:key="btn-delete-{{ $assetId }}"
                        class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                        Sil
                    </button>
                    <button type="button" wire:click="toggleEditMode" wire:key="btn-edit-{{ $assetId }}"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                        Düzenle
                    </button>
                @else
                    <button type="button" wire:click="cancel" wire:key="btn-cancel-{{ $assetId ?: 'new' }}"
                        class="theme-btn-cancel">
                        İptal
                    </button>
                    <button type="button" wire:click="save" wire:loading.attr="disabled"
                        wire:key="btn-save-{{ $assetId ?: 'new' }}" class="theme-btn-save">
                        <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                        <x-mary-icon name="o-check" class="w-4 h-4" />
                        @if($assetId) Güncelle @else Kaydet @endif
                    </button>
                @endif
            </div>
        </div>

        {{-- Tab Navigation --}}
        @if($isViewMode)
            <div class="flex items-center border-b border-slate-200 mb-8 overflow-x-auto scrollbar-hide">
                <button wire:click="$set('activeTab', 'info')"
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'info' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Varlık Bilgileri
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
                            {{-- Varlık Bilgileri Card --}}
                            <div class="theme-card p-6 shadow-sm">
                                <h2 class="text-base font-bold mb-4 text-skin-heading">Varlık Bilgileri
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
                                            <select wire:model="customer_id" class="select w-full">
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
                                        <label class="block text-xs font-medium mb-1 opacity-60">Varlık Adı *</label>
                                        @if($isViewMode)
                                            <div class="text-sm font-medium">{{ $name }}
                                            </div>
                                        @else
                                            <input type="text" wire:model="name" placeholder="Varlık adını girin (Örn: Web Sitesi)"
                                                class="input w-full">
                                            @error('name') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1 opacity-60">Varlık Türü *</label>
                                        @if($isViewMode)
                                            @php $typeName = collect($assetTypes)->firstWhere('id', $type)['name'] ?? $type; @endphp
                                            <div class="text-sm font-medium">{{ $typeName }}
                                            </div>
                                        @else
                                            <select wire:model="type" class="select w-full">
                                                <option value="">Varlık türü seçin</option>
                                                @foreach($assetTypes as $t)
                                                    <option value="{{ $t['id'] }}">{{ $t['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('type') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
                                        @endif
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-xs font-medium mb-1 opacity-60">URL</label>
                                        @if($isViewMode)
                                            <div class="text-sm font-medium">
                                                @if($url)
                                                    <a href="{{ $url }}" target="_blank"
                                                        class="text-blue-500 hover:underline">{{ $url }}</a>
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        @else
                                            <input type="text" wire:model.blur="url" placeholder="https://www.example.com"
                                                class="input w-full">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-4 space-y-6">
                            <div class="theme-card p-6 shadow-sm sticky top-6">
                                <h2 class="text-base font-bold mb-4 text-center text-skin-heading">Kayıt Bilgileri</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base text-center">Varlık ID</label>
                                        <div class="flex items-center justify-center gap-2">
                                            <code class="text-[10px] font-mono bg-[var(--dropdown-hover-bg)] px-2 py-1 rounded text-skin-base">{{ $assetId ?? 'YENİ' }}</code>
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
                    @if($assetId)
                        @livewire('shared.notes-tab', [
                            'entityType' => 'ASSET',
                            'entityId' => $assetId
                        ], key('notes-tab-' . $assetId))
                    @else
                        <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                                <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                    <div class="font-medium">Varlığı kaydedin, ardından not ekleyebilirsiniz</div>
                                </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>