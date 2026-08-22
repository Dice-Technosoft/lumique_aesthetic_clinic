<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'category_id')->orderBy('sort_order', 'asc');
    }
}
