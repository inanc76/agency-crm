<?php

use Livewire\Volt\Component;
use App\Livewire\Customers\Contacts\Traits\HasContactActions;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    use HasContactActions;
}; ?>

<div>
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=contacts"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Kişi Listesi</span>
        </a>

        {{-- Header --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight" class="text-skin-heading">
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
                        <p class="text-sm opacity-60">
                            Yeni kişi bilgilerini girin
                        </p>
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
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'info' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Kişi Bilgileri
                </button>
                <button wire:click="$set('activeTab', 'messages')" 
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'messages' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Mesajlar (0)
                </button>
                <button wire:click="$set('activeTab', 'notes')" 
                    class="px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'notes' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Notlar (0)
                </button>
            </div>
        @else
            <div class="mb-8"></div>
        @endif

        <div class="flex gap-6">
            {{-- Left Column (80%) --}}
            <div class="w-4/5">
                @if($activeTab === 'info')
                    <div class="space-y-6">
            {{-- Kişi Bilgileri Card --}}
            <div class="theme-card p-6 shadow-sm">
                <h2 class="text-base font-bold mb-4" class="text-skin-heading">Kişi Bilgileri</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium mb-1 opacity-60"
                           >Firma Seçin *</label>
                        @if($isViewMode)
                            @php $customerName = collect($customers)->firstWhere('id', $customer_id)['name'] ?? '-'; @endphp
                            <div class="text-sm font-medium">{{ $customerName }}
                            </div>
                        @else
                            <select wire:model="customer_id" class="select w-full">
                                <option value="">Firma Seçin</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                                @endforeach
                            </select>
                            @error('customer_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 opacity-60"
                           >Durum *</label>
                        @if($isViewMode)
                            <div class="text-sm font-medium">
                                {{ $status === 'WORKING' ? 'Çalışıyor' : 'Ayrıldı' }}
                            </div>
                        @else
                            <select wire:model="status" class="select w-full">
                                <option value="WORKING">Çalışıyor</option>
                                <option value="LEFT">Ayrıldı</option>
                            </select>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 opacity-60"
                           >Cinsiyet</label>
                        @if($isViewMode)
                            @php $genderName = collect($genders)->firstWhere('id', $gender)['name'] ?? '-'; @endphp
                            <div class="text-sm font-medium">{{ $genderName }}</div>
                        @else
                            <select wire:model="gender" class="select w-full">
                                <option value="">Cinsiyet seçin</option>
                                @foreach($genders as $g)
                                    <option value="{{ $g['id'] }}">{{ $g['name'] }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 opacity-60"
                           >Ad Soyad *</label>
                        @if($isViewMode)
                            <div class="text-sm font-medium">{{ $name }}</div>
                        @else
                            <input type="text" wire:model="name" placeholder="Kişinin adını ve soyadını girin"
                                class="input w-full">
                            @error('name') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-medium mb-1 opacity-60"
                           >Pozisyon</label>
                        @if($isViewMode)
                            <div class="text-sm font-medium">{{ $position ?: '-' }}
                            </div>
                        @else
                            <input type="text" wire:model="position" placeholder="Örn: Genel Müdür, Pazarlama Uzmanı"
                                class="input w-full">
                        @endif
                    </div>
                </div>
            </div>

            {{-- İletişim Bilgileri Card --}}
            <div class="theme-card p-6 shadow-sm border border-[var(--success-border)] bg-[var(--success-bg)]">
                <h2 class="text-base font-bold mb-4" class="text-skin-heading">İletişim Bilgileri</h2>

                <div class="grid grid-cols-2 gap-6">
                    {{-- Emails --}}
                    <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-medium opacity-60"
                           >Email</label>
                        @if(!$isViewMode)
                            <button type="button" wire:click="addEmail"
                                class="hover:opacity-80 text-xs font-bold"
                                style="color: var(--action-link-color);">
                                + Email
                            </button>
                        @endif
                    </div>
                    @if($isViewMode)
                        @foreach($emails as $email)
                            @if($email)
                                <div class="text-sm font-medium mb-1">{{ $email }}</div>
                            @endif
                        @endforeach
                        @if(empty(array_filter($emails)))
                        <div class="text-sm opacity-40">-</div> @endif
                    @else
                        @foreach($emails as $index => $email)
                            <div class="flex items-center gap-2 mb-2">
                                <input type="email" wire:model="emails.{{ $index }}" placeholder="Email {{ $index + 1 }}"
                                    class="input flex-1 bg-[var(--card-bg)]">
                                @if($index > 0)
                                    <button type="button" wire:click="removeEmail({{ $index }})" class="text-[var(--color-danger)]">
                                        <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Phones --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-medium opacity-60"
                           >Telefon</label>
                        @if(!$isViewMode)
                            <button type="button" wire:click="addPhone"
                                class="hover:opacity-80 text-xs font-bold"
                                style="color: var(--action-link-color);">
                                + Telefon
                            </button>
                        @endif
                    </div>
                    @if($isViewMode)
                        @foreach($phones as $phone)
                            @if(!empty($phone['number']))
                                <div class="text-sm font-medium mb-1">
                                    {{ $phone['number'] }}
                                    @if(!empty($phone['extension']))
                                        <span class="opacity-70 text-xs">(Dahili: {{ $phone['extension'] }})</span>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                        @if(empty(array_filter(array_column($phones, 'number'))))
                        <div class="text-sm opacity-40">-</div> @endif
                    @else
                        @foreach($phones as $index => $phone)
                            <div class="flex items-center gap-2 mb-2">
                                <input type="text" wire:model="phones.{{ $index }}.number"
                                    placeholder="Telefon {{ $index + 1 }}" class="input flex-1 bg-[var(--card-bg)]">
                                <input type="text" wire:model="phones.{{ $index }}.extension" placeholder="Dahili" maxlength="5"
                                    class="input w-24 bg-[var(--card-bg)] text-center"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5)">
                                @if($index > 0)
                                    <button type="button" wire:click="removePhone({{ $index }})" class="text-[var(--color-danger)]">
                                        <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    @endif
                    </div>
                </div>
            </div>

            {{-- Diğer Bilgiler Card --}}
            <div class="theme-card p-6 shadow-sm border border-[var(--brand-primary)]/20 bg-[var(--brand-primary)]/5">
                <h2 class="text-base font-bold mb-4" class="text-skin-heading">Diğer Bilgiler</h2>

                {{-- Birth Date --}}
                <div class="mb-4">
                    <label class="block text-xs font-medium mb-1 opacity-60"
                       >Doğum Tarihi</label>
                    @if($isViewMode)
                        <div class="text-sm font-medium">{{ $birth_date ?: '-' }}
                        </div>
                    @else
                        <input type="date" wire:model="birth_date" class="input w-full bg-[var(--card-bg)]">
                    @endif
                </div>

                {{-- Social Profiles --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-medium opacity-60"
                           >Sosyal Medya Profili</label>
                        @if(!$isViewMode)
                            <button type="button" wire:click="addSocialProfile"
                                class="hover:opacity-80 text-xs font-bold"
                                style="color: var(--action-link-color);">
                                + Profil
                            </button>
                        @endif
                    </div>

                    @if($isViewMode)
                        @foreach($social_profiles as $profile)
                            @if(!empty($profile['url']))
                                <div class="text-sm font-medium mb-1">
                                    <a href="{{ $profile['url'] }}" target="_blank"
                                        class="text-[var(--action-link-color)] hover:underline">{{ $profile['name'] ?: $profile['url'] }}</a>
                                </div>
                            @endif
                        @endforeach
                    @else
                        @foreach($social_profiles as $index => $profile)
                            <div class="flex items-center gap-2 mb-2">
                                <input type="text" wire:model="social_profiles.{{ $index }}.name"
                                    placeholder="Başlık (örn: LinkedIn)" class="input w-1/3 bg-[var(--card-bg)]">
                                <input type="text" wire:model="social_profiles.{{ $index }}.url" placeholder="Link"
                                    class="input flex-1 bg-[var(--card-bg)]">
                                @if($index > 0)
                                    <button type="button" wire:click="removeSocialProfile({{ $index }})" class="text-[var(--color-danger)]">
                                        <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
                </div>
            @endif

            @if($activeTab === 'messages')
                <div class="theme-card p-6 shadow-sm text-center text-[var(--color-text-muted)] py-12">
                    <x-mary-icon name="o-chat-bubble-left-right" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                    <div class="font-medium">Henüz mesaj bulunmuyor</div>
                </div>
            @endif

            @if($activeTab === 'notes')
                <div class="theme-card p-6 shadow-sm text-center text-[var(--color-text-muted)] py-12">
                    <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                    <div class="font-medium">Henüz not bulunmuyor</div>
                </div>
            @endif
        </div>

        {{-- Right Column (20%) --}}
        <div class="w-1/5">
             <div class="theme-card p-6 shadow-sm text-center">
                <h3 class="text-sm font-bold text-skin-heading mb-4">Kişi Fotoğrafı</h3>
                
                <div class="w-32 h-32 mx-auto border-2 border-dashed border-[var(--card-border)] rounded-lg flex items-center justify-center mb-4 bg-[var(--card-bg)] overflow-hidden">
                    @php
                        $initials = mb_substr($name ?? 'K', 0, 1) ?: 'K';
                    @endphp
                    <div class="w-full h-full flex items-center justify-center bg-[var(--dropdown-hover-bg)] text-[var(--icon-muted)] font-bold text-5xl uppercase">
                        {{ $initials }}
                    </div>
                </div>
                <div class="text-[10px] text-[var(--color-text-muted)]">PNG, JPG, GIF (Max 5MB)</div>
            </div>
        </div>
    </div>
    </div>
</div>
