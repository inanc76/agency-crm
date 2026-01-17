{{-- Customer --}}
<div>
    <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Müşteri *</label>
    @if($isViewMode)
        <div class="text-sm font-medium text-skin-base">{{ $selectedCustomer['name'] ?? '-' }}</div>
    @else
        <select name="customer_id" wire:model.live="customer_id" class="select w-full">
            <option value="">Müşteri Seçin</option>
            @foreach($customers as $customer)
                <option value="{{ $customer['id'] }}">{{ $customer['name'] }}</option>
            @endforeach
        </select>
        @error('customer_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
    @endif
</div>