<?php

namespace App\Models;

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
 * ⚙️ ProjectModule Model - Operasyonel Katman
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * @version Constitution V10 - ReferenceData Entegrasyonu
 *
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 *
 * @property string $id
 * @property string $phase_id
 * @property string $name
 * @property string|null $description
 * @property \Carbon\Carbon|null $start_date
 * @property \Carbon\Carbon|null $end_date
 * @property int $order
 * @property string|null $status_id Durum FK (reference_items)
 * @property \ArrayObject|null $custom_fields
 * @property-read ProjectPhase $phase  BelongsTo
 * @property-read ReferenceItem|null $status  BelongsTo: Durum
 * @property-read Collection<ProjectTask> $tasks  HasMany
 * @property-read Collection<User> $users  BelongsToMany (pivot)
 *
 * ⚠️ Observer: Max 50 modül sınırı + Domino Effect (ProjectModuleObserver)
 * ═══════════════════════════════════════════════════════════════════════════
 */
class ProjectModule extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'phase_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'order',
        'status_id',
        'estimated_hours',
        'custom_fields',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'order' => 'integer',
            'estimated_hours' => 'integer',
            'custom_fields' => AsArrayObject::class,
        ];
    }

    /**
     * Boot method - Varsayılan durum ata
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ProjectModule $module) {
            if (empty($module->status_id)) {
                $module->status_id = self::getDefaultStatusId();
            }
        });
    }

    /**
     * Varsayılan durum ID'sini al
     */
    public static function getDefaultStatusId(): ?string
    {
        return ReferenceItem::where('category_key', 'MODULE_STATUS')
            ->where('is_default', true)
            ->value('id');
    }

    /**
     * Durum key'ini al (Observer için)
     */
    public function getStatusKeyAttribute(): ?string
    {
        return $this->status?->key;
    }

    /**
     * Terminal durum mu? (Tamamlandı veya İptal)
     */
    public function isTerminal(): bool
    {
        return in_array($this->status_key, ['module_completed', 'module_cancelled']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────────────────────────────────

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class, 'phase_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ReferenceItem::class, 'status_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'module_id')->orderBy('order');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'module_user', 'project_module_id')
            ->withTimestamps();
    }

    /**
     * Get project through phase relationship
     */
    public function getProjectAttribute(): ?Project
    {
        return $this->phase?->project;
    }
}
