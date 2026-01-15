<?php

namespace App\Livewire\Customers\Offers\Traits;

use App\Models\Offer;
use Carbon\Carbon;

trait HasOfferCalculations
{
    /**
     * @trait HasOfferCalculations
     *
     * @purpose Teklifin matematiksel hesaplamalarını ve numara üretimini yönetir.
     *
     * @methods updatedValidDays(), updatedDiscountValue(), updatedDiscountType(), calculateTotals(), generateOfferNumber()
     */


    public function updatedDiscountValue(): void
    {
        $totals = $this->calculateTotals();
        $original = (float) $totals['original'];
        $this->discount_value = (float) ($this->discount_value ?? 0);

        if ($this->discount_type === 'PERCENTAGE') {
            if ($this->discount_value > 100) {
                $this->discount_value = 100.0;
                $this->warning('Uyarı', 'İndirim oranı %100\'ü geçemez.');
            }
        } else {
            if ($this->discount_value > $original) {
                $this->discount_value = $original;
                $this->warning('Uyarı', 'İndirim tutarı teklif tutarını geçemez.');
            }
        }

        if ($this->discount_value < 0) {
            $this->discount_value = 0.0;
        }
    }

    public function updatedDiscountType(): void
    {
        $this->discount_value = 0;
    }

    public function calculateTotals(): array
    {
        $original = 0;
        foreach ($this->sections as $section) {
            $original += collect($section['items'])->sum(fn($item) => (float) ($item['price'] ?? 0) * (float) ($item['quantity'] ?? 1));
        }

        $discountValue = (float) ($this->discount_value ?? 0);
        $vatRate = (float) ($this->vat_rate ?? 0);

        $discountAmount = 0;
        if ($this->discount_type === 'PERCENTAGE') {
            $discountAmount = $original * (min(100.0, $discountValue) / 100.0);
        } else {
            $discountAmount = min($original, $discountValue);
        }

        $totalAfterDiscount = max(0.0, $original - $discountAmount);
        $vatAmount = $totalAfterDiscount * ($vatRate / 100.0);
        $grandTotal = max(0.0, $totalAfterDiscount + $vatAmount);

        return [
            'original' => $original,
            'discount' => $discountAmount,
            'vat' => $vatAmount,
            'total' => $grandTotal,
        ];
    }

    private function generateOfferNumber(): string
    {
        // If updating existing offer, keep the same number
        if ($this->offerId) {
            $existingOffer = Offer::find($this->offerId);
            if ($existingOffer && $existingOffer->number) {
                return $existingOffer->number;
            }
        }

        $year = Carbon::now()->year;

        // Get customer prefix (first 3 letters of customer name, uppercase)
        $prefix = 'GEN'; // Default fallback
        if (!empty($this->customer_id)) {
            $customer = \App\Models\Customer::find($this->customer_id);
            if ($customer && !empty($customer->name)) {
                // Get first 3 letters, handle Turkish characters
                $name = mb_strtoupper(trim($customer->name), 'UTF-8');
                $prefix = mb_substr($name, 0, 3, 'UTF-8');
            }
        }

        // Get the highest sequence number for this prefix and year
        // Database-agnostic approach: fetch all matching numbers and parse in PHP
        $offers = Offer::where('number', 'LIKE', $prefix . '-' . $year . '-%')
            ->pluck('number');

        $maxSequence = 0;
        foreach ($offers as $number) {
            // Extract last 4 digits: PREFIX-YEAR-XXXX
            if (preg_match('/-(\d{4})$/', $number, $matches)) {
                $seq = (int) $matches[1];
                if ($seq > $maxSequence) {
                    $maxSequence = $seq;
                }
            }
        }

        $sequence = $maxSequence + 1;

        return sprintf('%s-%d-%04d', $prefix, $year, $sequence);
    }
}
