<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenMeteoWeatherService {

    private string $baseUrl = 'https://api.open-meteo.com/v1';

    /**
     * Get current weather data for given coordinates
     *
     * @param float $latitude Latitude coordinate (-90 to 90)
     * @param float $longitude Longitude coordinate (-180 to 180)
     * @param array $currentVariables Optional current weather variables
     * @param int $timezoneOffset Timezone offset in seconds (default 0)
     * @return array Raw API response or cached response
     * @throws Exception
     */
    public function getCurrentWeather(
        float $latitude,
        float $longitude,
        array $currentVariables = ['temperature_2m', 'relative_humidity_2m', 'precipitation', 'weather_code'],
        int   $timezoneOffset = 0
    ): array {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current' => implode(',', $currentVariables),
            'timezone' => 'auto',
            'forecast_days' => 1
        ];

        return $this->makeRequest('forecast', $params);
    }

    /**
     * Get hourly weather forecast for specified days
     *
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @param int $days Number of forecast days (1-16)
     * @param array $hourlyVariables Hourly variables to fetch
     * @return array Hourly forecast data
     * @throws Exception
     */
    public function getHourlyForecast(
        float $latitude,
        float $longitude,
        int   $days = 7,
        array $hourlyVariables = ['temperature_2m', 'relative_humidity_2m', 'precipitation', 'weather_code', 'wind_speed_10m']
    ): array {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'hourly' => implode(',', $hourlyVariables),
            'forecast_days' => min($days, 16),
            'timezone' => 'auto'
        ];

        return $this->makeRequest('forecast', $params);
    }

    /**
     * Get daily weather forecast
     *
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @param int $days Number of forecast days (1-16)
     * @param array $dailyVariables Daily variables to fetch
     * @return array Daily forecast data
     * @throws Exception
     */
    public function getDailyForecast(
        float $latitude,
        float $longitude,
        int   $days = 16,
        array $dailyVariables = [
            'temperature_2m_max', 'temperature_2m_min',
            'precipitation_sum', 'precipitation_probability_max',
            'weather_code', 'wind_speed_10m_max'
        ]
    ): array {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'daily' => implode(',', $dailyVariables),
            'forecast_days' => min($days, 16),
            'timezone' => 'auto'
        ];

        return $this->makeRequest('forecast', $params);
    }

    /**
     * Get historical weather data
     *
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @param string $start Start date (YYYY-MM-DD)
     * @param string|null $end End date (YYYY-MM-DD), defaults to start date
     * @param array $hourlyVariables Hourly historical variables
     * @param array $dailyVariables Daily historical variables
     * @return array Historical weather data
     * @throws Exception
     */
    public function getHistoricalWeather(
        float   $latitude,
        float   $longitude,
        string  $start,
        ?string $end = null,
        array   $hourlyVariables = [],
        array   $dailyVariables = []
    ): array {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'start_date' => $start,
            'end_date' => $end ?? $start,
            'timezone' => 'auto'
        ];

        if (!empty($hourlyVariables)) {
            $params['hourly'] = implode(',', $hourlyVariables);
        }

        if (!empty($dailyVariables)) {
            $params['daily'] = implode(',', $dailyVariables);
        }

        return $this->makeRequest('archive', $params);
    }

    /**
     * Get weather alerts for location
     *
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @return array Weather alerts data
     * @throws Exception
     */
    public function getWeatherAlerts(float $latitude, float $longitude): array {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude
        ];

        return $this->makeRequest('alerts', $params);
    }

    /**
     * Search for location by name (Geocoding)
     *
     * @param string $query Location name (city, country, etc.)
     * @param int $count Number of results (default 1)
     * @return array Location search results
     * @throws Exception
     */
    public function searchLocation(string $query, int $count = 1): array {
        $params = [
            'name' => $query,
            'count' => $count,
            'language' => 'en',
            'format' => 'json'
        ];

        return $this->makeRequest('geocoding', $params, 'search');
    }

    /**
     * Get elevation data for coordinates
     *
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @return array Elevation data
     * @throws Exception
     */
    public function getElevation(float $latitude, float $longitude): array {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude
        ];

        return $this->makeRequest('elevation', $params);
    }

    /**
     * Parse WMO Weather Code to human readable description
     *
     * @param int $weatherCode WMO Weather Code
     * @return string Weather description
     */
    public function parseWeatherCode(int $weatherCode): string {
        $codes = [
            0 => 'Clear sky',
            1 => 'Mainly clear',
            2 => 'Partly cloudy',
            3 => 'Overcast',
            45 => 'Fog',
            48 => 'Depositing rime fog',
            51 => 'Light drizzle',
            53 => 'Moderate drizzle',
            55 => 'Dense drizzle',
            61 => 'Slight rain',
            63 => 'Moderate rain',
            65 => 'Heavy rain',
            71 => 'Slight snow fall',
            73 => 'Moderate snow fall',
            75 => 'Heavy snow fall',
            80 => 'Slight rain showers',
            81 => 'Moderate rain showers',
            82 => 'Violent rain showers',
            95 => 'Thunderstorm',
            96 => 'Thunderstorm with slight hail',
            99 => 'Thunderstorm with heavy hail'
        ];

        return $codes[$weatherCode] ?? 'Unknown';
    }

    /**
     * Make HTTP request to Open-Meteo API with caching
     *
     * @param string $endpoint API endpoint
     * @param array $params Query parameters
     * @param string|null $endpointPath Custom endpoint path
     * @return array API response
     * @throws Exception
     */
    private function makeRequest(string $endpoint, array $params, ?string $endpointPath = null): array {
        $cacheKey = 'openmeteo_' . md5($endpoint . serialize($params));
        $cacheTTL = now()->addMinutes(15); // 15 minutes cache

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {

            $finalEndpoint = $endpointPath ?? $endpoint;

            $response = Http::timeout(30)
                ->get("{$this->baseUrl}/{$finalEndpoint}", $params);

            if ($response->successful()) {
                $data = $response->json();
                Cache::put($cacheKey, $data, $cacheTTL);
                return $data;
            }

            Log::error('Open-Meteo API Error', [
                'status' => $response->status(),
                'response' => $response->body(),
                'params' => $params
            ]);

            throw new Exception("API request failed: " . $response->status());

        } catch (Exception $e) {
            Log::error('Open-Meteo Service Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
