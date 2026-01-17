<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\MailTemplate;
use App\Models\Contact;
use App\Models\Message;
use App\Services\MailTemplateService;
use App\Mail\DynamicCustomerMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Mary\Traits\Toast;

new #[Layout('components.layouts.app', ['title' => 'Yeni Mesaj Oluştur'])]
    class extends Component {
    use Toast;

    public ?string $customer_id = null;
    public ?string $offer_id = null;
    public ?string $template_id = null;
    public array $selected_contacts = [];

    public function mount(): void
    {
        $this->customer_id = (string) request()->query('customer') ?: null;
        $this->offer_id = (string) request()->query('offer') ?: null;

        if ($this->offer_id && !$this->customer_id) {
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
        if (!$this->customer_id) {
            return [];
        }

        return Offer::where('customer_id', $this->customer_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($o) => ['id' => $o->id, 'name' => ($o->number ?? $o->title)])
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
        if (!$this->customer_id) {
            return collect();
        }

        return Contact::where('customer_id', $this->customer_id)->orderBy('name')->get();
    }

    public function cancel(): void
    {
        $this->redirect('/dashboard/customers?tab=messages', navigate: true);
    }

    public function send(MailTemplateService $mailService): void
    {
        $this->validate([
            'customer_id' => 'required',
            'template_id' => 'required',
            'selected_contacts' => 'required|array|min:1',
        ]);

        $template = MailTemplate::find($this->template_id);

        foreach ($this->selected_contacts as $contactId) {
            $contact = Contact::find($contactId);
            if (!$contact)
                continue;

            // Render template
            $rendered = $mailService->renderById($this->template_id, [
                '{{name}}' => $contact->name,
                '{{customer.name}}' => $contact->customer?->name,
            ]);

            $subject = $rendered['subject'] ?: ($template->subject ?? 'Konu Yok');
            $body = $rendered['content'] ?: ($template->content ?? '');

            // Create message record
            Message::create([
                'id' => Str::uuid()->toString(),
                'customer_id' => $this->customer_id,
                'offer_id' => $this->offer_id,
                'mail_template_id' => $this->template_id,
                'subject' => $subject,
                'body' => $body,
                'type' => 'EMAIL',
                'status' => 'SENT',
                'sent_at' => now(),
            ]);

            // Dispatch actual email
            Mail::to($contact->email)->send(new DynamicCustomerMail($subject, $body));
        }

        $this->success('İşlem Başarılı', count($this->selected_contacts) . ' kişiye mesaj başarıyla oluşturuldu ve gönderildi.');
        $this->redirect('/dashboard/customers?tab=messages', navigate: true);
    }
}; ?>

<div class="p-6 max-w-7xl mx-auto space-y-6">
    {{-- Back Button --}}
    <div>
        <a href="/dashboard/customers?tab=messages" wire:navigate
            class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Geri</span>
        </a>

        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Yeni Mesaj Oluştur</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Müşteri için yeni bir mesaj hazırlayın
                </p>
            </div>

            <div class="flex gap-3">
                <button wire:click="cancel" class="theme-btn-cancel">İptal</button>
                <button wire:click="send" wire:loading.attr="disabled" class="theme-btn-save">
                    <span wire:loading class="loading loading-spinner loading-xs mr-2"></span>
                    Mesaj Oluştur ({{ count($selected_contacts) }} kişi)
                </button>
            </div>
        </div>
    </div>

    {{-- Form Sections --}}
    <div class="grid grid-cols-1 gap-6">
        {{-- Section 1: Teklif Seçimi --}}
        <div class="rounded-xl border border-[#bfdbfe] bg-[#eff4ff] p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-6">
                <h2 class="text-sm font-medium text-slate-700 uppercase tracking-wider">Teklif Seçimi</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-mary-select label="Müşteri *" wire:model.live="customer_id" :options="$this->customers()"
                    placeholder="Müşteri seçin" icon="o-building-office" />

                <x-mary-select label="Teklif" wire:model.live="offer_id" :options="$this->offers()"
                    placeholder="Teklif seçin" icon="o-document-text" />
            </div>
        </div>

        {{-- Section 2: Şablon Seçimi --}}
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/30 p-6 shadow-sm">
            <h2 class="text-sm font-medium text-slate-700 uppercase tracking-wider mb-6">Şablon Seçimi</h2>
            <div class="max-w-xl">
                <x-mary-select label="Mail Şablonu *" wire:model.live="template_id" :options="$this->templates()"
                    placeholder="Mail şablonu seçin" icon="o-envelope" />
            </div>
        </div>

        {{-- Section 3: Gönderilecek Kişiler --}}
        <div class="rounded-xl border border-purple-200 bg-purple-50/20 p-6 shadow-sm">
            <h2 class="text-sm font-medium text-slate-700 uppercase tracking-wider mb-6">Gönderilecek Kişiler</h2>

            @if($this->contacts() && count($this->contacts()) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($this->contacts() as $contact)
                        @php $isSelected = in_array((string) $contact->id, array_map('strval', $selected_contacts)); @endphp
                        <div wire:key="contact-{{ $contact->id }}" wire:click="toggleContact('{{ $contact->id }}')"
                            class="flex items-center gap-4 p-4 rounded-xl border transition-all cursor-pointer {{ $isSelected ? 'border-rose-400 bg-rose-50 shadow-sm ring-1 ring-rose-400/20' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                            <div class="flex-shrink-0">
                                <input type="checkbox" value="{{ $contact->id }}" wire:model.live="selected_contacts"
                                    class="checkbox checkbox-sm checkbox-primary" @click.stop>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[13px] font-bold text-slate-900 truncate">{{ $contact->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ $contact->email }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div
                    class="flex flex-col items-center justify-center py-12 text-slate-400 bg-white/50 rounded-xl border border-dashed border-slate-200">
                    <x-mary-icon name="o-user-group" class="w-12 h-12 opacity-20 mb-3" />
                    <p class="text-sm italic">Müşteri seçildiğinde kişiler burada listelenir</p>
                </div>
            @endif
        </div>
    </div>
</div>