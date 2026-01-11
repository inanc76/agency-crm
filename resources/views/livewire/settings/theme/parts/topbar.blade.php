{{-- Accordion 3: Header & Top Bar Ayarları --}}
<x-mary-collapse name="group3" group="settings" separator
    class="bg-[var(--card-bg)] border border-[var(--card-border)] shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center gap-3">
            <x-mary-icon name="o-swatch" class="w-5 h-5 text-[var(--brand-primary)]" />
            <span class="font-semibold text-skin-heading">Header & Top Bar Ayarları</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="space-y-6 py-4">
            {{-- 1. General Header Colors --}}
            <div>
                <h3 class="text-xs font-semibold uppercase text-skin-muted mb-3 block">Genel Görünüm</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Header Arka Plan</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="header_bg_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="header_bg_color" placeholder="#3D3373" class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Border Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="header_border_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="header_border_color" placeholder="#000000" class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Border Width (px)</label>
                        <x-mary-input type="number" wire:model.live="header_border_width" placeholder="0" min="0"
                            max="20" />
                    </div>
                </div>
            </div>

            {{-- 2. Header Active Item --}}
            <div class="border-t border-[var(--card-border)] pt-4">
                <h3 class="text-xs font-semibold uppercase text-skin-muted mb-3 block">Aktif Menü Elemanı (Oval
                    Butonlar)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Active Background</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="header_active_item_bg_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="header_active_item_bg_color" placeholder="#ffffff"
                                class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Active Text Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="header_active_item_text_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="header_active_item_text_color" placeholder="#4f46e5"
                                class="flex-1" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Top Bar User & Notification --}}
            <div class="border-t border-[var(--card-border)] pt-4">
                <h3 class="text-xs font-semibold uppercase text-skin-muted mb-3 block">Kullanıcı Menüsü &
                    Bildirimler</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Header Icon Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="header_icon_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="header_icon_color" placeholder="#ffffff" class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Notification Badge</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="notification_badge_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="notification_badge_color" placeholder="#ef4444"
                                class="flex-1" />
                        </div>
                    </div>
                    <div class="col-span-1 md:col-span-2 lg:col-span-1">
                        <label class="block text-sm font-medium text-skin-base mb-2">Avatar Gradient
                            (Start-End)</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="avatar_gradient_start_color"
                                class="w-8 h-8 rounded border border-[var(--card-border)] cursor-pointer"
                                title="Start Color">
                            <input type="color" wire:model.live="avatar_gradient_end_color"
                                class="w-8 h-8 rounded border border-[var(--card-border)] cursor-pointer"
                                title="End Color">
                            <span class="text-xs text-skin-muted">Gradient</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Dropdown Header --}}
            <div class="border-t border-[var(--card-border)] pt-4">
                <h3 class="text-xs font-semibold uppercase text-skin-muted mb-3 block">Dropdown Menü Başlığı
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">Start Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="dropdown_header_bg_start_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="dropdown_header_bg_start_color" placeholder="#f5f3ff"
                                class="flex-1" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-skin-base mb-2">End Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="dropdown_header_bg_end_color"
                                class="w-12 h-10 rounded border border-[var(--card-border)] cursor-pointer">
                            <x-mary-input wire:model.live="dropdown_header_bg_end_color" placeholder="#eef2ff"
                                class="flex-1" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>