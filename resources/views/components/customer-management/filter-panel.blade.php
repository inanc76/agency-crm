@props([
    'showCategories' => true,
    'showAlphabet' => true,
    'showStatus' => true,
    'categoryLabel' => 'Tüm Kategoriler',
    'statusLabel' => 'Duruma Göre Filtrele',
    'letter' => ''
])

<x-mary-card class="theme-card shadow-sm mb-6" shadow separator>
    <div class="flex flex-wrap items-center gap-4">
        @if($showCategories)
            <div class="w-48">
                <x-mary-select 
                    :options="[['id' => 1, 'name' => $categoryLabel]]" 
                    option-label="name" 
                    option-value="id"
                    placeholder="{{ $categoryLabel }}"
                    class="select-sm !bg-[var(--card-bg)] !border-[var(--card-border)]"
                />
            </div>
        @endif

        @if($showStatus)
        <div class="w-48">
            <x-mary-select 
                :options="[
                    ['id' => 'all', 'name' => $statusLabel],
                    ['id' => 'active', 'name' => 'Aktif'],
                    ['id' => 'passive', 'name' => 'Pasif']
                ]" 
                option-label="name" 
                option-value="id"
                class="select-sm !bg-[var(--card-bg)] !border-[var(--card-border)]"
            />
        </div>
        @endif

        <div class="flex-grow max-w-xs">
            <x-mary-input 
                placeholder="Ara..." 
                icon="o-magnifying-glass" 
                class="input-sm !bg-[var(--card-bg)] !border-[var(--card-border)]"
                wire:model.live.debounce.300ms="search"
            />
        </div>

        @if(isset($extra))
            <div class="flex items-center">
                {{ $extra }}
            </div>
        @endif

        @if($showAlphabet)
            <div class="flex items-center gap-1 ml-auto flex-wrap justify-end">
                <x-mary-button 
                    label="0-9" 
                    wire:click="$set('letter', '0-9')"
                    class="btn-ghost btn-xs font-medium {{ $letter === '0-9' ? 'bg-[var(--dropdown-hover-bg)] text-[var(--color-text-heading)]' : 'text-[var(--color-text-muted)]' }} hover:bg-[var(--dropdown-hover-bg)] px-2"
                />
                <x-mary-button 
                    label="Tümü" 
                    wire:click="$set('letter', '')"
                    class="btn-ghost btn-xs font-medium {{ $letter === '' ? 'bg-[var(--dropdown-hover-bg)] text-[var(--color-text-heading)]' : 'text-[var(--color-text-muted)]' }} hover:bg-[var(--dropdown-hover-bg)] px-2"
                />
                <div class="divider divider-horizontal mx-0 h-4"></div>
                @foreach(range('A', 'Z') as $char)
                    <x-mary-button 
                        :label="$char" 
                        wire:click="$set('letter', '{{ $char }}')"
                        class="btn-ghost btn-xs font-medium {{ $letter === $char ? 'bg-[var(--dropdown-hover-bg)] text-[var(--color-text-heading)]' : 'text-[var(--color-text-muted)]' }} hover:bg-[var(--dropdown-hover-bg)] min-w-[24px] !px-1"
                    />
                @endforeach
            </div>
        @endif
    </div>
</x-mary-card>


