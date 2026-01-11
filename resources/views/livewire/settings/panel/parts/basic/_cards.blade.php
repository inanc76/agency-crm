{{-- Accordion 4: Kart & Konteyner --}}
<x-mary-collapse name="group_design_4" group="settings_design" separator
    class="bg-[var(--card-bg)] border border-[var(--card-border)] shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center gap-3">
            <x-mary-icon name="o-rectangle-group" class="w-5 h-5 text-[var(--brand-primary)]" />
            <span class="font-semibold text-skin-heading">Kart & Konteyner</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-2">
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Card Background</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="card_bg_color"
                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                    <x-mary-input wire:model.live="card_bg_color" placeholder="#eff4ff" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Card Border Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="card_border_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="card_border_color" placeholder="#bfdbfe" class="flex-1" />
                </div>
            </div>
            <div>
                <x-mary-input label="Border Radius" wire:model.live="card_border_radius" hint="Örn: 12px" />
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>