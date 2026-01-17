<?php

namespace App\Livewire\Customers\Offers\Traits;

use App\Models\Offer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11                                            ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasOfferStatusLogic Trait                                                                 ║
 * ║  🎯 ANA GÖREV: Teklif yaşam döngüsü ve statü geçiş yönetimi                                                     ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasOfferStatusLogic
{
    /**
     * @purpose Teklifin yaşam döngüsü statüsünü yönetir.
     */
    public function statusChange(string $newStatus): void
    {
        $this->authorize('offers.status');

        $validStatuses = ['DRAFT', 'SENT', 'APPROVED', 'REJECTED'];
        if (!in_array($newStatus, $validStatuses)) {
            $this->error('Hata', 'Geçersiz durum değeri.');
            return;
        }

        if (!$this->offerId) {
            $this->error('Hata', 'Teklif bulunamadı.');
            return;
        }

        $offer = Offer::findOrFail($this->offerId);
        $oldStatus = $offer->status;

        if (in_array($oldStatus, ['APPROVED', 'REJECTED']) && $newStatus !== 'DRAFT') {
            $this->error('Uyarı', 'Onaylanmış veya reddedilmiş teklifler sadece taslağa döndürülebilir.');
            return;
        }

        if ($oldStatus === $newStatus) {
            $this->warning('Bilgi', 'Teklif zaten bu durumda.');
            return;
        }

        DB::transaction(function () use ($offer, $oldStatus, $newStatus) {
            $offer->update(['status' => $newStatus]);

            $statusLabels = [
                'DRAFT' => 'Taslak',
                'SENT' => 'Gönderildi',
                'APPROVED' => 'Onaylandı',
                'REJECTED' => 'Reddedildi',
            ];

            $noteContent = sprintf(
                "Teklif durumu '%s' → '%s' olarak değiştirildi.",
                $statusLabels[$oldStatus] ?? $oldStatus,
                $statusLabels[$newStatus] ?? $newStatus
            );

            Log::info("Offer Status Change: {$offer->id} - {$noteContent}");

            if ($newStatus === 'APPROVED') {
                // Potential future logic for auto-sale creation
                Log::info("Offer Approved: {$offer->id} - Activation logic pending.");
            }
        });

        $this->status = $newStatus;
        $this->success('Durum Güncellendi', 'Teklif durumu başarıyla güncellendi.');

        $this->dispatch('offer-status-updated', [
            'offerId' => $this->offerId,
            'oldStatus' => $oldStatus,
            'newStatus' => $newStatus,
        ]);
    }

    /**
     * @purpose Görüntüleme modundan düzenleme moduna geçiş
     */
    public function toggleEditMode(): void
    {
        $this->authorize('offers.edit');
        $this->isViewMode = false;
    }
}
