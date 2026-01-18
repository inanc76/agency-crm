<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\MailTemplate;
use Mary\Traits\Toast;
use App\Livewire\Settings\MailTemplates\Traits\HasMailTemplateActions;

new #[Layout('components.layouts.app')]
    class extends Component {
    use Toast, HasMailTemplateActions;

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
}; ?>


<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    @include('livewire.settings.mail-templates.partials._editor-scripts')
    <div class="max-w-7xl mx-auto">
        @include('livewire.settings.mail-templates.partials._edit-header')

        <div class="grid grid-cols-12 gap-6">
            {{-- Main Form (8/12) --}}
            <div class="col-span-8 space-y-6">
                @include('livewire.settings.mail-templates.partials._template-form')
            </div>

            @include('livewire.settings.mail-templates.partials._template-modals')

            {{-- Sidebar (4/12) --}}
            <div class="col-span-4 space-y-6">
                @include('livewire.settings.mail-templates.partials._available-shortcodes')
            </div>
        </div>
    </div>

    {{-- Simple Toast for Copy --}}
    <div id="toast-copy"
        class="hidden fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded shadow-lg text-sm z-50">
        Kopyalandı!
    </div>
</div>