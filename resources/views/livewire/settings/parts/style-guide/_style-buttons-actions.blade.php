{{--
═══════════════════════════════════════════════════════════════════════════
🎯 STYLE GUIDE PART 2: BUTTONS & ACTION ELEMENTS
═══════════════════════════════════════════════════════════════════════════

📦 PACKAGE: resources/views/livewire/settings/parts/style-guide
📄 FILE: _style-buttons-actions.blade.php
🏗️ CONSTITUTION: V10

┌─────────────────────────────────────────────────────────────────────────┐
│ 💼 İŞ MANTIĞI ŞERHI (Business Logic) │
└─────────────────────────────────────────────────────────────────────────┘

Bu partial, sistemin TÜM BUTON VARYASYONLARını sergiler:

1. **Kullanım Alanları:**
- .theme-btn-save: Form kaydetme (Offer Create, Customer Edit)
- .theme-btn-action: Yeni kayıt ekleme (Tüm liste sayfaları)
- .theme-btn-edit: Düzenleme (Tablo satırları, detail sayfalar)
- .theme-btn-delete: Silme (Tablo satırları, modal onayları)
- .theme-btn-cancel: İptal (Modal close, form reset)
- --action-link-color: Detay linkleri (Tablo satırları)

2. **Bağlantılı Modüller:**
- Offer Form: Kaydet, İptal, Yeni Hizmet Ekle
- Customer Tabs: Yeni Contact, Yeni Asset, Düzenle, Sil
- Settings Pages: Kaydet, İptal butonları

3. **CSS Variables (PanelSetting'den beslenir):**
- --btn-save-bg: {{ $btn_save_bg_color }}
- --btn-action-bg: {{ $btn_create_bg_color }}
- --btn-edit-bg: {{ $btn_edit_bg_color }}
- --btn-delete-bg: {{ $btn_delete_bg_color }}
- --btn-cancel-bg: {{ $btn_cancel_bg_color }}
- --action-link-color: {{ $action_link_color }}

4. **Kullanım Kuralı:**
- Butonlarda SADECE theme-btn-* sınıfları kullanılmalı
- Inline style YASAK (Zero Hard-Coding)
- Icon kullanımı: <x-mary-icon name="o-..." class="w-4 h-4" />

═══════════════════════════════════════════════════════════════════════════
--}}

{{-- SECTION: Button & Action Parameters Preview --}}
<x-mary-collapse name="preview6" group="previews" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center justify-between w-full pr-4">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-cursor-arrow-rays" class="w-5 h-5 text-indigo-500" />
                <span class="font-semibold text-slate-700">Buton & Aksiyon Parametreleri Önizleme</span>
            </div>
            <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-1 rounded">.theme-btn-*,
                --action-link...</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="p-6 bg-white rounded-xl border border-slate-100 grid grid-cols-2 md:grid-cols-3 gap-6 mb-4">
            <div class="flex flex-col items-center gap-2">
                <button class="theme-btn-save w-full justify-center"
                    style="color: {{ $btn_save_text_color }} !important;">
                    <x-mary-icon name="o-check" class="w-4 h-4" /> <span>Kaydet</span>
                </button>
                <x-copy-badge text=".theme-btn-save" class="text-[9px] text-slate-400" />
            </div>
            <div class="flex flex-col items-center gap-2">
                <button class="theme-btn-action w-full justify-center">
                    <x-mary-icon name="o-plus" class="w-4 h-4" /> <span>Yeni Ekle</span>
                </button>
                <x-copy-badge text=".theme-btn-action" class="text-[9px] text-slate-400" />
            </div>
            <div class="flex flex-col items-center gap-2">
                <button class="theme-btn-edit w-full justify-center">
                    <x-mary-icon name="o-pencil-square" class="w-4 h-4" /> <span>Düzenle</span>
                </button>
                <x-copy-badge text=".theme-btn-edit" class="text-[9px] text-slate-400" />
            </div>
            <div class="flex flex-col items-center gap-2 text-center">
                <button class="theme-btn-delete w-full justify-center">
                    <x-mary-icon name="o-trash" class="w-4 h-4" /> <span>Sil</span>
                </button>
                <x-copy-badge text=".theme-btn-delete" class="text-[9px] text-slate-400" />
            </div>
            <div class="flex flex-col items-center gap-2">
                <button class="theme-btn-cancel w-full justify-center">
                    <span>İptal</span>
                </button>
                <x-copy-badge text=".theme-btn-cancel" class="text-[9px] text-slate-400" />
            </div>
            <div class="flex flex-col items-center gap-2 text-center">
                <div class="h-10 flex items-center justify-center">
                    <a href="#" class="text-sm font-semibold underline"
                        style="color: {{ $action_link_color }}">Detayları Gör</a>
                </div>
                <x-copy-badge text="--action-link-color" class="text-[9px] text-slate-400" />
            </div>
        </div>

        {{-- Token List Section --}}
        <div class="p-4 bg-slate-50 rounded-lg border border-dashed border-slate-200">
            <h5 class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2 uppercase tracking-wider">
                <x-mary-icon name="o-code-bracket" class="w-4 h-4" />
                CSS & Tasarım Tokenleri
            </h5>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                <x-copy-badge text=".theme-btn-save"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text=".theme-btn-action"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text=".theme-btn-edit"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text=".theme-btn-delete"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text=".theme-btn-cancel"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text="--action-link-color"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>