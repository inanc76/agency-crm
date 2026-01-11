{{--
🚀 SERVICE CORE PARTIAL
---------------------------------------------------------
NEDEN: Varlık (Asset) ve Müşteri (Customer) ilişkisinin kurulduğu, işlemin başlangıç tarihinin belirlendiği ana blok.
BAĞLANTILAR:
- $customer_id (wire:model.live - Müşteri değişince varlık listesi yenilenir)
- $asset_id (wire:model - Hizmetin bağlanacağı varlık)
- $start_date (wire:model - Hizmet başlangıç tarihi)
VALIDASYON (V10):
- customer_id: required, exists:customers,id
- asset_id: required, exists:assets,id (Müşteriye ait olmalı)
- start_date: required, date (Geçerli bir tarih olmalı)
---------------------------------------------------------
--}}
<div class="space-y-6">
    {{-- Varlık Seçimi Card --}}
    <div class="theme-card p-6 shadow-sm">
        <h2 class="text-base font-bold mb-4 text-skin-heading">Varlık Seçimi</h2>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-medium mb-1 opacity-60">Müşteri *</label>
                @if($isViewMode)
                    @php $customerName = collect($customers)->firstWhere('id', $customer_id)['name'] ?? '-'; @endphp
                    <div class="text-sm font-medium">
                        {{ $customerName }}
                    </div>
                @else
                    <select wire:model.live="customer_id" class="select w-full">
                        <option value="">Müşteri Seçin</option>
                        @foreach($customers as $c)
                            <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select>
                    @error('customer_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span>
                    @enderror
                @endif
            </div>

            <div>
                <label class="block text-xs font-medium mb-1 opacity-60">Varlık *</label>
                @if($isViewMode)
                    @php $assetName = \App\Models\Asset::find($asset_id)?->name ?? '-'; @endphp
                    <div class="text-sm font-medium">{{ $assetName }}
                    </div>
                @else
                    <select wire:model="asset_id" class="select w-full" @if(!$customer_id) disabled @endif>
                        <option value="">{{ $customer_id ? 'Varlık seçin' : 'Önce müşteri seçin' }}</option>
                        @foreach($assets as $a)
                            <option value="{{ $a['id'] }}">{{ $a['name'] }}</option>
                        @endforeach
                    </select>
                    @error('asset_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span>
                    @enderror
                @endif
            </div>
        </div>
    </div>

    {{-- Başlangıç Tarihi Card --}}
    <div class="theme-card p-6 shadow-sm border border-purple-100 bg-purple-50/50">
        <h2 class="text-base font-bold mb-4 text-skin-heading">Başlangıç Tarihi</h2>
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Başlangıç Tarihi *</label>
            <div class="text-xs text-slate-400 mb-2">Bitiş tarihi seçilen süreye göre otomatik hesaplanacaktır.</div>

            @if($isViewMode)
                <div class="text-sm font-medium">
                    {{ \Carbon\Carbon::parse($start_date)->format('d.m.Y') }}
                </div>
            @else
                <input type="date" wire:model="start_date" class="input w-full bg-white">
                @error('start_date') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>
    </div>
</div>