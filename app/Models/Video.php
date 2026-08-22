<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'youtube_url',
        'youtube_video_id',
        'thumbnail',
        'description',
        'category',
        'status',
        'is_featured',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
