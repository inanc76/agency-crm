<?php

use App\Services\ReferenceDataService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Asset;
use App\Models\ReferenceItem;
use Illuminate\Database\Eloquent\Builder;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination;
    use Toast;

    public string $search = '';
    public string $letter = '';
    public string $typeFilter = 'all';

    // Pagination & Selection
    public int $perPage = 25;
    public array $selected = [];
    public bool $selectAll = false;

    // Reset pagination when filtering
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedLetter()
    {
        $this->resetPage();
    }
    public function updatedTypeFilter()
    {
        $this->resetPage();
    }
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) {
            return;
        }

        Asset::whereIn('id', $this->selected)->delete();

        $this->success('İşlem Başarılı', count($this->selected) . ' varlık silindi.');
        $this->selected = [];
        $this->selectAll = false;
    }

    private function getQuery(): Builder
    {
        return Asset::query()
            ->with(['customer', 'type_item'])
            ->when($this->search, function (Builder $query) {
                $query->where('name', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'ilike', '%' . $this->search . '%');
                    });
            })
            ->when($this->letter, function (Builder $query) {
                if ($this->letter === '0-9') {
                    $query->whereRaw("name ~ '^[0-9]'")
                        ->orWhereHas('customer', function ($q) {
                            $q->whereRaw("name ~ '^[0-9]'");
                        });
                } else {
                    $query->where('name', 'ilike', $this->letter . '%')
                        ->orWhereHas('customer', function ($q) {
                            $q->where('name', 'ilike', $this->letter . '%');
                        });
                }
            })
            ->when($this->typeFilter && $this->typeFilter !== 'all', function (Builder $query) {
                $query->where('type', $this->typeFilter);
            })
            ->orderBy('name');
    }

    public function with(): array
    {
        $typeOptions = ReferenceItem::where('category_key', 'ASSET_TYPE')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key']);

        return [
            'assets' => $this->getQuery()->paginate($this->perPage),
            'typeOptions' => $typeOptions,
        ];
    }
}; ?>

{{-- Varlıklar Tab --}}
<div>
    @include('livewire.customers.tabs.partials._assets-header', ['selected' => $selected, 'assets' => $assets])

    {{-- Filter Panel --}}
    <x-mary-card class="theme-card shadow-sm mb-6" shadow separator>
        <div class="flex flex-wrap items-center gap-4">
            <div class="w-40">
                <x-mary-select :options="[['id' => 'all', 'display_label' => 'Tüm Varlık Türleri']] + $typeOptions->map(fn($i) => ['id' => $i->key, 'display_label' => $i->display_label])->toArray()"
                    option-label="display_label" option-value="id" wire:model.live="typeFilter"
                    class="select-sm !bg-white !border-gray-200" style="background-color: white !important;" />
            </div>

            <div class="flex-grow max-w-[10rem]">
                <x-mary-input placeholder="Ara..." icon="o-magnifying-glass" class="input-sm !bg-white !border-gray-200"
                    style="background-color: white !important;" wire:model.live.debounce.300ms="search" />
            </div>

            <div class="flex items-center gap-1 ml-auto flex-wrap justify-end">
                <x-mary-button label="0-9" wire:click="$set('letter', '0-9')"
                    class="btn-ghost btn-xs font-medium {{ $letter === '0-9' ? 'bg-skin-hover text-skin-base' : 'text-skin-muted' }} hover:bg-skin-hover px-1.5" />
                <x-mary-button label="Tümü" wire:click="$set('letter', '')"
                    class="btn-ghost btn-xs font-medium {{ $letter === '' ? 'bg-skin-hover text-skin-base' : 'text-skin-muted' }} hover:bg-skin-hover px-1.5" />
                <div class="divider divider-horizontal mx-0 h-4"></div>
                @foreach(range('A', 'Z') as $char)
                    <x-mary-button :label="$char" wire:click="$set('letter', '{{ $char }}')"
                        class="btn-ghost btn-xs font-medium text-[10px] {{ $letter === $char ? 'bg-skin-hover text-skin-base' : 'text-skin-muted' }} hover:bg-skin-hover min-w-[20px] !px-0.5" />
                @endforeach
            </div>
        </div>
    </x-mary-card>

    {{-- Data Table --}}
    @php
        $headers = [
            ['label' => '', 'sortable' => false, 'width' => '40px'],
            ['label' => 'Varlık', 'sortable' => true],
            ['label' => 'Tür', 'sortable' => true],
            ['label' => 'Müşteri', 'sortable' => true],
            ['label' => 'URL', 'sortable' => false],
        ];
    @endphp

    <div class="bg-white rounded-xl border border-skin-light shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="agency-table">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="checkbox checkbox-xs rounded border-slate-300">
                        </th>
                        @foreach(array_slice($headers, 1) as $header)
                            <th>
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                        @include('livewire.customers.tabs.partials._assets-row', ['asset' => $asset, 'selected' => $selected])
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-skin-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-mary-icon name="o-computer-desktop" class="w-12 h-12 opacity-20 mb-4" />
                                    <div class="font-medium">Henüz varlık kaydı bulunmuyor</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-skin-light flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs text-skin-muted">Göster:</span>
                <select wire:model.live="perPage"
                    class="select select-xs bg-white border-skin-light text-xs w-18 h-8 min-h-0 focus:outline-none focus:border-slate-400">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                </select>
            </div>

            <div>
                {{ $assets->links() }}
            </div>

            <div class="text-[10px] text-skin-muted font-mono">
                {{ number_format(microtime(true) - (defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT')), 3) }}s
            </div>
        </div>
    </div>
</div>