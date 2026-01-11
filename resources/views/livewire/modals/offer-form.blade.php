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
    @include('livewire.customers.offers.partials._header', ['isViewMode' => $isViewMode, 'title' => $title, 'offerId' => $offerId])

    {{-- Tab Navigation --}}
    @include('livewire.customers.offers.partials._tabs', ['isViewMode' => $isViewMode, 'activeTab' => $activeTab])

    <div class="grid grid-cols-12 gap-6">
        {{-- Left Column (8/12) --}}
        <div class="col-span-8">
            @if($activeTab === 'info')
                <div class="space-y-6">
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
                        'vatRates' => $vatRates
                    ])
                        @include('livewire.customers.offers.partials._title_description', [
                            'isViewMode' => $isViewMode,
                            'title' => $title,
                            'description' => $description
                        ])
                    @include('livewire.customers.offers.partials._items_table', [
                        'isViewMode' => $isViewMode,
                        'items' => $items
                    ])

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
                <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                    <x-mary-icon name="o-document-text" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                    <div class="font-medium">Henüz not bulunmuyor</div>
                </div>
            @endif

            @if($activeTab === 'downloads')
                <div class="theme-card p-6 shadow-sm text-center text-slate-500 py-12">
                    <x-mary-icon name="o-arrow-down-tray" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                    <div class="font-medium">Henüz indirme bulunmuyor</div>
                </div>
            @endif
        </div>

        {{-- Right Column (4/12) - Summary --}}
        <div class="col-span-4">
            @include('livewire.customers.offers.partials._summary', [
                'isViewMode' => $isViewMode,
                'currency' => $currency,
                'discount_type' => $discount_type,
                'discount_value' => $discount_value,
                'vat_rate' => $vat_rate,
                'valid_until' => $valid_until,
                'items' => $items
            ])

        </div>
    </div>

    @include('livewire.customers.offers.partials._modal-service-item', [
        'showServiceModal' => $showServiceModal,
        'selectedYear' => $selectedYear,
        'customerServices' => $customerServices,
        'modalCategory' => $modalCategory,
        'categories' => $categories,
        'modalServiceName' => $modalServiceName,
        'priceDefinitions' => $priceDefinitions
    ])
    @include('livewire.customers.offers.partials._modal-manual-item', [
        'showManualEntryModal' => $showManualEntryModal,
        'manualItems' => $manualItems,
        'currency' => $currency
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
    @include('livewire.customers.offers.partials._modal-item-description', [
        'showItemDescriptionModal' => $showItemDescriptionModal,
        'itemDescriptionTemp' => $itemDescriptionTemp
    ])
</div>
