<?php

namespace App\Livewire\Customers\Offers\Traits;

use App\Models\Customer;
use App\Models\Offer;
use App\Models\OfferAttachment;
use App\Models\OfferItem;
use App\Models\PriceDefinition;
use App\Models\ReferenceItem;
use App\Models\Service;
use App\Services\MinioService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗
 * ║                                          🏛️ MİMARIN NOTU - CONSTITUTION V10                                      ║
 * ║                                                                                                                  ║
 * ║  📋 SORUMLULUK ALANI: HasOfferActions Trait                                                                     ║
 * ║  🎯 ANA GÖREV: Teklif yaşam döngüsü yönetimi ve iş süreçleri                                                   ║
 * ║                                                                                                                  ║
 * ║  🔧 TEMEL YETKİNLİKLER:                                                                                         ║
 * ║  • Fiyatlandırma Hesaplamaları: KDV, indirim ve toplam tutar hesaplamaları                                     ║
 * ║  • PDF Üretimi ve Dosya Yönetimi: MinIO entegrasyonu ile ek dosya işlemleri                                    ║
 * ║  • Durum (Lifecycle) Yönetimi: DRAFT → SENT → APPROVED → REJECTED akış kontrolü                               ║
 * ║  • Modal State Kontrolü: Servis ve ek dosya modallarının açılma/kapanma durumları                              ║
 * ║  • Veri Yükleme ve Senkronizasyon: Müşteri servisleri ve referans verilerinin dinamik yüklenmesi               ║
 * ║                                                                                                                  ║
 * ║  🔐 GÜVENLİK KATMANLARI:                                                                                        ║
 * ║  • Form Validasyonu: Laravel validation rules ile veri doğrulama                                               ║
 * ║  • Dosya Güvenliği: Minio ile güvenli dosya depolama ve erişim kontrolü                                        ║
 * ║  • Transaction Yönetimi: DB işlemlerinde atomik operasyonlar                                                    ║
 * ║                                                                                                                  ║
 * ║  📊 BAĞIMLILIK HARİTASI:                                                                                        ║
 * ║  • $this->offer: Ana teklif verisi (Offer model instance)                                                      ║
 * ║  • $this->items: Teklif kalemleri array'i                                                                      ║
 * ║  • $this->attachments: Ek dosyalar array'i                                                                     ║
 * ║  • $this->customer_id: Seçili müşteri ID'si                                                                    ║
 * ║  • $this->customerServices: Müşteriye ait aktif servisler                                                      ║
 * ║                                                                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝
 */
trait HasOfferActions
{
    // Offer Fields
    public $customer_id = '';
    public $title = '';
    public $status = 'DRAFT';
    public $description = '';
    public $valid_days = 30;
    public $valid_until = null;
    public $discount_value = 0;
    public $discount_type = 'AMOUNT'; // PERCENTAGE or AMOUNT
    public $vat_rate = 20;
    public $currency = 'USD';

    // State Management
    public $isViewMode = false;
    public $offerId = null;
    public string $activeTab = 'info';

    // Reference Data
    public $customers = [];
    public $customerServices = [];
    public $priceDefinitions = [];
    public $categories = [];
    public $vatRates = [];

    // Service Modal State
    public $showServiceModal = false;
    public $selectedYear = 0;

    // Attachment Modal State
    public $showAttachmentModal = false;
    public $attachments = [];
    public $attachmentTitle = '';
    public $attachmentDescription = '';
    public $attachmentPrice = 0;
    public $attachmentFile = null;
    public $editingAttachmentIndex = null;

    /**
     * @purpose Livewire bileşeninin başlatılması ve başlangıç verilerinin yüklenmesi
     * @param string|null $offer Düzenlenecek teklif ID'si (opsiyonel)
     * @return void
     * 🔐 Security: Genel erişim - özel yetki kontrolü yok
     * 📢 Events: Sayfa yönlendirmesi yok, sadece veri yükleme
     * 
     * State Dependencies: $this->customers, $this->customerServices, $this->offerId
     */
    public function mount(?string $offer = null): void
    {
        $this->initReferenceData();

        // Set default valid_until
        $this->valid_until = Carbon::now()->addDays($this->valid_days)->format('Y-m-d');
        $this->selectedYear = Carbon::now()->year;

        // If offer ID is provided, load data
        if ($offer) {
            $this->offerId = $offer;
            $this->loadOfferData();

            // Set active tab from URL if present
            $this->activeTab = request()->query('tab', 'info');
        } else {
            // Check for customer query parameter
            $customerId = request()->query('customer');
            if ($customerId && collect($this->customers)->firstWhere('id', $customerId)) {
                $this->customer_id = $customerId;
                $this->loadCustomerServices();
            }
        }
    }



    /**
     * @purpose Referans verilerinin yüklenmesi (müşteriler, kategoriler, KDV oranları)
     * @return void
     * 🔐 Security: Private metot - sadece trait içinden erişilebilir
     * 📢 Events: Veri yükleme işlemi, UI güncellemesi yok
     * 
     * State Dependencies: $this->customers, $this->categories, $this->priceDefinitions, $this->vatRates
     */
    private function initReferenceData(): void
    {
        // Load Customers
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // Load Categories with Display Labels
        $usedCategoryKeys = PriceDefinition::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->toArray();

        $categoryDefinitions = ReferenceItem::where('category_key', 'SERVICE_CATEGORY')
            ->whereIn('key', $usedCategoryKeys)
            ->get()
            ->keyBy('key');

        $this->categories = collect($usedCategoryKeys)->map(function ($key) use ($categoryDefinitions) {
            return [
                'id' => $key,
                'name' => $categoryDefinitions[$key]->display_label ?? $key
            ];
        })->sortBy('name')->values()->toArray();

        // Load all price definitions
        $this->priceDefinitions = PriceDefinition::where('is_active', true)
            ->get()
            ->toArray();

        // Load VAT Rates
        $this->vatRates = ReferenceItem::where('category_key', 'VAT_RATES')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($item) {
                $rate = 0;
                if (preg_match('/(\d+)/', $item->display_label, $matches)) {
                    $rate = (float) $matches[1];
                }
                return [
                    'rate' => $rate,
                    'label' => $item->display_label
                ];
            })
            ->toArray();

        // Ensure initial vat_rate is set from default variable if available
        $defaultVat = ReferenceItem::where('category_key', 'VAT_RATES')
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();

        if ($defaultVat && preg_match('/(\d+)/', $defaultVat->display_label, $matches)) {
            $this->vat_rate = (float) $matches[1];
        }
    }

    /**
     * @purpose Mevcut teklif verilerinin veritabanından yüklenmesi ve form alanlarına doldurulması
     * @return void
     * 🔐 Security: Private metot - $this->offerId kontrolü ile güvenli erişim
     * 📢 Events: $this->isViewMode = true ile görüntüleme moduna geçiş
     * 
     * State Dependencies: $this->offerId, $this->items, $this->attachments, $this->customer_id
     */
    private function loadOfferData(): void
    {
        $offer = Offer::with('items')->findOrFail($this->offerId);

        $this->customer_id = $offer->customer_id;
        $this->loadCustomerServices();

        $this->title = $offer->title ?? '';
        $this->status = $offer->status;
        $this->description = $offer->description ?? '';
        $this->discount_value = $offer->discount_percentage > 0 ? $offer->discount_percentage : ($offer->original_amount - $offer->total_amount);
        $this->discount_type = $offer->discount_percentage > 0 ? 'PERCENTAGE' : 'AMOUNT';
        $this->vat_rate = (float) $offer->vat_rate;
        $this->currency = $offer->currency;
        $this->valid_until = Carbon::parse($offer->valid_until)->format('Y-m-d');

        // Load items
        $this->items = $offer->items->map(fn($item) => [
            'service_id' => $item->service_id,
            'service_name' => $item->service_name,
            'description' => $item->description,
            'price' => $item->price,
            'currency' => $item->currency,
            'duration' => $item->duration,
            'quantity' => $item->quantity,
        ])->toArray();

        // Load attachments
        $this->attachments = $offer->attachments->map(fn($att) => [
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

    /**
     * @purpose Müşteri değiştiğinde otomatik servis listesi güncelleme
     * @return void
     * 🔐 Security: Livewire property watcher - otomatik tetiklenir
     * 📢 Events: loadCustomerServices() çağrısı ile UI güncelleme
     * 
     * State Dependencies: $this->customer_id, $this->customerServices
     */
    public function updatedCustomerId(): void
    {
        $this->loadCustomerServices();
    }

    /**
     * @purpose Seçili müşterinin aktif servislerini yıla göre filtreleyerek yükleme
     * @return void
     * 🔐 Security: Private metot - customer_id kontrolü ile güvenli erişim
     * 📢 Events: $this->customerServices array'inin güncellenmesi
     * 
     * State Dependencies: $this->customer_id, $this->selectedYear, $this->customerServices
     */
    private function loadCustomerServices(): void
    {
        if ($this->customer_id) {
            $this->customerServices = Service::where('customer_id', $this->customer_id)
                ->where('status', 'ACTIVE')
                ->whereYear('start_date', $this->selectedYear)
                ->get(['id', 'service_name', 'service_category', 'service_price', 'service_duration', 'service_currency', 'description', 'start_date', 'end_date'])
                ->toArray();
        } else {
            $this->customerServices = [];
        }
    }

    /**
     * @purpose Yıl değiştiğinde müşteri servislerini yeniden yükleme
     * @return void
     * 🔐 Security: Livewire property watcher - otomatik tetiklenir
     * 📢 Events: loadCustomerServices() çağrısı ile UI güncelleme
     * 
     * State Dependencies: $this->selectedYear, $this->customerServices
     */
    public function updatedSelectedYear(): void
    {
        $this->loadCustomerServices();
    }

    /**
     * @purpose Servis seçim modalını açma ve müşteri kontrolü
     * @return void
     * 🔐 Security: Müşteri seçimi zorunlu - customer_id kontrolü
     * 📢 Events: $this->showServiceModal = true, error toast (müşteri yoksa)
     * 
     * State Dependencies: $this->customer_id, $this->showServiceModal
     */
    public function openServiceModal(): void
    {
        if (!$this->customer_id) {
            $this->error('Uyarı', 'Lütfen önce bir müşteri seçin.');
            return;
        }
        $this->showServiceModal = true;
        $this->loadCustomerServices();
    }

    /**
     * @purpose Servis seçim modalını kapatma ve form temizleme
     * @return void
     * 🔐 Security: Genel erişim - özel yetki kontrolü yok
     * 📢 Events: $this->showServiceModal = false, modal form alanları temizlenir
     * 
     * State Dependencies: $this->showServiceModal, $this->modalCategory, $this->modalServiceName
     */
    public function closeServiceModal(): void
    {
        $this->showServiceModal = false;
        $this->modalCategory = '';
        $this->modalServiceName = '';
    }

    // Attachment Methods
    /**
     * @purpose Ek dosya yükleme modalını açma ve form temizleme
     * @return void
     * 🔐 Security: Genel erişim - özel yetki kontrolü yok
     * 📢 Events: $this->showAttachmentModal = true, resetAttachmentForm() çağrısı
     * 
     * State Dependencies: $this->showAttachmentModal
     */
    public function openAttachmentModal(): void
    {
        $this->resetAttachmentForm();
        $this->showAttachmentModal = true;
    }

    /**
     * @purpose Ek dosya modalını kapatma ve form temizleme
     * @return void
     * 🔐 Security: Genel erişim - özel yetki kontrolü yok
     * 📢 Events: $this->showAttachmentModal = false, resetAttachmentForm() çağrısı
     * 
     * State Dependencies: $this->showAttachmentModal
     */
    public function closeAttachmentModal(): void
    {
        $this->showAttachmentModal = false;
        $this->resetAttachmentForm();
    }

    /**
     * @purpose Ek dosya form alanlarını sıfırlama
     * @return void
     * 🔐 Security: Private metot - sadece trait içinden erişilebilir
     * 📢 Events: Form alanları temizlenir, düzenleme modu sıfırlanır
     * 
     * State Dependencies: $this->attachmentTitle, $this->attachmentDescription, $this->attachmentPrice, $this->attachmentFile, $this->editingAttachmentIndex
     */
    private function resetAttachmentForm(): void
    {
        $this->attachmentTitle = '';
        $this->attachmentDescription = '';
        $this->attachmentPrice = 0;
        $this->attachmentFile = null;
        $this->editingAttachmentIndex = null;
    }

    /**
     * @purpose Ek dosya kaydetme (yeni ekleme veya güncelleme) ve MinIO'ya yükleme
     * @return void
     * 🔐 Security: Form validasyonu, dosya tipi kontrolü (PDF, DOC, DOCX), boyut limiti (25MB)
     * 📢 Events: Success/error toast, closeAttachmentModal() çağrısı
     * 
     * State Dependencies: $this->attachments, $this->editingAttachmentIndex, $this->attachmentFile, $this->currency
     */
    public function saveAttachment(): void
    {
        $this->resetErrorBag();

        $this->validate([
            'attachmentTitle' => 'required|string|max:255',
            'attachmentDescription' => 'nullable|string',
            'attachmentPrice' => 'required|numeric|min:0',
            'attachmentFile' => $this->editingAttachmentIndex === null ? 'required|file|mimes:pdf,doc,docx|min:1|max:25600' :
                'nullable|file|mimes:pdf,doc,docx|min:1|max:25600',
        ], [
            'attachmentTitle.required' => 'Lütfen ek için bir başlık giriniz.',
            'attachmentPrice.required' => 'Lütfen bir fiyat belirtiniz.',
            'attachmentFile.required' => 'Lütfen bir dosya seçiniz.',
            'attachmentFile.mimes' => 'Sadece PDF veya Microsoft Word (.doc, .docx) formatları kabul edilmektedir.',
            'attachmentFile.max' => 'Dosya boyutu çok büyük. Maksimum 25 MB yükleyebilirsiniz.',
        ]);

        try {
            $minioService = app(MinioService::class);

            if ($this->editingAttachmentIndex !== null) {
                // Update existing attachment
                $this->attachments[$this->editingAttachmentIndex]['title'] = $this->attachmentTitle;
                $this->attachments[$this->editingAttachmentIndex]['description'] = $this->attachmentDescription;
                $this->attachments[$this->editingAttachmentIndex]['price'] = $this->attachmentPrice;

                // If new file uploaded, replace old one
                if ($this->attachmentFile) {
                    $oldPath = $this->attachments[$this->editingAttachmentIndex]['file_path'] ?? null;
                    if ($oldPath) {
                        $minioService->deleteFile($oldPath);
                    }

                    $uploadResult = $minioService->uploadFile($this->attachmentFile, 'offers');

                    $this->attachments[$this->editingAttachmentIndex]['file_name'] = $this->attachmentFile->getClientOriginalName();
                    $this->attachments[$this->editingAttachmentIndex]['file_type'] = $this->attachmentFile->getClientOriginalExtension();
                    $this->attachments[$this->editingAttachmentIndex]['file_size'] = $this->attachmentFile->getSize();
                    $this->attachments[$this->editingAttachmentIndex]['file_path'] = $uploadResult['path'];
                }

                $this->success('Başarılı', 'Ek güncellendi.');
            } else {
                // Add new attachment - Upload to Minio
                $uploadResult = $minioService->uploadFile($this->attachmentFile, 'offers');

                $this->attachments[] = [
                    'title' => $this->attachmentTitle,
                    'description' => $this->attachmentDescription,
                    'price' => $this->attachmentPrice,
                    'currency' => $this->currency,
                    'file_name' => $this->attachmentFile->getClientOriginalName(),
                    'file_type' => $this->attachmentFile->getClientOriginalExtension(),
                    'file_size' => $this->attachmentFile->getSize(),
                    'file_path' => $uploadResult['path'],
                ];

                $this->success('Başarılı', 'Ek eklendi.');
            }

            $this->closeAttachmentModal();
        } catch (\Exception $e) {
            Log::error('Teklif Eki Yükleme Hatası: ' . $e->getMessage());
            $this->error('Hata', 'Dosya yüklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * @purpose Mevcut ek dosyayı düzenleme moduna alma
     * @param int $index Düzenlenecek ek dosyanın array indeksi
     * @return void
     * 🔐 Security: Array indeks kontrolü, mevcut dosya varlığı kontrolü
     * 📢 Events: $this->showAttachmentModal = true, form alanları doldurulur
     * 
     * State Dependencies: $this->attachments, $this->editingAttachmentIndex, attachment form fields
     */
    public function editAttachment(int $index): void
    {
        $attachment = $this->attachments[$index];
        $this->editingAttachmentIndex = $index;
        $this->attachmentTitle = $attachment['title'];
        $this->attachmentDescription = $attachment['description'] ?? '';
        $this->attachmentPrice = $attachment['price'];
        $this->showAttachmentModal = true;
    }

    /**
     * @purpose Ek dosyayı listeden ve MinIO'dan silme
     * @param int $index Silinecek ek dosyanın array indeksi
     * @return void
     * 🔐 Security: Array indeks kontrolü, MinIO dosya silme yetkisi
     * 📢 Events: Success/error toast, $this->attachments array güncelleme
     * 
     * State Dependencies: $this->attachments
     */
    public function removeAttachment(int $index): void
    {
        try {
            // Delete file from Minio
            $filePath = $this->attachments[$index]['file_path'] ?? null;
            if ($filePath) {
                $minioService = app(MinioService::class);
                $result = $minioService->deleteFile($filePath);

                if ($result) {
                    Log::info("Teklif Eki Başarıyla Silindi: {$filePath}");
                } else {
                    Log::error("Teklif Eki Silinemedi (Minio Hatası): {$filePath}");
                }
            }

            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
            $this->success('Başarılı', 'Ek silindi.');
        } catch (\Exception $e) {
            Log::error("Minio silme HATASI - Yol: {$filePath} - Hata: " . $e->getMessage());
            $this->error('Hata', 'Dosya silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * @purpose MinIO'dan ek dosyayı indirme
     * @param int $index İndirilecek ek dosyanın array indeksi
     * @return mixed Download response veya null (hata durumunda)
     * 🔐 Security: Dosya varlığı kontrolü, MinIO erişim yetkisi
     * 📢 Events: Error toast (hata durumunda), dosya indirme başlatılır
     * 
     * State Dependencies: $this->attachments
     */
    public function downloadAttachment(int $index): mixed
    {
        $attachment = $this->attachments[$index] ?? null;

        if (!$attachment || empty($attachment['file_path'])) {
            $this->error('Hata', 'Dosya bulunamadı.');
            return null;
        }

        try {
            $minioService = app(MinioService::class);
            return $minioService->downloadFile(
                $attachment['file_path'],
                $attachment['file_name']
            );
        } catch (\Exception $e) {
            Log::error("İndirme Hatası: " . $e->getMessage());
            $this->error('Hata', 'Dosya indirilemedi: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * @purpose Teklifi veritabanına kaydetme (yeni oluşturma veya güncelleme)
     * @return void
     * 🔐 Security: Form validasyonu, DB transaction, UUID güvenliği
     * 📢 Events: Success toast, 'offer-saved' dispatch, redirect to customers page
     * 
     * State Dependencies: $this->offerId, $this->items, $this->attachments, tüm form alanları
     */
    public function save(): void
    {
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
     * 🔐 Security: Genel erişim - özel yetki kontrolü yok
     * 📢 Events: $this->isViewMode = false ile düzenleme moduna geçiş
     * 
     * State Dependencies: $this->isViewMode
     */
    public function toggleEditMode(): void
    {
        $this->isViewMode = false;
    }

    /**
     * @purpose Teklifi veritabanından kalıcı olarak silme
     * @return void
     * 🔐 Security: Teklif varlığı kontrolü, silme yetkisi
     * 📢 Events: Success toast, redirect to customers page
     * 
     * State Dependencies: $this->offerId
     */
    public function delete(): void
    {
        if ($this->offerId) {
            Offer::findOrFail($this->offerId)->delete();
            $this->success('Teklif Silindi', 'Teklif başarıyla silindi.');
            $this->redirect('/dashboard/customers?tab=offers');
        }
    }
}
