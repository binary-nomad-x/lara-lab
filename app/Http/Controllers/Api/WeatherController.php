<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OpenMeteoWeatherService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WeatherController extends Controller {

    /**
     * @throws Exception
     */
    public function getWeather(Request $request) {
        // Coordinates for Lahore, Pakistan
        $lat = $request->input('lat', 31.5204);
        $lon = $request->input('lon', 74.3587);

        // Initialize Service
        $weatherService = new OpenMeteoWeatherService();

        try {
            // 1. Current Weather & Basic Forecast
            $currentWeather = $weatherService->getCurrentWeather($lat, $lon);
            $dailyForecast = $weatherService->getDailyForecast($lat, $lon, 7);

            // 2. New: Air Quality Data
            $airQuality = $weatherService->getAirQuality($lat, $lon);

            // 3. New: Astronomy Data (Sun/Moon for next 3 days)
            $today = now()->format('Y-m-d');
            $threeDaysLater = now()->addDays(3)->format('Y-m-d');
            $astronomy = $weatherService->getAstronomy($lat, $lon, $today, $threeDaysLater);

            // 4. New: Timezone Detection
            $timezoneInfo = $weatherService->getTimezone($lat, $lon);

            // 5. New: Reverse Geocoding (Confirming location name from coords)
            $locationDetails = $weatherService->reverseGeocode($lat, $lon);

            // 6. New: Elevation Data
            $elevation = $weatherService->getElevation($lat, $lon);

            // 7. New: Weather Alerts (Check for warnings)
            $alerts = $weatherService->getWeatherAlerts($lat, $lon);

            // Existing: Historical & Search
            $historical = $weatherService->getHistoricalWeather($lat, $lon, '2025-01-01', '2025-01-07');
            $searchResults = $weatherService->searchLocation('Lahore, Pakistan');

            // Helper: Parse a specific code (Example: 61)
            $exampleDescription = $weatherService->parseWeatherCode(61);

            // Helper: Parse the *actual* current weather code from the API response
            $currentCode = $currentWeather['current']['weather_code'] ?? 0;
            $currentCondition = $weatherService->parseWeatherCode($currentCode);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'location' => [
                        'query_coordinates' => ['lat' => $lat, 'lon' => $lon],
                        'resolved_name' => $locationDetails['results'][0]['name'] ?? 'Unknown',
                        'country' => $locationDetails['results'][0]['country'] ?? 'Unknown',
                        'admin_area' => $locationDetails['results'][0]['admin1'] ?? null,
                        'elevation_meters' => $elevation['elevation'] ?? null,
                        'timezone' => $timezoneInfo,
                    ],
                    'current' => [
                        'raw' => $currentWeather['current'] ?? [],
                        'human_readable_condition' => $currentCondition,
                        'temperature' => $currentWeather['current']['temperature_2m'] ?? null,
                        'humidity' => $currentWeather['current']['relative_humidity_2m'] ?? null,
                    ],
                    'air_quality' => [
                        'pm2_5' => $airQuality['current']['pm2_5'] ?? null,
                        'pm10' => $airQuality['current']['pm10'] ?? null,
                        'ozone' => $airQuality['current']['ozone'] ?? null,
                        'us_epa_index' => $airQuality['current']['us_epa_index'] ?? null, // If available
                    ],
                    'forecast' => [
                        'daily' => $dailyForecast['daily'] ?? [],
                    ],
                    'astronomy' => $astronomy['daily'] ?? [],
                    'alerts' => $alerts['alerts'] ?? [], // Will be empty array if no alerts
                    'historical_sample' => [
                        'date_range' => '2025-01-01 to 2025-01-07',
                        'data' => $historical['daily'] ?? $historical['hourly'] ?? []
                    ],
                    'search_demo' => $searchResults['results'] ?? [],
                    'helpers' => [
                        'parsed_code_61' => $exampleDescription,
                    ]
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
