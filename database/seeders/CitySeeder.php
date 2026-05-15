<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['name' => 'Jakarta', 'country' => 'ID', 'lat' => -6.2088, 'lon' => 106.8456],
            ['name' => 'Surabaya', 'country' => 'ID', 'lat' => -7.2575, 'lon' => 112.7521],
            ['name' => 'Bandung', 'country' => 'ID', 'lat' => -6.9175, 'lon' => 107.6191],
            ['name' => 'Medan', 'country' => 'ID', 'lat' => 3.5952, 'lon' => 98.6722],
            ['name' => 'Semarang', 'country' => 'ID', 'lat' => -6.9667, 'lon' => 110.4167],
            ['name' => 'Tokyo', 'country' => 'JP', 'lat' => 35.6895, 'lon' => 139.6917],
            ['name' => 'London', 'country' => 'GB', 'lat' => 51.5074, 'lon' => -0.1278],
            ['name' => 'New York', 'country' => 'US', 'lat' => 40.7128, 'lon' => -74.0060],
            ['name' => 'Paris', 'country' => 'FR', 'lat' => 48.8566, 'lon' => 2.3522],
            ['name' => 'Sydney', 'country' => 'AU', 'lat' => -33.8688, 'lon' => 151.2093],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(['name' => $city['name'], 'country' => $city['country']], $city);
        }
    }
}
