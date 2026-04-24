<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'category',
        'image',
        'is_active',
        'published_at',
        'views',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'published_at' => 'datetime',
        'views'        => 'integer',
    ];

    /* ---------- Scopes ---------- */

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeCategory(Builder $q, ?string $category): Builder
    {
        return $category ? $q->where('category', $category) : $q;
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (! $term) {
            return $q;
        }

        return $q->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('excerpt', 'like', "%{$term}%")
              ->orWhere('content', 'like', "%{$term}%");
        });
    }

    /* ---------- Accessors ---------- */

    /** Eğer excerpt boşsa content'ten türet. */
    protected function displayExcerpt(): Attribute
    {
        return Attribute::get(function () {
            if (! empty($this->excerpt)) {
                return $this->excerpt;
            }
            return Str::limit(strip_tags((string) $this->content), 180);
        });
    }

    /** Okuma süresi (≈ 220 kelime / dakika). */
    protected function readingTime(): Attribute
    {
        return Attribute::get(function () {
            $words = str_word_count(strip_tags((string) $this->content));
            return max(1, (int) ceil($words / 220));
        });
    }

    /** Gösterim tarihi: published_at varsa o, yoksa created_at. */
    protected function displayDate(): Attribute
    {
        return Attribute::get(fn () => $this->published_at ?? $this->created_at);
    }

    /* ---------- Helpers ---------- */

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
