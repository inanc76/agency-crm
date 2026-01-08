{{-- Firma Logosu Card --}}
<div class="theme-card p-6 shadow-sm sticky top-6">
    <h2 class="text-base font-bold mb-4 text-center" style="color: var(--color-text-heading);">Firma Logosu</h2>

    <div class="flex flex-col items-center">
        {{-- Logo Preview --}}
        <div
            class="w-32 h-32 border-2 border-dashed border-slate-300 rounded-lg flex items-center justify-center mb-4 bg-white/50 overflow-hidden">
            @if($logo)
                <img src="{{ $logo->temporaryUrl() }}" alt="Logo Preview" class="w-full h-full object-contain">
            @elseif($logo_url)
                <img src="{{ asset('storage' . $logo_url) }}" alt="Logo" class="w-full h-full object-contain">
            @else
                @php
                    $initials = mb_substr($name ?? 'C', 0, 1) ?: 'C';
                @endphp
                <div
                    class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400 font-bold text-5xl uppercase">
                    {{ $initials }}
                </div>
            @endif
        </div>

        {{-- Upload Button --}}
        @if(!$isViewMode)
            <label class="cursor-pointer">
                <span
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100/50 hover:bg-slate-200/50 text-slate-700 rounded-lg text-sm font-medium transition-colors"
                    style="color: var(--color-text-heading);">
                    <x-mary-icon name="o-arrow-up-tray" class="w-4 h-4" />
                    Logo Yükle
                </span>
                <input type="file" wire:model="logo" accept="image/png,image/jpeg,image/gif" class="hidden">
            </label>
        @endif

        <p class="text-xs mt-2 text-center opacity-40" style="color: var(--color-text-base);">PNG, JPG, GIF (Max 5MB)
        </p>

        @error('logo')
            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
        @enderror
    </div>
</div>