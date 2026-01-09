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
            ->with('customer')
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

    public function with(ReferenceDataService $service): array
    {
        $typeOptions = ReferenceItem::where('category_key', 'ASSET_TYPE')->where('is_active', true)->orderBy('sort_order')->get();
        // Prepare map with both label and color
        $typeMap = [];
        foreach ($typeOptions as $opt) {
            $colorId = $opt->metadata['color'] ?? 'gray';
            $typeMap[$opt->key] = [
                'label' => $opt->display_label,
                'class' => $service->getColorClasses($colorId)
            ];
        }

        return [
            'assets' => $this->getQuery()->paginate($this->perPage),
            'typeOptions' => $typeOptions,
            'typeMap' => $typeMap,
        ];
    }
}; ?>

{{-- Varlıklar Tab --}}
<div>
    {{-- Header with Action Button --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold" class="text-skin-heading">Varlıklar</h2>
            <p class="text-sm opacity-60">Müşterilere ait varlıkları görüntüleyin
                ve yönetin</p>
        </div>
        <div class="flex items-center gap-4">
            @if(count($selected) > 0)
                <button wire:click="deleteSelected"
                    wire:confirm="Seçili {{ count($selected) }} varlığı silmek istediğinize emin misiniz?"
                    class="btn-danger-outline">
                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                    Seçilileri Sil ({{ count($selected) }})
                </button>
            @endif

            <span class="text-sm opacity-60">{{ $assets->total() }} varlık</span>
            <x-customer-management.action-button label="Yeni Varlık" href="{{ route('customers.assets.create') }}" />
        </div>
    </div>

    {{-- Filter Panel --}}
    <x-mary-card class="theme-card shadow-sm mb-6" shadow separator>
        <div class="flex flex-wrap items-center gap-4">
            <div class="w-48">
                {{-- Assuming 'type' filter property is not explicitly defined in the component yet, using search for
                now or would need to add it --}}
                {{-- Since only search and letter and perPage are defined, I'll add type filter support if possible or
                just stick to existing ones if 'type' is not passed.
                Wait, I need to add $typeFilter to the component. Let's do that in a separate chunk or assume it
                exists/I add it.
                I will add $typeFilter to the component property list in another chunk. --}}
                <x-mary-select :options="[['id' => 'all', 'display_label' => 'Tüm Varlık Türleri']] + $typeOptions->map(fn($i) => ['id' => $i->key, 'display_label' => $i->display_label])->toArray()"
                    option-label="display_label" option-value="id" wire:model.live="typeFilter"
                    class="select-sm !bg-white !border-gray-200" />
            </div>

            <div class="flex-grow max-w-xs">
                <x-mary-input placeholder="Ara..." icon="o-magnifying-glass" class="input-sm !bg-white !border-gray-200"
                    wire:model.live.debounce.300ms="search" />
            </div>

            <div class="flex items-center gap-1 ml-auto flex-wrap justify-end">
                <x-mary-button label="0-9" wire:click="$set('letter', '0-9')"
                    class="btn-ghost btn-xs font-medium {{ $letter === '0-9' ? 'bg-skin-hover text-skin-base' : 'text-skin-muted' }} hover:bg-skin-hover px-2" />
                <x-mary-button label="Tümü" wire:click="$set('letter', '')"
                    class="btn-ghost btn-xs font-medium {{ $letter === '' ? 'bg-skin-hover text-skin-base' : 'text-skin-muted' }} hover:bg-skin-hover px-2" />
                <div class="divider divider-horizontal mx-0 h-4"></div>
                @foreach(range('A', 'Z') as $char)
                    <x-mary-button :label="$char" wire:click="$set('letter', '{{ $char }}')"
                        class="btn-ghost btn-xs font-medium {{ $letter === $char ? 'bg-skin-hover text-skin-base' : 'text-skin-muted' }} hover:bg-skin-hover min-w-[24px] !px-1" />
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
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-skin-light">
                    <tr>
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="checkbox checkbox-xs rounded border-slate-300">
                        </th>
                        @foreach(array_slice($headers, 1) as $header)
                            <th class="px-6 py-3 font-semibold text-skin-base">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assets as $asset)
                        @php
                            $char = mb_substr($asset->name, 0, 1);
                        @endphp
                        <tr class="group hover:bg-[var(--list-card-hover-bg)] transition-all duration-200 cursor-pointer"
                            onclick="window.location.href='/dashboard/customers/assets/{{ $asset->id }}'">
                            <td class="px-6 py-4" onclick="event.stopPropagation()">
                                <input type="checkbox" wire:model.live="selected" value="{{ $asset->id }}"
                                    class="checkbox checkbox-xs rounded border-slate-300">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs shadow-sm"
                                            style="background-color: var(--table-avatar-bg); color: var(--table-avatar-text); border: 1px solid var(--table-avatar-border);">
                                            {{ $char }}
                                        </div>
                                    </div>
                                    <div class="text-[13px] group-hover:opacity-80 transition-opacity"
                                        style="color: var(--list-card-link-color);">
                                        {{ $asset->name }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $typeData = $typeMap[$asset->type] ?? null;
                                    $typeLabel = $typeData['label'] ?? $asset->type;
                                    $typeClass = $typeData['class'] ?? 'bg-skin-hover text-skin-muted border border-skin-light';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $typeClass }}">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-[13px] font-medium">
                                {{ $asset->customer->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-[12px] font-medium hover:underline">
                                @if($asset->url)
                                    <a href="{{ $asset->url }}" target="_blank"
                                        style="color: var(--action-link-color);">{{ parse_url($asset->url, PHP_URL_HOST) ?: $asset->url }}</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
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