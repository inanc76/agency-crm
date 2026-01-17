{{--
⚡ PARTIAL: Action Buttons & Attachments
---------------------------------------------------------------------
Aktif ve indirilebilir durumdaki teklif arayüzü.

LOGIC BAĞIMLILIKLARI:
- PDF İndirme: `GenerateOfferPdfAction` servisini tetikler.
- Ek Dosyalar: MinioService üzerinden güvenli link (Base64/Presigned) üretir.
- Tanıtım Dosyaları: Panel ayarlarından gelen global dosyalar.

SCOPE DEĞİŞKENLERİ:
- $offer (Offer Model) : Teklif verisi ve ilişkileri (attachments).
- $settings (Setting) : PDF indirme izinleri (is_pdf_downloadable).
---------------------------------------------------------------------
--}}
{{-- ACTIVE / DOWNLOADABLE STATE --}}
<div class="flex flex-col items-center">

    {{-- Icon Circle --}}
    <div
        class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 shadow-lg shadow-emerald-100 flex items-center justify-center mb-6 text-white transform hover:scale-105 transition-transform duration-300">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
        </svg>
    </div>

    <h2 class="text-2xl font-black text-gray-900 mb-2">Teklifiniz Hazır</h2>
    <p class="text-gray-500 text-sm mb-8">
        <span class="font-bold text-gray-700">{{ $offer->number }}</span> numaralı teklifinizi PDF formatında
        indirebilirsiniz.
    </p>

    {{-- Main PDF Download Button --}}
    @if($offer->is_pdf_downloadable)
        <button wire:click="downloadPdf" wire:loading.attr="disabled"
            class="group relative w-full sm:w-64 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold py-4 px-8 rounded-2xl shadow-xl shadow-emerald-200 transition-all duration-300 transform hover:-translate-y-1 active:scale-[0.98] cursor-pointer">
            <div class="flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Teklifi İndir</span>
            </div>
            <div class="absolute inset-0 rounded-2xl ring-4 ring-white/20 group-hover:ring-white/30 transition-all"></div>
        </button>
    @else
        <div class="text-amber-600 font-medium text-sm bg-amber-50 px-4 py-2 rounded-lg">
            PDF indirme özelliği şu an aktif değil.
        </div>
    @endif

    {{-- Attachments & Info --}}
    <div class="mt-8 pt-8 border-t border-gray-100 w-full">

        {{-- Expiry Info --}}
        @if($offer->valid_until)
            <div class="flex flex-col items-center gap-2 mb-6">
                <div class="flex items-center gap-2 text-gray-500 text-xs uppercase font-bold tracking-widest">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Geçerlilik Tarihi: {{ $offer->valid_until->format('d F Y') }}
                </div>

                @if($remainingDays > 0)
                    <span
                        class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $remainingDays }} gün kaldı
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">
                        Süresi bugün doldu
                    </span>
                @endif
            </div>
        @endif

        {{-- Attachments --}}
        @if($offer->is_attachments_downloadable && $offer->attachments->count() > 0)
            <div class="text-left w-full max-w-sm mx-auto bg-white rounded-xl border border-gray-100 p-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Ek Dosyalar</h3>
                <div class="space-y-2">
                    @foreach($offer->attachments as $attachment)
                        <a href="#" wire:click.prevent="downloadAttachment('{{ $attachment->id }}')"
                            class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 group transition-colors cursor-pointer">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-medium text-gray-700 truncate group-hover:text-indigo-600 transition-colors">
                                    {{ $attachment->file_name }}
                                </p>
                                <p class="text-[10px] text-gray-400">
                                    {{ strtoupper(pathinfo($attachment->file_name, PATHINFO_EXTENSION)) }} Dosyası
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Introduction Files --}}
        @if(!empty($offer->selected_introduction_files))
            <div class="text-left w-full max-w-sm mx-auto bg-white rounded-xl border border-gray-100 p-4 mt-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Tanıtım Dosyaları</h3>
                <div class="space-y-2">
                    @foreach($offer->selected_introduction_files as $fileIndex)
                        {{-- Fetch file details from settings --}}
                        @php $file = $settings->introduction_files[$fileIndex] ?? null; @endphp
                        @if($file)
                            <a href="#" wire:click.prevent="downloadIntroFile({{ $fileIndex }})"
                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 group transition-colors cursor-pointer">
                                <div class="w-8 h-8 rounded-lg bg-pink-50 text-pink-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-medium text-gray-700 truncate group-hover:text-pink-600 transition-colors">
                                        {{ $file['name'] }}
                                    </p>
                                    <p class="text-[10px] text-gray-400">Tanıtım Dosyası</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-300 group-hover:text-pink-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>