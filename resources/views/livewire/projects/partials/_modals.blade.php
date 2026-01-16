{{-- Phase Modal --}}
<x-mary-modal wire:model="phaseModalOpen" title="{{ $editingPhaseIndex !== null ? 'Faz Düzenle' : 'Yeni Faz Ekle' }}" class="backdrop-blur-sm">
    <div class="grid gap-4">
        {{-- Status Selection Removed (Auto-Calculated) --}}

        <div>
            <label class="block text-sm font-medium mb-1">Faz Adı <span class="text-red-500">*</span></label>
            <input type="text" wire:model="phaseForm.name" placeholder="Örn: Planlama Aşaması" 
                   class="input w-full bg-white border-slate-300" />
            @error('phaseForm.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        
        <div>
            <label class="block text-sm font-medium mb-1">Açıklama</label>
            <textarea wire:model="phaseForm.description" placeholder="Faz hakkında kısa açıklama..." 
                      class="textarea w-full bg-white border-slate-300" rows="3"></textarea>
        </div>
        
        <div class="bg-blue-50 p-3 rounded-lg flex items-start gap-3 text-sm text-blue-700">
            <x-mary-icon name="o-information-circle" class="w-5 h-5 flex-shrink-0 mt-0.5" />
            <div>
                <span class="font-bold">Otomatik Hesaplama:</span>
                <p class="mt-1 opacity-90">Fazın başlangıç/bitiş tarihleri ve durumu, altına ekleyeceğiniz modüllerden otomatik olarak hesaplanacaktır.</p>
            </div>
        </div>
    </div>
    
    <x-slot:actions>
        <button class="btn btn-ghost" wire:click="$set('phaseModalOpen', false)">İptal</button>
        <button class="theme-btn-save" wire:click="savePhase">{{ $editingPhaseIndex !== null ? 'Güncelle' : 'Ekle' }}</button>
    </x-slot:actions>
</x-mary-modal>

{{-- Module Modal --}}
<x-mary-modal wire:model="moduleModalOpen" title="{{ $editingModuleIndex !== null ? 'Modül Düzenle' : 'Yeni Modül Ekle' }}" class="backdrop-blur-sm">
    <div class="grid gap-4">
        {{-- Status --}}
        <div>
             <label class="block text-sm font-medium mb-1">Durum <span class="text-red-500">*</span></label>
             <select wire:model="moduleForm.status_id" class="select w-full bg-white border-slate-300">
                 <option value="">Lütfen Seçiniz</option>
                 @foreach($moduleStatuses as $status)
                     <option value="{{ $status['id'] }}">{{ $status['display_label'] }}</option>
                 @endforeach
             </select>
             @error('moduleForm.status_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        {{-- Info --}}
        <div>
            <label class="block text-sm font-medium mb-1">Modül Adı <span class="text-red-500">*</span></label>
            <input type="text" wire:model="moduleForm.name" placeholder="Örn: Login Ekranı Tasarımı" 
                   class="input w-full bg-white border-slate-300" />
            @error('moduleForm.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        
        <div>
            <label class="block text-sm font-medium mb-1">Açıklama</label>
            <textarea wire:model="moduleForm.description" placeholder="Modül detayları..." 
                      class="textarea w-full bg-white border-slate-300" rows="2"></textarea>
        </div>

        {{-- Date Picker --}}
        <div wire:ignore>
            <label class="block text-sm font-medium mb-1">Zaman Planı</label>
            <x-date-range-picker 
                :startDate="$moduleForm['start_date'] ?? null" 
                :endDate="$moduleForm['end_date'] ?? null" 
                startWireModel="moduleForm.start_date"
                endWireModel="moduleForm.end_date"
                eventKey="module_modal"
            />
        </div>

        {{-- Estimated Hours --}}
        <div class="border-t border-slate-100 pt-3 mt-1">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium">Atanan Çalışma Saati</label>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500">Sınırsız</span>
                    <input type="checkbox" wire:model.live="moduleForm.is_unlimited" class="toggle toggle-sm toggle-primary" />
                </div>
            </div>

            @if(!$moduleForm['is_unlimited'])
                <div class="flex items-center gap-2 animate-in slide-in-from-left duration-200">
                    <div class="flex-1">
                        <input type="number" wire:model="moduleForm.estimated_hours" 
                               placeholder="Örn: 20" 
                               min="0" max="200"
                               class="input w-full bg-white border-slate-300 select-sm h-10" />
                    </div>
                    <span class="text-sm font-medium text-slate-500">Saat</span>
                </div>
                @error('moduleForm.estimated_hours') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            @endif
        </div>

        {{-- ACL: Participants --}}
        <div class="border-t border-slate-100 pt-3 mt-1">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Yetkili Kişiler</label>
            <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                @php
                    // Combine Leader and Team Members for the list
                    // Use optional helper or null coalescence to avoid errors if vars undefined in partial scope (should be fine as shared scope)
                    $team = $team_members ?? [];
                    $leader = $leader_id ?? null;
                    $allParticipants = collect($leaders ?? [])->whereIn('id', array_merge($team, [$leader]))->values();
                @endphp

                @forelse($allParticipants as $participant)
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-slate-50 p-1.5 rounded-lg transition-colors">
                        <input type="checkbox" value="{{ $participant['id'] }}" wire:model="moduleForm.assigned_users" 
                               class="checkbox checkbox-xs checkbox-primary rounded" />
                        <span class="text-sm text-slate-700">{{ $participant['name'] }}</span>
                    </label>
                @empty
                    <p class="text-xs text-slate-400 italic">Projeye atanmış katılımcı yok.</p>
                @endforelse
            </div>
            <p class="text-[10px] text-slate-400 mt-1">* İşaretli olmayan kullanıcılar bu modülü göremez.</p>
        </div>
    </div>
    
    <x-slot:actions>
        <button class="btn btn-ghost" wire:click="$set('moduleModalOpen', false)">İptal</button>
        <button class="theme-btn-save" wire:click="saveModule">{{ $editingModuleIndex !== null ? 'Güncelle' : 'Ekle' }}</button>
    </x-slot:actions>
</x-mary-modal>
