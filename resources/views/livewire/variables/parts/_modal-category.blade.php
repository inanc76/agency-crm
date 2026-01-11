{{--
    🚀 MODAL: CATEGORY FORM (REUSABLE)
    ---------------------------------------------------------------------------------------
    SORUMLULUK: ReferenceCategory modeli için 'Create' ve 'Edit' işlemlerini yönetir.
    MANTIKSAL AKIŞ: $categoryId doluysa 'Update', boşsa 'Create' aksiyonu tetiklenir.
    VALIDATION: Hatalar direkt input componentleri üzerinde MaryUI aracılığıyla yakalanır.
    ---------------------------------------------------------------------------------------
--}}
<x-mary-modal wire:model="showCategoryModal" title="{{ $categoryId ? 'Kategoriyi Düzenle' : 'Yeni Kategori' }}"
    class="backdrop-blur" box-class="!max-w-lg">
    <div class="grid gap-4">
        <x-mary-input label="Anahtar" wire:model="categoryKey" placeholder="CATEGORY_KEY"
            hint="Sistem tarafında kullanılacak benzersiz kod" />
        <x-mary-input label="İsim" wire:model="categoryName" placeholder="Kategori İsmi" />
        <x-mary-textarea label="Açıklama" wire:model="categoryDescription" placeholder="Kategori açıklaması" rows="3" />
    </div>
    <x-slot:actions>
        <button type="button" class="theme-btn-cancel" wire:click="$set('showCategoryModal', false)">
            İptal
        </button>
        <button type="button" class="theme-btn-save" wire:click="saveCategory" wire:loading.attr="disabled">
            <span wire:loading wire:target="saveCategory" class="loading loading-spinner loading-xs mr-1"></span>
            <x-mary-icon name="o-check" class="w-4 h-4" />
            {{ $categoryId ? 'Güncelle' : 'Oluştur' }}
        </button>
    </x-slot:actions>
</x-mary-modal>