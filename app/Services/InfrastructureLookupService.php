<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\InfrastructureStatus;
use Illuminate\Support\Str;

/**
 * Altyapı sorgu servisi.
 *
 * Öncelik sırası:
 *   1) Admin tarafından girilen en spesifik DB kaydı (neighborhood > district > city)
 *   2) Deterministik fallback: il/ilçe/mahalle slug'ından stabil hash üretip
 *      büyükşehir/kıyı/metropol eğimlerini uygulayarak makul bir tahmin döner.
 *
 * Çıktı: tek bir associative array — Controller bunu doğrudan JSON'a çevirir.
 */
class InfrastructureLookupService
{
    /** Büyükşehir + yüksek fiber yoğunluğu olduğu kabul edilen iller. */
    private const METROS = [
        'istanbul', 'ankara', 'izmir', 'bursa', 'antalya', 'kocaeli',
        'adana', 'gaziantep', 'konya', 'mersin', 'eskisehir', 'samsun',
        'kayseri', 'denizli', 'sakarya', 'tekirdag', 'trabzon',
    ];

    /** Fiber yoğunluğu düşük, ADSL baskın iller (örnek set — admin'den override edilebilir). */
    private const LOW_FIBER = [
        'hakkari', 'sirnak', 'agri', 'bayburt', 'tunceli', 'ardahan',
        'bitlis', 'mus', 'siirt', 'kars', 'igdir', 'van',
    ];

    public function lookup(array $input): array
    {
        $citySlug         = $this->slug($input['city'] ?? null);
        $districtSlug     = $this->slug($input['district'] ?? null);
        $neighborhoodSlug = $this->slug($input['neighborhood'] ?? null);

        if ($citySlug === null) {
            return $this->empty('İl bilgisi gerekli.');
        }

        // ---- 1) DB lookup ----------------------------------------------------
        $row = InfrastructureStatus::query()
            ->lookup($citySlug, $districtSlug, $neighborhoodSlug)
            ->orderByRaw('(neighborhood_slug IS NOT NULL) DESC')
            ->orderByRaw('(district_slug IS NOT NULL) DESC')
            ->first();

        if ($row) {
            return $this->toPayload($row->toArray(), source: 'managed');
        }

        // ---- 2) Deterministik fallback --------------------------------------
        return $this->toPayload(
            $this->fallback($citySlug, $districtSlug, $neighborhoodSlug, $input),
            source: 'estimated',
        );
    }

    // =========================================================================
    //  Fallback generator
    // =========================================================================

    private function fallback(string $city, ?string $district, ?string $neighborhood, array $input): array
    {
        // Scope'a göre kararlı bir seed üret; aynı adres aynı sonucu versin.
        $seed  = crc32($city . '|' . ($district ?? '') . '|' . ($neighborhood ?? ''));
        $rand  = static fn (int $offset, int $min, int $max) =>
            $min + (abs($seed ^ $offset) % max(1, $max - $min + 1));

        // Baseline — iller için coarse tuning
        if (in_array($city, self::LOW_FIBER, true)) {
            $fiber = $rand(1, 15, 45);
            $vdsl  = $rand(2, 35, 65);
            $adsl  = $rand(3, 85, 99);
            $down  = $rand(4, 24, 100);
        } elseif (in_array($city, self::METROS, true)) {
            $fiber = $rand(1, 72, 95);
            $vdsl  = $rand(2, 60, 85);
            $adsl  = $rand(3, 92, 99);
            $down  = $rand(4, 100, 1000);
        } else {
            $fiber = $rand(1, 45, 78);
            $vdsl  = $rand(2, 55, 80);
            $adsl  = $rand(3, 90, 99);
            $down  = $rand(4, 50, 300);
        }

        // Mahalle seviyesindeki tahmin biraz daha dalgalı olsun
        if ($neighborhood) {
            $fiber = max(0, min(100, $fiber + $rand(5, -10, 10)));
            $vdsl  = max(0, min(100, $vdsl  + $rand(6, -8, 8)));
        }

        return [
            'city_slug'         => $city,
            'district_slug'     => $district,
            'neighborhood_slug' => $neighborhood,
            'city_name'         => $this->humanize($city),
            'district_name'     => $district ? $this->humanize($district) : null,
            'neighborhood_name' => $input['neighborhood'] ?? null,
            'fiber_coverage'    => $fiber,
            'vdsl_coverage'     => $vdsl,
            'adsl_coverage'     => $adsl,
            'max_down_mbps'     => $down,
            'max_up_mbps'       => (int) floor($down / 10),
            'notes'             => null,
        ];
    }

    // =========================================================================
    //  Formatters
    // =========================================================================

    private function toPayload(array $row, string $source): array
    {
        $technologies = [];
        foreach (['fiber' => 'Fiber', 'vdsl' => 'VDSL', 'adsl' => 'ADSL'] as $k => $label) {
            $pct = $row[$k . '_coverage'] ?? null;
            if ($pct === null) continue;

            $technologies[] = [
                'key'       => $k,
                'label'     => $label,
                'coverage'  => (int) $pct,
                'available' => $pct >= 50,
                'status'    => $this->statusFromCoverage((int) $pct),
            ];
        }

        $primary = collect($technologies)
            ->sortByDesc(fn ($t) => ($t['available'] ? 100 : 0) + $t['coverage'])
            ->first();

        return [
            'ok'                => true,
            'source'            => $source, // managed | estimated
            'scope'             => [
                'city'         => $row['city_name']         ?? null,
                'district'     => $row['district_name']     ?? null,
                'neighborhood' => $row['neighborhood_name'] ?? null,
            ],
            'primary'           => $primary,
            'technologies'      => $technologies,
            'max_down_mbps'     => $row['max_down_mbps'] ?? null,
            'max_up_mbps'       => $row['max_up_mbps']   ?? null,
            'notes'             => $row['notes'] ?? null,
        ];
    }

    private function statusFromCoverage(int $pct): string
    {
        return match (true) {
            $pct >= 80 => 'available',   // Kesin hizmet var
            $pct >= 50 => 'partial',     // Kısmi, port durumuna bağlı
            $pct >  0  => 'limited',     // Sınırlı
            default    => 'unavailable',
        };
    }

    private function empty(string $message): array
    {
        return [
            'ok'      => false,
            'message' => $message,
        ];
    }

    private function slug($v): ?string
    {
        if ($v === null) return null;
        $s = Str::slug((string) $v);
        return $s === '' ? null : $s;
    }

    private function humanize(string $slug): string
    {
        return mb_convert_case(str_replace('-', ' ', $slug), MB_CASE_TITLE, 'UTF-8');
    }
}
