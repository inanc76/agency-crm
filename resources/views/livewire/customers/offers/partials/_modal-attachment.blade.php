{{--
🚀 MODAL: OFFER ATTACHMENT
---------------------------------------------------------------------------------------
SORUMLULUK: Teklife teknik şartname, sözleşme veya ek dökümanların yüklenmesi.
VALIDATION: Dosya boyutu (Client & Server side) ve dosya tipi kontrollerini içerir.
DÜZENLEME: editingAttachmentIndex üzerinden mevcut eklerin meta verilerini günceller.
BAĞLANTI: HasOfferActions trait'i - saveAttachment(), removeAttachment()
---------------------------------------------------------------------------------------
--}}
<x-mary-modal wire:model="showAttachmentModal"
    title="{{ $editingAttachmentIndex !== null ? 'Ek Düzenle' : 'Teklif Eki Ekle' }}" class="backdrop-blur"
    box-class="!max-w-2xl">
    <div class="space-y-4">
        {{-- Title --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Başlık *</label>
            <input type="text" wire:model="attachmentTitle" class="input w-full bg-white"
                placeholder="Örn: Teknik Şartname">
            @error('attachmentTitle') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Açıklama</label>
            <textarea wire:model="attachmentDescription" class="textarea w-full bg-white" rows="3"
                placeholder="Ek hakkında açıklama..."></textarea>
            @error('attachmentDescription') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Price --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Fiyat *</label>
            <div class="flex items-center gap-2">
                <input type="number" wire:model="attachmentPrice" class="input w-full bg-white" min="0" step="0.01"
                    placeholder="0.00">
                <span class="text-sm font-medium text-slate-600 min-w-[50px]">{{ $currency }}</span>
            </div>
            @error('attachmentPrice') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- File Upload --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">
                Dosya {{ $editingAttachmentIndex === null ? '*' : '(Değiştirmek için seçin)' }}
            </label>
            <input type="file" wire:model="attachmentFile" accept=".pdf,.doc,.docx"
                class="file-input file-input-bordered w-full bg-white"
                onchange="if(this.files[0] && this.files[0].size > 25600 * 1024) { alert('Dosya boyutu çok büyük! Maksimum 25MB yükleyebilirsiniz.'); this.value = ''; }">

            <div wire:loading wire:target="attachmentFile" class="w-full mt-2">
                <div class="flex items-center gap-2">
                    <span class="loading loading-ring loading-xs text-blue-600"></span>
                    <span class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Dosya Sunucuya
                        Aktarılıyor...</span>
                </div>
                <progress class="progress progress-primary w-full h-1.5 mt-1"></progress>
            </div>

            <div class="text-[10px] text-slate-400 mt-1" wire:loading.remove wire:target="attachmentFile">
                Maksimum 25MB - Sadece PDF veya Word dosyaları
            </div>
            @error('attachmentFile') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror

            @if($editingAttachmentIndex !== null && isset($attachments[$editingAttachmentIndex]['file_name']))
                <div class="text-xs text-slate-600 mt-2 flex items-center gap-2">
                    <x-mary-icon name="o-document" class="w-4 h-4" />
                    <span>Mevcut: {{ $attachments[$editingAttachmentIndex]['file_name'] }}</span>
                </div>
            @endif
        </div>
    </div>

    <x-slot:actions>
        <button wire:click="closeAttachmentModal" class="theme-btn-cancel" wire:loading.attr="disabled"
            wire:target="attachmentFile, saveAttachment">
            Vazgeç
        </button>
        <button wire:click="saveAttachment" class="theme-btn-save" wire:loading.attr="disabled"
            wire:target="attachmentFile, saveAttachment">
            <span wire:loading wire:target="saveAttachment" class="loading loading-spinner loading-xs mr-1"></span>
            <x-mary-icon name="o-check" class="w-4 h-4" wire:loading.remove wire:target="saveAttachment" />
            {{ $editingAttachmentIndex !== null ? 'Güncelle' : 'Ekle' }}
        </button>
    </x-slot:actions>
</x-mary-modal>