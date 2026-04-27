<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CustomCode extends Model
{
    protected $fillable = ['key', 'label', 'content'];

    public static function get(string $key): ?string
    {
        return Cache::remember("custom_code:{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->value('content');
        });
    }

    public static function set(string $key, string $label, ?string $content): void
    {
        static::updateOrCreate(['key' => $key], ['label' => $label, 'content' => $content]);
        Cache::forget("custom_code:{$key}");
    }
}
