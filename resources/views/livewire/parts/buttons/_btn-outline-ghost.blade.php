{{--
SECTION: Outline, Ghost & Link Buttons
Use Cases:
- Ghost (Cancel): Secondary actions, closing modals, resetting forms.
- Link: Navigation within components, inline actions.
- Outline: Alternative secondary actions (often used for 'Back' or 'View Details').
--}}

<div class="space-y-8">
    {{-- 5. İptal (Cancel) Button --}}
    <div>
        <h3
            class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2 flex items-center gap-2">
            <x-mary-icon name="o-x-circle" class="w-4 h-4 text-[var(--color-text-muted)]" /> İptal Butonu
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Background Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_cancel_bg_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_cancel_bg_color" placeholder="#94a3b8" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Text Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_cancel_text_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_cancel_text_color" placeholder="#ffffff" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Hover Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_cancel_hover_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_cancel_hover_color" placeholder="#64748b" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="btn_cancel_border_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="btn_cancel_border_color" placeholder="#94a3b8" class="flex-1" />
                </div>
            </div>
        </div>
    </div>

    {{-- Action Links --}}
    <div>
        <h3 class="text-sm font-bold text-skin-heading mb-4 border-b border-[var(--card-border)] pb-2">Diğer Aksiyonlar
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Link Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="action_link_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="action_link_color" placeholder="#4f46e5" class="flex-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-skin-base mb-2">Active Tab Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" wire:model.live="active_tab_color"
                        class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                    <x-mary-input wire:model.live="active_tab_color" placeholder="#4f46e5" class="flex-1" />
                </div>
            </div>
        </div>
    </div>
</div>