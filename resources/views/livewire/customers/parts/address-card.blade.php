{{-- Adres Bilgileri Card --}}
<div class="card border p-6 shadow-sm @if($isViewMode) bg-slate-50/50 @endif">
    <h2 class="text-base font-semibold text-slate-800 mb-4">Adres Bilgileri</h2>

    <div class="grid grid-cols-2 gap-8">
        {{-- Ülke --}}
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Ülke</label>
            @if($isViewMode)
                @php
                    $countryName = collect($countries)->firstWhere('id', $country_id)['name'] ?? '-';
                @endphp
                <div class="text-sm font-semibold text-slate-900">{{ $countryName }}</div>
            @else
                <select wire:model="country_id" wire:change="loadCities" class="select w-full">
                    <option value="">Ülke seçin...</option>
                    @foreach($countries as $country)
                        <option value="{{ $country['id'] }}">{{ $country['name'] }}</option>
                    @endforeach
                </select>
                @error('country_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Şehir --}}
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Şehir</label>
            @if($isViewMode)
                @php
                    $cityName = collect($cities)->firstWhere('id', $city_id)['name'] ?? '-';
                @endphp
                <div class="text-sm font-semibold text-slate-900">{{ $cityName }}</div>
            @else
                <select wire:model="city_id" class="select w-full">
                    <option value="">Şehir seçin...</option>
                    @foreach($cities as $city)
                        <option value="{{ $city['id'] }}">{{ $city['name'] }}</option>
                    @endforeach
                </select>
                @error('city_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Adres --}}
        <div class="col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Adres</label>
            @if($isViewMode)
                <div class="text-sm font-semibold text-slate-900">{{ $address ?: '-' }}</div>
            @else
                <textarea wire:model="address" rows="3" placeholder="Detaylı adres bilgisi"
                    class="textarea w-full resize-none"></textarea>
                @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @endif
        </div>
    </div>
</div>