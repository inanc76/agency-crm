<div class="col-span-4">
    <div class="theme-card p-6 shadow-sm sticky top-6">
        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">Rapor Özeti</h3>
        
        <div class="space-y-4 text-sm">
            <div class="flex items-center justify-between py-2 border-b border-slate-100">
                <span class="text-slate-500">Oluşturan</span>
                <span class="font-medium text-slate-700">{{ auth()->user()->name }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-slate-100">
                <span class="text-slate-500">Tarih</span>
                <div class="flex items-center gap-2">
                    @if($isDateEditing)
                        <input type="date" wire:model.blur="report_date" max="{{ date('Y-m-d') }}" 
                            class="input input-sm w-32 px-1 text-xs border-slate-200 rounded focus:outline-none focus:border-indigo-500 bg-white">
                        <button type="button" wire:click="toggleDateEdit" class="p-1 rounded hover:bg-green-50 text-green-600 hover:text-green-700 transition-colors">
                            <x-mary-icon name="o-check" class="w-4 h-4" />
                        </button>
                    @else
                        <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($report_date)->format('d.m.Y') }}</span>
                        @if(!$isViewMode)
                            <button type="button" wire:click="toggleDateEdit" class="text-slate-400 hover:text-indigo-600 transition-colors" title="Tarihi Düzenle">
                                <x-mary-icon name="o-pencil-square" class="w-3.5 h-3.5" />
                            </button>
                        @endif
                    @endif
                </div>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-slate-100">
                <span class="text-slate-500">Toplam Süre</span>
                <span class="font-medium text-slate-700">
                    {{ $this->totalTime['hours'] }}s {{ sprintf('%02d', $this->totalTime['minutes']) }}dk
                </span>
            </div>
            
            @if($selectedProject)
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-bold mb-2">Proje Bilgileri</p>
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl mb-2">
                        <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold shrink-0">
                            <x-mary-icon name="o-briefcase" class="w-4 h-4" />
                        </div>
                        <div class="truncate">
                            <p class="font-bold text-slate-900 truncate" title="{{ $selectedProject->name }}">{{ $selectedProject->name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $report_type === 'PROJECT' ? 'Yapım Projesi' : ($report_type === 'SERVICE' ? 'Destek Hizmeti' : '-') }}</p>
                        </div>
                    </div>
                </div>
            @elseif($customer_id)
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-bold mb-2">Seçili Müşteri</p>
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                            {{ substr(collect($customers)->firstWhere('id', $customer_id)['name'] ?? 'M', 0, 1) }}
                        </div>
                        <div class="truncate">
                            <p class="font-bold text-slate-900 truncate">{{ collect($customers)->firstWhere('id', $customer_id)['name'] ?? '-' }}</p>
                            <p class="text-[10px] text-slate-500">{{ $report_type === 'PROJECT' ? 'Proje Raporu' : ($report_type === 'SERVICE' ? 'Hizmet Raporu' : '-') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
