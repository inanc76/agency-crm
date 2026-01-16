<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-skin-heading">
            @if($isViewMode)
                {{ $task?->name ?: 'Görev Detayı' }}
            @elseif($task?->id)
                Düzenle: {{ $name }}
            @else
                Yeni Görev Oluştur: {{ $name }}
            @endif
        </h1>
        <div class="flex items-center gap-2 mt-1">
            @if($task?->id)
                <span
                    class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--dropdown-hover-bg)] text-[var(--color-text-base)] border border-[var(--card-border)]">Görev</span>
                <span class="text-[11px] font-mono text-[var(--color-text-muted)]">ID: {{ $task->id }}</span>
            @else
                <p class="text-sm opacity-60 text-skin-base">
                    Yeni görev bilgilerini girin
                </p>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-3">
        @if($isViewMode && $task?->id)
            {{-- View Mode Actions --}}
            <button type="button" wire:click="delete" wire:confirm="Bu görevi silmek istediğinize emin misiniz?"
                wire:key="btn-delete-{{ $task->id }}" class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                <x-mary-icon name="o-trash" class="w-4 h-4" />
                Sil
            </button>
            <button type="button" wire:click="toggleEditMode" wire:key="btn-edit-{{ $task->id }}"
                class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                Düzenle
            </button>
        @else
            {{-- Edit Mode Actions --}}
            @if($task?->id)
                <button type="button" wire:click="toggleEditMode" wire:key="btn-cancel-{{ $task->id }}"
                    class="theme-btn-cancel px-4 py-2 text-sm">
                    İptal
                </button>
            @else
                <a href="{{ route('projects.index', ['tab' => 'tasks']) }}" class="theme-btn-cancel px-4 py-2 text-sm">
                    İptal
                </a>
            @endif
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                wire:key="btn-save-{{ $task?->id ?: 'new' }}"
                class="theme-btn-save flex items-center gap-2 px-4 py-2 text-sm">
                <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                <x-mary-icon name="o-check" class="w-4 h-4" />
                @if($task?->id) Güncelle @else Kaydet @endif
            </button>
        @endif
    </div>
</div>