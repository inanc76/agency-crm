<?php

use App\Services\ReferenceDataService;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Contact;
use App\Models\ReferenceItem;
use Illuminate\Database\Eloquent\Builder;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination;
    use Toast;

    public string $customerId = '';
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

        Contact::whereIn('id', $this->selected)->delete();

        $this->dispatch('contacts-updated');

        $this->success('İşlem Başarılı', count($this->selected) . ' kişi silindi.');
        $this->selected = [];
        $this->selectAll = false;
    }

    private function getQuery(): Builder
    {
        return Contact::query()
            ->with('customer')
            ->when($this->customerId, fn($q) => $q->where('customer_id', $this->customerId))
            ->when($this->search, function (Builder $query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->letter, function (Builder $query) {
                if ($this->letter === '0-9') {
                    $query->whereRaw("name ~ '^[0-9]'")
                        ->orWhereHas('customer', function ($q) {
                            $q->whereRaw("name ~ '^[0-9]'");
                        });
                } else {
                    $query->where('name', 'like', $this->letter . '%')
                        ->orWhereHas('customer', function ($q) {
                            $q->where('name', 'like', $this->letter . '%');
                        });
                }
            })
            ->when($this->statusFilter && $this->statusFilter !== 'all', function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('name');
    }

    public function with(ReferenceDataService $service): array
    {
        $statusOptions = ReferenceItem::where('category_key', 'CONTACT_STATUS')->where('is_active', true)->orderBy('sort_order')->get();
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
            'contacts' => $this->getQuery()->paginate($this->perPage),
            'statusOptions' => $statusOptions,
            'statusMap' => $statusMap,
        ];
    }
}; ?>

{{-- Kişiler Tab --}}
<div>
    {{-- Header with Action Button --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold" class="text-skin-heading">Kişiler</h2>
            <p class="text-sm opacity-60">Tüm iletişim kişilerini görüntüleyin ve
                yönetin</p>
        </div>
        <div class="flex items-center gap-4">
            @if(count($selected) > 0)
                <button wire:click="deleteSelected"
                    wire:confirm="Seçili {{ count($selected) }} kişiyi silmek istediğinize emin misiniz?"
                    class="btn-danger-outline">
                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                    Seçilileri Sil ({{ count($selected) }})
                </button>
            @endif

            <span class="text-sm opacity-60">{{ $contacts->total() }} kişi</span>
            <x-customer-management.action-button label="Yeni Kişi" href="{{ route('customers.contacts.create') }}" />
        </div>
    </div>

    {{-- Filter Panel --}}
    <x-mary-card class="theme-card shadow-sm mb-6" shadow separator>
        <div class="flex flex-wrap items-center gap-4">
            <div class="w-48">
                <x-mary-select :options="[['id' => 'all', 'display_label' => 'Tüm Durumlar']] + $statusOptions->map(fn($i) => ['id' => $i->key, 'display_label' => $i->display_label])->toArray()"
                    option-label="display_label" option-value="id" wire:model.live="statusFilter"
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
            ['label' => 'Kişi', 'sortable' => true],
            ['label' => 'Durum', 'sortable' => true],
            ['label' => 'Pozisyon', 'sortable' => true],
            ['label' => 'Müşteri', 'sortable' => true],
            ['label' => 'E-posta', 'sortable' => false],
            ['label' => 'Telefon', 'sortable' => false],
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
                    @forelse($contacts as $contact)
                        @php
                            $char = mb_substr($contact->name, 0, 1);
                        @endphp
                        <tr class="group hover:bg-[var(--list-card-hover-bg)] transition-all duration-200 cursor-pointer"
                            onclick="window.location.href='/dashboard/customers/contacts/{{ $contact->id }}'">
                            <td class="px-6 py-4" onclick="event.stopPropagation()">
                                <input type="checkbox" wire:model.live="selected" value="{{ $contact->id }}"
                                    class="checkbox checkbox-xs rounded border-slate-300">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-6 text-center">
                                        @if($contact->gender === 'MALE')
                                            {{-- Male Symbol (Mars) --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="text-blue-500">
                                                <path d="M16 3h5v5"></path>
                                                <path d="M21 3l-7 7"></path>
                                                <circle cx="10" cy="14" r="7"></circle>
                                            </svg>
                                        @elseif($contact->gender === 'FEMALE')
                                            {{-- Female Symbol (Venus) --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="text-pink-500">
                                                <path d="M12 15v7"></path>
                                                <path d="M9 19h6"></path>
                                                <circle cx="12" cy="9" r="6"></circle>
                                            </svg>
                                        @else
                                            {{-- Unknown (Question Mark) --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="text-gray-400">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                                <path d="M12 17h.01"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="text-[13px] group-hover:opacity-80 transition-opacity"
                                        style="color: var(--list-card-link-color);">
                                        {{ $contact->name }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusData = $statusMap[$contact->status] ?? null;
                                    $statusLabel = $statusData['label'] ?? $contact->status;
                                    $statusClass = $statusData['class'] ?? 'bg-skin-hover text-skin-muted border border-skin-light';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-[12px] opacity-70">
                                {{ $contact->position ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-[13px] font-medium">
                                {{ $contact->customer->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-[13px] opacity-70">
                                {{ $contact->email ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-[12px] font-mono opacity-70">
                                {{ $contact->phone ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-skin-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-mary-icon name="o-users" class="w-12 h-12 opacity-20 mb-4" />
                                    <div class="font-medium">Henüz kişi kaydı bulunmuyor</div>
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
                {{ $contacts->links() }}
            </div>

            <div class="text-[10px] text-skin-muted font-mono">
                {{ number_format(microtime(true) - (defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT')), 3) }}s
            </div>
        </div>
    </div>
</div>