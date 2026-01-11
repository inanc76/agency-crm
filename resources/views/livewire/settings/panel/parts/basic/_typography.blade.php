{{-- Accordion 1: Global Tipografi --}}
<x-mary-collapse name="group_design_1" group="settings_design" separator
    class="bg-[var(--card-bg)] border border-[var(--card-border)] shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center gap-3">
            <x-mary-icon name="o-language" class="w-5 h-5 text-[var(--brand-primary)]" />
            <span class="font-semibold text-skin-heading">Global Tipografi</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 py-2">
            <div class="lg:col-span-3">
                <x-mary-input label="Font Family" wire:model="font_family"
                    hint="Sistemin tamamında kullanılacak ana font (Örn: Inter, Geist, Plus Jakarta Sans)" />
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Sayfa Arka Plan Rengi</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="page_bg_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="page_bg_color" placeholder="#f8fafc" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Base Text Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="base_text_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="base_text_color" placeholder="#475569" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Heading Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="heading_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="heading_color" placeholder="#0f172a" class="flex-1" />
                </div>
            </div>
            <div>
                <x-mary-input label="Form Başlığı (H2) Font Boyutu" wire:model="heading_font_size" suffix="px"
                    hint="Default: 18px" />
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>