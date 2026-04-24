<?php

namespace App\Http\Controllers;

use App\Models\InternetPackage;
use App\Models\Operator;
use App\Models\TariffSeoContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TariffController extends Controller
{
    // -----------------------------------------------------------------------
    // Yardımcı: tr-provinces.json'dan il/ilçe kataloğu (1 saat cache)
    // -----------------------------------------------------------------------
    private function provinceCatalog(): array
    {
        return Cache::remember('province_catalog', 3600, function () {
            $raw = json_decode(
                file_get_contents(public_path('data/tr-provinces.json')),
                true
            );

            $catalog = [];
            foreach ($raw['data'] ?? [] as $province) {
                $slug = Str::slug($province['name']);
                $districts = [];
                foreach ($province['districts'] ?? [] as $d) {
                    $dSlug = Str::slug($d['name']);
                    $districts[$dSlug] = $d['name'];
                }
                $catalog[$slug] = [
                    'name'      => $province['name'],
                    'districts' => $districts,
                ];
            }
            return $catalog;
        });
    }

    // -----------------------------------------------------------------------
    // GET /internet-tarifeleri/{city-url-slug}
    // Örn: /internet-tarifeleri/ucuz-istanbul-ev-interneti-fiyatlari
    // -----------------------------------------------------------------------
    public function city(string $urlSlug): \Illuminate\View\View|\Illuminate\Http\Response
    {
        // URL slug'ından il slug'ını çıkar: "ucuz-{city}-ev-interneti-fiyatlari"
        $citySlug = $this->extractCitySlug($urlSlug);

        if (! $citySlug) {
            abort(404);
        }

        $catalog = $this->provinceCatalog();

        if (! isset($catalog[$citySlug])) {
            abort(404);
        }

        $cityName = $catalog[$citySlug]['name'];
        $districts = $catalog[$citySlug]['districts']; // [slug => name]

        // SEO içeriği
        $seo = TariffSeoContent::where('page_key', TariffSeoContent::cityKey($citySlug))->first();

        // Paketler: null available_provinces = tüm Türkiye, ya da bu ili içerenler
        $packages = $this->getPackagesForCity($citySlug);

        // Operatörler (filtre sidebar için)
        $operators = Operator::where('is_active', true)->get();

        // İlçe GeoJSON harita dosyası
        $geojsonFile         = public_path('data/districts/' . $citySlug . '.geojson');
        $districtsGeoJsonUrl = is_file($geojsonFile)
            ? '/data/districts/' . $citySlug . '.geojson'
            : null;

        return view('frontend.tariffs.city', compact(
            'citySlug',
            'cityName',
            'districts',
            'packages',
            'operators',
            'seo',
            'urlSlug',
            'districtsGeoJsonUrl',
        ));
    }

    // -----------------------------------------------------------------------
    // GET /internet-tarifeleri/{city-slug}/{district-url-slug}
    // Örn: /internet-tarifeleri/istanbul/ucuz-pendik-ev-interneti-fiyatlari
    // -----------------------------------------------------------------------
    public function district(string $citySlug, string $urlSlug): \Illuminate\View\View|\Illuminate\Http\Response
    {
        $catalog = $this->provinceCatalog();

        if (! isset($catalog[$citySlug])) {
            abort(404);
        }

        // URL slug'ından ilçe slug'ını çıkar: "ucuz-{district}-ev-interneti-fiyatlari"
        $districtSlug = $this->extractDistrictSlug($urlSlug);

        if (! $districtSlug) {
            abort(404);
        }

        $districts = $catalog[$citySlug]['districts'];

        if (! isset($districts[$districtSlug])) {
            abort(404);
        }

        $cityName     = $catalog[$citySlug]['name'];
        $districtName = $districts[$districtSlug];

        // SEO içeriği
        $seo = TariffSeoContent::where(
            'page_key',
            TariffSeoContent::districtKey($citySlug, $districtSlug)
        )->first();

        // Paketler
        $packages = $this->getPackagesForCity($citySlug);

        // Operatörler (filtre sidebar için)
        $operators = Operator::where('is_active', true)->get();

        return view('frontend.tariffs.district', compact(
            'citySlug',
            'cityName',
            'districtSlug',
            'districtName',
            'packages',
            'operators',
            'seo',
            'urlSlug',
        ));
    }

    // -----------------------------------------------------------------------
    // Paket sorgusu: null available_provinces = tüm Türkiye
    // -----------------------------------------------------------------------
    private function getPackagesForCity(string $citySlug): \Illuminate\Database\Eloquent\Collection
    {
        return InternetPackage::with('operator')
            ->where('is_active', true)
            ->where(function ($q) use ($citySlug) {
                $q->whereNull('available_provinces')
                  ->orWhereJsonContains('available_provinces', $citySlug);
            })
            ->orderByDesc('is_sponsored')
            ->orderBy('price')
            ->get();
    }

    // -----------------------------------------------------------------------
    // "ucuz-istanbul-ev-interneti-fiyatlari" → "istanbul"
    // -----------------------------------------------------------------------
    private function extractCitySlug(string $urlSlug): ?string
    {
        // Pattern: ucuz-{slug}-ev-interneti-fiyatlari
        if (preg_match('/^ucuz-(.+)-ev-interneti-fiyatlari$/', $urlSlug, $m)) {
            return $m[1];
        }
        return null;
    }

    // -----------------------------------------------------------------------
    // "ucuz-pendik-ev-interneti-fiyatlari" → "pendik"
    // -----------------------------------------------------------------------
    private function extractDistrictSlug(string $urlSlug): ?string
    {
        if (preg_match('/^ucuz-(.+)-ev-interneti-fiyatlari$/', $urlSlug, $m)) {
            return $m[1];
        }
        return null;
    }
}
