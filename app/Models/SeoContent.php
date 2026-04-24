<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SeoContent extends Model
{
    protected $fillable = [
        'page_key',
        'title',
        'content',
        'meta_title',
        'meta_description',
    ];

    /**
     * page_key'e göre içerik getir — 1 saat cache.
     */
    public static function forKey(string $key): ?self
    {
        return Cache::remember("seo_content:{$key}", 3600, function () use ($key) {
            return static::where('page_key', $key)->first();
        });
    }

    /**
     * Cache'i temizle (kayıt güncellenince çağrılır).
     */
    public static function clearCache(string $key): void
    {
        Cache::forget("seo_content:{$key}");
    }
}
