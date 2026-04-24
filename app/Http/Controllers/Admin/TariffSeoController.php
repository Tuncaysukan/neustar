<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TariffSeoContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TariffSeoController extends Controller
{
    public function index(Request $request)
    {
        $query = TariffSeoContent::latest();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('city_name', 'like', "%{$search}%")
                  ->orWhere('district_name', 'like', "%{$search}%")
                  ->orWhere('page_key', 'like', "%{$search}%");
            });
        }

        if ($type = $request->query('type')) {
            if ($type === 'city') {
                $query->whereNull('district_slug');
            } elseif ($type === 'district') {
                $query->whereNotNull('district_slug');
            }
        }

        $items = $query->paginate(15)->withQueryString();

        return view('admin.tariff-seo.index', compact('items'));
    }

    public function create()
    {
        $catalog = $this->provinceCatalog();
        return view('admin.tariff-seo.create', compact('catalog'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'city_slug'        => ['required', 'string', 'max:100'],
            'city_name'        => ['required', 'string', 'max:100'],
            'district_slug'    => ['nullable', 'string', 'max:100'],
            'district_name'    => ['nullable', 'string', 'max:100'],
            'h1_title'         => ['nullable', 'string', 'max:255'],
            'intro_text'       => ['nullable', 'string'],
            'seo_footer_text'  => ['nullable', 'string'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ]);

        // page_key otomatik üret
        $validated['page_key'] = $validated['district_slug']
            ? TariffSeoContent::districtKey($validated['city_slug'], $validated['district_slug'])
            : TariffSeoContent::cityKey($validated['city_slug']);

        // Aynı page_key zaten varsa güncelle
        TariffSeoContent::updateOrCreate(
            ['page_key' => $validated['page_key']],
            $validated
        );

        Cache::forget('province_catalog');

        return redirect()->route('admin.tariff-seo.index')
            ->with('success', 'Tarife SEO içeriği başarıyla kaydedildi.');
    }

    public function edit(TariffSeoContent $tariffSeo)
    {
        $catalog = $this->provinceCatalog();
        return view('admin.tariff-seo.edit', ['item' => $tariffSeo, 'catalog' => $catalog]);
    }

    public function update(Request $request, TariffSeoContent $tariffSeo)
    {
        $validated = $request->validate([
            'h1_title'         => ['nullable', 'string', 'max:255'],
            'intro_text'       => ['nullable', 'string'],
            'seo_footer_text'  => ['nullable', 'string'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ]);

        $tariffSeo->update($validated);

        return redirect()->route('admin.tariff-seo.index')
            ->with('success', 'Tarife SEO içeriği güncellendi.');
    }

    public function destroy(TariffSeoContent $tariffSeo)
    {
        $tariffSeo->delete();
        return redirect()->route('admin.tariff-seo.index')
            ->with('success', 'Tarife SEO içeriği silindi.');
    }

    // -----------------------------------------------------------------------
    // Yardımcı: tr-provinces.json kataloğu
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
}
