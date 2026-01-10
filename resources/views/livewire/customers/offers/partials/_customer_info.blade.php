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
                <div class="text-sm font-medium">
                    @if($status === 'DRAFT') Taslak
                    @elseif($status === 'SENT') Gönderildi
                    @elseif($status === 'ACCEPTED') Kabul Edildi
                    @else Reddedildi
                    @endif
                </div>
            @else
                <select wire:model="status" class="select w-full">
                    <option value="DRAFT">Taslak</option>
                    <option value="SENT">Gönderildi</option>
                    <option value="ACCEPTED">Kabul Edildi</option>
                    <option value="REJECTED">Reddedildi</option>
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
                <div class="text-sm font-medium">{{ $currency }}
                </div>
            @else
                <select wire:model.live="currency" class="select w-full bg-white">
                    <option value="TRY">TRY</option>
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
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
                <div class="text-sm font-medium">
                    %{{ $vat_rate }}
                </div>
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