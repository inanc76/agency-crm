<?php

use App\Models\Contact;
use App\Models\Customer;
use App\Models\MailTemplate;
use App\Models\Message;
use App\Models\Offer;
use App\Services\MailTemplateService;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new #[Layout('components.layouts.app', ['title' => 'Yeni Mesaj Oluştur'])]
    class extends Component
    {
        use Toast;

        public ?string $customer_id = null;

        public ?string $offer_id = null;

        public ?string $template_id = null;

        public string $message_type = 'EMAIL';

        public array $selected_contacts = [];

        public ?string $cc = null;

        public ?string $bcc = null;

        public function mount(): void
        {
            $this->customer_id = (string) request()->query('customer') ?: null;
            $this->offer_id = (string) request()->query('offer') ?: null;

            if ($this->offer_id && ! $this->customer_id) {
                $offer = Offer::find($this->offer_id);
                if ($offer) {
                    $this->customer_id = (string) $offer->customer_id;
                }
            }
        }

        public function toggleContact(string $id): void
        {
            if (in_array($id, $this->selected_contacts)) {
                $this->selected_contacts = array_values(array_diff($this->selected_contacts, [$id]));
            } else {
                $this->selected_contacts[] = $id;
            }
        }

        public function updatedCustomerId(): void
        {
            $this->offer_id = null;
            $this->selected_contacts = [];
        }

        public function customers()
        {
            return Customer::orderBy('name')->get(['id', 'name'])->toArray();
        }

        public function offers()
        {
            if (! $this->customer_id) {
                return [];
            }

            return Offer::where('customer_id', $this->customer_id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($o) => ['id' => $o->id, 'name' => $o->title])
                ->toArray();
        }

        public function mailTypes()
        {
            return \App\Models\ReferenceItem::where('category_key', 'MAIL_TYPE')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'key', 'display_label'])
                ->map(fn ($item) => ['id' => $item->key, 'name' => $item->display_label])
                ->toArray();
        }

        public function templates()
        {
            return MailTemplate::where('is_system', false)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->toArray();
        }

        public function contacts()
        {
            if (! $this->customer_id) {
                return collect();
            }

            return Contact::where('customer_id', $this->customer_id)->orderBy('name')->get();
        }

        public function cancel(): void
        {
            $this->redirect('/dashboard/customers?tab=messages', navigate: true);
        }

        public function createDraft(MailTemplateService $mailService): void
        {
            $this->validate([
                'customer_id' => 'required',
                'template_id' => 'required',
                'selected_contacts' => 'required|array|min:1',
            ]);

            $template = MailTemplate::find($this->template_id);

            foreach ($this->selected_contacts as $contactId) {
                $contact = Contact::find($contactId);
                if (! $contact) {
                    continue;
                }

                // Prepare variables
                $offer = $this->offer_id ? Offer::find($this->offer_id) : null;
                $variables = [
                    '{{name}}' => $contact->name,
                    '{{contact.name}}' => $contact->name,
                    '{{customer.name}}' => $contact->customer?->name,
                    '{{offer.download_link}}' => $offer?->tracking_token ? url('/offer/'.$offer->tracking_token) : '#',
                    '{{offer.number}}' => $offer?->number ?? '',
                    '{{offer.title}}' => $offer?->title ?? '',
                ];

                // Render template
                $rendered = $mailService->renderById($this->template_id, $variables);

                $subject = $rendered['subject'] ?: ($template->subject ?? 'Konu Yok');
                $body = $rendered['content'] ?: ($template->content ?? '');

                // Create message record as DRAFT
                Message::create([
                    'id' => Str::uuid()->toString(),
                    'customer_id' => $this->customer_id,
                    'offer_id' => $this->offer_id,
                    'mail_template_id' => $this->template_id,
                    'subject' => $subject,
                    'body' => $body,
                    'recipient_name' => $contact->name,
                    'recipient_email' => $contact->email,
                    'cc' => $this->cc,
                    'bcc' => $this->bcc,
                    'contact_id' => $contact->id,
                    'type' => $this->message_type,
                    'status' => 'DRAFT',
                    'sent_at' => null,
                ]);
            }

            $this->success('İşlem Başarılı', count($this->selected_contacts).' adet taslak mesaj oluşturuldu.');
            $this->redirect('/dashboard/customers?tab=messages', navigate: true);
        }
    }; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=messages" wire:navigate
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-6 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Mesajlar Listesi</span>
        </a>

        {{-- Header Section --}}
        <div class="flex items-start justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-skin-heading">Yeni Mesaj Oluştur</h1>
                <p class="text-sm text-skin-muted mt-1">
                    Müşteri için yeni bir mesaj hazırlayın
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button wire:click="cancel" class="theme-btn-cancel">
                    İptal
                </button>
                <button wire:click="createDraft" wire:loading.attr="disabled" class="theme-btn-save">
                    <span wire:loading class="loading loading-spinner loading-xs mr-2"></span>
                    <x-mary-icon name="o-document-plus" class="w-4 h-4" />
                    Taslak Oluştur ({{ count($selected_contacts) }} kişi)
                </button>
            </div>
        </div>

        {{-- Main Layout: 2 Column Grid --}}
        <div class="grid grid-cols-12 gap-6">
            {{-- Left Column (8/12) --}}
            <div class="col-span-8 space-y-6">
                {{-- Müşteri ve Teklif Seçimi --}}
                <div class="theme-card p-6 shadow-sm">
                    <h2 class="text-base font-bold text-skin-heading mb-4">Müşteri ve Teklif Seçimi</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-mary-select label="Müşteri *" wire:model.live="customer_id" :options="$this->customers()"
                            placeholder="Müşteri seçin" icon="o-building-office" />

                        <x-mary-select label="Teklif" wire:model.live="offer_id" :options="$this->offers()"
                            placeholder="Teklif seçin (opsiyonel)" icon="o-document-text" />
                    </div>
                </div>

                {{-- Şablon Seçimi --}}
                <div class="theme-card p-6 shadow-sm">
                    <h2 class="text-base font-bold text-skin-heading mb-4">Şablon Seçimi</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-mary-select label="Mesaj Tipi *" wire:model.live="message_type" :options="$this->mailTypes()"
                            placeholder="Mesaj tipi seçin" icon="o-chat-bubble-left-right" />

                        <x-mary-select label="Şablon *" wire:model.live="template_id" :options="$this->templates()"
                            placeholder="Şablon seçin" icon="o-document-text" />
                    </div>
                </div>
            </div>

            {{-- Right Column (4/12) - Gönderilecek Kişiler --}}
            <div class="col-span-4 space-y-6">
                <div class="theme-card p-6 shadow-sm sticky top-6">
                    <h2 class="text-base font-bold text-skin-heading mb-4">Gönderilecek Kişiler</h2>

                    @if($this->contacts() && count($this->contacts()) > 0)
                        <div class="space-y-2 max-h-[600px] overflow-y-auto pr-2">
                            @foreach($this->contacts() as $contact)
                                @php $isSelected = in_array((string) $contact->id, array_map('strval', $selected_contacts)); @endphp
                                <div wire:key="contact-{{ $contact->id }}" wire:click="toggleContact('{{ $contact->id }}')"
                                    class="flex items-center gap-3 p-3 rounded-lg border transition-all cursor-pointer {{ $isSelected ? 'border-indigo-400 bg-indigo-50 shadow-sm' : 'border-[var(--card-border)] bg-white hover:border-indigo-200' }}">
                                    <div class="flex-shrink-0">
                                        <input type="checkbox" value="{{ $contact->id }}" wire:model.live="selected_contacts"
                                            class="checkbox checkbox-sm checkbox-primary" @click.stop>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-skin-heading truncate">{{ $contact->name }}</p>
                                        <p class="text-[10px] text-skin-muted truncate">{{ $contact->email }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- CC & BCC Inputs --}}
                        <div class="mt-6 space-y-4 border-t border-[var(--card-border)] pt-4">
                            <x-mary-input label="CC (Virgül ile ayırın)" wire:model="cc"
                                placeholder="ornek1@mail.com, ornek2@mail.com" icon="o-envelope" />
                            <x-mary-input label="BCC (Virgül ile ayırın)" wire:model="bcc"
                                placeholder="hidden1@mail.com, hidden2@mail.com" icon="o-eye-slash" />
                        </div>

                        @if(count($selected_contacts) > 0)
                            <div class="mt-4 pt-4 border-t border-[var(--card-border)]">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-skin-muted">Seçili Kişi:</span>
                                    <span class="font-bold text-skin-heading">{{ count($selected_contacts) }}</span>
                                </div>
                            </div>
                        @endif
                    @else
                        <div
                            class="flex flex-col items-center justify-center py-12 text-skin-muted bg-[var(--card-bg)] rounded-lg border border-dashed border-[var(--card-border)]">
                            <x-mary-icon name="o-user-group" class="w-12 h-12 opacity-20 mb-3" />
                            <p class="text-xs italic">Müşteri seçildiğinde kişiler burada listelenir</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>