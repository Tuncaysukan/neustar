<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TariffSeoContent extends Model
{
    protected $table = 'tariff_seo_contents';

    protected $fillable = [
        'page_key',
        'city_slug',
        'city_name',
        'district_slug',
        'district_name',
        'h1_title',
        'intro_text',
        'seo_footer_text',
        'faqs',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'faqs' => 'array',
    ];

    /**
     * İl sayfası için page_key üret.
     */
    public static function cityKey(string $citySlug): string
    {
        return 'tariff_city:' . $citySlug;
    }

    /**
     * İlçe sayfası için page_key üret.
     */
    public static function districtKey(string $citySlug, string $districtSlug): string
    {
        return 'tariff_district:' . $citySlug . ':' . $districtSlug;
    }

    /**
     * İl sayfası için varsayılan H1 başlığı.
     */
    public function resolvedH1(): string
    {
        if ($this->h1_title) {
            return $this->h1_title;
        }

        if ($this->district_name) {
            return $this->city_name . ' ' . $this->district_name . ' Ev İnternet Kampanyaları ve Fiyat Karşılaştırma';
        }

        return $this->city_name . ' Ev İnternet Kampanyaları ve Fiyat Karşılaştırma';
    }

    /**
     * İl sayfası için varsayılan giriş metni.
     */
    public function resolvedIntro(): string
    {
        if ($this->intro_text) {
            return $this->intro_text;
        }

        if ($this->district_name) {
            return $this->city_name . ' ' . $this->district_name . ' için hazırladığımız güncel ev interneti tarife listesidir.';
        }

        return $this->city_name . '\'daki ev interneti kullanıcıları için hazırladığımız güncel tarife listesidir.';
    }

    /**
     * Sayfa URL slug'ı — il sayfası için.
     * Örn: ucuz-istanbul-ev-interneti-fiyatlari
     */
    public static function cityUrlSlug(string $citySlug): string
    {
        return 'ucuz-' . $citySlug . '-ev-interneti-fiyatlari';
    }

    /**
     * Sayfa URL slug'ı — ilçe sayfası için.
     * Örn: ucuz-pendik-ev-interneti-fiyatlari
     */
    public static function districtUrlSlug(string $districtSlug): string
    {
        return 'ucuz-' . $districtSlug . '-ev-interneti-fiyatlari';
    }
}
