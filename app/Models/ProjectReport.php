<?php

namespace App\Models;

use App\Traits\HasBlameable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectReport extends Model
{
    use HasBlameable, HasFactory, HasUuids, SoftDeletes;


    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'customer_id',
        'report_type',
        'project_id',
        'service_id',
        'task_id',
        'hours',
        'minutes',
        'content',
        'created_by',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(ProjectTask::class, 'task_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
