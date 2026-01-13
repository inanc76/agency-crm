{{-- 📝 Varlıklar Sekmesi (Bağımlılık: $relatedAssets) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-skin-heading">Varlıklar</h2>
        <x-customer-management.action-button label="Yeni Varlık"
            href="/dashboard/customers/assets/create?customer={{ $customerId }}" />
    </div>
    @if(count($relatedAssets) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--card-border)]">
                        <th class="text-left py-2 px-2 font-medium opacity-60">Varlık Adı</th>
                        <th class="text-left py-2 px-2 font-medium opacity-60">Tür</th>
                        <th class="text-left py-2 px-2 font-medium opacity-60">URL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($relatedAssets as $asset)
                        <tr class="border-b border-[var(--card-border)]/50 hover:bg-[var(--dropdown-hover-bg)] cursor-pointer transition-colors"
                            onclick="window.location.href='/dashboard/customers/assets/{{ $asset['id'] }}'">
                            <td class="py-3 px-2 font-medium">{{ $asset['name'] }}</td>
                            <td class="py-3 px-2 opacity-70">{{ $asset['type'] }}</td>
                            <td class="py-3 px-2 opacity-70">
                                @if($asset['url'])
                                    <a href="{{ $asset['url'] }}" target="_blank" class="text-skin-primary hover:underline"
                                        onclick="event.stopPropagation();">{{ Str::limit($asset['url'], 40) }}</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 text-[var(--color-text-muted)]">
            <x-mary-icon name="o-globe-alt" class="w-12 h-12 mx-auto mb-2 opacity-30" />
            <p class="text-sm">Henüz varlık kaydı bulunmuyor</p>
        </div>
    @endif
</div>