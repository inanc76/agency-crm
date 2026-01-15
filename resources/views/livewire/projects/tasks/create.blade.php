<?php
/**
 * ✅ TASK CREATE/EDIT COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Class-Based API)
 *
 * Görev oluşturma ve düzenleme formu.
 * ---------------------------------------------------------
 */

use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ReferenceItem;
use App\Models\User;
use Livewire\Volt\Component;

new class extends Component
{
    // Route Model Binding
    public ?ProjectTask $task = null;

    // Form State
    public ?string $customer_id = null;

    public ?string $project_id = null;

    public ?string $assigned_by = null;

    public ?string $assigned_to = null;

    public ?string $priority_id = null;

    // Dropdowns
    public array $customers = [];

    public array $projects = [];

    public array $users = [];

    public array $priorities = [];

    // UI State
    public bool $isViewMode = false;

    public bool $isAdmin = false;

    public function mount(?ProjectTask $task = null): void
    {
        $this->task = $task;
        $this->isAdmin = auth()->user()?->role?->name === 'admin';
        $this->assigned_by = auth()->id();

        // Load Customers
        $this->customers = Customer::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        // Load CRM Users
        $this->users = User::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        // Load Priorities from ReferenceData
        $this->priorities = ReferenceItem::query()
            ->where('category_key', 'TASK_PRIORITY')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'key', 'display_label'])
            ->toArray();

        // Set default priority to "Normal"
        $normalPriority = collect($this->priorities)->firstWhere('key', 'NORMAL');
        $this->priority_id = $normalPriority['id'] ?? null;

        // If editing existing task
        if ($this->task?->id) {
            $this->loadTaskData();
        }
    }

    public function loadTaskData(): void
    {
        // TODO: Load existing task data for editing
    }

    public function updatedCustomerId(): void
    {
        $this->project_id = null;
        $this->loadProjects();
    }

    public function loadProjects(): void
    {
        if (! $this->customer_id) {
            $this->projects = [];

            return;
        }

        // Load active projects for selected customer
        $activeStatusId = ReferenceItem::where('key', 'project_active')->value('id');

        $this->projects = Project::query()
            ->where('customer_id', $this->customer_id)
            ->where('status_id', $activeStatusId)
            ->orderBy('name')
            ->get(['id', 'name', 'project_id_code'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => "[{$p->project_id_code}] {$p->name}",
            ])
            ->toArray();
    }

    public function save(): void
    {
        // TODO: Implement save logic
        $this->dispatch('toast', type: 'info', message: 'Kaydetme henüz aktif değil.');
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-7xl mx-auto">
        {{-- Back Button --}}
        <a href="{{ route('projects.index', ['tab' => 'tasks']) }}"
            class="inline-flex items-center gap-2 text-skin-base hover:text-skin-heading mb-4 transition-colors">
            <x-mary-icon name="o-arrow-left" class="w-4 h-4" />
            <span class="text-sm font-medium">Görev Listesi</span>
        </a>

        {{-- Header with Action Buttons --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
                    {{ $task?->id ? 'Görev Düzenle' : 'Yeni Görev Oluştur' }}
                </h1>
                <p class="text-sm opacity-60 text-skin-base mt-1">
                    Görev bilgilerini girin ve kaydedin.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.index', ['tab' => 'tasks']) }}" class="theme-btn-cancel px-4 py-2 text-sm">
                    İptal
                </a>
                <button type="button" wire:click="save" wire:loading.attr="disabled"
                    class="theme-btn-save flex items-center gap-2 px-4 py-2 text-sm">
                    <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                    <x-mary-icon name="o-check" class="w-4 h-4" />
                    Kaydet
                </button>
            </div>
        </div>

        {{-- Main Layout: 8/12 Left, 4/12 Right --}}
        <div class="grid grid-cols-12 gap-6">
            {{-- Left Column (8/12) --}}
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
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Müşteri <span
                                    class="text-red-500">*</span></label>
                            <select wire:model.live="customer_id" class="select w-full">
                                <option value="">Müşteri seçin...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer['id'] }}">{{ $customer['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Proje --}}
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Proje <span
                                    class="text-red-500">*</span></label>
                            <select wire:model="project_id" class="select w-full" @if(!$customer_id) disabled @endif>
                                <option value="">{{ $customer_id ? 'Proje seçin...' : 'Önce müşteri seçin' }}</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                                @endforeach
                            </select>
                            @if($customer_id && empty($projects))
                                <p class="text-xs text-orange-500 mt-1">Bu müşteriye ait aktif proje bulunamadı.</p>
                            @endif
                        </div>

                        {{-- Kim Atıyor --}}
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Kim Atıyor</label>
                            @if($isAdmin)
                                <select wire:model="assigned_by" class="select w-full">
                                    @foreach($users as $user)
                                        <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                    @endforeach
                                </select>
                            @else
                                @php
                                    $currentUser = collect($users)->firstWhere('id', $assigned_by);
                                @endphp
                                <div
                                    class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg">
                                    <div
                                        class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-xs font-medium">
                                        {{ strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) }}
                                    </div>
                                    <span
                                        class="text-sm font-medium text-slate-700">{{ $currentUser['name'] ?? 'Bilinmiyor' }}</span>
                                    <x-mary-icon name="o-lock-closed" class="w-4 h-4 text-slate-400 ml-auto" />
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">Bu alan sadece yöneticiler tarafından
                                    değiştirilebilir.</p>
                            @endif
                        </div>

                        {{-- Kime Atanıyor --}}
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Kime Atanıyor <span
                                    class="text-red-500">*</span></label>
                            <select wire:model="assigned_to" class="select w-full">
                                <option value="">Kullanıcı seçin...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Öncelik --}}
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60 text-skin-base">Öncelik <span
                                    class="text-red-500">*</span></label>
                            <select wire:model="priority_id" class="select w-full">
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority['id'] }}">{{ $priority['display_label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Rapor (Placeholder) --}}
                <div class="theme-card p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-[var(--color-text-heading)] mb-4 flex items-center gap-2">
                        <x-mary-icon name="o-document-text" class="w-5 h-5" />
                        Rapor
                    </h3>

                    <div class="text-center py-8 border-2 border-dashed border-slate-200 rounded-xl">
                        <x-mary-icon name="o-clock" class="w-12 h-12 mx-auto mb-3 text-slate-300" />
                        <p class="text-slate-500 mb-2">İçerik bekleniyor</p>
                        <p class="text-xs text-slate-400">Bu bölümün içeriği daha sonra belirlenecek.</p>
                    </div>
                </div>
            </div>

            {{-- Right Column (4/12) - Sticky Sidebar --}}
            <div class="col-span-4 flex flex-col gap-6">
                <div class="theme-card p-6 shadow-sm sticky top-6">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">Görev Özeti</h3>

                    @if($customer_id)
                        @php
                            $selectedCustomer = collect($customers)->firstWhere('id', $customer_id);
                            $selectedProject = collect($projects)->firstWhere('id', $project_id);
                            $selectedAssignee = collect($users)->firstWhere('id', $assigned_to);
                            $selectedPriority = collect($priorities)->firstWhere('id', $priority_id);
                        @endphp

                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                <span class="text-slate-500">Müşteri</span>
                                <span class="font-medium text-slate-700">{{ $selectedCustomer['name'] ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                <span class="text-slate-500">Proje</span>
                                <span class="font-medium text-slate-700">{{ $selectedProject['name'] ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-slate-100">
                                <span class="text-slate-500">Atanan</span>
                                <span class="font-medium text-slate-700">{{ $selectedAssignee['name'] ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-slate-500">Öncelik</span>
                                <span
                                    class="font-medium text-slate-700">{{ $selectedPriority['display_label'] ?? '-' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-mary-icon name="o-clipboard-document-list" class="w-12 h-12 mx-auto mb-2 text-slate-300" />
                            <p class="text-sm text-slate-500">Müşteri seçilmedi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>