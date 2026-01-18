{{-- 📝 Varlıklar Sekmesi (Bağımlılık: $relatedAssets) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-skin-heading">Varlıklar</h2>
        <x-customer-management.action-button label="Yeni Varlık"
            href="/dashboard/customers/assets/create?customer={{ $customerId }}" />
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
                        <th>Varlık Adı</th>
                        <th>Tür</th>
                        <th>URL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($relatedAssets as $asset)
                        <tr onclick="window.location.href='/dashboard/customers/assets/{{ $asset->id }}'">
                            <td onclick="event.stopPropagation()">
                                <input type="checkbox" disabled
                                    class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                            </td>
                            <td class="item-name">{{ $asset->name }}</td>
                            <td>
                                @php
                                    $typeLabel = $asset->type_item->label ?? $asset->type ?? 'Diğer';
                                    $statusClass = $asset->type_item->color_class ?? 'bg-slate-100 text-slate-500';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="opacity-70">
                                @if($asset->url)
                                    <a href="{{ $asset->url }}" target="_blank" class="hover:underline text-indigo-600"
                                        onclick="event.stopPropagation();">{{ Str::limit($asset->url, 40) }}</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-skin-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-mary-icon name="o-globe-alt" class="w-12 h-12 opacity-20 mb-4" />
                                    <div class="font-medium">Henüz varlık kaydı bulunmuyor</div>
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
                {{ count($relatedAssets) }} kayıt listelendi
            </div>
        </div>
    </div>
</div>