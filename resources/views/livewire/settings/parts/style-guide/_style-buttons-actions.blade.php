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
                <span class="font-semibold text-slate-700">Buton & Aksiyon Parametreleri
                    Önizleme</span>
            </div>
            <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">Isolated
                Design System</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        {{--
        ┌─────────────────────────────────────────────────────────────────┐
        │ 📝 KULLANIM NOTU │
        └─────────────────────────────────────────────────────────────────┘

        Buton Sınıfları ve Kullanım Alanları:

        1. .theme-btn-save
        - Kullanım: Form kaydetme butonları
        - Örnek: <button class="theme-btn-save">Kaydet</button>
        - Modüller: Offer Create, Customer Edit, Settings

        2. .theme-btn-action
        - Kullanım: Yeni kayıt ekleme (primary action)
        - Örnek: <button class="theme-btn-action">Yeni Ekle</button>
        - Modüller: Customer List, Offer List, Service List

        3. .theme-btn-edit
        - Kullanım: Düzenleme butonları
        - Örnek: <button class="theme-btn-edit">Düzenle</button>
        - Modüller: Tablo satırları, detail sayfalar

        4. .theme-btn-delete
        - Kullanım: Silme butonları
        - Örnek: <button class="theme-btn-delete">Sil</button>
        - Modüller: Tablo satırları, modal onayları

        5. .theme-btn-cancel
        - Kullanım: İptal butonları
        - Örnek: <button class="theme-btn-cancel">İptal</button>
        - Modüller: Modal close, form reset

        6. --action-link-color
        - Kullanım: Detay linkleri
        - Örnek: <a href="#" style="color: var(--action-link-color)">Detaylar</a>
        - Modüller: Tablo satırları, card footers

        ⚠️ UYARI: Butonlarda inline style KULLANMAYIN!
        --}}
        <div class="p-6 bg-white rounded-xl border border-slate-100 grid grid-cols-2 md:grid-cols-3 gap-6">
            <div class="flex flex-col items-center gap-2">
                <button class="theme-btn-save w-full justify-center"
                    style="color: {{ $btn_save_text_color }} !important;">
                    <x-mary-icon name="o-check" class="w-4 h-4" /> <span>Kaydet</span>
                </button>
                <span class="text-[9px] font-mono text-slate-400">.theme-btn-save</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <button class="theme-btn-action w-full justify-center">
                    <x-mary-icon name="o-plus" class="w-4 h-4" /> <span>Yeni Ekle</span>
                </button>
                <span class="text-[9px] font-mono text-slate-400">.theme-btn-action</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <button class="theme-btn-edit w-full justify-center">
                    <x-mary-icon name="o-pencil-square" class="w-4 h-4" /> <span>Düzenle</span>
                </button>
                <span class="text-[9px] font-mono text-slate-400">.theme-btn-edit</span>
            </div>
            <div class="flex flex-col items-center gap-2 text-center">
                <button class="theme-btn-delete w-full justify-center">
                    <x-mary-icon name="o-trash" class="w-4 h-4" /> <span>Sil</span>
                </button>
                <span class="text-[9px] font-mono text-slate-400">.theme-btn-delete</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <button class="theme-btn-cancel w-full justify-center">
                    <span>İptal</span>
                </button>
                <span class="text-[9px] font-mono text-slate-400">.theme-btn-cancel</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <div class="h-10 flex items-center">
                    <a href="#" class="text-sm font-semibold underline"
                        style="color: {{ $action_link_color }}">Detayları Gör</a>
                </div>
                <span class="text-[9px] font-mono text-slate-400">--action-link-color</span>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>