{{-- Accordion 2: Input & Validation --}}
<x-mary-collapse name="group_design_2" group="settings_design" separator
    class="bg-[var(--card-bg)] border border-[var(--card-border)] shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center gap-3">
            <x-mary-icon name="o-pencil-square" class="w-5 h-5 text-[var(--brand-primary)]" />
            <span class="font-semibold text-skin-heading">Input & Validation</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="space-y-6 py-2">
            {{-- Normal State --}}
            <div>
                <h3 class="text-sm font-semibold text-skin-heading mb-3 block">Normal State</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Focus Ring Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="input_focus_ring_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="input_focus_ring_color" placeholder="#6366f1"
                                class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="input_border_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="input_border_color" placeholder="#cbd5e1" class="flex-1" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error State --}}
            <div>
                <h3 class="text-sm font-semibold text-skin-heading mb-3 block">Error State</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Error Ring Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="input_error_ring_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="input_error_ring_color" placeholder="#ef4444"
                                class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Error Border Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="input_error_border_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="input_error_border_color" placeholder="#ef4444"
                                class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Error Text Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="input_error_text_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="input_error_text_color" placeholder="#ef4444"
                                class="flex-1" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Geometry & Fonts --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-900 mb-3 block">Input Geometry & Typography</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <x-mary-input label="Vertical Padding" wire:model="input_vertical_padding" hint="Örn: 8px" />
                    <x-mary-input label="Border Radius" wire:model="input_border_radius"
                        hint="Örn: 6px veya rounded-md" />
                    <x-mary-input label="Label Font Size" wire:model="label_font_size" suffix="px"
                        hint="Default: 14px" />
                    <x-mary-input label="Input Font Size" wire:model="input_font_size" suffix="px"
                        hint="Default: 16px" />
                </div>
            </div>

            {{-- Helper & Error Typography --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-900 mb-3 block">Validation Typography</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-mary-input label="Error Message Font Size" wire:model="error_font_size" suffix="px"
                        hint="Default: 12px" />
                    <x-mary-input label="Helper Text Font Size" wire:model="helper_font_size" suffix="px"
                        hint="Default: 12px" />
                </div>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>