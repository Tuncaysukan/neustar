<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocationMetaTemplate;
use App\Models\TariffSeoContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LocationMetaTemplateController extends Controller
{
    public function index()
    {
        $templates = LocationMetaTemplate::latest()->get();
        return view('admin.location-meta.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.location-meta.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:120'],
            'type'                  => ['required', 'in:city,district'],
            'meta_title_tpl'        => ['nullable', 'string', 'max:255'],
            'meta_description_tpl'  => ['nullable', 'string'],
            'h1_tpl'                => ['nullable', 'string', 'max:255'],
            'intro_tpl'             => ['nullable', 'string'],
            'seo_footer_tpl'        => ['nullable', 'string'],
            'is_default'            => ['boolean'],
        ]);

        // Yeni varsayılan atanıyorsa eskisini kaldır
        if (!empty($validated['is_default'])) {
            LocationMetaTemplate::where('type', $validated['type'])
                ->update(['is_default' => false]);
        }

        LocationMetaTemplate::create($validated);

        return redirect()->route('admin.location-meta.index')
            ->with('success', 'Şablon oluşturuldu.');
    }

    public function edit(LocationMetaTemplate $locationMeta)
    {
        return view('admin.location-meta.edit', ['template' => $locationMeta]);
    }

    public function update(Request $request, LocationMetaTemplate $locationMeta)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:120'],
            'meta_title_tpl'        => ['nullable', 'string', 'max:255'],
            'meta_description_tpl'  => ['nullable', 'string'],
            'h1_tpl'                => ['nullable', 'string', 'max:255'],
            'intro_tpl'             => ['nullable', 'string'],
            'seo_footer_tpl'        => ['nullable', 'string'],
            'is_default'            => ['boolean'],
        ]);

        if (!empty($validated['is_default'])) {
            LocationMetaTemplate::where('type', $locationMeta->type)
                ->where('id', '!=', $locationMeta->id)
                ->update(['is_default' => false]);
        }

        $locationMeta->update($validated);

        return redirect()->route('admin.location-meta.index')
            ->with('success', 'Şablon güncellendi.');
    }

    public function destroy(LocationMetaTemplate $locationMeta)
    {
        $locationMeta->delete();
        return redirect()->route('admin.location-meta.index')
            ->with('success', 'Şablon silindi.');
    }

    /**
     * POST /admin/location-meta/{template}/apply
     *
     * Şablonu tüm il veya ilçe sayfalarına tek tıkla uygular.
     * tariff_seo_contents tablosundaki kayıtları günceller.
     * Kayıt yoksa oluşturur.
     */
    public function apply(Request $request, LocationMetaTemplate $locationMeta)
    {
        $catalog = $this->provinceCatalog();
        $count   = 0;

        if ($locationMeta->type === 'city') {
            foreach ($catalog as $citySlug => $cityData) {
                $cityName = $cityData['name'];
                $rendered = $locationMeta->render($cityName, $citySlug);
                $pageKey  = TariffSeoContent::cityKey($citySlug);

                TariffSeoContent::updateOrCreate(
                    ['page_key' => $pageKey],
                    [
                        'city_slug'        => $citySlug,
                        'city_name'        => $cityName,
                        'district_slug'    => null,
                        'district_name'    => null,
                        'h1_title'         => $rendered['h1'],
                        'intro_text'       => $rendered['intro'],
                        'seo_footer_text'  => $rendered['seo_footer'],
                        'meta_title'       => $rendered['meta_title'],
                        'meta_description' => $rendered['meta_description'],
                    ]
                );
                $count++;
            }
        } else {
            // district: tüm il + ilçe kombinasyonları
            foreach ($catalog as $citySlug => $cityData) {
                $cityName  = $cityData['name'];
                foreach ($cityData['districts'] as $districtSlug => $districtName) {
                    $rendered = $locationMeta->render($cityName, $citySlug, $districtName, $districtSlug);
                    $pageKey  = TariffSeoContent::districtKey($citySlug, $districtSlug);

                    TariffSeoContent::updateOrCreate(
                        ['page_key' => $pageKey],
                        [
                            'city_slug'        => $citySlug,
                            'city_name'        => $cityName,
                            'district_slug'    => $districtSlug,
                            'district_name'    => $districtName,
                            'h1_title'         => $rendered['h1'],
                            'intro_text'       => $rendered['intro'],
                            'seo_footer_text'  => $rendered['seo_footer'],
                            'meta_title'       => $rendered['meta_title'],
                            'meta_description' => $rendered['meta_description'],
                        ]
                    );
                    $count++;
                }
            }
        }

        Cache::forget('province_catalog');

        return redirect()->route('admin.location-meta.index')
            ->with('success', "Şablon {$count} sayfaya uygulandı.");
    }

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
                $slug      = Str::slug($province['name']);
                $districts = [];
                foreach ($province['districts'] ?? [] as $d) {
                    $dSlug = Str::slug($d['name']);
                    $districts[$dSlug] = $d['name'];
                }
                $catalog[$slug] = ['name' => $province['name'], 'districts' => $districts];
            }
            return $catalog;
        });
    }
}
