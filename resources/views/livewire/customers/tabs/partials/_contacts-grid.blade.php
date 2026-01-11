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
                                <div class="flex-shrink-0 w-6 text-center">
                                    @if(strtolower($contact->gender ?? '') === 'male')
                                        {{-- Male Symbol (Mars) --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="text-blue-500">
                                            <path d="M16 3h5v5"></path>
                                            <path d="M21 3l-7 7"></path>
                                            <circle cx="10" cy="14" r="7"></circle>
                                        </svg>
                                    @elseif(strtolower($contact->gender ?? '') === 'female')
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