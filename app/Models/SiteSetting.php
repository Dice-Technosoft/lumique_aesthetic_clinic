<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            'number' => is_numeric($setting->value) ? $setting->value + 0 : $setting->value,
            default => $setting->value,
        };
    }

    public static function set(string $key, $value, string $group = 'general', string $type = 'string', bool $isPublic = true)
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
            $type = 'json';
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
            $type = 'boolean';
        }

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'group' => $group,
                'type' => $type,
                'is_public' => $isPublic,
            ]
        );
    }
}
