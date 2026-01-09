{{-- Cari Bilgiler Card --}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-skin-heading">Cari Bilgiler</h2>

    <div class="grid grid-cols-2 gap-8">
        {{-- Firma Ünvanı --}}
        <div class="col-span-2">
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Firma
                Ünvanı</label>
            @if($isViewMode)
                <div class="text-sm font-medium text-skin-base">{{ $title ?: '-' }}</div>
            @else
                <input type="text" wire:model.blur="title" placeholder="Örn: ABC Teknoloji Ltd. Şti." class="input w-full">
                @error('title') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Vergi Dairesi --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Vergi
                Dairesi</label>
            @if($isViewMode)
                <div class="text-sm font-medium text-skin-base">{{ $tax_office ?: '-' }}</div>
            @else
                <input type="text" wire:model.blur="tax_office" placeholder="Örn: Halkalı" class="input w-full">
                @error('tax_office') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Vergi Numarası --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Vergi
                Numarası</label>
            @if($isViewMode)
                <div class="text-sm font-medium text-skin-base">{{ $tax_number ?: '-' }}</div>
            @else
                <input type="text" wire:model="tax_number" placeholder="Örn: 1234567890" class="input w-full">
                @error('tax_number') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Cari Kod --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Cari
                Kod</label>
            @if($isViewMode)
                <div class="text-sm font-medium text-skin-base">{{ $current_code ?: '-' }}</div>
            @else
                <input type="text" wire:model.blur="current_code" placeholder="Örn: 120.02.0155" class="input w-full">
                @error('current_code') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>
    </div>
</div>