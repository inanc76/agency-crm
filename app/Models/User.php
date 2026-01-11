<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Traits\HasBlameable;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 👨‍💼 User Model - Sistem Kullanıcıları ve Yetkilendirme
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * @package App\Models
 * @version Constitution V10
 * 
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 * 
 * @property string $id                  UUID primary key
 * @property string $name                Kullanıcı adı soyadı
 * @property string $email               E-posta (unique, login için)
 * @property string $password            Hashed şifre
 * @property string|null $role_id        Rol UUID (FK: roles)
 * @property \Carbon\Carbon|null $email_verified_at E-posta doğrulama zamanı
 * @property string|null $remember_token Remember me token
 * @property string|null $two_factor_secret 2FA secret (encrypted)
 * @property string|null $two_factor_recovery_codes 2FA recovery codes (encrypted)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Role|null $role        BelongsTo: Kullanıcının rolü
 * 
 * User modeli, sistemdeki KULLANICILARI ve YETKİLENDİRMEYİ yönetir:
 * - Laravel Fortify ile authentication (login, 2FA)
 * - Role-based access control (RBAC): User -> Role -> Permissions
 * - hasPermissionTo(): Permission kontrolü
 * - givePermissionTo(): Test/setup için permission atama
 * - initials(): Avatar için baş harfler (örn: "VK")
 * 
 * ⚠️ Güvenlik: password hashed, 2FA secret encrypted
 * 🛡️ Audit: SoftDeletes + Blameable aktif
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasUuids, SoftDeletes, HasBlameable;

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermissionTo(string $permissionName): bool
    {
        return $this->role && $this->role->permissions()->where('name', $permissionName)->exists();
    }

    /**
     * Grants a permission to the user's current role (for testing/setup)
     */
    public function givePermissionTo(string $permissionName): void
    {
        $permission = Permission::firstOrCreate(
            ['name' => $permissionName],
            ['type' => 'ACTION', 'resource' => explode('.', $permissionName)[0] ?? 'global']
        );

        if ($this->role) {
            $this->role->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }
}
