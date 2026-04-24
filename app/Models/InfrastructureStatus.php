<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfrastructureStatus extends Model
{
    protected $fillable = [
        'city_slug', 'district_slug', 'neighborhood_slug',
        'city_name', 'district_name', 'neighborhood_name',
        'fiber_coverage', 'vdsl_coverage', 'adsl_coverage',
        'max_down_mbps', 'max_up_mbps',
        'notes',
    ];

    protected $casts = [
        'fiber_coverage' => 'integer',
        'vdsl_coverage'  => 'integer',
        'adsl_coverage'  => 'integer',
        'max_down_mbps'  => 'integer',
        'max_up_mbps'    => 'integer',
    ];

    /**
     * Skor bazlı en spesifik eşleşmeyi tek sorguda bulan scope.
     * Öncelik: (city + district + neighborhood) > (city + district) > (city).
     */
    public function scopeLookup($q, ?string $city, ?string $district = null, ?string $neighborhood = null)
    {
        return $q->where('city_slug', $city)
            ->where(function ($q) use ($district, $neighborhood) {
                $q->whereNull('district_slug')
                    ->orWhere(function ($q) use ($district, $neighborhood) {
                        $q->where('district_slug', $district)
                            ->where(function ($q) use ($neighborhood) {
                                $q->whereNull('neighborhood_slug')
                                    ->orWhere('neighborhood_slug', $neighborhood);
                            });
                    });
            });
    }
}
