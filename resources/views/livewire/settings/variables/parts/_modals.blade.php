<div>
    {{-- Category Create/Edit Modal --}}
    <x-mary-modal wire:model="showCategoryModal" title="{{ $categoryId ? 'Kategoriyi Düzenle' : 'Yeni Kategori' }}"
        class="backdrop-blur" box-class="!max-w-lg">
        <div class="grid gap-4">
            <x-mary-input label="Anahtar" wire:model="categoryKey" placeholder="CATEGORY_KEY"
                hint="Sistem tarafında kullanılacak benzersiz kod" />
            <x-mary-input label="İsim" wire:model="categoryName" placeholder="Kategori İsmi" />
            <x-mary-textarea label="Açıklama" wire:model="categoryDescription" placeholder="Kategori açıklaması"
                rows="3" />
        </div>
        <x-slot:actions>
            <x-mary-button label="İptal" @click="$wire.showCategoryModal = false" class="btn-ghost" />
            <button type="button" class="theme-btn-save" wire:click="saveCategory">
                {{ $categoryId ? 'Güncelle' : 'Oluştur' }}
            </button>
        </x-slot:actions>
    </x-mary-modal>

    {{-- Create/Edit Item Modal --}}
    <x-mary-modal wire:model="showItemModal" title="{{ $itemId ? 'Öğeyi Düzenle' : 'Yeni Öğe' }}" class="backdrop-blur"
        box-class="!max-w-2xl">
        <div class="grid gap-5">
            <x-mary-input label="Anahtar (Key)" wire:model="key"
                hint="Sistem tarafında kullanılacak benzersiz kod (örn: MALE)" />

            <x-mary-input label="Görünen İsim" wire:model="display_label"
                hint="Arayüzde kullanıcıların göreceği isim (örn: Erkek)" />

            <x-mary-textarea label="Açıklama" wire:model="description" placeholder="Öğe hakkında açıklama yazın..."
                rows="3" hint="Opsiyonel - Bu öğe hakkında ek bilgi" />

            {{-- Color Picker --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-3">Renk Şeması</label>
                <div class="p-4 border border-slate-100 rounded-xl bg-slate-50/30">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ön İzleme:</span>
                        <span
                            class="px-3 py-1 rounded-full text-[10px] font-bold border border-transparent {{ $this->getTailwindColor($selectedColor) }} uppercase tracking-wider shadow-sm">
                            {{ $display_label ?: 'Örnek Etiket' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-5 gap-y-4 gap-x-2">
                        @foreach($availableColors as $colorScheme)
                            <button type="button" wire:click="$set('selectedColor', '{{ $colorScheme['id'] }}')"
                                class="flex flex-col items-center justify-center p-2 rounded-lg border transition-all duration-200 group {{ $selectedColor === $colorScheme['id'] ? 'border-indigo-200 bg-white ring-2 ring-indigo-50 shadow-sm' : 'border-transparent hover:bg-slate-100' }}">
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold {{ $this->getTailwindColor($colorScheme['id']) }} mb-1 shadow-sm uppercase tracking-tighter">ABC</span>
                                <span
                                    class="text-[8px] uppercase tracking-tighter {{ $selectedColor === $colorScheme['id'] ? 'text-indigo-600 font-bold' : 'text-slate-400' }}">{{ $colorScheme['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <x-mary-toggle label="Varsayılan Öğe" wire:model="is_default"
                hint="Bu kategorinin varsayılan seçeneği olsun" class="toggle-info" />
        </div>

        <x-slot:actions>
            <x-mary-button label="İptal" @click="$wire.showItemModal = false" class="btn-ghost" />
            <button type="button" class="theme-btn-save" wire:click="saveItem">
                {{ $itemId ? 'Güncelle' : 'Oluştur' }}
            </button>
        </x-slot:actions>
    </x-mary-modal>
</div>