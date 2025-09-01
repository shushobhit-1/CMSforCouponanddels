<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'slides', 'is_active',
    ];

    protected $casts = [
        'slides' => 'array',
        'is_active' => 'boolean',
    ];
}
