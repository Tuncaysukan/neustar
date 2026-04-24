<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * DSMART (telkotürk) adres drill-down servisi.
 *
 * Tüm çağrılar POST + JSON body olarak gidiyor.
 * Endpoint: https://basvuru.dsmart.com.tr/telkoturk-backend/altyapi-sorgulama.php?api=1
 *
 * Op tipleri:
 *   - cities
 *   - districts     + ilKod
 *   - neighborhoods + ilceKod
 *   - streets       + mahalleKod
 *   - buildings     + csbmKod
 *   - doors         + binaKodu
 *
 * Cache: 30 gün (adres verileri nadir değişir).
 */
class DsmartAddressService
{
    private const BASE = 'https://basvuru.dsmart.com.tr/telkoturk-backend/altyapi-sorgulama.php?api=1';

    /**
     * @return array<int, array{id:string,name:string}>
     */
    public function cities(): array
    {
        return Cache::remember('dsmart.cities.v1', now()->addDays(30), function () {
            return $this->call('cities');
        });
    }

    /**
     * @return array<int, array{id:string,name:string}>
     */
    public function districts(string $ilKod): array
    {
        return Cache::remember("dsmart.districts.{$ilKod}.v1", now()->addDays(30), function () use ($ilKod) {
            return $this->call('districts', ['ilKod' => $ilKod]);
        });
    }

    /**
     * @return array<int, array{id:string,name:string}>
     */
    public function neighborhoods(string $ilceKod): array
    {
        return Cache::remember("dsmart.neighborhoods.{$ilceKod}.v1", now()->addDays(30), function () use ($ilceKod) {
            return $this->call('neighborhoods', ['ilceKod' => $ilceKod]);
        });
    }

    /**
     * @return array<int, array{id:string,name:string}>
     */
    public function streets(string $mahalleKod): array
    {
        return Cache::remember("dsmart.streets.{$mahalleKod}.v1", now()->addDays(30), function () use ($mahalleKod) {
            return $this->call('streets', ['mahalleKod' => $mahalleKod]);
        });
    }

    /**
     * @return array<int, array{id:string,name:string,bbkCode:string}>
     */
    public function buildings(string $csbmKod): array
    {
        return Cache::remember("dsmart.buildings.{$csbmKod}.v1", now()->addDays(30), function () use ($csbmKod) {
            return $this->call('buildings', ['csbmKod' => $csbmKod]);
        });
    }

    /**
     * @return array<int, array{id:string,name:string,bbkCode:string}>
     */
    public function doors(string $binaKodu): array
    {
        return Cache::remember("dsmart.doors.{$binaKodu}.v1", now()->addDays(30), function () use ($binaKodu) {
            return $this->call('doors', ['binaKodu' => $binaKodu]);
        });
    }

    // =========================================================================
    //  Slug helpers — kullanıcının URL slug'ından DSMART ID'sine çevirir
    // =========================================================================

    public function findCityIdBySlug(string $slug): ?string
    {
        $slug = Str::slug($slug);
        foreach ($this->cities() as $row) {
            if (Str::slug($row['name'] ?? '') === $slug) {
                return (string) $row['id'];
            }
        }
        return null;
    }

    public function findDistrictIdBySlug(string $ilKod, string $slug): ?string
    {
        $slug = Str::slug($slug);
        foreach ($this->districts($ilKod) as $row) {
            if (Str::slug($row['name'] ?? '') === $slug) {
                return (string) $row['id'];
            }
        }
        return null;
    }

    // =========================================================================
    //  Low-level HTTP
    // =========================================================================

    /**
     * @return array<int, array<string, mixed>>
     */
    private function call(string $op, array $params = []): array
    {
        try {
            $res = $this->http()
                ->withBody(
                    json_encode(array_merge(['op' => $op], $params), JSON_UNESCAPED_UNICODE),
                    'application/json',
                )
                ->post(self::BASE);

            if (!$res->successful()) {
                Log::warning('dsmart.http.fail', [
                    'op' => $op, 'params' => $params, 'status' => $res->status(),
                ]);
                return [];
            }

            $json = $res->json();
            if (!is_array($json) || ($json['ok'] ?? false) !== true) {
                Log::warning('dsmart.logical.fail', [
                    'op' => $op, 'params' => $params, 'body' => $json,
                ]);
                return [];
            }

            $items = $json['items'] ?? [];
            return is_array($items) ? array_values($items) : [];
        } catch (\Throwable $e) {
            Log::warning('dsmart.exception', [
                'op' => $op, 'params' => $params, 'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    private function http(): PendingRequest
    {
        return Http::timeout(12)
            ->retry(2, 250)
            ->withHeaders([
                'Accept'           => 'application/json',
                'Content-Type'     => 'application/json',
                'Referer'          => 'https://www.telkoturk.net/',
                'Origin'           => 'https://www.telkoturk.net',
                'User-Agent'       => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
    }
}
