<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'customer_id',
        'type',
        'name',
        'url'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
