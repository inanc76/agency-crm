<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🔐 Permission Model - Sistem Yetkileri
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * @property string $id              UUID primary key
 * @property string $name            Yetki adı (örn: customer.create, offer.delete)
 * @property string|null $type       Yetki tipi (ACTION, VIEW, etc.)
 * @property string|null $resource   Kaynak adı (customer, offer, etc.)
 * @property string|null $action     Aksiyon (create, update, delete, etc.)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Role> $roles
 *                BelongsToMany: Bu yetkiye sahip roller (permission_role pivot)
 * 
 * Permission, sistemdeki TÜM YETKİLERİ tanımlar:
 * - Granular permissions: resource.action formatı
 * - Role'lere atanır (many-to-many)
 * - Server Actions'da withServerActionPermission ile kontrol edilir
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Permission extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'type',
        'resource',
        'action',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }
}
