<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationMetaTemplate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'meta_title_tpl',
        'meta_description_tpl',
        'h1_tpl',
        'intro_tpl',
        'seo_footer_tpl',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Placeholder'ları gerçek değerlerle değiştir.
     */
    public function render(string $cityName, string $citySlug, string $districtName = '', string $districtSlug = ''): array
    {
        $map = [
            '{il}'       => $cityName,
            '{ilce}'     => $districtName,
            '{il_seo}'   => $citySlug,
            '{ilce_seo}' => $districtSlug,
        ];

        $replace = fn (?string $tpl) => $tpl ? strtr($tpl, $map) : null;

        return [
            'meta_title'       => $replace($this->meta_title_tpl),
            'meta_description' => $replace($this->meta_description_tpl),
            'h1'               => $replace($this->h1_tpl),
            'intro'            => $replace($this->intro_tpl),
            'seo_footer'       => $replace($this->seo_footer_tpl),
        ];
    }

    /**
     * Varsayılan district şablonunu getir.
     */
    public static function defaultDistrict(): ?self
    {
        return static::where('type', 'district')->where('is_default', true)->first();
    }

    /**
     * Varsayılan city şablonunu getir.
     */
    public static function defaultCity(): ?self
    {
        return static::where('type', 'city')->where('is_default', true)->first();
    }
}
