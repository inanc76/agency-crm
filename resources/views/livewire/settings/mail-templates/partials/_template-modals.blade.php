{{--
🛡️ ZIRHLI BELGELEME KARTI (V12.2)
-------------------------------------------------------------------------
PARTIAL : Şablon Modalları (_template-modals.blade.php)
SORUMLULUK : HTML düzenleme ve Test Maili gönderim pencerelerini yönetir.

BAĞIMLILIKLAR (Variables):
@var $htmlModal, $tempHtml, $testModal, $testEmails

METODLAR (Actions):
- saveHtmlModal(), sendTestEmail()
-------------------------------------------------------------------------
--}}

{{-- HTML Source Modal --}}
<x-mary-modal wire:model="htmlModal" title="HTML Kaynak Kodu" class="backdrop-blur">
    <div class="space-y-4">
        <p class="text-xs text-slate-500">Şablonun HTML kaynak kodunu buradan düzenleyebilirsiniz.</p>
        <textarea wire:model.live="tempHtml"
            class="w-full h-[500px] p-4 font-mono text-sm bg-slate-900 text-green-400 rounded-lg focus:ring-2 focus:ring-primary-500 border-none resize-none"></textarea>
    </div>

    <x-slot:actions>
        <button wire:click="$set('htmlModal', false)" class="theme-btn-cancel">Vazgeç</button>
        <button wire:click="saveHtmlModal" class="theme-btn-save">Kaydet</button>
    </x-slot:actions>
</x-mary-modal>

{{-- Test Message Modal --}}
<x-mary-modal wire:model="testModal" title="Test Mesajı Gönder" class="backdrop-blur">
    <div class="space-y-4">
        <x-mary-alert icon="o-information-circle" class="bg-blue-50 border-blue-100 text-blue-700 text-xs">
            Önce şablon kayıt edilmelidir. Test mesajında değişkenler örnek verilerle doldurulacaktır.
        </x-mary-alert>

        <div class="space-y-2">
            <x-mary-input wire:model="testEmails" label="Alıcı E-posta Adresleri"
                placeholder="mail1@example.com, mail2@example.com"
                hint="Birden fazla adresi virgülle ayırarak girebilirsiniz." />
        </div>
    </div>

    <x-slot:actions>
        <button wire:click="$set('testModal', false)" class="theme-btn-cancel">Vazgeç</button>
        <button wire:click="sendTestEmail" class="theme-btn-save flex items-center gap-2">
            <x-mary-icon name="o-paper-airplane" class="w-4 h-4" />
            <span>Gönder</span>
        </button>
    </x-slot:actions>
</x-mary-modal>