<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMeta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'seo_metas';

    protected $fillable = [
        'model_type',
        'model_id',
        'path',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'robots',
        'schema_json',
    ];

    protected $casts = [
        'schema_json' => 'array',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
