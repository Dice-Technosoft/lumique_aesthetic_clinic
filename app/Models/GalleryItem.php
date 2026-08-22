<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'gallery_id',
        'title',
        'image_before',
        'image_after',
        'image',
        'alt_text',
        'category',
        'treatment_name',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }
}
