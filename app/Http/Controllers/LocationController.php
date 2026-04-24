<?php

namespace App\Http\Controllers;

use App\Models\InfrastructureLead;
use App\Services\DsmartAddressService;
use App\Services\InfrastructureLookupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    /**
     * POST /altyapi-sorgu — adres bilgisinden altyapı durumu döner.
     * Frontend (district-lookup.js) buraya fetch atıyor.
     */
    public function lookup(Request $request, InfrastructureLookupService $service): JsonResponse
    {
        $data = $request->validate([
            'city'         => ['required', 'string', 'max:100'],
            'district'     => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['nullable', 'string', 'max:150'],
            'street'       => ['nullable', 'string', 'max:200'],
            'no'           => ['nullable', 'string', 'max:40'],
        ]);

        $result = $service->lookup($data);

        return response()->json($result);
    }

    /**
     * POST /altyapi-basvuru — sonuç sonrası ad/telefon bırakan kullanıcıyı kaydeder.
     * Frontend sorgu sonucu snapshot'ını da birlikte gönderir.
     */
    public function submitLead(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name'    => ['required', 'string', 'max:120'],
            'phone'        => ['required', 'string', 'max:32', 'regex:/^[0-9 +()\-]{7,}$/'],
            'email'        => ['nullable', 'email', 'max:160'],

            'city'         => ['required', 'string', 'max:100'],
            'district'     => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['nullable', 'string', 'max:160'],
            'street'       => ['nullable', 'string', 'max:200'],
            'building_no'  => ['nullable', 'string', 'max:40'],

            'lookup'       => ['nullable', 'array'],
            'hp'           => ['nullable', 'string', 'max:0'], // honeypot — boş olmalı
        ]);

        // Aynı telefonla son 60 saniyede yeni kayıt varsa engelle (duplike submit koruması)
        $recent = InfrastructureLead::query()
            ->where('phone', $data['phone'])
            ->where('created_at', '>=', now()->subSeconds(60))
            ->exists();

        if ($recent) {
            return response()->json([
                'ok'      => true,
                'message' => 'Başvurun zaten alındı, en kısa sürede döneceğiz.',
                'duplicate' => true,
            ]);
        }

        $lead = InfrastructureLead::create([
            'full_name'         => $data['full_name'],
            'phone'             => $data['phone'],
            'email'             => $data['email'] ?? null,
            'city_slug'         => Str::slug($data['city']),
            'district_slug'     => !empty($data['district']) ? Str::slug($data['district']) : null,
            'city_name'         => $data['city'],
            'district_name'     => $data['district'] ?? null,
            'neighborhood_name' => $data['neighborhood'] ?? null,
            'street'            => $data['street'] ?? null,
            'building_no'       => $data['building_no'] ?? null,
            'lookup_snapshot'   => $data['lookup'] ?? null,
            'status'            => 'new',
            'ip'                => $request->ip(),
            'user_agent'        => substr((string) $request->userAgent(), 0, 255),
        ]);

        return response()->json([
            'ok'      => true,
            'id'      => $lead->id,
            'message' => 'Başvurun alındı — uzman ekibimiz en kısa sürede seni arayacak.',
        ]);
    }

    /**
     * GET /api/adres/mahalleler/{city-slug}/{district-slug}
     *
     * DSMART (telkotürk backend) üzerinden mahalle listesi.
     * Slug → ilKod → ilceKod → neighborhoods zincirini server-side'da çözer.
     */
    public function neighborhoods(string $city, string $district, DsmartAddressService $svc): JsonResponse
    {
        $ilKod = $svc->findCityIdBySlug($city);
        if (!$ilKod) {
            return response()->json(['ok' => false, 'message' => 'İl bulunamadı.', 'items' => []]);
        }

        $ilceKod = $svc->findDistrictIdBySlug($ilKod, $district);
        if (!$ilceKod) {
            return response()->json(['ok' => false, 'message' => 'İlçe bulunamadı.', 'items' => []]);
        }

        $items = $svc->neighborhoods($ilceKod);

        return response()->json([
            'ok'      => true,
            'ilKod'   => $ilKod,
            'ilceKod' => $ilceKod,
            'items'   => array_map(
                fn ($x) => ['id' => (string) $x['id'], 'name' => $x['name'] ?? ''],
                $items,
            ),
        ]);
    }

    /** GET /api/adres/sokaklar/{mahalleKod} */
    public function streets(string $mahalleKod, DsmartAddressService $svc): JsonResponse
    {
        $items = $svc->streets($mahalleKod);

        return response()->json([
            'ok'    => true,
            'items' => array_map(
                fn ($x) => ['id' => (string) $x['id'], 'name' => $x['name'] ?? ''],
                $items,
            ),
        ]);
    }

    /** GET /api/adres/binalar/{csbmKod} */
    public function buildings(string $csbmKod, DsmartAddressService $svc): JsonResponse
    {
        $items = $svc->buildings($csbmKod);

        return response()->json([
            'ok'    => true,
            'items' => array_map(fn ($x) => [
                'id'      => (string) $x['id'],
                'name'    => $x['name'] ?? '',
                'bbkCode' => (string) ($x['bbkCode'] ?? $x['id'] ?? ''),
            ], $items),
        ]);
    }

    /** GET /api/adres/daireler/{binaKodu} */
    public function doors(string $binaKodu, DsmartAddressService $svc): JsonResponse
    {
        $items = $svc->doors($binaKodu);

        return response()->json([
            'ok'    => true,
            'items' => array_map(fn ($x) => [
                'id'      => (string) $x['id'],
                'name'    => $x['name'] ?? '',
                'bbkCode' => (string) ($x['bbkCode'] ?? ''),
            ], $items),
        ]);
    }

    public function city(Request $request, string $city)
    {
        $province = $this->findProvinceBySlug($city);

        // Slug eşleşmezse yine de sayfayı çizsin, ilçeler boş geçsin
        $cityName  = $province['name'] ?? $this->humanize($city);
        $districts = $province['districts'] ?? [];
        $query     = $this->extractQuery($request);

        // İlçe polygon GeoJSON dosyası varsa harita için URL ver
        $geojsonFile   = public_path('data/districts/' . $city . '.geojson');
        $districtsGeoJsonUrl = is_file($geojsonFile)
            ? '/data/districts/' . $city . '.geojson'
            : null;

        // İl tarife sayfası URL'si
        $tariffCityUrl = route('tariffs.city', [
            'urlSlug' => 'ucuz-' . $city . '-ev-interneti-fiyatlari',
        ]);

        return view('frontend.locations.city', compact(
            'cityName', 'districts', 'query', 'districtsGeoJsonUrl', 'tariffCityUrl'
        ) + ['citySlug' => $city]);
    }

    public function district(Request $request, string $city, string $district)
    {
        $cityName     = $this->humanize($city);
        $districtName = $this->humanize($district);
        $query        = $this->extractQuery($request);

        // Bu il/ilçeye uygun paketleri çek (null = tüm Türkiye + bu ili içerenler)
        $packages = \App\Models\InternetPackage::with('operator')
            ->where('is_active', true)
            ->where(function ($q) use ($city) {
                $q->whereNull('available_provinces')
                  ->orWhereJsonContains('available_provinces', $city);
            })
            ->orderByDesc('is_sponsored')
            ->orderBy('price')
            ->get();

        // İlçe tarife sayfası URL'si (varsa)
        $tariffDistrictUrl = route('tariffs.district', [
            'citySlug' => $city,
            'urlSlug'  => 'ucuz-' . $district . '-ev-interneti-fiyatlari',
        ]);

        return view('frontend.locations.district', compact(
            'cityName', 'districtName', 'query', 'packages', 'tariffDistrictUrl'
        ) + ['citySlug' => $city, 'districtSlug' => $district]);
    }

    /**
     * İl slug'ına göre provinces snapshot'ından kayıt döner:
     *   ['name' => 'İstanbul', 'districts' => [['name' => ..., 'slug' => ...], ...]]
     */
    private function findProvinceBySlug(string $slug): ?array
    {
        $catalog = $this->provinceCatalog();
        return $catalog[$slug] ?? null;
    }

    /**
     * tr-provinces.json dosyasını okuyup slug => {name, districts[]} formatına
     * dönüştürür. 1 saat cache.
     */
    private function provinceCatalog(): array
    {
        return Cache::remember('tr.provinces.catalog.v1', now()->addHour(), function () {
            $path = public_path('data/tr-provinces.json');
            if (!is_file($path)) {
                return [];
            }
            $raw = @file_get_contents($path);
            if ($raw === false) {
                return [];
            }
            $json = json_decode($raw, true);
            $items = $json['data'] ?? [];
            $out   = [];

            foreach ($items as $p) {
                $provinceSlug = Str::slug($p['name'] ?? '');
                if ($provinceSlug === '') continue;

                $districts = collect($p['districts'] ?? [])
                    ->map(fn ($d) => [
                        'name' => $d['name'] ?? '',
                        'slug' => Str::slug($d['name'] ?? ''),
                    ])
                    ->filter(fn ($d) => $d['name'] !== '' && $d['slug'] !== '')
                    ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all();

                $out[$provinceSlug] = [
                    'name'      => $p['name'],
                    'districts' => $districts,
                ];
            }

            return $out;
        });
    }

    private function humanize(string $slug): string
    {
        return mb_convert_case(str_replace('-', ' ', $slug), MB_CASE_TITLE, 'UTF-8');
    }

    private function extractQuery(Request $request): array
    {
        return [
            'mahalle' => trim((string) $request->query('mahalle', '')),
            'sokak'   => trim((string) $request->query('sokak', '')),
            'no'      => trim((string) $request->query('no', '')),
        ];
    }
}
