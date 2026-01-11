<?php

use Livewire\Volt\Component;
use App\Livewire\Customers\Assets\Traits\HasAssetActions;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    use HasAssetActions;
}; ?>

<div>
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/customers?tab=assets"
            class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Varlık Listesi</span>
        </a>

        {{-- Header --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
                    @if($isViewMode)
                        {{ $name }}
                    @elseif($assetId)
                        Düzenle: {{ $name }}
                    @else
                        Yeni Varlık Ekle
                    @endif
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($isViewMode)
                        <span
                            class="text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">Varlık</span>
                        <span class="text-[11px] font-mono text-slate-400">ID: {{ $assetId }}</span>
                    @else
                        <p class="text-sm opacity-60">
                            Yeni varlık bilgilerini girin
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($isViewMode)
                    <button type="button" wire:click="delete" wire:confirm="Bu varlığı silmek istediğinize emin misiniz?"
                        wire:key="btn-delete-{{ $assetId }}"
                        class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                        Sil
                    </button>
                    <button type="button" wire:click="toggleEditMode" wire:key="btn-edit-{{ $assetId }}"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                        Düzenle
                    </button>
                @else
                    <button type="button" wire:click="cancel" wire:key="btn-cancel-{{ $assetId ?: 'new' }}"
                        class="theme-btn-cancel">
                        İptal
                    </button>
                    <button type="button" wire:click="save" wire:loading.attr="disabled"
                        wire:key="btn-save-{{ $assetId ?: 'new' }}" class="theme-btn-save">
                        <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                        <x-mary-icon name="o-check" class="w-4 h-4" />
                        @if($assetId) Güncelle @else Kaydet @endif
                    </button>
                @endif
            </div>
        </div>

        {{-- Tab Navigation --}}
        @if($isViewMode)
            <div class="flex items-center border-b border-slate-200 mb-8 overflow-x-auto scrollbar-hide">
                <button wire:click="$set('activeTab', 'info')"
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'info' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Varlık Bilgileri
                </button>
                <button wire:click="$set('activeTab', 'messages')"
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
                    style="{{ $activeTab === 'messages' ? 'border-color: var(--active-tab-color); color: var(--color-text-heading);' : 'border-color: transparent; color: var(--color-text-base); opacity: 0.6;' }}">
                    Mesajlar (0)
                </button>
                <button wire:click="$set('activeTab', 'notes')"
                    class="cursor-pointer px-5 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
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
                        {{-- Varlık Bilgileri Card --}}
                        <div class="theme-card p-6 shadow-sm">
                            <h2 class="text-base font-bold mb-4 text-skin-heading">Varlık Bilgileri
                            </h2>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60">Müşteri *</label>
                                    @if($isViewMode)
                                        @php $customerName = collect($customers)->firstWhere('id', $customer_id)['name'] ?? '-'; @endphp
                                        <div class="text-sm font-medium">
                                            {{ $customerName }}
                                        </div>
                                    @else
                                        <select wire:model="customer_id" class="select w-full">
                                            <option value="">Müşteri Seçin</option>
                                            @foreach($customers as $c)
                                                <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('customer_id') <span class="text-skin-danger text-xs">{{ $message }}</span>
                                        @enderror
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60">Varlık Adı *</label>
                                    @if($isViewMode)
                                        <div class="text-sm font-medium">{{ $name }}
                                        </div>
                                    @else
                                        <input type="text" wire:model="name" placeholder="Varlık adını girin (Örn: Web Sitesi)"
                                            class="input w-full">
                                        @error('name') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1 opacity-60">Varlık Türü *</label>
                                    @if($isViewMode)
                                        @php $typeName = collect($assetTypes)->firstWhere('id', $type)['name'] ?? '-'; @endphp
                                        <div class="text-sm font-medium">{{ $typeName }}
                                        </div>
                                    @else
                                        <select wire:model="type" class="select w-full">
                                            <option value="">Varlık türü seçin</option>
                                            @foreach($assetTypes as $t)
                                                <option value="{{ $t['id'] }}">{{ $t['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('type') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
                                    @endif
                                </div>

                                <div class="col-span-2">
                                    <label class="block text-xs font-medium mb-1 opacity-60">URL</label>
                                    @if($isViewMode)
                                        <div class="text-sm font-medium">
                                            @if($url)
                                                <a href="{{ $url }}" target="_blank"
                                                    class="text-blue-500 hover:underline">{{ $url }}</a>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    @else
                                        <input type="text" wire:model.blur="url" placeholder="https://www.example.com"
                                            class="input w-full">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($activeTab === 'messages')
                    <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                        <x-mary-icon name="o-chat-bubble-left-right" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Henüz mesaj bulunmuyor</div>
                    </div>
                @endif

                @if($activeTab === 'notes')
                    <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                        <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Henüz not bulunmuyor</div>
                    </div>
                @endif
            </div>

            {{-- Right Column (20%) --}}
            <div class="w-1/5">
                <div class="theme-card p-6 shadow-sm text-center">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Varlık Görseli</h3>

                    <div
                        class="w-32 h-32 mx-auto border-2 border-dashed border-slate-200 rounded-lg flex items-center justify-center mb-4 bg-white/50 overflow-hidden">
                        @php
                            $initials = mb_substr($name ?? 'V', 0, 1) ?: 'V';
                        @endphp
                        <div
                            class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400 font-bold text-5xl uppercase">
                            {{ $initials }}
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-400">PNG, JPG, GIF (Max 5MB)</div>
                </div>
            </div>
        </div>
    </div>
</div>