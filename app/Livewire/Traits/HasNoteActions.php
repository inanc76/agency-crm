<?php

namespace App\Livewire\Traits;

use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * @trait HasNoteActions
 * @purpose Polymorphic notlar için CRUD işlemleri ve görünürlük yönetimi
 * @methods createNote(), updateNote(), deleteNote(), loadNotes(), canUserSeeNote()
 * 
 * Bu trait, herhangi bir entity'ye (Customer, Project, Task, etc.) not ekleme,
 * düzenleme, silme ve görüntüleme işlemlerini yönetir.
 * 
 * Kullanım:
 * - Component'te use HasNoteActions;
 * - $entityType ve $entityId property'lerini tanımla
 * - loadNotes() ile notları yükle
 */
trait HasNoteActions
{
    // Note Modal State
    public bool $showNoteModal = false;
    public ?string $editingNoteId = null;
    public string $noteContent = '';
    public array $noteVisibleTo = [];

    // Notes Data
    public $notes = [];

    /**
     * Not modalını aç
     */
    public function openNoteModal(?string $noteId = null): void
    {
        $this->editingNoteId = $noteId;

        if ($noteId) {
            $note = Note::with('visibleTo')->findOrFail($noteId);
            
            // Yetki kontrolü
            if (!$note->canBeSeenBy(Auth::user())) {
                $this->error('Bu notu görüntüleme yetkiniz yok.');
                return;
            }

            $this->noteContent = $note->content;
            $this->noteVisibleTo = $note->visibleTo->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->resetNoteForm();
            // Varsayılan olarak tüm kullanıcıları seç
            $this->noteVisibleTo = User::pluck('id')->map(fn($id) => (string) $id)->toArray();
        }

        $this->showNoteModal = true;
    }

    /**
     * Not modalını kapat
     */
    public function closeNoteModal(): void
    {
        $this->showNoteModal = false;
        $this->resetNoteForm();
    }

    /**
     * Not formunu sıfırla
     */
    private function resetNoteForm(): void
    {
        $this->editingNoteId = null;
        $this->noteContent = '';
        $this->noteVisibleTo = [];
        $this->resetValidation(['noteContent', 'noteVisibleTo']);
    }

    /**
     * Not kaydet (yeni veya güncelleme)
     */
    public function saveNote(): void
    {
        $this->validate([
            'noteContent' => 'required|string|max:10000',
            'noteVisibleTo' => 'required|array|min:1',
            'noteVisibleTo.*' => 'exists:users,id',
        ], [
            'noteContent.required' => 'Not içeriği zorunludur.',
            'noteContent.max' => 'Not içeriği en fazla 10.000 karakter olabilir.',
            'noteVisibleTo.required' => 'En az bir kullanıcı seçmelisiniz.',
            'noteVisibleTo.min' => 'En az bir kullanıcı seçmelisiniz.',
            'noteVisibleTo.*.exists' => 'Seçilen kullanıcı geçersiz.',
        ]);

        if ($this->editingNoteId) {
            $this->updateNote();
        } else {
            $this->createNote();
        }
    }

    /**
     * Yeni not oluştur
     */
    private function createNote(): void
    {
        $note = Note::create([
            'content' => $this->noteContent,
            'author_id' => Auth::id(),
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
        ]);

        // Görünürlük ayarla
        $note->visibleTo()->sync($this->noteVisibleTo);

        $this->success('Not başarıyla eklendi.');
        $this->closeNoteModal();
        $this->loadNotes();
    }

    /**
     * Mevcut notu güncelle
     */
    private function updateNote(): void
    {
        $note = Note::findOrFail($this->editingNoteId);

        // Yetki kontrolü - Sadece yazar düzenleyebilir
        if ($note->author_id !== Auth::id()) {
            $this->error('Bu notu düzenleme yetkiniz yok.');
            return;
        }

        $note->update([
            'content' => $this->noteContent,
        ]);

        // Görünürlük güncelle
        $note->visibleTo()->sync($this->noteVisibleTo);

        $this->success('Not başarıyla güncellendi.');
        $this->closeNoteModal();
        $this->loadNotes();
    }

    /**
     * Notu sil
     */
    public function deleteNote(string $noteId): void
    {
        $note = Note::findOrFail($noteId);

        // Yetki kontrolü - Sadece yazar silebilir
        if ($note->author_id !== Auth::id()) {
            $this->error('Bu notu silme yetkiniz yok.');
            return;
        }

        $note->delete();

        $this->success('Not başarıyla silindi.');
        $this->loadNotes();
    }

    /**
     * Entity'ye ait notları yükle (sadece kullanıcının görebildikleri)
     */
    public function loadNotes(): void
    {
        $userId = Auth::id();

        $this->notes = Note::with(['author', 'visibleTo'])
            ->where('entity_type', $this->entityType)
            ->where('entity_id', $this->entityId)
            ->where(function ($query) use ($userId) {
                // Yazarı olan veya görünürlük listesinde olan notlar
                $query->where('author_id', $userId)
                    ->orWhereHas('visibleTo', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Kullanıcının notu görme yetkisi var mı?
     */
    public function canUserSeeNote(Note $note): bool
    {
        return $note->canBeSeenBy(Auth::user());
    }

    /**
     * Kullanıcının notu düzenleme yetkisi var mı?
     */
    public function canUserEditNote(Note $note): bool
    {
        return $note->author_id === Auth::id();
    }
}
