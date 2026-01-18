{{-- 📝 Kişiler Sekmesi (Bağımlılık: $relatedContacts) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-skin-heading">Kişiler</h2>
        <x-customer-management.action-button label="Yeni Kişi"
            href="/dashboard/customers/contacts/create?customer={{ $customerId }}" />
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
                        <th>Ad Soyad</th>
                        <th>Pozisyon</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th class="text-center">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($relatedContacts as $contact)
                        <tr onclick="window.location.href='/dashboard/customers/contacts/{{ $contact->id }}'">
                            <td onclick="event.stopPropagation()">
                                <input type="checkbox" disabled
                                    class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        @php
                                            $gravatarUrl = $contact->getGravatarUrl(36);
                                        @endphp
                                        <div class="avatar-circle overflow-hidden">
                                            @if($gravatarUrl)
                                                <img src="{{ $gravatarUrl }}" alt="{{ $contact->name }}"
                                                    class="w-full h-full object-cover rounded-full"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-full h-full flex items-center justify-center font-bold"
                                                    style="display: none;">
                                                    {{ $contact->initials() }}
                                                </div>
                                            @else
                                                <span class="font-bold">{{ $contact->initials() }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="item-name">{{ $contact->name }}</div>
                                </div>
                            </td>
                            <td class="opacity-70">{{ $contact->position ?? '-' }}</td>
                            <td class="opacity-70">{{ $contact->emails[0] ?? '-' }}</td>
                            <td class="opacity-70">{{ $contact->phones[0] ?? '-' }}</td>
                            <td class="text-center">
                                @php
                                    $statusLabel = $contact->status_item->label ?? $contact->status ?? 'Ayrıldı';
                                    $statusClass = $contact->status_item->color_class ?? 'bg-slate-100 text-slate-500';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-skin-muted">
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

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-skin-light flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs text-skin-muted">Göster:</span>
                <div class="px-2 py-1 border border-skin-light rounded text-xs bg-white">25</div>
            </div>

            <div class="text-[10px] text-skin-muted font-mono">
                {{ count($relatedContacts) }} kayıt listelendi
            </div>
        </div>
    </div>
</div>