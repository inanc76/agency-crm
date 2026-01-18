<?php
/**
 * ✅ NOTES-TAB COMPONENT
 * ---------------------------------------------------------
 * MİMARİ: Volt Component (Class-based)
 * SORUMLULUK: Proje ve görevlere ait notları yönetir
 * ---------------------------------------------------------
 */

use App\Livewire\Traits\HasNoteActions;
use App\Models\User;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use HasNoteActions;
    use Toast;

    public ?string $project_id = null;
    public ?string $task_id = null;

    // Entity bilgileri (HasNoteActions için gerekli)
    public string $entityType;
    public string $entityId;

    /**
     * Component mount edildiğinde
     */
    public function mount(): void
    {
        // Proje veya görev için entity type ve id belirle
        if ($this->task_id) {
            $this->entityType = 'PROJECT_TASK';
            $this->entityId = $this->task_id;
        } elseif ($this->project_id) {
            $this->entityType = 'PROJECT';
            $this->entityId = $this->project_id;
        }

        $this->loadNotes();
    }

    /**
     * Tüm departmanları getir (x-mary-choices için)
     */
    public function getAllDepartmentsProperty()
    {
        return \App\Models\ReferenceItem::where('category_key', 'DEPARTMENT')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($item) => [
                'id' => (string) $item->id,
                'name' => $item->display_label,
            ])
            ->toArray();
    }
}; ?>

{{--
SECTION: Notes Tab Main Container
Mimarın Notu: Bu sekme polymorphic Note modeli ile konuşur ve HasNoteActions trait'ini kullanır.
İş Mantığı Şerhi: Proje veya göreve bağlı notları listeler, ekleme/düzenleme/silme işlemlerini yönetir.
Mühür Koruması: Tüm değişkenler explicit olarak partials'a aktarılır.
--}}
<div>
    {{-- SECTION: Actions Bar - Not ekleme butonu --}}
    @include('livewire.shared.notes.partials._notes-actions', [
        'notes' => $notes,
    ])

    {{-- SECTION: Notes List - Notların listelendiği alan --}}
    @include('livewire.shared.notes.partials._notes-list', [
        'notes' => $notes,
    ])

    {{-- SECTION: Note Modal - Not ekleme/düzenleme modalı --}}
    @include('livewire.shared.notes.partials._modal-note', [
        'showNoteModal' => $showNoteModal,
        'editingNoteId' => $editingNoteId,
        'noteContent' => $noteContent,
        'noteVisibleToDepartments' => $this->noteVisibleToDepartments,
        'allDepartments' => $this->allDepartments,
    ])
</div>