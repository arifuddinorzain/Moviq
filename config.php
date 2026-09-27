<?php
/**
 * Moviq - Movie & TV Hub Configuration
 * TMDB API integration, caching, and helper utilities.
 */

define('TMDB_API_KEY', '3f1aba31128a4d95a87d771553cd672b');
define('TMDB_BASE_URL', 'https://api.themoviedb.org/3');
define('TMDB_IMG_BASE', 'https://image.tmdb.org/t/p');
// Cache directory configuration (supports serverless read-only filesystems like Vercel)
if (!defined('CACHE_DIR')) {
    $cacheDir = (getenv('VERCEL') || !is_writable(__DIR__)) ? sys_get_temp_dir() . '/moviq_cache' : __DIR__ . '/cache';
    define('CACHE_DIR', $cacheDir);
}
define('CACHE_DEFAULT_EXPIRY', 1800); // 30 minutes in seconds

// Ensure cache directory exists
if (!is_dir(CACHE_DIR)) {
    @mkdir(CACHE_DIR, 0755, true);
}

/**
 * Fetch data from TMDB with automatic file caching and cURL/file_get_contents fallback.
 *
 * @param string $endpoint e.g. '/movie/popular' or '/trending/all/day'
 * @param array $params Query parameters
 * @param int $cache_expiry Cache expiration in seconds
 * @return array|null Parsed JSON data or null on failure
 */
function tmdb_request($endpoint, $params = [], $cache_expiry = CACHE_DEFAULT_EXPIRY) {
    $params['api_key'] = TMDB_API_KEY;
    if (!isset($params['language'])) {
        $params['language'] = 'en-US';
    }

    $queryString = http_build_query($params);
    $url = TMDB_BASE_URL . (str_starts_with($endpoint, '/') ? $endpoint : '/' . $endpoint) . '?' . $queryString;
    
    // Generate cache filename from normalized URL
    $cacheKey = md5($url);
    $cacheFile = CACHE_DIR . '/' . $cacheKey . '.json';

    // Check cache
    if ($cache_expiry > 0 && file_exists($cacheFile)) {
        $fileAge = time() - filemtime($cacheFile);
        if ($fileAge < $cache_expiry) {
            $cachedContent = @file_get_contents($cacheFile);
            if ($cachedContent) {
                $decoded = json_decode($cachedContent, true);
                if ($decoded !== null) {
                    return $decoded;
                }
            }
        }
    }

    // Fetch from TMDB
    $response = null;
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Moviq-Cinematic-Hub/1.0');
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $response = null;
        }
    }

    if (!$response) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'Moviq-Cinematic-Hub/1.0',
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);
        $response = @file_get_contents($url, false, $context);
    }

    if (!$response) {
        // Fallback to expired cache if available during network error
        if (file_exists($cacheFile)) {
            $cachedContent = @file_get_contents($cacheFile);
            if ($cachedContent) {
                return json_decode($cachedContent, true);
            }
        }
        return null;
    }

    $decoded = json_decode($response, true);
    if ($decoded !== null && !isset($decoded['status_code'])) {
        // Save to cache
        @file_put_contents($cacheFile, $response);
    }

    return $decoded;
}

/**
 * Helper to build high-res or responsive image URLs
 */
function tmdb_image($path, $size = 'w500') {
    if (empty($path)) {
        return null;
    }
    return TMDB_IMG_BASE . '/' . $size . $path;
}

/**
 * Format vote average to single decimal
 */
function format_rating($rating) {
    return number_format((float)$rating, 1, '.', '');
}

/**
 * Format minutes to hours & minutes (e.g. 142 -> 2h 22m)
 */
function format_runtime($minutes) {
    if (!$minutes || $minutes <= 0) return 'N/A';
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    if ($hours > 0 && $mins > 0) {
        return "{$hours}h {$mins}m";
    } elseif ($hours > 0) {
        return "{$hours}h";
    }
    return "{$mins}m";
}

/**
 * Format large currency numbers
 */
function format_currency($amount) {
    if (!$amount || $amount <= 0) return 'N/A';
    return '$' . number_format($amount);
}
