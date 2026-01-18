{{--
🛡️ ZIRHLI BELGELEME KARTI (V12.2)
-------------------------------------------------------------------------
PARTIAL : Sayfa Başlığı (_edit-header.blade.php)
SORUMLULUK : Edit sayfasının üst kısmındaki başlık, açıklama ve aksiyon butonlarını yönetir.

BAĞIMLILIKLAR (Variables):
@var $template, $name

METODLAR (Actions):
- cancel(), delete(), save(), showSystemDeleteWarning()
-------------------------------------------------------------------------
--}}

{{-- Back Button --}}
<a href="{{ route('settings.mail-templates.index') }}"
    class="inline-flex items-center gap-2 text-[var(--color-text-base)] hover:text-[var(--color-text-heading)] mb-6 transition-colors">
    <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
    <span class="text-sm font-medium">Şablonlara Dön</span>
</a>

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-[var(--color-text-heading)]">
            {{ $template ? 'Düzenle: ' . $this->name : 'Yeni Mail Şablonu' }}
        </h1>
        <p class="text-sm opacity-60 mt-1">Müşterilere gönderilecek e-postalar için şablon oluşturun</p>
    </div>
    <div class="flex items-center gap-2">
        <button wire:click="cancel" class="theme-btn-cancel">İptal</button>

        @if($template)
            @if($template->is_system || $template->system_key)
                <button type="button" wire:click="showSystemDeleteWarning"
                    class="px-4 py-2 text-sm font-bold bg-gray-100 text-gray-400 border border-gray-200 rounded-lg cursor-not-allowed flex items-center gap-2"
                    title="Sistem şablonları silinemez">
                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                    <span>Sil</span>
                </button>
            @else
                <button type="button" wire:confirm="Bu şablonu silmek istediğinizden emin misiniz?" wire:click="delete"
                    class="px-4 py-2 text-sm font-bold bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 rounded-lg transition-colors flex items-center gap-2">
                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                    <span>Sil</span>
                </button>
            @endif
        @endif

        <button wire:click="save" class="theme-btn-save flex items-center gap-2">
            <x-mary-icon name="o-check" class="w-4 h-4" />
            <span>{{ $template ? 'Güncelle' : 'Kaydet ve Oluştur' }}</span>
        </button>
    </div>
</div>