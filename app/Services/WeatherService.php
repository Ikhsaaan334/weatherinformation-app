<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected $baseUrl = 'https://api.open-meteo.com/v1/forecast';

    public function getWeather($lat, $lon)
    {
        $cacheKey = "weather_{$lat}_{$lon}";

        return Cache::remember($cacheKey, 600, function () use ($lat, $lon) {
            $response = Http::get($this->baseUrl, [
                'latitude' => $lat,
                'longitude' => $lon,
                'current_weather' => true,
                'hourly' => 'temperature_2m,relative_humidity_2m,wind_speed_10m',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        });
    }
}
