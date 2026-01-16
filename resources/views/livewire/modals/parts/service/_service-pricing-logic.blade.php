{{--
🚀 SERVICE PRICING LOGIC PARTIAL
---------------------------------------------------------
NEDEN: Dinamik hizmet kalemlerinin (services loop), kategori/hizmet seçimlerinin ve fiyatlandırma mantığının yönetildiği
teknik blok.
BAĞLANTILAR:
- $services (array - Döngü ve state yönetimi)
- addService() (Hizmet satırı ekler)
- removeService($index) (Hizmet satırını siler)
- services.*.category (wire:model.live - Kategori değişince hizmet listesi filtrelenir)
- services.*.service_name, services.*.status, services.*.service_price
VALIDASYON (V10):
- services.*.category: required (Referans listesinden olmalı)
- services.*.service_name: required (Seçili kategoriye ait olmalı)
- services.*.service_price: numeric, min:0 (Negatif fiyat olamaz)
- services.*.status: in:ACTIVE,PASSIVE,CANCELLED
---------------------------------------------------------
--}}
<div>
    {{-- Hizmet Bilgileri Cards (Dynamic) --}}
    @foreach($services as $index => $service)
        <div class="theme-card p-6 shadow-sm border border-green-100 bg-green-50/50 mb-6" wire:key="service-{{ $index }}">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-skin-heading">
                    Hizmet Bilgileri @if(count($services) > 1) #{{ $index + 1 }} @endif
                </h2>
                @if(!$isViewMode && $index > 0)
                    <button type="button" wire:click="removeService({{ $index }})"
                        class="text-[var(--color-danger)] hover:opacity-80 text-xs font-medium flex items-center gap-1">
                        <x-mary-icon name="o-x-mark" class="w-4 h-4" />
                        Kaldır
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-6">
                {{-- Kategori --}}
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60">Kategori *</label>
                    @if($isViewMode)
                        <div class="text-sm font-medium">
                            @php
                                $catLabel = collect($categories)->firstWhere('key', $service['category'])['label'] ?? $service['category'];
                            @endphp
                            {{ $catLabel }}
                        </div>
                    @else
                        <select wire:model.live="services.{{ $index }}.category" class="select w-full">
                            <option value="">Kategori Seçin</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat['key'] }}">{{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                        @error("services.{$index}.category") <span
                        class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                {{-- Hizmet Adı --}}
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60">Hizmet *</label>
                    @if($isViewMode)
                        <div class="text-sm font-medium">
                            {{ $service['service_name'] }}
                        </div>
                    @else
                        <select wire:model.live="services.{{ $index }}.service_name" class="select w-full"
                            @if(empty($service['category'])) disabled @endif>
                            <option value="">
                                {{ !empty($service['category']) ? 'Hizmet seçin' : 'Kategori seçin' }}
                            </option>
                            @foreach($service['services_list'] ?? [] as $s)
                                <option value="{{ $s['name'] }}">{{ $s['name'] }}</option>
                            @endforeach
                        </select>
                        @error("services.{$index}.service_name") <span
                        class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                {{-- Durum --}}
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60">Durum *</label>
                    @if($isViewMode)
                        <div class="badge {{ $service['status'] === 'ACTIVE' ? 'badge-success' : 'badge-ghost' }} gap-2">
                            {{ $service['status'] === 'ACTIVE' ? 'Aktif' : 'Pasif' }}
                        </div>
                    @else
                        <select wire:model="services.{{ $index }}.status" class="select w-full">
                            <option value="ACTIVE">Aktif</option>
                            <option value="PASSIVE">Pasif</option>
                        </select>
                    @endif
                </div>

                {{-- Özel Fiyat --}}
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60">Özel Fiyat (Opsiyonel)</label>
                    @if($isViewMode)
                        <div class="text-sm font-medium">
                            {{ number_format($service['service_price'], 2, ',', '.') }}
                            {{ $service['service_currency'] }}
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <input type="number" step="0.01" wire:model="services.{{ $index }}.service_price"
                                class="input w-full">
                            <span class="text-sm font-bold text-slate-500">{{ $service['service_currency'] }}</span>
                        </div>
                    @endif
                </div>

                {{-- Açıklama --}}
                <div class="col-span-2">
                    <label class="block text-xs font-medium mb-1 opacity-60">Açıklama</label>
                    @if($isViewMode)
                        <div class="text-sm font-medium">
                            {{ $service['description'] ?: '-' }}
                        </div>
                    @else
                        <textarea wire:model="services.{{ $index }}.description" class="textarea w-full"
                            placeholder="Açıklama..."></textarea>
                    @endif
                </div>

                {{-- Proje Seçimi --}}
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60">Proje Adı</label>
                    @if($isViewMode)
                        <div class="text-sm font-medium">
                            @if(!empty($service['project_id']))
                                <a href="/dashboard/projects/{{ $service['project_id'] }}"
                                    class="text-skin-primary hover:text-skin-heading underline transition-colors" wire:navigate>
                                    {{ collect($projects)->firstWhere('id', $service['project_id'])['name'] ?? '-' }}
                                </a>
                            @else
                                {{ collect($projects)->firstWhere('id', $service['project_id'])['name'] ?? '-' }}
                            @endif
                        </div>
                    @else
                        <select wire:model.live="services.{{ $index }}.project_id" class="select w-full">
                            <option value="">Proje Seçin (Opsiyonel)</option>
                            @foreach($projects as $project)
                                <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60">Proje Fazı</label>
                    @if($isViewMode)
                        <div class="text-sm font-medium">
                            {{ collect($service['phases_list'])->firstWhere('id', $service['project_phase_id'])['name'] ?? '-' }}
                        </div>
                    @else
                        <select wire:model="services.{{ $index }}.project_phase_id" class="select w-full"
                            @if(empty($service['project_id'])) disabled @endif>
                            <option value="">
                                {{ !empty($service['project_id']) ? 'Faz seçin (Opsiyonel)' : 'Önce proje seçin' }}
                            </option>
                            @foreach($service['phases_list'] ?? [] as $phase)
                                <option value="{{ $phase['id'] }}">{{ $phase['name'] }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    {{-- Add Service Button --}}
    @if(!$isViewMode && count($services) < 5)
        <button type="button" wire:click="addService"
            class="w-full theme-card p-4 shadow-sm border-2 border-dashed border-slate-300 hover:border-slate-400 transition-colors flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900">
            <x-mary-icon name="o-plus-circle" class="w-5 h-5" />
            <span class="font-bold text-sm" style="color: var(--action-link-color);">+ Hizmet Ekle</span>
        </button>
    @endif
</div>