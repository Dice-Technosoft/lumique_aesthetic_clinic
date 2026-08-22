<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'service_id',
        'question',
        'answer',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
