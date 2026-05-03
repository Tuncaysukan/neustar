<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\SeoContent;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class FrontendLayoutComposer
{
    public function compose(View $view): void
    {
        $routeName = Route::currentRouteName();
        $seoKey    = $this->seoKeyForRoute($routeName);
        $pageSeo   = $seoKey !== null ? SeoContent::forKey($seoKey) : null;

        $view->with('pageSeo', $pageSeo)->with('pageSeoKey', $seoKey);

        $view->with('layoutSite', [
            'site_name'        => (string) SiteSetting::get('site_name', config('app.name', 'Neustar')),
            'site_tagline'     => (string) SiteSetting::get('site_tagline', ''),
            'contact_email'    => (string) SiteSetting::get('contact_email', ''),
            'contact_phone'    => (string) SiteSetting::get('contact_phone', ''),
            'contact_address'  => (string) SiteSetting::get('contact_address', ''),
            'footer_copyright' => (string) SiteSetting::get('footer_copyright', ''),
            'facebook_url'     => (string) SiteSetting::get('facebook_url', ''),
            'twitter_url'      => (string) SiteSetting::get('twitter_url', ''),
            'instagram_url'    => (string) SiteSetting::get('instagram_url', ''),
            'youtube_url'      => (string) SiteSetting::get('youtube_url', ''),
            'google_analytics' => trim((string) SiteSetting::get('google_analytics', '')),
        ]);
    }

    /**
     * Admin SEO "Sayfa anahtarı" ile Laravel route adını eşleştirir.
     * Paket listesinin tüm URL varyantları tek kayıt: packages.index
     */
    private function seoKeyForRoute(?string $routeName): ?string
    {
        if ($routeName === null) {
            return null;
        }

        return match ($routeName) {
            'packages.operator', 'packages.operator_infra' => 'packages.index',
            default => $routeName,
        };
    }
}
