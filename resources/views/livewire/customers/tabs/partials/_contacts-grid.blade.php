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
        <table class="agency-table">
            <thead>
                <tr>
                    <th class="w-10">
                        <input type="checkbox" wire:model.live="selectAll"
                            class="checkbox checkbox-xs rounded border-slate-300">
                    </th>
                    @foreach(array_slice($headers, 1) as $header)
                        <th>
                            {{ $header['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $contact)
                    <tr onclick="window.location.href='/dashboard/customers/contacts/{{ $contact->id }}'">
                        <td onclick="event.stopPropagation()">
                            <input type="checkbox" wire:model.live="selected" value="{{ $contact->id }}"
                                class="checkbox checkbox-xs rounded border-slate-300">
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    @php
                                        $gravatarUrl = $contact->getGravatarUrl(32);
                                    @endphp
                                    <div class="avatar-circle overflow-hidden">
                                        @if($gravatarUrl)
                                            <img src="{{ $gravatarUrl }}" alt="{{ $contact->name }}"
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
                                <div class="item-name">
                                    {{ $contact->name }}
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusLabel = $contact->status_item->label ?? $contact->status;
                                $statusClass = $contact->status_item->color_class ?? 'bg-slate-50 text-slate-500 border border-slate-200';
                            @endphp
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="text-xs opacity-70">
                            {{ $contact->position ?? '-' }}
                        </td>
                        <td class="text-xs opacity-80 font-medium">
                            {{ $contact->customer->name ?? '-' }}
                        </td>
                        <td class="text-xs opacity-70">
                            {{ $contact->email ?? '-' }}
                        </td>
                        <td class="text-[11px] opacity-60 font-mono">
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