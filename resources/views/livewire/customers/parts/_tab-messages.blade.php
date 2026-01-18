{{-- 📝 Mesajlar Sekmesi (Bağımlılık: $relatedMessages) --}}
@php
    $headers = [
        ['label' => '', 'sortable' => false, 'width' => '40px'],
        ['label' => 'Konu', 'sortable' => false],
        ['label' => 'Gönderilecek Kişi', 'sortable' => false],
        ['label' => 'Tür', 'sortable' => false],
        ['label' => 'Durum', 'sortable' => false],
        ['label' => 'Gönderilme Tarihi', 'sortable' => false, 'class' => 'text-right'],
    ];
@endphp
@php
    $cid = $customerId ?? $customer_id ?? null;
@endphp

<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-skin-heading">Mesajlar</h2>
        <x-customer-management.action-button label="Yeni Mesaj"
            href="/dashboard/customers/messages/create?customer={{ $cid }}" />
    </div>

    <div class="bg-white rounded-xl border border-skin-light shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="agency-table">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" disabled
                                class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                        </th>
                        @foreach(array_slice($headers, 1) as $header)
                            <th class="{{ isset($header['width']) ? 'w-[' . $header['width'] . ']' : '' }} {{ $header['class'] ?? '' }}">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($relatedMessages as $message)
                        @php
                            $char = mb_substr($message->subject, 0, 1) ?: 'M';
                        @endphp
                        <tr onclick="Livewire.navigate('/dashboard/customers/messages/{{ $message->id }}')">
                            <td onclick="event.stopPropagation()">
                                <input type="checkbox" disabled
                                    class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-circle">
                                            {{ $char }}
                                        </div>
                                    </div>
                                    <div class="item-name">
                                        {{ $message->subject }}
                                    </div>
                                </div>
                            </td>
                            <td class="opacity-70">
                                @if($message->recipient_name && $message->recipient_email)
                                    <div class="flex flex-col">
                                        <span class="font-medium text-skin-heading">{{ $message->recipient_name }}</span>
                                        <span class="text-xs text-skin-muted">{{ $message->recipient_email }}</span>
                                    </div>
                                @else
                                    <span class="text-skin-muted">{{ $message->customer->name ?? '-' }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $typeLabel = $message->type_item->display_label ?? $message->type ?? 'Bilinmiyor';
                                    $typeClass = $message->type_item->metadata['color_class'] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $typeClass }}">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusLabel = $message->status_item->display_label ?? $message->status ?? 'Bilinmiyor';
                                    $statusClass = $message->status_item->metadata['color_class'] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="text-xs font-mono opacity-60 whitespace-nowrap text-right">
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
</div>