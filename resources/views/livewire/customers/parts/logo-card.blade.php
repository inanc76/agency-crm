{{-- Firma Logosu Card --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 sticky top-6">
    <h2 class="text-base font-semibold text-slate-800 mb-4 text-center">Firma Logosu</h2>

    <div class="flex flex-col items-center">
        {{-- Logo Preview --}}
        <div
            class="w-32 h-32 border-2 border-dashed border-slate-300 rounded-lg flex items-center justify-center mb-4 bg-slate-50 overflow-hidden">
            @if($logo)
                <img src="{{ $logo->temporaryUrl() }}" alt="Logo Preview" class="w-full h-full object-contain">
            @else
                <x-mary-icon name="o-photo" class="w-12 h-12 text-slate-400" />
            @endif
        </div>

        {{-- Upload Button --}}
        @if(!$isViewMode)
            <label class="cursor-pointer">
                <span
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-colors">
                    <x-mary-icon name="o-arrow-up-tray" class="w-4 h-4" />
                    Logo Yükle
                </span>
                <input type="file" wire:model="logo" accept="image/png,image/jpeg,image/gif" class="hidden">
            </label>
        @endif

        <p class="text-xs text-slate-500 mt-2 text-center">PNG, JPG, GIF (Max 5MB)</p>

        @error('logo')
            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
        @enderror
    </div>
</div>