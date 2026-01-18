<?php
/**
 * 🛡️ ZIRHLI BELGELEME KARTI (V12.2)
 * -------------------------------------------------------------------------
 * COMPONENT   : ContactForm (Orchestra Shell)
 * SORUMLULUK  : Kontak kişisi ekleme/düzenleme formu için ViewModel görevi görür.
 *               Trait üzerindeki CRUD aksiyonlarını yönetir.
 *
 * BAĞIMLILIKLAR:
 * - App\Livewire\Customers\Contacts\Traits\HasContactActions
 * - Mary\Traits\Toast
 * -------------------------------------------------------------------------
 */
use App\Livewire\Customers\Contacts\Traits\HasContactActions;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Mary\Traits\Toast;
use App\Models\Customer;
use App\Models\ReferenceItem;

new 
#[Layout('components.layouts.app')]
class extends Component
{
    use HasContactActions, Toast;

    // --- Kontak Verileri (State Management) ---
    public string $customer_id = '';
    public string $name = '';
    public string $status = 'WORKING';
    public string $gender = '';
    public string $position = '';
    
    public array $emails = [''];
    public array $phones = [['number' => '', 'extension' => '']];
    public ?string $birth_date = null;
    public array $social_profiles = [['name' => '', 'url' => '']];

    // --- UI ve Sistem Durumu ---
    public bool $isViewMode = false;
    public ?string $contactId = null;
    public string $activeTab = 'info';

    // --- Referans Verileri (ReferenceData) ---
    public array $customers = [];
    public array $contactStatuses = [];
    public array $genders = [];
    public $relatedMessages = [];
    public int $messageCount = 0;
    public int $noteCount = 0;

    /**
     * Bileşen yaşam döngüsü başlangıcı.
     * Referans dataları hazırlar ve varsa mevcut kontağı yükler.
     */
    public function mount(?string $contact = null): void
    {
        // Müşteri listesini yükle (Arama/Seçim için)
        $this->customers = Customer::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        // Sistemdeki İletişim Durum kodlarını yükle
        $this->contactStatuses = ReferenceItem::where('category_key', 'CONTACT_STATUS')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key', 'metadata'])
            ->map(fn ($i) => ['id' => $i->id, 'display_label' => $i->display_label, 'key' => $i->key])
            ->toArray();

        // Cinsiyet tanımlarını yükle (Fallback statik matris içerir)
        $this->genders = ReferenceItem::where('category_key', 'GENDER')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'display_label', 'key'])
            ->map(fn ($i) => ['id' => $i->key, 'name' => $i->display_label])
            ->toArray();

        if (empty($this->genders)) {
            $this->genders = [
                ['id' => 'male', 'name' => 'Erkek'],
                ['id' => 'female', 'name' => 'Kadın'],
                ['id' => 'other', 'name' => 'Diğer'],
            ];
        }

        if ($contact) {
            $this->contactId = $contact;
            $this->loadContactData();
            $this->activeTab = request()->query('tab', 'info');
        } else {
            // Query string ile gelen müşteri verisi varsa yakala
            $customerId = request()->query('customer');
            if ($customerId && collect($this->customers)->firstWhere('id', $customerId)) {
                $this->customer_id = $customerId;
            }
            if (! empty($this->contactStatuses)) {
                $this->status = $this->contactStatuses[0]['key'];
            }
        }
    }
}; ?>

<div>
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=contacts"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Kişi Listesi</span>
        </a>

        {{-- Header Section --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
                    @if($isViewMode)
                        {{ $name }}
                    @elseif($contactId)
                        Düzenle: {{ $name }}
                    @else
                        Yeni Kişi Ekle
                    @endif
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($isViewMode)
                        <span class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--badge-bg)] text-[var(--badge-text)] border border-[var(--badge-border)]">Kişi</span>
                        <span class="text-[11px] font-mono text-[var(--color-text-muted)]">ID: {{ $contactId }}</span>
                    @else
                        <p class="text-sm opacity-60">Yeni kişi bilgilerini girin</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($isViewMode)
                    <button type="button" wire:click="delete" wire:confirm="Bu kişiyi silmek istediğinize emin misiniz?"
                        wire:key="btn-delete-{{ $contactId }}"
                        class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                        Sil
                    </button>
                    <button type="button" wire:click="toggleEditMode" wire:key="btn-edit-{{ $contactId }}"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                        Düzenle
                    </button>
                @else
                    <button type="button" wire:click="cancel" wire:key="btn-cancel-{{ $contactId ?: 'new' }}"
                        class="theme-btn-cancel">
                        İptal
                    </button>
                    <button type="button" wire:click="save" wire:loading.attr="disabled"
                        wire:key="btn-save-{{ $contactId ?: 'new' }}" class="theme-btn-save">
                        <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                        <x-mary-icon name="o-check" class="w-4 h-4" />
                        @if($contactId) Güncelle @else Kaydet @endif
                    </button>
                @endif
            </div>
        </div>

        {{-- Tab Navigation --}}
        @if($isViewMode)
            <div class="flex items-center border-b border-[var(--card-border)] mb-8 overflow-x-auto scrollbar-hide">
                <button wire:click="$set('activeTab', 'info')" 
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'info' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Kişi Bilgileri
                </button>
                <button wire:click="$set('activeTab', 'messages')" 
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'messages' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Mesajlar ({{ $messageCount }})
                </button>
                <button wire:click="$set('activeTab', 'notes')" 
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'notes' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Notlar ({{ $noteCount }})
                </button>
            </div>
        @else
            <div class="mb-8"></div>
        @endif

        {{-- Main Layout --}}
        <div>
            {{-- Content --}}
            <div>
                @if($activeTab === 'info')
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-8 space-y-6">
                            @include('livewire.modals.parts.contact._personal-info', [
                                'name' => $name,
                                'customer_id' => $customer_id,
                                'status' => $status,
                                'gender' => $gender,
                                'position' => $position,
                                'isViewMode' => $isViewMode,
                                'customers' => $customers,
                                'genders' => $genders
                            ])

                            @include('livewire.modals.parts.contact._communication', [
                                'emails' => $emails,
                                'phones' => $phones,
                                'isViewMode' => $isViewMode
                            ])

                            @include('livewire.modals.parts.contact._social-profiles', [
                                'social_profiles' => $social_profiles,
                                'isViewMode' => $isViewMode
                            ])

                            @include('livewire.modals.parts.contact._other-details', [
                                'birth_date' => $birth_date,
                                'isViewMode' => $isViewMode
                            ])
                        </div>
                        <div class="col-span-4 space-y-6">
                            {{-- Profile Photo Card --}}
                            @php
                                $contact = $contactId ? \App\Models\Contact::find($contactId) : null;
                                $gravatarUrl = $contact && $contact->email ? $contact->getGravatarUrl(256) : '';
                            @endphp
                            @if($contactId)
                                <div class="theme-card p-6 shadow-sm sticky top-6">
                                    <h2 class="text-base font-bold mb-4 text-center text-skin-heading">Profil Fotoğrafı</h2>
                                    
                                    <div class="flex flex-col items-center">
                                        {{-- Avatar Preview --}}
                                        <div class="w-32 h-32 rounded-full border-4 border-[var(--card-bg)] shadow-md flex items-center justify-center mb-4 overflow-hidden relative group"
                                            style="background-color: var(--table-avatar-bg); color: var(--table-avatar-text);">
                                            @if($contact && $contact->email && $gravatarUrl)
                                                <img src="{{ $gravatarUrl }}" 
                                                     alt="{{ $name }}"
                                                     class="w-full h-full object-cover rounded-full"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            @endif
                                            <div class="w-full h-full flex items-center justify-center text-2xl font-semibold" 
                                                 style="{{ $contact && $contact->email && $gravatarUrl ? 'display: none;' : 'display: flex;' }}">
                                                {{ $contact ? $contact->initials() : '' }}
                                            </div>
                                        </div>
                                        
                                        @if($contact && $contact->email)
                                            <p class="text-xs text-center opacity-60 text-skin-base">
                                                Gravatar: {{ $contact->email }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="theme-card p-6 shadow-sm sticky top-6">
                                <h2 class="text-base font-bold mb-4 text-center text-skin-heading">Kayıt Bilgileri</h2>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base text-center">Kişi ID</label>
                                        <div class="flex items-center justify-center gap-2">
                                            <code class="text-[10px] font-mono bg-[var(--dropdown-hover-bg)] px-2 py-1 rounded text-skin-base">{{ $contactId ?? 'YENİ' }}</code>
                                        </div>
                                    </div>
                                    @if($customer_id && $isViewMode)
                                         @php $customerName = collect($customers)->firstWhere('id', $customer_id)['name'] ?? '-'; @endphp
                                         <div>
                                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base text-center">Bağlı Müşteri</label>
                                            <div class="text-sm font-medium text-center">
                                                <a href="/dashboard/customers/{{ $customer_id }}" class="text-skin-primary hover:underline">{{ $customerName }}</a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($activeTab === 'messages')
                    <div class="space-y-6">
                        @include('livewire.customers.parts._tab-messages', [
                            'relatedMessages' => $relatedMessages
                        ])
                    </div>
                @endif

                @if($activeTab === 'notes')
                    @if($contactId)
                        @livewire('shared.notes-tab', [
                            'entityType' => 'CONTACT',
                            'entityId' => $contactId
                        ], key('notes-tab-contact-' . $contactId))
                    @else
                        <div class="theme-card p-6 shadow-sm text-center text-[var(--color-text-muted)] py-12">
                            <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                            <div class="font-medium">Kişiyi kaydedin, ardından not ekleyebilirsiniz</div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
