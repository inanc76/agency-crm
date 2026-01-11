{{-- 📝 Hizmetler Sekmesi (Bağımlılık: $relatedServices, $servicesStatusFilter) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-4">
            <h2 class="text-base font-bold text-skin-heading">Hizmetler</h2>
            <select wire:model.live="servicesStatusFilter" class="select select-xs bg-[var(--card-bg)] border-[var(--card-border)]">
                <option value="">Tüm Durumlar</option>
                <option value="ACTIVE">Aktif</option>
                <option value="PASSIVE">Pasif</option>
            </select>
        </div>
        <a href="/dashboard/customers/services/create?customer={{ $customerId }}"
            class="text-xs font-bold px-3 py-1.5 rounded-lg border border-[var(--card-border)] bg-[var(--card-bg)] hover:bg-[var(--dropdown-hover-bg)] transition-colors text-skin-primary">
            + Yeni Hizmet
        </a>
    </div>
    @php
        $filteredServices = collect($relatedServices)->when($servicesStatusFilter, function ($collection) {
            return $collection->where('status', $this->servicesStatusFilter);
        });
    @endphp
    @if($filteredServices->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--card-border)]">
                        <th class="text-left py-2 px-2 font-medium opacity-60">Hizmet Adı</th>
                        <th class="text-left py-2 px-2 font-medium opacity-60">Kategori</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Kalan Gün</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Bitiş</th>
                        <th class="text-right py-2 px-2 font-medium opacity-60">Fiyat</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredServices as $service)
                        @php
                            $endDate = \Carbon\Carbon::parse($service['end_date']);
                            $daysLeft = now()->diffInDays($endDate, false);
                        @endphp
                        <tr class="border-b border-[var(--card-border)]/50 hover:bg-[var(--dropdown-hover-bg)] cursor-pointer transition-colors"
                            onclick="window.location.href='/dashboard/customers/services/{{ $service['id'] }}'">
                            <td class="py-3 px-2 font-medium">{{ $service['service_name'] }}</td>
                            <td class="py-3 px-2 opacity-70">{{ $service['service_category'] ?? '-' }}</td>
                            <td class="py-3 px-2 text-center">
                                @if($daysLeft < 0)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)]">
                                        {{ abs((int)$daysLeft) }} gün geçti
                                    </span>
                                @elseif($daysLeft <= 30)
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)]">
                                        {{ (int)$daysLeft }} gün
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-[var(--color-success)]/10 text-[var(--color-success)]">
                                        {{ (int)$daysLeft }} gün
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-2 text-center opacity-70 text-xs font-mono">
                                {{ $endDate->format('d.m.Y') }}</td>
                            <td class="py-3 px-2 text-right font-medium">
                                {{ number_format($service['service_price'], 2) }}
                                {{ $service['service_currency'] }}</td>
                            <td class="py-3 px-2 text-center">
                                <span
                                    class="px-2 py-0.5 rounded text-xs font-medium {{ $service['status'] === 'ACTIVE' ? 'bg-[var(--color-success)]/10 text-[var(--color-success)]' : 'bg-[var(--dropdown-hover-bg)] text-[var(--color-text-muted)]' }}">
                                    {{ $service['status'] === 'ACTIVE' ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 text-[var(--color-text-muted)]">
            <x-mary-icon name="o-cog-6-tooth" class="w-12 h-12 mx-auto mb-2 opacity-30" />
            <p class="text-sm">{{ $servicesStatusFilter ? 'Filtreye uygun hizmet bulunamadı' : 'Henüz hizmet kaydı bulunmuyor' }}</p>
        </div>
    @endif
</div>
