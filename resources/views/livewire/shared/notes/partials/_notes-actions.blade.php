{{--
    @component: _notes-actions.blade.php
    @section: Notes Tab - Action Bar
    @description: Not ekleme butonu ve özet bilgiler
    @params: $notes (Collection)
    @events: openNoteModal()
--}}

<div class="mb-6 flex items-center justify-between">
    {{-- Sol: Özet Bilgi --}}
    <div>
        <h3 class="text-lg font-semibold text-skin-base">Notlar</h3>
        <p class="text-sm text-skin-muted mt-1">
            Toplam {{ count($notes) }} not
        </p>
    </div>

    {{-- Sağ: Not Ekle Butonu --}}
    <div>
        <button wire:click="openNoteModal" class="theme-btn-save">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Yeni Not
        </button>
    </div>
</div>
