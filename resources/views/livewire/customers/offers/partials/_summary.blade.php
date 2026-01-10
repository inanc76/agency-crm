{{--
@component: _summary.blade.php
@section: Sağ Kolon - Teklif Özeti
@description: Ara toplam, indirim, KDV ve genel toplam hesaplamalarının gösterildiği özet kartı.
@params: $isViewMode (bool), $currency (string), $discount_type (string), $discount_value (float), $vat_rate (float),
$valid_until (string), $items (array)
@events: calculateTotals()
--}}
<div class="theme-card p-6 shadow-sm sticky top-6">
    <h3 class="text-sm font-bold text-slate-900 mb-4">Teklif Özeti</h3>

    @php
        $totals = $this->calculateTotals();
    @endphp

    <div class="space-y-3 text-sm">
        <div class="flex justify-between">
            <span class="opacity-60">Ara Toplam:</span>
            <span class="font-medium">{{ number_format($totals['original'], 0, ',', '.') }}
                {{ $currency }}</span>
        </div>

        @if($totals['discount'] > 0)
            <div class="flex justify-between text-skin-danger">
                <span>İndirim (@if($discount_type === 'PERCENTAGE') %{{ $discount_value }} @else Tutar
                @endif):</span>
                <span class="font-medium">-{{ number_format($totals['discount'], 0, ',', '.') }}
                    {{ $currency }}</span>
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-200">
                <span class="opacity-60">İndirimli Toplam:</span>
                <span class="font-medium">{{ number_format($totals['original'] - $totals['discount'], 0, ',', '.') }}
                    {{ $currency }}</span>
            </div>
        @endif

        <div class="flex justify-between">
            <span class="opacity-60">KDV (%{{ (int) $vat_rate }}):</span>
            <span class="font-medium">{{ number_format($totals['vat'], 0, ',', '.') }}
                {{ $currency }}</span>
        </div>

        <div class="flex justify-between pt-2 border-t border-slate-200">
            <span class="opacity-60 text-[11px] uppercase tracking-wider">Geçerlilik Tarihi:</span>
            <span class="font-medium text-[11px]">{{ \Carbon\Carbon::parse($valid_until)->format('d.m.Y') }}</span>
        </div>

        <div class="flex justify-between pt-3 border-t-2 border-slate-300 text-base font-bold"
            class="text-skin-heading">
            <span>Genel Toplam:</span>
            <span>{{ number_format($totals['total'], 0, ',', '.') }} {{ $currency }}</span>
        </div>
    </div>

    @if(!$isViewMode && count($items) > 0)
        <div class="mt-4 text-xs text-slate-400 text-center">
            Fiyatlar KDV dahil değildir
        </div>
    @endif
</div>