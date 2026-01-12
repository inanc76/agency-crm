<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Offer;
use App\Models\PanelSetting;
use App\Services\MinioService;

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

        $this->settings = PanelSetting::where('is_active', true)->first() ?? new PanelSetting();

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
    }

    public function downloadPdf()
    {
        $action = new \App\Actions\Offers\GenerateOfferPdfAction();
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
            <div class="flex-1 min-w-0">
                <div class="theme-card overflow-hidden" 
                     style="font-family: '{{ $settings->pdf_font_family ?? 'Segoe UI' }}', sans-serif; background-color: #f8fafc;">
                    
                    {{-- İçerik (Sayfa Yapıları) --}}
                    <div class="p-8 space-y-12">
                        {{-- YÖNETİCİ ÖZETİ SAYFASI --}}
                        @if(count($sections) > 1)
                            <div class="bg-white rounded-[24px] shadow-2xl overflow-hidden relative border border-gray-100 p-12 transition-all hover:shadow-indigo-100">
                                <div class="absolute top-0 right-0 px-6 py-2 bg-gray-100 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 rounded-bl-xl">YÖNETİCİ ÖZETİ</div>
                                
                                {{-- Header --}}
                                <div class="flex justify-between items-center mb-10 p-6 rounded-xl" 
                                     style="background-color: {{ $settings->pdf_header_bg_color ?? '#4f46e5' }};">
                                    <div>
                                        @if($logoUrl)
                                            <img src="{{ $logoUrl }}" class="object-contain" style="height: {{ $settings->pdf_logo_height ?? 40 }}px" alt="Logo">
                                        @else
                                            <span class="text-2xl font-black tracking-tighter" style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">
                                                {{ config('app.name') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex gap-6 items-center">
                                        <div class="text-left border-r pr-6" style="border-color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}40;">
                                            <p class="text-[9px] font-extrabold uppercase tracking-widest mb-1" style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}; opacity: 0.8;">Teklif No</p>
                                            <p class="text-sm font-black leading-none" style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">{{ $offerNumber }}</p>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-[9px] font-extrabold uppercase tracking-widest mb-1" style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}; opacity: 0.8;">Tarih</p>
                                            <p class="text-sm font-black leading-none" style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">{{ $offerDate }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Teklif Bilgileri Card --}}
                                <div class="mb-10 p-8 bg-gray-50/50 rounded-2xl border border-gray-100 shadow-sm relative group overflow-hidden">
                                    <div class="absolute top-0 left-0 w-1 h-full" style="background-color: {{ $settings->pdf_primary_color ?? '#4F46E5' }}"></div>
                                    <h2 class="text-[10px] uppercase tracking-[0.2em] font-black mb-6 text-gray-400">Teklif Bilgileri</h2>
                                    <div class="grid grid-cols-2 gap-x-12 gap-y-6">
                                        <div>
                                            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest mb-1">Müşteri Adı</p>
                                            <p class="text-sm font-bold text-gray-800">{{ $offer->customer?->name ?? 'Belirtilmemiş' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest mb-1">Teklifi Hazırlayan</p>
                                            <p class="text-sm font-bold text-gray-800">{{ $preparedBy }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest mb-1">Hazırlanma Tarihi</p>
                                            <p class="text-sm font-bold text-gray-800">{{ $offerDate }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest mb-1">Geçerlilik Tarihi</p>
                                            <p class="text-sm font-bold text-gray-800">{{ $validUntil }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-8">
                                    <h2 class="text-xl font-black mb-6 pb-2 border-b-4 inline-block" 
                                        style="color: {{ $settings->pdf_primary_color ?? '#111827' }}; border-color: {{ $settings->pdf_primary_color ?? '#4F46E5' }}">
                                        Yönetici Özeti
                                    </h2>

                                    {{-- Basit Tablo - Yönetici Özeti --}}
                                    <div class="mb-10 overflow-hidden rounded-lg border border-gray-200">
                                        <table class="w-full">
                                            <thead>
                                                <tr style="background-color: {{ $settings->pdf_header_bg_color ?? '#4f46e5' }};">
                                                    <th class="py-3 px-4 text-left text-xs font-bold uppercase" style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">Bölüm Başlığı</th>
                                                    <th class="py-3 px-4 text-right text-xs font-bold uppercase" style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">Tutar</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 bg-white">
                                                @foreach($sections as $section)
                                                    <tr>
                                                        <td class="py-4 px-4 text-sm font-medium text-gray-700">{{ $section['title'] }}</td>
                                                        <td class="py-4 px-4 text-right text-sm font-bold text-gray-900">{{ number_format($section['total_with_vat'], 0, ',', '.') }} {{ $currency }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="grid grid-cols-12 gap-10 mt-12 items-start">
                                    <div class="col-span-7">
                                        @if($description)
                                            <div class="p-6 bg-white rounded-xl border-l-4 border-gray-200 shadow-sm italic">
                                                <h3 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Teklif Açıklaması</h3>
                                                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $description }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-span-5">
                                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative">
                                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6 pb-3 border-b border-gray-100">Teklif Özeti</h3>
                                            
                                            @if(($offer->discounted_amount ?? 0) > 0)
                                                <div class="flex justify-between text-rose-500 font-bold mb-4">
                                                    <span class="text-xs uppercase tracking-wide">İndirim (@if($offer->discount_percentage > 0) %{{ (int)$offer->discount_percentage }} @else Tutar @endif):</span>
                                                    @php $discountWithVat = $offer->discounted_amount * (1 + ($vatRate / 100)); @endphp
                                                    <span class="text-sm font-black">-{{ number_format($discountWithVat, 0, ',', '.') }} {{ $currency }}</span>
                                                </div>
                                                <div class="pt-4 border-t border-gray-100 mt-4">
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">İndirimli Toplam</span>
                                                        <span class="text-2xl font-black italic" style="color: {{ $settings->pdf_total_color ?? '#4F46E5' }}">
                                                            {{ number_format($offer->total_amount, 0, ',', '.') }} {{ $currency }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex justify-between items-center">
                                                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Genel Toplam</span>
                                                    <span class="text-2xl font-black italic" style="color: {{ $settings->pdf_total_color ?? '#4F46E5' }}">
                                                        {{ number_format($offer->total_amount, 0, ',', '.') }} {{ $currency }}
                                                    </span>
                                                </div>
                                            @endif
                                            <p class="text-[9px] text-gray-400 text-right italic mt-4">Fiyatlara KDV dahildir.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- HTML Önizleme için Sayfa Sonu Ayırıcı --}}
                            <div class="py-12 flex items-center justify-center gap-4">
                                <div class="h-px flex-1 bg-gray-200"></div>
                                <span class="bg-gray-200 text-gray-500 px-4 py-1 rounded-full text-[9px] font-black uppercase tracking-[0.3em]">SAYFA SONU</span>
                                <div class="h-px flex-1 bg-gray-200"></div>
                            </div>
                        @endif
                        
                        {{-- Detay Bölümleri --}}
                        @foreach($sections as $index => $section)
                            <div class="bg-white rounded-[24px] shadow-2xl overflow-hidden relative border border-gray-100 p-12 transition-all hover:shadow-indigo-100">
                                <div class="absolute top-0 right-0 px-6 py-2 bg-gray-100 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 rounded-bl-xl">SAYFA {{ count($sections) > 1 ? ($index + 2) : 1 }}</div>
                                
                                {{-- Header --}}
                                <div class="flex justify-between items-center mb-10 p-6 rounded-xl" 
                                     style="background-color: {{ $settings->pdf_header_bg_color ?? '#4f46e5' }};">
                                    <div>
                                        @if($logoUrl)
                                            <img src="{{ $logoUrl }}" class="object-contain" style="height: {{ $settings->pdf_logo_height ?? 40 }}px" alt="Logo">
                                        @else
                                            <span class="text-2xl font-black tracking-tighter" style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">
                                                {{ config('app.name') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex gap-6 items-center">
                                        <div class="text-left border-r pr-6" style="border-color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}40;">
                                            <p class="text-[9px] font-extrabold uppercase tracking-widest mb-1" style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}; opacity: 0.8;">Teklif No</p>
                                            <p class="text-sm font-black leading-none" style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">{{ $offerNumber }}</p>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-[9px] font-extrabold uppercase tracking-widest mb-1" style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}; opacity: 0.8;">Tarih</p>
                                            <p class="text-sm font-black leading-none" style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">{{ $offerDate }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-8">
                                    <h2 class="text-xl font-black mb-6 pb-2 border-b-4 inline-block italic" 
                                        style="color: {{ $settings->pdf_primary_color ?? '#111827' }}; border-color: {{ $settings->pdf_primary_color ?? '#4F46E5' }}">
                                        {{ $section['title'] }}
                                    </h2>
                                    
                                    @if(!empty($section['description']))
                                        <div class="mb-8 p-4 bg-gray-50 rounded-xl border-l-4 border-gray-200 italic text-gray-600 text-sm">
                                            {{ $section['description'] }}
                                        </div>
                                    @endif

                                    <div class="overflow-hidden rounded-2xl border border-gray-100 mb-10 shadow-sm bg-white">
                                        <table class="w-full text-sm">
                                            <thead style="background-color: {{ $settings->pdf_table_header_bg_color ?? '#f8fafc' }}">
                                                <tr class="divide-x divide-gray-200/30">
                                                    <th class="text-left py-5 px-6 font-black text-[9px] uppercase tracking-[0.2em]" 
                                                        style="color: {{ $settings->pdf_table_header_text_color ?? '#374151' }}">Hizmet</th>
                                                    <th class="text-left py-5 px-6 font-black text-[9px] uppercase tracking-[0.2em]"
                                                        style="color: {{ $settings->pdf_table_header_text_color ?? '#374151' }}">Açıklama</th>
                                                    <th class="text-center py-5 px-6 font-black text-[9px] uppercase tracking-[0.2em]"
                                                        style="color: {{ $settings->pdf_table_header_text_color ?? '#374151' }}">Adet</th>
                                                    <th class="text-center py-5 px-6 font-black text-[9px] uppercase tracking-[0.2em]"
                                                        style="color: {{ $settings->pdf_table_header_text_color ?? '#374151' }}">Süre</th>
                                                    <th class="text-right py-5 px-6 font-black text-[9px] uppercase tracking-[0.2em]"
                                                        style="color: {{ $settings->pdf_table_header_text_color ?? '#374151' }}">Birim Fiyat</th>
                                                    <th class="text-right py-5 px-6 font-black text-[9px] uppercase tracking-[0.2em]"
                                                        style="color: {{ $settings->pdf_table_header_text_color ?? '#374151' }}">Toplam</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($section['items'] as $item)
                                                <tr class="divide-x divide-gray-50 hover:bg-gray-50/50 transition-all bg-white">
                                                    <td class="py-5 px-6 font-bold text-gray-900">{{ $item['name'] }}</td>
                                                    <td class="py-5 px-6 text-gray-500 text-xs leading-relaxed italic">{{ $item['description'] }}</td>
                                                    <td class="py-5 px-6 text-center text-gray-700 font-bold">{{ $item['quantity'] }}</td>
                                                    <td class="py-5 px-6 text-center text-gray-700 font-bold uppercase text-[10px]">{{ $item['duration'] }}</td>
                                                    <td class="py-5 px-6 text-right text-gray-600 font-bold whitespace-nowrap">{{ number_format($item['price'], 0, ',', '.') }} {{ $currency }}</td>
                                                    <td class="py-5 px-6 text-right font-black text-gray-900 whitespace-nowrap bg-gray-50/50">{{ number_format($item['total'], 0, ',', '.') }} {{ $currency }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="grid grid-cols-12 gap-10 items-start mt-8">
                                    <div class="col-span-7">
                                        @if(count($sections) == 1 && $description)
                                            <div class="p-6 bg-white rounded-xl border-l-4 border-gray-200 shadow-sm italic text-gray-500 text-sm">
                                                <h2 class="text-[9px] uppercase tracking-widest font-black mb-3 text-gray-400">Teklif Açıklaması</h2>
                                                <p class="leading-relaxed whitespace-pre-line">{{ $description }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-span-5">
                                        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative">
                                            <h3 class="font-black text-[10px] uppercase tracking-[0.2em] text-gray-400 mb-6 pb-3 border-b border-gray-100">Teklif Özeti</h3>
                                            <div class="space-y-3">
                                                <div class="flex justify-between text-gray-600 font-semibold">
                                                    <span class="text-xs uppercase tracking-wide">Ara Toplam:</span>
                                                    <span class="text-sm font-bold">{{ number_format($section['subtotal'], 0, ',', '.') }} {{ $currency }}</span>
                                                </div>
                                                
                                                <div class="flex justify-between text-gray-600 font-semibold">
                                                    <span class="text-xs uppercase tracking-wide">KDV (%{{ (int)$vatRate }}):</span>
                                                    <span class="text-sm font-bold">{{ number_format($section['vat_amount'], 0, ',', '.') }} {{ $currency }}</span>
                                                </div>
                                                
                                                <div class="flex justify-between pt-4 mt-4 border-t border-gray-100 items-center">
                                                    <span class="font-black text-gray-600 uppercase text-[10px] tracking-widest">Genel Toplam</span>
                                                    <span class="text-2xl font-black" style="color: {{ $settings->pdf_total_color ?? '#4F46E5' }}">
                                                        {{ number_format($section['total_with_vat'], 0, ',', '.') }} {{ $currency }}
                                                    </span>
                                                </div>
                                                <p class="text-[9px] text-gray-400 text-right italic mt-2">Fiyatlara KDV dahildir.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Footer Note --}}
                                @if(!empty($settings->pdf_footer_text))
                                <div class="mt-16 pt-8 border-t border-gray-50 text-[10px] text-gray-300 text-center font-black uppercase tracking-[0.3em]">
                                    {{ $settings->pdf_footer_text }}
                                </div>
                                @endif
                            </div>
                            
                            {{-- Sayfa Sonu Ayırıcı (Sadece aralara ekle) --}}
                            @if($index < count($sections) - 1)
                                <div class="py-12 flex items-center justify-center gap-4">
                                    <div class="h-px flex-1 bg-gray-200"></div>
                                    <span class="bg-gray-200 text-gray-500 px-4 py-1 rounded-full text-[9px] font-black uppercase tracking-[0.3em]">SAYFA SONU</span>
                                    <div class="h-px flex-1 bg-gray-200"></div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sağ Kolon - Butonlar (Dar) --}}
            <div class="w-40 flex-shrink-0">
                <div class="theme-card p-4 sticky top-6">
                    <div class="space-y-3">
                        {{-- PDF İndir --}}
                        <button wire:click="downloadPdf" wire:loading.attr="disabled"
                            class="w-full flex items-center justify-center gap-2 px-3 py-3 text-white font-medium rounded-lg shadow-md transition-all hover:shadow-lg text-sm cursor-pointer"
                            style="background: linear-gradient(135deg, #7C3AED, #8B5CF6);">
                            <span wire:loading wire:target="downloadPdf"
                                class="loading loading-spinner loading-xs"></span>
                            <svg wire:loading.remove wire:target="downloadPdf" class="w-4 h-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span wire:loading.remove wire:target="downloadPdf">PDF İndir</span>
                        </button>

                        {{-- Print --}}
                        <button onclick="window.print()"
                            class="w-full flex items-center justify-center gap-2 px-3 py-3 text-gray-700 font-medium rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition-all text-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Print Style --}}
        <style>
            @media print {
                .w-40 {
                    display: none !important;
                }

                .flex-1 {
                    width: 100% !important;
                }
            }
        </style>
    </div>
</div>