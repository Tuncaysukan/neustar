<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\InfrastructureStatus;
use Illuminate\Database\Seeder;

/**
 * Doğrulanmış "managed" kayıtlar — admin panelinden düzenlenebilir.
 * Buradaki hedef: ilk açılışta büyükşehirlerde gerçekçi bir his vermek.
 */
class InfrastructureStatusSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // İstanbul — merkez ilçeler
            ['istanbul', 'kadikoy',     null, 'İstanbul', 'Kadıköy',     null, 97, 85, 99, 1000, 100],
            ['istanbul', 'besiktas',    null, 'İstanbul', 'Beşiktaş',    null, 95, 82, 99, 1000, 100],
            ['istanbul', 'sisli',       null, 'İstanbul', 'Şişli',       null, 94, 80, 99, 1000, 100],
            ['istanbul', 'beylikduzu',  null, 'İstanbul', 'Beylikdüzü',  null, 90, 78, 98, 1000, 100],
            ['istanbul', 'umraniye',    null, 'İstanbul', 'Ümraniye',    null, 86, 80, 98,  500,  50],
            ['istanbul', 'pendik',      null, 'İstanbul', 'Pendik',      null, 82, 78, 98,  500,  50],

            // Ankara
            ['ankara',   'cankaya',     null, 'Ankara',   'Çankaya',     null, 94, 80, 99, 1000, 100],
            ['ankara',   'kecioren',    null, 'Ankara',   'Keçiören',    null, 86, 78, 99,  500,  50],
            ['ankara',   'yenimahalle', null, 'Ankara',   'Yenimahalle', null, 88, 78, 99,  500,  50],

            // İzmir
            ['izmir',    'konak',       null, 'İzmir',    'Konak',       null, 92, 80, 99, 1000, 100],
            ['izmir',    'bornova',     null, 'İzmir',    'Bornova',     null, 90, 78, 99,  500,  50],
            ['izmir',    'karsiyaka',   null, 'İzmir',    'Karşıyaka',   null, 91, 80, 99,  500,  50],

            // İl-geneli baseline (ilçe/mahalle spesifik yoksa fallback override)
            ['bursa',    null,          null, 'Bursa',    null,          null, 78, 72, 98,  500,  50],
            ['antalya',  null,          null, 'Antalya',  null,          null, 80, 74, 98,  500,  50],
            ['kocaeli',  null,          null, 'Kocaeli',  null,          null, 82, 75, 98,  500,  50],
            ['hakkari',  null,          null, 'Hakkari',  null,          null, 25, 45, 95,   50,   5],
        ];

        foreach ($rows as [$city, $district, $neighborhood, $cityName, $districtName, $neighborhoodName,
            $fiber, $vdsl, $adsl, $maxDown, $maxUp]) {
            InfrastructureStatus::updateOrCreate(
                [
                    'city_slug'         => $city,
                    'district_slug'     => $district,
                    'neighborhood_slug' => $neighborhood,
                ],
                [
                    'city_name'         => $cityName,
                    'district_name'     => $districtName,
                    'neighborhood_name' => $neighborhoodName,
                    'fiber_coverage'    => $fiber,
                    'vdsl_coverage'     => $vdsl,
                    'adsl_coverage'     => $adsl,
                    'max_down_mbps'     => $maxDown,
                    'max_up_mbps'       => $maxUp,
                ]
            );
        }
    }
}
