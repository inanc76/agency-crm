<div class="col-span-8 flex flex-col gap-6">
    {{-- Card 1: Genel Bilgiler --}}
    <div class="theme-card p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
            <x-mary-icon name="o-information-circle" class="w-5 h-5" />
            Genel Bilgiler
        </h3>

        <div class="grid grid-cols-2 gap-6">
            {{-- Müşteri --}}
            <div>
                <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Müşteri</label>
                @if($isViewMode)
                    @php $selectedCustomer = collect($customers)->firstWhere('id', $customer_id); @endphp
                    <div class="text-sm font-medium text-skin-base">{{ $selectedCustomer['name'] ?? '-' }}</div>
                @else
                    <select name="customer_id" wire:model.live="customer_id" class="select w-full">
                        <option value="">Müşteri seçin...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer['id'] }}">{{ $customer['name'] }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            {{-- Proje --}}
            <div>
                <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Proje</label>
                @if($isViewMode)
                    @php $selectedProject = collect($projects)->firstWhere('id', $project_id); @endphp
                    <div class="text-sm font-medium text-skin-base">{{ $selectedProject['name'] ?? '-' }}</div>
                @else
                    <select name="project_id" wire:model="project_id" class="select w-full" @if(!$customer_id) disabled @endif>
                        <option value="">{{ $customer_id ? 'Proje seçin...' : 'Önce müşteri seçin' }}</option>
                        @foreach($projects as $project)
                            <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                        @endforeach
                    </select>
                    @if($customer_id && empty($projects))
                        <p class="text-xs text-orange-500 mt-1">Bu müşteriye ait aktif proje bulunamadı.</p>
                    @endif
                @endif
            </div>

            {{-- Kim Atıyor --}}
            <div>
                <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Kim Atıyor</label>
                @php $assignedByUser = collect($users)->firstWhere('id', $assigned_by); @endphp
                @if($isViewMode)
                    <div class="text-sm font-medium text-skin-base">{{ $assignedByUser['name'] ?? '-' }}</div>
                @elseif($isAdmin)
                    <select name="assigned_by" wire:model="assigned_by" class="select w-full">
                        @foreach($users as $user)
                            <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                        @endforeach
                    </select>
                @else
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg">
                        <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-xs font-medium">
                            {{ strtoupper(substr($assignedByUser['name'] ?? 'U', 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-slate-700">{{ $assignedByUser['name'] ?? 'Bilinmiyor' }}</span>
                        <x-mary-icon name="o-lock-closed" class="w-4 h-4 text-slate-400 ml-auto" />
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Bu alan sadece yöneticiler tarafından değiştirilebilir.</p>
                @endif
            </div>

            {{-- Kime Atanıyor --}}
            <div>
                <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Kime Atanıyor</label>
                @if($isViewMode)
                    @php 
                        $assignedNames = collect($users)
                            ->whereIn('id', $assigned_to)
                            ->pluck('name')
                            ->join(', ');
                    @endphp
                    <div class="text-sm font-medium text-skin-base">{{ $assignedNames ?: '-' }}</div>
                @else
                    <x-mary-choices 
                        name="assigned_to"
                        wire:model="assigned_to" 
                        :options="$users" 
                        option-label="name" 
                        option-value="id"
                        searchable
                        class="w-full"
                        no-result-text="Sonuç bulunamadı"
                    />
                    @error('assigned_to') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                @endif
            </div>

            {{-- Priority & Status --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 col-span-2">
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Öncelik</label>
                    @php 
                        $selectedPriority = collect($priorities)->firstWhere('id', $priority_id);
                        $priorityColorId = $selectedPriority['metadata']['color'] ?? 'gray';
                        $priorityClasses = $this->getColorClasses($priorityColorId);
                    @endphp

                    @if($isViewMode)
                        <div class="text-sm font-medium text-skin-base flex items-center gap-2">
                             <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $priorityClasses }}">
                                {{ $selectedPriority['display_label'] ?? '-' }}
                             </span>
                        </div>
                    @else
                        <div class="relative flex items-center">
                            <div class="absolute left-2 w-3 h-3 rounded-full {{ $this->getColorClasses($priorityColorId) }}"></div>
                            <select name="priority_id" wire:model.live="priority_id" class="select w-full pl-8">
                                <option value="">Seçiniz...</option>
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority['id'] }}">
                                        {{ $priority['display_label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Durum</label>
                    @php 
                        $selectedStatus = collect($statuses)->firstWhere('id', $status_id);
                        $statusColorId = $selectedStatus['metadata']['color'] ?? 'gray';
                        $statusClasses = $this->getColorClasses($statusColorId);
                    @endphp

                    @if($isViewMode)
                        <div class="text-sm font-medium text-skin-base flex items-center gap-2">
                             <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClasses }}">
                                {{ $selectedStatus['display_label'] ?? '-' }}
                             </span>
                        </div>
                    @else
                        <div class="relative flex items-center">
                            <div class="absolute left-2 w-3 h-3 rounded-full {{ $this->getColorClasses($statusColorId) }}"></div>
                            <select name="status_id" wire:model.live="status_id" class="select w-full pl-8">
                                <option value="">Seçiniz...</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status['id'] }}">
                                        {{ $status['display_label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Detay --}}
    <div class="theme-card p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
            <x-mary-icon name="o-document-text" class="w-5 h-5" />
            Görev Detayları
        </h3>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1 text-skin-base">Konu</label>
            @if($isViewMode)
                <div class="text-base text-slate-800 font-medium">{{ $name }}</div>
            @else
                <x-mary-input name="title" wire:model.live="name" placeholder="Görev konusunu girin..." class="input-sm" />
            @endif
        </div>

        <label class="block text-sm font-medium mb-1 text-skin-base">Detay</label>
        @if($isViewMode)
            <div class="prose prose-sm max-w-none text-slate-700">
                {!! $description ?: '<span class="text-slate-400 italic">Henüz detay eklenmedi.</span>' !!}
            </div>
        @else
            <textarea name="description" wire:model="description" rows="6" 
                class="textarea w-full border-slate-200"
                placeholder="Görev detaylarını buraya yazın..."></textarea>
        @endif
    </div>

    {{-- Card 3: Checklist --}}
    <div class="theme-card p-6 shadow-sm min-h-[400px]">
         @if($task && $task->exists)
            <livewire:projects.tasks.partials.checklist :task="$task" />
         @else
            <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
                <x-mary-icon name="o-list-bullet" class="w-5 h-5" />
                Öğeler
            </h3>
            <div class="text-center py-8 border-2 border-dashed border-slate-200 rounded-xl">
                <x-mary-icon name="o-queue-list" class="w-12 h-12 mx-auto mb-3 text-slate-300" />
                <p class="text-slate-500 mb-2">Öğe eklemek için önce kaydedin</p>
                <p class="text-xs text-slate-400">Görev oluşturulduktan sonra liste öğeleri ekleyebilirsiniz.</p>
            </div>
         @endif
    </div>
</div>
