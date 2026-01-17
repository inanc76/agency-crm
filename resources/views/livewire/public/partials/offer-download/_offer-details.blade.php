{{--
🏷️ PARTIAL: Offer Header Details
---------------------------------------------------------------------
Sayfanın en üstünde yer alan statik bilgi kartı.
Teklif başlığı, numarası, oluşturulma tarihi ve firma logosunu içerir.

SCOPE BAĞIMLILIKLARI:
- $offer->title, $offer->number, $offer->created_at
- $logoUrl (Minio/S3 URL string)
---------------------------------------------------------------------
--}}
<div class="text-center mb-10">
    <h1 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight">
        {{ $offer->title ?? 'TEKLİF' }}
    </h1>
    <div class="flex items-center justify-center gap-3 text-sm font-medium text-gray-500">
        <span>Teklif No: {{ $offer->number }}</span>
        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
        <span>Tarih: {{ $offer->created_at->format('d.m.Y') }}</span>
    </div>

    @if($logoUrl)
        <div class="mt-6 flex justify-center">
            <img src="{{ $logoUrl }}" alt="Logo" class="h-12 object-contain transition-all duration-300">
        </div>
    @endif
</div>