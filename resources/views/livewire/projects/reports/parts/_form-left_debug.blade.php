<div class="col-span-8 flex flex-col gap-6">
    {{-- Card 1: Müşteri Bilgileri --}}
    <div class="theme-card p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
            <x-mary-icon name="o-user" class="w-5 h-5" />
            Müşteri Bilgileri
        </h3>

        <div class="grid grid-cols-2 gap-6">
            {{-- Müşteri --}}
            <div>
                <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Müşteri *</label>
                @if($isViewMode)
                    <div class="text-sm font-medium text-skin-base">
                        {{ collect($customers)->firstWhere('id', $customer_id)['name'] ?? '-' }}
                    </div>
                @else
                    <select wire:model.live="customer_id" class="select w-full">
                        <option value="">Müşteri seçin...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer['id'] }}">{{ $customer['name'] }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            {{-- Rapor Hedefi Switch --}}
            <div>
                <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Raporu nereye gireyim?</label>
                @if($isViewMode)
                    <div class="text-sm font-medium text-skin-base">
                        {{ $creation_target === 'PROJECT' ? 'Proje' : 'Görev' }}
                    </div>
                @else
                    <div class="flex items-center bg-slate-100 p-1 rounded-lg w-fit">
                        <button type="button" wire:click="$set('creation_target', 'PROJECT')"
                            class="px-4 py-1.5 text-xs font-bold rounded-md transition-all {{ $creation_target === 'PROJECT' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                            Proje
                        </button>
                        <button type="button" wire:click="$set('creation_target', 'TASK')"
                            class="px-4 py-1.5 text-xs font-bold rounded-md transition-all {{ $creation_target === 'TASK' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                            Görev
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Card: İlişkili Proje (Conditional) --}}
    @if($creation_target === 'PROJECT')
        <div class="theme-card p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
                <x-mary-icon name="o-briefcase" class="w-5 h-5" />
                İlişkili Proje
            </h3>

            <div class="grid grid-cols-2 gap-6">
                {{-- Proje Tipi --}}
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Proje Tipi *</label>
                    @if($isViewMode)
                        <div class="text-sm font-medium text-skin-base">
                            {{ $report_type === 'PROJECT' ? 'Yapım Projesi' : 'Destek Hizmeti' }}
                        </div>
                    @else
                        <select wire:model.live="report_type" class="select w-full">
                            <option value="">Seçiniz...</option>
                            <option value="PROJECT">Yapım Projesi</option>
                            <option value="SERVICE">Destek Hizmeti</option>
                        </select>
                        @error('report_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @endif
                </div>

                {{-- Hizmetler / Projeler Logic --}}
                @if($report_type === 'SERVICE')
                    <div>
                        <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Hizmet Kategorisi *</label>
                        @if($isViewMode)
                            <div class="text-sm font-medium text-skin-base">
                                {{ collect($categories)->firstWhere('key', $service_category_key)['name'] ?? '-' }}
                            </div>
                        @else
                            <select wire:model.live="service_category_key" class="select w-full">
                                <option value="">Kategori seçin...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat['key'] }}">{{ $cat['name'] }}</option>
                                @endforeach
                            </select>
                            @error('service_category_key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Projeler *</label>
                        @if($isViewMode)
                            <div class="text-sm font-medium text-skin-base">
                                {{ collect($projects)->firstWhere('id', $project_id)['name'] ?? '-' }}
                            </div>
                        @else
                            <select wire:model.live="project_id" class="select w-full">
                                <option value="">
                                    @if($service_category_key && count($projects) > 0)
                                        Proje Seçiniz...
                                    @elseif($service_category_key)
                                        Bağlı proje bulunamadı...
                                    @else
                                        Proje seçin...
                                    @endif
                                </option>
                                @foreach($projects as $p)
                                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                                @endforeach
                            </select>
                            @error('project_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>
                @elseif($report_type === 'PROJECT')
                    <div>
                        <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Projeler *</label>
                        @if($isViewMode)
                            <div class="text-sm font-medium text-skin-base">
                                {{ collect($projects)->firstWhere('id', $project_id)['name'] ?? '-' }}
                            </div>
                        @else
                            <select wire:model.live="project_id" class="select w-full">
                                <option value="">Proje seçin...</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                                @endforeach
                            </select>
                            @error('project_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Card: İlişkili Görev (Conditional) --}}
    @if($creation_target === 'TASK' && $customer_id)
        <div class="theme-card p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
                <x-mary-icon name="o-clipboard-document-check" class="w-5 h-5" />
                İlişkili Görev
            </h3>
            <div>
                <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Devam Eden Görevler</label>
                @if($isViewMode)
                    <div class="text-sm font-medium text-skin-base">
                        {{ collect($inProgressTasks)->firstWhere('id', $task_id)['name'] ?? 'Seçilmedi' }}
                    </div>
                @else
                    <x-mary-choices wire:model="task_id" :options="$inProgressTasks" option-label="name" option-value="id"
                        searchable class="w-full" no-result-text="Devam eden görev bulunamadı" single />
                    <p class="text-[10px] mt-1 text-slate-400 italic">Sadece "Devam Ediyor" durumundaki görevler listelenir.
                        Seçim zorunlu değildir.</p>
                @endif
            </div>
        </div>
    @endif

    {{-- Card 2: Rapor Bilgileri --}}
    <div class="theme-card p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-[var(--color-text-heading)] flex items-center gap-2">
                <x-mary-icon name="o-document-text" class="w-5 h-5" />
                Rapor Bilgileri
            </h3>
            @if(!$isViewMode)
                <div class="flex flex-col items-end gap-1">
                    <button type="button" @if(!$project_id)
                        class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-slate-100 border border-slate-200 rounded-lg text-slate-400 cursor-not-allowed"
                    title="Lütfen önce geçerli bir proje seçin" @else wire:click="openReportModal"
                            class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700"
                        @endif>
                        <x-mary-icon name="o-plus" class="w-4 h-4" />
                        Rapor Ekle
                    </button>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-2 font-medium opacity-60">Raporu Giren</th>
                        <th class="text-left py-2 px-2 font-medium opacity-60">Süre</th>
                        <th class="text-left py-2 px-2 font-medium opacity-60">Rapor</th>
                        @if(!$isViewMode)
                            <th class="text-center py-2 px-2 font-medium opacity-60 w-16">İşlem</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reportLines as $index => $line)
                        <tr>
                            <td class="py-3 px-2 font-medium text-slate-700 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 border border-slate-200">
                                        {{ substr($line['user_name'] ?? '?', 0, 1) }}
                                    </div>
                                    <span>{{ $line['user_name'] ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-2 font-medium text-slate-700 whitespace-nowrap">
                                {{ $line['hours'] }}s {{ sprintf('%02d', $line['minutes']) }}dk
                            </td>
                            <td class="py-3 px-2 text-slate-600 text-sm">
                                {{ $line['content'] }}
                            </td>
                            @if(!$isViewMode)
                                <td class="py-3 px-2 text-center">
                                    <button type="button" wire:click="removeReportLine({{ $index }})"
                                        class="text-slate-400 hover:text-red-500 transition-colors cursor-pointer">
                                        <x-mary-icon name="o-trash" class="w-4 h-4" />
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isViewMode ? 3 : 4 }}" class="py-8 text-center text-slate-400 italic">
                                Henüz rapor satırı eklenmedi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>