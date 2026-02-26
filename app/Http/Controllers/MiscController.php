<?php

namespace App\Http\Controllers;

use App\Services\OpenMeteoWeatherService;

class MiscController extends Controller {

    public function index(int $number): ?string {
        return str_repeat('hello world', $number);
    }

    /**
     * @throws \Exception
     */
    public function getWeather() {

        // In Controller
        $weatherService = new OpenMeteoWeatherService();

        return response()->json([
            'current' => $weatherService->getCurrentWeather(31.5204, 74.3587),  // Current weather for Lahore (31.5204, 74.3587)
            'forecast' => $weatherService->getDailyForecast(31.5204, 74.3587, 7),  // 7-day forecast
            'historical' => $weatherService->getHistoricalWeather(31.5204, 74.3587, '2025-01-01', '2025-01-07'), // Historical data
            'locations' => $weatherService->searchLocation('Lahore, Pakistan'),  // Location search
            'description' => $weatherService->parseWeatherCode(61), // Parse weather code "slight rain"
        ]);

    }

}
