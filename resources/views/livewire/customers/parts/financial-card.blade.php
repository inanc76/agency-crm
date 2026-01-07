{{-- Cari Bilgiler Card --}}
<div class="card border p-6 shadow-sm @if($isViewMode) bg-slate-50/60 @endif">
    <h2 class="text-base font-semibold text-slate-800 mb-4">Cari Bilgiler</h2>

    <div class="grid grid-cols-2 gap-8">
        {{-- Firma Ünvanı --}}
        <div class="col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Firma Ünvanı</label>
            @if($isViewMode)
                <div class="text-sm font-semibold text-slate-900">{{ $title ?: '-' }}</div>
            @else
                <input type="text" wire:model="title" placeholder="Örn: ABC Teknoloji Ltd. Şti." class="input w-full">
                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Vergi Dairesi --}}
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Vergi Dairesi</label>
            @if($isViewMode)
                <div class="text-sm font-semibold text-slate-900">{{ $tax_office ?: '-' }}</div>
            @else
                <input type="text" wire:model="tax_office" placeholder="Örn: Halkalı" class="input w-full">
                @error('tax_office') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Vergi Numarası --}}
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Vergi Numarası</label>
            @if($isViewMode)
                <div class="text-sm font-semibold text-slate-900">{{ $tax_number ?: '-' }}</div>
            @else
                <input type="text" wire:model="tax_number" placeholder="Örn: 1234567890" class="input w-full">
                @error('tax_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Cari Kod --}}
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Cari Kod</label>
            @if($isViewMode)
                <div class="text-sm font-semibold text-slate-900">{{ $current_code ?: '-' }}</div>
            @else
                <input type="text" wire:model="current_code" placeholder="Örn: 120.02.0155" class="input w-full">
                @error('current_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>
    </div>
</div>