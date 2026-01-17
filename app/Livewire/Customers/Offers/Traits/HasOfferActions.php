<?php

namespace App\Livewire\Customers\Offers\Traits;

use App\Models\Offer;
use App\Models\OfferAttachment;
use App\Models\OfferItem;
use App\Models\OfferSection;
use App\Services\MinioService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasOfferActions Trait (Core Execution)                                                     ║
 * ║  🎯 ANA GÖREV: Tekliflerin kalıcı depolama ve silme süreçlerinin yönetimi                                        ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasOfferActions
{
    use HasOfferAttachments, HasOfferDataLoader, HasOfferStatusLogic;

    /**
     * @purpose Teklifi veritabanına kaydetme (UPSERT)
     */
    public function save(): void
    {
        if ($this->offerId) {
            $this->authorize('offers.edit');
        } else {
            $this->authorize('offers.create');
        }

        $this->performValidation();

        $totals = $this->calculateTotals();

        DB::transaction(function () use ($totals) {
            $offerData = $this->prepareOfferData($totals);

            if ($this->offerId) {
                $offer = Offer::findOrFail($this->offerId);
                $offer->update($offerData);
                $offer->sections()->delete();
                $offer->items()->delete();
            } else {
                $this->offerId = Str::uuid()->toString();
                $offerData['id'] = $this->offerId;
                $offer = Offer::create($offerData);
            }

            $this->saveSectionsAndItems($offer);
            $this->syncAttachments($offer);
        });

        $this->success('İşlem Başarılı', 'Teklif başarıyla kaydedildi.');
        $this->dispatch('offer-saved');

        if ($this->offerId) {
            $this->loadOfferData();
        } else {
            $this->redirect("/dashboard/customers/offers/{$this->offerId}", navigate: true);
        }
    }

    protected function performValidation(): void
    {
        $this->validate([
            'customer_id' => 'required',
            'title' => 'required|string|max:255',
            'valid_until' => 'required|date',
            'sections' => 'required|array|min:1',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.items' => 'required|array|min:1',
            'sections.*.items.*.service_name' => 'required|string|max:255',
            'sections.*.items.*.price' => 'required|numeric|min:0.01',
            'sections.*.items.*.quantity' => 'required|numeric|min:1',
        ]);
    }

    protected function prepareOfferData(array $totals): array
    {
        return [
            'customer_id' => $this->customer_id,
            'number' => $this->generateOfferNumber(),
            'title' => $this->title,
            'status' => $this->status,
            'description' => $this->description,
            'original_amount' => $totals['original'],
            'discount_percentage' => $this->discount_type === 'PERCENTAGE' ? $this->discount_value : 0,
            'discounted_amount' => $totals['discount'],
            'total_amount' => $totals['total'],
            'currency' => $this->currency,
            'vat_rate' => $this->vat_rate,
            'vat_amount' => $totals['vat'],
            'valid_until' => $this->valid_until,
            'is_pdf_downloadable' => $this->is_pdf_downloadable,
            'is_attachments_downloadable' => $this->is_attachments_downloadable,
            'is_downloadable_after_expiry' => $this->is_downloadable_after_expiry,
        ];
    }

    protected function saveSectionsAndItems(Offer $offer): void
    {
        foreach ($this->sections as $sIndex => $sectionData) {
            $section = OfferSection::create([
                'id' => Str::uuid()->toString(),
                'offer_id' => $offer->id,
                'title' => $sectionData['title'],
                'description' => $sectionData['description'] ?? '',
                'sort_order' => $sIndex,
            ]);

            foreach ($sectionData['items'] as $item) {
                OfferItem::create([
                    'id' => Str::uuid()->toString(),
                    'offer_id' => $offer->id,
                    'section_id' => $section->id,
                    'service_id' => $item['service_id'] ?? null,
                    'service_name' => $item['service_name'],
                    'description' => $item['description'] ?? '',
                    'price' => $item['price'],
                    'currency' => $item['currency'],
                    'duration' => $item['duration'],
                    'quantity' => $item['quantity'],
                ]);
            }
        }
    }

    protected function syncAttachments(Offer $offer): void
    {
        $offer->attachments()->delete();
        foreach ($this->attachments as $att) {
            OfferAttachment::create([
                'id' => Str::uuid()->toString(),
                'offer_id' => $offer->id,
                'title' => $att['title'],
                'description' => $att['description'] ?? '',
                'price' => $att['price'],
                'currency' => $att['currency'],
                'file_path' => $att['file_path'],
                'file_name' => $att['file_name'],
                'file_type' => $att['file_type'],
                'file_size' => $att['file_size'],
            ]);
        }
    }

    public function cancel(): void
    {
        if (!empty($this->attachments)) {
            $minioService = app(MinioService::class);
            foreach ($this->attachments as $attachment) {
                if (!isset($attachment['id']) && isset($attachment['file_path'])) {
                    try {
                        $minioService->deleteFile($attachment['file_path']);
                    } catch (\Exception $e) {
                        Log::error('Cancel cleanup error: ' . $e->getMessage());
                    }
                }
            }
        }

        if ($this->offerId) {
            $this->loadOfferData();
        } else {
            $this->redirect('/dashboard/customers?tab=offers', navigate: true);
        }
    }

    public function delete(): void
    {
        $this->authorize('offers.delete');
        if ($this->offerId) {
            Offer::findOrFail($this->offerId)->delete();
            $this->success('Silindi', 'Teklif arşive taşındı.');
            $this->redirect('/dashboard/customers?tab=offers');
        }
    }
}
