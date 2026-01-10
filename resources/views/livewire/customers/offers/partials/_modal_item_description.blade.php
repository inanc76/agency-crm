{{--
@component: _modal_item_description.blade.php
@section: Kalem Açıklama Modalı
@description: Teklif kalemleri için özel açıklama girilmesini sağlayan modal.
@params: $showItemDescriptionModal (bool), $itemDescriptionTemp (string)
@events: saveItemDescription
--}}
{{-- Item Description Modal --}}
<x-mary-modal wire:model="showItemDescriptionModal" title="Teklif Kalem Açıklaması" class="backdrop-blur"
    box-class="!max-w-md">
    <div class="space-y-4">
        <div class="relative">
            <div class="flex justify-between items-center mb-2">
                <label class="text-xs font-bold opacity-70">Kalem
                    Açıklaması</label>
                <span
                    class="text-[10px] font-black px-2 py-0.5 rounded-lg {{ strlen($itemDescriptionTemp) >= 50 ? 'bg-skin-danger-muted text-skin-danger' : 'bg-blue-100 text-blue-600' }}">
                    {{ 50 - strlen($itemDescriptionTemp) }} Karakter Kaldı
                </span>
            </div>
            <textarea wire:model.live="itemDescriptionTemp"
                class="textarea textarea-bordered w-full bg-white border-slate-200 focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition-all text-sm leading-relaxed"
                placeholder="Bu kalem için özel bir not ekleyin..." rows="3" maxlength="50"
                style="border-radius: var(--input-radius, 0.375rem);"></textarea>
        </div>
        <p class="text-[11px] opacity-50 italic leading-relaxed">
            * Bu açıklama teklif dökümanında ilgili hizmet kalemi altında gösterilecektir.
        </p>
    </div>

    <x-slot:actions>
        <button wire:click="showItemDescriptionModal = false" class="theme-btn-cancel">
            Vazgeç
        </button>
        <button wire:click="saveItemDescription" class="theme-btn-save">
            <x-mary-icon name="o-check" class="w-4 h-4" />
            Kaydet
        </button>
    </x-slot:actions>
</x-mary-modal>