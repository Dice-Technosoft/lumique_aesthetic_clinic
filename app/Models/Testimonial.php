<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'designation',
        'treatment_taken',
        'photo',
        'rating',
        'content',
        'source',
        'status',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
