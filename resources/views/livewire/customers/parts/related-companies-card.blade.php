{{-- İlişkili Firmalar Card --}}
<div class="card border p-6 shadow-sm @if($isViewMode) bg-slate-50/40 @endif">
    <h2 class="text-base font-semibold text-slate-800 mb-4">İlişkili Firmalar</h2>



    {{-- Firma Seç Combobox (Hide in View Mode) --}}
    @if(!$isViewMode)
        <div class="mb-4">
            <div class="flex items-center justify-between mb-1">
                <label class="block text-sm font-medium text-slate-700">Firma Seç</label>
                <span class="text-xs text-slate-500">{{ count($related_customers) }}/10</span>
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
        <div class="border-t border-slate-200 pt-4">
            <p class="text-sm font-medium text-slate-700 mb-3">Seçilen Firmalar ({{ count($related_customers) }})</p>
            <div class="space-y-2">
                @foreach($related_customers as $customerId)
                    @php
                        $customer = collect($existingCustomers)->firstWhere('id', $customerId);
                    @endphp
                    @if($customer)
                        <div
                            class="flex items-center justify-between bg-white border border-slate-200 rounded-lg p-3 hover:border-slate-300 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-semibold"
                                    style="background-color: color-mix(in srgb, var(--btn-primary-bg), white 90%); color: var(--btn-primary-bg);">
                                    {{ $loop->iteration }}
                                </div>
                                <span class="text-sm font-medium text-slate-800">{{ $customer['name'] }}</span>
                            </div>
                            @if(!$isViewMode)
                                <button type="button" wire:click="removeRelatedCustomer('{{ $customerId }}')"
                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 p-1 rounded transition-colors">
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