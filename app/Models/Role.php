<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 👔 Role Model - Kullanıcı Rolleri
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * @property string $id              UUID primary key
 * @property string $name            Rol adı (örn: Admin, Manager, User)
 * @property string|null $description Rol açıklaması
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Permission> $permissions
 *                BelongsToMany: Role ait yetkiler (permission_role pivot)
 * 
 * Role, kullanıcıların YETKİ GRUPLARIdır:
 * - User -> Role -> Permissions (RBAC chain)
 * - Örnek roller: Admin (tüm yetkiler), Manager (kısıtlı), User (sadece görüntüleme)
 * - Seed ile oluşturulur, UI'da yönetilebilir
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Role extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }
}
