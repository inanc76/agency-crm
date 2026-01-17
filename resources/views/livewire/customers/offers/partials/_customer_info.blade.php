{{--
@component: _customer_info.blade.php
@section: Sol Kolon - Müşteri ve Ayarlar
@description: Müşteri seçimi, teklif geçerlilik süresi, para birimi, KDV ve indirim ayarlarını içerir.
@params: $isViewMode (bool), $customers (array), $customer_id (string), $status (string), $valid_days (int), $currency
(string), $discount_type (string), $discount_value (float), $vat_rate (float), $vatRates (array)
@events: updatedCustomerId, updatedValidDays, updatedDiscountValue, updatedDiscountType
--}}
{{-- Müşteri Bilgileri Card --}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-skin-heading">Müşteri Bilgileri
    </h2>
    <div class="grid grid-cols-1 gap-4 mb-4 pb-4 border-b border-slate-100">
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Teklif Başlığı *</label>
            @if($isViewMode)
                <div class="text-sm font-medium">{{ $title }}</div>
            @else
                <input type="text" wire:model.live="title" placeholder="Örn: Web Sitesi Yenileme Teklifi"
                    class="input w-full bg-white">
                @error('title') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
            @endif
        </div>
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Genel Açıklama</label>
            @if($isViewMode)
                <div class="text-sm font-medium whitespace-pre-wrap">{{ $description ?: '-' }}</div>
            @else
                <textarea wire:model.live="description" class="textarea w-full bg-white" rows="2"
                    placeholder="Teklif hakkında genel notlar..."></textarea>
                @error('description') <span class="text-skin-danger text-xs">{{ $message }}</span> @enderror
            @endif
        </div>
    </div>

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
                @error('customer_id') <span class="text-skin-danger text-xs">{{ $message }}</span>
                @enderror
            @endif
        </div>

        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Teklif Durumu</label>
            @if($isViewMode)
                @php $statusObj = collect($offerStatuses)->firstWhere('key', $status); @endphp
                @if($statusObj)
                    <span
                        class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $statusObj['color_class'] ?? 'bg-slate-50 text-slate-500' }}">
                        {{ $statusObj['display_label'] }}
                    </span>
                @else
                    <div class="text-sm font-medium">{{ $status }}</div>
                @endif
            @else
                <select wire:model="status" class="select w-full">
                    @foreach($offerStatuses as $s)
                        <option value="{{ $s['key'] }}">{{ $s['display_label'] }}</option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>
</div>

{{-- Teklif Ayarları Card --}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-skin-heading">Teklif Ayarları
    </h2>
    <div class="grid grid-cols-2 gap-6">
        {{-- Title moved to Description card --}}

        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Geçerlilik Süresi (Gün)</label>
            @if($isViewMode)
                <div class="text-sm font-medium">
                    {{ $valid_days }}
                    gün
                </div>
            @else
                <input type="number" wire:model.live="valid_days" class="input w-full bg-white" min="1">
            @endif
        </div>

        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Para Birimi</label>
            @if($isViewMode)
                @php $currencyRef = $offerModel->currency_item; @endphp
                <span
                    class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $currencyRef->color_class ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                    {{ $currencyRef->display_label ?? $currency }}
                </span>
            @else
                <select wire:model.live="currency" class="select w-full bg-white">
                    @foreach($currencies as $c)
                        <option value="{{ $c['id'] }}">{{ $c['id'] }} ({{ $c['name'] }})</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">İndirim</label>
            @if($isViewMode)
                <div class="text-sm font-medium">
                    @if($discount_type === 'PERCENTAGE') %{{ $discount_value }} @else
                    {{ number_format($discount_value, 0, ',', '.') }} {{ $currency }} @endif
                </div>
            @else
                <div class="flex items-center gap-[5px]">
                    <select wire:model.live="discount_type"
                        class="select select-sm w-24 bg-white border-slate-200 focus:outline-none">
                        <option value="PERCENTAGE">%</option>
                        <option value="AMOUNT">Tutar</option>
                    </select>
                    <input type="number" wire:model.live="discount_value"
                        class="input input-sm flex-1 bg-white border-slate-200 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        min="0" step="0.01" placeholder="0.00">
                </div>
            @endif
        </div>

        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">KDV Oranı</label>
            @if($isViewMode)
                @php $vatRef = $offerModel->vat_item(); @endphp
                <span
                    class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $vatRef->color_class ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                    {{ $vatRef->display_label ?? '%' . (int) $vat_rate }}
                </span>
            @else
                <select wire:model.live="vat_rate"
                    class="select select-sm w-full bg-white border-slate-200 group-hover:border-slate-300">
                    @foreach($vatRates as $rate)
                        <option value="{{ $rate['rate'] }}">{{ $rate['label'] }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        {{-- Validity date moved to summary sidebar --}}
    </div>
</div>