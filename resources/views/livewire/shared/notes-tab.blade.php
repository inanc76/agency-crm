<?php

use App\Livewire\Traits\HasNoteActions;
use App\Models\User;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use HasNoteActions;
    use Toast;

    // Entity bilgileri (parent component'ten gelecek)
    public string $entityType;
    public string $entityId;

    /**
     * Component mount edildiğinde notları yükle
     */
    public function mount(): void
    {
        $this->loadNotes();
    }

    /**
     * Tüm kullanıcıları getir (x-mary-choices için)
     */
    public function getAllUsersProperty()
    {
        return User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get()
            ->map(fn($user) => [
                'id' => (string) $user->id,
                'name' => $user->name,
            ])
            ->toArray();
    }
}; ?>

{{-- 
    SECTION: Notes Tab Main Container
    Mimarın Notu: Bu sekme polymorphic Note modeli ile konuşur ve HasNoteActions trait'ini kullanır.
    İş Mantığı Şerhi: Entity'ye bağlı notları listeler, ekleme/düzenleme/silme işlemlerini yönetir.
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
        'noteVisibleTo' => $noteVisibleTo,
        'allUsers' => $this->allUsers,
    ])
</div>
