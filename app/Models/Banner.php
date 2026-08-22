<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'badge_text',
        'button_text',
        'button_url',
        'secondary_button_text',
        'secondary_button_url',
        'image',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
