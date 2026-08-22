<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'original_name',
        'file_name',
        'path',
        'url',
        'disk',
        'mime_type',
        'extension',
        'size',
        'alt_text',
        'title',
        'folder',
        'uploaded_by',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileUrlAttribute(): string
    {
        if ($this->url) {
            return $this->url;
        }
        return Storage::disk($this->disk)->url($this->path);
    }
}
