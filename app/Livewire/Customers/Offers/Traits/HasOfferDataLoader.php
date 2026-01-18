<?php

namespace App\Livewire\Customers\Offers\Traits;

use App\Models\Offer;
use Carbon\Carbon;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasOfferDataLoader Trait (Data Transformation & Initialization)                            ║
 * ║  🎯 ANA GÖREV: Teklif verisi yükleme ve model ↔ state dönüşümü                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasOfferDataLoader
{
    use HasOfferReferenceData;

    // Offer Primary State
    public $customer_id = '';

    public $number = '';

    public $title = '';

    public $status = 'DRAFT';

    public $description = '';

    public $valid_days = 30;

    public $valid_until = null;

    public $created_at = null;

    public $discount_value = 0;

    public $discount_type = 'AMOUNT'; // PERCENTAGE or AMOUNT

    public $vat_rate = 20;

    public $currency = 'USD';

    // UI State
    public $isViewMode = false;

    public $offerId = null;

    public ?Offer $offerModel = null;

    public string $activeTab = 'info';

    public $showServiceModal = false;

    public $selectedYear = 0;

    // Download Settings
    public $is_pdf_downloadable = true;

    public $is_attachments_downloadable = true;

    public $is_downloadable_after_expiry = false;

    public function mount(?string $offer = null): void
    {
        $this->initReferenceData();

        $this->valid_until = Carbon::now()->addDays($this->valid_days)->format('Y-m-d');
        $this->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $this->selectedYear = Carbon::now()->year;

        if ($offer) {
            $this->offerId = $offer;
            $this->loadOfferData();
            $this->activeTab = request()->query('tab', 'info');
        } else {
            $this->initNewOffer();
        }
    }

    protected function initNewOffer(): void
    {
        $this->title = '';
        $this->description = 'Bu sayfadaki fiyat alt sayfaların toplamıdır. Ayrı bir ürün ya da hizmet içermez.';
        $this->sections = [
            [
                'id' => null,
                'title' => 'Teklif Bölümü - 1',
                'description' => '',
                'items' => [],
            ],
        ];

        $customerId = request()->query('customer');
        if ($customerId && collect($this->customers)->firstWhere('id', $customerId)) {
            $this->customer_id = $customerId;
            $this->loadCustomerServices();
        }
    }

    protected function loadOfferData(): void
    {
        $this->offerModel = Offer::with(['sections.items', 'attachments', 'status_item', 'currency_item', 'downloadLogs', 'messages'])->findOrFail($this->offerId);
        $offer = $this->offerModel;

        $this->customer_id = $offer->customer_id;
        $this->number = $offer->number;
        $this->loadCustomerServices();

        $this->title = $offer->title ?? '';
        $this->status = $offer->status;
        $this->description = $offer->description ?? '';

        if ($offer->discount_percentage > 0) {
            $this->discount_value = (float) $offer->discount_percentage;
            $this->discount_type = 'PERCENTAGE';
        } else {
            $this->discount_value = (float) $offer->discounted_amount;
            $this->discount_type = 'AMOUNT';
        }

        $this->vat_rate = (float) $offer->vat_rate;
        $this->currency = $offer->currency;
        $this->valid_until = Carbon::parse($offer->valid_until)->format('Y-m-d');
        $this->created_at = $offer->created_at;

        $this->is_pdf_downloadable = (bool) $offer->is_pdf_downloadable;
        $this->is_attachments_downloadable = (bool) $offer->is_attachments_downloadable;
        $this->is_downloadable_after_expiry = (bool) $offer->is_downloadable_after_expiry;

        if ($this->created_at && $this->valid_until) {
            $created = Carbon::parse($this->created_at)->startOfDay();
            $validUntil = Carbon::parse($this->valid_until)->startOfDay();
            $this->valid_days = (int) $created->diffInDays($validUntil);
        }

        $this->sections = $offer->sections->map(fn ($section) => [
            'id' => $section->id,
            'title' => $section->title,
            'description' => $section->description,
            'items' => $section->items->map(fn ($item) => [
                'service_id' => $item->service_id,
                'service_name' => $item->service_name,
                'description' => $item->description,
                'price' => (float) $item->price,
                'currency' => $item->currency,
                'duration' => $item->duration,
                'quantity' => (int) $item->quantity,
            ])->toArray(),
        ])->toArray();

        $this->attachments = $offer->attachments->map(fn ($att) => [
            'id' => $att->id,
            'title' => $att->title,
            'description' => $att->description,
            'price' => $att->price,
            'currency' => $att->currency,
            'file_name' => $att->file_name,
            'file_type' => $att->file_type,
            'file_size' => $att->file_size,
            'file_path' => $att->file_path,
        ])->toArray();

        $this->isViewMode = true;
    }

    public function updatedCustomerId(): void
    {
        $this->loadCustomerServices();
    }

    public function updatedValidDays(): void
    {
        if ($this->created_at && is_numeric($this->valid_days)) {
            $this->valid_until = Carbon::parse($this->created_at)->addDays((int) $this->valid_days)->format('Y-m-d');
        }
    }

    public function updatedSelectedYear(): void
    {
        $this->loadCustomerServices();
    }
}
