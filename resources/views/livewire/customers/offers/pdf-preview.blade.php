{{--
🛡️ ZIRHLI BELGELEME KARTI (V12.2)
-------------------------------------------------------------------------
MODÜL : Customers / Offers / PDF Preview
SORUMLULUK : Tekliflerin PDF'e dönüştürülmeden önceki son "canlı" önizleme hali.
MİMARİ : Monolitik -> Partial Tabanlı (Mission Zeta).

YAPI HİYERARŞİSİ:
1. _styles.blade.php : PDF ve Print'e özel CSS kuralları (DomPDF uyumlu).
2. _executive-summary.blade.php: Yönetici özeti, kapak sayfası ve genel toplam kartı.
3. _items-detail.blade.php : Sayfalandırılmış teklif detay tablosu (Items Loop).

VERİ KAYNAĞI:
- Veriler `Offer` modeli ve `PanelSetting` üzerinden beslenir.
- Hesaplamalar (KDV, Ara Toplam) `mount()` metodunda yapılarak `$sections` dizisine atanır.

⚠️ MİMARIN NOTU:
Bu sayfa hem tarayıcıda (HTML) hem de PDF motorunda (DomPDF/Browsershot) render edilir.
CSS değişikliklerinde "Print" modunu (media print) mutlaka test ediniz.
-------------------------------------------------------------------------
--}}
<?php

use App\Models\Offer;
use App\Models\PanelSetting;
use App\Services\MinioService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

/**
 * PDF Preview Page - Teklif Önizleme Sayfası
 * Layout: Flex %83 sol, %17 sağ
 */
new
    #[Layout('components.layouts.app', ['title' => 'Teklif PDF'])]
    class extends Component {
    public ?Offer $offer = null;

    public ?PanelSetting $settings = null;

    public string $offerNumber = '';

    public string $offerDate = '';

    public string $offerTitle = '';

    public string $validUntil = '';

    public string $currency = 'USD';

    public int $vatRate = 20;

    public string $description = '';

    public array $sections = [];

    public ?string $logoUrl = null;

    public string $preparedBy = '';

    public bool $isPdfDownloadable = true;

    public bool $isAttachmentsDownloadable = true;

    public bool $blockAfterExpiry = true;

    public array $availableIntroductionFiles = [];
    public array $selectedIntroductionFiles = [];



    public function mount($offer): void
    {
        if ($offer instanceof Offer) {
            $this->offer = $offer->load(['customer', 'sections.items']);
        } else {
            $offerId = $offer;
            if (is_array($offer)) {
                $offerId = $offer['id'] ?? null;
            } elseif (is_string($offer) && str_starts_with($offer, '{')) {
                $decoded = json_decode($offer, true);
                $offerId = $decoded['id'] ?? $offer;
            }
            $this->offer = Offer::with(['customer', 'sections.items'])->findOrFail($offerId);
        }

        $this->settings = PanelSetting::where('is_active', true)->first() ?? new PanelSetting;

        if ($this->settings->pdf_logo_path) {
            try {
                $this->logoUrl = app(MinioService::class)->getFileUrl($this->settings->pdf_logo_path);
            } catch (\Exception $e) {
                // Fail silently
            }
        }

        $this->offerNumber = $this->offer->number ?? 'TKL-0001';
        $this->offerDate = $this->offer->created_at->format('d.m.Y');
        $this->offerTitle = $this->offer->title ?? 'Teklif';
        $this->validUntil = $this->offer->valid_until?->format('d.m.Y') ?? 'Belirtilmemiş';
        $this->currency = $this->offer->currency ?? 'USD';
        $this->description = $offer->description ?? '';
        $this->vatRate = $offer->vat_rate ?? 20;
        $this->preparedBy = auth()->user()?->name ?? 'Belirtilmemiş';

        $this->sections = $this->offer->sections->map(function ($section) {
            $subtotal = $section->items->sum(fn($item) => ($item->quantity ?? 1) * ($item->price ?? 0));
            $vatAmount = $subtotal * ($this->vatRate / 100);
            $totalWithVat = $subtotal + $vatAmount;

            return [
                'id' => $section->id,
                'title' => $section->title,
                'description' => $section->description,
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'total_with_vat' => $totalWithVat,
                'items' => $section->items->map(fn($item) => [
                    'name' => $item->service_name ?? $item->name ?? 'Hizmet',
                    'description' => $item->description ?? '',
                    'quantity' => $item->quantity ?? 1,
                    'duration' => $item->duration ? $item->duration . ' Yıl' : '-',
                    'price' => $item->price ?? 0,
                    'total' => ($item->quantity ?? 1) * ($item->price ?? 0),
                ])->toArray(),
            ];
        })->toArray();

        $this->isPdfDownloadable = $this->offer->is_pdf_downloadable ?? true;
        $this->isAttachmentsDownloadable = $this->offer->is_attachments_downloadable ?? true;
        $this->isAttachmentsDownloadable = $this->offer->is_attachments_downloadable ?? true;
        $this->blockAfterExpiry = !($this->offer->is_downloadable_after_expiry ?? false);

        $this->availableIntroductionFiles = $this->settings->introduction_files ?? [];
        $this->selectedIntroductionFiles = $this->offer->selected_introduction_files ?? [];
    }

    public function saveSettings()
    {
        $this->offer->update([
            'is_pdf_downloadable' => $this->isPdfDownloadable,
            'is_attachments_downloadable' => $this->isAttachmentsDownloadable,
            'is_attachments_downloadable' => $this->isAttachmentsDownloadable,
            'is_downloadable_after_expiry' => !$this->blockAfterExpiry,
            'selected_introduction_files' => $this->selectedIntroductionFiles,
        ]);

        session()->flash('success', 'Ayarlar kaydedildi.');
    }

    public function downloadPdf()
    {
        $action = new \App\Actions\Offers\GenerateOfferPdfAction;
        $pdfPath = $action->execute($this->offer);

        $fileName = 'Teklif-' . $this->offerNumber . '.pdf';

        return response()->download($pdfPath, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Main Container with Flex Layout --}}
        <div class="flex gap-6">
            {{-- Sol Kolon - Teklif İçeriği (Geniş) --}}
            <div class="w-9/12 min-w-0">
                <div class="theme-card overflow-hidden"
                    style="font-family: '{{ $settings->pdf_font_family ?? 'Segoe UI' }}', sans-serif; background-color: #f8fafc;">

                    {{-- İçerik (Sayfa Yapıları) --}}
                    <div class="p-8 space-y-12">
                        {{-- YÖNETİCİ ÖZETİ SAYFASI --}}
                        {{-- YÖNETİCİ ÖZETİ SAYFASI --}}
                        @include('livewire.customers.offers.partials.pdf-preview._executive-summary')

                        {{-- Detay Bölümleri --}}
                        {{-- Detay Bölümleri --}}
                        @include('livewire.customers.offers.partials.pdf-preview._items-detail')
                    </div>
                </div>
            </div>

            {{-- Sağ Kolon - Butonlar (Dar) --}}
            <div class="w-3/12 flex-shrink-0">
                <div class="theme-card p-4 sticky top-6">
                    <div class="space-y-3">
                        {{-- Üst Butonlar: PDF İndir & Print --}}
                        <div class="flex gap-2">
                            <button wire:click="downloadPdf" wire:loading.attr="disabled"
                                class="flex-1 theme-btn-save flex items-center justify-center gap-2">
                                <span wire:loading wire:target="downloadPdf"
                                    class="loading loading-spinner loading-xs"></span>
                                <svg wire:loading.remove wire:target="downloadPdf" class="w-3 h-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span wire:loading.remove wire:target="downloadPdf">İndir (PDF)</span>
                            </button>

                            <button onclick="window.print()"
                                class="flex-1 theme-btn-edit flex items-center justify-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print
                            </button>
                        </div>

                        {{-- İndirme Sayfası (Yeni Buton) --}}
                        <a href="{{ route('offer.download', $offer->tracking_token ?? '') }}" target="_blank"
                            class="w-full theme-btn-delete flex items-center justify-center gap-2 no-underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span>İndirme Sayfası</span>
                        </a>

                        {{-- Ayarlar Bölümü --}}
                        <div class="pt-6 mt-6 border-t border-gray-100">
                            <h3 class="text-xs font-bold text-gray-900 mb-4">İndirme Sayfası Ayarları</h3>

                            <div class="space-y-3 mb-6">
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <input type="checkbox" wire:model="isPdfDownloadable"
                                        class="checkbox checkbox-xs checkbox-primary mt-0.5 rounded-sm">
                                    <span
                                        class="text-[11px] font-medium text-gray-600 group-hover:text-gray-900 transition-colors">
                                        PDF teklif (yandaki) indirilebilir.
                                    </span>
                                </label>

                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <input type="checkbox" wire:model="isAttachmentsDownloadable"
                                        class="checkbox checkbox-xs checkbox-primary mt-0.5 rounded-sm">
                                    <span
                                        class="text-[11px] font-medium text-gray-600 group-hover:text-gray-900 transition-colors">
                                        Teklif ekleri (varsa) indirilebilir.
                                    </span>
                                </label>

                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <input type="checkbox" wire:model="blockAfterExpiry"
                                        class="checkbox checkbox-xs checkbox-primary mt-0.5 rounded-sm">
                                    <span
                                        class="text-[11px] font-medium text-gray-600 group-hover:text-gray-900 transition-colors">
                                        Geçerlilik tarihinden sonra indirilemez.
                                    </span>
                                </label>
                            </div>

                            {{-- Tanıtım Dosyaları --}}
                            @if(count($availableIntroductionFiles) > 0)
                                <div class="mb-6">
                                    <h3 class="text-xs font-bold text-gray-900 mb-3">Tanıtım Dosyaları</h3>
                                    <div class="space-y-2">
                                        @foreach($availableIntroductionFiles as $index => $file)
                                            <label
                                                class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg border border-transparent hover:bg-gray-50 hover:border-gray-100 transition-all">
                                                <input type="checkbox" value="{{ $index }}"
                                                    wire:model="selectedIntroductionFiles"
                                                    class="checkbox checkbox-xs checkbox-primary rounded-sm">
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-[11px] font-medium text-gray-600 group-hover:text-gray-900 truncate"
                                                        title="{{ $file['name'] }}">
                                                        {{ $file['name'] }}
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <button wire:click="saveSettings"
                                class="w-full theme-btn-save flex items-center justify-center gap-2 py-2 text-xs relative">
                                <span wire:loading.remove wire:target="saveSettings">KAYDET</span>
                                <span wire:loading wire:target="saveSettings"
                                    class="loading loading-spinner loading-xs"></span>
                            </button>

                            <div class="h-6 mt-2 text-center">
                                @if (session()->has('success'))
                                    <div class="text-[10px] font-bold text-emerald-600 animate-pulse transition-all"
                                        x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                                        {{ session('success') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Print Style --}}
        {{-- Print Style --}}
        @include('livewire.customers.offers.partials.pdf-preview._styles')
    </div>
</div>