<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Services\WeatherService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CityController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = City::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('country', 'like', '%' . $request->search . '%');
        }

        $cities = $query->paginate(10)->withQueryString();

        return Inertia::render('Cities/Index', [
            'cities' => $cities,
            'filters' => $request->only(['search']),
            'can' => [
                'manage_cities' => request()->user() ? request()->user()->can('manage cities') : false,
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        $this->authorize('manage cities');
        City::create($request->validated());
        return redirect()->back()->with('message', 'City created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, City $city)
    {
        $this->authorize('manage cities');
        $city->update($request->validated());
        return redirect()->back()->with('message', 'City updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $this->authorize('manage cities');
        $city->delete();
        return redirect()->back()->with('message', 'City deleted successfully.');
    }

    /**
     * Display weather for the city.
     */
    public function weather(City $city)
    {
        $weatherData = $this->weatherService->getWeather($city->lat, $city->lon);

        return Inertia::render('Cities/Weather', [
            'city' => $city,
            'weather' => $weatherData
        ]);
    }
}
