<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 📊 ProjectPhase Model - Stratejik Katman
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * @version Constitution V10 - ReferenceData Entegrasyonu
 *
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 *
 * @property string $id
 * @property string $project_id
 * @property string $name
 * @property string|null $description
 * @property int $order Sıralama
 * @property string|null $status_id Durum FK (reference_items)
 * @property \Carbon\Carbon|null $start_date (Observer tarafından senkronize)
 * @property \Carbon\Carbon|null $end_date (Observer tarafından senkronize)
 * @property \ArrayObject|null $custom_fields
 * @property-read Project $project  BelongsTo
 * @property-read ReferenceItem|null $status  BelongsTo: Durum
 * @property-read Collection<ProjectModule> $modules  HasMany
 *
 * ⚠️ Observer: Max 20 faz sınırı (ProjectPhaseObserver)
 * ═══════════════════════════════════════════════════════════════════════════
 */
class ProjectPhase extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'order',
        'status_id',
        'start_date',
        'end_date',
        'custom_fields',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'custom_fields' => AsArrayObject::class,
        ];
    }

    /**
     * Boot method - Varsayılan durum ata
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ProjectPhase $phase) {
            if (empty($phase->status_id)) {
                $phase->status_id = self::getDefaultStatusId();
            }
        });
    }

    /**
     * Varsayılan durum ID'sini al
     */
    public static function getDefaultStatusId(): ?string
    {
        return ReferenceItem::where('category_key', 'PHASE_STATUS')
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

    // ─────────────────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ReferenceItem::class, 'status_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(ProjectModule::class, 'phase_id')->orderBy('order');
    }
}
