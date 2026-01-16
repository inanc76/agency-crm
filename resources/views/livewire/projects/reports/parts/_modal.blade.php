<x-mary-modal wire:model="showReportModal" title="Rapor Ekle" class="backdrop-blur" box-class="!max-w-4xl">
    <div class="min-h-[300px]">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="text-left py-2 px-2 w-32">Harcanan Süre *</th>
                    <th class="text-left py-2 px-2">Rapor *</th>
                    <th class="w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($modalLines as $index => $mLine)
                    <tr wire:key="modal-report-{{ $index }}">
                        <td class="p-2 align-top">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1">
                                    <select wire:model="modalLines.{{ $index }}.hours"
                                        class="select select-xs w-16 bg-white border-slate-200">
                                        @foreach($hourOptions as $h)
                                            <option value="{{ $h }}">{{ $h }}s</option>
                                        @endforeach
                                    </select>
                                    <select wire:model="modalLines.{{ $index }}.minutes"
                                        class="select select-xs w-16 bg-white border-slate-200">
                                        @foreach($minuteOptions as $m)
                                            <option value="{{ $m }}">{{ sprintf('%02d', $m) }}d</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error("modalLines.{$index}.hours") <span
                                class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </td>
                        <td class="p-2 align-top">
                            <textarea wire:model="modalLines.{{ $index }}.content"
                                class="textarea textarea-sm w-full bg-white border-slate-200 focus:border-blue-400" rows="2"
                                placeholder="Neler yapıldı?"></textarea>
                            @error("modalLines.{$index}.content") <span
                            class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                        </td>
                        <td class="p-2 align-top text-center">
                            @if(count($modalLines) > 1)
                                <button type="button" wire:click="removeModalLine({{ $index }})"
                                    class="text-slate-400 hover:text-red-500 transition-colors p-1">
                                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <button type="button" wire:click="addModalLine"
                class="flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors cursor-pointer">
                <x-mary-icon name="o-plus-circle" class="w-4 h-4" />
                Yeni Satır Ekle
            </button>
        </div>
    </div>

    <x-slot:actions>
        <button wire:click="$set('showReportModal', false)" class="theme-btn-cancel">
            Vazgeç
        </button>
        <button wire:click="confirmModalLines" class="theme-btn-save">
            <x-mary-icon name="o-check" class="w-4 h-4" />
            Listeye Ekle
        </button>
    </x-slot:actions>
</x-mary-modal>