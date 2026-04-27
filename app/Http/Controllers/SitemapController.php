<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\InternetPackage;
use App\Models\Operator;
use App\Models\TariffSeoContent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = Cache::remember('sitemap', 3600, function () {
            $urls = [];
            $base = config('app.url');

            // Statik sayfalar
            $statics = ['/', '/internet-paketleri', '/markalar', '/karsilastir', '/hiz-testi', '/taahhut-sayaci', '/blog'];
            foreach ($statics as $path) {
                $urls[] = ['loc' => $base . $path, 'priority' => '0.9', 'changefreq' => 'weekly'];
            }

            // Paketler
            InternetPackage::where('is_active', true)->select('slug', 'updated_at')->each(function ($p) use (&$urls, $base) {
                $urls[] = ['loc' => $base . '/internet-paketleri/' . $p->slug, 'priority' => '0.8', 'changefreq' => 'weekly', 'lastmod' => $p->updated_at->toAtomString()];
            });

            // Operatörler
            Operator::where('is_active', true)->select('slug', 'updated_at')->each(function ($o) use (&$urls, $base) {
                $urls[] = ['loc' => $base . '/markalar/' . $o->slug, 'priority' => '0.7', 'changefreq' => 'monthly'];
            });

            // Blog
            Blog::where('is_active', true)->whereNotNull('published_at')->select('slug', 'updated_at')->each(function ($b) use (&$urls, $base) {
                $urls[] = ['loc' => $base . '/blog/' . $b->slug, 'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => $b->updated_at->toAtomString()];
            });

            // Tarife sayfaları
            TariffSeoContent::select('city_slug', 'district_slug', 'updated_at')->each(function ($t) use (&$urls, $base) {
                if ($t->district_slug) {
                    $loc = $base . '/internet-tarifeleri/' . $t->city_slug . '/ucuz-' . $t->district_slug . '-ev-interneti-fiyatlari';
                } else {
                    $loc = $base . '/internet-tarifeleri/ucuz-' . $t->city_slug . '-ev-interneti-fiyatlari';
                }
                $urls[] = ['loc' => $loc, 'priority' => '0.8', 'changefreq' => 'weekly', 'lastmod' => $t->updated_at->toAtomString()];
            });

            return $urls;
        });

        return response()->view('sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}
