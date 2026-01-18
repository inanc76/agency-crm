<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Message;
use App\Models\Contact;
use App\Services\MailTemplateService;
use App\Mail\DynamicCustomerMail;
use Illuminate\Support\Facades\Mail;
use Mary\Traits\Toast;

new #[Layout('components.layouts.app', ['title' => 'Mesaj Detayı'])]
    class extends Component {
    use Toast;

    public Message $message;
    public string $renderedBody = '';

    public function mount(Message $message, MailTemplateService $mailService): void
    {
        $this->message = $message->load(['customer', 'offer', 'status_item', 'type_item']);

        // Get first contact for this customer
        $contact = Contact::where('customer_id', $this->message->customer_id)->first();

        // Prepare replacement variables
        $variables = [
            '{{name}}' => $contact?->name ?? 'Kullanıcı',
            '{{contact.name}}' => $contact?->name ?? 'Kullanıcı',
            '{{customer.name}}' => $this->message->customer?->name ?? '',
            '{{offer.download_link}}' => $this->message->offer?->download_page_id
                ? url('/offer/' . $this->message->offer->download_page_id)
                : '#',
            '{{offer.number}}' => $this->message->offer?->number ?? '',
            '{{offer.title}}' => $this->message->offer?->title ?? '',
        ];

        // Render message body with variables replaced
        if ($this->message->mail_template_id) {
            $rendered = $mailService->renderById($this->message->mail_template_id, $variables);
            $this->renderedBody = $rendered['content'] ?? $this->message->body;
        } else {
            // If no template, do simple string replacement
            $this->renderedBody = str_replace(
                array_keys($variables),
                array_values($variables),
                $this->message->body
            );
        }
    }

    public function delete(): void
    {
        $this->message->delete();
        $this->success('Başarılı', 'Mesaj silindi.');
        $this->redirect('/dashboard/customers?tab=messages', navigate: true);
    }

    public function sendMessage(): void
    {
        if ($this->message->status === 'SENT') {
            $this->error('Hata', 'Bu mesaj zaten gönderilmiş.');
            return;
        }

        // Get all contacts for this customer
        $contacts = Contact::where('customer_id', $this->message->customer_id)->get();

        if ($contacts->isEmpty()) {
            $this->error('Hata', 'Bu müşteriye ait kişi bulunamadı.');
            return;
        }

        // Send email to all contacts
        foreach ($contacts as $contact) {
            if ($contact->email) {
                Mail::to($contact->email)->send(new DynamicCustomerMail(
                    $this->message->subject,
                    $this->renderedBody
                ));
            }
        }

        // Update message status
        $this->message->update([
            'status' => 'SENT',
            'sent_at' => now(),
        ]);

        $this->success('Başarılı', 'Mesaj ' . $contacts->count() . ' kişiye gönderildi.');
        $this->redirect('/dashboard/customers?tab=messages', navigate: true);
    }
}; ?>

<div class="p-6 max-w-7xl mx-auto space-y-6">
    {{-- Navigation & Actions --}}
    <div class="flex items-start justify-between">
        <div>
            <a href="/dashboard/customers?tab=messages" wire:navigate
                class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 mb-4 transition-colors">
                <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
                <span class="text-sm font-medium">Mesaj Listesine Dön</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-900">{{ $message->subject }}</h1>
            <p class="text-sm text-slate-500 mt-1">İletişim Kaydı Detayı</p>
        </div>

        <div class="flex gap-3">
            @if($message->status !== 'SENT')
                <button wire:click="sendMessage" wire:loading.attr="disabled"
                    wire:confirm="Bu mesajı göndermek istediğinize emin misiniz?"
                    class="theme-btn-save flex items-center gap-2">
                    <span wire:loading class="loading loading-spinner loading-xs"></span>
                    <x-mary-icon name="o-paper-airplane" class="w-4 h-4" />
                    Mesaj Gönder
                </button>
            @endif
            <button wire:click="delete" wire:confirm="Bu mesaj kaydını silmek istediğinize emin misiniz?"
                class="theme-btn-delete flex items-center gap-2">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
                Kaydı Sil
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Left: Message Content (2/3) --}}
        <div class="md:col-span-2 space-y-6">
            <div class="theme-card p-8 shadow-sm min-h-[500px]">
                <div class="mail-content prose prose-sm max-w-none">
                    {!! $renderedBody !!}
                </div>
            </div>
        </div>

        {{-- Right: Sidebar Info (1/3) --}}
        <div class="space-y-6">
            {{-- Customer Card --}}
            <div class="theme-card p-6 shadow-sm">
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Müşteri Bilgisi</h2>
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-600 font-bold text-xl">
                        {{ mb_substr($message->customer->name ?? 'M', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 leading-tight">{{ $message->customer->name ?? '-' }}
                        </p>
                        <a href="/dashboard/customers/{{ $message->customer_id }}" wire:navigate
                            class="text-xs text-blue-600 hover:underline">Profiline Git</a>
                    </div>
                </div>
            </div>

            {{-- Metadata Card --}}
            <div class="theme-card p-6 shadow-sm space-y-4">
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Gönderim Detayları</h2>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Durum</label>
                    <div class="mt-1">
                        @php
                            $statusLabel = $message->status_item->display_label ?? $message->status ?? 'Bilinmiyor';
                            $statusClass = $message->status_item->metadata['color_class'] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                        @endphp
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Tarih</label>
                    <p class="text-sm font-medium text-slate-700 mt-0.5">
                        {{ $message->sent_at?->format('d.m.Y H:i') ?? '-' }}
                    </p>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Tür</label>
                    <div class="mt-1">
                        @php
                            $typeLabel = $message->type_item->display_label ?? $message->type ?? 'Bilinmiyor';
                            $typeClass = $message->type_item->metadata['color_class'] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                        @endphp
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $typeClass }}">
                            {{ $typeLabel }}
                        </span>
                    </div>
                </div>

                @if($message->offer_id)
                    <div class="pt-4 border-t border-slate-50">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">İlgili Teklif</label>
                        <a href="/dashboard/customers/offers/{{ $message->offer_id }}" wire:navigate
                            class="flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-700 mt-1 transition-colors">
                            <x-mary-icon name="o-document-text" class="w-4 h-4" />
                            {{ $message->offer->number ?? 'Teklife Git' }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>