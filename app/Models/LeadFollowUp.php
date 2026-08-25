<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadFollowUp extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lead_followups';

    protected $fillable = [
        'lead_id',
        'assigned_to',
        'follow_up_date',
        'follow_up_time',
        'note',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getFormattedTimeAttribute(): ?string
    {
        if (empty($this->follow_up_time)) return null;
        try {
            return \Carbon\Carbon::parse($this->follow_up_time)->format('h:i A');
        } catch (\Throwable $e) {
            return (string) $this->follow_up_time;
        }
    }
}
