{{--
@component: _modals.blade.php
@section: Modal Container
@description: Tüm modal parçalarını (Hizmet, Açıklama, Manuel, Ek Dosya) yükleyen ana kapsayıcıdır.
@params: Tüm modal değişkenleri
@sub-components: _modal_service_selection, _modal_item_description, _modal_manual_entry, _modal_attachment
--}}
@include('livewire.customers.offers.partials._modal_service_selection', [
    'showServiceModal' => $showServiceModal,
    'selectedYear' => $selectedYear,
    'customerServices' => $customerServices,
    'modalCategory' => $modalCategory,
    'categories' => $categories,
    'modalServiceName' => $modalServiceName,
    'priceDefinitions' => $priceDefinitions
])

@include('livewire.customers.offers.partials._modal_item_description', [
    'showItemDescriptionModal' => $showItemDescriptionModal,
    'itemDescriptionTemp' => $itemDescriptionTemp
])

@include('livewire.customers.offers.partials._modal_manual_entry', [
    'showManualEntryModal' => $showManualEntryModal,
    'manualItems' => $manualItems,
    'currency' => $currency
])

@include('livewire.customers.offers.partials._modal_attachment', [
    'showAttachmentModal' => $showAttachmentModal,
    'editingAttachmentIndex' => $editingAttachmentIndex,
    'attachmentTitle' => $attachmentTitle,
    'attachmentDescription' => $attachmentDescription,
    'attachmentPrice' => $attachmentPrice,
    'currency' => $currency,
    'attachmentFile' => $attachmentFile,
    'attachments' => $attachments
])