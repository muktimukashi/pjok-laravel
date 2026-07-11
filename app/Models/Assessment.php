<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'year', 'semester', 'class_name', 'meeting', 'type', 'materi', 'tujuan', 'aspect', 'criteria',
    ];

    protected function casts(): array
    {
        return [
            'criteria' => 'array',
        ];
    }
}
