{{--
SECTION: Standard Buttons (Primary, Secondary, Success, Danger)
Use Cases:
- Primary (Create): Main actions like "Add New", "Create".
- Success (Save): Confirming actions, saving forms.
- Warning (Edit): Edit modes, important but not destructive alerts.
- Danger (Delete): Destructive actions, delete confirmations.
--}}

<div class="space-y-8">
    {{-- 1. Ekle (Create) Button --}}
    <div>
        <h3
            class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
            <x-mary-icon name="o-plus-circle" class="w-4 h-4 text-[var(--brand-primary)]" /> Ekle Butonu
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Background Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_create_bg_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_create_bg_color" placeholder="#4f46e5" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Text Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_create_text_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_create_text_color" placeholder="#ffffff" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Hover Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_create_hover_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_create_hover_color" placeholder="#4338ca" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_create_border_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_create_border_color" placeholder="#4f46e5" class="flex-1" />
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Kaydet (Save) Button --}}
    <div>
        <h3
            class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
            <x-mary-icon name="o-check-circle" class="w-4 h-4" style="color: var(--btn-save-bg);" /> Kaydet Butonu
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Background Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_save_bg_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_save_bg_color" placeholder="#10b981" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Text Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_save_text_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_save_text_color" placeholder="#ffffff" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Hover Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_save_hover_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_save_hover_color" placeholder="#059669" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_save_border_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_save_border_color" placeholder="#10b981" class="flex-1" />
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Düzenle (Edit) Button --}}
    <div>
        <h3
            class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
            <x-mary-icon name="o-pencil-square" class="w-4 h-4 text-[var(--color-warning)]" /> Düzenle Butonu
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Background Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_edit_bg_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_edit_bg_color" placeholder="#f59e0b" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Text Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_edit_text_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_edit_text_color" placeholder="#ffffff" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Hover Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_edit_hover_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_edit_hover_color" placeholder="#d97706" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_edit_border_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_edit_border_color" placeholder="#f59e0b" class="flex-1" />
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Sil (Delete) Button --}}
    <div>
        <h3
            class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
            <x-mary-icon name="o-trash" class="w-4 h-4 text-[var(--color-danger)]" /> Sil Butonu
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Background Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_delete_bg_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_delete_bg_color" placeholder="#ef4444" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Text Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_delete_text_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_delete_text_color" placeholder="#ffffff" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Hover Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_delete_hover_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_delete_hover_color" placeholder="#dc2626" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_delete_border_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_delete_border_color" placeholder="#ef4444" class="flex-1" />
                </div>
            </div>
        </div>
    </div>
</div>