<a href="/dashboard/customers?tab=offers"
    class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-4 transition-colors">
    <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
    <span class="text-sm font-medium">Teklif Listesi</span>
</a>

{{-- Header --}}
{{--
@component: _header.blade.php
@section: Teklif Oluşturma Üst Başlık
@description: Sayfa başlığı, geri dön butonu ve ana aksiyon butonlarını (İptal, Kaydet, Sil) içerir.
@params: $isViewMode (bool), $title (string), $number (string|null), $offerId (string|null)
@events: cancel, delete, toggleEditMode, save
--}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight" class="text-skin-heading">
            @if($isViewMode)
                {{ $number ?? $title }}
            @elseif($offerId)
                Düzenle: {{ $title }}
            @else
                Yeni Teklif Oluştur
            @endif
        </h1>
        <div class="flex items-center gap-2 mt-1">
            @if($isViewMode)
                <span
                    class="text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">Teklif</span>
                <span class="text-[11px] font-mono text-slate-400">ID: {{ $offerId }}</span>
            @else
                <p class="text-sm opacity-60">
                    Müşteri için yeni bir teklif hazırlayın
                </p>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3">
        @if($isViewMode)
            {{-- PDF Butonu --}}
            @php
                $pdfOfferId = is_object($offerId) ? $offerId->id : (is_array($offerId) ? ($offerId['id'] ?? $offerId) : $offerId);
            @endphp
            <a href="/dashboard/customers/offers/{{ $pdfOfferId }}/pdf" wire:key="btn-pdf-{{ $pdfOfferId }}" target="_blank"
                class="theme-btn-save flex items-center gap-2 px-4 py-2 text-sm" title="Teklifi Gör ve Gönder">
                <x-mary-icon name="o-paper-airplane" class="w-4 h-4" />
                Gönder
            </a>

            <button type="button" wire:click="delete" wire:confirm="Bu teklifi silmek istediğinize emin misiniz?"
                wire:key="btn-delete-{{ $offerId }}" class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
                Sil
            </button>
            <button type="button" wire:click="toggleEditMode" wire:key="btn-edit-{{ $offerId }}"
                class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                Düzenle
            </button>
        @else
            <button type="button" wire:click="cancel" wire:key="btn-cancel-{{ $offerId ?: 'new' }}"
                class="theme-btn-cancel">
                İptal
            </button>
            <button type="button" wire:click="save" wire:loading.attr="disabled" wire:key="btn-save-{{ $offerId ?: 'new' }}"
                class="theme-btn-save">
                <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                <x-mary-icon name="o-check" class="w-4 h-4" />
                @if($offerId) Güncelle @else Teklif Oluştur @endif
            </button>
        @endif
    </div>
</div>