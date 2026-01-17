{{-- 📝 Mesajlar Sekmesi (Bağımlılık: $relatedMessages) --}}
@php
    $headers = [
        ['label' => '', 'sortable' => false, 'width' => '40px'],
        ['label' => 'Konu', 'sortable' => false],
        ['label' => 'Müşteri', 'sortable' => false],
        ['label' => 'Tür', 'sortable' => false],
        ['label' => 'Durum', 'sortable' => false],
        ['label' => 'Gönderilme Tarihi', 'sortable' => false],
    ];
@endphp

<div class="bg-white rounded-xl border border-skin-light shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b border-skin-light">
                <tr>
                    <th class="px-6 py-3 w-10">
                        <input type="checkbox" disabled
                            class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                    </th>
                    @foreach(array_slice($headers, 1) as $header)
                        <th class="px-6 py-3 font-semibold text-skin-base uppercase text-[11px] tracking-wider">
                            {{ $header['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($relatedMessages as $message)
                    @php
                        $char = mb_substr($message->subject, 0, 1) ?: 'M';
                    @endphp
                    <tr class="group hover:bg-[var(--list-card-hover-bg)] transition-all duration-200 cursor-pointer"
                        onclick="Livewire.navigate('/dashboard/customers/messages/{{ $message->id }}')">
                        <td class="px-6 py-4" onclick="event.stopPropagation()">
                            <input type="checkbox" disabled
                                class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] shadow-sm font-semibold"
                                        style="background-color: var(--table-avatar-bg); color: var(--table-avatar-text); border: 1px solid var(--table-avatar-border);">
                                        {{ $char }}
                                    </div>
                                </div>
                                <div class="text-[13px] group-hover:opacity-80 transition-opacity font-medium"
                                    style="color: var(--list-card-link-color);">
                                    {{ $message->subject }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-[13px] text-skin-base font-medium">
                            {{ $message->customer->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-[13px] text-skin-muted">
                            {{ $message->type }}
                        </td>
                        <td class="px-6 py-4">
                            <x-customer-management.status-badge :status="$message->status ?? 'sent'" />
                        </td>
                        <td class="px-6 py-4 text-[12px] text-skin-muted font-mono whitespace-nowrap">
                            {{ $message->sent_at?->format('d.m.Y H:i') ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-skin-muted">
                            <div class="flex flex-col items-center justify-center">
                                <x-mary-icon name="o-envelope" class="w-12 h-12 opacity-20 mb-4" />
                                <div class="font-medium">Henüz mesaj kaydı bulunmuyor</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
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
            {{ count($relatedMessages) }} kayıt listelendi
        </div>
    </div>
</div>