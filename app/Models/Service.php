<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'icon',
        'featured_image',
        'gallery_images',
        'gallery_videos',
        'video_url',
        'video_title',
        'short_description',
        'description',
        'duration',
        'downtime',
        'price_starting_at',
        'benefits',
        'faqs',
        'procedure_steps',
        'status',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'gallery_videos' => 'array',
        'benefits' => 'array',
        'faqs' => 'array',
        'procedure_steps' => 'array',
        'is_featured' => 'boolean',
    ];

    public function faqsList(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'model');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
