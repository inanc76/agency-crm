<?php

namespace App\Livewire\Customers\Offers\Traits;

use App\Models\Offer;
use App\Models\OfferAttachment;
use App\Models\OfferItem;
use App\Services\MinioService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                    🏛️ MİMARIN NOTU - CONSTITUTION V11 (SLIM)                                     ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasOfferActions Trait (Core CRUD Operations)                                              ║
 * ║  🎯 ANA GÖREV: Teklif yaşam döngüsü yönetimi - Create, Update, Delete, Status Change                           ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • save(): Teklif oluşturma ve güncelleme (DB Transaction)                                                     ║
 * ║  • cancel(): İptal işlemi ve geçici dosya temizliği                                                            ║
 * ║  • toggleEditMode(): Görüntüleme ↔ Düzenleme modu geçişi                                                       ║
 * ║  • statusChange(): Yaşam döngüsü statü yönetimi (DRAFT → SENT → APPROVED → REJECTED)                           ║
 * ║  • delete(): Kalıcı silme işlemi                                                                                ║
 * ║                                                                                                                  ║
 * ║  📦 TRAIT BAĞIMLILIKLARI (Composition):                                                                         ║
 * ║  • HasOfferDataLoader: Veri yükleme (mount, initReferenceData, loadOfferData, loadCustomerServices)            ║
 * ║  • HasOfferAttachments: Ek dosya yönetimi (openAttachmentModal, saveAttachment, etc.)                          ║
 * ║  • HasOfferItems: Kalem yönetimi (addServiceFromExisting, saveManualItems, etc.)                               ║
 * ║  • HasOfferCalculations: Hesaplamalar (calculateTotals, generateOfferNumber, etc.)                             ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK KATMANLARI:                                                                                        ║
 * ║  • offers.create: Yeni teklif oluşturma                                                                        ║
 * ║  • offers.edit: Mevcut teklif düzenleme                                                                        ║
 * ║  • offers.delete: Teklif silme                                                                                 ║
 * ║  • offers.status: Statü değişikliği                                                                            ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasOfferActions
{
    use HasOfferDataLoader;   // 📊 Veri yükleme trait'i
    use HasOfferAttachments;  // 📎 Ek dosya yönetimi trait'i

    /**
     * @purpose Teklifi veritabanına kaydetme (yeni oluşturma veya güncelleme)
     * @return void
     * 🔐 Security: offers.create (new) or offers.edit (existing) - Authorization enforced
     * 📢 Events: Success toast, 'offer-saved' dispatch, redirect to customers page
     * 
     * State Dependencies: $this->offerId, $this->items, $this->attachments, tüm form alanları
     */
    public function save(): void
    {
        // 🔐 Security: Authorization check based on operation type (offers.create or offers.edit)
        if ($this->offerId) {
            $this->authorize('offers.edit');
        } else {
            $this->authorize('offers.create');
        }

        $this->validate([
            'customer_id' => 'required',
            'title' => 'required|string|max:255',
            'valid_until' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0.01',
            'items.*.quantity' => 'required|numeric|min:1',
        ], [
            'items.*.service_name.required' => 'Hizmet adı zorunludur.',
            'items.*.price.min' => 'Fiyat 0.01 veya daha büyük olmalıdır.',
        ]);

        $totals = $this->calculateTotals();

        DB::transaction(function () use ($totals) {
            $offerData = [
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
            ];

            if ($this->offerId) {
                $offer = Offer::findOrFail($this->offerId);
                $offer->update($offerData);
                $offer->items()->delete();
            } else {
                $this->offerId = Str::uuid()->toString();
                $offerData['id'] = $this->offerId;
                $offer = Offer::create($offerData);
            }

            // Create items
            foreach ($this->items as $item) {
                OfferItem::create([
                    'id' => Str::uuid()->toString(),
                    'offer_id' => $offer->id,
                    'service_id' => $item['service_id'] ?? null,
                    'service_name' => $item['service_name'],
                    'description' => $item['description'] ?? '',
                    'price' => $item['price'],
                    'currency' => $item['currency'],
                    'duration' => $item['duration'],
                    'quantity' => $item['quantity'],
                ]);
            }

            // Sync Attachments
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
        });

        $this->success('İşlem Başarılı', 'Teklif başarıyla kaydedildi.');
        $this->dispatch('offer-saved');
        $this->redirect('/dashboard/customers?tab=offers');
    }

    /**
     * @purpose Teklif düzenlemeyi iptal etme ve geçici dosyaları temizleme
     * @return void
     * 🔐 Security: Geçici dosya temizleme, MinIO'dan silme yetkisi
     * 📢 Events: Redirect (yeni teklif) veya loadOfferData() (mevcut teklif)
     * 
     * State Dependencies: $this->offerId, $this->attachments
     */
    public function cancel(): void
    {
        // Clean up unsaved attachments from Minio
        if (!empty($this->attachments)) {
            $minioService = app(MinioService::class);

            foreach ($this->attachments as $attachment) {
                // If the attachment doesn't have an ID, it means it hasn't been saved to the DB yet
                // and was just uploaded in this session.
                if (!isset($attachment['id'])) {
                    if (isset($attachment['file_path'])) {
                        try {
                            $minioService->deleteFile($attachment['file_path']);
                            Log::info("Cancelled Offer Creation: Deleted temporary file: " . $attachment['file_path']);
                        } catch (\Exception $e) {
                            Log::error("Failed to delete file on cancel: " . $e->getMessage());
                        }
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

    /**
     * @purpose Görüntüleme modundan düzenleme moduna geçiş
     * @return void
     * 🔐 Security: offers.edit - Authorization enforced
     * 📢 Events: $this->isViewMode = false ile düzenleme moduna geçiş
     * 
     * State Dependencies: $this->isViewMode
     */
    public function toggleEditMode(): void
    {
        // 🔐 Security: Require edit permission to enter edit mode
        $this->authorize('offers.edit');

        $this->isViewMode = false;
    }

    /**
     * 🔄 statusChange
     * @purpose Teklifin yaşam döngüsü statüsünü (DRAFT/SENT/APPROVED/REJECTED) yönetir.
     * @param string $newStatus Yeni statü değeri
     * @return void
     * 
     * 🔐 Security: authorize('offers.status') - Yetkisiz statü değişimlerini engeller
     * 📢 Events: Dispatch 'offer-status-updated' for UI & Notification sync
     * 🔗 Side Effects:
     *    - Statü değişikliği için polymorphic sistem notu oluşturur
     *    - APPROVED durumunda ilişkili servislerin aktivasyon potansiyelini hazırlar
     *    - Tarihçe (history) kaydı tutar
     * 
     * 🎯 Business Rules:
     *    - Sadece geçerli statüler: DRAFT, SENT, APPROVED, REJECTED
     *    - APPROVED/REJECTED final states → sadece DRAFT'a dönüş izinli
     *    - Her statü değişimi sistem notu ile loglanır
     * 
     * State Dependencies: $this->offerId, $this->status
     */
    public function statusChange(string $newStatus): void
    {
        // 🔐 Security: Require status change permission
        $this->authorize('offers.status');

        // Validate status
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

        // Prevent changing from final states (except to DRAFT)
        if (in_array($oldStatus, ['APPROVED', 'REJECTED']) && $newStatus !== 'DRAFT') {
            $this->error('Uyarı', 'Onaylanmış veya reddedilmiş teklifler sadece taslağa döndürülebilir.');
            return;
        }

        // Prevent no-op changes
        if ($oldStatus === $newStatus) {
            $this->warning('Bilgi', 'Teklif zaten bu durumda.');
            return;
        }

        DB::transaction(function () use ($offer, $oldStatus, $newStatus) {
            // Update offer status
            $offer->update(['status' => $newStatus]);

            // 📝 Create system note for history tracking (Polymorphic Note)
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

            // Log for now until Note model is implemented
            Log::info("Offer Status Change: {$offer->id} - {$noteContent}", [
                'offer_id' => $offer->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'user_id' => auth()->id(),
            ]);

            // 🎯 Side Effect: APPROVED status handling
            if ($newStatus === 'APPROVED') {
                Log::info("Offer Approved: {$offer->id} - Service activation logic placeholder");
            }
        });

        // Update local state
        $this->status = $newStatus;

        $statusLabels = [
            'DRAFT' => 'Taslak',
            'SENT' => 'Gönderildi',
            'APPROVED' => 'Onaylandı',
            'REJECTED' => 'Reddedildi',
        ];

        $this->success('Durum Güncellendi', "Teklif durumu '{$statusLabels[$newStatus]}' olarak değiştirildi.");

        // 📢 Dispatch event for UI & Notification sync
        $this->dispatch('offer-status-updated', [
            'offerId' => $this->offerId,
            'oldStatus' => $oldStatus,
            'newStatus' => $newStatus,
        ]);
    }

    /**
     * @purpose Teklifi veritabanından kalıcı olarak silme
     * @return void
     * 🔐 Security: offers.delete - Authorization enforced
     * 📢 Events: Success toast, redirect to customers page
     * 
     * State Dependencies: $this->offerId
     */
    public function delete(): void
    {
        // 🔐 Security: Require delete permission
        $this->authorize('offers.delete');

        if ($this->offerId) {
            Offer::findOrFail($this->offerId)->delete();
            $this->success('Teklif Arşivlendi', 'Teklif başarıyla arşivlendi ve çöp kutusuna taşındı.');
            $this->redirect('/dashboard/customers?tab=offers');
        }
    }
}
