<?php
/**
 * Moviq — Discover Movies, TV Shows & Official Trailers
 * Main Application Entry Point
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Main Content Area -->
<main class="main-content" id="mainContent">

    <!-- ========================================================
         VIEW 1: HOME VIEW (Default)
    ======================================================== -->
    <div class="view-section active" id="homeView">
        <!-- Dynamic Hero Slider -->
        <?php require_once __DIR__ . '/includes/hero.php'; ?>

        <!-- Quick Genre Pills Ribbon -->
        <section class="genre-ribbon-section">
            <div class="section-container">
                <div class="genre-ribbon-wrapper">
                    <span class="ribbon-label"><i class="fa-solid fa-shapes"></i> Genres:</span>
                    <button class="genre-ribbon-nav-btn prev" id="genreRibbonPrev" aria-label="Scroll genres left" title="Scroll left">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <div class="genre-pills-list" id="genreQuickPills">
                        <button class="genre-pill active" data-genre-id="all">✨ All</button>
                        <button class="genre-pill" data-genre-id="28" data-name="Action">💥 Action</button>
                        <button class="genre-pill" data-genre-id="korean" data-name="Korean">🇰🇷 Korean</button>
                        <button class="genre-pill" data-genre-id="878" data-name="Sci-Fi">🚀 Sci-Fi</button>
                        <button class="genre-pill" data-genre-id="12" data-name="Adventure">🗺️ Adventure</button>
                        <button class="genre-pill" data-genre-id="16" data-name="Animation">🎨 Animation</button>
                        <button class="genre-pill" data-genre-id="35" data-name="Comedy">😂 Comedy</button>
                        <button class="genre-pill" data-genre-id="27" data-name="Horror">👻 Horror</button>
                        <button class="genre-pill" data-genre-id="53" data-name="Thriller">⚡ Thriller</button>
                        <button class="genre-pill" data-genre-id="10749" data-name="Romance">💖 Romance</button>
                        <button class="genre-pill" data-genre-id="14" data-name="Fantasy">🔮 Fantasy</button>
                        <button class="genre-pill" data-genre-id="99" data-name="Documentary">📽️ Documentary</button>
                    </div>
                    <button class="genre-ribbon-nav-btn next" id="genreRibbonNext" aria-label="Scroll genres right" title="Scroll right">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Shelf 1: Trending Today Top 10 (Styled Rank Badges) -->
        <section class="shelf-section trending-shelf">
            <div class="section-container">
                <div class="section-header">
                    <div class="header-left">
                        <div class="section-badge fire-badge"><i class="fa-solid fa-fire"></i> Top 10 Today</div>
                        <h2 class="section-title">Trending Globally</h2>
                    </div>
                    <div class="header-actions-shelf">
                        <button class="shelf-nav-btn prev" data-target="trendingShelfRow" aria-label="Scroll left"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="shelf-nav-btn next" data-target="trendingShelfRow" aria-label="Scroll right"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="shelf-row rank-shelf-row" id="trendingShelfRow">
                    <!-- Dynamic Top 10 Cards with Giant Numbers -->
                    <div class="shelf-loading-cards">
                        <?php for($i=0; $i<6; $i++): ?>
                            <div class="movie-card-skeleton"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Shelf 2: In Theaters / Now Playing -->
        <section class="shelf-section">
            <div class="section-container">
                <div class="section-header">
                    <div class="header-left">
                        <div class="section-badge theater-badge"><i class="fa-solid fa-ticket"></i> In Theaters</div>
                        <h2 class="section-title">Now Playing</h2>
                    </div>
                    <div class="header-actions-shelf">
                        <a href="#movies" class="see-all-link" data-category="now_playing">See All <i class="fa-solid fa-arrow-right"></i></a>
                        <button class="shelf-nav-btn prev" data-target="nowPlayingShelfRow" aria-label="Scroll left"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="shelf-nav-btn next" data-target="nowPlayingShelfRow" aria-label="Scroll right"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="shelf-row standard-shelf-row" id="nowPlayingShelfRow">
                    <div class="shelf-loading-cards">
                        <?php for($i=0; $i<6; $i++): ?>
                            <div class="movie-card-skeleton"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Shelf 3: Top Rated Cinema Masterpieces -->
        <section class="shelf-section">
            <div class="section-container">
                <div class="section-header">
                    <div class="header-left">
                        <div class="section-badge star-badge"><i class="fa-solid fa-star"></i> Hall of Fame</div>
                        <h2 class="section-title">Top Rated Masterpieces</h2>
                    </div>
                    <div class="header-actions-shelf">
                        <a href="#movies" class="see-all-link" data-category="top_rated">See All <i class="fa-solid fa-arrow-right"></i></a>
                        <button class="shelf-nav-btn prev" data-target="topRatedShelfRow" aria-label="Scroll left"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="shelf-nav-btn next" data-target="topRatedShelfRow" aria-label="Scroll right"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="shelf-row standard-shelf-row" id="topRatedShelfRow">
                    <div class="shelf-loading-cards">
                        <?php for($i=0; $i<6; $i++): ?>
                            <div class="movie-card-skeleton"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Shelf 4: Popular TV Series & Dramas -->
        <section class="shelf-section">
            <div class="section-container">
                <div class="section-header">
                    <div class="header-left">
                        <div class="section-badge tv-badge"><i class="fa-solid fa-tv"></i> Binge Worthy</div>
                        <h2 class="section-title">Popular TV Series</h2>
                    </div>
                    <div class="header-actions-shelf">
                        <a href="#tv" class="see-all-link" data-category="popular_tv">See All <i class="fa-solid fa-arrow-right"></i></a>
                        <button class="shelf-nav-btn prev" data-target="popularTvShelfRow" aria-label="Scroll left"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="shelf-nav-btn next" data-target="popularTvShelfRow" aria-label="Scroll right"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="shelf-row standard-shelf-row" id="popularTvShelfRow">
                    <div class="shelf-loading-cards">
                        <?php for($i=0; $i<6; $i++): ?>
                            <div class="movie-card-skeleton"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Shelf 5: Upcoming Anticipated Blockbusters -->
        <section class="shelf-section">
            <div class="section-container">
                <div class="section-header">
                    <div class="header-left">
                        <div class="section-badge upcoming-badge"><i class="fa-solid fa-calendar-days"></i> Coming Soon</div>
                        <h2 class="section-title">Upcoming Anticipated</h2>
                    </div>
                    <div class="header-actions-shelf">
                        <a href="#movies" class="see-all-link" data-category="upcoming">See All <i class="fa-solid fa-arrow-right"></i></a>
                        <button class="shelf-nav-btn prev" data-target="upcomingShelfRow" aria-label="Scroll left"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="shelf-nav-btn next" data-target="upcomingShelfRow" aria-label="Scroll right"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="shelf-row standard-shelf-row" id="upcomingShelfRow">
                    <div class="shelf-loading-cards">
                        <?php for($i=0; $i<6; $i++): ?>
                            <div class="movie-card-skeleton"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Shelf 6: Trending Korean Movies & K-Cinema -->
        <section class="shelf-section">
            <div class="section-container">
                <div class="section-header">
                    <div class="header-left">
                        <div class="section-badge korean-badge"><i class="fa-solid fa-film"></i> 🇰🇷 Korean Cinema</div>
                        <h2 class="section-title">Trending Korean Movies</h2>
                    </div>
                    <div class="header-actions-shelf">
                        <a href="#explore?with_original_language=ko" class="see-all-link" id="koreanSeeAllLink">See All <i class="fa-solid fa-arrow-right"></i></a>
                        <button class="shelf-nav-btn prev" data-target="koreanShelfRow" aria-label="Scroll left"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="shelf-nav-btn next" data-target="koreanShelfRow" aria-label="Scroll right"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="shelf-row standard-shelf-row" id="koreanShelfRow">
                    <div class="shelf-loading-cards">
                        <?php for($i=0; $i<6; $i++): ?>
                            <div class="movie-card-skeleton"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dynamic Genre Spotlight Section -->
        <section class="genre-spotlight-section">
            <div class="section-container">
                <div class="spotlight-header">
                    <div class="header-left">
                        <div class="section-badge spotlight-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Spotlight</div>
                        <h2 class="section-title">Explore by Genre</h2>
                    </div>
                    <div class="genre-spotlight-tabs" id="spotlightTabs">
                        <button class="spotlight-tab active" data-genre-id="28"><i class="fa-solid fa-burst"></i> Action</button>
                        <button class="spotlight-tab" data-genre-id="korean"><i class="fa-solid fa-heart"></i> 🇰🇷 Korean</button>
                        <button class="spotlight-tab" data-genre-id="878"><i class="fa-solid fa-robot"></i> Sci-Fi</button>
                        <button class="spotlight-tab" data-genre-id="16"><i class="fa-solid fa-palette"></i> Animation</button>
                        <button class="spotlight-tab" data-genre-id="27"><i class="fa-solid fa-skull"></i> Horror</button>
                        <button class="spotlight-tab" data-genre-id="35"><i class="fa-solid fa-face-laugh-beam"></i> Comedy</button>
                        <button class="spotlight-tab" data-genre-id="53"><i class="fa-solid fa-bolt"></i> Thriller</button>
                    </div>
                </div>
                <div class="movies-grid spotlight-grid" id="spotlightGrid">
                    <!-- Populated dynamically based on chosen genre tab -->
                </div>
            </div>
        </section>
    </div>

    <!-- ========================================================
         VIEW 2: EXPLORE & FILTER VIEW (Search / Discovery)
    ======================================================== -->
    <div class="view-section" id="exploreView">
        <div class="section-container">
            <!-- Explore Hero / Header Banner -->
            <div class="explore-hero-banner">
                <div class="explore-hero-content">
                    <span class="section-badge"><i class="fa-solid fa-compass"></i> Discovery Hub</span>
                    <h1 class="explore-main-title" id="exploreMainTitle">Explore Cinematic Universe</h1>
                    <p class="explore-subtitle">Search, filter by genre, release year, rating, and sort through millions of titles on TMDB.</p>
                </div>
            </div>

            <!-- Filter Controls Bar -->
            <div class="filter-controls-panel">
                <div class="filter-row-top">
                    <!-- Media Type Switch -->
                    <div class="filter-group type-filter-group">
                        <label class="filter-label"><i class="fa-solid fa-layer-group"></i> Type</label>
                        <div class="segmented-control" id="exploreMediaType">
                            <button class="segment-btn active" data-value="movie">Movies</button>
                            <button class="segment-btn" data-value="tv">TV Shows</button>
                        </div>
                    </div>

                    <!-- Genre Select -->
                    <div class="filter-group">
                        <label class="filter-label" for="exploreGenreSelect"><i class="fa-solid fa-masks-theater"></i> Genre</label>
                        <div class="custom-select-wrapper">
                            <select id="exploreGenreSelect" class="filter-select">
                                <option value="">All Genres</option>
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                    </div>

                    <!-- Release Year -->
                    <div class="filter-group">
                        <label class="filter-label" for="exploreYearSelect"><i class="fa-solid fa-calendar"></i> Year</label>
                        <div class="custom-select-wrapper">
                            <select id="exploreYearSelect" class="filter-select">
                                <option value="">All Years</option>
                                <option value="2026">2026 (Latest)</option>
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                                <option value="2022">2022</option>
                                <option value="2020">2020</option>
                                <option value="2015">2015</option>
                                <option value="2010">2010</option>
                                <option value="2000">2000s</option>
                                <option value="1990">1990s</option>
                            </select>
                        </div>
                    </div>

                    <!-- Minimum Rating -->
                    <div class="filter-group">
                        <label class="filter-label" for="exploreRatingSelect"><i class="fa-solid fa-star"></i> Min Rating</label>
                        <div class="custom-select-wrapper">
                            <select id="exploreRatingSelect" class="filter-select">
                                <option value="">Any Rating</option>
                                <option value="8.0">⭐ 8.0+ Masterpiece</option>
                                <option value="7.0">⭐ 7.0+ Great</option>
                                <option value="6.0">⭐ 6.0+ Good</option>
                                <option value="5.0">⭐ 5.0+ Average</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sort By -->
                    <div class="filter-group">
                        <label class="filter-label" for="exploreSortSelect"><i class="fa-solid fa-arrow-down-short-wide"></i> Sort By</label>
                        <div class="custom-select-wrapper">
                            <select id="exploreSortSelect" class="filter-select">
                                <option value="popularity.desc">Most Popular</option>
                                <option value="vote_average.desc">Highest Rated</option>
                                <option value="primary_release_date.desc">Newest Release</option>
                                <option value="revenue.desc">Top Box Office</option>
                            </select>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="filter-group filter-actions-group">
                        <label class="filter-label">&nbsp;</label>
                        <button class="reset-filter-btn" id="exploreResetBtn" title="Reset all filters">
                            <i class="fa-solid fa-rotate-right"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Explore Results Stats -->
            <div class="results-meta-bar">
                <span class="results-count-text" id="exploreResultsCount">Showing cinematic discoveries...</span>
                <div class="view-layout-toggle">
                    <button class="layout-btn active" id="layoutGridBtn" title="Grid View"><i class="fa-solid fa-grip"></i></button>
                    <button class="layout-btn" id="layoutCompactBtn" title="Compact View"><i class="fa-solid fa-list"></i></button>
                </div>
            </div>

            <!-- Dynamic Explore Results Grid -->
            <div class="movies-grid explore-results-grid" id="exploreResultsGrid">
                <!-- Injected dynamically -->
            </div>

            <!-- Pagination / Load More Button -->
            <div class="load-more-wrapper" id="exploreLoadMoreWrapper">
                <button class="btn btn-gradient load-more-btn" id="exploreLoadMoreBtn">
                    <i class="fa-solid fa-spinner spinner-icon" style="display: none;"></i>
                    <span>Load More Titles</span>
                    <i class="fa-solid fa-arrow-down"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================
         VIEW 3: MOVIES DEDICATED VIEW
    ======================================================== -->
    <div class="view-section" id="moviesView">
        <div class="section-container">
            <div class="page-title-banner">
                <div class="page-title-content">
                    <span class="section-badge"><i class="fa-solid fa-film"></i> Feature Films</span>
                    <h1 class="page-main-heading">All Movies</h1>
                    <p class="page-subheading">From Hollywood blockbusters to indie triumphs — discover films that captivate and inspire.</p>
                </div>
                <div class="category-tabs" id="moviesCategoryTabs">
                    <button class="cat-tab active" data-cat="popular">Popular</button>
                    <button class="cat-tab" data-cat="now_playing">Now Playing</button>
                    <button class="cat-tab" data-cat="top_rated">Top Rated</button>
                    <button class="cat-tab" data-cat="upcoming">Upcoming</button>
                </div>
            </div>

            <div class="movies-grid" id="moviesGrid">
                <!-- Populated dynamically -->
            </div>

            <div class="load-more-wrapper" id="moviesLoadMoreWrapper">
                <button class="btn btn-gradient load-more-btn" id="moviesLoadMoreBtn">
                    <span>Load More Movies</span>
                    <i class="fa-solid fa-arrow-down"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================
         VIEW 4: TV SHOWS DEDICATED VIEW
    ======================================================== -->
    <div class="view-section" id="tvView">
        <div class="section-container">
            <div class="page-title-banner">
                <div class="page-title-content">
                    <span class="section-badge"><i class="fa-solid fa-tv"></i> Television</span>
                    <h1 class="page-main-heading">TV Shows & Series</h1>
                    <p class="page-subheading">Dive into gripping dramas, hilarious comedies, epic fantasies, and world-class series.</p>
                </div>
                <div class="category-tabs" id="tvCategoryTabs">
                    <button class="cat-tab active" data-cat="popular_tv">Popular Shows</button>
                    <button class="cat-tab" data-cat="top_rated_tv">Top Rated</button>
                    <button class="cat-tab" data-cat="on_the_air_tv">Airing on TV</button>
                </div>
            </div>

            <div class="movies-grid" id="tvGrid">
                <!-- Populated dynamically -->
            </div>

            <div class="load-more-wrapper" id="tvLoadMoreWrapper">
                <button class="btn btn-gradient load-more-btn" id="tvLoadMoreBtn">
                    <span>Load More TV Series</span>
                    <i class="fa-solid fa-arrow-down"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================
         VIEW: LIVE TV 24/7 BROADCAST STREAMING VIEW
    ======================================================== -->
    <div class="view-section" id="livetvView">
        <div class="section-container">
            <!-- Live TV Header Banner -->
            <div class="livetv-header-banner">
                <div class="livetv-header-content">
                    <div class="livetv-live-badge-pill">
                        <span class="live-dot-pulse"></span>
                        <span>24/7 BROADCAST NETWORK</span>
                    </div>
                    <h1 class="page-main-heading">Live Television & World Channels</h1>
                    <p class="page-subheading">Watch free 24/7 global television channels — Blockbuster Movies, Breaking News, Live Sports, Music Concerts, Wildlife Safaris, and Cartoons powered by iptv-org.</p>
                </div>
            </div>

            <!-- Cinema Live TV Stage (Player & Channel Info) -->
            <div class="livetv-stage-card" id="liveTvStage">
                <div class="livetv-player-container">
                    <div class="livetv-player-wrapper">
                        <video id="liveTvVideoPlayer" class="livetv-video-element" controls autoplay playsinline></video>
                        <div class="livetv-player-overlay" id="liveTvPlayerOverlay" style="display: none;">
                            <div class="livetv-spinner"></div>
                            <span class="livetv-overlay-text" id="liveTvOverlayText">Connecting live broadcast...</span>
                        </div>
                        <div class="livetv-player-badges">
                            <span class="livetv-live-tag"><span class="live-inner-dot"></span> LIVE</span>
                            <span class="livetv-res-tag" id="liveTvResTag">HLS Live Stream</span>
                        </div>
                    </div>
                </div>
                
                <div class="livetv-now-playing-banner" id="liveTvNowPlayingBanner">
                    <div class="channel-main-meta">
                        <div class="channel-logo-wrap" id="currentChannelLogoWrap">
                            <i class="fa-solid fa-tower-broadcast default-tv-icon"></i>
                        </div>
                        <div class="channel-text-details">
                            <div class="channel-title-row">
                                <h2 class="current-channel-name" id="currentChannelName">Red Bull TV Live</h2>
                                <span class="channel-category-tag" id="currentChannelCategory">Sports & Action</span>
                                <span class="channel-country-tag" id="currentChannelCountry">⚡ Global</span>
                            </div>
                            <p class="current-channel-desc" id="currentChannelDesc">Formula 1, Extreme Motocross, Surfing, Cliff Diving & Action Sports 24/7 Live.</p>
                        </div>
                    </div>
                    <div class="channel-action-controls">
                        <button class="btn-channel-action" id="reloadLiveStreamBtn" title="Reconnect / Reload Stream">
                            <i class="fa-solid fa-rotate-right"></i> <span>Reload Stream</span>
                        </button>
                        <button class="btn-channel-action" id="fullscreenLiveTvBtn" title="Fullscreen Player">
                            <i class="fa-solid fa-expand"></i> <span>Fullscreen</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Directory Controls Bar (Categories & Filter Search) -->
            <div class="livetv-directory-panel">
                <div class="livetv-controls-row">
                    <!-- Category Tabs with Left/Right Scroll Arrows -->
                    <div class="livetv-tabs-wrapper">
                        <button class="livetv-tabs-nav-btn prev" id="liveTvTabsPrev" aria-label="Scroll categories left" title="Scroll left">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <div class="livetv-category-tabs" id="liveTvCategoryTabs">
                            <button class="livetv-cat-pill active" data-category="all"><i class="fa-solid fa-border-all"></i> All Channels (<span id="liveTvCountAll">44</span>)</button>
                            <button class="livetv-cat-pill" data-category="malaysia"><i class="fa-solid fa-flag"></i> 🇲🇾 Malaysia TV</button>
                            <button class="livetv-cat-pill" data-category="movies"><i class="fa-solid fa-film"></i> Movies & Cinema</button>
                            <button class="livetv-cat-pill" data-category="news"><i class="fa-solid fa-newspaper"></i> News 24/7</button>
                            <button class="livetv-cat-pill" data-category="entertainment"><i class="fa-solid fa-tv"></i> Entertainment</button>
                            <button class="livetv-cat-pill" data-category="sports"><i class="fa-solid fa-trophy"></i> Sports & Action</button>
                            <button class="livetv-cat-pill" data-category="music"><i class="fa-solid fa-music"></i> Music & Concerts</button>
                            <button class="livetv-cat-pill" data-category="documentary"><i class="fa-solid fa-earth-americas"></i> Documentary & Sci</button>
                            <button class="livetv-cat-pill" data-category="kids"><i class="fa-solid fa-shapes"></i> Kids & Cartoons</button>
                        </div>
                        <button class="livetv-tabs-nav-btn next" id="liveTvTabsNext" aria-label="Scroll categories right" title="Scroll right">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="livetv-search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="liveTvSearchInput" placeholder="Filter channels by name..." autocomplete="off">
                        <button type="button" class="livetv-clear-search" id="liveTvClearSearch" style="display: none;" aria-label="Clear channel filter">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dynamic Channels Grid -->
            <div class="livetv-grid-wrapper">
                <div class="livetv-grid" id="liveTvChannelsGrid">
                    <!-- Populated dynamically -->
                </div>
                <div class="livetv-empty-state" id="liveTvEmptyState" style="display: none;">
                    <div class="empty-icon-circle">
                        <i class="fa-solid fa-tower-broadcast"></i>
                    </div>
                    <h3>No Channels Found</h3>
                    <p>No live channels matched your filter query. Try searching for something else or pick "All Channels".</p>
                    <button class="btn btn-gradient btn-sm" id="liveTvResetFiltersBtn">
                        <i class="fa-solid fa-rotate-right"></i> Reset Channel Filters
                    </button>
                </div>
            </div>

            <!-- Load More Channels Wrapper -->
            <div class="load-more-wrapper" id="liveTvLoadMoreWrapper" style="display: none; margin-top: 2rem;">
                <button class="btn btn-gradient load-more-btn" id="liveTvLoadMoreBtn">
                    <span>Load More Channels</span>
                    <i class="fa-solid fa-arrow-down"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================
         VIEW 5: WATCHLIST VIEW
    ======================================================== -->
    <div class="view-section" id="watchlistView">
        <div class="section-container">
            <div class="watchlist-header-card">
                <div class="watchlist-header-info">
                    <span class="section-badge"><i class="fa-solid fa-bookmark"></i> Personal Library</span>
                    <h1 class="page-main-heading">My Watchlist</h1>
                    <p class="page-subheading">Your handpicked collection of movies and TV shows to stream next.</p>
                </div>
                <div class="watchlist-stats-bar">
                    <div class="stat-pill" id="watchlistCapacityPill" title="Watchlist Storage Capacity">
                        <span class="stat-number"><span id="watchlistTotalCount">0</span> / <span id="watchlistLimitCount">2</span></span>
                        <span class="stat-label">Slots</span>
                    </div>
                    <button class="btn btn-sm btn-gradient-gold expand-watchlist-btn" id="expandWatchlistBtn" onclick="App.openCoinPerksModal('rolls');" title="Expand watchlist space (50 Coins for +2 slots)">
                        <i class="fa-solid fa-folder-plus"></i> <span>+2 Space (50 🪙)</span>
                    </button>
                    <div class="watchlist-controls">
                        <div class="watchlist-filter-tabs">
                            <button class="w-tab active" data-wfilter="all">All (<span id="wCountAll">0</span>)</button>
                            <button class="w-tab" data-wfilter="movie">Movies (<span id="wCountMovies">0</span>)</button>
                            <button class="w-tab" data-wfilter="tv">TV Shows (<span id="wCountTv">0</span>)</button>
                        </div>
                        <button class="btn-clear-watchlist" id="clearWatchlistBtn" title="Clear entire watchlist">
                            <i class="fa-solid fa-trash-can"></i> Clear All
                        </button>
                    </div>
                </div>
            </div>

            <!-- Watchlist Grid -->
            <div class="movies-grid" id="watchlistGrid">
                <!-- Dynamic Items or Empty State -->
            </div>

            <!-- Empty Watchlist State -->
            <div class="empty-state-box" id="watchlistEmptyState" style="display: none;">
                <div class="empty-icon-circle">
                    <i class="fa-solid fa-film"></i>
                </div>
                <h3>Your Watchlist is Empty</h3>
                <p>You haven't saved any movies or TV series yet. Tap the bookmark icon on any title to save it for later!</p>
                <a href="#home" class="btn btn-gradient btn-lg">
                    <i class="fa-solid fa-compass"></i> Explore Trending Titles
                </a>
            </div>
        </div>
    </div>

</main>

<?php
require_once __DIR__ . '/includes/modal.php';
require_once __DIR__ . '/includes/toast.php';
require_once __DIR__ . '/includes/footer.php';
?>
