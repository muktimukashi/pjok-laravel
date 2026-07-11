<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PjokRecord extends Model
{
    protected $fillable = [
        'type',
        'code',
        'name',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
