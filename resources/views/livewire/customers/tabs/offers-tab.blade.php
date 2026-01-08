<?php

use App\Services\ReferenceDataService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Offer;
use App\Models\ReferenceItem;
use Illuminate\Database\Eloquent\Builder;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination;
    use Toast;

    public string $search = '';
    public string $letter = '';
    public string $statusFilter = 'all';

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
    public function updatedStatusFilter()
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

        Offer::whereIn('id', $this->selected)->delete();

        $this->success('İşlem Başarılı', count($this->selected) . ' teklif silindi.');
        $this->selected = [];
        $this->selectAll = false;
    }

    private function getQuery(): Builder
    {
        return Offer::query()
            ->with('customer')
            ->withCount('items')
            ->when($this->search, function (Builder $query) {
                $query->where('title', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'ilike', '%' . $this->search . '%');
                    });
            })
            ->when($this->letter, function (Builder $query) {
                if ($this->letter === '0-9') {
                    $query->whereRaw("title ~ '^[0-9]'")
                        ->orWhereHas('customer', function ($q) {
                            $q->whereRaw("name ~ '^[0-9]'");
                        });
                } else {
                    $query->where('title', 'ilike', $this->letter . '%')
                        ->orWhereHas('customer', function ($q) {
                            $q->where('name', 'ilike', $this->letter . '%');
                        });
                }
            })
            ->when($this->statusFilter && $this->statusFilter !== 'all', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc');
    }

    public function with(ReferenceDataService $service): array
    {
        $statusOptions = ReferenceItem::where('category_key', 'OFFER_STATUS')->where('is_active', true)->orderBy('sort_order')->get();

        // Prepare map with both label and color
        $statusMap = [];
        foreach ($statusOptions as $opt) {
            $colorId = $opt->metadata['color'] ?? 'gray';
            $statusMap[$opt->key] = [
                'label' => $opt->display_label,
                'class' => $service->getColorClasses($colorId)
            ];
        }

        return [
            'offers' => $this->getQuery()->paginate($this->perPage),
            'statusOptions' => $statusOptions,
            'statusMap' => $statusMap,
        ];
    }
}; ?>

{{-- Teklifler Tab --}}
<div>
    {{-- Header with Action Button --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold" style="color: var(--color-text-heading);">Teklifler</h2>
            <p class="text-sm opacity-60" style="color: var(--color-text-base);">Tüm müşteri tekliflerini görüntüleyin
                ve yönetin</p>
        </div>
        <div class="flex items-center gap-4">
            @if(count($selected) > 0)
                <button wire:click="deleteSelected"
                    wire:confirm="Seçili {{ count($selected) }} teklifi silmek istediğinize emin misiniz?"
                    class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                    Seçilileri Sil ({{ count($selected) }})
                </button>
            @endif

            <span class="text-sm opacity-60" style="color: var(--color-text-base);">{{ $offers->total() }} teklif</span>
            <x-customer-management.action-button label="Yeni Teklif" href="/dashboard/customers/offers/create" />
        </div>
    </div>

    {{-- Filter Panel --}}
    <x-mary-card class="theme-card shadow-sm mb-6" shadow separator>
        <div class="flex flex-wrap items-center gap-3">
            <div class="w-40">
                <x-mary-select :options="[['id' => 'all', 'display_label' => 'Tüm Durumlar']] + $statusOptions->map(fn($i) => ['id' => $i->key, 'display_label' => $i->display_label])->toArray()"
                    option-label="display_label" option-value="id" wire:model.live="statusFilter"
                    class="select-sm !bg-white !border-gray-200 text-xs" />
            </div>

            <div class="flex-grow max-w-[12rem]">
                <x-mary-input placeholder="Ara..." icon="o-magnifying-glass"
                    class="input-sm !bg-white !border-gray-200 text-xs" wire:model.live.debounce.300ms="search" />
            </div>

            <div class="flex items-center gap-1 ml-auto flex-wrap justify-end">
                <x-mary-button label="0-9" wire:click="$set('letter', '0-9')"
                    class="btn-ghost btn-xs font-medium {{ $letter === '0-9' ? 'bg-slate-200 text-slate-700' : 'text-slate-500' }} hover:bg-slate-100 px-2" />
                <x-mary-button label="Tümü" wire:click="$set('letter', '')"
                    class="btn-ghost btn-xs font-medium {{ $letter === '' ? 'bg-slate-200 text-slate-700' : 'text-slate-500' }} hover:bg-slate-100 px-2" />
                <div class="divider divider-horizontal mx-0 h-4"></div>
                @foreach(range('A', 'Z') as $char)
                    <x-mary-button :label="$char" wire:click="$set('letter', '{{ $char }}')"
                        class="btn-ghost btn-xs font-medium {{ $letter === $char ? 'bg-slate-200 text-slate-700' : 'text-slate-500' }} hover:bg-slate-100 min-w-[24px] !px-1" />
                @endforeach
            </div>
        </div>
    </x-mary-card>

    {{-- Data Table --}}
    @php
        $headers = [
            ['label' => '', 'sortable' => false, 'width' => '40px'],
            ['label' => 'Teklif Başlığı', 'sortable' => true],
            ['label' => 'Teklif Durumu', 'sortable' => false],
            ['label' => 'Hizmet Sayısı', 'sortable' => false, 'align' => 'center'],
            ['label' => 'Teklif Tarihi', 'sortable' => true],
            ['label' => 'Geçerlilik Tarihi', 'sortable' => true],
            ['label' => 'Müşteri', 'sortable' => true],
            ['label' => 'Fiyat (KDV Hariç)', 'sortable' => true, 'align' => 'right'],
        ];
    @endphp

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="checkbox checkbox-xs rounded border-slate-300">
                        </th>
                        @foreach(array_slice($headers, 1) as $header)
                            <th
                                class="px-6 py-3 font-semibold text-slate-700 {{ isset($header['align']) && $header['align'] == 'center' ? 'text-center' : (isset($header['align']) && $header['align'] == 'right' ? 'text-right' : '') }}">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($offers as $offer)
                        @php
                            $char = mb_substr($offer->title, 0, 1);
                        @endphp
                        <tr class="group hover:bg-[var(--list-card-hover-bg)] transition-all duration-200 cursor-pointer"
                            onclick="window.location.href='/dashboard/customers/offers/{{ $offer->id }}'">
                            <td class="px-6 py-4" onclick="event.stopPropagation()">
                                <input type="checkbox" wire:model.live="selected" value="{{ $offer->id }}"
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
                                    <div>
                                        <div class="text-[13px] group-hover:opacity-80 transition-opacity"
                                            style="color: var(--color-text-heading);">
                                            {{ $offer->title }}
                                        </div>
                                        @if(!empty($offer->offer_no))
                                            <div class="text-[11px] font-medium opacity-60"
                                                style="color: var(--color-text-base);">
                                                {{ $offer->offer_no }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusData = $statusMap[$offer->status] ?? null;
                                    $statusLabel = $statusData['label'] ?? $offer->status;
                                    $statusClass = $statusData['class'] ?? 'bg-slate-100 text-slate-500 border border-slate-200';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[11px]"
                                    style="background-color: var(--card-bg); color: var(--color-text-heading); border: 1px solid var(--card-border);">
                                    {{ $offer->items_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-[12px] font-mono text-center opacity-70"
                                style="color: var(--color-text-base);">
                                {{ $offer->created_at->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 text-[12px] font-mono text-center opacity-70"
                                style="color: var(--color-text-base);">
                                {{ $offer->valid_until?->format('d.m.Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-[13px] font-medium" style="color: var(--color-text-base);">
                                {{ $offer->customer->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right text-[13px]" style="color: var(--color-text-heading);">
                                {{ number_format($offer->total_amount, 2) }} {{ $offer->currency }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <x-mary-icon name="o-document-text" class="w-12 h-12 opacity-20 mb-4" />
                                    <div class="font-medium">Henüz teklif kaydı bulunmuyor</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">Göster:</span>
                <select wire:model.live="perPage"
                    class="select select-xs bg-white border-slate-200 text-xs w-18 h-8 min-h-0 focus:outline-none focus:border-slate-400">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                </select>
            </div>

            <div>
                {{ $offers->links() }}
            </div>

            <div class="text-[10px] text-slate-400 font-mono">
                {{ number_format(microtime(true) - (defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT')), 3) }}s
            </div>
        </div>
    </div>
</div>