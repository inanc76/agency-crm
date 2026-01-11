{{--
🚀 MODAL: ITEM FORM (REUSABLE)
---------------------------------------------------------------------------------------
SORUMLULUK: ReferenceItem modeli için 'Create' ve 'Edit' işlemlerini yönetir.
MANTIKSAL AKIŞ: $itemId durumuna göre Update/Create ayrımı yapar.
RENK YÖNETİMİ: Metadata içindeki 'color' değerini dinamik olarak günceller.
VALIDATION: Uniqueness kontrolü Trait üzerinden, görsel feedback MaryUI üzerindendir.
---------------------------------------------------------------------------------------
--}}
<x-mary-modal wire:model="showItemModal" title="{{ $itemId ? 'Öğeyi Düzenle' : 'Yeni Öğe' }}" class="backdrop-blur"
    box-class="!max-w-2xl">
    <div class="grid gap-3">
        <div class="grid grid-cols-2 gap-4">
            <x-mary-input label="Anahtar (Key)" wire:model="key" hint="Sistem kodu" />
            <x-mary-input label="Görünen İsim" wire:model="display_label" hint="Arayüz ismi" />
        </div>

        <x-mary-input label="Açıklama" wire:model="description" placeholder="Öğe hakkında açıklama..."
            hint="Opsiyonel ek bilgi" />

        {{-- Color Picker --}}
        <div>
            <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase mb-2">Renk Şeması</label>
            <div class="p-3 border border-[var(--card-border)] rounded-lg bg-[var(--color-background)]">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-[10px] font-medium text-[var(--color-text-muted)] uppercase tracking-tight">Ön
                        İzleme:</span>
                    <span
                        class="px-2 py-0.5 rounded textxs font-medium border border-transparent {{ $this->getTailwindColor($selectedColor) }} ring-1 ring-black/5 shadow-sm">
                        {{ $display_label ?: 'Örnek Etiket' }}
                    </span>
                </div>

                <div class="grid grid-cols-5 gap-y-2 gap-x-2">
                    @foreach($availableColors as $colorScheme)
                        <button type="button" wire:click="$set('selectedColor', '{{ $colorScheme['id'] }}')"
                            class="flex flex-col items-center justify-center p-1 rounded-md border transition-all duration-200 group {{ $selectedColor === $colorScheme['id'] ? 'border-[var(--color-active-border)] bg-white ring-1 ring-[var(--color-active-bg)] shadow-sm' : 'border-transparent hover:bg-[var(--dropdown-hover-bg)]' }}">
                            <span
                                class="px-1.5 py-0.5 rounded text-[9px] font-medium {{ $this->getTailwindColor($colorScheme['id']) }} mb-0.5 shadow-sm ring-1 ring-black/5 min-w-[28px] text-center">Abc</span>
                            <span
                                class="text-[9px] tracking-tighter {{ $selectedColor === $colorScheme['id'] ? 'text-[var(--color-active-text)] font-bold' : 'text-[var(--color-text-muted)]' }}">{{ $colorScheme['name'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <x-mary-toggle label="Varsayılan Öğe" wire:model="is_default" hint="Kategorinin varsayılan seçeneği olsun"
            class="toggle-sm toggle-info" />
    </div>

    <x-slot:actions>
        <button type="button" class="theme-btn-cancel" wire:click="$set('showItemModal', false)">
            İptal
        </button>
        <button type="button" class="theme-btn-save" wire:click="saveItem" wire:loading.attr="disabled">
            <span wire:loading wire:target="saveItem" class="loading loading-spinner loading-xs mr-1"></span>
            <x-mary-icon name="o-check" class="w-4 h-4" />
            {{ $itemId ? 'Güncelle' : 'Oluştur' }}
        </button>
    </x-slot:actions>
</x-mary-modal>