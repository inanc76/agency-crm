{{-- Accordion 1: Logo --}}
<x-mary-collapse name="group1" group="settings" separator
    class="bg-[var(--card-bg)] border border-[var(--card-border)] shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center gap-3">
            <x-mary-icon name="o-photo" class="w-5 h-5 text-[var(--brand-primary)]" />
            <span class="font-semibold text-skin-heading">Logo Ayarları</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start py-2">
            {{-- Site Name --}}
            <div class="lg:col-span-3">
                <x-mary-input label="Site Adı" wire:model="site_name" hint="Logo yüklenmediğinde gözükür" />
            </div>

            {{-- Logo --}}
            <div class="lg:col-span-6">
                <label class="block text-sm font-medium text-skin-base mb-2">Logo</label>
                <div class="flex items-center gap-2">
                    @if($current_logo_path)
                        <img src="{{ asset('storage/' . $current_logo_path) }}" alt="Current Logo"
                            class="h-10 object-contain border border-[var(--card-border)] rounded p-1 bg-white">
                    @endif
                    <div class="flex-1">
                        <x-mary-file wire:model="logo" accept=".png,.jpg,.jpeg,.svg"
                            hint="PNG, JPG veya SVG, max 2MB" />
                    </div>
                    <div class="flex gap-1">
                        <button type="button" wire:click="$set('logo_scale', 1)"
                            class="px-2 py-1 text-xs font-medium rounded {{ $logo_scale == 1 ? 'text-white' : 'bg-slate-200 text-slate-700' }}"
                            style="{{ $logo_scale == 1 ? 'background-color: var(--btn-create-bg)' : '' }}">
                            1x
                        </button>
                        <button type="button" wire:click="$set('logo_scale', 1.5)"
                            class="px-2 py-1 text-xs font-medium rounded {{ $logo_scale == 1.5 ? 'text-white' : 'bg-slate-200 text-slate-700' }}"
                            style="{{ $logo_scale == 1.5 ? 'background-color: var(--btn-create-bg)' : '' }}">
                            1.5x
                        </button>
                        <button type="button" wire:click="$set('logo_scale', 2)"
                            class="px-2 py-1 text-xs font-medium rounded {{ $logo_scale == 2 ? 'text-white' : 'bg-slate-200 text-slate-700' }}"
                            style="{{ $logo_scale == 2 ? 'background-color: var(--btn-create-bg)' : '' }}">
                            2x
                        </button>
                    </div>
                </div>
            </div>

            {{-- Favicon --}}
            <div class="lg:col-span-3">
                <label class="block text-sm font-medium text-skin-base mb-2">Favicon</label>
                <div class="flex items-center gap-2">
                    @if($current_favicon_path)
                        <img src="{{ asset('storage/' . $current_favicon_path) }}" alt="Current Favicon"
                            class="w-8 h-8 object-contain border border-[var(--card-border)] rounded p-1">
                    @endif
                    <div class="flex-1">
                        <x-mary-file wire:model="favicon" accept=".ico,.png" hint="ICO veya PNG, max 512KB" />
                    </div>
                </div>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>