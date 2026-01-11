{{-- Accordion 5: Tablo Ayarları --}}
<x-mary-collapse name="group_design_5" group="settings_design" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center gap-3">
            <x-mary-icon name="o-table-cells" class="w-5 h-5 text-indigo-500" />
            <span class="font-semibold text-skin-base">Tablo Ayarları</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-2">
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Row Hover Background Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="table_hover_bg_color"
                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                    <x-mary-input wire:model.live="table_hover_bg_color" placeholder="#f8fafc" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Row Hover Text Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="table_hover_text_color"
                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                    <x-mary-input wire:model.live="table_hover_text_color" placeholder="#0f172a" class="flex-1" />
                </div>
            </div>
        </div>

        {{-- Table Avatar Settings --}}
        <div class="border-t border-slate-100 pt-6 mt-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-3 block">Table Avatar Styling</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Avatar Background</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="table_avatar_bg_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="table_avatar_bg_color" placeholder="#f1f5f9" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Avatar Border Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="table_avatar_border_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="table_avatar_border_color" placeholder="#e2e8f0"
                            class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">Avatar Text Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="table_avatar_text_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="table_avatar_text_color" placeholder="#475569" class="flex-1" />
                    </div>
                </div>
            </div>
        </div>

        {{-- List Card Settings --}}
        <div class="border-t border-slate-100 pt-6 mt-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-3 block">List Card Styling</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">List Card Background</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="list_card_bg_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="list_card_bg_color" placeholder="#ffffff" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">List Card Border Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="list_card_border_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="list_card_border_color" placeholder="#e2e8f0" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">List Card Link Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="list_card_link_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="list_card_link_color" placeholder="#4f46e5" class="flex-1" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-skin-base mb-2">List Card Hover Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model.live="list_card_hover_color"
                            class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                        <x-mary-input wire:model.live="list_card_hover_color" placeholder="#f8fafc" class="flex-1" />
                    </div>
                </div>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>