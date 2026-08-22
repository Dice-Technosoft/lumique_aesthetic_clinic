<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'content',
        'template',
        'status',
        'published_at',
        'og_image',
        'sort_order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order', 'asc');
    }

    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'model');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
