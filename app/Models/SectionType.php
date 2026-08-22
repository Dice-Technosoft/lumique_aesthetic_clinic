<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'key',
        'description',
        'icon',
        'default_settings',
    ];

    protected $casts = [
        'default_settings' => 'array',
    ];
}
