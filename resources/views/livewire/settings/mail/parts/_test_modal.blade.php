<x-mary-modal wire:model="showTestModal" title="Test E-postası Gönder" class="backdrop-blur">
    <div class="grid gap-4">
        <p class="text-sm text-slate-500">Mevcut ayarların doğruluğunu kontrol etmek için bir test e-postası gönderin.
        </p>
        <x-mary-input label="Alıcı E-posta" wire:model="test_email" placeholder="test@example.com" />
        <x-mary-input label="Konu" wire:model="test_subject" />
        <x-mary-textarea label="Mesaj İçeriği" wire:model="test_body" rows="3" />
    </div>
    <x-slot:actions>
        <x-mary-button label="İptal" @click="$wire.showTestModal = false" class="btn-ghost" />
        <x-mary-button label="Gönder" icon="o-paper-airplane" class="btn-primary" wire:click="sendTest"
            spinner="sendTest" />
    </x-slot:actions>
</x-mary-modal>