{{-- 📝 Notlar Sekmesi --}}
@livewire('shared.notes-tab', [
    'entityType' => 'CUSTOMER',
    'entityId' => $customer->id
], key('notes-tab-customer-' . $customer->id))
