{{-- 📝 Satışlar Sekmesi (Bağımlılık: $relatedSales) --}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-skin-heading">Satışlar</h2>
    @if(count($relatedSales) > 0)
        <div class="overflow-x-auto">
            <table class="agency-table">
                <thead>
                    <tr>
                        <th>Satış No</th>
                        <th class="text-center">Tarih</th>
                        <th class="text-right">Tutar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($relatedSales as $sale)
                        <tr>
                            <td class="item-name">{{ $sale->number ?? $sale->id }}</td>
                            <td class="text-center opacity-70 text-xs font-mono">
                                {{ $sale->created_at->format('d.m.Y') }}
                            </td>
                            <td class="text-right font-bold text-slate-700">
                                {{ number_format($sale->total_amount ?? 0, 2) }}
                                {{ $sale->currency ?? 'TRY' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 text-[var(--color-text-muted)]">
            <x-mary-icon name="o-banknotes" class="w-12 h-12 mx-auto mb-2 opacity-30" />
            <p class="text-sm">Henüz satış kaydı bulunmuyor</p>
        </div>
    @endif
</div>