<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Operator extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'website_url',
        'description',
        'seo_title',
        'seo_description',
        'seo_text',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function packages()
    {
        return $this->hasMany(InternetPackage::class);
    }

    public function logos()
    {
        return $this->hasMany(OperatorLogo::class)->orderBy('order');
    }

    public function primaryLogo(): ?OperatorLogo
    {
        return $this->logos->firstWhere('is_primary', true)
            ?? $this->logos->first();
    }

    /**
     * Normalized logo URL — handles three cases:
     *   1. External absolute URL stored in DB (https://…)
     *   2. Local storage path (operators/xyz.png) — served via /storage
     *   3. null → fallback initial avatar renders instead
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $logo = $this->logo;
            if (! $logo) {
                return null;
            }
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
                return $logo;
            }
            return '/storage/' . ltrim($logo, '/');
        });
    }

    /** First glyph of the operator name, uppercased (Unicode-safe). */
    protected function initial(): Attribute
    {
        return Attribute::get(function (): string {
            return mb_strtoupper(mb_substr((string) $this->name, 0, 1) ?: '?');
        });
    }

    /**
     * Deterministic brand accent color for the initial-avatar fallback.
     * Derives a hue from the name so different operators don't collapse
     * into the same beige block.
     */
    protected function brandHue(): Attribute
    {
        return Attribute::get(function (): int {
            $hash = 0;
            foreach (str_split((string) $this->name) as $ch) {
                $hash = ($hash * 31 + ord($ch)) & 0xFFFFFF;
            }
            return $hash % 360;
        });
    }
}
