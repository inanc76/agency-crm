<div class="theme-card p-6 shadow-sm">
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
        <h2 class="text-sm font-medium text-slate-700">Minio Bağlantı Ayarları</h2>
    </div>

    {{-- Error Display Area --}}
    @include('livewire.settings.storage.parts._test_results')

    {{-- Form --}}
    <div class="grid grid-cols-1 gap-6">
        {{-- Bucket Name, Port & SSL --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
            <div class="md:col-span-6">
                <x-mary-input label="Bucket Adı" wire:model="bucket_name"
                    hint="Dosyaların saklanacağı bucket adı. Yoksa otomatik oluşturulur." />
            </div>
            <div class="md:col-span-2">
                <x-mary-input label="Port" type="number" wire:model="port" />
            </div>
            <div class="md:col-span-4 self-center pt-4">
                <x-mary-checkbox label="SSL Kullan (HTTPS)" wire:model="use_ssl"
                    hint="Güvenli bağlantı için önerilir" />
            </div>
        </div>

        {{-- Endpoint --}}
        <div>
            <x-mary-input label="Endpoint" wire:model="endpoint" hint="Minio sunucunuzun adresi (http/https olmadan)"
                placeholder="minio.example.com" />
        </div>

        {{-- Access Key --}}
        <div>
            <x-mary-input label="Access Key" wire:model="access_key" />
        </div>

        {{-- Secret Key --}}
        <div>
            <x-mary-password label="Secret Key" wire:model="secret_key" />
        </div>
    </div>
</div>