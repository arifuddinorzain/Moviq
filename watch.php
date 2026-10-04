<?php
/**
 * Moviq — Watch Movie & TV Shows Cinema Player
 * Powered by VidLink (Primary) and VidFast (Secondary) with server switching, theater mode, and TV episode navigation.
 */

require_once __DIR__ . '/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = (isset($_GET['type']) && $_GET['type'] === 'tv') ? 'tv' : 'movie';
$season = isset($_GET['s']) ? max(1, (int)$_GET['s']) : 1;
$episode = isset($_GET['e']) ? max(1, (int)$_GET['e']) : 1;
$server = isset($_GET['server']) ? trim($_GET['server']) : 'vidlink';

// If no ID is provided, default to trending title or redirect to home
if ($id <= 0) {
    $trending = tmdb_request('/trending/movie/day', ['page' => 1]);
    if ($trending && !empty($trending['results'])) {
        $id = (int)$trending['results'][0]['id'];
        $type = 'movie';
    } else {
        header('Location: index.php');
        exit;
    }
}

// Fetch full title details from TMDB with videos, credits, similar, and recommendations
$data = tmdb_request("/{$type}/{$id}", [
    'append_to_response' => 'videos,credits,similar,recommendations,keywords'
], 3600);

if (!$data) {
    // If not found, show error state
    $pageTitle = "Title Not Found — Moviq";
    require_once __DIR__ . '/includes/header.php';
    require_once __DIR__ . '/includes/navbar.php';
    ?>
    <main class="main-content" style="min-height: 70vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 4rem 1.5rem;">
        <div class="empty-state-box" style="max-width: 500px;">
            <div style="font-size: 4rem; color: #ff0080; margin-bottom: 1rem;"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <h2 style="font-family: var(--font-heading); font-size: 2rem; margin-bottom: 0.8rem;">Title Not Available</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">We couldn't retrieve the streaming details for this title. It may have been removed or TMDB is temporarily unreachable.</p>
            <a href="index.php#home" class="btn btn-gradient btn-lg"><i class="fa-solid fa-house"></i> Back to Home</a>
        </div>
    </main>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$title = $data['title'] ?? $data['name'] ?? 'Untitled';
$pageTitle = "Watch {$title} " . ($type === 'tv' ? "(S{$season} E{$episode}) " : '') . "Online Free in HD — Moviq";
$tagline = $data['tagline'] ?? '';
$overview = $data['overview'] ?? 'No storyline summary available for this title.';
$releaseDate = $data['release_date'] ?? $data['first_air_date'] ?? '';
$releaseYear = !empty($releaseDate) ? substr($releaseDate, 0, 4) : 'TBA';
$rating = isset($data['vote_average']) ? format_rating($data['vote_average']) : 'N/A';
$voteCount = isset($data['vote_count']) ? number_format($data['vote_count']) : '0';
$backdropUrl = !empty($data['backdrop_path']) ? tmdb_image($data['backdrop_path'], 'w1280') : '';
$posterUrl = !empty($data['poster_path']) ? tmdb_image($data['poster_path'], 'w500') : '';
$runtime = ($type === 'movie' && !empty($data['runtime'])) ? format_runtime($data['runtime']) : ($data['number_of_seasons'] ?? 1) . ' Season(s)';
$genres = $data['genres'] ?? [];

// Trailer Key
$trailerKey = null;
if (!empty($data['videos']['results'])) {
    foreach ($data['videos']['results'] as $v) {
        if ($v['site'] === 'YouTube' && in_array($v['type'], ['Trailer', 'Teaser'])) {
            $trailerKey = $v['key'];
            if ($v['type'] === 'Trailer') break;
        }
    }
}

// Build Embed Player URLs
// VidFast.vc (Fast HD Stream - High Availability)
$vidfastUrl = ($type === 'tv') 
    ? "https://vidfast.vc/tv/{$id}/{$season}/{$episode}?autoPlay=true&theme=ff0080" 
    : "https://vidfast.vc/movie/{$id}?autoPlay=true&theme=ff0080";

// VidLink (Minimal / Low Ads - Default Sound Unmuted)
$vidlinkUrl = ($type === 'tv')
    ? "https://vidlink.pro/tv/{$id}/{$season}/{$episode}?primaryColor=ff0080&secondaryColor=7928ca&iconColor=ff0080&muted=false&volume=1"
    : "https://vidlink.pro/movie/{$id}?primaryColor=ff0080&secondaryColor=7928ca&iconColor=ff0080&muted=false&volume=1";

// EmbedSU (Multi-Source Pro)
$embedsuUrl = ($type === 'tv')
    ? "https://embed.su/embed/tv/{$id}/{$season}/{$episode}"
    : "https://embed.su/embed/movie/{$id}";

// VidSrc (Backup Source)
$vidsrcUrl = ($type === 'tv') 
    ? "https://vidsrc.to/embed/tv/{$id}/{$season}/{$episode}" 
    : "https://vidsrc.to/embed/movie/{$id}";

// High Quality Stream Servers List with Clean Badges
$servers = [
    'vidlink' => [
        'name' => 'VidLink (Clean / Low Ads)',
        'icon' => 'fa-shield-halved',
        'badge' => 'Main Server',
        'badge_class' => 'adfree',
        'url' => $vidlinkUrl
    ],
    'vidfast' => [
        'name' => 'VidFast (Fast HD)',
        'icon' => 'fa-bolt',
        'badge' => 'Server 2',
        'badge_class' => 'hd',
        'url' => $vidfastUrl
    ],
    'embedsu' => [
        'name' => 'EmbedSU (Multi-Source)',
        'icon' => 'fa-server',
        'badge' => 'Multi',
        'badge_class' => 'multi',
        'url' => $embedsuUrl
    ],
    'vidsrc' => [
        'name' => 'VidSrc Alternative',
        'icon' => 'fa-film',
        'badge' => 'Backup',
        'badge_class' => 'backup',
        'url' => $vidsrcUrl
    ]
];

$activePlayerUrl = $servers[$server]['url'] ?? $vidlinkUrl;

// TV Show Episode Navigation & Seasons
$tvEpisodes = [];
$totalSeasons = 1;
$currentSeasonData = null;
if ($type === 'tv') {
    $totalSeasons = $data['number_of_seasons'] ?? 1;
    // Fetch season episodes
    $seasonRes = tmdb_request("/tv/{$id}/season/{$season}", [], 86400);
    if ($seasonRes && !empty($seasonRes['episodes'])) {
        $tvEpisodes = $seasonRes['episodes'];
        $currentSeasonData = $seasonRes;
    }
}

// Similar / Recommendations
$similarItems = !empty($data['recommendations']['results']) 
    ? array_slice($data['recommendations']['results'], 0, 12) 
    : array_slice($data['similar']['results'] ?? [], 0, 12);

// Cast members
$castMembers = array_slice($data['credits']['cast'] ?? [], 0, 16);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="watch-page-main" id="watchPageMain">
    <!-- Theater Mode Dim Backdrop -->
    <div class="theater-backdrop" id="theaterBackdrop"></div>

    <div class="watch-container">
        <!-- Top Navigation Bar & Breadcrumbs -->
        <div class="watch-breadcrumb-bar">
            <div class="watch-breadcrumbs">
                <a href="index.php#home"><i class="fa-solid fa-house"></i> Home</a>
                <i class="fa-solid fa-chevron-right separator"></i>
                <a href="index.php#<?php echo $type === 'tv' ? 'tv' : 'movies'; ?>"><?php echo $type === 'tv' ? 'TV Shows' : 'Movies'; ?></a>
                <i class="fa-solid fa-chevron-right separator"></i>
                <span class="active-crumb"><?php echo htmlspecialchars($title); ?></span>
                <?php if ($type === 'tv'): ?>
                    <span class="season-badge-crumb">Season <?php echo $season; ?> &bull; Episode <?php echo $episode; ?></span>
                <?php endif; ?>
            </div>

            <div class="watch-top-actions">
                <button class="btn btn-secondary btn-sm" id="toggleTheaterBtn" title="Toggle Theater Mode (Lights Off)">
                    <i class="fa-solid fa-lightbulb"></i> <span class="theater-btn-text">Theater Mode</span>
                </button>
                <a href="index.php#explore" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-compass"></i> Explore More
                </a>
            </div>
        </div>

        <!-- Cinema Video Player Section -->
        <div class="cinema-player-section" id="cinemaPlayerSection">
            <!-- Player Ambient Glow -->
            <div class="player-glow-ambient" style="background-image: url('<?php echo htmlspecialchars($backdropUrl); ?>');"></div>

            <div class="player-aspect-wrapper" id="playerAspectWrapper">
                <!-- Status / Auto Failover Loading Overlay -->
                <div class="player-status-overlay" id="playerStatusOverlay" style="display: none;">
                    <div class="status-spinner-ring"></div>
                    <div id="playerStatusText">Connecting to streaming server...</div>
                </div>

                <!-- Streaming iframe -->
                <iframe 
                    id="mainVideoPlayer"
                    class="main-player-iframe"
                    src="<?php echo htmlspecialchars($activePlayerUrl); ?>"
                    allowfullscreen="true"
                    webkitallowfullscreen="true"
                    mozallowfullscreen="true"
                    allow="autoplay; encrypted-media; picture-in-picture; fullscreen"
                    loading="eager"
                    title="<?php echo htmlspecialchars($title); ?> Player">
                </iframe>
            </div>

            <!-- Player Controls & Server Switcher Bar -->
            <div class="player-controls-bar">
                <div class="player-servers-list">
                    <span class="servers-label"><i class="fa-solid fa-server"></i> Servers:</span>
                    <?php foreach ($servers as $sKey => $sInfo): ?>
                        <a href="watch.php?id=<?php echo $id; ?>&type=<?php echo $type; ?><?php echo $type === 'tv' ? "&s={$season}&e={$episode}" : ''; ?>&server=<?php echo $sKey; ?>" 
                           class="server-pill <?php echo $server === $sKey ? 'active' : ''; ?>"
                           data-server-key="<?php echo $sKey; ?>"
                           data-server-name="<?php echo htmlspecialchars($sInfo['name']); ?>">
                            <i class="fa-solid <?php echo $sInfo['icon']; ?>"></i> 
                            <span><?php echo $sInfo['name']; ?></span>
                            <?php if (!empty($sInfo['badge'])): ?>
                                <span class="server-tag-badge <?php echo $sInfo['badge_class'] ?? ''; ?>"><?php echo $sInfo['badge']; ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="player-quick-tools">
                    <button class="tool-btn" id="nextServerBtn" title="Switch to Next Available Server">
                        <i class="fa-solid fa-angles-right"></i> Next Server
                    </button>
                    <button class="tool-btn" id="reloadPlayerBtn" title="Reload Video Stream">
                        <i class="fa-solid fa-rotate-right"></i> Reload
                    </button>
                    <?php if ($type === 'tv' && !empty($tvEpisodes)): ?>
                        <?php if ($episode > 1): ?>
                            <a href="watch.php?id=<?php echo $id; ?>&type=tv&s=<?php echo $season; ?>&e=<?php echo $episode - 1; ?>&server=<?php echo $server; ?>" class="tool-btn" title="Previous Episode">
                                <i class="fa-solid fa-backward-step"></i> Prev Ep
                            </a>
                        <?php endif; ?>
                        <?php if ($episode < count($tvEpisodes)): ?>
                            <a href="watch.php?id=<?php echo $id; ?>&type=tv&s=<?php echo $season; ?>&e=<?php echo $episode + 1; ?>&server=<?php echo $server; ?>" class="tool-btn highlight" title="Next Episode">
                                Next Ep <i class="fa-solid fa-forward-step"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Stream Troubleshooting & Quick Server Switch Bar -->
        <div class="player-troubleshoot-banner" id="streamTroubleshootBanner">
            <div class="troubleshoot-text">
                <i class="fa-solid fa-circle-question"></i>
                <span>Showing <em>"Content Not Found"</em> or buffering?</span>
            </div>
            <button class="btn-switch-server-quick" id="quickNextServerBtn" title="Switch to another server">
                <i class="fa-solid fa-angles-right"></i> Try Next Server (<span id="quickNextServerName">VidFast</span>)
            </button>
        </div>

        <!-- TV Show Seasons & Episode Selector Shelf -->
        <?php if ($type === 'tv'): ?>
            <section class="watch-tv-browser-section">
                <div class="tv-browser-header">
                    <div class="tv-season-selector-wrapper">
                        <h3 class="watch-section-title"><i class="fa-solid fa-list-ol"></i> Episodes</h3>
                        <div class="season-pills-row">
                            <?php for ($s = 1; $s <= $totalSeasons; $s++): ?>
                                <a href="watch.php?id=<?php echo $id; ?>&type=tv&s=<?php echo $s; ?>&e=1&server=<?php echo $server; ?>" 
                                   class="season-pill <?php echo $s === $season ? 'active' : ''; ?>">
                                    Season <?php echo $s; ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

                <!-- Episodes Grid -->
                <div class="episodes-cards-grid">
                    <?php if (!empty($tvEpisodes)): ?>
                        <?php foreach ($tvEpisodes as $ep): 
                            $epNum = $ep['episode_number'] ?? 1;
                            $epName = $ep['name'] ?? "Episode {$epNum}";
                            $epStill = !empty($ep['still_path']) ? tmdb_image($ep['still_path'], 'w300') : $backdropUrl;
                            $epAirDate = !empty($ep['air_date']) ? date('M d, Y', strtotime($ep['air_date'])) : '';
                            $epOverview = !empty($ep['overview']) ? $ep['overview'] : 'No episode description available.';
                            $isCurrentEp = ($epNum === $episode);
                        ?>
                            <a href="watch.php?id=<?php echo $id; ?>&type=tv&s=<?php echo $season; ?>&e=<?php echo $epNum; ?>&server=<?php echo $server; ?>" 
                               class="episode-card <?php echo $isCurrentEp ? 'now-playing' : ''; ?>">
                                <div class="episode-still-box">
                                    <img src="<?php echo htmlspecialchars($epStill); ?>" alt="<?php echo htmlspecialchars($epName); ?>" loading="lazy" decoding="async" onerror="this.onerror=null; this.src=Api.getPosterPlaceholder();">
                                    <div class="ep-number-tag">EP <?php echo $epNum; ?></div>
                                    <?php if ($isCurrentEp): ?>
                                        <div class="ep-playing-badge"><i class="fa-solid fa-circle-play"></i> Playing</div>
                                    <?php endif; ?>
                                </div>
                                <div class="episode-info-box">
                                    <h4 class="episode-title" title="<?php echo htmlspecialchars($epName); ?>"><?php echo htmlspecialchars($epName); ?></h4>
                                    <?php if ($epAirDate): ?>
                                        <div class="episode-air-date"><i class="fa-solid fa-calendar-day"></i> <?php echo $epAirDate; ?></div>
                                    <?php endif; ?>
                                    <p class="episode-overview"><?php echo htmlspecialchars($epOverview); ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding: 2rem; color: var(--text-muted);">No episodes found for this season.</div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Movie / TV Details & Action Info Panel -->
        <section class="watch-details-panel">
            <div class="watch-details-grid">
                <!-- Left Poster -->
                <div class="watch-poster-col">
                    <img src="<?php echo htmlspecialchars($posterUrl); ?>" alt="<?php echo htmlspecialchars($title); ?>" class="watch-poster-img" decoding="async" onerror="this.onerror=null; this.src=Api.getPosterPlaceholder();">
                </div>

                <!-- Center/Right Details -->
                <div class="watch-info-col">
                    <div class="watch-title-header">
                        <h1 class="watch-main-title"><?php echo htmlspecialchars($title); ?></h1>
                        <?php if ($tagline): ?>
                            <p class="watch-tagline">"<?php echo htmlspecialchars($tagline); ?>"</p>
                        <?php endif; ?>
                    </div>

                    <!-- Meta Badges -->
                    <div class="watch-meta-badges">
                        <span class="meta-chip score-badge"><i class="fa-solid fa-star"></i> <?php echo $rating; ?> (<?php echo $voteCount; ?> votes)</span>
                        <span class="meta-chip"><i class="fa-solid fa-calendar"></i> <?php echo $releaseYear; ?></span>
                        <span class="meta-chip"><i class="fa-solid fa-clock"></i> <?php echo $runtime; ?></span>
                        <span class="meta-chip type-badge"><?php echo $type === 'tv' ? 'TV Series' : 'Movie'; ?></span>
                        <?php foreach ($genres as $g): ?>
                            <span class="meta-chip genre-chip"><?php echo htmlspecialchars($g['name']); ?></span>
                        <?php endforeach; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div class="watch-actions-bar">
                        <?php if ($trailerKey): ?>
                            <button class="btn btn-secondary modal-trailer-btn" data-key="<?php echo htmlspecialchars($trailerKey); ?>" data-title="<?php echo htmlspecialchars($title); ?>">
                                <i class="fa-solid fa-film"></i> Watch Trailer
                            </button>
                        <?php endif; ?>
                        <button class="btn btn-secondary watch-bookmark-btn" id="watchBookmarkBtn" data-id="<?php echo $id; ?>" data-type="<?php echo $type; ?>">
                            <i class="fa-solid fa-bookmark"></i> Add to Watchlist
                        </button>
                        <button class="btn btn-secondary" id="watchShareBtn" data-title="<?php echo htmlspecialchars($title); ?>">
                            <i class="fa-solid fa-share-nodes"></i> Share
                        </button>
                    </div>

                    <!-- Storyline -->
                    <div class="watch-overview-box">
                        <h3 class="watch-section-title"><i class="fa-solid fa-book-open"></i> Storyline</h3>
                        <p class="watch-overview-text"><?php echo htmlspecialchars($overview); ?></p>
                    </div>

                    <!-- Cast & Crew Row -->
                    <?php if (!empty($castMembers)): ?>
                        <div class="watch-cast-box">
                            <h3 class="watch-section-title"><i class="fa-solid fa-users"></i> Top Cast & Crew</h3>
                            <div class="cast-avatars-row">
                                <?php foreach ($castMembers as $c): 
                                    $cName = $c['name'] ?? 'Unknown Actor';
                                    $cRole = $c['character'] ?? 'Cast';
                                    $cImg = !empty($c['profile_path']) ? tmdb_image($c['profile_path'], 'w185') : null;
                                ?>
                                    <div class="cast-item" onclick="App.openPersonModal(<?php echo (int)$c['id']; ?>)">
                                        <div class="cast-img-box">
                                            <img src="<?php echo $cImg ? htmlspecialchars($cImg) : "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'><rect width='200' height='200' rx='100' fill='%231e293b'/></svg>"; ?>" 
                                                 alt="<?php echo htmlspecialchars($cName); ?>" 
                                                 loading="lazy" 
                                                 decoding="async" 
                                                 onerror="this.onerror=null; this.src=Api.getProfilePlaceholder();">
                                        </div>
                                        <div class="cast-name" title="<?php echo htmlspecialchars($cName); ?>"><?php echo htmlspecialchars($cName); ?></div>
                                        <div class="cast-role" title="<?php echo htmlspecialchars($cRole); ?>"><?php echo htmlspecialchars($cRole); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Recommended & Similar Movies Carousel -->
        <?php if (!empty($similarItems)): ?>
            <section class="watch-recommended-section">
                <div class="shelf-header">
                    <div class="shelf-title-box">
                        <i class="fa-solid fa-wand-magic-sparkles shelf-icon"></i>
                        <h2 class="shelf-title">You May Also Like</h2>
                    </div>
                </div>
                <div class="shelf-row">
                    <?php foreach ($similarItems as $sim): 
                        $sId = (int)$sim['id'];
                        $sType = $sim['media_type'] ?? ($type === 'tv' ? 'tv' : 'movie');
                        $sTitle = $sim['title'] ?? $sim['name'] ?? 'Untitled';
                        $sPoster = !empty($sim['poster_path']) ? tmdb_image($sim['poster_path'], 'w342') : '';
                        $sRating = isset($sim['vote_average']) ? format_rating($sim['vote_average']) : 'N/A';
                        $sYear = !empty($sim['release_date'] ?? $sim['first_air_date'] ?? '') ? substr($sim['release_date'] ?? $sim['first_air_date'], 0, 4) : 'TBA';
                    ?>
                        <div class="movie-card" onclick="window.location.href='watch.php?id=<?php echo $sId; ?>&type=<?php echo $sType; ?>'">
                            <div class="poster-wrapper">
                                <img class="card-poster-img" src="<?php echo htmlspecialchars($sPoster); ?>" alt="<?php echo htmlspecialchars($sTitle); ?>" loading="lazy" decoding="async" onerror="this.onerror=null; this.src=Api.getPosterPlaceholder();">
                                <div class="card-rating-badge"><i class="fa-solid fa-star"></i> <?php echo $sRating; ?></div>
                                <div class="card-type-badge"><?php echo $sType === 'tv' ? 'TV Series' : 'Movie'; ?></div>
                                <div class="card-play-overlay">
                                    <div class="card-play-btn"><i class="fa-solid fa-play"></i></div>
                                </div>
                            </div>
                            <div class="card-info">
                                <h4 class="card-title" title="<?php echo htmlspecialchars($sTitle); ?>"><?php echo htmlspecialchars($sTitle); ?></h4>
                                <div class="card-meta-row">
                                    <span><?php echo $sYear; ?></span>
                                    <span><i class="fa-solid fa-film"></i> HD</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</main>

<!-- Interactive Scripts for Watch Page -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Theater Mode Toggle
    const theaterBtn = document.getElementById('toggleTheaterBtn');
    const theaterBackdrop = document.getElementById('theaterBackdrop');
    
    if (theaterBtn) {
        theaterBtn.addEventListener('click', () => {
            document.body.classList.toggle('theater-mode-active');
            const isActive = document.body.classList.contains('theater-mode-active');
            theaterBtn.innerHTML = isActive 
                ? '<i class="fa-solid fa-lightbulb" style="color: #ff0080;"></i> <span>Lights On</span>' 
                : '<i class="fa-solid fa-lightbulb"></i> <span>Theater Mode</span>';
        });
    }
    
    if (theaterBackdrop) {
        theaterBackdrop.addEventListener('click', () => {
            document.body.classList.remove('theater-mode-active');
            if (theaterBtn) theaterBtn.innerHTML = '<i class="fa-solid fa-lightbulb"></i> <span>Theater Mode</span>';
        });
    }

    const iframe = document.getElementById('mainVideoPlayer');
    const statusOverlay = document.getElementById('playerStatusOverlay');
    const statusText = document.getElementById('playerStatusText');

    // Reload Player Button
    const reloadBtn = document.getElementById('reloadPlayerBtn');
    if (reloadBtn && iframe) {
        reloadBtn.addEventListener('click', () => {
            const currentSrc = iframe.src;
            if (statusOverlay && statusText) {
                statusText.innerHTML = '<span>Reloading video stream...</span>';
                statusOverlay.style.display = 'flex';
            }
            iframe.src = '';
            setTimeout(() => { 
                iframe.src = currentSrc; 
                armFailoverWatchdog();
            }, 150);
            if (window.App && App.showToast) {
                App.showToast('Reloading video stream...', 'info');
            }
        });
    }

    // --- Auto Server Failover & Seamless Switcher System ---
    const streamServers = <?php echo json_encode(array_values(array_map(function($key, $s) use ($id, $type, $season, $episode) {
        return [
            'key' => $key,
            'name' => $s['name'],
            'url' => $s['url'],
            'watchUrl' => "watch.php?id={$id}&type={$type}" . ($type === 'tv' ? "&s={$season}&e={$episode}" : '') . "&server={$key}"
        ];
    }, array_keys($servers), $servers))); ?>;

    let currentServerIndex = streamServers.findIndex(s => s.key === <?php echo json_encode($server); ?>);
    if (currentServerIndex === -1) currentServerIndex = 0;

    let attemptedServers = new Set([currentServerIndex]);
    let failoverTimer = null;

        function updateServerPills(idx) {
        const sObj = streamServers[idx];
        if (!sObj) return;
        document.querySelectorAll('.player-servers-list .server-pill').forEach(pill => {
            const isMatch = (pill.getAttribute('data-server-key') === sObj.key);
            pill.classList.toggle('active', isMatch);
        });
        
        // Update next server name echo in quick-switch troubleshoot button
        const nextIdx = (idx + 1) % streamServers.length;
        const nextServerObj = streamServers[nextIdx];
        const nextNameEl = document.getElementById('quickNextServerName');
        if (nextNameEl && nextServerObj) {
            nextNameEl.textContent = nextServerObj.name.split(' ')[0]; // E.g. "VidLink" or "VidFast"
        }

        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', sObj.watchUrl);
        }
    }

    function switchStreamServer(idx, isAutoFailover = false) {
        if (idx < 0 || idx >= streamServers.length) return;
        currentServerIndex = idx;
        attemptedServers.add(idx);
        const targetServer = streamServers[idx];

        updateServerPills(idx);

        if (statusOverlay && statusText) {
            statusText.innerHTML = `<span>Connecting to <strong>${targetServer.name}</strong>...</span>`;
            statusOverlay.style.display = 'flex';
        }

        if (isAutoFailover && window.App && App.showToast) {
            App.showToast(`Server issue detected. Auto-switching to ${targetServer.name}...`, 'info');
        }

        if (iframe) {
            iframe.src = targetServer.url;
        }

        armFailoverWatchdog();
    }

    function triggerNextServerFallback() {
        let nextIdx = -1;
        for (let i = 0; i < streamServers.length; i++) {
            const candidateIdx = (currentServerIndex + 1 + i) % streamServers.length;
            if (!attemptedServers.has(candidateIdx)) {
                nextIdx = candidateIdx;
                break;
            }
        }

        if (nextIdx !== -1) {
            switchStreamServer(nextIdx, true);
        } else {
            if (statusOverlay && statusText) {
                statusText.innerHTML = `
                    <div style="font-size: 2.2rem; color: #ff4b2b; margin-bottom: 0.5rem;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.3rem;">All stream servers were attempted</div>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">The video stream could not be loaded automatically.</p>
                    <button class="btn btn-gradient btn-sm" id="retryServerOneBtn"><i class="fa-solid fa-rotate-right"></i> Retry Server 1</button>
                `;
                statusOverlay.style.display = 'flex';
                const retryBtn = document.getElementById('retryServerOneBtn');
                if (retryBtn) {
                    retryBtn.addEventListener('click', () => {
                        attemptedServers.clear();
                        switchStreamServer(0, false);
                    });
                }
            }
        }
    }

    function armFailoverWatchdog() {
        if (failoverTimer) clearTimeout(failoverTimer);
        failoverTimer = setTimeout(() => {
            if (statusOverlay && statusOverlay.style.display !== 'none') {
                statusOverlay.style.display = 'none';
            }
        }, 8000);
    }

    // Iframe monitoring
    if (iframe) {
        iframe.addEventListener('load', () => {
            if (failoverTimer) clearTimeout(failoverTimer);
            if (statusOverlay) statusOverlay.style.display = 'none';
        });

        iframe.addEventListener('error', () => {
            triggerNextServerFallback();
        });
    }

    // Listen to potential postMessage signals from embed players
    window.addEventListener('message', (event) => {
        try {
            const data = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;
            if (data && (data.event === 'error' || data.type === 'error' || data.error || data.status === 404 || data.status === 'not_found')) {
                triggerNextServerFallback();
            }
        } catch (e) {}
    });

    // Initial server pills state
    updateServerPills(currentServerIndex);
    armFailoverWatchdog();

    // Next Server Button in Quick Tools
    const nextServerBtn = document.getElementById('nextServerBtn');
    if (nextServerBtn) {
        nextServerBtn.addEventListener('click', () => {
            const nextIdx = (currentServerIndex + 1) % streamServers.length;
            switchStreamServer(nextIdx, false);
            if (window.App && App.showToast) {
                App.showToast(`Switched to ${streamServers[nextIdx].name}`, 'success');
            }
        });
    }

    // Quick Next Server Troubleshoot Banner Button
    const quickNextBtn = document.getElementById('quickNextServerBtn');
    if (quickNextBtn) {
        quickNextBtn.addEventListener('click', () => {
            const nextIdx = (currentServerIndex + 1) % streamServers.length;
            switchStreamServer(nextIdx, false);
            if (window.App && App.showToast) {
                App.showToast(`Switched to ${streamServers[nextIdx].name}`, 'success');
            }
        });
    }

    // Server Pills instant switching without page reload
    document.querySelectorAll('.player-servers-list .server-pill').forEach(pill => {
        pill.addEventListener('click', (e) => {
            e.preventDefault();
            const sKey = pill.getAttribute('data-server-key');
            const sIdx = streamServers.findIndex(s => s.key === sKey);
            if (sIdx !== -1 && sIdx !== currentServerIndex) {
                switchStreamServer(sIdx, false);
            }
        });
    });

    // Watchlist sync on Watch Page
    const watchBookmarkBtn = document.getElementById('watchBookmarkBtn');
    if (watchBookmarkBtn && window.Watchlist) {
        const itemObj = {
            id: <?php echo $id; ?>,
            type: '<?php echo $type; ?>',
            title: <?php echo json_encode($title); ?>,
            poster_path: <?php echo json_encode($data['poster_path'] ?? ''); ?>,
            vote_average: <?php echo (float)($data['vote_average'] ?? 0); ?>,
            release_date: <?php echo json_encode($data['release_date'] ?? $data['first_air_date'] ?? ''); ?>
        };

        const updateBtn = () => {
            const has = Watchlist.has(itemObj.id, itemObj.type);
            watchBookmarkBtn.classList.toggle('active', has);
            watchBookmarkBtn.innerHTML = has 
                ? '<i class="fa-solid fa-check"></i> In Watchlist' 
                : '<i class="fa-solid fa-bookmark"></i> Add to Watchlist';
        };

        updateBtn();

        watchBookmarkBtn.addEventListener('click', () => {
            const res = Watchlist.toggle(itemObj);
            updateBtn();
            if (window.App && App.showToast) {
                if (res.limitReached) {
                    App.showToast(`⚠️ Watchlist full (${res.count}/${res.limit} slots)! Buy +2 space for 50 Coins in Rewards Shop.`, 'info');
                } else {
                    App.showToast(res.added ? `Added "${itemObj.title}" to Watchlist` : `Removed "${itemObj.title}" from Watchlist`, res.added ? 'success' : 'remove');
                }
            }
        });
    }

    // Share Button
    const shareBtn = document.getElementById('watchShareBtn');
    if (shareBtn) {
        shareBtn.addEventListener('click', () => {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    if (window.App && App.showToast) App.showToast('Watch link copied to clipboard!', 'success');
                });
            }
        });
    }
});
</script>

<?php
require_once __DIR__ . '/includes/modal.php';
require_once __DIR__ . '/includes/toast.php';
require_once __DIR__ . '/includes/footer.php';
?>
