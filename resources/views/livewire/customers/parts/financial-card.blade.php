{{-- Cari Bilgiler Card --}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4" style="color: var(--color-text-heading);">Cari Bilgiler</h2>

    <div class="grid grid-cols-2 gap-8">
        {{-- Firma Ünvanı --}}
        <div class="col-span-2">
            <label class="block text-xs font-medium mb-1 opacity-60" style="color: var(--color-text-base);">Firma
                Ünvanı</label>
            @if($isViewMode)
                <div class="text-sm font-medium" style="color: var(--color-text-base);">{{ $title ?: '-' }}</div>
            @else
                <input type="text" wire:model.blur="title" placeholder="Örn: ABC Teknoloji Ltd. Şti." class="input w-full">
                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Vergi Dairesi --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60" style="color: var(--color-text-base);">Vergi
                Dairesi</label>
            @if($isViewMode)
                <div class="text-sm font-medium" style="color: var(--color-text-base);">{{ $tax_office ?: '-' }}</div>
            @else
                <input type="text" wire:model.blur="tax_office" placeholder="Örn: Halkalı" class="input w-full">
                @error('tax_office') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Vergi Numarası --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60" style="color: var(--color-text-base);">Vergi
                Numarası</label>
            @if($isViewMode)
                <div class="text-sm font-medium" style="color: var(--color-text-base);">{{ $tax_number ?: '-' }}</div>
            @else
                <input type="text" wire:model="tax_number" placeholder="Örn: 1234567890" class="input w-full">
                @error('tax_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Cari Kod --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60" style="color: var(--color-text-base);">Cari
                Kod</label>
            @if($isViewMode)
                <div class="text-sm font-medium" style="color: var(--color-text-base);">{{ $current_code ?: '-' }}</div>
            @else
                <input type="text" wire:model.blur="current_code" placeholder="Örn: 120.02.0155" class="input w-full">
                @error('current_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>
    </div>
</div>