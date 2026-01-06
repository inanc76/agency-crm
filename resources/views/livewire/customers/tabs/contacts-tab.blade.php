<?php

use Livewire\Volt\Component;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    public string $search = '';
    public string $letter = '';

    public function with(): array
    {
        $contacts = Contact::query()
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
            ->orderBy('name')
            ->get();

        return [
            'contacts' => $contacts,
        ];
    }
}; ?>

{{-- Kişiler Tab --}}
<div>
    {{-- Header with Action Button --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Kişiler</h2>
            <p class="text-sm text-gray-500">Tüm iletişim kişilerini görüntüleyin ve yönetin</p>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500">{{ $contacts->count() }} kişi</span>
            <x-customer-management.action-button label="Yeni Kişi" href="#" />
        </div>
    </div>

    {{-- Filter Panel --}}
    <x-customer-management.filter-panel :showCategories="false" :showAlphabet="true" statusLabel="Duruma Göre Filtrele"
        :letter="$letter" />

    {{-- Data Table --}}
    @php
        $headers = [
            ['label' => 'Kişi', 'sortable' => true],
            ['label' => 'Müşteri', 'sortable' => true],
            ['label' => 'E-posta', 'sortable' => false],
            ['label' => 'Telefon', 'sortable' => false],
            ['label' => 'Pozisyon', 'sortable' => true],
            ['label' => 'Durum', 'sortable' => true],
        ];
    @endphp

    <x-customer-management.data-table :headers="$headers" emptyMessage="Henüz kişi kaydı bulunmuyor">
        @foreach($contacts as $contact)
            @php
                $char = mb_substr($contact->name, 0, 1);
            @endphp
            <tr class="group hover:bg-slate-50/80 transition-all duration-200 cursor-pointer"
                onclick="window.location.href='/dashboard/customers/{{ $contact->customer_id }}?tab=contacts'">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <x-mary-avatar placeholder="{{ $char }}"
                                class="!w-9 !h-9 bg-white text-black font-semibold text-xs border border-gray-100 shadow-sm" />
                        </div>
                        <div class="font-bold text-slate-700 text-[13px] group-hover:text-blue-600 transition-colors">
                            {{ $contact->name }}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-[13px] text-slate-600 font-medium">
                    {{ $contact->customer->name ?? '-' }}
                </td>
                <td class="px-6 py-4 text-[13px] text-slate-500">
                    {{ $contact->email ?? '-' }}
                </td>
                <td class="px-6 py-4 text-[12px] text-slate-500 font-mono">
                    {{ $contact->phone ?? '-' }}
                </td>
                <td class="px-6 py-4 text-[12px] text-slate-500">
                    {{ $contact->position ?? '-' }}
                </td>
                <td class="px-6 py-4">
                    <x-customer-management.status-badge :status="$contact->status ?? 'active'" />
                </td>
            </tr>
        @endforeach
    </x-customer-management.data-table>
</div>