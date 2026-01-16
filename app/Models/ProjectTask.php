<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * ✅ ProjectTask Model - Eylem Katmanı
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * @version Constitution V10 - ReferenceData Entegrasyonu
 *
 * 🔑 UUID: ✅ ACTIVE (HasUuids) | PK: string | Incrementing: false
 *
 * @property string $id
 * @property string $module_id
 * @property string $name
 * @property string|null $description
 * @property int $order
 * @property string|null $status_id Durum FK (reference_items)
 * @property string $priority low, medium, high, urgent
 * @property \Carbon\Carbon|null $due_date
 * @property float|null $estimated_hours
 * @property float|null $actual_hours
 * @property \ArrayObject|null $custom_fields
 * @property-read ProjectModule $module  BelongsTo
 * @property-read ReferenceItem|null $status  BelongsTo: Durum
 * @property-read Collection<User> $users  BelongsToMany (pivot)
 * ═══════════════════════════════════════════════════════════════════════════
 */
class ProjectTask extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'project_id',
        'module_id',
        'name',
        'description',
        'order',
        'status_id',
        'priority',
        'due_date',
        'estimated_hours',
        'actual_hours',
        'custom_fields',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'estimated_hours' => 'decimal:2',
            'actual_hours' => 'decimal:2',
            'order' => 'integer',
            'custom_fields' => AsArrayObject::class,
        ];
    }

    // Not: Task için henüz TASK_STATUS kategorisi yok, gerekirse eklenecek

    // ─────────────────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(ProjectModule::class, 'module_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ReferenceItem::class, 'status_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user', 'project_task_id')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectTaskItem::class, 'project_task_id')->orderBy('sort_order')->orderBy('created_at');
    }

    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectReport::class, 'task_id');
    }
}
