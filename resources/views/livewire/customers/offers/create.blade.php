<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Customer;
use App\Models\Service;
use App\Models\PriceDefinition;
use App\Models\Offer;
use App\Models\OfferItem;
use Mary\Traits\Toast;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ReferenceItem;
use App\Models\OfferAttachment;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Teklif Oluştur'])]
    class extends Component {
    use Toast, WithFileUploads;

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

    // Offer Items
    public $items = [];

    // Modal State
    public $showServiceModal = false;
    public $selectedYear = 0;
    public $modalCategory = '';
    public $modalServiceName = '';
    public $showItemDescriptionModal = false;
    public $editingItemIndex = null;
    public $itemDescriptionTemp = '';

    // State Management
    public $isViewMode = false;
    public $offerId = null;
    public string $activeTab = 'info';

    // Manual Entry Modal State
    public $showManualEntryModal = false;
    public $manualItems = [];

    // Attachment Modal State
    public $showAttachmentModal = false;
    public $attachments = [];
    public $attachmentTitle = '';
    public $attachmentDescription = '';
    public $attachmentPrice = 0;
    public $attachmentFile = null;
    public $editingAttachmentIndex = null;

    public function openManualEntryModal(): void
    {
        $this->manualItems = [
            [
                'service_name' => '',
                'description' => '',
                'duration' => null,
                'price' => 0,
                'quantity' => 1
            ]
        ];
        $this->showManualEntryModal = true;
    }

    public function addManualItemRow(): void
    {
        $this->manualItems[] = [
            'service_name' => '',
            'description' => '',
            'duration' => null,
            'price' => 0,
            'quantity' => 1
        ];
    }

    public function removeManualItemRow(int $index): void
    {
        unset($this->manualItems[$index]);
        $this->manualItems = array_values($this->manualItems);
    }

    public function saveManualItems(): void
    {
        $this->validate([
            'manualItems.*.service_name' => 'required|string|max:255',
            'manualItems.*.description' => 'nullable|string',
            'manualItems.*.duration' => 'nullable|integer|min:1',
            'manualItems.*.price' => 'required|numeric|min:0',
            'manualItems.*.quantity' => 'required|integer|min:1',
        ], [
            'manualItems.*.service_name.required' => 'Hizmet adı zorunludur.',
            'manualItems.*.price.required' => 'Fiyat zorunludur.',
        ]);

        $count = count($this->manualItems);

        foreach ($this->manualItems as $item) {
            $this->items[] = [
                'service_id' => null, // Manual item
                'service_name' => $item['service_name'],
                'description' => $item['description'] ?? '',
                'price' => (float) $item['price'],
                'currency' => $this->currency,
                'duration' => $item['duration'] ? (int) $item['duration'] : null,
                'quantity' => (int) $item['quantity'],
            ];
        }

        $this->showManualEntryModal = false;
        $this->manualItems = [];
        $this->success('Başarılı', $count . ' kalem eklendi.');
    }
    // Attachment Methods
    public function openAttachmentModal(): void
    {
        $this->resetAttachmentForm();
        $this->showAttachmentModal = true;
    }

    public function closeAttachmentModal(): void
    {
        $this->showAttachmentModal = false;
        $this->resetAttachmentForm();
    }

    private function resetAttachmentForm(): void
    {
        $this->attachmentTitle = '';
        $this->attachmentDescription = '';
        $this->attachmentPrice = 0;
        $this->attachmentFile = null;
        $this->editingAttachmentIndex = null;
    }

    public function saveAttachment(): void
    {
        $this->resetErrorBag();

        $this->validate([
            'attachmentTitle' => 'required|string|max:255',
            'attachmentDescription' => 'nullable|string',
            'attachmentPrice' => 'required|numeric|min:0',
            'attachmentFile' => $this->editingAttachmentIndex === null ? 'required|file|mimes:pdf,doc,docx|max:25600' :
                'nullable|file|mimes:pdf,doc,docx|max:25600',
        ], [
            'attachmentTitle.required' => 'Lütfen ek için bir başlık giriniz.',
            'attachmentPrice.required' => 'Lütfen bir fiyat belirtiniz.',
            'attachmentFile.required' => 'Lütfen bir dosya seçiniz.',
            'attachmentFile.mimes' => 'Sadece PDF veya Microsoft Word (.doc, .docx) formatları kabul edilmektedir.',
            'attachmentFile.max' => 'Dosya boyutu çok büyük. Maksimum 25 MB yükleyebilirsiniz.',
        ]);

        try {
            $minioService = app(\App\Services\MinioService::class);

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
            \Illuminate\Support\Facades\Log::error('Teklif Eki Yükleme Hatası: ' . $e->getMessage());
            $this->error('Hata', 'Dosya yüklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function editAttachment(int $index): void
    {
        $attachment = $this->attachments[$index];
        $this->editingAttachmentIndex = $index;
        $this->attachmentTitle = $attachment['title'];
        $this->attachmentDescription = $attachment['description'] ?? '';
        $this->attachmentPrice = $attachment['price'];
        $this->showAttachmentModal = true;
    }

    public function removeAttachment(int $index): void
    {
        try {
            // Delete file from Minio
            $filePath = $this->attachments[$index]['file_path'] ?? null;
            if ($filePath) {
                $minioService = app(\App\Services\MinioService::class);
                $result = $minioService->deleteFile($filePath);

                if ($result) {
                    \Illuminate\Support\Facades\Log::info("Teklif Eki Başarıyla Silindi: {$filePath}");
                } else {
                    \Illuminate\Support\Facades\Log::error("Teklif Eki Silinemedi (Minio Hatası): {$filePath}");
                }
            }

            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
            $this->success('Başarılı', 'Ek silindi.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Minio silme HATASI - Yol: {$filePath} - Hata: " . $e->getMessage());
            $this->error('Hata', 'Dosya silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function downloadAttachment(int $index): mixed
    {
        $attachment = $this->attachments[$index] ?? null;

        if (!$attachment || empty($attachment['file_path'])) {
            $this->error('Hata', 'Dosya bulunamadı.');
            return null;
        }

        try {
            $minioService = app(\App\Services\MinioService::class);
            return $minioService->downloadFile(
                $attachment['file_path'],
                $attachment['file_name']
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("İndirme Hatası: " . $e->getMessage());
            $this->error('Hata', 'Dosya indirilemedi: ' . $e->getMessage());
            return null;
        }
    }

    // Reference Data
    public $customers = [];
    public $customerServices = [];
    public $priceDefinitions = [];
    public $categories = [];
    public $vatRates = [];

    public function mount(?string $offer = null): void
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

    public function updatedCustomerId(): void
    {
        $this->loadCustomerServices();
    }

    private function loadCustomerServices(): void
    {
        if ($this->customer_id) {
            $this->customerServices = Service::where('customer_id', $this->customer_id)
                ->where('status', 'ACTIVE')
                ->whereYear('start_date', $this->selectedYear)
                ->get(['id', 'service_name', 'service_price', 'service_duration', 'service_currency', 'description', 'end_date'])
                ->toArray();
        } else {
            $this->customerServices = [];
        }
    }

    public function updatedValidDays(): void
    {
        $this->valid_until = Carbon::now()->addDays($this->valid_days)->format('Y-m-d');
    }

    public function updatedSelectedYear(): void
    {
        $this->loadCustomerServices();
    }

    public function updatedModalCategory(): void
    {
        $this->modalServiceName = '';
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

    public function openServiceModal(): void
    {
        if (!$this->customer_id) {
            $this->error('Uyarı', 'Lütfen önce bir müşteri seçin.');
            return;
        }
        $this->showServiceModal = true;
        $this->loadCustomerServices();
    }

    public function closeServiceModal(): void
    {
        $this->showServiceModal = false;
        $this->modalCategory = '';
        $this->modalServiceName = '';
    }

    public function addServiceFromExisting(string $serviceId): void
    {
        $service = collect($this->customerServices)->firstWhere('id', $serviceId);

        if ($service) {
            // Currency sync & validation
            if (count($this->items) > 0) {
                if ($service['service_currency'] !== $this->currency) {
                    $this->error('Para Birimi Uyumsuzluğu', "Bu teklif {$this->currency} cinsindendir. {$service['service_currency']} birimli bir hizmet ekleyemezsiniz.");
                    return;
                }
            } else {
                $this->currency = $service['service_currency'];
            }

            $this->items[] = [
                'service_id' => $service['id'],
                'service_name' => $service['service_name'],
                'description' => ($service['description'] ?? '') . " (Uzatma)",
                'price' => $service['service_price'],
                'currency' => $service['service_currency'],
                'duration' => $service['service_duration'] ?? 1,
                'quantity' => 1,
            ];

            $this->success('Başarılı', 'Hizmet uzatma kalemi eklendi.');
            $this->closeServiceModal();
        }
    }

    public function addServiceFromPriceDefinition(): void
    {
        if (!$this->modalServiceName) {
            $this->error('Uyarı', 'Lütfen bir hizmet seçin.');
            return;
        }

        $priceDef = collect($this->priceDefinitions)
            ->where('category', $this->modalCategory)
            ->firstWhere('name', $this->modalServiceName);

        if ($priceDef) {
            // Currency sync & validation
            if (count($this->items) > 0) {
                if ($priceDef['currency'] !== $this->currency) {
                    $this->error('Para Birimi Uyumsuzluğu', "Bu teklif {$this->currency} cinsindendir. {$priceDef['currency']} birimli bir hizmet ekleyemezsiniz.");
                    return;
                }
            } else {
                $this->currency = $priceDef['currency'];
            }

            $this->items[] = [
                'service_id' => null,
                'service_name' => $priceDef['name'],
                'description' => $priceDef['description'] ?? '',
                'price' => $priceDef['price'],
                'currency' => $priceDef['currency'],
                'duration' => $priceDef['duration'] ?? 1,
                'quantity' => 1,
            ];

            $this->success('Başarılı', 'Hizmet eklendi.');
            $this->closeServiceModal();
        }
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function openItemDescriptionModal(int $index): void
    {
        $this->editingItemIndex = $index;
        $this->itemDescriptionTemp = $this->items[$index]['description'] ?? '';
        $this->showItemDescriptionModal = true;
    }

    public function saveItemDescription(): void
    {
        if ($this->editingItemIndex !== null) {
            $this->items[$this->editingItemIndex]['description'] = Str::limit($this->itemDescriptionTemp, 50, '');
            $this->showItemDescriptionModal = false;
            $this->editingItemIndex = null;
            $this->itemDescriptionTemp = '';
            $this->success('Başarılı', 'Açıklama güncellendi.');
        }
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

    public function save(): void
    {
        $this->validate([
            'customer_id' => 'required',
            'title' => 'required|string|max:255',
            'valid_until' => 'required|date',
            'items' => 'required|array|min:1',
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
                    'service_id' => $item['service_id'],
                    'service_name' => $item['service_name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'currency' => $item['currency'],
                    'duration' => $item['duration'],
                    'quantity' => $item['quantity'],
                ]);
            }

            // Sync Attachments
            // First delete old attachments (except those we keep, but easier to delete all and recreate for now or just delete all since we have full state in $this->attachments)
            // Correction: Recreating is safer for sync as long as we don't lose the file paths.
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
        $this->redirect('/dashboard/customers?tab=offers');
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

    public function cancel(): void
    {
        // Clean up unsaved attachments from Minio
        if (!empty($this->attachments)) {
            $minioService = app(\App\Services\MinioService::class);

            foreach ($this->attachments as $attachment) {
                // If the attachment doesn't have an ID, it means it hasn't been saved to the DB yet
                // and was just uploaded in this session.
                if (!isset($attachment['id'])) {
                    if (isset($attachment['file_path'])) {
                        try {
                            $minioService->deleteFile($attachment['file_path']);
                            \Illuminate\Support\Facades\Log::info("Cancelled Offer Creation: Deleted temporary file: " . $attachment['file_path']);
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Failed to delete file on cancel: " . $e->getMessage());
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

    public function toggleEditMode(): void
    {
        $this->isViewMode = false;
    }

    public function delete(): void
    {
        if ($this->offerId) {
            Offer::findOrFail($this->offerId)->delete();
            $this->success('Teklif Silindi', 'Teklif başarıyla silindi.');
            $this->redirect('/dashboard/customers?tab=offers');
        }
    }

}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        @include('livewire.customers.offers.partials._header', ['isViewMode' => $isViewMode, 'title' => $title, 'offerId' => $offerId])


        {{-- Tab Navigation --}}
        @include('livewire.customers.offers.partials._tabs', ['isViewMode' => $isViewMode, 'activeTab' => $activeTab])


        <div class="grid grid-cols-12 gap-6">
            {{-- Left Column (8/12) --}}
            <div class="col-span-8">
                @if($activeTab === 'info')
                    <div class="space-y-6">
                        @include('livewire.customers.offers.partials._customer_info', [
                            'isViewMode' => $isViewMode,
                            'customers' => $customers,
                            'customer_id' => $customer_id,
                            'status' => $status,
                            'valid_days' => $valid_days,
                            'currency' => $currency,
                            'discount_type' => $discount_type,
                            'discount_value' => $discount_value,
                            'vat_rate' => $vat_rate,
                            'vatRates' => $vatRates
                        ])


                        @include('livewire.customers.offers.partials._title_description', [
                            'isViewMode' => $isViewMode,
                            'title' => $title,
                            'description' => $description
                        ])


                        @include('livewire.customers.offers.partials._items_table', [
                            'isViewMode' => $isViewMode,
                            'items' => $items
                        ])


                            @include('livewire.customers.offers.partials._attachments', [
                                'isViewMode' => $isViewMode,
                                'attachments' => $attachments
                            ])
                        </div>
                @endif
                @if($activeTab === 'messages')
                    <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                        <x-mary-icon name="o-chat-bubble-left-right" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Henüz mesaj bulunmuyor</div>
                    </div>
                @endif

                @if($activeTab === 'notes')
                    <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                        <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Henüz not bulunmuyor</div>
                    </div>
                @endif

                @if($activeTab === 'downloads')
                    <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                        <x-mary-icon name="o-arrow-down-tray" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Henüz indirme bulunmuyor</div>
                    </div>
                @endif
            </div>

            {{-- Right Column (4/12) - Summary --}}
            <div class="col-span-4">
                @include('livewire.customers.offers.partials._summary', [
                    'isViewMode' => $isViewMode,
                    'currency' => $currency,
                    'discount_type' => $discount_type,
                    'discount_value' => $discount_value,
                    'vat_rate' => $vat_rate,
                    'valid_until' => $valid_until,
                    'items' => $items
                ])

            </div>
        </div>
    </div>

    @include('livewire.customers.offers.partials._modals', [
        'customerServices' => $customerServices,
        'categories' => $categories,
        'priceDefinitions' => $priceDefinitions,
        'manualItems' => $manualItems,
        'currency' => $currency,
        'attachments' => $attachments,
        'editingAttachmentIndex' => $editingAttachmentIndex,
        'itemDescriptionTemp' => $itemDescriptionTemp,
        'modalCategory' => $modalCategory,
        'modalServiceName' => $modalServiceName
    ])
</div>