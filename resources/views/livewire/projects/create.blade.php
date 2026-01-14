<?php
/**
 * 🏗️ PROJECT CREATE/EDIT COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Single File Component)
 *
 * HİYERARŞİ:
 *  - Ana Dosya: Layout, State ve Form yönetimi
 *  - Partials: Form kartları
 * ---------------------------------------------------------
 */

use App\Models\Customer;
use App\Models\Project;
use App\Models\ReferenceItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new
    #[Layout('components.layouts.app', ['title' => 'Yeni Proje Oluştur'])]
    class extends Component
    {
        use Toast;

        // Temel Bilgiler
        public string $name = '';

        public string $description = '';

        public string $customer_id = '';

        public string $leader_id = '';

        public string $status_id = '';

        public string $timezone = 'Europe/Istanbul';

        // Tarihler
        public ?string $start_date = null;

        public ?string $target_end_date = null;

        // State
        public bool $isViewMode = false;

        public ?string $projectId = null;

        // Reference Data
        public $customers = [];

        public $leaders = [];

        public $statuses = [];

        public ?array $selectedCustomer = null;

        // External User
        public bool $inviteExternalUser = false;

        public string $externalUserEmail = '';

        public string $externalUserName = '';

        // Hierarchical Form - Phases & Modules
        public array $phases = [];
        
        // Phase Modal State
        public bool $phaseModalOpen = false;
        
        public array $phaseForm = [
            'name' => '',
            'description' => ''
        ];

        public function mount(?string $project = null): void
        {
            $this->loadReferenceData();

            if ($project) {
                $this->projectId = $project;
                $this->loadProjectData();
            } else {
                // Varsayılan durum ata
                $defaultStatus = ReferenceItem::where('category_key', 'PROJECT_STATUS')
                    ->where('is_default', true)
                    ->first();
                if ($defaultStatus) {
                    $this->status_id = $defaultStatus->id;
                }
            }
        }

        private function loadReferenceData(): void
        {
            $this->customers = Customer::orderBy('name')
                ->get(['id', 'name', 'logo_url'])
                ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'logo_url' => $c->logo_url])
                ->toArray();

            $this->leaders = User::orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])
                ->toArray();

            $this->statuses = ReferenceItem::where('category_key', 'PROJECT_STATUS')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'display_label', 'key'])
                ->toArray();
        }

        private function loadProjectData(): void
        {
            try {
                $project = Project::with(['customer', 'status', 'leader', 'phases.modules'])->findOrFail($this->projectId);

                $this->name = $project->name;
                $this->description = $project->description ?? '';
                $this->customer_id = $project->customer_id ?? '';
                $this->leader_id = $project->leader_id ?? '';
                $this->status_id = $project->status_id ?? '';
                $this->timezone = $project->timezone ?? 'Europe/Istanbul';
                $this->start_date = $project->start_date?->format('Y-m-d');
                $this->target_end_date = $project->target_end_date?->format('Y-m-d');

                if ($project->customer) {
                    $this->selectedCustomer = [
                        'id' => $project->customer->id,
                        'name' => $project->customer->name,
                        'logo_url' => $project->customer->logo_url,
                    ];
                }

                // Load phases and modules
                $this->phases = $project->phases->map(function ($phase) {
                    return [
                        'id' => $phase->id,
                        'name' => $phase->name,
                        'start_date' => $phase->start_date?->format('Y-m-d'),
                        'end_date' => $phase->end_date?->format('Y-m-d'),
                        'color' => $phase->custom_fields['color'] ?? '#3b82f6',
                        'modules' => $phase->modules->map(function ($module) {
                            return [
                                'id' => $module->id,
                                'name' => $module->name,
                            ];
                        })->toArray(),
                    ];
                })->toArray();

                $this->isViewMode = true;
            } catch (\Exception $e) {
                $this->error('Proje Bulunamadı', 'İstenilen proje kaydı bulunamadı.');
                $this->redirect('/dashboard/projects', navigate: true);
            }
        }

        public function updatedCustomerId($value): void
        {
            if ($value) {
                $customer = Customer::find($value);
                if ($customer) {
                    $this->selectedCustomer = [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'logo_url' => $customer->logo_url,
                    ];
                }
            } else {
                $this->selectedCustomer = null;
            }
        }

        // ─────────────────────────────────────────────────────────────────────────
        // HIERARCHICAL FORM METHODS
        // ─────────────────────────────────────────────────────────────────────────

        public function savePhase(): void
        {
            $this->validate(['phaseForm.name' => 'required|string|max:255']);

             $colors = ['#3b82f6', '#14b8a6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
             $colorIndex = count($this->phases) % count($colors);

             $this->phases[] = [
                 'id' => (string) Str::uuid(), // Ensure ID is generated for keying
                 'name' => $this->phaseForm['name'],
                 'description' => $this->phaseForm['description'],
                 'start_date' => null,
                 'end_date' => null,
                 'color' => $colors[$colorIndex],
                 'modules' => [],
             ];
             
             $this->phaseModalOpen = false;
             $this->success('Faz Eklendi', 'Yeni faz listeye eklendi.');
        }

        public function openPhaseModal(): void
        {
            if (count($this->phases) >= 20) {
                $this->error('Sınır Aşıldı', 'Bir projeye en fazla 20 faz eklenebilir.');
                return;
            }
            
            $this->phaseForm = [
                'name' => '',
                'description' => ''
            ];
            
            $this->phaseModalOpen = true;
        }

        public function removePhase(int $index): void
        {
            unset($this->phases[$index]);
            $this->phases = array_values($this->phases);
        }

        public function addModule(int $phaseIndex): void
        {
            $this->phases[$phaseIndex]['modules'][] = [
                'name' => '',
            ];
        }

        public function removeModule(int $phaseIndex, int $moduleIndex): void
        {
            unset($this->phases[$phaseIndex]['modules'][$moduleIndex]);
            $this->phases[$phaseIndex]['modules'] = array_values($this->phases[$phaseIndex]['modules']);
        }

        // ─────────────────────────────────────────────────────────────────────────
        // STATE MANAGEMENT
        // ─────────────────────────────────────────────────────────────────────────

        public function toggleEdit(): void
        {
            $this->isViewMode = ! $this->isViewMode;
        }

        public function save(): void
        {
            $rules = [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'customer_id' => 'required|exists:customers,id',
                'leader_id' => 'nullable|exists:users,id',
                'status_id' => 'required|exists:reference_items,id',
                'timezone' => 'required|string',
                'start_date' => 'nullable|date',
                'target_end_date' => 'nullable|date|after_or_equal:start_date',
            ];

            // External user validation (only for new projects or if switch is toggled and not yet attached)
            if ($this->inviteExternalUser) {
                // Check if user already exists
                $existingUser = User::where('email', $this->externalUserEmail)->first();
                if (! $existingUser) {
                    $rules['externalUserEmail'] = 'required|email|unique:users,email';
                    $rules['externalUserName'] = 'required|string|max:255';
                }
            }

            $validated = $this->validate($rules);

            try {
                DB::transaction(function () use ($validated) {
                    if ($this->projectId) {
                        $project = Project::findOrFail($this->projectId);
                        $project->update($validated);
                        $this->success('Proje Güncellendi', 'Proje başarıyla güncellendi.');
                    } else {
                        $project = Project::create($validated);
                        $this->projectId = $project->id;
                    }

                    // 1. External User Logic
                    if ($this->inviteExternalUser && $this->externalUserEmail) {
                        $externalUser = User::where('email', $this->externalUserEmail)->first();

                        if (! $externalUser) {
                            $guestRole = \App\Models\Role::firstOrCreate(
                                ['name' => 'guest'],
                                ['description' => 'External Guest User']
                            );

                            $externalUser = User::create([
                                'name' => $this->externalUserName,
                                'email' => $this->externalUserEmail,
                                'password' => bcrypt(\Str::random(16)),
                                'role_id' => $guestRole->id,
                                'custom_fields' => ['is_external' => true],
                            ]);
                        }

                        // Attach if not already attached
                        if (! $project->users()->where('users.id', $externalUser->id)->exists()) {
                            $project->users()->attach($externalUser->id, ['role' => 'external']);
                        }
                    }

                    // 2. Hierarchical Form Sync (Phases & Modules)
                    $existingPhaseIds = $project->phases()->pluck('id')->toArray();
                    $submittedPhaseIds = collect($this->phases)->pluck('id')->filter()->toArray();

                    // Delete removed phases
                    $phasesToDelete = array_diff($existingPhaseIds, $submittedPhaseIds);
                    if (! empty($phasesToDelete)) {
                        $project->phases()->whereIn('id', $phasesToDelete)->delete();
                    }

                    foreach ($this->phases as $phaseIndex => $phaseData) {
                        if (empty($phaseData['name'])) {
                            continue;
                        }

                        $phase = $project->phases()->updateOrCreate(
                            ['id' => $phaseData['id'] ?? null],
                            [
                                'name' => $phaseData['name'],
                                'start_date' => $phaseData['start_date'] ?? null,
                                'end_date' => $phaseData['end_date'] ?? null,
                                'custom_fields' => ['color' => $phaseData['color'] ?? null],
                                'order' => $phaseIndex + 1,
                            ]
                        );

                        // Sync Modules for this phase
                        $existingModuleIds = $phase->modules()->pluck('id')->toArray();
                        $submittedModuleIds = collect($phaseData['modules'] ?? [])->pluck('id')->filter()->toArray();

                        // Delete removed modules
                        $modulesToDelete = array_diff($existingModuleIds, $submittedModuleIds);
                        if (! empty($modulesToDelete)) {
                            $phase->modules()->whereIn('id', $modulesToDelete)->delete();
                        }

                        foreach ($phaseData['modules'] ?? [] as $moduleIndex => $moduleData) {
                            if (empty($moduleData['name'])) {
                                continue;
                            }

                            $phase->modules()->updateOrCreate(
                                ['id' => $moduleData['id'] ?? null],
                                [
                                    'name' => $moduleData['name'],
                                    'order' => $moduleIndex + 1,
                                ]
                            );
                        }
                    }

                    if (! $this->projectId) {
                        $this->success('Proje Oluşturuldu', 'Yeni proje başarıyla oluşturuldu.');
                        $this->redirect(route('projects.edit', $this->projectId), navigate: true);
                    }
                });

                $this->isViewMode = true;
                // Reload data to refresh IDs and state
                $this->loadProjectData();
            } catch (\Exception $e) {
                $this->error('Hata', 'Proje kaydedilirken bir hata oluştu: '.$e->getMessage());
            }
        }

        public function delete(): void
        {
            if (! $this->projectId) {
                return;
            }

            try {
                $project = Project::findOrFail($this->projectId);
                $project->delete();

                $this->success('Proje Silindi', 'Proje başarıyla silindi.');
                $this->redirect('/dashboard/projects', navigate: true);
            } catch (\Exception $e) {
                $this->error('Hata', 'Proje silinirken bir hata oluştu.');
            }
        }
    }; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="/dashboard/projects"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Proje Listesi</span>
        </a>

        {{-- Header with Action Buttons --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
                    @if($isViewMode)
                        {{ $name ?: 'Proje Detayı' }}
                    @elseif($projectId)
                        Düzenle: {{ $name }}
                    @else
                        Yeni Proje Oluştur
                    @endif
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($projectId)
                        <span class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--dropdown-hover-bg)] text-[var(--color-text-base)] border border-[var(--card-border)]">Proje</span>
                        <span class="text-[11px] font-mono text-[var(--color-text-muted)]">Kod: {{ Project::find($projectId)?->project_id_code }}</span>
                    @else
                        <p class="text-sm opacity-60 text-skin-base">
                            Yeni proje bilgilerini girin
                        </p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($isViewMode)
                    {{-- View Mode Actions --}}
                    @if($projectId)
                        <button type="button" wire:click="delete" wire:confirm="Bu projeyi silmek istediğinize emin misiniz?"
                            wire:key="btn-delete-{{ $projectId }}"
                            class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                            <x-mary-icon name="o-trash" class="w-4 h-4" />
                            Sil
                        </button>
                    @endif
                    <button type="button" wire:click="toggleEdit"
                        wire:key="btn-edit-{{ $projectId }}"
                        class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                        Düzenle
                    </button>
                @else
                    {{-- Edit Mode Actions --}}
                    <button type="button" wire:click="{{ $projectId ? 'toggleEdit' : '' }}" 
                            wire:key="btn-cancel-{{ $projectId ?: 'new' }}"
                            @if(!$projectId) onclick="window.location.href='/dashboard/projects'" @endif
                        class="theme-btn-cancel px-4 py-2 text-sm">
                        İptal
                    </button>
                    <button type="button" wire:click="save" wire:loading.attr="disabled"
                        wire:key="btn-save-{{ $projectId ?: 'new' }}"
                        class="theme-btn-save flex items-center gap-2 px-4 py-2 text-sm">
                        <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                        <x-mary-icon name="o-check" class="w-4 h-4" />
                        @if($projectId) Güncelle @else Kaydet @endif
                    </button>
                @endif
            </div>
        </div>

        {{-- Main Layout: 8/12 Left, 4/12 Right --}}
        <div class="grid grid-cols-12 gap-6">
            {{-- Left Column (8/12) --}}
            <div class="col-span-8 flex flex-col gap-6">
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
                                <input type="text" wire:model.blur="name" placeholder="Proje adını girin..." class="input w-full">
                                @error('name') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        {{-- Customer --}}
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Müşteri *</label>
                            @if($isViewMode)
                                <div class="text-sm font-medium text-skin-base">{{ $selectedCustomer['name'] ?? '-' }}</div>
                            @else
                                <select wire:model.live="customer_id" class="select w-full">
                                    <option value="">Müşteri Seçin</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer['id'] }}">{{ $customer['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        {{-- Leader --}}
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Proje Lideri</label>
                            @if($isViewMode)
                                @php $leaderName = collect($leaders)->firstWhere('id', $leader_id)['name'] ?? '-'; @endphp
                                <div class="text-sm font-medium text-skin-base">{{ $leaderName }}</div>
                            @else
                                <select wire:model="leader_id" class="select w-full">
                                    <option value="">Proje Lideri Seçin</option>
                                    @foreach($leaders as $leader)
                                        <option value="{{ $leader['id'] }}">{{ $leader['name'] }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Durum *</label>
                            @if($isViewMode)
                                @php $statusLabel = collect($statuses)->firstWhere('id', $status_id)['display_label'] ?? '-'; @endphp
                                <div class="text-sm font-medium text-skin-base">{{ $statusLabel }}</div>
                            @else
                                <select wire:model="status_id" class="select w-full">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status['id'] }}">{{ $status['display_label'] }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        {{-- Timezone (Hidden or single row if needed, but let's keep grid) --}}
                        <div>
                             <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Zaman Dilimi</label>
                             @if($isViewMode)
                                <div class="text-sm font-medium text-skin-base">{{ $timezone }}</div>
                             @else
                                <select wire:model="timezone" class="select w-full">
                                    <option value="Europe/Istanbul">İstanbul (UTC+3)</option>
                                    <option value="UTC">UTC</option>
                                </select>
                             @endif
                        </div>

                        {{-- Description --}}
                        <div class="col-span-2">
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Açıklama</label>
                            @if($isViewMode)
                                <div class="text-sm text-skin-base whitespace-pre-wrap">{{ $description ?: '-' }}</div>
                            @else
                                <textarea wire:model="description" rows="3" class="textarea w-full"
                                    placeholder="Proje açıklaması..."></textarea>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Dates Card --}}
                <div class="theme-card p-6 shadow-sm mb-6">
                    <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
                        <x-mary-icon name="o-calendar-days" class="w-5 h-5" />
                        Tarih Aralığı
                    </h3>

                    {{-- Linear-Style Date Range Picker --}}
                    <x-date-range-picker :startDate="$start_date" :endDate="$target_end_date" :disabled="$isViewMode" />
                    @error('target_end_date') <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- External User Card --}}
                <div class="theme-card p-6 shadow-sm mb-6">
                    <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
                        <x-mary-icon name="o-user-plus" class="w-5 h-5" />
                        Dış Katılımcı
                    </h3>

                    {{-- Toggle Switch --}}
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="font-medium text-[var(--color-text-heading)]">Dış Katılımcı Davet Et</div>
                            <div class="text-sm text-slate-500">Projeye harici kullanıcı ekleyin</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="inviteExternalUser" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[var(--primary-color)]"></div>
                        </label>
                    </div>

                    {{-- External User Form (Conditional) --}}
                    @if($inviteExternalUser)
                    <div class="grid grid-cols-2 gap-8 pt-4 border-t border-slate-100" wire:key="external-user-form">
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Ad Soyad *</label>
                            <input type="text"
                                wire:model="externalUserName" 
                                placeholder="Örn: Ahmet Yılmaz"
                                class="input w-full"
                            />
                            @error('externalUserName') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">E-posta Adresi *</label>
                            <input type="email"
                                wire:model="externalUserEmail" 
                                placeholder="ornek@firma.com"
                                class="input w-full"
                            />
                            @error('externalUserEmail') <span class="text-[var(--color-danger)] text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2 flex items-center gap-2 p-3 rounded-lg bg-blue-50 border border-blue-100">
                            <x-mary-icon name="o-information-circle" class="w-5 h-5 text-blue-600" />
                            <span class="text-sm text-blue-700">Kullanıcı kaydedilirken otomatik olarak oluşturulur ve projeye eklenir.</span>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Hierarchical Form Card --}}
                <div class="theme-card p-6 shadow-sm mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-[var(--color-text-heading)] flex items-center gap-2">
                            <x-mary-icon name="o-squares-2x2" class="w-5 h-5" />
                            Proje Hiyerarşisi
                        </h3>
                        @if(!$isViewMode)
                        <button wire:click="openPhaseModal" 
                                class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 cursor-pointer transition-all text-slate-700">
                            <x-mary-icon name="o-plus" class="w-4 h-4" />
                            Faz Ekle
                        </button>
                        @endif
                    </div>

                    {{-- Phases List --}}
                    <div class="space-y-4">
                        @forelse($phases as $index => $phase)
                            @include('livewire.projects.parts._phase-form', [
                                'index' => $index,
                                'phase' => $phase,
                                'isViewMode' => $isViewMode
                            ])
                        @empty
                            <div class="text-center py-8 border-2 border-dashed border-slate-200 rounded-xl">
                                <x-mary-icon name="o-cube-transparent" class="w-12 h-12 mx-auto mb-3 text-slate-300" />
                                <p class="text-slate-500 mb-2">Henüz faz eklenmedi</p>
                                <p class="text-xs text-slate-400">"Faz Ekle" butonuna tıklayarak proje aşamalarını tanımlayın</p>
                            </div>
                        @endforelse
                    </div>
                </div>



            </div>

            {{-- Right Column (4/12) --}}
            <div class="col-span-4 flex flex-col gap-6" wire:key="customer-preview-{{ $customer_id }}">
                {{-- Customer Logo Card --}}
                <div class="theme-card p-6 shadow-sm mb-6">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">Müşteri</h3>

                    @if($selectedCustomer)
                        <div class="text-center">
                            @if($selectedCustomer['logo_url'])
                                <img src="{{ str_contains($selectedCustomer['logo_url'], '/storage/') ? $selectedCustomer['logo_url'] : asset('storage' . $selectedCustomer['logo_url']) }}" alt="{{ $selectedCustomer['name'] }}"
                                    class="w-24 h-24 rounded-xl object-cover mx-auto mb-3 shadow-sm" />
                            @else
                                <div class="w-24 h-24 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                    <x-mary-icon name="o-building-office" class="w-10 h-10 text-slate-400" />
                                </div>
                            @endif
                            <div class="font-semibold text-[var(--color-text-heading)]">{{ $selectedCustomer['name'] }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-mary-icon name="o-building-office-2" class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                            <p class="text-sm text-slate-500">Müşteri seçilmedi</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Phase Modal --}}
    <x-mary-modal wire:model="phaseModalOpen" title="Yeni Faz Ekle" class="backdrop-blur-sm">
        <div class="grid gap-4">
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
            <button class="btn btn-primary" wire:click="savePhase">Ekle</button>
        </x-slot:actions>
    </x-mary-modal>
</div>