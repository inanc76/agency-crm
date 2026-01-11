{{--
📂 PARTIAL: REFERENCE ITEMS LIST
---------------------------------------------------------------------------------------
SORUMLULUK: Seçili kategoriye ait değişken öğelerinin (ReferenceItem) listelenmesi.
SIRALAMA: moveItemUp ve moveItemDown metodları ile 'sort_order' kolonunu manipüle eder.
BOŞ DURUM: @forelse yapısı ile kategori boşsa 'Empty State' görseli sunar.
BAĞLANTI: Veriler direkt olarak $selectedCategory->items ilişkisi üzerinden akar.
---------------------------------------------------------------------------------------
--}}
<div
    class="w-full lg:w-1/2 bg-[var(--card-bg)] border border-[var(--card-border)] rounded-lg shadow-sm flex flex-col h-full overflow-hidden">
    @if($selectedCategory)
        <div
            class="p-6 border-b border-[var(--card-border)] flex justify-between items-center bg-[var(--dropdown-hover-bg)]">
            <div>
                <h2 class="text-lg font-bold text-[var(--color-text-heading)]">{{ $selectedCategory->name }}</h2>
            </div>
            <x-mary-button label="Yeni Öğe" icon="o-plus"
                class="btn-sm bg-[var(--card-bg)] border border-[var(--card-border)] text-[var(--color-text-base)] hover:bg-[var(--dropdown-hover-bg)] shadow-sm"
                wire:click="openCreateModal" />
        </div>

        <div class="p-4 flex-1 overflow-y-auto bg-[var(--color-background)]">
            <div class="space-y-2">
                @forelse($selectedCategory->items as $item)
                    <div
                        class="flex items-center justify-between p-3 bg-[var(--card-bg)] rounded-lg border border-[var(--card-border)] shadow-sm hover:shadow-md transition-shadow group relative">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if(isset($item->metadata['color']))
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-medium {{ $this->getTailwindColor($item->metadata['color']) }} border border-transparent">
                                        {{ $item->display_label }}
                                    </span>
                                @else
                                    <span class="font-medium text-[var(--color-text-base)]">{{ $item->display_label }}</span>
                                @endif
                                @if($item->is_default)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--dropdown-hover-bg)]"
                                        style="color: var(--btn-primary-bg);">Varsayılan</span>
                                @endif
                                <div class="text-xs text-[var(--color-text-muted)] font-mono uppercase">{{ $item->key }}</div>
                            </div>
                            @if($item->description)
                                <div class="text-xs text-[var(--color-text-muted)] mt-1 truncate">{{ $item->description }}</div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 pl-2">
                            {{-- Move buttons --}}
                            <div class="flex flex-col gap-0.5 mr-1">
                                <button wire:click="moveItemUp('{{ $item->id }}')"
                                    class="p-1 text-[var(--color-text-muted)] hover:text-[var(--color-text-base)] hover:bg-[var(--dropdown-hover-bg)] rounded transition-colors"
                                    title="Yukarı taşı">
                                    <x-mary-icon name="o-arrow-up" class="w-3 h-3" />
                                </button>
                                <button wire:click="moveItemDown('{{ $item->id }}')"
                                    class="p-1 text-[var(--color-text-muted)] hover:text-[var(--color-text-base)] hover:bg-[var(--dropdown-hover-bg)] rounded transition-colors"
                                    title="Aşağı taşı">
                                    <x-mary-icon name="o-arrow-down" class="w-3 h-3" />
                                </button>
                            </div>

                            <button wire:click="editItem('{{ $item->id }}')"
                                class="p-1.5 text-[var(--color-text-muted)] hover:bg-[var(--dropdown-hover-bg)] rounded transition-colors"
                                style="color: var(--action-link-color);">
                                <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                            </button>
                            <button wire:click="deleteItem('{{ $item->id }}')"
                                wire:confirm="Bu öğeyi silmek istediğinize emin misiniz?"
                                class="p-1.5 text-[var(--color-text-muted)] hover:text-[var(--color-danger)] hover:bg-[var(--color-danger-muted)] rounded transition-colors">
                                <x-mary-icon name="o-trash" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div
                            class="w-16 h-16 bg-[var(--card-bg)] rounded-full flex items-center justify-center mb-4 border border-dashed border-[var(--card-border)]">
                            <x-mary-icon name="o-inbox" class="w-8 h-8 text-[var(--color-text-muted)]" />
                        </div>
                        <p class="text-[var(--color-text-muted)] text-sm">Bu kategori boş.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <div class="h-full flex flex-col items-center justify-center bg-[var(--color-background)]">
            <div class="p-8 text-center">
                <h3 class="text-lg font-medium text-[var(--color-text-heading)] mb-2">Kategori Seçimi</h3>
                <p class="text-[var(--color-text-muted)] text-sm">İşlem yapmak için soldan bir kategori seçin.</p>
            </div>
        </div>
    @endif
</div>