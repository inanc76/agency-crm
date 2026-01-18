{{-- 📝 Varlıklar Sekmesi (Bağımlılık: $relatedAssets) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-skin-heading">Varlıklar</h2>
        <x-customer-management.action-button label="Yeni Varlık"
            href="/dashboard/customers/assets/create?customer={{ $customerId }}" />
    </div>
    @if(count($relatedAssets) > 0)
        <div class="overflow-x-auto">
            <table class="agency-table">
                <thead>
                    <tr>
                        <th>Varlık Adı</th>
                        <th>Tür</th>
                        <th>URL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($relatedAssets as $asset)
                        <tr onclick="window.location.href='/dashboard/customers/assets/{{ $asset->id }}'">
                            <td class="item-name">{{ $asset->name }}</td>
                            <td>
                                @php
                                    $typeLabel = $asset->type_item->label ?? $asset->type ?? 'Diğer';
                                    $statusClass = $asset->type_item->color_class ?? 'bg-slate-100 text-slate-500';
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="opacity-70">
                                @if($asset->url)
                                    <a href="{{ $asset->url }}" target="_blank" class="hover:underline"
                                        onclick="event.stopPropagation();">{{ Str::limit($asset->url, 40) }}</a>
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