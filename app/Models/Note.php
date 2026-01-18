<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 📝 Note Model - Polymorphic Notlar
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * @version Constitution V11
 *
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 *
 * @property string $id UUID primary key
 * @property string $content Not içeriği
 * @property string $author_id Yazan kullanıcı UUID (FK: users)
 * @property string $entity_type Varlık tipi (CUSTOMER, OFFER, SERVICE, etc.)
 * @property string $entity_id Varlık UUID (polymorphic)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $author       BelongsTo: Notu yazan kullanıcı
 * @property-read \Illuminate\Database\Eloquent\Collection<User> $visibleTo BelongsToMany: Notu görebilecek kullanıcılar
 *
 * Note, POLYMORPHIC yapıdadır. Herhangi bir varlığa (Customer, Offer, vb.)
 * not eklenebilir. entity_type + entity_id ile ilişkilendirilir.
 *
 * Görünürlük: note_user pivot tablosu ile hangi kullanıcıların görebileceği kontrol edilir.
 *
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Note extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'content',
        'author_id',
        'entity_type',
        'entity_id',
    ];

    /**
     * Notu yazan kullanıcı
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Bu notu görebilecek kullanıcılar (Many-to-Many)
     */
    public function visibleTo()
    {
        return $this->belongsToMany(User::class, 'note_user', 'note_id', 'user_id');
    }

    /**
     * Bu notu görebilecek departmanlar (Many-to-Many)
     */
    public function visibleToDepartments()
    {
        return $this->belongsToMany(ReferenceItem::class, 'note_department', 'note_id', 'department_id');
    }

    /**
     * Kullanıcının bu notu görme yetkisi var mı?
     */
    public function canBeSeenBy(User $user): bool
    {
        // Yazar her zaman görebilir
        if ($this->author_id === $user->id) {
            return true;
        }

        // Görünürlük listesinde var mı? (Kişisel bazlı)
        if ($this->visibleTo()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Departman bazlı yetki kontrolü
        if ($user->department_id && $this->visibleToDepartments()->where('department_id', $user->department_id)->exists()) {
            return true;
        }

        return false;
    }
}
