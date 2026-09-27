<?php
/**
 * Vercel Serverless Router for Moviq
 * Dispatches requests to the appropriate PHP handler
 */

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH);

// Change working directory to project root so relative includes work smoothly
chdir(__DIR__ . '/..');

if (strpos($path, '/api.php') !== false || strpos($path, '/api') === 0) {
    require __DIR__ . '/../api.php';
} elseif (strpos($path, '/watch.php') !== false || strpos($path, '/watch') === 0) {
    require __DIR__ . '/../watch.php';
} else {
    require __DIR__ . '/../index.php';
}
