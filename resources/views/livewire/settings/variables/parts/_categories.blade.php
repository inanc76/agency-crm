<div class="w-full lg:w-1/2 bg-white border border-slate-100 rounded-xl flex flex-col h-full shadow-sm">
    <div class="p-4 border-b border-slate-50 flex justify-between items-center">
        <div class="font-bold text-slate-800">Kategoriler</div>
        <x-mary-button label="Yeni Kategori" icon="o-plus"
            class="btn-sm btn-ghost text-indigo-600"
            wire:click="openCreateCategoryModal" />
    </div>
    <div class="px-4 py-3 border-b border-slate-50 bg-slate-50/50">
        <div class="relative">
            <x-mary-icon name="o-magnifying-glass"
                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
            <input wire:model.live="search" type="search" placeholder="Kategori ara..."
                class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-indigo-500 transition-colors">
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-2">
        @foreach($categories as $category)
            <div class="group relative flex items-center justify-between p-4 rounded-xl border transition-all duration-200 cursor-pointer hover:shadow-md {{ $selectedCategoryKey === $category->key ? 'border-indigo-200 bg-indigo-50/30' : 'border-slate-100 bg-white hover:border-slate-200' }}"
                wire:click="selectCategory('{{ $category->key }}')">

                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-slate-900 truncate">{{ $category->name }}</div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5 uppercase tracking-wider">{{ $category->key }}</div>
                </div>

                <div class="flex items-center gap-3 pl-3">
                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">{{ $category->items->count() }}</span>

                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <x-mary-button icon="o-pencil-square" class="btn-ghost btn-xs text-slate-400"
                            wire:click.stop="editCategory('{{ $category->id }}')" />
                        <x-mary-button icon="o-trash" class="btn-ghost btn-xs text-slate-400 text-rose-400"
                            wire:click.stop="deleteCategory('{{ $category->id }}')"
                            wire:confirm="Bu kategoriyi silmek istediğinize emin misiniz?" />
                    </div>
                </div>
            </div>
        @endforeach

        @if($categories->isEmpty())
            <div class="p-12 text-center text-slate-400">
                <x-mary-icon name="o-folder" class="w-8 h-8 mx-auto mb-2 opacity-20" />
                <p class="text-xs">Kategori bulunamadı.</p>
            </div>
        @endif
    </div>
</div>
