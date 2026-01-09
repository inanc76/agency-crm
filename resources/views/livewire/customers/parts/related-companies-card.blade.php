{{-- İlişkili Firmalar Card --}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-skin-heading">İlişkili Firmalar</h2>

    {{-- Firma Seç Combobox (Hide in View Mode) --}}
    @if(!$isViewMode)
        <div class="mb-4">
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-medium opacity-60 text-skin-base">Firma
                    Seç</label>
                <span class="text-xs opacity-40 text-skin-base">{{ count($related_customers) }}/10</span>
            </div>

            @php
                // Filter out already selected customers
                $availableCustomers = array_filter($existingCustomers, function ($customer) {
                    return !in_array($customer['id'], $this->related_customers);
                });
            @endphp

            <select wire:change="addRelatedCustomer($event.target.value); $event.target.value = ''" class="select w-full"
                @if(count($related_customers) >= 10) disabled @endif>
                <option value="">İlişkili firma seçin...</option>
                @foreach($availableCustomers as $customer)
                    <option value="{{ $customer['id'] }}">{{ $customer['name'] }}</option>
                @endforeach
            </select>
        </div>
    @endif

    {{-- Seçilen Firmalar (2) --}}
    @if(count($related_customers) > 0)
        <div class="border-t border-[var(--card-border)]/50 pt-4 mt-2">
            <p class="text-xs font-medium opacity-60 mb-3 text-skin-base">Seçilen Firmalar
                ({{ count($related_customers) }})</p>
            <div class="space-y-2">
                @foreach($related_customers as $customerId)
                    @php
                        $customer = collect($existingCustomers)->firstWhere('id', $customerId);
                    @endphp
                    @if($customer)
                        <div
                            class="border rounded-lg p-3 transition-colors flex items-center justify-between bg-[var(--card-bg)]/50 border-[var(--card-border)]/60 hover:border-[var(--color-text-muted)]">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-semibold bg-[var(--brand-primary)]/10 text-[var(--brand-primary)]">
                                    {{ $loop->iteration }}
                                </div>
                                <span class="text-sm font-medium text-skin-base">{{ $customer['name'] }}</span>
                            </div>
                            @if(!$isViewMode)
                                <button type="button" wire:click="removeRelatedCustomer('{{ $customerId }}')"
                                    class="text-[var(--color-danger)] hover:opacity-80 hover:bg-[var(--color-danger-muted)] p-1 rounded transition-colors">
                                    <x-mary-icon name="o-x-mark" class="w-5 h-5" />
                                </button>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>