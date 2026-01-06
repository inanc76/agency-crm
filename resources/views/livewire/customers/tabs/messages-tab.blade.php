<?php

use Livewire\Volt\Component;
use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    public string $search = '';
    public string $letter = '';

    public function with(): array
    {
        $messages = Message::query()
            ->with('customer')
            ->when($this->search, function (Builder $query) {
                $query->where('subject', 'ilike', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'ilike', '%' . $this->search . '%');
                    });
            })
            ->when($this->letter, function (Builder $query) {
                if ($this->letter === '0-9') {
                    $query->whereRaw("subject ~ '^[0-9]'")
                        ->orWhereHas('customer', function ($q) {
                            $q->whereRaw("name ~ '^[0-9]'");
                        });
                } else {
                    $query->where('subject', 'ilike', $this->letter . '%')
                        ->orWhereHas('customer', function ($q) {
                            $q->where('name', 'ilike', $this->letter . '%');
                        });
                }
            })
            ->orderBy('sent_at', 'desc')
            ->get();

        return [
            'messages' => $messages,
        ];
    }
}; ?>

{{-- Mesajlar Tab --}}
<div>
    {{-- Header with Action Button --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Mesajlar</h2>
            <p class="text-sm text-gray-500">Tüm mesajları görüntüleyin ve yönetin</p>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500">{{ $messages->count() }} mesaj</span>
            <x-customer-management.action-button label="Yeni Mesaj" href="#" />
        </div>
    </div>

    {{-- Filter Panel --}}
    <x-customer-management.filter-panel :showCategories="false" :showAlphabet="true" statusLabel="Duruma Göre Filtrele"
        :letter="$letter" />

    {{-- Data Table --}}
    @php
        $headers = [
            ['label' => 'Konu', 'sortable' => true],
            ['label' => 'Müşteri', 'sortable' => true],
            ['label' => 'Tür', 'sortable' => false],
            ['label' => 'Durum', 'sortable' => false],
            ['label' => 'Gönderilme Tarihi', 'sortable' => false],
        ];
    @endphp

    <x-customer-management.data-table :headers="$headers" emptyMessage="Henüz mesaj kaydı bulunmuyor">
        @foreach($messages as $message)
            @php
                $char = mb_substr($message->subject, 0, 1);
            @endphp
            <tr class="group hover:bg-slate-50/80 transition-all duration-200 cursor-pointer"
                onclick="window.location.href='/dashboard/customers/{{ $message->customer_id }}?tab=messages'">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <x-mary-avatar placeholder="{{ $char }}"
                                class="!w-9 !h-9 bg-white text-black font-semibold text-xs border border-gray-100 shadow-sm" />
                        </div>
                        <div class="font-bold text-slate-700 text-[13px] group-hover:text-blue-600 transition-colors">
                            {{ $message->subject }}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-[13px] text-slate-600 font-medium">
                    {{ $message->customer->name ?? '-' }}
                </td>
                <td class="px-6 py-4 text-[13px] text-slate-500">
                    {{ $message->type }}
                </td>
                <td class="px-6 py-4">
                    <x-customer-management.status-badge :status="$message->status ?? 'sent'" />
                </td>
                <td class="px-6 py-4 text-[12px] text-slate-500 font-mono text-center">
                    {{ $message->sent_at?->format('d.m.Y H:i') ?? '-' }}
                </td>
            </tr>
        @endforeach
    </x-customer-management.data-table>
</div>