{{--
📊 PARTIAL: Executive Summary (Yönetici Özeti)
---------------------------------------------------------------------
Teklifin kapak sayfası ve genel özetini içerir.
Genel toplam, KDV ve indirim hesaplamaları burada son kullanıcıya sunulur.

SCOPE BAĞIMLILIKLARI:
- $offer (Offer Model) : Teklifin kendisi.
- $sections (Array) : Bölümlerin toplamlarını içeren hesaplanmış dizi.
- $settings (Setting) : Renk ve logo ayarları.
- $currency (String) : Para birimi (örn: USD, TRY).
- $offerNumber, $offerDate, $validUntil: Biçimlendirilmiş tarih ve no.
---------------------------------------------------------------------
--}}
<div
    class="bg-white rounded-[24px] shadow-2xl overflow-hidden relative border border-gray-100 p-12 transition-all hover:shadow-indigo-100">
    <div
        class="absolute top-0 right-0 px-6 py-2 bg-gray-100 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 rounded-bl-xl">
        YÖNETİCİ ÖZETİ</div>

    {{-- Header --}}
    <div class="flex justify-between items-center mb-10 p-6 rounded-xl"
        style="background-color: {{ $settings->pdf_header_bg_color ?? '#4f46e5' }};">
        <div>
            @if($logoUrl)
                <img src="{{ $logoUrl }}" class="object-contain" style="height: {{ $settings->pdf_logo_height ?? 40 }}px"
                    alt="Logo">
            @else
                <span class="text-2xl font-black tracking-tighter"
                    style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">
                    {{ config('app.name') }}
                </span>
            @endif
        </div>
        <div class="flex gap-6 items-center">
            <div class="text-left border-r pr-6"
                style="border-color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}40;">
                <p class="text-[9px] font-extrabold uppercase tracking-widest mb-1"
                    style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}; opacity: 0.8;">Teklif No</p>
                <p class="text-sm font-black leading-none"
                    style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">{{ $offerNumber }}</p>
            </div>
            <div class="text-left">
                <p class="text-[9px] font-extrabold uppercase tracking-widest mb-1"
                    style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}; opacity: 0.8;">Tarih</p>
                <p class="text-sm font-black leading-none"
                    style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">{{ $offerDate }}</p>
            </div>
        </div>
    </div>

    {{-- Teklif Bilgileri Card --}}
    <div class="mb-10 p-8 bg-gray-50/50 rounded-2xl border border-gray-100 shadow-sm relative group overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full"
            style="background-color: {{ $settings->pdf_primary_color ?? '#4F46E5' }}"></div>
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
                        <th class="py-3 px-4 text-left text-xs font-bold uppercase"
                            style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">Bölüm Başlığı</th>
                        <th class="py-3 px-4 text-right text-xs font-bold uppercase"
                            style="color: {{ $settings->pdf_header_text_color ?? '#ffffff' }}">Tutar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($sections as $section)
                        <tr>
                            <td class="py-4 px-4 text-sm font-medium text-gray-700">{{ $section['title'] }}</td>
                            <td class="py-4 px-4 text-right text-sm font-bold text-gray-900">
                                {{ number_format($section['total_with_vat'], 0, ',', '.') }} {{ $currency }}
                            </td>
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
                <h3
                    class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6 pb-3 border-b border-gray-100">
                    Teklif Özeti</h3>

                @if(($offer->discounted_amount ?? 0) > 0)
                    <div class="flex justify-between text-rose-500 font-bold mb-4">
                        <span class="text-xs uppercase tracking-wide">İndirim (@if($offer->discount_percentage > 0)
                        %{{ (int) $offer->discount_percentage }} @else Tutar @endif):</span>
                        @php $discountWithVat = $offer->discounted_amount * (1 + ($vatRate / 100)); @endphp
                        <span class="text-sm font-black">-{{ number_format($discountWithVat, 0, ',', '.') }}
                            {{ $currency }}</span>
                    </div>
                    <div class="pt-4 border-t border-gray-100 mt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">İndirimli
                                Toplam</span>
                            <span class="text-2xl font-black italic"
                                style="color: {{ $settings->pdf_total_color ?? '#4F46E5' }}">
                                {{ number_format($offer->total_amount, 0, ',', '.') }} {{ $currency }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Genel Toplam</span>
                        <span class="text-2xl font-black italic"
                            style="color: {{ $settings->pdf_total_color ?? '#4F46E5' }}">
                            {{ number_format($offer->total_amount, 0, ',', '.') }} {{ $currency }}
                        </span>
                    </div>
                @endif
                <p class="text-[9px] text-gray-400 text-right italic mt-4">Fiyatlara KDV dahildir.</p>
            </div>
        </div>
    </div>
</div>

<div class="py-12 flex items-center justify-center gap-4">
    <div class="h-px flex-1 bg-gray-200"></div>
    <span
        class="bg-gray-200 text-gray-500 px-4 py-1 rounded-full text-[9px] font-black uppercase tracking-[0.3em]">SAYFA
        SONU</span>
    <div class="h-px flex-1 bg-gray-200"></div>
</div>