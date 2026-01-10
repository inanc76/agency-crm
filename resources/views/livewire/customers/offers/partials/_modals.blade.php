{{-- Service Selection Modal --}}
<x-mary-modal wire:model="showServiceModal" title="Hizmet Ekle" class="backdrop-blur" box-class="!max-w-5xl">
    <div class="grid grid-cols-2 gap-6">
        {{-- Left Panel: Existing Services --}}
        <div class="border-r border-slate-200 pr-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-bold text-sm" class="text-skin-heading">Mevcut Hizmetleri Uzat</h4>
                <select wire:model.live="selectedYear" class="select select-sm bg-white border-slate-200">
                    @for($year = date('Y'); $year >= date('Y') - 2; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>

            @if(count($customerServices) > 0)
                <div class="space-y-3 h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($customerServices as $service)
                        <div class="theme-card p-3 border border-slate-200 hover:border-blue-300 transition-colors group">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="badge badge-xs bg-slate-100 text-slate-500 border-none">{{ $service['category'] }}</span>
                                        <span
                                            class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($service['start_date'])->format('Y') }}</span>
                                    </div>
                                    <p class="font-bold text-sm text-slate-800 truncate">{{ $service['service_name'] }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="text-xs font-semibold text-blue-600">{{ number_format($service['service_price'], 0, ',', '.') }}
                                            {{ $service['service_currency'] }}</span>
                                        <span
                                            class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $service['service_duration'] }}
                                            Yıl</span>
                                        @if($service['end_date'])
                                            <span
                                                class="text-[10px] px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-100 font-medium">Bitiş:
                                                {{ \Carbon\Carbon::parse($service['end_date'])->format('d.m.Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <button wire:click="addServiceFromExisting('{{ $service['id'] }}')"
                                    class="theme-btn-cancel !py-1 !px-4 !text-xs !shadow-none hover:!bg-blue-600 hover:!text-white hover:!border-blue-600 transition-colors">
                                    Uzat
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                    <x-mary-icon name="o-clock" class="w-8 h-8 mx-auto mb-2 opacity-20" />
                    <p class="text-sm text-slate-400">Bu yıl için süreli hizmet bulunamadı</p>
                </div>
            @endif
        </div>

        {{-- Right Panel: Price Definitions --}}
        <div class="pl-6">
            <h4 class="font-bold text-sm mb-4" class="text-skin-heading">Yeni Hizmet Ekle</h4>
            <p class="text-xs text-slate-500 mb-6 font-medium">Fiyat tanımlarından yeni hizmet ekleyebilirsiniz</p>

            <div class="space-y-5">
                <x-mary-select label="Kategori Seçin" icon="o-tag" wire:model.live="modalCategory"
                    :options="$categories" placeholder="Kategori Seçin" />

                @if($modalCategory)
                    <x-mary-select label="Hizmet Adı" icon="o-briefcase" wire:model.live="modalServiceName"
                        :options="collect($priceDefinitions)->where('category', $modalCategory)->map(fn($pd) => ['id' => $pd['name'], 'name' => $pd['name']])->toArray()" placeholder="Hizmet Seçin" />
                @endif

                @if($modalServiceName)
                    @php
                        $selectedPD = collect($priceDefinitions)
                            ->where('category', $modalCategory)
                            ->firstWhere('name', $modalServiceName);
                    @endphp
                    @if($selectedPD)
                        <div class="p-5 theme-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-slate-800">{{ $selectedPD['name'] }}</p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $selectedPD['description'] ?? 'Tanımlı hizmet içeriği' }}
                                    </p>
                                </div>
                                <span
                                    class="bg-white px-2 py-1 rounded text-[10px] font-bold text-green-600 border border-green-100 shadow-sm">{{ $selectedPD['duration'] ?? 1 }}
                                    Yıl</span>
                            </div>
                            <div class="mt-4 flex items-end justify-between">
                                <span
                                    class="text-lg font-black text-slate-900">{{ number_format($selectedPD['price'], 0, ',', '.') }}
                                    <span class="text-sm font-medium">{{ $selectedPD['currency'] }}</span></span>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <x-slot:actions>
        <button wire:click="closeServiceModal" class="theme-btn-cancel">
            Vazgeç
        </button>
        <button wire:click="addServiceFromPriceDefinition" class="theme-btn-save" @if(!$modalServiceName) disabled
        @endif>
            <x-mary-icon name="o-check" class="w-4 h-4" />
            Hizmeti Ekle
        </button>
    </x-slot:actions>
</x-mary-modal>

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

{{-- Manual Entry Modal --}}
<x-mary-modal wire:model="showManualEntryModal" title="Manuel Kalem Ekle" class="backdrop-blur" box-class="!max-w-6xl">
    <div class="overflow-x-auto min-h-[300px]">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="text-left py-2 px-2 w-[25%]">Hizmet Adı *</th>
                    <th class="text-left py-2 px-2 w-[25%]">Açıklama</th>
                    <th class="text-center py-2 px-2 w-[10%]">Süre (Yıl)</th>
                    <th class="text-right py-2 px-2 w-[15%]">Fiyat ({{ $currency }}) *</th>
                    <th class="text-center py-2 px-2 w-[10%]">Adet *</th>
                    <th class="text-right py-2 px-2 w-[10%]">Toplam</th>
                    <th class="w-[5%]"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($manualItems as $index => $mItem)
                    <tr wire:key="manual-item-{{ $index }}">
                        <td class="p-2 align-top">
                            <input type="text" wire:model="manualItems.{{ $index }}.service_name"
                                class="input input-sm w-full bg-white border-slate-200 focus:border-blue-400"
                                placeholder="Hizmet Adı">
                            @error("manualItems.{$index}.service_name")
                                <span class="text-skin-danger text-[10px] block mt-1">{{ $message }}</span>
                            @enderror
                        </td>
                        <td class="p-2 align-top">
                            <textarea wire:model="manualItems.{{ $index }}.description"
                                class="textarea textarea-sm w-full bg-white border-slate-200 focus:border-blue-400" rows="1"
                                placeholder="Açıklama"></textarea>
                        </td>
                        <td class="p-2 align-top">
                            <input type="number" wire:model="manualItems.{{ $index }}.duration"
                                class="input input-sm w-full bg-white border-slate-200 focus:border-blue-400 text-center"
                                placeholder="Opsiyonel" min="1">
                        </td>
                        <td class="p-2 align-top">
                            <input type="number" wire:model.live="manualItems.{{ $index }}.price"
                                class="input input-sm w-full bg-white border-slate-200 focus:border-blue-400 text-right"
                                min="0" step="0.01">
                            @error("manualItems.{$index}.price")
                                <span class="text-skin-danger text-[10px] block mt-1">{{ $message }}</span>
                            @enderror
                        </td>
                        <td class="p-2 align-top">
                            <input type="number" wire:model.live="manualItems.{{ $index }}.quantity"
                                class="input input-sm w-full bg-white border-slate-200 focus:border-blue-400 text-center"
                                min="1">
                        </td>
                        <td class="p-2 align-top text-right font-medium pt-3 text-slate-700">
                            {{ number_format(((float) ($mItem['price'] ?? 0)) * ((int) ($mItem['quantity'] ?? 1)), 0, ',', '.') }}
                        </td>
                        <td class="p-2 align-top pt-2 text-center">
                            @if(count($manualItems) > 1)
                                <button type="button" wire:click="removeManualItemRow({{ $index }})"
                                    class="text-skin-danger hover:opacity-80 p-1">
                                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <button type="button" wire:click="addManualItemRow"
                class="flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                <x-mary-icon name="o-plus-circle" class="w-4 h-4" />
                Yeni Satır Ekle
            </button>
        </div>
    </div>

    <x-slot:actions>
        <button wire:click="$set('showManualEntryModal', false)" class="theme-btn-cancel">
            Vazgeç
        </button>
        <button wire:click="saveManualItems" class="theme-btn-save">
            <x-mary-icon name="o-check" class="w-4 h-4" />
            Listeye Ekle
        </button>
    </x-slot:actions>
</x-mary-modal>

{{-- Attachment Modal --}}
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