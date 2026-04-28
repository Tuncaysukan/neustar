<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'type', 'group', 'order'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('site_settings', 3600, fn() =>
            static::pluck('value', 'key')->toArray()
        );
        return $settings[$key] ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
        Cache::forget('site_settings');
    }

    public static function defaults(): array
    {
        return [
            ['key' => 'site_name',        'label' => 'Site Adı',              'type' => 'text',     'group' => 'general', 'order' => 1,  'value' => 'Neustar'],
            ['key' => 'site_tagline',     'label' => 'Slogan',                'type' => 'text',     'group' => 'general', 'order' => 2,  'value' => 'İnternet paketlerini karşılaştır'],
            ['key' => 'contact_email',    'label' => 'İletişim E-postası',    'type' => 'email',    'group' => 'contact', 'order' => 3,  'value' => 'info@neustar.com'],
            ['key' => 'contact_phone',    'label' => 'Telefon',               'type' => 'tel',      'group' => 'contact', 'order' => 4,  'value' => '0543 180 00 79'],
            ['key' => 'contact_address',  'label' => 'Adres',                 'type' => 'textarea', 'group' => 'contact', 'order' => 5,  'value' => ''],
            ['key' => 'facebook_url',     'label' => 'Facebook URL',          'type' => 'url',      'group' => 'social',  'order' => 6,  'value' => ''],
            ['key' => 'twitter_url',      'label' => 'Twitter / X URL',       'type' => 'url',      'group' => 'social',  'order' => 7,  'value' => ''],
            ['key' => 'instagram_url',    'label' => 'Instagram URL',         'type' => 'url',      'group' => 'social',  'order' => 8,  'value' => ''],
            ['key' => 'youtube_url',      'label' => 'YouTube URL',           'type' => 'url',      'group' => 'social',  'order' => 9,  'value' => ''],
            ['key' => 'google_analytics', 'label' => 'Google Analytics ID',   'type' => 'text',     'group' => 'seo',     'order' => 10, 'value' => ''],
            ['key' => 'meta_title',       'label' => 'Varsayılan Meta Başlık','type' => 'text',     'group' => 'seo',     'order' => 11, 'value' => 'Neustar — İnternet Tarifeleri'],
            ['key' => 'meta_description', 'label' => 'Varsayılan Meta Açıklama','type' => 'textarea','group' => 'seo',    'order' => 12, 'value' => 'İnternet paketlerini karşılaştır, en uygununu seç.'],
            ['key' => 'footer_copyright', 'label' => 'Footer Telif Hakkı',   'type' => 'text',     'group' => 'general', 'order' => 13, 'value' => ''],
            ['key' => 'commitment_durations', 'label' => 'Taahhüt Süreleri (ay, virgülle ayır)', 'type' => 'text', 'group' => 'general', 'order' => 14, 'value' => '12,24'],
        ];
    }
}
