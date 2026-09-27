<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Moviq — Discover Movies, TV Shows & Official Trailers</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Moviq is your ultimate cinematic hub to discover trending movies, top-rated TV shows, watch high-definition trailers, explore genres, and manage your personal watchlist in style.">
    <meta name="keywords" content="movies, tv shows, streaming, trailers, tmdb, top rated movies, trending movies, cinema, moviq, watchlist">
    <meta name="author" content="Moviq">
    <meta name="theme-color" content="#080b12">

    <!-- OpenGraph / Social Media Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Moviq — The Ultimate Movie & TV Discovery Hub">
    <meta property="og:description" content="Explore trending blockbusters, stream high-definition trailers, discover hidden gems, and track your favorite movies.">
    <meta property="og:image" content="https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1200&auto=format&fit=crop&q=80">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="Moviq — Discover Movies, TV Shows & Trailers">
    <meta property="twitter:description" content="Explore trending blockbusters, stream trailers, and manage your personal watchlist.">

    <!-- Google Fonts: Outfit (Display & Headings) and Plus Jakarta Sans (UI & Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- App Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Favicon (Inline SVG) -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='g' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' stop-color='%237928ca'/><stop offset='100%' stop-color='%23ff0080'/></linearGradient></defs><rect width='100' height='100' rx='24' fill='%23080b12'/><path d='M25 25 L45 50 L25 75 Z M45 25 L65 50 L45 75 Z M65 25 L85 50 L65 75 Z' fill='url(%23g)'/></svg>">

    <!-- Early VIP State Check for Instant Seamless Page Transitions -->
    <script>
        (function() {
            try {
                var isPerm = localStorage.getItem('moviq_vip_active') === 'true';
                var demoUntil = parseInt(localStorage.getItem('moviq_vip_demo_until') || '0', 10);
                if (isPerm || (demoUntil > Date.now())) {
                    document.documentElement.classList.add('vip-active');
                    window.__EARLY_VIP__ = true;
                }
            } catch(e) {}
        })();
    </script>
</head>
<body class="<?php echo !empty($_COOKIE['vip']) ? 'vip-active' : ''; ?>">
    <script>
        if (window.__EARLY_VIP__) {
            document.body.classList.add('vip-active');
        }
    </script>
    <!-- Ambient Dynamic Glow Background Orbs -->
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>
    <div class="ambient-glow glow-3"></div>
