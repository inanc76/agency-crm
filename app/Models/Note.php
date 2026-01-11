<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 📝 Note Model - Polymorphic Notlar
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * @property string $id              UUID primary key
 * @property string $content         Not içeriği
 * @property string $author_id       Yazan kullanıcı UUID (FK: users)
 * @property string $entity_type     Varlık tipi (CUSTOMER, OFFER, SERVICE, etc.)
 * @property string $entity_id       Varlık UUID (polymorphic)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read User $author       BelongsTo: Notu yazan kullanıcı
 * 
 * Note, POLYMORPHIC yapıdadır. Herhangi bir varlığa (Customer, Offer, vb.)
 * not eklenebilir. entity_type + entity_id ile ilişkilendirilir.
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
        'entity_id'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
