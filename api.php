<?php
/**
 * Moviq - REST API Gateway
 * Handles all AJAX queries from the frontend, caches results, and returns clean JSON.
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: public, max-age=300, s-maxage=3600, stale-while-revalidate=86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/config.php';

$action = isset($_GET['action']) ? trim($_GET['action']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? trim($_GET['type']) : 'movie';
if (!in_array($type, ['movie', 'tv', 'all', 'person'])) {
    $type = 'movie';
}

$response = [
    'success' => false,
    'data' => null,
    'error' => null
];

try {
    switch ($action) {
        case 'trending':
            $mediaType = in_array($type, ['all', 'movie', 'tv']) ? $type : 'all';
            $timeWindow = (isset($_GET['time']) && $_GET['time'] === 'week') ? 'week' : 'day';
            $data = tmdb_request("/trending/{$mediaType}/{$timeWindow}", ['page' => $page]);
            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            } else {
                $response['error'] = 'Failed to fetch trending titles.';
            }
            break;

        case 'hero_slides':
            // Fetch top trending movies with rich backdrops and details for the hero banner
            $trending = tmdb_request('/trending/movie/day', ['page' => 1]);
            $slides = [];
            if ($trending && !empty($trending['results'])) {
                // Filter items with backdrops, take top 6
                $count = 0;
                foreach ($trending['results'] as $item) {
                    if (!empty($item['backdrop_path']) && $count < 6) {
                        // Fetch videos for trailer key
                        $videos = tmdb_request("/movie/{$item['id']}/videos", [], 3600);
                        $trailerKey = null;
                        if ($videos && !empty($videos['results'])) {
                            foreach ($videos['results'] as $v) {
                                if ($v['site'] === 'YouTube' && ($v['type'] === 'Trailer' || $v['type'] === 'Teaser')) {
                                    $trailerKey = $v['key'];
                                    if ($v['type'] === 'Trailer' && (stripos($v['name'], 'Official') !== false || stripos($v['name'], 'Main') !== false)) {
                                        break;
                                    }
                                }
                            }
                        }
                        
                        $item['trailer_key'] = $trailerKey;
                        $item['backdrop_url'] = tmdb_image($item['backdrop_path'], 'original');
                        $item['poster_url'] = tmdb_image($item['poster_path'], 'w780');
                        $slides[] = $item;
                        $count++;
                    }
                }
                $response['success'] = true;
                $response['data'] = $slides;
            } else {
                $response['error'] = 'Hero slides unavailable.';
            }
            break;

        case 'popular_movies':
            $data = tmdb_request('/movie/popular', ['page' => $page]);
            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            }
            break;

        case 'top_rated_movies':
            $data = tmdb_request('/movie/top_rated', ['page' => $page]);
            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            }
            break;

        case 'now_playing':
            $data = tmdb_request('/movie/now_playing', ['page' => $page]);
            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            }
            break;

        case 'upcoming_movies':
            $data = tmdb_request('/movie/upcoming', ['page' => $page]);
            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            }
            break;

        case 'korean_movies':
            $data = tmdb_request('/discover/movie', [
                'page' => $page,
                'with_original_language' => 'ko',
                'sort_by' => 'popularity.desc',
                'include_adult' => 'false',
                'vote_count.gte' => 20
            ], 3600);
            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            }
            break;

        case 'popular_tv':
            $data = tmdb_request('/tv/popular', ['page' => $page]);
            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            }
            break;

        case 'top_rated_tv':
            $data = tmdb_request('/tv/top_rated', ['page' => $page]);
            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            }
            break;

        case 'on_the_air_tv':
            $data = tmdb_request('/tv/on_the_air', ['page' => $page]);
            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            }
            break;

        case 'genres':
            $movieGenres = tmdb_request('/genre/movie/list', [], 86400);
            $tvGenres = tmdb_request('/genre/tv/list', [], 86400);
            $response['success'] = true;
            $response['data'] = [
                'movie' => $movieGenres['genres'] ?? [],
                'tv' => $tvGenres['genres'] ?? []
            ];
            break;

        case 'discover':
            $media = ($type === 'tv') ? 'tv' : 'movie';
            $params = [
                'page' => $page,
                'include_adult' => 'false',
                'include_video' => 'false'
            ];

            if (!empty($_GET['with_genres'])) {
                $params['with_genres'] = trim($_GET['with_genres']);
            }
            if (!empty($_GET['with_original_language'])) {
                $params['with_original_language'] = trim($_GET['with_original_language']);
            }
            if (!empty($_GET['sort_by'])) {
                $params['sort_by'] = trim($_GET['sort_by']);
            } else {
                $params['sort_by'] = 'popularity.desc';
            }
            if (!empty($_GET['year'])) {
                if ($media === 'tv') {
                    $params['first_air_date_year'] = (int)$_GET['year'];
                } else {
                    $params['primary_release_year'] = (int)$_GET['year'];
                }
            }
            if (!empty($_GET['min_rating'])) {
                $params['vote_average.gte'] = (float)$_GET['min_rating'];
                $params['vote_count.gte'] = 50; // Filter out 1-vote anomalies
            }

            $data = tmdb_request("/discover/{$media}", $params);
            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            }
            break;

        case 'search':
            $query = isset($_GET['query']) ? trim($_GET['query']) : '';
            if (empty($query)) {
                $response['error'] = 'Search query is required.';
                break;
            }
            $searchType = isset($_GET['search_type']) ? trim($_GET['search_type']) : 'multi';
            if (!in_array($searchType, ['multi', 'movie', 'tv', 'person'])) {
                $searchType = 'multi';
            }

            $data = tmdb_request("/search/{$searchType}", [
                'query' => $query,
                'page' => $page,
                'include_adult' => 'false'
            ], 600); // 10 min search cache
            
            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            }
            break;

        case 'details':
            if ($id <= 0) {
                $response['error'] = 'Valid ID is required.';
                break;
            }
            $media = ($type === 'tv') ? 'tv' : 'movie';
            $data = tmdb_request("/{$media}/{$id}", [
                'append_to_response' => 'videos,credits,similar,recommendations,images,keywords,reviews,external_ids'
            ], 3600);

            if ($data) {
                // Find trailer
                $trailerKey = null;
                if (!empty($data['videos']['results'])) {
                    foreach ($data['videos']['results'] as $v) {
                        if ($v['site'] === 'YouTube' && in_array($v['type'], ['Trailer', 'Teaser', 'Clip'])) {
                            $trailerKey = $v['key'];
                            if ($v['type'] === 'Trailer') {
                                break;
                            }
                        }
                    }
                }
                $data['trailer_key'] = $trailerKey;
                $data['media_type'] = $media;
                $response['success'] = true;
                $response['data'] = $data;
            } else {
                $response['error'] = 'Title details not found.';
            }
            break;

        case 'person':
            if ($id <= 0) {
                $response['error'] = 'Valid Person ID is required.';
                break;
            }
            $data = tmdb_request("/person/{$id}", [
                'append_to_response' => 'combined_credits,images'
            ], 86400);

            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            }
            break;

        case 'tv_season':
            $seasonNumber = isset($_GET['season']) ? (int)$_GET['season'] : 1;
            if ($id <= 0) {
                $response['error'] = 'Valid TV ID is required.';
                break;
            }
            $data = tmdb_request("/tv/{$id}/season/{$seasonNumber}", [], 86400);
            if ($data) {
                $response['success'] = true;
                $response['data'] = $data;
            } else {
                $response['error'] = 'Season data not found.';
            }
            break;

        case 'stream_proxy':
            require_once __DIR__ . '/includes/stream_proxy.php';
            $targetUrl = isset($_GET['url']) ? trim($_GET['url']) : '';
            proxy_hls_stream($targetUrl);
            exit;

        case 'livetv_channels':
            require_once __DIR__ . '/includes/iptv.php';
            $category = isset($_GET['category']) ? trim($_GET['category']) : 'all';
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $channels = get_iptv_channels($category, $search);
            foreach ($channels as &$ch) {
                if (!isset($ch['description']) && isset($ch['current_show'])) {
                    $ch['description'] = $ch['current_show'];
                }
            }
            unset($ch);
            $response['success'] = true;
            $response['data'] = $channels;
            break;

        default:
            $response['error'] = 'Invalid action specified.';
            break;
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
