<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gallery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class)->orderBy('sort_order', 'asc');
    }
}
