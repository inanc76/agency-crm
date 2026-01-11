{{-- Accordion 2: Sidebar (Sol Menü) --}}
<x-mary-collapse name="group2" group="settings" separator
    class="bg-[var(--card-bg)] border border-[var(--card-border)] shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center gap-3">
            <x-mary-icon name="o-bars-3" class="w-5 h-5 text-[var(--brand-primary)]" />
            <span class="font-semibold text-skin-heading">Sidebar (Sol Menü) Ayarları</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 py-2">
            {{-- Sidebar Background Color --}}
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Sidebar Arka Plan</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="sidebar_bg_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="sidebar_bg_color" placeholder="#3D3373" class="flex-1" />
                </div>
            </div>

            {{-- Sidebar Text Color --}}
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Sidebar Yazı Rengi</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="sidebar_text_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="sidebar_text_color" placeholder="#ffffff" class="flex-1" />
                </div>
            </div>
        </div>

        {{-- Validations & Active States --}}
        <div class="border-t border-[var(--card-border)] pt-4 mt-4">
            <h3 class="text-xs font-semibold uppercase text-skin-muted mb-3">Durum Renkleri (Hover & Active)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Hover Background</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="sidebar_hover_bg_color"
                            class="w-10 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="sidebar_hover_bg_color" placeholder="#4338ca" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Hover Text</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="sidebar_hover_text_color"
                            class="w-10 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="sidebar_hover_text_color" placeholder="#ffffff" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Active Background</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="sidebar_active_item_bg_color"
                            class="w-10 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="sidebar_active_item_bg_color" placeholder="#4f46e5"
                            class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Active Text</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="sidebar_active_item_text_color"
                            class="w-10 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                        <x-mary-input wire:model.live="sidebar_active_item_text_color" placeholder="#ffffff"
                            class="flex-1" />
                    </div>
                </div>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>