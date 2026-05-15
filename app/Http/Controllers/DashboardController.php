<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\WeatherService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function index()
    {
        $cityOrder = ['Jakarta' => 0, 'Bandung' => 1, 'Surabaya' => 2];

        $cities = City::query()
            ->get()
            ->filter(fn (City $city) => array_key_exists($city->name, $cityOrder))
            ->sortBy(fn (City $city) => $cityOrder[$city->name] ?? PHP_INT_MAX)
            ->values();

        $weatherData = $cities->map(function (City $city) {
            $weather = $this->weatherService->getWeather($city->lat, $city->lon);

            if (! $weather) {
                return null;
            }

            $current = $weather['current_weather'] ?? [];

            return [
                'wilayah' => $city->name,
                'kecamatan' => '',
                'kota' => $city->country,
                'temp' => $current['temperature'] ?? null,
                'humidity' => $weather['hourly']['relative_humidity_2m'][0] ?? null,
                'wind_speed' => $current['windspeed'] ?? null,
                'description' => $this->describeWeatherCode($current['weathercode'] ?? null),
                'image' => null,
                'datetime' => $current['time'] ?? now()->toIso8601String(),
            ];
        })->filter()->values();

        return Inertia::render('Dashboard', [
            'weatherData' => $weatherData
        ]);
    }

    protected function describeWeatherCode(?int $code): string
    {
        $weatherCodes = [
            0 => 'Clear sky',
            1 => 'Mainly clear',
            2 => 'Partly cloudy',
            3 => 'Overcast',
            45 => 'Fog',
            48 => 'Depositing rime fog',
            51 => 'Drizzle: Light',
            61 => 'Rain: Slight',
            71 => 'Snow fall: Slight',
            95 => 'Thunderstorm',
        ];

        return $weatherCodes[$code] ?? 'Unknown';
    }
}
