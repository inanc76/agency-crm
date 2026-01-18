<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\MailTemplate;
use Mary\Traits\Toast;

new #[Layout('components.layouts.app')]
    class extends Component {
    use Toast;

    public ?MailTemplate $template = null;

    // Form fields
    public $name = '';
    public $subject = '';
    public $content = '';
    public $is_system = false;
    public $htmlModal = false;
    public $tempHtml = '';
    public $testModal = false;
    public $testEmails = '';
    public $sender_provider = 'mailgun'; // Default to active provider

    // Variables for sidebar
    public $variables = [
        'MÜŞTERİ BİLGİLERİ' => [
            ['code' => '{{customer.name}}', 'desc' => 'Müşteri Adı'],
            ['code' => '{{customer.email}}', 'desc' => 'E-posta'],
            ['code' => '{{customer.phone}}', 'desc' => 'Telefon'],
            ['code' => '{{customer.address}}', 'desc' => 'Adres'],
            ['code' => '{{customer.tax_number}}', 'desc' => 'Vergi No'],
        ],
        'KİŞİ BİLGİLERİ' => [
            ['code' => '{{contact.name}}', 'desc' => 'Kişi Adı'],
            ['code' => '{{contact.email}}', 'desc' => 'E-posta'],
            ['code' => '{{contact.phone}}', 'desc' => 'Telefon'],
            ['code' => '{{contact.position}}', 'desc' => 'Pozisyon'],
        ],
        'TEKLİF BİLGİLERİ' => [
            ['code' => '{{offer.number}}', 'desc' => 'Teklif No'],
            ['code' => '{{offer.download_link}}', 'desc' => 'İndirme Linki'],
            ['code' => '{{offer.valid_until}}', 'desc' => 'Geçerlilik Tarihi'],
            ['code' => '{{offer.total_amount}}', 'desc' => 'Toplam Tutar'],
            ['code' => '{{offer.status}}', 'desc' => 'Teklif Durumu'],
        ],
        'ŞİRKET BİLGİLERİ' => [
            ['code' => '{{company.name}}', 'desc' => 'Şirket Adı'],
            ['code' => '{{company.email}}', 'desc' => 'E-posta'],
            ['code' => '{{company.website}}', 'desc' => 'Web Sitesi'],
        ],
        'SİSTEM BİLGİLERİ' => [
            ['code' => '{{system.current_date}}', 'desc' => 'Bugünün Tarihi'],
            ['code' => '{{system.url}}', 'desc' => 'Sistem URL'],
        ]
    ];

    public function mount(?MailTemplate $template = null)
    {
        if ($template && $template->exists) {
            $this->template = $template;
            $this->name = $template->name;
            $this->subject = $template->subject;
            $this->content = $template->content;
            $this->is_system = $template->is_system;
        }
        
        // Set default sender provider to active one
        $activeMail = \App\Models\MailSetting::where('is_active', true)->first();
        if ($activeMail) {
            $this->sender_provider = $activeMail->provider;
        }
    }

    public function showSystemDeleteWarning()
    {
        $this->error('Hata', 'Sistem şablonları silinemez.');
    }

    public function delete()
    {
        if (!$this->template) {
            return;
        }

        if ($this->template->is_system || $this->template->system_key) {
            $this->error('Hata', 'Sistem şablonları silinemez.');
            return;
        }

        $this->template->delete();
        $this->success('Başarılı', 'Şablon silindi.');
        return $this->redirect(route('settings.mail-templates.index'), navigate: true);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $data = [
            'name' => $this->name,
            'subject' => $this->subject,
            'content' => $this->content,
            'updated_by' => auth()->id(),
        ];

        if ($this->template) {
            $this->template->update($data);
            $this->success('Başarılı', 'Şablon güncellendi.');
        } else {
            $data['created_by'] = auth()->id();
            $data['is_system'] = false; // New templates are always custom
            MailTemplate::create($data);
            $this->success('Başarılı', 'Şablon oluşturuldu.');
            return $this->redirect(route('settings.mail-templates.index'), navigate: true);
        }
    }

    public function openHtmlModal()
    {
        $this->tempHtml = $this->content;
        $this->htmlModal = true;
    }

    public function saveHtmlModal()
    {
        $this->content = $this->tempHtml;
        $this->htmlModal = false;
        // The WYSIWYG should pick up the change
        $this->dispatch('content-updated', content: $this->content);
    }

    public function sendTestEmail()
    {
        if (!$this->template) {
            $this->error('Hata', 'Lütfen önce şablonu kaydedin.');
            return;
        }

        $this->validate([
            'testEmails' => 'required|string',
        ]);

        $emails = array_map('trim', explode(',', $this->testEmails));
        $validEmails = array_filter($emails, fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL));

        if (empty($validEmails)) {
            $this->error('Hata', 'Lütfen geçerli en az bir e-posta adresi girin.');
            return;
        }

        try {
            // Simple dummy data for testing variable replacement
            $dummyData = [
                '{{customer.name}}' => 'Ahmet Yılmaz',
                '{{customer.email}}' => 'ahmet@example.com',
                '{{customer.phone}}' => '0555 123 45 67',
                '{{contact.name}}' => 'Mehmet Demir',
                '{{offer.number}}' => 'TK-2024-001',
                '{{offer.download_link}}' => url('/offer/e87844eb-b44f-4835-9aaa-729addc6e2b1'),
                '{{offer.total_amount}}' => '1.500,00 ₺',
                '{{company.name}}' => config('app.name'),
                '{{system.current_date}}' => now()->format('d.m.Y'),
                '{{system.url}}' => config('app.url'),
            ];

            $finalSubject = strtr($this->subject, $dummyData);
            $finalContent = strtr($this->content, $dummyData);

            foreach ($validEmails as $recipient) {
                \Illuminate\Support\Facades\Mail::html($finalContent, function ($message) use ($recipient, $finalSubject) {
                    $message->to($recipient)
                        ->subject('[TEST] ' . $finalSubject);
                });
            }

            $this->testModal = false;
            $this->success('Başarılı', 'Test e-postası gönderildi.');
        } catch (\Exception $e) {
            $this->error('Hata', 'E-posta gönderilemedi: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return $this->redirect(route('settings.mail-templates.index'), navigate: true);
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
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
                        <button type="button" 
                            wire:click="showSystemDeleteWarning"
                            class="px-4 py-2 text-sm font-bold bg-gray-100 text-gray-400 border border-gray-200 rounded-lg cursor-not-allowed flex items-center gap-2"
                            title="Sistem şablonları silinemez">
                            <x-mary-icon name="o-trash" class="w-4 h-4" />
                            <span>Sil</span>
                        </button>
                    @else
                        <button type="button" 
                            wire:confirm="Bu şablonu silmek istediğinizden emin misiniz?"
                            wire:click="delete" 
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

        <div class="grid grid-cols-12 gap-6">
            {{-- Main Form (8/12) --}}
            <div class="col-span-8 space-y-6">
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
                        <x-mary-input wire:model="name" label="Şablon Adı *"
                            placeholder="Örn: Teklif Gönderim Şablonu" />

                        @php
                            $mailSettings = \App\Models\MailSetting::where('is_active', true)->first();
                            $senderOptions = [];
                            
                            if ($mailSettings) {
                                // Add Mailgun option if configured
                                if ($mailSettings->mailgun_from_name && $mailSettings->mailgun_from_email) {
                                    $senderOptions[] = [
                                        'id' => 'mailgun',
                                        'name' => '📧 ' . $mailSettings->mailgun_from_name . ' (' . $mailSettings->mailgun_from_email . ')'
                                    ];
                                }
                                
                                // Add SMTP option if configured
                                if ($mailSettings->smtp_from_name && $mailSettings->smtp_from_email) {
                                    $senderOptions[] = [
                                        'id' => 'smtp',
                                        'name' => '✉️ ' . $mailSettings->smtp_from_name . ' (' . $mailSettings->smtp_from_email . ')'
                                    ];
                                }
                            }
                        @endphp
                        
                        @if(count($senderOptions) > 0)
                            <x-mary-select 
                                wire:model="sender_provider" 
                                label="Gönderen" 
                                :options="$senderOptions" 
                                option-value="id"
                                option-label="name"
                                hint="Mail ayarlarından yapılandırılır."
                            />
                        @else
                            <div>
                                <label class="block text-sm font-medium mb-1 text-[var(--color-text-heading)]">Gönderen</label>
                                <div class="px-3 py-2 bg-gray-50 border border-[var(--input-border)] rounded-lg text-sm text-red-600">
                                    Yapılandırılmamış - Lütfen mail ayarlarını kontrol edin
                                </div>
                            </div>
                        @endif

                        <div>
                            <x-mary-input wire:model="subject" label="E-posta Konusu (Subject) *"
                                :placeholder="'Örn: {{customer.name}} - Size Özel Bir Teklifimiz Var'"
                                :hint="'Değişkenleri {{degisken_adi}} formatında kullanabilirsiniz.'" />
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
                            
                            <div 
                                class="theme-card shadow-sm overflow-hidden border border-[var(--input-border)] rounded-lg bg-white" 
                                wire:ignore
                                x-data="{ 
                                    content: @entangle('content'),
                                    quill: null,
                                    init() {
                                        this.$nextTick(() => {
                                            // Cleanup any previous Quill toolbars/containers that might linger
                                            const existingToolbars = this.$el.querySelectorAll('.ql-toolbar');
                                            existingToolbars.forEach(el => el.remove());
                                            
                                            const editorContainer = document.getElementById('quill-editor');
                                            if (editorContainer) {
                                                editorContainer.innerHTML = '';
                                            }

                                            this.quill = new Quill('#quill-editor', {
                                                theme: 'snow',
                                                modules: {
                                                    toolbar: [
                                                        [{ 'header': [1, 2, 3, false] }],
                                                        ['bold', 'italic', 'underline', 'strike'],
                                                        ['link', 'blockquote', 'code-block', 'image'],
                                                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                                        [{ 'color': [] }, { 'background': [] }],
                                                        ['clean']
                                                    ]
                                                }
                                            });

                                            if (this.content) {
                                                this.quill.clipboard.dangerouslyPasteHTML(this.content);
                                            }

                                            this.quill.on('text-change', () => {
                                                this.content = this.quill.root.innerHTML;
                                            });

                                            Livewire.on('content-updated', (event) => {
                                                if (this.quill.root.innerHTML !== event.content) {
                                                    this.quill.clipboard.dangerouslyPasteHTML(event.content);
                                                }
                                            });
                                        });
                                    }
                                }"
                            >
                                {{-- Quill Reader implementation - Open Source & Free --}}
                                <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                                <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
                                
                                <style>
                                    .ql-toolbar.ql-snow {
                                        background-color: #f8fafc;
                                        border: none !important;
                                        border-bottom: 1px solid var(--input-border) !important;
                                    }
                                    .ql-container.ql-snow {
                                        border: none !important;
                                        background-color: white;
                                    }
                                    .ql-editor {
                                        font-family: 'Inter', sans-serif;
                                        font-size: 14px;
                                        line-height: 1.6;
                                        color: #1e293b;
                                        background-color: white !important;
                                        min-height: 700px;
                                    }
                                </style>
                                
                                <div id="quill-editor" style="height: 700px;" wire:key="quill-editor-{{ $template?->id ?? 'new' }}"></div>
                            </div>
                            <p class="text-xs text-[var(--color-text-muted)] mt-2 italic">
                                * Değişken Rehberi'ndeki kodları içeriğe ekleyebilirsiniz.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HTML Source Modal --}}
            <x-mary-modal wire:model="htmlModal" title="HTML Kaynak Kodu" class="backdrop-blur">
                <div class="space-y-4">
                    <p class="text-xs text-slate-500">Şablonun HTML kaynak kodunu buradan düzenleyebilirsiniz.</p>
                    <textarea 
                        wire:model.live="tempHtml" 
                        class="w-full h-[500px] p-4 font-mono text-sm bg-slate-900 text-green-400 rounded-lg focus:ring-2 focus:ring-primary-500 border-none resize-none"
                    ></textarea>
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
                        <x-mary-input 
                            wire:model="testEmails" 
                            label="Alıcı E-posta Adresleri" 
                            placeholder="mail1@example.com, mail2@example.com"
                            hint="Birden fazla adresi virgülle ayırarak girebilirsiniz."
                        />
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

            {{-- Sidebar (4/12) --}}
            <div class="col-span-4 space-y-6">
                <div class="theme-card p-4 shadow-sm h-full max-h-[calc(100vh-100px)] overflow-y-auto sticky top-6">
                    <h2 class="text-base font-bold mb-4 text-[var(--color-text-heading)]">Değişken Rehberi</h2>
                    <p class="text-xs text-[var(--color-text-muted)] mb-6">Değişkenleri tıklayarak veya buton ile kopyalayabilirsiniz.</p>

                    <div class="space-y-3" x-data="{ openCategory: 'MÜŞTERİ BİLGİLERİ' }">
                        @foreach($variables as $category => $vars)
                            <div class="border border-[var(--card-border)] rounded-lg overflow-hidden bg-[var(--color-background)]">
                                {{-- Accordion Header --}}
                                <button 
                                    @click="openCategory = (openCategory === '{{ $category }}' ? null : '{{ $category }}')"
                                    class="w-full flex items-center justify-between p-3 text-left hover:bg-[var(--dropdown-hover-bg)] transition-colors bg-[var(--card-bg)]"
                                >
                                    <span class="text-xs font-bold uppercase text-[var(--color-text-muted)] tracking-wider">{{ $category }}</span>
                                    <x-mary-icon 
                                        name="o-chevron-down" 
                                        class="w-3 h-3 transition-transform duration-200" 
                                        ::class="openCategory === '{{ $category }}' ? 'rotate-180' : ''" 
                                    />
                                </button>

                                {{-- Accordion Content --}}
                                <div 
                                    x-show="openCategory === '{{ $category }}'" 
                                    x-collapse
                                    class="p-2 space-y-1 bg-[var(--card-bg)] border-t border-[var(--card-border)]"
                                >
                                    @foreach($vars as $var)
                                        <div 
                                            class="group flex items-center justify-between p-2 rounded border border-transparent bg-[var(--color-background)]"
                                            x-data="{ copied: false }"
                                        >
                                            <div class="flex-1 min-w-0 mr-2">
                                                <code class="text-[11px] font-mono text-[var(--primary-color)] block truncate leading-tight">{{ $var['code'] }}</code>
                                                <span class="text-[10px] text-[var(--color-text-base)] opacity-60 truncate block">{{ $var['desc'] }}</span>
                                            </div>
                                            
                                            <button 
                                                type="button"
                                                class="flex-shrink-0 p-1.5 rounded hover:bg-[var(--card-bg)] transition-colors border border-transparent hover:border-[var(--card-border)] cursor-pointer"
                                                :class="copied ? 'text-green-600' : 'text-[var(--color-text-muted)] hover:text-[var(--primary-color)]'"
                                                @click="
                                                    navigator.clipboard.writeText('{{ $var['code'] }}'); 
                                                    copied = true; 
                                                    setTimeout(() => copied = false, 2000);
                                                "
                                                title="Kopyala"
                                            >
                                                <template x-if="!copied">
                                                    <x-mary-icon name="o-document-duplicate" class="w-3.5 h-3.5" />
                                                </template>
                                                <template x-if="copied">
                                                    <x-mary-icon name="o-check" class="w-3.5 h-3.5" />
                                                </template>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Simple Toast for Copy --}}
    <div id="toast-copy"
        class="hidden fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded shadow-lg text-sm z-50">
        Kopyalandı!
    </div>
</div>