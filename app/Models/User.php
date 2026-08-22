<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar_url',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    public function preferences(): HasMany
    {
        return $this->hasMany(UserPreference::class);
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function assignedInquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class, 'assigned_to');
    }

    public function hasRole(string $roleSlug): bool
    {
        return $this->roles->contains('slug', $roleSlug);
    }

    public function hasPermission(string $permissionSlug): bool
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('slug', $permissionSlug)) {
                return true;
            }
        }
        return false;
    }
}
