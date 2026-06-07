<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function __construct(protected WeatherService $weatherService)
    {
    }

    /**
     * Paginated, searchable list of cities.
     */
    public function index(Request $request): JsonResponse
    {
        $query = City::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('country', 'like', '%'.$search.'%');
            });
        }

        $cities = $query->orderBy('name')->paginate(10)->withQueryString();

        return response()->json([
            'cities' => $cities,
            'filters' => $request->only(['search']),
            'can' => [
                'manage_cities' => $request->user()?->can('manage cities') ?? false,
            ],
        ]);
    }

    /**
     * Create a city (admin only — enforced by StoreCityRequest + route middleware).
     */
    public function store(StoreCityRequest $request): JsonResponse
    {
        $city = City::create($request->validated());

        return response()->json([
            'message' => 'City created successfully.',
            'city' => $city,
        ], 201);
    }

    /**
     * Update a city (admin only).
     */
    public function update(UpdateCityRequest $request, City $city): JsonResponse
    {
        $city->update($request->validated());

        return response()->json([
            'message' => 'City updated successfully.',
            'city' => $city,
        ]);
    }

    /**
     * Delete a city (admin only).
     */
    public function destroy(City $city): JsonResponse
    {
        $city->delete();

        return response()->json(['message' => 'City deleted successfully.']);
    }

    /**
     * Current weather for a city.
     */
    public function weather(City $city): JsonResponse
    {
        $weatherData = $this->weatherService->getWeather($city->lat, $city->lon);

        return response()->json([
            'city' => $city,
            'weather' => $weatherData,
        ]);
    }
}
