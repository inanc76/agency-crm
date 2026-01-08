<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;
}
