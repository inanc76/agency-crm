<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tema & Tasarım Yönetimi</h1>
        <p class="text-sm text-slate-500 mt-1">Uygulamanın görünümünü özelleştirin ve canlı tasarım rehberini
            inceleyin.</p>
    </div>
    <div class="flex items-center gap-3">
        <button type="button" wire:click="resetThemeToDefaults"
            wire:confirm="Tüm ayarları varsayılana döndürmek istediğinize emin misiniz?" class="theme-btn-cancel">
            <x-mary-icon name="o-arrow-path" class="w-4 h-4 mr-1" />
            Sıfırla
        </button>
        <button type="button" wire:click="saveThemeSettings" class="theme-btn-save">
            <x-mary-icon name="o-check" class="w-4 h-4 mr-1" />
            Ayarları Kaydet
        </button>
    </div>
</div>