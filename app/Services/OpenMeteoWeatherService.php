<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenMeteoWeatherService {

    // Fixed: Removed trailing spaces from URL
    private string $baseUrl = 'https://api.open-meteo.com/v1';

    // New Base URL for Air Quality API (separate endpoint in Open-Meteo ecosystem)
    private string $airQualityBaseUrl = 'https://air-quality-api.open-meteo.com/v1';

    /**
     * Get current weather data for given coordinates
     *
     * @param float $latitude Latitude coordinate (-90 to 90)
     * @param float $longitude Longitude coordinate (-180 to 180)
     * @param array $currentVariables Optional current weather variables
     * @return array Raw API response or cached response
     * @throws Exception
     */
    public function getCurrentWeather(
        float $latitude,
        float $longitude,
        array $currentVariables = ['temperature_2m', 'relative_humidity_2m', 'precipitation', 'weather_code', 'wind_speed_10m', 'wind_direction_10m'],
        int   $timezoneOffset = 0
    ): array {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current' => implode(',', $currentVariables),
            'timezone' => 'auto',
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
        array $hourlyVariables = ['temperature_2m', 'relative_humidity_2m', 'precipitation', 'weather_code', 'wind_speed_10m', 'visibility']
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
            'weather_code', 'wind_speed_10m_max',
            'sunrise', 'sunset', 'uv_index_max'
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
     * @param string|null $countryCode Optional ISO 3166-1 alpha-2 country code filter
     * @return array Location search results
     * @throws Exception
     */
    public function searchLocation(string $query, int $count = 1, ?string $countryCode = null): array {
        $params = [
            'name' => $query,
            'count' => $count,
            'language' => 'en',
            'format' => 'json'
        ];

        if ($countryCode) {
            $params['countrycode'] = $countryCode;
        }

        return $this->makeRequest('geocoding', $params, 'search');
    }

    /**
     * Reverse geocode coordinates to get location details
     *
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @return array Location details (name, country, admin areas)
     * @throws Exception
     */
    public function reverseGeocode(float $latitude, float $longitude): array {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        // Open-Meteo geocoding API supports reverse lookup via the same endpoint structure usually,
        // but strictly speaking, their public docs highlight the 'search' endpoint.
        // However, we can use the generic geocoding endpoint with lat/lon if supported or fallback to logic.
        // Actually, Open-Meteo Geocoding API allows reverse geocoding by passing lat/lon without 'name'.
        unset($params['name']);

        return $this->makeRequest('geocoding', $params, 'reverse');
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
     * Get current Air Quality data (PM2.5, PM10, O3, NO2, etc.)
     * Requires separate API endpoint
     *
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @param array $variables Air quality variables (default: all major pollutants)
     * @return array Air quality data
     * @throws Exception
     */
    public function getAirQuality(
        float $latitude,
        float $longitude,
        array $variables = [
            'pm10', 'pm2_5', 'carbon_monoxide', 'nitrogen_dioxide',
            'sulphur_dioxide', 'ozone', 'aerosol_optical_depth',
            'dust', 'uv_index'
        ]
    ): array {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current' => implode(',', $variables),
            'timezone' => 'auto'
        ];

        // Air Quality uses a different base URL
        return $this->makeRequest('air-quality', $params, null, true);
    }

    /**
     * Get Forecast Office responsible for a specific location (US/NWS focused mostly)
     *
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @return array Forecast office metadata
     * @throws Exception
     */
    public function getForecastOffice(float $latitude, float $longitude): array {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'additional_info' => 'true'
        ];

        return $this->makeRequest('forecast', $params, 'forecastoffice');
    }

    /**
     * Get Astronomical Data (Sun, Moon phases, rise/set times) for a date range
     *
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @return array Astronomical data
     * @throws Exception
     */
    public function getAstronomy(
        float  $latitude,
        float  $longitude,
        string $startDate,
        string $endDate
    ): array {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'daily' => 'sunrise,sunset,moonrise,moonset,moon_phase,moon_phase_index',
            'timezone' => 'auto'
        ];

        return $this->makeRequest('forecast', $params);
    }

    /**
     * Detect Timezone for coordinates
     *
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @return array Timezone information
     * @throws Exception
     */
    public function getTimezone(float $latitude, float $longitude): array {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current' => 'time' // Minimal payload to trigger timezone resolution
        ];

        $response = $this->makeRequest('forecast', $params);

        // Extract only timezone info to keep return clean
        return [
            'timezone' => $response['timezone'] ?? null,
            'timezone_abbreviation' => $response['timezone_abbreviation'] ?? null,
            'gmt_offset' => $response['gmt_offset_seconds'] ?? null,
            'current_time' => $response['current']['time'] ?? null
        ];
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
            56 => 'Light freezing drizzle',
            57 => 'Dense freezing drizzle',
            61 => 'Slight rain',
            63 => 'Moderate rain',
            65 => 'Heavy rain',
            66 => 'Light freezing rain',
            67 => 'Heavy freezing rain',
            71 => 'Slight snow fall',
            73 => 'Moderate snow fall',
            75 => 'Heavy snow fall',
            77 => 'Snow grains',
            80 => 'Slight rain showers',
            81 => 'Moderate rain showers',
            82 => 'Violent rain showers',
            85 => 'Slight snow showers',
            86 => 'Heavy snow showers',
            95 => 'Thunderstorm',
            96 => 'Thunderstorm with slight hail',
            99 => 'Thunderstorm with heavy hail'
        ];

        return $codes[$weatherCode] ?? 'Unknown';
    }

    /**
     * Make HTTP request to Open-Meteo API with caching
     *
     * @param string $endpoint API endpoint path segment
     * @param array $params Query parameters
     * @param string|null $endpointPath Custom endpoint path override
     * @param bool $isAirQuality Use the Air Quality base URL instead of standard
     * @return array API response
     * @throws Exception
     */
    private function makeRequest(string $endpoint, array $params, ?string $endpointPath = null, bool $isAirQuality = false): array {
        // Create a unique cache key based on endpoint, params, and API type
        $cachePrefix = $isAirQuality ? 'openmeteo_aq_' : 'openmeteo_';
        $cacheKey = $cachePrefix . md5($endpoint . serialize($params));

        // Dynamic TTL: Air quality changes faster, maybe 10 mins. Weather 15 mins.
        $cacheTTL = $isAirQuality ? now()->addMinutes(10) : now()->addMinutes(15);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $base = $isAirQuality ? $this->airQualityBaseUrl : $this->baseUrl;
            $finalEndpoint = $endpointPath ?? $endpoint;

            $response = Http::timeout(30)
                ->userAgent('Laravel-App/1.0') // Good practice to identify app
                ->get("{$base}/{$finalEndpoint}", $params);

            if ($response->successful()) {
                $data = $response->json();

                // Handle specific error format from Open-Meteo (sometimes returns 200 with error key)
                if (isset($data['error'])) {
                    throw new Exception("Open-Meteo API Error: " . ($data['reason'] ?? $data['error']));
                }

                Cache::put($cacheKey, $data, $cacheTTL);
                return $data;
            }

            Log::error('Open-Meteo API Error', [
                'status' => $response->status(),
                'response' => $response->body(),
                'params' => $params,
                'url' => "{$base}/{$finalEndpoint}"
            ]);

            throw new Exception("API request failed: " . $response->status());

        } catch (Exception $e) {
            // Don't double log if it's already logged above, but ensure propagation
            if (strpos($e->getMessage(), 'API request failed') === false) {
                Log::error('Open-Meteo Service Error: ' . $e->getMessage());
            }
            throw $e;
        }
    }
}