<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
            @if($isViewMode)
                Rapor Detayı
            @elseif($report?->exists)
                Düzenle: Rapor
            @else
                Yeni Rapor Ekle
            @endif
        </h1>
        <p class="text-sm opacity-60 text-skin-base">
            Rapor bilgilerini yönetin
        </p>
    </div>
    <div class="flex items-center gap-3">
        @if($isViewMode && $report?->exists)
            <button type="button" wire:click="delete" wire:confirm="Bu raporu silmek istediğinize emin misiniz?"
                class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
                Sil
            </button>
            <button type="button" wire:click="toggleEditMode"
                class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                Düzenle
            </button>
        @else
            @if($report?->exists)
                <button type="button" wire:click="toggleEditMode" class="theme-btn-cancel px-4 py-2 text-sm">
                    İptal
                </button>
            @else
                <a href="{{ route('projects.index', ['tab' => 'reports']) }}" class="theme-btn-cancel px-4 py-2 text-sm">
                    İptal
                </a>
            @endif
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                class="theme-btn-save flex items-center gap-2 px-4 py-2 text-sm">
                <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                <x-mary-icon name="o-check" class="w-4 h-4" />
                @if($report?->exists) Güncelle @else Kaydet @endif
            </button>
        @endif
    </div>
</div>