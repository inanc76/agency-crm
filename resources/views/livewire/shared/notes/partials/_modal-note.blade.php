{{--
@component: _modal-note.blade.php
@section: Notes Tab - Note Modal
@description: Not ekleme ve düzenleme modalı
@params: $showNoteModal (bool), $editingNoteId (string|null), $noteContent (string), $noteVisibleToDepartments (array),
$allDepartments (Collection)
@events: saveNote(), closeNoteModal()
--}}

<x-mary-modal wire:model="showNoteModal" title="{{ $editingNoteId ? 'Notu Düzenle' : 'Yeni Not Ekle' }}"
    class="backdrop-blur" box-class="!max-w-3xl" without-trap-focus>

    <div class="space-y-6">
        {{-- Not İçeriği --}}
        <div>
            <label class="block text-sm font-medium text-skin-base mb-2">
                Not İçeriği <span class="text-red-500">*</span>
            </label>
            <textarea wire:model="noteContent"
                class="textarea w-full bg-white border-skin-light focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                rows="8" autofocus placeholder="Notunuzu buraya yazın..."></textarea>
            @error('noteContent')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
            <p class="text-xs text-skin-muted mt-1">
                {{ strlen($noteContent) }} / 10.000 karakter
            </p>
        </div>

        {{-- Kimlerin Görebileceği departmanlar --}}
        <div>
            <label class="block text-sm font-medium text-skin-base mb-2">
                Kimler Görebilir <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-skin-muted mb-3">
                Bu notu görebilecek departmanları seçin. Seçilen departmandaki tüm kullanıcılar bu notu görebilir.
            </p>

            <x-mary-choices wire:model="noteVisibleToDepartments" :options="$allDepartments" option-label="name"
                option-value="id" searchable class="w-full" no-result-text="Sonuç bulunamadı" />

            @error('noteVisibleToDepartments')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror

            {{-- Seçili Departman Sayısı --}}
            @if(count($noteVisibleToDepartments) > 0)
                <p class="text-xs text-blue-600 mt-2 font-medium">
                    {{ count($noteVisibleToDepartments) }} departman seçildi
                </p>
            @else
                <p class="text-xs text-amber-600 mt-2 font-medium">
                    ⚠️ En az bir departman seçmelisiniz
                </p>
            @endif
        </div>
    </div>

    {{-- Modal Actions --}}
    <x-slot:actions>
        <button wire:click="closeNoteModal" class="theme-btn-cancel">
            İptal
        </button>
        <button wire:click="saveNote" class="theme-btn-save" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="saveNote">
                {{ $editingNoteId ? 'Güncelle' : 'Kaydet' }}
            </span>
            <span wire:loading wire:target="saveNote" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Kaydediliyor...
            </span>
        </button>
    </x-slot:actions>
</x-mary-modal>