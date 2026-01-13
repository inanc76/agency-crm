{{-- 📝 Kişiler Sekmesi (Bağımlılık: $relatedContacts) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-skin-heading">Kişiler</h2>
        <x-customer-management.action-button label="Yeni Kişi"
            href="/dashboard/customers/contacts/create?customer={{ $customerId }}" />
    </div>
    @if(count($relatedContacts) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--card-border)]">
                        <th class="text-left py-2 px-2 font-medium opacity-60">Ad Soyad</th>
                        <th class="text-left py-2 px-2 font-medium opacity-60">Pozisyon</th>
                        <th class="text-left py-2 px-2 font-medium opacity-60">Email</th>
                        <th class="text-left py-2 px-2 font-medium opacity-60">Telefon</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($relatedContacts as $contact)
                        <tr class="border-b border-[var(--card-border)]/50 hover:bg-[var(--dropdown-hover-bg)] cursor-pointer transition-colors"
                            onclick="window.location.href='/dashboard/customers/contacts/{{ $contact['id'] }}'">
                            <td class="py-3 px-2 font-medium">{{ $contact['name'] }}</td>
                            <td class="py-3 px-2 opacity-70">{{ $contact['position'] ?? '-' }}</td>
                            <td class="py-3 px-2 opacity-70">{{ $contact['emails'][0] ?? '-' }}</td>
                            <td class="py-3 px-2 opacity-70">{{ $contact['phones'][0] ?? '-' }}</td>
                            <td class="py-3 px-2 text-center">
                                <span
                                    class="px-2 py-0.5 rounded text-xs font-medium {{ $contact['status'] === 'WORKING' ? 'bg-[var(--color-success)]/10 text-[var(--color-success)]' : 'bg-[var(--dropdown-hover-bg)] text-[var(--color-text-muted)]' }}">
                                    {{ $contact['status'] === 'WORKING' ? 'Çalışıyor' : 'Ayrıldı' }}
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