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
     * page_key'e göre içerik getir. Yalnızca id cache'lenir; model her istekte DB'den okunur.
     */
    public static function forKey(string $key): ?self
    {
        $cacheKey = "seo_content:id:{$key}";

        if (! Cache::has($cacheKey)) {
            $id = static::query()->where('page_key', $key)->value('id');
            if ($id !== null) {
                Cache::put($cacheKey, $id, 3600);
            }

            return $id !== null ? static::query()->find($id) : null;
        }

        $id = Cache::get($cacheKey);
        $model = static::query()->find($id);

        if ($model === null) {
            Cache::forget($cacheKey);

            return null;
        }

        return $model;
    }

    /**
     * Cache'i temizle (kayıt güncellenince çağrılır).
     */
    public static function clearCache(string $key): void
    {
        Cache::forget("seo_content:{$key}");
        Cache::forget("seo_content:id:{$key}");
    }
}
