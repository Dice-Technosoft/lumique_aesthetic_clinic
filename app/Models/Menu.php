<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'location',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('sort_order', 'asc');
    }

    public function allItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order', 'asc');
    }
}
