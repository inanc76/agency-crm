{{-- Firma Logosu Card --}}
<div class="theme-card p-6 shadow-sm sticky top-6">
    <h2 class="text-base font-bold mb-4 text-center text-skin-heading">Firma Logosu</h2>

    <div class="flex flex-col items-center">
        {{-- Logo Preview --}}
        <div
            class="w-32 h-32 border-2 border-dashed border-[var(--card-border)] rounded-lg flex items-center justify-center mb-4 bg-[var(--card-bg)]/50 overflow-hidden">
            @if($logo)
                <img src="{{ $logo->temporaryUrl() }}" alt="Logo Preview" class="w-full h-full object-contain">
            @elseif($logo_url)
                <img src="{{ str_contains($logo_url, '/storage/') ? $logo_url : asset('storage' . $logo_url) }}"
                    alt="Logo" class="w-full h-full object-contain">
            @else
                @php
                    $initials = mb_substr($name ?? 'C', 0, 1) ?: 'C';
                @endphp
                <div
                    class="w-full h-full flex items-center justify-center bg-[var(--dropdown-hover-bg)] text-[var(--color-text-muted)] font-bold text-5xl uppercase">
                    {{ $initials }}
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            {{-- Upload Button --}}
            @if(!$isViewMode)
                <label class="cursor-pointer">
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--dropdown-hover-bg)]/50 hover:bg-[var(--dropdown-hover-bg)] text-skin-heading rounded-lg text-sm font-medium transition-colors">
                        <x-mary-icon name="o-arrow-up-tray" class="w-4 h-4" />
                        {{ $logo_url ? 'Değiştir' : 'Logo Yükle' }}
                    </span>
                    <input type="file" wire:model="logo" accept="image/png,image/jpeg,image/gif" class="hidden">
                </label>

                {{-- Delete Logo Button --}}
                @if($logo_url)
                    <button type="button" wire:click="deleteLogo" wire:confirm="Logoyu silmek istediğinize emin misiniz?"
                        class="theme-btn-delete p-2 rounded-lg" title="Logoyu Sil">
                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                    </button>
                @endif
            @endif
        </div>

        <p class="text-xs mt-2 text-center opacity-40 text-skin-base">PNG, JPG, GIF (Max 5MB)
        </p>

        @error('logo')
            <p class="text-[var(--color-danger)] text-xs mt-2">{{ $message }}</p>
        @enderror
    </div>
</div>