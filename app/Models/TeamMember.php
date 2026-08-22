<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'designation',
        'qualification',
        'department',
        'short_bio',
        'full_bio',
        'photo',
        'experience_years',
        'social_links',
        'status',
        'is_lead',
        'sort_order',
    ];

    protected $casts = [
        'social_links' => 'array',
        'status' => 'boolean',
        'is_lead' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
