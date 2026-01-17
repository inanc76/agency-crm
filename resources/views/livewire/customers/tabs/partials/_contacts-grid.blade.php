{{--
SECTION: Contacts Data Grid & Listing
Mimarın Notu: Bu bölüm kişi kartları veya listesini içerir.
İş Mantığı Şerhi: Contact modeli ile konuşur, Customer ilişkisini kullanır, gender field'ı için icon mapping yapar.
Mühür Koruması: Table styling, gender icons ve hover effects korunmalıdır.
--}}

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
                                <div class="flex-shrink-0">
                                    @php
                                        $gravatarUrl = $contact->getGravatarUrl(32);
                                    @endphp
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs shadow-sm font-semibold overflow-hidden"
                                        style="background-color: var(--table-avatar-bg); color: var(--table-avatar-text); border: 1px solid var(--table-avatar-border);">
                                        @if($gravatarUrl)
                                            <img src="{{ $gravatarUrl }}" 
                                                 alt="{{ $contact->name }}"
                                                 class="w-full h-full object-cover rounded-full"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="w-full h-full flex items-center justify-center" style="display: none;">
                                                {{ $contact->initials() }}
                                            </div>
                                        @else
                                            {{ $contact->initials() }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-[13px] group-hover:opacity-80 transition-opacity font-medium"
                                    style="color: var(--list-card-link-color);">
                                    {{ $contact->name }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusLabel = $contact->status_item->label ?? $contact->status;
                                $statusClass = $contact->status_item->color_class ?? 'bg-skin-hover text-skin-muted border border-skin-light';
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-[12px] text-skin-muted">
                            {{ $contact->position ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-[13px] text-skin-base font-medium">
                            {{ $contact->customer->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-[13px] text-skin-muted">
                            {{ $contact->email ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-[12px] text-skin-muted font-mono">
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