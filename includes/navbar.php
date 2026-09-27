<!-- Navbar Component -->
<header class="site-header" id="siteHeader">
    <div class="nav-container">
        <!-- Logo -->
        <a href="#home" class="brand-logo" id="brandLogo">
            <div class="logo-icon-box">
                <i class="fa-solid fa-play"></i>
            </div>
            <span class="logo-text">MOVI<span class="logo-gradient">Q</span></span>
            <span class="logo-badge">CINEMA</span>
            <span class="vip-crown-badge" id="navVipBadge" style="display: none;"><i class="fa-solid fa-crown"></i> VIP</span>
        </a>

        <!-- Desktop Navigation Links -->
        <nav class="nav-menu" id="navMenu">
            <a href="#home" class="nav-link active" data-nav="home">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </a>
            <a href="#movies" class="nav-link" data-nav="movies">
                <i class="fa-solid fa-film"></i>
                <span>Movies</span>
            </a>
            <a href="#tv" class="nav-link" data-nav="tv">
                <i class="fa-solid fa-tv"></i>
                <span>Series</span>
            </a>
            <a href="#livetv" class="nav-link nav-livetv-link" data-nav="livetv">
                <i class="fa-solid fa-tower-broadcast"></i>
                <span>Live</span>
                <span class="live-dot-beacon"></span>
            </a>
            <a href="#trending" class="nav-link" data-nav="trending">
                <i class="fa-solid fa-fire"></i>
                <span>Trending</span>
            </a>
            <a href="#explore" class="nav-link" data-nav="explore">
                <i class="fa-solid fa-compass"></i>
                <span>Explore</span>
            </a>
            <a href="#watchlist" class="nav-link nav-watchlist-link" data-nav="watchlist">
                <i class="fa-solid fa-bookmark"></i>
                <span>Watchlist</span>
                <span class="watchlist-badge" id="navWatchlistCount">0</span>
            </a>
        </nav>

        <!-- Search Bar -->
        <div class="search-wrapper" id="searchWrapper">
            <div class="search-input-box">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="globalSearchInput" placeholder="Search movies, TV shows, actors..." autocomplete="off" spellcheck="false">
                <span class="search-shortcut" title="Press Ctrl+K or / to search">
                    <kbd>Ctrl</kbd>+<kbd>K</kbd>
                </span>
                <button type="button" class="search-clear-btn" id="searchClearBtn" style="display: none;" aria-label="Clear search">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Autocomplete Results Dropdown -->
            <div class="search-autocomplete-dropdown" id="searchAutocomplete">
                <div class="autocomplete-header">
                    <span>Quick Suggestions</span>
                    <span class="search-type-toggle">
                        <button class="active" data-search-filter="multi">All</button>
                        <button data-search-filter="movie">Movies</button>
                        <button data-search-filter="tv">TV</button>
                    </span>
                </div>
                <div class="autocomplete-list" id="autocompleteList">
                    <!-- Populated dynamically -->
                </div>
                <div class="autocomplete-footer">
                    <a href="#explore" id="viewAllSearchResultsBtn">
                        <span>View all results for "<strong id="searchQueryEcho"></strong>"</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Header Actions -->
        <div class="header-actions">
            <!-- Daily Coin System & Movie Dice Quest -->
            <button class="action-btn coin-badge-btn" id="headerCoinBtn" title="Daily Movie Dice & Coins">
                <span class="coin-icon">🪙</span>
                <span class="coin-amount" id="headerCoinCount">25</span>
                <span class="coin-pulse-dot" id="coinDailyReadyDot" title="Daily Movie Dice Ready!"></span>
                <span class="btn-tooltip">Daily Dice (+25 Coins)</span>
            </button>

            <!-- Surprise Me / Random Movie -->
            <button class="action-btn icon-btn surprise-btn" id="surpriseMeBtn" title="Surprise Me! Pick a Random Gem">
                <i class="fa-solid fa-dice"></i>
                <span class="btn-tooltip">Random Movie</span>
            </button>

            <!-- Watchlist Header Button -->
            <button class="action-btn icon-btn header-watchlist-btn" id="headerWatchlistBtn" title="View Watchlist">
                <i class="fa-solid fa-bookmark"></i>
                <span class="watchlist-badge-header" id="headerWatchlistCount">0</span>
                <span class="btn-tooltip">My Watchlist</span>
            </button>

            <!-- Mobile Hamburger Toggle -->
            <button class="mobile-menu-btn" id="mobileMenuToggle" aria-label="Toggle navigation menu">
                <span class="bar bar-1"></span>
                <span class="bar bar-2"></span>
                <span class="bar bar-3"></span>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Drawer Navigation Backdrop & Drawer -->
<div class="mobile-drawer-overlay" id="mobileDrawerOverlay"></div>
<div class="mobile-drawer" id="mobileDrawer">
    <div class="mobile-drawer-header">
        <div class="brand-logo">
            <div class="logo-icon-box">
                <i class="fa-solid fa-play"></i>
            </div>
            <span class="logo-text">MOVI<span class="logo-gradient">Q</span></span>
            <span class="vip-crown-badge" id="mobileNavVipBadge" style="display: none;"><i class="fa-solid fa-crown"></i> VIP</span>
        </div>
        <button class="drawer-close-btn" id="mobileDrawerClose" aria-label="Close menu">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Mobile Coin Card Banner -->
    <div class="mobile-coin-card" id="mobileCoinCard">
        <div class="mobile-coin-left">
            <span class="coin-icon-lg">🪙</span>
            <div class="mobile-coin-info">
                <span class="mobile-coin-val"><strong id="mobileCoinCount">25</strong> Coins</span>
                <span class="mobile-coin-sub" id="mobileDiceStatusText">Daily Roll Ready!</span>
            </div>
        </div>
        <button class="btn btn-sm btn-gradient mobile-dice-btn" id="mobileDiceBtn">
            <i class="fa-solid fa-dice"></i> Roll
        </button>
    </div>

    <div class="mobile-nav-links">
        <a href="#home" class="mobile-nav-item active" data-nav="home">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <button class="mobile-nav-item mobile-surprise-item" id="mobileSurpriseBtn">
            <i class="fa-solid fa-dice"></i>
            <span>Random Movie Gem</span>
            <span class="mobile-nav-chip">🎲 Spin</span>
        </button>
        <a href="#movies" class="mobile-nav-item" data-nav="movies">
            <i class="fa-solid fa-film"></i>
            <span>Movies</span>
        </a>
        <a href="#tv" class="mobile-nav-item" data-nav="tv">
            <i class="fa-solid fa-tv"></i>
            <span>TV Shows</span>
        </a>
        <a href="#livetv" class="mobile-nav-item" data-nav="livetv">
            <i class="fa-solid fa-tower-broadcast"></i>
            <span>Live TV</span>
            <span class="mobile-live-badge">LIVE</span>
        </a>
        <a href="#trending" class="mobile-nav-item" data-nav="trending">
            <i class="fa-solid fa-fire"></i>
            <span>Trending</span>
        </a>
        <a href="#explore" class="mobile-nav-item" data-nav="explore">
            <i class="fa-solid fa-compass"></i>
            <span>Explore & Filter</span>
        </a>
        <a href="#watchlist" class="mobile-nav-item" data-nav="watchlist">
            <i class="fa-solid fa-bookmark"></i>
            <span>My Watchlist</span>
            <span class="mobile-watchlist-badge" id="mobileWatchlistCount">0</span>
        </a>
    </div>
</div>
