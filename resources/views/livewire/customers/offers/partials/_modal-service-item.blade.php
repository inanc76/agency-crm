{{--
🚀 MODAL: SERVICE ITEM SELECTION
---------------------------------------------------------------------------------------
SORUMLULUK: Veritabanındaki hazır hizmet tanımlarının veya müşterinin mevcut hizmetlerinin teklife eklenmesi.
SOL PANEL: Müşterinin geçmiş hizmetlerini (Existing Services) uzatmak için kullanılır.
SAĞ PANEL: Kategori bazlı yeni hizmet (Price Definitions) seçimi sağlar.
BAĞLANTI: HasOfferItems trait'i - addServiceFromExisting(), addServiceFromPriceDefinition()
---------------------------------------------------------------------------------------
--}}
<x-mary-modal wire:model="showServiceModal" title="Hizmet Ekle" class="backdrop-blur" box-class="!max-w-5xl">
    <div class="grid grid-cols-2 gap-6">
        {{-- Left Panel: Existing Services --}}
        <div class="border-r border-slate-200 pr-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-bold text-sm text-skin-heading">Mevcut Hizmetleri Uzat</h4>
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
                                            class="badge badge-xs bg-slate-100 text-slate-500 border-none">{{ $service['service_category'] }}</span>
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
            <h4 class="font-bold text-sm mb-4 text-skin-heading">Yeni Hizmet Ekle</h4>
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