{{--
🚀 CONTACT PERSONAL INFO PARTIAL
---------------------------------------------------------
SORUMLULUK ALANI: Kişinin temel kimlik verileri (Ad, Soyad, Pozisyon) ve kurumsal aidiyeti (Firma).
STATE BAĞLANTISI: $name, $customer_id, $status, $gender, $position, $isViewMode, $customers, $genders.
VALIDASYON ŞERHİ (V10):
- 'name' mandatory (min:2, max:150).
- 'customer_id' mandatory (exists:customers,id).
- 'status' mandatory (in:WORKING,LEFT).
---------------------------------------------------------
--}}
<div class="theme-card p-6 shadow-sm">
    <h2 class="text-base font-bold mb-4 text-skin-heading">Kişi Bilgileri</h2>
    <div class="grid grid-cols-2 gap-6">
        {{-- Firma Seçimi --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Firma Seçin *</label>
            @if($isViewMode)
                @php $customerName = collect($customers)->firstWhere('id', $customer_id)['name'] ?? '-'; @endphp
                <div class="text-sm font-medium">{{ $customerName }}</div>
            @else
                <select wire:model="customer_id" class="select w-full">
                    <option value="">Firma Seçin</option>
                    @foreach($customers as $c)
                        <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                    @endforeach
                </select>
                @error('customer_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Durum --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Durum *</label>
            @if($isViewMode)
                <div class="text-sm font-medium">
                    {{ $status === 'WORKING' ? 'Çalışıyor' : 'Ayrıldı' }}
                </div>
            @else
                <select wire:model="status" class="select w-full">
                    <option value="WORKING">Çalışıyor</option>
                    <option value="LEFT">Ayrıldı</option>
                </select>
                @error('status') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Cinsiyet --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Cinsiyet</label>
            @if($isViewMode)
                @php $genderName = collect($genders)->firstWhere('id', $gender)['name'] ?? '-'; @endphp
                <div class="text-sm font-medium">{{ $genderName }}</div>
            @else
                <select wire:model="gender" class="select w-full">
                    <option value="">Cinsiyet seçin</option>
                    @foreach($genders as $g)
                        <option value="{{ $g['id'] }}">{{ $g['name'] }}</option>
                    @endforeach
                </select>
                @error('gender') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Ad Soyad --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60">Ad Soyad *</label>
            @if($isViewMode)
                <div class="text-sm font-medium">{{ $name }}</div>
            @else
                <input type="text" wire:model="name" placeholder="Kişinin adını ve soyadını girin" class="input w-full">
                @error('name') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Pozisyon --}}
        <div class="col-span-2">
            <label class="block text-xs font-medium mb-1 opacity-60">Pozisyon</label>
            @if($isViewMode)
                <div class="text-sm font-medium">{{ $position ?: '-' }}</div>
            @else
                <input type="text" wire:model="position" placeholder="Örn: Genel Müdür, Pazarlama Uzmanı"
                    class="input w-full">
                @error('position') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>
    </div>
</div>