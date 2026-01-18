<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use App\Livewire\Customers\Offers\Traits\HasOfferCalculations;
use App\Livewire\Customers\Offers\Traits\HasOfferItems;
use App\Livewire\Customers\Offers\Traits\HasOfferActions;

new class extends Component {
    use Toast, WithFileUploads;
    use HasOfferCalculations, HasOfferItems, HasOfferActions;
}; ?>

<div class="max-w-7xl mx-auto">
    {{-- Back Button --}}
    @include('livewire.customers.offers.partials._header', ['isViewMode' => $isViewMode, 'title' => $title, 'number' => $number, 'offerId' => $offerId])

    {{-- Tab Navigation --}}
    @include('livewire.customers.offers.partials._tabs', ['isViewMode' => $isViewMode, 'activeTab' => $activeTab])

    <div class="grid grid-cols-12 gap-6">
        {{-- Left Column (8/12 for info tab, 12/12 for notes/messages/downloads tabs) --}}
        <div class="{{ ($activeTab === 'notes' || $activeTab === 'messages' || $activeTab === 'downloads') ? 'col-span-12' : 'col-span-8' }}">
            @if($activeTab === 'info')
                <div class="space-y-6">
                    {{-- Validation Errors Summary --}}
                    @if($errors->any())
                        <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @include('livewire.customers.offers.partials._customer_info', [
                        'isViewMode' => $isViewMode,
                        'customers' => $customers,
                        'customer_id' => $customer_id,
                        'status' => $status,
                        'valid_days' => $valid_days,
                        'currency' => $currency,
                        'discount_type' => $discount_type,
                        'discount_value' => $discount_value,
                        'vat_rate' => $vat_rate,
                        'vatRates' => $vatRates,
                        'offerStatuses' => $offerStatuses,
                        'currencies' => $currencies,
                        'offerModel' => $offerModel,
                        'title' => $title,
                        'description' => $description
                    ])
                                        @foreach($sections as $index => $section)
                                            @include('livewire.customers.offers.partials._section_row', [
                                                'isViewMode' => $isViewMode,
                                                'section' => $section,
                                                'index' => $index
                                            ])
                                        @endforeach
                            @if(!$isViewMode)
                                            <div class="flex justify-center mt-4">
                                            <button type="button" wire:click="addSection" 
                                        class="flex items-center gap-2 px-4 py-2 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700">
                                    <x-mary-icon name="o-plus-circle" class="w-4 h-4" />
                                Bölüm Ekle
                                    </button>
                                        </div>
                            @endif
                            @include('livewire.customers.offers.partials._attachments', [
                                'isViewMode' => $isViewMode,
                                'attachments' => $attachments
                            ])
                </div>
              @endif
          @if($activeTab === 'messages')
            <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                <x-mary-icon name="o-chat-bubble-left-right" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                    <div class="font-medium">Henüz mesaj bulunmuyor</div>
            </div>
          @endif
        @if($activeTab === 'notes')
            @if($offerId)
                @livewire('shared.notes-tab', [
                    'entityType' => 'OFFER',
                    'entityId' => $offerId
                ], key('notes-tab-' . $offerId))
            @else
                    <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                        <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                        <div class="font-medium">Teklifi kaydedin, ardından not ekleyebilirsiniz</div>
                    </div>
                @endif
        @endif

            @if($activeTab === 'downloads')
                <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                    <x-mary-icon name="o-arrow-down-tray" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                    <div class="font-medium">Henüz indirme bulunmuyor</div>
                </div>
            @endif
        </div>

        {{-- Right Column (4/12) - Summary (hidden on notes/messages/downloads tabs) --}}
        @if($activeTab !== 'notes' && $activeTab !== 'messages' && $activeTab !== 'downloads')
            <div class="col-span-4 space-y-6">
                @include('livewire.customers.offers.partials._summary', [
                    'isViewMode' => $isViewMode,
                    'currency' => $currency,
                    'discount_type' => $discount_type,
                    'discount_value' => $discount_value,
                    'vat_rate' => $vat_rate,
                    'valid_until' => $valid_until,
                    'created_at' => $created_at,
                    'sections' => $sections,
                    'offerModel' => $offerModel,
                    'vatRates' => $vatRates
                ])
                @include('livewire.customers.offers.partials._modal-attachment', [
                    'showAttachmentModal' => $showAttachmentModal,
                    'editingAttachmentIndex' => $editingAttachmentIndex,
                    'attachmentTitle' => $attachmentTitle,
                    'attachmentDescription' => $attachmentDescription,
                    'attachmentPrice' => $attachmentPrice,
                    'currency' => $currency,
                    'attachmentFile' => $attachmentFile,
                    'attachments' => $attachments
                ])
            </div>
        @endif
    </div>
    @include('livewire.customers.offers.partials._modal-item-description', [
        'showItemDescriptionModal' => $showItemDescriptionModal,
        'itemDescriptionTemp' => $itemDescriptionTemp
    ])
</div>
