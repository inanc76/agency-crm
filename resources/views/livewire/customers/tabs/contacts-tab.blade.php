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
            <h2 class="text-lg font-bold" style="color: var(--color-text-heading);">Kişiler</h2>
            <p class="text-sm opacity-60" style="color: var(--color-text-base);">Tüm iletişim kişilerini görüntüleyin ve
                yönetin</p>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm opacity-60" style="color: var(--color-text-base);">{{ $contacts->count() }} kişi</span>
            <x-customer-management.action-button label="Yeni Kişi" href="{{ route('customers.contacts.create') }}" />
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
            <tr class="group hover:bg-[var(--list-card-hover-bg)] transition-all duration-200 cursor-pointer"
                onclick="window.location.href='/dashboard/customers/contacts/{{ $contact->id }}'">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <x-mary-avatar placeholder="{{ $char }}"
                                class="!w-9 !h-9 bg-white text-black font-semibold text-xs border border-gray-100 shadow-sm" />
                        </div>
                        <div class="font-bold text-[13px] group-hover:opacity-80 transition-opacity"
                            style="color: var(--list-card-link-color);">
                            {{ $contact->name }}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-[13px] font-medium" style="color: var(--color-text-base);">
                    {{ $contact->customer->name ?? '-' }}
                </td>
                <td class="px-6 py-4 text-[13px] opacity-70" style="color: var(--color-text-base);">
                    {{ $contact->email ?? '-' }}
                </td>
                <td class="px-6 py-4 text-[12px] font-mono opacity-70" style="color: var(--color-text-base);">
                    {{ $contact->phone ?? '-' }}
                </td>
                <td class="px-6 py-4 text-[12px] opacity-70" style="color: var(--color-text-base);">
                    {{ $contact->position ?? '-' }}
                </td>
                <td class="px-6 py-4">
                    <x-customer-management.status-badge :status="$contact->status ?? 'active'" />
                </td>
            </tr>
        @endforeach
    </x-customer-management.data-table>
</div>