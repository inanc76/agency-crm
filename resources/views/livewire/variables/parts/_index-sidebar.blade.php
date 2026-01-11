{{--
📂 PARTIAL: CATEGORY SIDEBAR
---------------------------------------------------------------------------------------
SORUMLULUK: Referans kategorilerinin listelenmesi ve filtrelenmesi.
ETKİLEŞİM: wire:model.live="search" üzerinden anlık kategori araması yapar.
BAĞLANTI: Parent component üzerindeki $selectedCategoryKey değerini güncelleyerek
sağ taraftaki içerik akışını tetikler.
---------------------------------------------------------------------------------------
--}}
<div
    class="w-full lg:w-1/2 bg-[var(--card-bg)] border border-[var(--card-border)] rounded-lg flex flex-col h-full shadow-sm">
    <div class="p-4 border-b border-[var(--card-border)] flex justify-between items-center">
        <div class="font-bold text-lg text-[var(--color-text-heading)]">Kategoriler</div>
        <x-mary-button label="Yeni Kategori" icon="o-plus"
            class="btn-sm bg-[var(--card-bg)] border border-[var(--card-border)] text-[var(--color-text-base)] hover:bg-[var(--dropdown-hover-bg)] shadow-sm"
            wire:click="openCreateCategoryModal" />
    </div>
    <div class="px-4 py-3 border-b border-[var(--card-border)] bg-[var(--card-bg)]">
        <div class="relative">
            <x-mary-icon name="o-magnifying-glass"
                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)]" />
            <input wire:model.live="search" type="search" placeholder="Kategori ara..."
                class="w-full pl-9 pr-3 py-2 bg-[var(--card-bg)] border border-[var(--card-border)] rounded-lg text-sm focus:outline-none focus:border-[var(--color-focus)] transition-colors">
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-2">
        {{-- Categories List --}}
        @foreach($categories as $category)
            <div class="group relative flex items-center justify-between p-4 rounded-lg border transition-all duration-200 cursor-pointer hover:shadow-md {{ $selectedCategoryKey === $category->key ? 'border-[var(--color-active-border)] bg-[var(--color-active-bg)]' : 'border-[var(--card-border)] bg-[var(--card-bg)] hover:border-[var(--card-hover-border)]' }}"
                wire:click="selectCategory('{{ $category->key }}')">

                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-[var(--color-text-heading)] truncate">{{ $category->name }}</div>
                    <div class="text-xs text-[var(--color-text-muted)] font-mono mt-0.5 uppercase">{{ $category->key }}
                    </div>
                </div>

                <div class="flex items-center gap-3 pl-3">
                    <span
                        class="text-xs font-semibold text-[var(--color-text-muted)] bg-[var(--dropdown-hover-bg)] px-2 py-1 rounded">{{ $category->items->count() }}
                        öğe</span>

                    <div class="flex items-center gap-1">
                        <button wire:click.stop="editCategory('{{ $category->id }}')"
                            class="p-1.5 text-[var(--color-text-muted)] hover:bg-[var(--dropdown-hover-bg)] rounded transition-colors"
                            style="color: var(--action-link-color);">
                            <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                        </button>
                        <button wire:click.stop="deleteCategory('{{ $category->id }}')"
                            wire:confirm="Bu kategoriyi silmek istediğinize emin misiniz?"
                            class="p-1.5 text-[var(--color-text-muted)] hover:text-[var(--color-danger)] hover:bg-[var(--color-danger-muted)] rounded transition-colors">
                            <x-mary-icon name="o-trash" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        @endforeach

        @if($categories->isEmpty())
            <div class="p-8 text-center text-[var(--color-text-muted)]">
                <x-mary-icon name="o-folder" class="w-8 h-8 mx-auto mb-2 opacity-50" />
                <p class="text-sm">Kategori bulunamadı.</p>
            </div>
        @endif
    </div>
</div>