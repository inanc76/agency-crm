{{--
    📋 PARTIAL: Items Detail Loop (Teklif Kalemleri)
    ---------------------------------------------------------------------
    Teklifin detaylı kalemlerini listeler.
    Her biri ayrı bir "Sayfa" olarak tasarlanmış olan $sections dizisi üzerinde döner.

    @architect-note [Calculation Logic]:
    - Bu partial içinde herhangi bir matematiksel hesaplama YAPILMAZ.
    - Tüm 'subtotal', 'vat_amount' ve 'total' değerleri `pdf-preview.blade.php`
      içindeki `mount()` metodunda hesaplanıp `$sections` dizisine enjekte edilmiştir.
    - Bu prensip, görünüm (View) ile mantığın (Logic) ayrılması için kritiktir.

    SCOPE BAĞIMLILIKLARI:
    - $sections (Array): Her biri 'items' array'i içeren bölüm nesneleri.
    - $settings (Setting): Stil ayarları.
    ---------------------------------------------------------------------
--}}
@foreach($sections as $index => $section)
    <div
        class="bg-white rounded-[24px] shadow-2xl overflow-hidden relative border border-gray-100 p-12 transition-all hover:shadow-indigo-100">
        <div
            class="absolute top-0 right-0 px-6 py-2 bg-gray-100 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 rounded-bl-xl">
            SAYFA {{ $index + 2 }}</div>

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
                                <td class="py-5 px-6 text-gray-500 text-xs leading-relaxed italic">{{ $item['description'] }}
                                </td>
                                <td class="py-5 px-6 text-center text-gray-700 font-bold">{{ $item['quantity'] }}</td>
                                <td class="py-5 px-6 text-center text-gray-700 font-bold uppercase text-[10px]">
                                    {{ $item['duration'] }}</td>
                                <td class="py-5 px-6 text-right text-gray-600 font-bold whitespace-nowrap">
                                    {{ number_format($item['price'], 0, ',', '.') }} {{ $currency }}</td>
                                <td class="py-5 px-6 text-right font-black text-gray-900 whitespace-nowrap bg-gray-50/50">
                                    {{ number_format($item['total'], 0, ',', '.') }} {{ $currency }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-10 items-start mt-8">
            <div class="col-span-7">
            </div>
            <div class="col-span-5">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative">
                    <h3
                        class="font-black text-[10px] uppercase tracking-[0.2em] text-gray-400 mb-6 pb-3 border-b border-gray-100">
                        Teklif Özeti</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-600 font-semibold">
                            <span class="text-xs uppercase tracking-wide">Ara Toplam:</span>
                            <span class="text-sm font-bold">{{ number_format($section['subtotal'], 0, ',', '.') }}
                                {{ $currency }}</span>
                        </div>

                        <div class="flex justify-between text-gray-600 font-semibold">
                            <span class="text-xs uppercase tracking-wide">KDV (%{{ (int) $vatRate }}):</span>
                            <span class="text-sm font-bold">{{ number_format($section['vat_amount'], 0, ',', '.') }}
                                {{ $currency }}</span>
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
            <div
                class="mt-16 pt-8 border-t border-gray-50 text-[10px] text-gray-300 text-center font-black uppercase tracking-[0.3em]">
                {{ $settings->pdf_footer_text }}
            </div>
        @endif
    </div>

    {{-- Sayfa Sonu Ayırıcı (Sadece aralara ekle) --}}
    @if($index < count($sections) - 1)
        <div class="py-12 flex items-center justify-center gap-4">
            <div class="h-px flex-1 bg-gray-200"></div>
            <span
                class="bg-gray-200 text-gray-500 px-4 py-1 rounded-full text-[9px] font-black uppercase tracking-[0.3em]">SAYFA
                SONU</span>
            <div class="h-px flex-1 bg-gray-200"></div>
        </div>
    @endif
@endforeach