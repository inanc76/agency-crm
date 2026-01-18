{{-- 📝 Kişiler Sekmesi (Bağımlılık: $relatedContacts) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-skin-heading">Kişiler</h2>
        <x-customer-management.action-button label="Yeni Kişi"
            href="/dashboard/customers/contacts/create?customer={{ $customerId }}" />
    </div>
    @if(count($relatedContacts) > 0)
        <div class="overflow-x-auto">
            <table class="agency-table">
                <thead>
                    <tr>
                        <th>Ad Soyad</th>
                        <th>Pozisyon</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th class="text-center">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($relatedContacts as $contact)
                        <tr onclick="window.location.href='/dashboard/customers/contacts/{{ $contact->id }}'">
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
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 text-[var(--color-text-muted)]">
            <x-mary-icon name="o-users" class="w-12 h-12 mx-auto mb-2 opacity-30" />
            <p class="text-sm">Henüz kişi kaydı bulunmuyor</p>
        </div>
    @endif
</div>