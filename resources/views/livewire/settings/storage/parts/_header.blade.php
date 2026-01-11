<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Depolama Ayarları</h1>
        <p class="text-sm text-slate-500 mt-1">Minio (S3 Uyumlu) depolama entegrasyonu ayarları.</p>
    </div>
    <div class="flex items-center gap-3">
        <x-mary-button label="Bağlantıyı Test Et" icon="o-wifi" class="btn-outline btn-sm" wire:click="testConnection"
            spinner="testConnection" />
        <button type="button" wire:click="save" class="theme-btn-save">
            <x-mary-icon name="o-check" class="w-4 h-4 mr-1" />
            Ayarları Kaydet
        </button>
    </div>
</div>