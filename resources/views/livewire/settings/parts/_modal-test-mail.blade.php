{{--
🚀 TEST MAIL MODAL PARTIAL
---------------------------------------------------------
DESCRIPTION: Isolated modal for sending test emails.
ACTION: Triggers sendTest() from HasMailSettings trait.
STATE RESET: Closing modal manually or successful completion sets $showTestModal to false.
---------------------------------------------------------
--}}
<x-mary-modal wire:model="showTestModal" title="Test E-postası Gönder" class="backdrop-blur">
    <div class="space-y-4">
        <x-mary-input label="Alıcı E-posta" wire:model="test_email" icon="o-envelope" />
        <x-mary-input label="Konu" wire:model="test_subject" icon="o-bars-3-bottom-left" />
        <x-mary-textarea label="Mesaj İçeriği" wire:model="test_body" rows="3" />
    </div>
    <x-slot:actions>
        <button type="button" wire:click="$set('showTestModal', false)" class="theme-btn-cancel">
            İptal
        </button>
        <button type="button" wire:click="sendTest" wire:loading.attr="disabled" class="theme-btn-save">
            <span class="loading loading-spinner loading-xs" wire:loading wire:target="sendTest"></span>
            <x-mary-icon name="o-paper-airplane" class="w-4 h-4" wire:loading.remove wire:target="sendTest" />
            <span>Gönder</span>
        </button>
    </x-slot:actions>
</x-mary-modal>