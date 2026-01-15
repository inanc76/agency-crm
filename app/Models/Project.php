<?php

namespace App\Models;

use App\Traits\HasBlameable;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🏗️ Project Model - Ajans Projesi Ana Varlığı
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * @version Constitution V10 - ReferenceData Entegrasyonu
 *
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 *
 * @property string $id UUID primary key
 * @property string $customer_id Müşteri FK
 * @property string $leader_id Proje Lideri FK
 * @property string $project_id_code Auto-generated (PRJ-2026-001)
 * @property string $name Proje adı
 * @property string|null $description Açıklama
 * @property string $timezone Zaman dilimi
 * @property string|null $status_id Durum FK (reference_items)
 * @property \Carbon\Carbon|null $start_date
 * @property \Carbon\Carbon|null $target_end_date
 * @property \ArrayObject|null $custom_fields Dinamik JSONB alanlar
 * @property-read Customer $customer       BelongsTo: Müşteri
 * @property-read User $leader             BelongsTo: Proje Lideri
 * @property-read ReferenceItem|null $status  BelongsTo: Durum
 * @property-read Collection<ProjectPhase> $phases  HasMany: Fazlar
 * @property-read Collection<User> $users  BelongsToMany: Üyeler (pivot)
 *
 * 🛡️ Audit: SoftDeletes + Blameable aktif
 * ═══════════════════════════════════════════════════════════════════════════
 */
class Project extends Model
{
    use HasBlameable, HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'customer_id',
        'leader_id',
        'project_id_code',
        'name',
        'description',
        'timezone',
        'status_id',
        'type_id',
        'start_date',
        'target_end_date',
        'completed_at',
        'custom_fields',
    ];

    protected function casts(): array
    {
        return [
            'custom_fields' => AsArrayObject::class,
            'start_date' => 'date',
            'target_end_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Boot method to auto-generate project_id_code and set default status
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Project $project) {
            if (empty($project->project_id_code)) {
                $project->project_id_code = self::generateProjectCode();
            }

            // Varsayılan durum ata
            if (empty($project->status_id)) {
                $project->status_id = self::getDefaultStatusId();
            }
        });

        static::updating(function (Project $project) {
            if ($project->isDirty('status_id')) {
                // Status key'ini bul
                $newStatus = ReferenceItem::find($project->status_id);

                if ($newStatus) {
                    if (in_array($newStatus->key, ['project_completed', 'project_cancelled'])) {
                        // Eğer zaten dolu değilse doldur (ilk kez tamamlanıyor/iptal ediliyor)
                        // Veya her statü değişiminde güncellensin istiyorsak direkt ata. Genelde ilk bitiş tarihi esastır ama
                        // kullanıcı "tekrar aktif -> tekrar tamamlandı" yaparsa son tarih geçerli olmalı.
                        $project->completed_at = now();
                    } elseif ($newStatus->key === 'project_active') {
                        // Aktife dönerse sayacı tekrar başlat (null yap)
                        $project->completed_at = null;
                    }
                }
            }
        });
    }

    /**
     * Generate unique project code: PRJ-YYYY-NNN
     */
    public static function generateProjectCode(): string
    {
        $year = date('Y');
        $lastProject = self::withTrashed()
            ->where('project_id_code', 'like', "PRJ-{$year}-%")
            ->orderByDesc('project_id_code')
            ->first();

        if ($lastProject) {
            $lastNumber = (int) substr($lastProject->project_id_code, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "PRJ-{$year}-{$newNumber}";
    }

    /**
     * Varsayılan durum ID'sini al
     */
    public static function getDefaultStatusId(): ?string
    {
        return ReferenceItem::where('category_key', 'PROJECT_STATUS')
            ->where('is_default', true)
            ->value('id');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ReferenceItem::class, 'status_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ReferenceItem::class, 'type_id');
    }

    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class)->orderBy('order');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
