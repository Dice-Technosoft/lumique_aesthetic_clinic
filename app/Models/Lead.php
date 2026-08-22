<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inquiry_id',
        'lead_source_id',
        'name',
        'email',
        'phone',
        'service_id',
        'service_name',
        'status',
        'priority',
        'assigned_to',
        'preferred_date',
        'preferred_time',
        'estimated_value',
        'notes',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'estimated_value' => 'decimal:2',
    ];

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function notesList(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }

    public function followups(): HasMany
    {
        return $this->hasMany(LeadFollowUp::class)->orderBy('follow_up_date', 'asc');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest();
    }
}
