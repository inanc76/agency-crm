<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

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
