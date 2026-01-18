{{--
🛡️ ZIRHLI BELGELEME KARTI (V12.2)
-------------------------------------------------------------------------
PARTIAL : Şablon Formu (_template-form.blade.php)
SORUMLULUK : Mail şablonu bilgilerini (Ad, Konu, Gönderen) ve içerik editörünü yönetir.

ZIRH PROTOKOLÜ (SAFE EDITOR):
- wire:ignore (DOM stability)
- @entangle sync (Reactivity)
- External Script Binding (Decoupling)
--}}

<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-[var(--color-text-heading)]">Şablon Bilgileri</h2>
        <button type="button" wire:click="$set('testModal', true)"
            class="px-3 py-1.5 text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200 rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer">
            <x-mary-icon name="o-paper-airplane" class="w-3.5 h-3.5" />
            <span>Test Mesajı Gönder</span>
        </button>
    </div>

    <div class="space-y-4">
        <x-mary-input wire:model="name" label="Şablon Adı *" placeholder="Örn: Teklif Gönderim Şablonu" />

        @php
            $mailSettings = \App\Models\MailSetting::where('is_active', true)->first();
            $senderOptions = [];

            if ($mailSettings) {
                if ($mailSettings->mailgun_from_name && $mailSettings->mailgun_from_email) {
                    $senderOptions[] = [
                        'id' => 'mailgun',
                        'name' => '📧 ' . $mailSettings->mailgun_from_name . ' (' . $mailSettings->mailgun_from_email . ')'
                    ];
                }

                if ($mailSettings->smtp_from_name && $mailSettings->smtp_from_email) {
                    $senderOptions[] = [
                        'id' => 'smtp',
                        'name' => '✉️ ' . $mailSettings->smtp_from_name . ' (' . $mailSettings->smtp_from_email . ')'
                    ];
                }
            }
        @endphp

        @if(count($senderOptions) > 0)
            <x-mary-select wire:model="sender_provider" label="Gönderen" :options="$senderOptions" option-value="id"
                option-label="name" hint="Mail ayarlarından yapılandırılır." />
        @else
            <div>
                <label class="block text-sm font-medium mb-1 text-[var(--color-text-heading)]">Gönderen</label>
                <div class="px-3 py-2 bg-gray-50 border border-[var(--input-border)] rounded-lg text-sm text-red-600">
                    Yapılandırılmamış - Lütfen mail ayarlarını kontrol edin
                </div>
            </div>
        @endif

        <div>
            <x-mary-input wire:model="subject" label="E-posta Konusu (Subject) *" :placeholder="'Örn: {{customer.name}} - Size Özel Bir Teklifimiz Var'" :hint="'Değişkenleri {{degisken_adi}} formatında kullanabilirsiniz.'" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-[var(--color-text-heading)]">Şablon İçeriği *</label>
                <button type="button" wire:click="openHtmlModal"
                    class="px-3 py-1 text-xs font-bold bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded transition-colors flex items-center gap-1">
                    <x-mary-icon name="o-code-bracket" class="w-3.5 h-3.5" />
                    <span>HTML</span>
                </button>
            </div>

            <div class="theme-card shadow-sm overflow-hidden border border-[var(--input-border)] rounded-lg bg-white"
                wire:ignore x-data="{ 
                    content: @entangle('content'),
                    quill: null,
                    init() {
                        this.quill = initializeQuillEditor(
                            this.$el, 
                            this.content, 
                            (newContent) => { this.content = newContent; },
                            (quillInstance) => {
                                Livewire.on('content-updated', (event) => {
                                    if (quillInstance.root.innerHTML !== event.content) {
                                        quillInstance.clipboard.dangerouslyPasteHTML(event.content);
                                    }
                                });
                            }
                        );
                    }
                }">
                <div id="quill-editor" style="height: 700px;" wire:key="quill-editor-{{ $template?->id ?? 'new' }}">
                </div>
            </div>
            <p class="text-xs text-[var(--color-text-muted)] mt-2 italic">
                * Değişken Rehberi'ndeki kodları içeriğe ekleyebilirsiniz.
            </p>
        </div>
    </div>
</div>