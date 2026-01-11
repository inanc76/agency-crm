{{--
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🎯 IDENTITY CARD
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Partial: _asset-selection-card.blade.php
Purpose: Customer & Asset Selection Card
Layer: UI Component (Blade Partial)
Dependencies: Livewire Component State ($customers, $assets)
Created: 2026-01-10
Refactored From: customers/services/create.blade.php (lines 388-430)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
--}}

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
                @error('customer_id') <span class="text-skin-danger text-xs">{{ $message }}</span>
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
                @error('asset_id') <span class="text-skin-danger text-xs">{{ $message }}</span>
                @enderror
            @endif
        </div>
    </div>
</div>