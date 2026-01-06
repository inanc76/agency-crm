<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PanelSetting extends Model
{
    use HasUuids;

    protected $fillable = [
        'site_name',
        'favicon_path',
        'logo_path',
        'logo_scale',
        'header_bg_color',
        'menu_bg_color',
        'menu_text_color',
        'header_icon_color',
        'header_border_color',
        'header_border_width',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
