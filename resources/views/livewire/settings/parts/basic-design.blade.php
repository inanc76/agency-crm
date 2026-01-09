<div class="theme-card p-6 shadow-sm mt-6"
    style="background-color: {{ $card_bg_color }}; border-color: {{ $card_border_color }}; border-radius: {{ $card_border_radius }};">
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
                        <label class="block text-sm font-medium text-slate-700 mb-2">Sayfa Arka Plan Rengi</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="page_bg_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="page_bg_color" placeholder="#f8fafc" class="flex-1" />
                        </div>
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
                    <div>
                        <x-mary-input label="Form Başlığı (H2) Font Boyutu" wire:model="heading_font_size" suffix="px"
                            hint="Default: 18px" />
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

                    {{-- Geometry & Fonts --}}
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-3 block">Input Geometry & Typography</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <x-mary-input label="Vertical Padding" wire:model="input_vertical_padding"
                                hint="Örn: 8px" />
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
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Active Tab Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="active_tab_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="active_tab_color" placeholder="#4f46e5"
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
                        <x-mary-input label="Border Radius" wire:model.live="card_border_radius" hint="Örn: 12px" />
                    </div>
                </div>
            </x-slot:content>
        </x-mary-collapse>

        {{-- Accordion 5: Tablo Ayarları --}}
        <x-mary-collapse name="group_design_5" group="settings_design" separator
            class="bg-white border border-slate-200 shadow-sm rounded-lg">
            <x-slot:heading>
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-table-cells" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Tablo Ayarları</span>
                </div>
            </x-slot:heading>
            <x-slot:content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Row Hover Background Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_hover_bg_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="table_hover_bg_color" placeholder="#f8fafc" class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Row Hover Text Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="table_hover_text_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="table_hover_text_color" placeholder="#0f172a"
                                class="flex-1" />
                        </div>
                    </div>
                </div>

                {{-- Table Avatar Settings --}}
                <div class="border-t border-slate-100 pt-6 mt-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3 block">Table Avatar Styling</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Avatar Background</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="table_avatar_bg_color"
                                    class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                <x-mary-input wire:model.live="table_avatar_bg_color" placeholder="#f1f5f9"
                                    class="flex-1" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Avatar Border Color</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="table_avatar_border_color"
                                    class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                <x-mary-input wire:model.live="table_avatar_border_color" placeholder="#e2e8f0"
                                    class="flex-1" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Avatar Text Color</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="table_avatar_text_color"
                                    class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                <x-mary-input wire:model.live="table_avatar_text_color" placeholder="#475569"
                                    class="flex-1" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- List Card Settings --}}
                <div class="border-t border-slate-100 pt-6 mt-6">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3 block">List Card Styling</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">List Card Background</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="list_card_bg_color"
                                    class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                <x-mary-input wire:model.live="list_card_bg_color" placeholder="#ffffff"
                                    class="flex-1" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">List Card Border Color</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="list_card_border_color"
                                    class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                <x-mary-input wire:model.live="list_card_border_color" placeholder="#e2e8f0"
                                    class="flex-1" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">List Card Link Color</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="list_card_link_color"
                                    class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                <x-mary-input wire:model.live="list_card_link_color" placeholder="#4f46e5"
                                    class="flex-1" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">List Card Hover Color</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="list_card_hover_color"
                                    class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                <x-mary-input wire:model.live="list_card_hover_color" placeholder="#f8fafc"
                                    class="flex-1" />
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot:content>
        </x-mary-collapse>

        {{-- Accordion 6: Combo Box --}}
        <x-mary-collapse name="group_design_6" group="settings_design" separator
            class="bg-white border border-slate-200 shadow-sm rounded-lg">
            <x-slot:heading>
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-chevron-up-down" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Combo Box</span>
                </div>
            </x-slot:heading>
            <x-slot:content>
                <div class="space-y-8 py-4">

                    {{-- 1. Filtre Combo Box --}}
                    <div>
                        <h3
                            class="text-sm font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                            <x-mary-icon name="o-funnel" class="w-4 h-4 text-blue-500" />
                            Filtre Combo Box
                            <span class="text-xs font-normal text-slate-400 ml-2">(Liste sayfalarındaki
                                filtreler)</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Preview --}}
                            <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                <label class="block text-xs font-medium text-slate-500 mb-2">Önizleme</label>
                                <div class="flex items-center gap-3">
                                    <select class="select select-sm bg-white border-slate-200 text-xs w-40">
                                        <option>Tüm Kategoriler</option>
                                        <option>Kategori 1</option>
                                        <option>Kategori 2</option>
                                    </select>
                                    <select class="select select-sm bg-white border-slate-200 text-xs w-32">
                                        <option>Tüm Durumlar</option>
                                        <option>Aktif</option>
                                        <option>Pasif</option>
                                    </select>
                                </div>
                            </div>
                            {{-- Code Example --}}
                            <div class="bg-slate-900 rounded-lg p-4 text-xs font-mono text-green-400 overflow-x-auto">
                                <pre>&lt;select class="select select-sm bg-white 
       border-slate-200 text-xs"&gt;
    &lt;option&gt;Seçenek&lt;/option&gt;
&lt;/select&gt;</pre>
                            </div>
                        </div>
                        <div class="text-xs text-slate-500 bg-blue-50 p-3 rounded-lg border border-blue-100">
                            <strong class="text-blue-700">Kullanım:</strong> Liste sayfalarının üst kısmındaki
                            filtreleme panellerinde kullanılır.
                            <code class="bg-blue-100 px-1 rounded text-blue-800">select-sm</code> sınıfı küçük boyut
                            sağlar.
                        </div>
                    </div>

                    {{-- 1b. Tab Inline Filtre Combo Box --}}
                    <div>
                        <h3
                            class="text-sm font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                            <x-mary-icon name="o-adjustments-horizontal" class="w-4 h-4 text-emerald-500" />
                            Tab Inline Filtre
                            <span class="text-xs font-normal text-slate-400 ml-2">(Tab içi kompakt filtreler)</span>
                            <span
                                class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded">XS</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Preview --}}
                            <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                <label class="block text-xs font-medium text-slate-500 mb-2">Önizleme</label>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-slate-700">Hizmetler</span>
                                    <select class="select select-xs bg-white border-slate-200">
                                        <option>Tüm Durumlar</option>
                                        <option>Aktif</option>
                                        <option>Pasif</option>
                                    </select>
                                </div>
                            </div>
                            {{-- Code Example --}}
                            <div class="bg-slate-900 rounded-lg p-4 text-xs font-mono text-green-400 overflow-x-auto">
                                <pre>&lt;select class="select select-xs bg-white 
       border-slate-200"&gt;
    &lt;option&gt;Tüm Durumlar&lt;/option&gt;
&lt;/select&gt;</pre>
                            </div>
                        </div>
                        <div class="text-xs text-slate-500 bg-emerald-50 p-3 rounded-lg border border-emerald-100">
                            <strong class="text-emerald-700">Kullanım:</strong> Müşteri detay tabları içinde satır başı
                            filtreler için kullanılır.
                            <code class="bg-emerald-100 px-1 rounded text-emerald-800">select-xs</code> sınıfı ekstra
                            küçük boyut sağlar (28px yükseklik).
                        </div>
                    </div>

                    {{-- 2. Form Combo Box --}}
                    <div>
                        <h3
                            class="text-sm font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                            <x-mary-icon name="o-document-plus" class="w-4 h-4 text-purple-500" />
                            Form Combo Box
                            <span class="text-xs font-normal text-slate-400 ml-2">(Yeni ekle / düzenle formları)</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Preview --}}
                            <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                <label class="block text-xs font-medium text-slate-500 mb-2">Önizleme</label>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium mb-1 opacity-60">Müşteri Seçimi
                                            *</label>
                                        <select class="select w-full">
                                            <option>Müşteri Seçin</option>
                                            <option>Örnek Müşteri A.Ş.</option>
                                            <option>Demo Ltd. Şti.</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium mb-1 opacity-60">Kategori *</label>
                                        <select class="select w-full">
                                            <option>Kategori Seçin</option>
                                            <option>Web Hosting</option>
                                            <option>Domain</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            {{-- Code Example --}}
                            <div class="bg-slate-900 rounded-lg p-4 text-xs font-mono text-green-400 overflow-x-auto">
                                <pre>&lt;label class="block text-xs font-medium 
       mb-1 opacity-60"&gt;Label *&lt;/label&gt;
&lt;select wire:model="field" class="select w-full"&gt;
    &lt;option value=""&gt;Seçin&lt;/option&gt;
    @@foreach($items as $item)
        &lt;option value="@{{ $item['id'] }}"&gt;
            @{{ $item['name'] }}
        &lt;/option&gt;
    @@endforeach
&lt;/select&gt;
@@error('field') 
    &lt;span class="text-red-500 text-xs"&gt;
        @{!! $message !!}
    &lt;/span&gt; 
@@enderror</pre>
                            </div>
                        </div>
                        <div class="text-xs text-slate-500 bg-purple-50 p-3 rounded-lg border border-purple-100">
                            <strong class="text-purple-700">Kullanım:</strong> Yeni kayıt oluşturma ve düzenleme
                            formlarında kullanılır.
                            <code class="bg-purple-100 px-1 rounded text-purple-800">w-full</code> sınıfı tam genişlik
                            sağlar.
                            <code class="bg-purple-100 px-1 rounded text-purple-800">wire:model</code> ile Livewire
                            binding yapılır.
                        </div>
                    </div>

                    {{-- Style Classes Reference --}}
                    <div>
                        <h3
                            class="text-sm font-bold text-slate-900 mb-4 border-b border-gray-100 pb-2 flex items-center gap-2">
                            <x-mary-icon name="o-code-bracket" class="w-4 h-4 text-slate-500" />
                            CSS Sınıfları Referansı
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 bg-slate-50">
                                        <th class="text-left py-2 px-3 font-medium text-slate-700">Sınıf</th>
                                        <th class="text-left py-2 px-3 font-medium text-slate-700">Açıklama</th>
                                        <th class="text-left py-2 px-3 font-medium text-slate-700">Kullanım Alanı</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs">
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 px-3"><code
                                                class="bg-slate-100 px-1.5 py-0.5 rounded">select</code></td>
                                        <td class="py-2 px-3 text-slate-600">Temel select stili</td>
                                        <td class="py-2 px-3 text-slate-500">Tüm comboboxlar</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 px-3"><code
                                                class="bg-slate-100 px-1.5 py-0.5 rounded">select-sm</code></td>
                                        <td class="py-2 px-3 text-slate-600">Küçük boyut</td>
                                        <td class="py-2 px-3 text-slate-500">Filtre panelleri</td>
                                    </tr>
                                    <tr class="border-b border-slate-100 bg-emerald-50/30">
                                        <td class="py-2 px-3"><code
                                                class="bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded">select-xs</code>
                                        </td>
                                        <td class="py-2 px-3 text-slate-600 font-bold">Ekstra Küçük</td>
                                        <td class="py-2 px-3 text-slate-500">Tab içi kompakt filtreler</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 px-3"><code
                                                class="bg-slate-100 px-1.5 py-0.5 rounded">w-full</code></td>
                                        <td class="py-2 px-3 text-slate-600">Tam genişlik</td>
                                        <td class="py-2 px-3 text-slate-500">Form alanları</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 px-3"><code
                                                class="bg-slate-100 px-1.5 py-0.5 rounded">bg-white</code></td>
                                        <td class="py-2 px-3 text-slate-600">Beyaz arka plan</td>
                                        <td class="py-2 px-3 text-slate-500">Tüm comboboxlar</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 px-3"><code
                                                class="bg-slate-100 px-1.5 py-0.5 rounded">border-slate-200</code></td>
                                        <td class="py-2 px-3 text-slate-600">Açık gri kenarlık</td>
                                        <td class="py-2 px-3 text-slate-500">Normal durum</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 px-3"><code
                                                class="bg-slate-100 px-1.5 py-0.5 rounded">text-xs</code></td>
                                        <td class="py-2 px-3 text-slate-600">Küçük font boyutu</td>
                                        <td class="py-2 px-3 text-slate-500">Filtre panelleri</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </x-slot:content>
        </x-mary-collapse>

        {{-- Accordion 7: Dashboard Özelleştirme --}}
        <x-mary-collapse name="group_design_7" group="settings_design" separator
            class="bg-white border border-slate-200 shadow-sm rounded-lg">
            <x-slot:heading>
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-presentation-chart-line" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Dashboard Özelleştirme</span>
                </div>
            </x-slot:heading>
            <x-slot:content>
                <div class="space-y-6 py-4">
                    {{-- Dashboard Card Colors --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase text-slate-500 mb-3 block">Dashboard Kartları</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Card Background</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="dashboard_card_bg_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="dashboard_card_bg_color" placeholder="#eff4ff"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Card Text Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="dashboard_card_text_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="dashboard_card_text_color" placeholder="#475569"
                                        class="flex-1" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Colors --}}
                    <div class="border-t border-slate-100 pt-4">
                        <h3 class="text-xs font-semibold uppercase text-slate-500 mb-3 block">İstatistik Kart Renkleri
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Stats 1 (Blue)</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="dashboard_stats_1_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="dashboard_stats_1_color" placeholder="#3b82f6"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Stats 2 (Teal)</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="dashboard_stats_2_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="dashboard_stats_2_color" placeholder="#14b8a6"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Stats 3 (Amber)</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="dashboard_stats_3_color"
                                        class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="dashboard_stats_3_color" placeholder="#f59e0b"
                                        class="flex-1" />
                                </div>
                            </div>
                        </div>
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