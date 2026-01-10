{{--
@component: _title_description.blade.php
@section: Teklif Başlığı ve Açıklaması
@description: Teklifin başlık ve genel açıklama metinlerinin girildiği kart bileşeni.
@params: $isViewMode (bool), $title (string), $description (string)
--}}
{{-- Teklif Başlığı ve Açıklaması Card --}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-skin-heading">Teklif Başlığı ve
        Açıklaması</h2>

    <div class="mb-4">
        <label class="block text-xs font-medium mb-1 opacity-60">Teklif Başlığı *</label>
        @if($isViewMode)
            <div class="text-sm font-medium">{{ $title }}</div>
        @else
            <input type="text" wire:model="title" placeholder="Örn: Web Sitesi Bakım Teklifi" class="input w-full bg-white">
            @error('title') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
        @endif
    </div>

    <div>
        <label class="block text-xs font-medium mb-1 opacity-60">Teklif Açıklaması</label>
        @if($isViewMode)
            <div class="text-sm font-medium whitespace-pre-wrap">
                {{ $description ?: '-' }}
            </div>
        @else
            <textarea wire:model="description" class="textarea w-full bg-white" rows="4"
                placeholder="Teklif hakkında detaylı açıklama yazabilirsiniz..."></textarea>
        @endif
    </div>
</div>