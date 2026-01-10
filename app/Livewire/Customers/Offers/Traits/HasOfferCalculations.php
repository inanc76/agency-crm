<?php

namespace App\Livewire\Customers\Offers\Traits;

use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Support\Collection;

trait HasOfferCalculations
{
    /**
     * @trait HasOfferCalculations
     * @purpose Teklifin matematiksel hesaplamalarını ve numara üretimini yönetir.
     * @methods updatedValidDays(), updatedDiscountValue(), updatedDiscountType(), calculateTotals(), generateOfferNumber()
     */
    public function updatedValidDays(): void
    {
        $this->valid_until = Carbon::now()->addDays($this->valid_days)->format('Y-m-d');
    }

    public function updatedDiscountValue(): void
    {
        $totals = $this->calculateTotals();
        $original = $totals['original'];

        if ($this->discount_type === 'PERCENTAGE') {
            if ($this->discount_value > 100) {
                $this->discount_value = 100;
                $this->warning('Uyarı', 'İndirim oranı %100\'ü geçemez.');
            }
        } else {
            if ($this->discount_value > $original) {
                $this->discount_value = $original;
                $this->warning('Uyarı', 'İndirim tutarı teklif tutarını geçemez.');
            }
        }

        if ($this->discount_value < 0) {
            $this->discount_value = 0;
        }
    }

    public function updatedDiscountType(): void
    {
        $this->discount_value = 0;
    }

    public function calculateTotals(): array
    {
        $original = collect($this->items)->sum(fn($item) => $item['price'] * $item['quantity']);

        $discountAmount = 0;
        if ($this->discount_type === 'PERCENTAGE') {
            $discountAmount = $original * (min(100, $this->discount_value) / 100);
        } else {
            $discountAmount = min($original, $this->discount_value);
        }

        $totalAfterDiscount = max(0, $original - $discountAmount);
        $vatAmount = $totalAfterDiscount * ($this->vat_rate / 100);
        $grandTotal = max(0, $totalAfterDiscount + $vatAmount);

        return [
            'original' => $original,
            'discount' => $discountAmount,
            'vat' => $vatAmount,
            'total' => $grandTotal,
        ];
    }

    private function generateOfferNumber(): string
    {
        $year = Carbon::now()->year;
        $lastOffer = Offer::whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->first();

        $sequence = $lastOffer ? (int) substr($lastOffer->number, -4) + 1 : 1;

        return sprintf('TKL-%d-%04d', $year, $sequence);
    }
}
