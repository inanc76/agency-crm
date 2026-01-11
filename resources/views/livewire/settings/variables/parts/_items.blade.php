<div class="w-full lg:w-1/2 bg-white border border-slate-100 rounded-xl shadow-sm flex flex-col h-full overflow-hidden">
    @if($selectedCategory)
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $selectedCategory->name }}</h2>
            </div>
            <x-mary-button label="Yeni Öğe" icon="o-plus" class="btn-sm btn-primary" wire:click="openCreateModal" />
        </div>

        <div class="p-4 flex-1 overflow-y-auto bg-white/50">
            <div class="space-y-2">
                @forelse($selectedCategory->items as $item)
                    <div
                        class="flex items-center justify-between p-3 bg-white rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow group relative">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if(isset($item->metadata['color']))
                                    <span
                                        class="px-3 py-1 rounded-full text-[10px] font-bold {{ $this->getTailwindColor($item->metadata['color']) }} border border-transparent uppercase tracking-wider">
                                        {{ $item->display_label }}
                                    </span>
                                @else
                                    <span class="font-medium text-slate-900">{{ $item->display_label }}</span>
                                @endif
                                @if($item->is_default)
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-600 uppercase tracking-widest">Varsayılan</span>
                                @endif
                                <div class="text-[10px] text-slate-400 font-mono uppercase tracking-wider">{{ $item->key }}
                                </div>
                            </div>
                            @if($item->description)
                                <div class="text-xs text-slate-500 mt-1 truncate">{{ $item->description }}</div>
                            @endif
                        </div>

                        <div class="flex items-center gap-1 pl-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{-- Move buttons --}}
                            <div class="flex flex-col gap-0.5 mr-1">
                                <button wire:click="moveItemUp('{{ $item->id }}')"
                                    class="p-1 text-slate-400 hover:text-indigo-600">
                                    <x-mary-icon name="o-chevron-up" class="w-3 h-3" />
                                </button>
                                <button wire:click="moveItemDown('{{ $item->id }}')"
                                    class="p-1 text-slate-400 hover:text-indigo-600">
                                    <x-mary-icon name="o-chevron-down" class="w-3 h-3" />
                                </button>
                            </div>

                            <x-mary-button icon="o-pencil-square" class="btn-ghost btn-xs text-slate-400 hover:text-indigo-600"
                                wire:click="editItem('{{ $item->id }}')" />
                            <x-mary-button icon="o-trash" class="btn-ghost btn-xs text-slate-400 hover:text-rose-600"
                                wire:click="deleteItem('{{ $item->id }}')"
                                wire:confirm="Bu öğeyi silmek istediğinize emin misiniz?" />
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 text-center text-slate-400">
                        <x-mary-icon name="o-inbox" class="w-12 h-12 mb-2 opacity-20" />
                        <p class="text-sm">Bu kategori boş.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <div class="h-full flex flex-col items-center justify-center bg-slate-50/50">
            <div class="p-8 text-center text-slate-400">
                <x-mary-icon name="o-cursor-arrow-rays" class="w-12 h-12 mx-auto mb-4 opacity-10" />
                <h3 class="text-sm font-bold uppercase tracking-widest mb-1">Kategori Seçimi</h3>
                <p class="text-xs">İşlem yapmak için soldan bir kategori seçin.</p>
            </div>
        </div>
    @endif
</div>