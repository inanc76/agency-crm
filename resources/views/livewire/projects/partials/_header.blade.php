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
                <span
                    class="text-xs font-medium px-2 py-0.5 rounded bg-[var(--dropdown-hover-bg)] text-[var(--color-text-base)] border border-[var(--card-border)]">Proje</span>
                <span class="text-[11px] font-mono text-[var(--color-text-muted)]">Kod:
                    {{ \App\Models\Project::find($projectId)?->project_id_code }}</span>
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
                    wire:key="btn-delete-{{ $projectId }}" class="theme-btn-delete flex items-center gap-2 px-4 py-2 text-sm">
                    <x-mary-icon name="o-trash" class="w-4 h-4" />
                    Sil
                </button>
            @endif
            <button type="button" wire:click="toggleEdit" wire:key="btn-edit-{{ $projectId }}"
                class="theme-btn-edit flex items-center gap-2 px-4 py-2 text-sm">
                <x-mary-icon name="o-pencil-square" class="w-4 h-4" />
                Düzenle
            </button>
        @else
            {{-- Edit Mode Actions --}}
            <button type="button" wire:click="{{ $projectId ? 'toggleEdit' : '' }}"
                wire:key="btn-cancel-{{ $projectId ?: 'new' }}" @if(!$projectId)
                onclick="window.location.href='/dashboard/projects'" @endif class="theme-btn-cancel px-4 py-2 text-sm">
                İptal
            </button>
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                wire:key="btn-save-{{ $projectId ?: 'new' }}" @click="markClean()"
                class="theme-btn-save flex items-center gap-2 px-4 py-2 text-sm">
                <span wire:loading class="loading loading-spinner loading-xs mr-1"></span>
                <x-mary-icon name="o-check" class="w-4 h-4" />
                @if($projectId) Güncelle @else Kaydet @endif
            </button>
        @endif
    </div>
</div>