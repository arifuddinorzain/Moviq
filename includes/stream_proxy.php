<?php
/**
 * Moviq — High-Performance Live HLS Stream Proxy
 * Eliminates browser CORS blocks, Mixed-Content errors, and User-Agent filtering
 */

function proxy_hls_stream($url) {
    $url = trim($url);
    if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        header('Content-Type: text/plain');
        echo 'Invalid stream URL.';
        exit;
    }

    // Set CORS headers for all browser requests
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, Range, Content-Type, Accept, X-Requested-With');
    header('Access-Control-Expose-Headers: Content-Length, Content-Range');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    // Pass Range header if present
    if (isset($_SERVER['HTTP_RANGE'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Range: ' . $_SERVER['HTTP_RANGE']]);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL) ?: $url;
    curl_close($ch);

    if ($httpCode >= 400 || $response === false) {
        http_response_code($httpCode >= 400 ? $httpCode : 502);
        header('Content-Type: text/plain');
        echo 'Stream source unreachable (HTTP ' . $httpCode . ').';
        exit;
    }

    // Determine base URL and current directory URL with forward slashes (cross-platform safe)
    $parsed = parse_url($effectiveUrl);
    $scheme = $parsed['scheme'] ?? 'http';
    $host = $parsed['host'] ?? '';
    $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
    $baseUrl = $scheme . '://' . $host . $port;
    
    $path = $parsed['path'] ?? '/';
    $lastSlash = strrpos($path, '/');
    $pathDir = ($lastSlash !== false) ? substr($path, 0, $lastSlash) : '';
    $currentDirUrl = rtrim($baseUrl . $pathDir, '/') . '/';

    // Check if this is an M3U8 playlist
    $isM3u8 = (strpos($response, '#EXTM3U') !== false || stripos($contentType, 'mpegurl') !== false || stripos($url, '.m3u8') !== false);

    if ($isM3u8) {
        header('Content-Type: application/vnd.apple.mpegurl');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $lines = explode("\n", $response);
        $rewrittenLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                $rewrittenLines[] = $line;
                continue;
            }

            // Handle M3U8 comment / tag lines
            if ($trimmed[0] === '#') {
                // Check for URI attributes (e.g., #EXT-X-KEY:METHOD=...,URI="..." or #EXT-X-MAP:URI="...")
                if (preg_match('/URI="([^"]+)"/', $trimmed, $matches)) {
                    $origUri = $matches[1];
                    $resolvedUri = resolve_relative_stream_url($origUri, $currentDirUrl, $baseUrl);
                    $proxiedUri = 'api.php?action=stream_proxy&url=' . urlencode($resolvedUri);
                    $trimmed = str_replace('URI="' . $origUri . '"', 'URI="' . $proxiedUri . '"', $trimmed);
                }
                $rewrittenLines[] = $trimmed;
            } else {
                // Segment URL or child playlist URL
                $resolvedUri = resolve_relative_stream_url($trimmed, $currentDirUrl, $baseUrl);
                $proxiedUri = 'api.php?action=stream_proxy&url=' . urlencode($resolvedUri);
                $rewrittenLines[] = $proxiedUri;
            }
        }

        echo implode("\n", $rewrittenLines);
        exit;
    } else {
        // Binary media chunk (.ts, .m4s, .aac, etc.)
        if (!empty($contentType)) {
            header('Content-Type: ' . $contentType);
        } else {
            header('Content-Type: video/MP2T');
        }
        header('Content-Length: ' . strlen($response));
        echo $response;
        exit;
    }
}

function resolve_relative_stream_url($relative, $currentDirUrl, $baseUrl) {
    $relative = trim($relative);
    if (filter_var($relative, FILTER_VALIDATE_URL)) {
        return $relative;
    }
    if (strpos($relative, '//') === 0) {
        return 'https:' . $relative;
    }
    if (strlen($relative) > 0 && $relative[0] === '/') {
        return rtrim($baseUrl, '/') . $relative;
    }
    return $currentDirUrl . $relative;
}
