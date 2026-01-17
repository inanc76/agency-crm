{{-- Participants Card --}}
<div class="theme-card p-6 shadow-sm mb-6">
    <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
        <x-mary-icon name="o-users" class="w-5 h-5" />
        Katılımcılar
    </h3>
    
    <div class="grid grid-cols-1 gap-6">
        {{-- Leader --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Proje Lideri <span class="text-red-500">*</span></label>
            @if($isViewMode)
                @php 
                    $leader = collect($leaders)->firstWhere('id', $leader_id);
                    $leaderName = $leader ? $leader['name'] : '-';
                @endphp
                <div class="text-sm font-medium text-skin-base">{{ $leaderName }}</div>
            @else
                <select name="leader_id" wire:model="leader_id" class="select w-full">
                    <option value="">Proje Lideri Seçin</option>
                    @foreach($leaders as $leader)
                        <option value="{{ $leader['id'] }}">{{ $leader['name'] }}</option>
                    @endforeach
                </select>
                @error('leader_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Team Members --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Proje Üyeleri</label>
            @if($isViewMode)
                @php 
                    $memberNames = collect($leaders)
                        ->whereIn('id', $team_members)
                        ->pluck('name')
                        ->join(', ');
                @endphp
                <div class="text-sm font-medium text-skin-base">{{ $memberNames ?: '-' }}</div>
            @else
                <x-mary-choices 
                    wire:model="team_members" 
                    :options="$leaders" 
                    option-label="name" 
                    option-value="id"
                    searchable
                    class="w-full"
                    no-result-text="Aktif kullanıcı bulunamadı"
                    placeholder="Proje üyelerini seçin"
                />
                <div class="text-xs text-gray-500 mt-1">
                    Sadece aktif kullanıcılar listelenmektedir
                </div>
            @endif
        </div>
    </div>
</div>
