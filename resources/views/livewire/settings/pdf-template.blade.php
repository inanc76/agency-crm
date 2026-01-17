{{--
🛡️ ZIRHLI BELGELEME KARTI (V12.2)
-------------------------------------------------------------------------
MODÜL : Settings / PDF Template
SORUMLULUK : PDF şablonunun genel yapılandırmasını (Header, Footer, Content) yönetmek.
MİMARİ : Monolitik yapıdan Partial tabanlı yapıya geçiş (Mission Lambda).

YAPI HİYERARŞİSİ:
1. _header.blade.php : Logo yönetimi, header arkaplan ve metin renkleri.
2. _content.blade.php : Ana font ailesi, birincil/ikincil renkler ve tablo stilleri.
3. _footer.blade.php : Footer metni ve yasal uyarı alanları.

VERİ AKIŞI:
- Veriler `PanelSettingRepository` üzerinden çekilir.
- `pdf_` prefix'li değişkenler Livewire state'inde tutulur.
- `@include` direktifleri ile alt bileşenlere (partials) veri aktarılır (Livewire scope sayesinde).

⚠️ MİMARIN NOTU:
Bu dosya sadece bir "Orkestratör" görevi görür. Layout ve mantık detayları
partial dosyalarına dağıtılmıştır. Buraya yeni bir özellik eklemeden önce
ilgili partial dosyasını kontrol ediniz.
-------------------------------------------------------------------------
--}}
<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Livewire\Settings\Traits\HasPdfTemplateSettings;

new #[Layout('components.layouts.app', ['title' => 'Teklif Şablonu'])] class extends Component {
    use HasPdfTemplateSettings;
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto pb-20">
        {{-- Back Button & Page Title --}}
        {{-- Back Button --}}
        <a href="{{ route('settings.index') }}"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-sm font-medium">Geri</span>
        </a>

        {{-- Page Title --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Teklif Şablonu</h1>
            <p class="text-sm text-slate-500 mt-1">Teklif PDF şablonunu ve ayarlarını özelleştirin.</p>
        </div>

        {{-- Main Settings Card --}}
        <div
            class="theme-card p-6 shadow-sm border border-[var(--card-border)] rounded-[var(--card-radius)] bg-[var(--card-bg)]">

            {{-- Card Header --}}
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
                <h2 class="text-lg font-medium text-skin-heading flex items-center gap-2">
                    <x-mary-icon name="o-document-text" class="w-5 h-5 opacity-70" />
                    PDF Görünüm Ayarları
                </h2>
                <x-mary-button label="Kaydet" icon="o-check" class="btn-sm"
                    style="background-color: var(--btn-save-bg) !important; color: var(--btn-save-text) !important; border-color: var(--btn-save-border) !important;"
                    wire:click="save" spinner="save" />
            </div>

            {{-- Accordions --}}
            <x-mary-accordion wire:model="group" separator>
                {{-- 1. Header Ayarları --}}
                @include('livewire.settings.partials.pdf._header')

                {{-- 2. İçerik & Renkler --}}
                {{-- 2. İçerik & Renkler --}}
                @include('livewire.settings.partials.pdf._content')

                {{-- 3. Footer Ayarları --}}
                {{-- 3. Footer Ayarları --}}
                @include('livewire.settings.partials.pdf._footer')
            </x-mary-accordion>

        </div>

        {{-- Download Page Settings Card --}}
        @include('livewire.settings.partials.pdf._download-settings')
    </div>
</div>