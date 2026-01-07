<div class="card border p-6 shadow-sm mt-6">
    {{-- Card Header --}}
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-200">
        <h2 class="text-sm font-medium text-slate-700">Temel Tasarım Elemanları</h2>
    </div>

    {{-- Accordion Sections --}}
    <div class="flex flex-col gap-2">

        {{-- Accordion 1: Global Tipografi --}}
        <x-mary-collapse name="group_design_1" group="settings_design" separator
            class="bg-white border border-slate-200 shadow-sm rounded-lg">
            <x-slot:heading>
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-language" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Global Tipografi</span>
                </div>
            </x-slot:heading>
            <x-slot:content>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 py-2">
                    <div class="lg:col-span-3">
                        <x-mary-input label="Font Family" wire:model="font_family"
                            hint="Sistemin tamamında kullanılacak ana font (Örn: Inter, Geist, Plus Jakarta Sans)" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Base Text Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="base_text_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="base_text_color" placeholder="#475569" class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Heading Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="heading_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="heading_color" placeholder="#0f172a" class="flex-1" />
                        </div>
                    </div>
                </div>
            </x-slot:content>
        </x-mary-collapse>

        {{-- Accordion 2: Input & Validation --}}
        <x-mary-collapse name="group_design_2" group="settings_design" separator
            class="bg-white border border-slate-200 shadow-sm rounded-lg">
            <x-slot:heading>
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-pencil-square" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Input & Validation</span>
                </div>
            </x-slot:heading>
            <x-slot:content>
                <div class="space-y-6 py-2">
                    {{-- Normal State --}}
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-3 block">Normal State</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Focus Ring Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="input_focus_ring_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="input_focus_ring_color" placeholder="#6366f1"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Border Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="input_border_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="input_border_color" placeholder="#cbd5e1"
                                        class="flex-1" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Error State --}}
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-3 block">Error State</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Error Ring Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="input_error_ring_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="input_error_ring_color" placeholder="#ef4444"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Error Border Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="input_error_border_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="input_error_border_color" placeholder="#ef4444"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Error Text Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="input_error_text_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="input_error_text_color" placeholder="#ef4444"
                                        class="flex-1" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Geometry --}}
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-3 block">Input Geometry</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-mary-input label="Vertical Padding" wire:model="input_vertical_padding"
                                hint="Örn: 0.5rem" />
                            <x-mary-input label="Border Radius" wire:model="input_border_radius"
                                hint="Örn: 0.375rem veya rounded-md" />
                        </div>
                    </div>
                </div>
            </x-slot:content>
        </x-mary-collapse>

        {{-- Accordion 3: Buton & Aksiyon --}}
        <x-mary-collapse name="group_design_3" group="settings_design" separator
            class="bg-white border border-slate-200 shadow-sm rounded-lg">
            <x-slot:heading>
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-cursor-arrow-rays" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Buton & Aksiyon Parametreleri</span>
                </div>
            </x-slot:heading>
            <x-slot:content>
                <div class="space-y-8 py-4">

                    {{-- 1. Ekle (Create) Button --}}
                    <div>
                        <h3
                            class="text-sm font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                            <x-mary-icon name="o-plus-circle" class="w-4 h-4 text-indigo-600" /> Ekle Butonu
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Background Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_create_bg_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_create_bg_color" placeholder="#4f46e5"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Text Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_create_text_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_create_text_color" placeholder="#ffffff"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Hover Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_create_hover_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_create_hover_color" placeholder="#4338ca"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Border Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_create_border_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_create_border_color" placeholder="#4f46e5"
                                        class="flex-1" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Kaydet (Save) Button --}}
                    <div>
                        <h3
                            class="text-sm font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                            <x-mary-icon name="o-check-circle" class="w-4 h-4" style="color: var(--btn-save-bg);" />
                            Kaydet Butonu
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Background Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_save_bg_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_save_bg_color" placeholder="#10b981"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Text Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_save_text_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_save_text_color" placeholder="#ffffff"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Hover Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_save_hover_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_save_hover_color" placeholder="#059669"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Border Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_save_border_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_save_border_color" placeholder="#10b981"
                                        class="flex-1" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Düzenle (Edit) Button --}}
                    <div>
                        <h3
                            class="text-sm font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                            <x-mary-icon name="o-pencil-square" class="w-4 h-4 text-amber-500" /> Düzenle Butonu
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Background Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_edit_bg_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_edit_bg_color" placeholder="#f59e0b"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Text Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_edit_text_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_edit_text_color" placeholder="#ffffff"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Hover Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_edit_hover_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_edit_hover_color" placeholder="#d97706"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Border Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_edit_border_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_edit_border_color" placeholder="#f59e0b"
                                        class="flex-1" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Sil (Delete) Button --}}
                    <div>
                        <h3
                            class="text-sm font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                            <x-mary-icon name="o-trash" class="w-4 h-4 text-red-500" /> Sil Butonu
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Background Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_delete_bg_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_delete_bg_color" placeholder="#ef4444"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Text Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_delete_text_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_delete_text_color" placeholder="#ffffff"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Hover Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_delete_hover_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_delete_hover_color" placeholder="#dc2626"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Border Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_delete_border_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_delete_border_color" placeholder="#ef4444"
                                        class="flex-1" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. İptal (Cancel) Button --}}
                    <div>
                        <h3
                            class="text-sm font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                            <x-mary-icon name="o-x-circle" class="w-4 h-4 text-slate-500" /> İptal Butonu
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Background Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_cancel_bg_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_cancel_bg_color" placeholder="#94a3b8"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Text Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_cancel_text_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_cancel_text_color" placeholder="#ffffff"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Hover Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_cancel_hover_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_cancel_hover_color" placeholder="#64748b"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Border Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="btn_cancel_border_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="btn_cancel_border_color" placeholder="#94a3b8"
                                        class="flex-1" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Links --}}
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2">Diğer Aksiyonlar
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Link Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="action_link_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="action_link_color" placeholder="#4f46e5"
                                        class="flex-1" />
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </x-slot:content>
        </x-mary-collapse>

        {{-- Accordion 4: Kart & Konteyner --}}
        <x-mary-collapse name="group_design_4" group="settings_design" separator
            class="bg-white border border-slate-200 shadow-sm rounded-lg">
            <x-slot:heading>
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-rectangle-group" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Kart & Konteyner</span>
                </div>
            </x-slot:heading>
            <x-slot:content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Card Background</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="card_bg_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="card_bg_color" placeholder="#eff4ff" class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Card Border Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="card_border_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="card_border_color" placeholder="#bfdbfe" class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <x-mary-input label="Border Radius" wire:model="card_border_radius" hint="Örn: 0.75rem" />
                    </div>
                </div>
            </x-slot:content>
        </x-mary-collapse>

    </div>

    {{-- Card Footer --}}
    <div class="flex justify-end pt-6 mt-6 border-t border-slate-200">
        <button type="button" wire:click="save" wire:loading.attr="disabled" class="theme-btn-save">
            <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>Ayarları Kaydet</span>
        </button>
    </div>
</div>