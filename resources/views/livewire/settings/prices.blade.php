<?php
/**
 * 🛡️ PRICES SETTINGS (PURE FUNCTIONAL VOLT)
 * ---------------------------------------------------------
 * ARCHITECTURE: Functional Livewire Volt.
 * UI DESIGN: Balanced Architecture (_price-list, _price-form).
 * SECURITY: Restricted via 'settings.view' and 'settings.edit' gates.
 * VALIDATION: Strict numeric and required checks.
 * ---------------------------------------------------------
 */

use App\Models\PriceDefinition;
use App\Models\ReferenceItem;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;

// Functional API imports
use function Livewire\Volt\{state, mount, uses, with, layout};

// Define Layout
layout('components.layouts.app', ['title' => 'Fiyat Tanımları']);

// Traits
uses(Toast::class);

// State
state([
    // Filters & Search
    'search' => '',
    'filterCategory' => '',
    'filterDuration' => '',

    // Modal State
    'showModal' => false,
    'selectedId' => null,

    // Form State
    'name' => '',
    'category' => '',
    'duration' => '',
    'price' => 0,
    'currency' => 'TRY',
    'description' => '',
    'is_active' => true,
]);

// Mounting
mount(function () {
    $this->authorize('settings.view');
});

// Computed Properties via with()
with(function () {
    $prices = PriceDefinition::query()
        ->when($this->search, function ($q) {
            $search = mb_strtolower($this->search);
            $q->where(function ($sq) use ($search) {
                $sq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        })
        ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
        ->when($this->filterDuration, fn($q) => $q->where('duration', $this->filterDuration))
        ->orderBy('created_at', 'desc')
        ->get();

    $categories = ReferenceItem::where('category_key', 'SERVICE_CATEGORY')
        ->where('is_active', true)
        ->orderBy('display_label')
        ->get();

    $durations = ReferenceItem::where('category_key', 'SERVICE_EXTENSION_YEARS')
        ->where('is_active', true)
        ->orderBy('sort_order', 'asc')
        ->orderBy('created_at', 'asc')
        ->get();

    $currencies = ReferenceItem::where('category_key', 'CURRENCY')
        ->where('is_active', true)
        ->get();

    return [
        'prices' => $prices,
        'categories' => $categories,
        'durations' => $durations,
        'currencies' => $currencies,
    ];
});

// Actions
$openCreateModal = function () {
    $this->authorize('settings.edit');
    $this->resetForm();
    $this->showModal = true;
};

$edit = function ($id) {
    $this->authorize('settings.edit');
    $price = PriceDefinition::findOrFail($id);
    $this->selectedId = $price->id;
    $this->name = $price->name;
    $this->category = $price->category;
    $this->duration = $price->duration;
    $this->price = $price->price;
    $this->currency = $price->currency;
    $this->description = $price->description ?? '';
    $this->is_active = $price->is_active;

    $this->showModal = true;
};

$save = function () {
    $this->authorize('settings.edit');
    $this->validate([
        'name' => 'required|string|max:255',
        'category' => 'required|string',
        'duration' => 'required|string',
        'price' => 'required|numeric|min:0',
        'currency' => 'required|string',
        'description' => 'nullable|string',
    ]);

    try {
        DB::transaction(function () {
            $data = [
                'name' => $this->name,
                'category' => $this->category,
                'duration' => $this->duration,
                'price' => $this->price,
                'currency' => $this->currency,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ];

            if ($this->selectedId) {
                PriceDefinition::findOrFail($this->selectedId)->update($data);
                $this->success('Fiyat tanımı güncellendi.');
            } else {
                PriceDefinition::create($data);
                $this->success('Yeni fiyat tanımı oluşturuldu.');
            }
        });

        $this->showModal = false;
        $this->resetForm();
    } catch (\Exception $e) {
        $this->error('Bir hata oluştu: ' . $e->getMessage());
    }
};

$toggleStatus = function ($id) {
    $this->authorize('settings.edit');
    $price = PriceDefinition::findOrFail($id);
    $price->update(['is_active' => !$price->is_active]);
    $this->success('Durum güncellendi.');
};

$delete = function ($id) {
    $this->authorize('settings.edit');
    PriceDefinition::findOrFail($id)->delete();
    $this->success('Fiyat tanımı silindi.');
};

$clearFilters = function () {
    $this->search = '';
    $this->filterCategory = '';
    $this->filterDuration = '';
};

$resetForm = function () {
    $this->selectedId = null;
    $this->name = '';
    $this->category = '';
    $this->duration = '';
    $this->price = 0;
    $this->currency = 'TRY';
    $this->description = '';
    $this->is_active = true;
};

?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="w-full lg:w-3/4 mx-auto">
        {{-- Breadcrumbs & Back Button --}}
        <div class="mb-6">
            <a href="/dashboard/settings"
                class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading transition-colors group">
                <x-mary-icon name="o-arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
                <span class="text-sm font-medium">Geri</span>
            </a>
        </div>

        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-skin-heading">Fiyat Tanımları</h1>
                <p class="text-sm opacity-60 mt-1">Hizmet fiyat tanımlarını görüntüleyin ve yönetin</p>
            </div>
            @can('settings.edit')
                <button class="theme-btn-save" wire:click="openCreateModal">
                    <x-mary-icon name="o-plus" class="w-4 h-4 mr-1" />
                    Yeni Fiyat Ekle
                </button>
            @endcan
        </div>

        {{-- Filters Card --}}
        <div class="theme-card p-6 shadow-sm mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-mary-input label="Arama" placeholder="Hizmet adı veya açıklama..." icon="o-magnifying-glass"
                    wire:model.live.debounce.300ms="search" />

                <x-mary-select label="Kategori" placeholder="Tüm Kategoriler" :options="$categories" option-value="key"
                    option-label="display_label" wire:model.live="filterCategory" />

                <x-mary-select label="Hizmet Süresi" placeholder="Tüm Süreler" :options="$durations" option-value="key"
                    option-label="display_label" wire:model.live="filterDuration" />

                <div class="flex items-end">
                    <x-mary-button label="Filtreleri Temizle" icon="o-x-mark" class="btn-ghost"
                        wire:click="clearFilters" />
                </div>
            </div>
        </div>

        <div class="mb-4 text-sm opacity-60">
            {{ $prices->count() }} tanım gösteriliyor (toplam {{ \App\Models\PriceDefinition::count() }} tanımdan)
        </div>

        {{-- Table Partial (uses $prices from with()) --}}
        @include('livewire.settings.parts._price-list', [
            'prices' => $prices,
            'categories' => $categories,
            'durations' => $durations
        ])
    </div>
    {{-- Create/Edit Modal --}}
    <x-mary-modal wire:model="showModal" title="{{ $selectedId ? 'Fiyat Tanımı Düzenle' : 'Yeni Fiyat Tanımı Ekle' }}"
        class="backdrop-blur" box-class="!max-w-2xl">
        
        {{-- Form Partial --}}
        @include('livewire.settings.parts._price-form', [
            'categories' => $categories,
            'durations' => $durations,
            'currencies' => $currencies,
            'is_active' => $is_active
        ])

        <x-slot:actions>
            <x-mary-button label="İptal" class="btn-ghost" wire:click="$set('showModal', false)" />
            <x-mary-button label="Kaydet" class="btn-success"
                style="background-color: var(--brand-success); color: white; border: none;" wire:click="save" />
        </x-slot:actions>
    </x-mary-modal>
</div>