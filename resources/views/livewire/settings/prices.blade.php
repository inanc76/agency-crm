<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\PriceDefinition;
use App\Models\ReferenceItem;
use App\Repositories\ReferenceDataRepository;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\DB;

new #[Layout('components.layouts.app', ['title' => 'Fiyat Tanımları'])]
    class extends Component {
    use Toast;

    // Filters & Search
    public string $search = '';
    public string $filterCategory = '';
    public string $filterDuration = '';

    // Modal State
    public bool $showModal = false;
    public ?string $selectedId = null;

    // Form State
    public string $name = '';
    public string $category = '';
    public string $duration = '';
    public float $price = 0;
    public string $currency = 'TRY';
    public string $description = '';
    public bool $is_active = true;

    public function with(): array
    {
        $prices = PriceDefinition::query()
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('name', 'ilike', "%{$this->search}%")
                        ->orWhere('description', 'ilike', "%{$this->search}%");
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
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
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
    }

    public function save(): void
    {
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
    }

    public function toggleStatus(string $id): void
    {
        $price = PriceDefinition::findOrFail($id);
        $price->update(['is_active' => !$price->is_active]);
        $this->success('Durum güncellendi.');
    }

    public function delete(string $id): void
    {
        PriceDefinition::findOrFail($id)->delete();
        $this->success('Fiyat tanımı silindi.');
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterCategory', 'filterDuration']);
    }

    private function resetForm(): void
    {
        $this->reset(['selectedId', 'name', 'category', 'duration', 'price', 'currency', 'description', 'is_active']);
        $this->currency = 'TRY';
        $this->is_active = true;
        $this->price = 0;
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="w-full lg:w-3/4 mx-auto">
        {{-- Breadcrumbs & Back Button --}}
        <div class="mb-6">
            <a href="/dashboard/settings"
                class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 transition-colors group">
                <x-mary-icon name="o-arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
                <span class="text-sm font-medium">Geri</span>
            </a>
        </div>

        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold" style="color: var(--color-text-heading);">Fiyat Tanımları</h1>
                <p class="text-sm opacity-60 mt-1" style="color: var(--color-text-base);">Hizmet fiyat tanımlarını
                    görüntüleyin ve yönetin</p>
            </div>
            <button class="theme-btn-save" wire:click="openCreateModal">
                <x-mary-icon name="o-plus" class="w-4 h-4 mr-1" />
                Yeni Fiyat Ekle
            </button>
        </div>

        {{-- Filters Card --}}
        <div class="bg-[var(--color-info-bg)] border border-[var(--color-info-border)] rounded-xl shadow-sm p-6 mb-6">
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

        {{-- Table Card --}}
        <div
            class="bg-[var(--color-info-bg)] border border-[var(--color-info-border)] rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50">
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">Durum</th>
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">Hizmet Adı
                            </th>
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">Kategori
                            </th>
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">Süre</th>
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">Fiyat</th>
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">Oluşturulma
                            </th>
                            <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider opacity-50">İşlemler
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($prices as $price)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-2 h-2 rounded-full {{ $price->is_active ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]' : 'bg-slate-300' }}">
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm" style="color: var(--color-text-heading);">
                                    {{ $price->name }}
                                </td>
                                <td class="px-4 py-4 text-sm opacity-70">
                                    {{ $categories->firstWhere('key', $price->category)?->display_label ?? $price->category }}
                                </td>
                                <td class="px-4 py-4 text-sm opacity-70">
                                    {{ $durations->firstWhere('key', $price->duration)?->display_label ?? $price->duration }}
                                </td>
                                <td class="px-4 py-4 text-sm" style="color: var(--color-text-heading);">
                                    {{ number_format($price->price, 2) }} {{ $price->currency }}
                                </td>
                                <td class="px-4 py-4 text-sm opacity-60">{{ $price->created_at->format('d.m.Y') }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-1">
                                        <x-mary-button icon="o-pencil" class="btn-ghost btn-xs text-slate-400"
                                            wire:click="edit('{{ $price->id }}')" />
                                        <x-mary-button icon="o-trash" class="btn-ghost btn-xs text-slate-400"
                                            wire:click="delete('{{ $price->id }}')"
                                            wire:confirm="Bu fiyat tanımını silmek istediğinize emin misiniz?" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-40">
                                        <x-mary-icon name="o-banknotes" class="w-12 h-12 mb-2" />
                                        <p class="text-sm font-medium">Herhangi bir fiyat tanımı bulunamadı.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <x-mary-modal wire:model="showModal" title="{{ $selectedId ? 'Fiyat Tanımı Düzenle' : 'Yeni Fiyat Tanımı Ekle' }}"
        class="backdrop-blur" box-class="!max-w-2xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <x-mary-input label="Hizmet Adı *" placeholder="Örn: Premium Domain, SSL Sertifikası"
                    wire:model="name" />
            </div>

            <x-mary-select label="Hizmet Kategorisi *" placeholder="Kategori Seçin" :options="$categories"
                option-value="key" option-label="display_label" wire:model="category" />

            <x-mary-select label="Hizmet Süresi *" placeholder="Süre Seçin" :options="$durations" option-value="key"
                option-label="display_label" wire:model="duration" />

            <x-mary-input label="Fiyat *" type="number" wire:model="price" />

            <x-mary-select label="Para Birimi *" placeholder="Para Birimi Seçin" :options="$currencies"
                option-value="key" option-label="display_label" wire:model="currency" />

            <div class="md:col-span-2">
                <x-mary-textarea label="Açıklama" placeholder="Hizmet detaylarını açıklayın..." rows="4"
                    wire:model="description" />
            </div>

            <div class="md:col-span-2 flex items-center gap-3">
                <span class="text-sm font-medium {{ !$is_active ? 'text-red-500' : 'text-slate-400' }}">Pasif</span>
                <x-mary-toggle wire:model="is_active" class="toggle-success" />
                <span class="text-sm font-medium {{ $is_active ? 'text-green-500' : 'text-slate-400' }}">Aktif</span>
            </div>
        </div>

        <x-slot:actions>
            <x-mary-button label="İptal" class="btn-ghost" wire:click="$set('showModal', false)" />
            <x-mary-button label="Kaydet" class="btn-success"
                style="background-color: var(--brand-success); color: white; border: none;" wire:click="save" />
        </x-slot:actions>
    </x-mary-modal>
</div>