{{-- 📝 Satışlar Sekmesi (Bağımlılık: $relatedSales) --}}
<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-skin-heading">Satışlar</h2>
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
                        <th>Satış No</th>
                        <th class="text-center">Tarih</th>
                        <th class="text-right">Tutar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($relatedSales as $sale)
                        <tr>
                            <td onclick="event.stopPropagation()">
                                <input type="checkbox" disabled
                                    class="checkbox checkbox-xs rounded border-slate-300 opacity-50">
                            </td>
                            <td class="item-name">{{ $sale->number ?? $sale->id }}</td>
                            <td class="text-center opacity-70 text-xs font-mono">
                                {{ $sale->created_at->format('d.m.Y') }}
                            </td>
                            <td class="text-right font-bold text-slate-700">
                                {{ number_format($sale->total_amount ?? 0, 2) }}
                                {{ $sale->currency ?? 'TRY' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-skin-muted">
                                <div class="flex flex-col items-center justify-center">
                                    <x-mary-icon name="o-banknotes" class="w-12 h-12 opacity-20 mb-4" />
                                    <div class="font-medium">Henüz satış kaydı bulunmuyor</div>
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
                {{ count($relatedSales) }} kayıt listelendi
            </div>
        </div>
    </div>
</div>