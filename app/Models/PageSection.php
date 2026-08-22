<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'page_id',
        'section_type_id',
        'section_type_key',
        'title',
        'subtitle',
        'content',
        'image',
        'settings',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'settings' => 'array',
        'status' => 'boolean',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function sectionType(): BelongsTo
    {
        return $this->belongsTo(SectionType::class);
    }
}
