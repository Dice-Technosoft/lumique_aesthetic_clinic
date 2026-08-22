<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected string $cacheKey = 'site_settings_all';

    public function all(): array
    {
        return Cache::remember($this->cacheKey, 3600, function () {
            return SiteSetting::all()->mapWithKeys(function ($item) {
                $val = match ($item->type) {
                    'boolean' => filter_var($item->value, FILTER_VALIDATE_BOOLEAN),
                    'json' => json_decode($item->value, true),
                    'number' => is_numeric($item->value) ? $item->value + 0 : $item->value,
                    default => $item->value,
                };
                return [$item->key => $val];
            })->toArray();
        });
    }

    public function get(string $key, $default = null)
    {
        $settings = $this->all();
        return $settings[$key] ?? $default;
    }

    public function getPublic(): array
    {
        return Cache::remember('site_settings_public', 3600, function () {
            return SiteSetting::where('is_public', true)->get()->mapWithKeys(function ($item) {
                $val = match ($item->type) {
                    'boolean' => filter_var($item->value, FILTER_VALIDATE_BOOLEAN),
                    'json' => json_decode($item->value, true),
                    'number' => is_numeric($item->value) ? $item->value + 0 : $item->value,
                    default => $item->value,
                };
                return [$item->key => $val];
            })->toArray();
        });
    }

    public function set(string $key, $value, string $group = 'general', string $type = 'string', bool $isPublic = true): SiteSetting
    {
        $setting = SiteSetting::set($key, $value, $group, $type, $isPublic);
        $this->clearCache();
        return $setting;
    }

    public function updateBulk(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $setting = SiteSetting::where('key', $key)->first();
            if ($setting) {
                if (is_array($value)) {
                    $setting->value = json_encode($value);
                    $setting->type = 'json';
                } elseif (is_bool($value)) {
                    $setting->value = $value ? '1' : '0';
                    $setting->type = 'boolean';
                } else {
                    $setting->value = (string) $value;
                }
                $setting->save();
            } else {
                SiteSetting::set($key, $value);
            }
        }
        $this->clearCache();
    }

    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
        Cache::forget('site_settings_public');
    }
}
