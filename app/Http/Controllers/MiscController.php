<?php

namespace App\Http\Controllers;

use App\Services\OpenMeteoWeatherService;

class MiscController extends Controller {

    public function index(int $number): ?string {
        return str_repeat('hello world', $number);
    }

    public function getWeather() {

        // In Controller
        $weatherService = new OpenMeteoWeatherService();

        // Current weather for Lahore (31.5204, 74.3587)
        $current = $weatherService->getCurrentWeather(31.5204, 74.3587);

        // 7-day forecast
        $forecast = $weatherService->getDailyForecast(31.5204, 74.3587, 7);

        // Historical data
        $historical = $weatherService->getHistoricalWeather(31.5204, 74.3587, '2025-01-01', '2025-01-07');

        // Location search
        $locations = $weatherService->searchLocation('Lahore, Pakistan');

        // Parse weather code
        $description = $weatherService->parseWeatherCode(61); // "Slight rain"

        return response()->json([
            'current' => $current,
            'forecast' => $forecast,
            'historical' => $historical,
            'locations' => $locations,
            'description' => $description
        ]);

    }

}
