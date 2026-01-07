@props([
    'showCategories' => true,
    'showAlphabet' => true,
    'categoryLabel' => 'Tüm Kategoriler',
    'statusLabel' => 'Duruma Göre Filtrele',
    'letter' => ''
])

<x-mary-card class="bg-white border shadow-sm mb-6" shadow separator>
    <div class="flex flex-wrap items-center gap-4">
        @if($showCategories)
            <div class="w-48">
                <x-mary-select 
                    :options="[['id' => 1, 'name' => $categoryLabel]]" 
                    option-label="name" 
                    option-value="id"
                    placeholder="{{ $categoryLabel }}"
                    class="select-sm !bg-white !border-gray-200"
                />
            </div>
        @endif

        <div class="w-48">
            <x-mary-select 
                :options="[
                    ['id' => 'all', 'name' => $statusLabel],
                    ['id' => 'active', 'name' => 'Aktif'],
                    ['id' => 'passive', 'name' => 'Pasif']
                ]" 
                option-label="name" 
                option-value="id"
                class="select-sm !bg-white !border-gray-200"
            />
        </div>

        <div class="flex-grow max-w-xs">
            <x-mary-input 
                placeholder="Ara..." 
                icon="o-magnifying-glass" 
                class="input-sm !bg-white !border-gray-200"
                wire:model.live.debounce.300ms="search"
            />
        </div>

        @if($showAlphabet)
            <div class="flex items-center gap-1 ml-auto flex-wrap justify-end">
                <x-mary-button 
                    label="0-9" 
                    wire:click="$set('letter', '0-9')"
                    class="btn-ghost btn-xs font-medium {{ $letter === '0-9' ? 'bg-gray-100' : 'text-gray-500' }} hover:bg-gray-100 px-2"
                    style="{{ $letter === '0-9' ? 'color: var(--btn-primary-bg)' : '' }}"
                />
                <x-mary-button 
                    label="Tümü" 
                    wire:click="$set('letter', '')"
                    class="btn-ghost btn-xs font-medium {{ $letter === '' ? 'bg-gray-100' : 'text-gray-500' }} hover:bg-gray-100 px-2"
                    style="{{ $letter === '' ? 'color: var(--btn-primary-bg)' : '' }}"
                />
                <div class="divider divider-horizontal mx-0 h-4"></div>
                @foreach(range('A', 'Z') as $char)
                    <x-mary-button 
                        :label="$char" 
                        wire:click="$set('letter', '{{ $char }}')"
                        class="btn-ghost btn-xs font-medium {{ $letter === $char ? 'bg-gray-100' : 'text-gray-500' }} hover:bg-gray-100 min-w-[24px] !px-1"
                        style="{{ $letter === $char ? 'color: var(--btn-primary-bg)' : '' }}"
                    />
                @endforeach
            </div>
        @endif
    </div>
</x-mary-card>


