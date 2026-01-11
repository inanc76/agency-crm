{{-- 📝 Satışlar Sekmesi (Bağımlılık: $relatedSales) --}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-skin-heading">Satışlar</h2>
    @if(count($relatedSales) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--card-border)]">
                        <th class="text-left py-2 px-2 font-medium opacity-60">Satış No</th>
                        <th class="text-center py-2 px-2 font-medium opacity-60">Tarih</th>
                        <th class="text-right py-2 px-2 font-medium opacity-60">Tutar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($relatedSales as $sale)
                        <tr class="border-b border-[var(--card-border)]/50 hover:bg-[var(--dropdown-hover-bg)] transition-colors">
                            <td class="py-3 px-2 font-medium">{{ $sale['number'] ?? $sale['id'] }}</td>
                            <td class="py-3 px-2 text-center opacity-70 text-xs font-mono">
                                {{ \Carbon\Carbon::parse($sale['created_at'])->format('d.m.Y') }}</td>
                            <td class="py-3 px-2 text-right font-medium">
                                {{ number_format($sale['total_amount'] ?? 0, 2) }}
                                {{ $sale['currency'] ?? 'TRY' }}</td>
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
