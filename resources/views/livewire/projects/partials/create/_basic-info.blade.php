{{-- Basic Info Card --}}
<div class="theme-card p-6 shadow-sm mb-6">
    <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
        <x-mary-icon name="o-folder" class="w-5 h-5" />
        Proje Bilgileri
    </h3>

    <div class="grid grid-cols-2 gap-8">
        {{-- Name --}}
        <div class="col-span-2">
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Proje Adı *</label>
            @if($isViewMode)
                <div class="text-sm font-medium text-skin-base">{{ $name ?: '-' }}</div>
            @else
                <input type="text" name="project_name" wire:model.blur="name" placeholder="Proje adını girin..."
                    class="input w-full">
                @error('name') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Customer --}}
        @include('livewire.projects.partials.create._client-selection')

        {{-- Leader Removed (Moved to Participants Card) --}}

        {{-- Status --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Durum *</label>
            @if($isViewMode)
                @php $statusLabel = collect($statuses)->firstWhere('id', $status_id)['display_label'] ?? '-'; @endphp
                <div class="text-sm font-medium text-skin-base">{{ $statusLabel }}</div>
            @else
                <select name="status" wire:model="status_id" class="select w-full">
                    @foreach($statuses as $status)
                        <option value="{{ $status['id'] }}">{{ $status['display_label'] }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        {{-- Timezone --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Zaman Dilimi *</label>
            @if($isViewMode)
                <div class="text-sm font-medium text-skin-base">{{ $timezone }}</div>
            @else
                <select name="timezone" wire:model="timezone" class="select w-full">
                    <option value="Europe/Istanbul">İstanbul (UTC+3)</option>
                    <option value="UTC">UTC</option>
                </select>
                @error('timezone') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Project Type --}}
        <div>
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Proje Tipi *</label>
            @if($isViewMode)
                @php $typeLabel = collect($projectTypes)->firstWhere('id', $type_id)['display_label'] ?? '-'; @endphp
                <div class="text-sm font-medium text-skin-base">{{ $typeLabel }}</div>
            @else
                <select name="type_id" wire:model="type_id" class="select w-full">
                    <option value="">Proje Tipi Seçin</option>
                    @foreach($projectTypes as $type)
                        <option value="{{ $type['id'] }}">{{ $type['display_label'] }}</option>
                    @endforeach
                </select>
                @error('type_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- Dates --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-medium opacity-60 text-skin-base">Başlangıç Tarihi *</label>
                @if(!$isViewMode)
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-slate-400">Otomatik</span>
                        <input type="checkbox" wire:model.live="auto_calculate_start_date"
                            class="toggle toggle-xs toggle-success" />
                    </div>
                @endif
            </div>

            @if($isViewMode)
                <div class="text-sm font-medium text-skin-base">
                    {{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d.m.Y') : '-' }}
                </div>
            @else
                <div class="relative">
                    <input type="date" name="start_date" wire:model="start_date" class="input w-full"
                        @if($auto_calculate_start_date) readonly @endif>
                    @if($auto_calculate_start_date)
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <x-mary-icon name="o-lock-closed" class="w-4 h-4 text-slate-400" />
                        </div>
                    @endif
                </div>
                @if($auto_calculate_start_date)
                    <p class="text-[10px] text-slate-400 mt-1">Fazlardan otomatik hesaplanıyor</p>
                @endif
                @error('start_date') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-medium opacity-60 text-skin-base">Hedef Bitiş Tarihi *</label>
                @if(!$isViewMode)
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-slate-400">Otomatik</span>
                        <input type="checkbox" wire:model.live="auto_calculate_end_date"
                            class="toggle toggle-xs toggle-success" />
                    </div>
                @endif
            </div>

            @if($isViewMode)
                <div class="text-sm font-medium text-skin-base">
                    {{ $target_end_date ? \Carbon\Carbon::parse($target_end_date)->format('d.m.Y') : '-' }}
                </div>
            @else
                <div class="relative">
                    <input type="date" name="end_date" wire:model="target_end_date" class="input w-full"
                        @if($auto_calculate_end_date) readonly @endif>
                    @if($auto_calculate_end_date)
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <x-mary-icon name="o-lock-closed" class="w-4 h-4 text-slate-400" />
                        </div>
                    @endif
                </div>
                @if($auto_calculate_end_date)
                    <p class="text-[10px] text-slate-400 mt-1">Fazlardan otomatik hesaplanıyor</p>
                @endif
                @error('target_end_date') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
            @endif
        </div>



        {{-- Description --}}
        <div class="col-span-2">
            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Açıklama</label>
            @if($isViewMode)
                <div class="text-sm text-skin-base whitespace-pre-wrap">{{ $description ?: '-' }}</div>
            @else
                <textarea name="description" wire:model="description" rows="3" class="textarea w-full"
                    placeholder="Proje açıklaması..."></textarea>
            @endif
        </div>
    </div>
</div>