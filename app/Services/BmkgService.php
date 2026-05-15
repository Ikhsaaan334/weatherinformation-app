<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BmkgService
{
    protected $baseUrl = 'https://api.bmkg.go.id/publik/prakiraan-cuaca';

    /**
     * Get weather data for a specific adm4 code.
     */
    public function getWeather($adm4)
    {
        $cacheKey = "bmkg_weather_{$adm4}";

        return Cache::remember($cacheKey, 1800, function () use ($adm4) {
            $response = Http::get($this->baseUrl, [
                'adm4' => $adm4
            ]);

            if ($response->successful()) {
                return $this->transformData($response->json());
            }

            return null;
        });
    }

    /**
     * Transform BMKG JSON response to application-friendly format.
     */
    public function transformData($json)
    {
        if (!isset($json['data']) || empty($json['data'])) {
            return null;
        }

        $lokasi = $json['lokasi'] ?? [];
        // Extract the first item of the data array as requested
        $firstData = $json['data'][0] ?? [];
        
        // Find the current or most relevant weather point
        // BMKG usually returns an array of forecast points. 
        // We'll take the first one available in the 'cuaca' array of the first data group.
        $cuaca = $firstData['cuaca'][0] ?? [];

        return [
            'wilayah' => $lokasi['desa'] ?? 'Unknown',
            'kecamatan' => $lokasi['kecamatan'] ?? '',
            'kota' => $lokasi['kota'] ?? '',
            'temp' => $cuaca['t'] ?? 0,
            'humidity' => $cuaca['hu'] ?? 0,
            'wind_speed' => $cuaca['ws'] ?? 0,
            'description' => $cuaca['weather_desc'] ?? 'No Data',
            'image' => $cuaca['image'] ?? null,
            'datetime' => $cuaca['datetime'] ?? null,
        ];
    }
}
