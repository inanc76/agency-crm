<?php

namespace App\Livewire\Traits;

use App\Models\Note;
use Illuminate\Support\Facades\Auth;

/**
 * 🛡️ ZIRHLI BELGELEME KARTI (V12.2)
 * -------------------------------------------------------------------------
 * TRAIT      : HasNoteActions
 * SORUMLULUK : Polymorphic not yapısının (Customer, Project, Task vb.)
 *              eklenmesi, düzenlenmesi ve hassas görünürlük kontrolü.
 *
 * BAĞIMLILIKLAR:
 * - App\Models\Note
 * - App\Models\ReferenceItem (Departman bazlı yetkilendirme için)
 *
 * METODLAR:
 * - loadNotes(): Yetki dahilindeki notları asenkron yükler.
 * - saveNote(): Yeni not oluşturur veya günceller.
 * - deleteNote(): Notu siler (Yazar kontrolü ile).
 * - canUserSeeNote(): Görünürlük mantığını kontrol eder.
 * -------------------------------------------------------------------------
 */
trait HasNoteActions
{
    /**
     * Not modalını verileri hazırlayarak açar.
     */
    public function openNoteModal(?string $noteId = null): void
    {
        $this->editingNoteId = $noteId;

        if ($noteId) {
            $note = Note::with('visibleToDepartments')->findOrFail($noteId);

            // 🔐 Security: Not görünürlük denetimi
            if (!$note->canBeSeenBy(Auth::user())) {
                $this->error('Bu notu görüntüleme yetkiniz yok.');
                return;
            }

            $this->noteContent = $note->content;
            $this->noteVisibleToDepartments = $note->visibleToDepartments->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->resetNoteForm();
            // İş Kuralı: Yeni notlarda varsayılan olarak tüm departmanlar seçili gelir
            $this->noteVisibleToDepartments = \App\Models\ReferenceItem::where('category_key', 'DEPARTMENT')
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        }

        $this->showNoteModal = true;
    }

    /**
     * Not modalını kapatır ve formu sıfırlar.
     */
    public function closeNoteModal(): void
    {
        $this->showNoteModal = false;
        $this->resetNoteForm();
    }

    /**
     * Dahili form sıfırlama mekanizması.
     */
    private function resetNoteForm(): void
    {
        $this->editingNoteId = null;
        $this->noteContent = '';
        $this->noteVisibleToDepartments = [];
        $this->resetValidation(['noteContent', 'noteVisibleToDepartments']);
    }

    /**
     * Notu kaydeder veya günceller.
     */
    public function saveNote(): void
    {
        $this->validate([
            'noteContent' => 'required|string|max:10000',
            'noteVisibleToDepartments' => 'required|array|min:1',
            'noteVisibleToDepartments.*' => 'exists:reference_items,id',
        ], [
            'noteContent.required' => 'Not içeriği zorunludur.',
            'noteContent.max' => 'Not içeriği en fazla 10.000 karakter olabilir.',
            'noteVisibleToDepartments.required' => 'En az bir departman seçmelisiniz.',
            'noteVisibleToDepartments.min' => 'En az bir departman seçmelisiniz.',
            'noteVisibleToDepartments.*.exists' => 'Seçilen departman geçersiz.',
        ]);

        if ($this->editingNoteId) {
            $this->updateNote();
        } else {
            $this->createNote();
        }
    }

    /**
     * Yeni not oluşturur ve departman bağlarını kurar.
     */
    private function createNote(): void
    {
        $note = Note::create([
            'content' => $this->noteContent,
            'author_id' => Auth::id(),
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
        ]);

        $note->visibleToDepartments()->sync($this->noteVisibleToDepartments);

        $this->success('Not başarıyla eklendi.');
        $this->closeNoteModal();
        $this->loadNotes();
    }

    /**
     * Mevcut notu günceller.
     * Güvenlik: Sadece not yazarı güncelleyebilir.
     */
    private function updateNote(): void
    {
        $note = Note::findOrFail($this->editingNoteId);

        if ($note->author_id !== Auth::id()) {
            $this->error('Bu notu düzenleme yetkiniz yok.');
            return;
        }

        $note->update(['content' => $this->noteContent]);
        $note->visibleToDepartments()->sync($this->noteVisibleToDepartments);

        $this->success('Not başarıyla güncellendi.');
        $this->closeNoteModal();
        $this->loadNotes();
    }

    /**
     * Notu kalıcı olarak siler.
     * Güvenlik: Sadece not yazarı silebilir.
     */
    public function deleteNote(string $noteId): void
    {
        $note = Note::findOrFail($noteId);

        if ($note->author_id !== Auth::id()) {
            $this->error('Bu notu silme yetkiniz yok.');
            return;
        }

        $note->delete();
        $this->success('Not başarıyla silindi.');
        $this->loadNotes();
    }

    /**
     * Görünürlük kısıtlamalarına göre notları yükler.
     * Performans: Author ve Departments eager loading ile yüklenir.
     */
    public function loadNotes(): void
    {
        $user = Auth::user();
        $userId = $user->id;
        $departmentId = $user->department_id;

        $this->notes = Note::with(['author', 'visibleToDepartments'])
            ->where('entity_type', $this->entityType)
            ->where('entity_id', $this->entityId)
            ->where(function ($query) use ($userId, $departmentId) {
                // Görünürlük Mantığı: Yazar veya ilgili departman üyesi görebilir
                $query->where('author_id', $userId)
                    ->orWhereHas('visibleTo', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                    ->orWhereHas('visibleToDepartments', function ($q) use ($departmentId) {
                    if ($departmentId) {
                        $q->where('department_id', $departmentId);
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Kullanıcının notu görme yetkisini kontrol eder.
     */
    public function canUserSeeNote(Note $note): bool
    {
        return $note->canBeSeenBy(Auth::user());
    }

    /**
     * Kullanıcının notu düzenleme/silme yetkisini (Sahiplik) kontrol eder.
     */
    public function canUserEditNote(Note $note): bool
    {
        return $note->author_id === Auth::id();
    }
}
