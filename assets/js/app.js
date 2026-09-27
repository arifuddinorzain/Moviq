/**
 * Moviq — Master Application Controller
 * Dynamic interactions, slider, SPA hash router, search, modals, and shelves.
 */

const App = {
    currentView: 'home',
    heroSlides: [],
    currentSlideIndex: 0,
    heroTimer: null,
    heroProgressInterval: null,
    genresMap: { movie: {}, tv: {} },
    exploreState: {
        type: 'movie',
        genre: '',
        year: '',
        minRating: '',
        sortBy: 'popularity.desc',
        with_original_language: '',
        page: 1,
        query: '',
        isLoading: false,
        totalPages: 1
    },
    moviesState: { category: 'popular', page: 1, isLoading: false },
    tvState: { category: 'popular_tv', page: 1, isLoading: false },
    liveTvState: {
        category: 'all',
        search: '',
        currentChannel: null,
        hlsInstance: null,
        channelsList: [],
        displayedCount: 8,
        pageSize: 8,
        isLoading: false
    },
    watchlistFilter: 'all',

    /**
     * Application Initialization
     */
    async init() {
        this.isWatchPage = window.location.pathname.includes('watch.php') || document.getElementById('watchPageMain') !== null;
        this.bindEvents();
        this.initWatchlistSync();
        this.initCoinSystem();
        this.initVipState();
        await this.loadGenres();

        if (!this.isWatchPage) {
            this.initRouter();
            this.loadHeroSlider();
            this.loadHomeShelves();
        }
    },

    /**
     * Bind all global DOM events and keyboard shortcuts
     */
    bindEvents() {
        // Window scroll for sticky header elevation
        window.addEventListener('scroll', () => {
            const header = document.getElementById('siteHeader');
            if (header) {
                if (window.scrollY > 40) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }
        });

        // Hash Change Listener (SPA Routing)
        window.addEventListener('hashchange', () => this.handleRouting());

        // Keyboard Shortcuts: Ctrl+K or / to search, Esc to close modals
        window.addEventListener('keydown', (e) => {
            if ((e.ctrlKey && e.key.toLowerCase() === 'k') || (e.key === '/' && document.activeElement.tagName !== 'INPUT')) {
                e.preventDefault();
                const searchInput = document.getElementById('globalSearchInput');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });

        // Search Input & Autocomplete
        const searchInput = document.getElementById('globalSearchInput');
        const searchClearBtn = document.getElementById('searchClearBtn');
        let searchDebounce = null;

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const q = e.target.value.trim();
                if (searchClearBtn) {
                    searchClearBtn.style.display = q.length > 0 ? 'block' : 'none';
                }
                clearTimeout(searchDebounce);
                if (q.length >= 2) {
                    searchDebounce = setTimeout(() => this.handleAutocomplete(q), 280);
                } else {
                    this.closeAutocomplete();
                }
            });

            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    const q = searchInput.value.trim();
                    if (q.length > 0) {
                        this.closeAutocomplete();
                        window.location.hash = `#explore?search=${encodeURIComponent(q)}`;
                    }
                }
            });
        }

        if (searchClearBtn) {
            searchClearBtn.addEventListener('click', () => {
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
                searchClearBtn.style.display = 'none';
                this.closeAutocomplete();
            });
        }

        // Close Autocomplete when clicking outside
        document.addEventListener('click', (e) => {
            const searchWrapper = document.getElementById('searchWrapper');
            if (searchWrapper && !searchWrapper.contains(e.target)) {
                this.closeAutocomplete();
            }
        });

        // Autocomplete Search Type Toggle
        document.querySelectorAll('.search-type-toggle button').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.search-type-toggle button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                if (searchInput && searchInput.value.trim().length >= 2) {
                    this.handleAutocomplete(searchInput.value.trim());
                }
            });
        });

        // View All Results button inside autocomplete
        const viewAllBtn = document.getElementById('viewAllSearchResultsBtn');
        if (viewAllBtn) {
            viewAllBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const q = searchInput ? searchInput.value.trim() : '';
                if (q) {
                    this.closeAutocomplete();
                    window.location.hash = `#explore?search=${encodeURIComponent(q)}`;
                }
            });
        }

        // Mobile Menu Drawer & Overlay
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const mobileDrawer = document.getElementById('mobileDrawer');
        const mobileDrawerOverlay = document.getElementById('mobileDrawerOverlay');
        const mobileClose = document.getElementById('mobileDrawerClose');

        const openMobileDrawer = () => {
            if (mobileDrawer) mobileDrawer.classList.add('open');
            if (mobileDrawerOverlay) mobileDrawerOverlay.classList.add('open');
            document.body.classList.add('drawer-open');
        };

        const closeMobileDrawer = () => {
            if (mobileDrawer) mobileDrawer.classList.remove('open');
            if (mobileDrawerOverlay) mobileDrawerOverlay.classList.remove('open');
            document.body.classList.remove('drawer-open');
        };

        if (mobileToggle) mobileToggle.addEventListener('click', openMobileDrawer);
        if (mobileClose) mobileClose.addEventListener('click', closeMobileDrawer);
        if (mobileDrawerOverlay) mobileDrawerOverlay.addEventListener('click', closeMobileDrawer);
        document.querySelectorAll('.mobile-nav-item').forEach(item => {
            item.addEventListener('click', closeMobileDrawer);
        });

        // Header Watchlist Button
        const headerWatchlistBtn = document.getElementById('headerWatchlistBtn');
        if (headerWatchlistBtn) {
            headerWatchlistBtn.addEventListener('click', () => {
                window.location.hash = '#watchlist';
            });
        }

        // Surprise Me / Random Gem Mystery Box Button
        const surpriseBtn = document.getElementById('surpriseMeBtn');
        if (surpriseBtn) {
            surpriseBtn.addEventListener('click', () => this.openSurpriseMysteryModal());
        }

        const mobileSurpriseBtn = document.getElementById('mobileSurpriseBtn');
        if (mobileSurpriseBtn) {
            mobileSurpriseBtn.addEventListener('click', () => {
                const mobileDrawer = document.getElementById('mobileDrawer');
                const mobileOverlay = document.getElementById('mobileDrawerOverlay');
                if (mobileDrawer) mobileDrawer.classList.remove('open');
                if (mobileOverlay) mobileOverlay.classList.remove('open');
                document.body.classList.remove('drawer-open');
                this.openSurpriseMysteryModal();
            });
        }

        const surpriseMysteryCloseBtn = document.getElementById('surpriseMysteryCloseBtn');
        const surpriseMysteryOverlay = document.getElementById('surpriseMysteryOverlay');
        if (surpriseMysteryCloseBtn) surpriseMysteryCloseBtn.addEventListener('click', () => this.closeSurpriseMysteryModal());
        if (surpriseMysteryOverlay) {
            surpriseMysteryOverlay.addEventListener('click', (e) => {
                if (e.target === surpriseMysteryOverlay) this.closeSurpriseMysteryModal();
            });
        }

        const mysteryRerollBtn = document.getElementById('mysteryRerollBtn');
        if (mysteryRerollBtn) {
            mysteryRerollBtn.addEventListener('click', () => this.rollSurpriseMovie());
        }

        // Back to Top Button
        const backToTopBtn = document.getElementById('backToTopBtn');
        if (backToTopBtn) {
            backToTopBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // Shelf Navigation Arrows (Scroll buttons)
        document.querySelectorAll('.shelf-nav-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.dataset.target;
                const row = document.getElementById(targetId);
                if (row) {
                    const scrollAmount = row.clientWidth * 0.75;
                    if (btn.classList.contains('prev')) {
                        row.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                    } else {
                        row.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                    }
                }
            });
        });

        // Hero Slider Controls
        const heroPrev = document.getElementById('heroPrevBtn');
        const heroNext = document.getElementById('heroNextBtn');
        if (heroPrev) heroPrev.addEventListener('click', () => this.prevSlide());
        if (heroNext) heroNext.addEventListener('click', () => this.nextSlide());

        // Quick Genre Ribbon Pills on Home
        document.querySelectorAll('#genreQuickPills .genre-pill').forEach(pill => {
            pill.addEventListener('click', () => {
                document.querySelectorAll('#genreQuickPills .genre-pill').forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                pill.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                const genreId = pill.dataset.genreId;
                if (genreId === 'all') {
                    // Default view
                } else if (genreId === 'korean') {
                    window.location.hash = `#explore?with_original_language=ko`;
                } else {
                    window.location.hash = `#explore?genre=${genreId}`;
                }
            });
        });

        // Genre Spotlight Tabs on Home
        document.querySelectorAll('#spotlightTabs .spotlight-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('#spotlightTabs .spotlight-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const genreId = tab.dataset.genreId;
                this.loadSpotlightGenre(genreId);
            });
        });

        // Explore Filters Listeners
        const exploreTypeBtns = document.querySelectorAll('#exploreMediaType .segment-btn');
        exploreTypeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                exploreTypeBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.exploreState.type = btn.dataset.value;
                this.populateExploreGenreSelect();
                this.applyExploreFilters(true);
            });
        });

        const exploreGenreSelect = document.getElementById('exploreGenreSelect');
        const exploreYearSelect = document.getElementById('exploreYearSelect');
        const exploreRatingSelect = document.getElementById('exploreRatingSelect');
        const exploreSortSelect = document.getElementById('exploreSortSelect');
        const exploreResetBtn = document.getElementById('exploreResetBtn');
        const exploreLoadMoreBtn = document.getElementById('exploreLoadMoreBtn');

        if (exploreGenreSelect) exploreGenreSelect.addEventListener('change', () => this.applyExploreFilters(true));
        if (exploreYearSelect) exploreYearSelect.addEventListener('change', () => this.applyExploreFilters(true));
        if (exploreRatingSelect) exploreRatingSelect.addEventListener('change', () => this.applyExploreFilters(true));
        if (exploreSortSelect) exploreSortSelect.addEventListener('change', () => this.applyExploreFilters(true));

        if (exploreResetBtn) {
            exploreResetBtn.addEventListener('click', () => {
                if (exploreGenreSelect) exploreGenreSelect.value = '';
                if (exploreYearSelect) exploreYearSelect.value = '';
                if (exploreRatingSelect) exploreRatingSelect.value = '';
                if (exploreSortSelect) exploreSortSelect.value = 'popularity.desc';
                this.exploreState.query = '';
                this.applyExploreFilters(true);
                this.showToast('Filters reset to default', 'info');
            });
        }

        if (exploreLoadMoreBtn) {
            exploreLoadMoreBtn.addEventListener('click', () => {
                this.exploreState.page++;
                this.fetchExploreResults(false);
            });
        }

        // Explore Layout Grid/Compact switch
        const gridBtn = document.getElementById('layoutGridBtn');
        const compactBtn = document.getElementById('layoutCompactBtn');
        const exploreGrid = document.getElementById('exploreResultsGrid');
        if (gridBtn && compactBtn && exploreGrid) {
            gridBtn.addEventListener('click', () => {
                gridBtn.classList.add('active');
                compactBtn.classList.remove('active');
                exploreGrid.classList.remove('compact-mode');
            });
            compactBtn.addEventListener('click', () => {
                compactBtn.classList.add('active');
                gridBtn.classList.remove('active');
                exploreGrid.classList.add('compact-mode');
            });
        }

        // Movies View Category Tabs
        document.querySelectorAll('#moviesCategoryTabs .cat-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('#moviesCategoryTabs .cat-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                this.moviesState.category = tab.dataset.cat;
                this.moviesState.page = 1;
                this.loadMoviesView(true);
            });
        });
        const moviesLoadMoreBtn = document.getElementById('moviesLoadMoreBtn');
        if (moviesLoadMoreBtn) {
            moviesLoadMoreBtn.addEventListener('click', () => {
                this.moviesState.page++;
                this.loadMoviesView(false);
            });
        }

        // TV View Category Tabs
        document.querySelectorAll('#tvCategoryTabs .cat-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('#tvCategoryTabs .cat-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                this.tvState.category = tab.dataset.cat;
                this.tvState.page = 1;
                this.loadTvView(true);
            });
        });
        const tvLoadMoreBtn = document.getElementById('tvLoadMoreBtn');
        if (tvLoadMoreBtn) {
            tvLoadMoreBtn.addEventListener('click', () => {
                this.tvState.page++;
                this.loadTvView(false);
            });
        }

        // Live TV Controls Listeners
        document.querySelectorAll('#liveTvCategoryTabs .livetv-cat-pill').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('#liveTvCategoryTabs .livetv-cat-pill').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                tab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                this.liveTvState.category = tab.dataset.category;
                this.loadLiveTvView();
            });
        });

        // Horizontal Tabs & Ribbon Scroll Controls (Live TV & Home Quick Genres)
        this.setupTabsScroll('liveTvCategoryTabs', 'liveTvTabsPrev', 'liveTvTabsNext', 260);
        this.setupTabsScroll('genreQuickPills', 'genreRibbonPrev', 'genreRibbonNext', 240);

        const liveTvSearchInput = document.getElementById('liveTvSearchInput');
        const liveTvClearSearch = document.getElementById('liveTvClearSearch');
        let liveTvSearchDebounce = null;
        if (liveTvSearchInput) {
            liveTvSearchInput.addEventListener('input', (e) => {
                const val = e.target.value.trim();
                if (liveTvClearSearch) liveTvClearSearch.style.display = val.length > 0 ? 'block' : 'none';
                clearTimeout(liveTvSearchDebounce);
                liveTvSearchDebounce = setTimeout(() => {
                    this.liveTvState.search = val;
                    this.loadLiveTvView();
                }, 250);
            });
        }

        if (liveTvClearSearch) {
            liveTvClearSearch.addEventListener('click', () => {
                if (liveTvSearchInput) {
                    liveTvSearchInput.value = '';
                    liveTvSearchInput.focus();
                }
                liveTvClearSearch.style.display = 'none';
                this.liveTvState.search = '';
                this.loadLiveTvView();
            });
        }

        const liveTvResetFiltersBtn = document.getElementById('liveTvResetFiltersBtn');
        if (liveTvResetFiltersBtn) {
            liveTvResetFiltersBtn.addEventListener('click', () => {
                this.liveTvState.category = 'all';
                this.liveTvState.search = '';
                if (liveTvSearchInput) liveTvSearchInput.value = '';
                if (liveTvClearSearch) liveTvClearSearch.style.display = 'none';
                document.querySelectorAll('#liveTvCategoryTabs .livetv-cat-pill').forEach(t => {
                    t.classList.toggle('active', t.dataset.category === 'all');
                });
                this.loadLiveTvView();
            });
        }

        const reloadLiveStreamBtn = document.getElementById('reloadLiveStreamBtn');
        if (reloadLiveStreamBtn) {
            reloadLiveStreamBtn.addEventListener('click', () => this.reloadCurrentLiveStream());
        }

        const fullscreenLiveTvBtn = document.getElementById('fullscreenLiveTvBtn');
        if (fullscreenLiveTvBtn) {
            fullscreenLiveTvBtn.addEventListener('click', () => this.toggleLiveTvFullscreen());
        }

        const liveTvLoadMoreBtn = document.getElementById('liveTvLoadMoreBtn');
        if (liveTvLoadMoreBtn) {
            liveTvLoadMoreBtn.addEventListener('click', () => this.loadMoreLiveChannels());
        }

        // Watchlist View Filter Tabs
        document.querySelectorAll('.watchlist-filter-tabs .w-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.watchlist-filter-tabs .w-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                this.watchlistFilter = tab.dataset.wfilter;
                this.renderWatchlistView();
            });
        });

        // Clear Watchlist Button
        const clearWatchlistBtn = document.getElementById('clearWatchlistBtn');
        if (clearWatchlistBtn) {
            clearWatchlistBtn.addEventListener('click', () => {
                if (confirm('Are you sure you want to clear your entire watchlist?')) {
                    Watchlist.clear();
                    this.showToast('Watchlist cleared', 'remove');
                }
            });
        }

        // Modals Close Events
        const detailsCloseBtn = document.getElementById('detailsModalCloseBtn');
        const detailsOverlay = document.getElementById('detailsModalOverlay');
        if (detailsCloseBtn) detailsCloseBtn.addEventListener('click', () => this.closeDetailsModal());
        if (detailsOverlay) {
            detailsOverlay.addEventListener('click', (e) => {
                if (e.target === detailsOverlay) this.closeDetailsModal();
            });
        }

        const trailerCloseBtn = document.getElementById('trailerModalCloseBtn');
        const trailerOverlay = document.getElementById('trailerModalOverlay');
        if (trailerCloseBtn) trailerCloseBtn.addEventListener('click', () => this.closeTrailerModal());
        if (trailerOverlay) {
            trailerOverlay.addEventListener('click', (e) => {
                if (e.target === trailerOverlay) this.closeTrailerModal();
            });
        }

        // Daily Coin & Movie Dice Modal Trigger
        const headerCoinBtn = document.getElementById('headerCoinBtn');
        const mobileDiceBtn = document.getElementById('mobileDiceBtn');
        const mobileCoinCard = document.getElementById('mobileCoinCard');
        if (headerCoinBtn) headerCoinBtn.addEventListener('click', () => this.openDailyDiceModal());
        if (mobileDiceBtn) mobileDiceBtn.addEventListener('click', () => {
            closeMobileDrawer();
            this.openDailyDiceModal();
        });
        if (mobileCoinCard) mobileCoinCard.addEventListener('click', (e) => {
            if (e.target.closest('#mobileDiceBtn')) return;
            closeMobileDrawer();
            this.openDailyDiceModal();
        });

        const diceModalCloseBtn = document.getElementById('dailyDiceModalCloseBtn');
        const diceModalOverlay = document.getElementById('dailyDiceModalOverlay');
        if (diceModalCloseBtn) diceModalCloseBtn.addEventListener('click', () => this.closeDailyDiceModal());
        if (diceModalOverlay) {
            diceModalOverlay.addEventListener('click', (e) => {
                if (e.target === diceModalOverlay) this.closeDailyDiceModal();
            });
        }

        const rollMovieDiceBtn = document.getElementById('rollMovieDiceBtn');
        if (rollMovieDiceBtn) {
            rollMovieDiceBtn.addEventListener('click', () => this.handleRollMovieDice());
        }

        // Coin Perks Shop Triggers & Tab listeners
        const perkVipBtn = document.getElementById('perkVipBtn');
        const perkLuckyBtn = document.getElementById('perkLuckyBtn');
        const perkStreakBtn = document.getElementById('perkStreakBtn');
        if (perkVipBtn) perkVipBtn.addEventListener('click', () => this.openCoinPerksModal('vip'));
        if (perkLuckyBtn) perkLuckyBtn.addEventListener('click', () => this.openCoinPerksModal('rolls'));
        if (perkStreakBtn) perkStreakBtn.addEventListener('click', () => this.openCoinPerksModal('ranks'));

        const coinPerksModalCloseBtn = document.getElementById('coinPerksModalCloseBtn');
        const coinPerksModalOverlay = document.getElementById('coinPerksModalOverlay');
        if (coinPerksModalCloseBtn) coinPerksModalCloseBtn.addEventListener('click', () => this.closeCoinPerksModal());
        if (coinPerksModalOverlay) {
            coinPerksModalOverlay.addEventListener('click', (e) => {
                if (e.target === coinPerksModalOverlay) this.closeCoinPerksModal();
            });
        }

        document.querySelectorAll('#perksShopTabs .perks-tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('#perksShopTabs .perks-tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.perk-tab-content').forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                const targetId = `ptab-${btn.dataset.ptab}`;
                const targetContent = document.getElementById(targetId);
                if (targetContent) targetContent.classList.add('active');
            });
        });

        const buyVipBtn = document.getElementById('buyVipBtn');
        const demoVipBtn = document.getElementById('demoVipBtn');
        const buyExtraRollBtn = document.getElementById('buyExtraRollBtn');
        const openMysteryBoxBtn = document.getElementById('openMysteryBoxBtn');
        const buyWatchlistSlotsBtn = document.getElementById('buyWatchlistSlotsBtn');

        if (buyVipBtn) buyVipBtn.addEventListener('click', () => this.handleBuyVip());
        if (demoVipBtn) demoVipBtn.addEventListener('click', () => this.handleDemoVip());
        if (buyExtraRollBtn) buyExtraRollBtn.addEventListener('click', () => this.handleBuyExtraRoll());
        if (openMysteryBoxBtn) openMysteryBoxBtn.addEventListener('click', () => this.handleOpenMysteryBox());
        if (buyWatchlistSlotsBtn) buyWatchlistSlotsBtn.addEventListener('click', () => this.handleBuyWatchlistSlots());

        const personCloseBtn = document.getElementById('personModalCloseBtn');
        const personOverlay = document.getElementById('personModalOverlay');
        if (personCloseBtn) personCloseBtn.addEventListener('click', () => this.closePersonModal());
        if (personOverlay) {
            personOverlay.addEventListener('click', (e) => {
                if (e.target === personOverlay) this.closePersonModal();
            });
        }
    },

    /**
     * Initialize Watchlist Synchronizer
     */
    initWatchlistSync() {
        const updateBadges = () => {
            const counts = Watchlist.getCounts();
            const navBadge = document.getElementById('navWatchlistCount');
            const headerBadge = document.getElementById('headerWatchlistCount');
            const mobileBadge = document.getElementById('mobileWatchlistCount');
            const totalCount = document.getElementById('watchlistTotalCount');
            const limitCount = document.getElementById('watchlistLimitCount');
            const shopCapacity = document.getElementById('shopWatchlistCapacity');
            const countAll = document.getElementById('wCountAll');
            const countMovies = document.getElementById('wCountMovies');
            const countTv = document.getElementById('wCountTv');

            if (navBadge) navBadge.textContent = counts.total;
            if (headerBadge) headerBadge.textContent = counts.total;
            if (mobileBadge) mobileBadge.textContent = counts.total;
            if (totalCount) totalCount.textContent = counts.total;
            if (limitCount) limitCount.textContent = counts.limit;
            if (shopCapacity) shopCapacity.textContent = counts.limit;
            if (countAll) countAll.textContent = counts.total;
            if (countMovies) countMovies.textContent = counts.movies;
            if (countTv) countTv.textContent = counts.tv;

            // Update all active bookmark icons across visible cards
            document.querySelectorAll('.card-bookmark-btn').forEach(btn => {
                const id = btn.dataset.id;
                const type = btn.dataset.type || 'movie';
                const isActive = Watchlist.has(id, type);
                btn.classList.toggle('active', isActive);
                btn.innerHTML = isActive ? '<i class="fa-solid fa-check"></i>' : '<i class="fa-solid fa-bookmark"></i>';
            });
        };

        Watchlist.subscribe(() => {
            updateBadges();
            if (this.currentView === 'watchlist') {
                this.renderWatchlistView();
            }
        });

        updateBadges();
    },

    /**
     * Load Genre definitions and build maps
     */
    async loadGenres() {
        const genres = await Api.getGenres();
        if (genres) {
            if (genres.movie) {
                genres.movie.forEach(g => this.genresMap.movie[g.id] = g.name);
            }
            if (genres.tv) {
                genres.tv.forEach(g => this.genresMap.tv[g.id] = g.name);
            }
        }
        this.populateExploreGenreSelect();
    },

    /**
     * Populate Explore Genre Select options based on active media type
     */
    populateExploreGenreSelect() {
        const select = document.getElementById('exploreGenreSelect');
        if (!select) return;
        const currentType = this.exploreState.type || 'movie';
        const genres = this.genresMap[currentType] || {};

        select.innerHTML = '<option value="">All Genres</option>';
        Object.entries(genres).forEach(([id, name]) => {
            const opt = document.createElement('option');
            opt.value = id;
            opt.textContent = name;
            select.appendChild(opt);
        });
    },

    /**
     * SPA Hash Router
     */
    initRouter() {
        if (!window.location.hash) {
            window.location.hash = '#home';
        } else {
            this.handleRouting();
        }
    },

    handleRouting() {
        if (this.isWatchPage) {
            const explicitHash = window.location.hash.substring(1);
            if (explicitHash && ['home', 'movies', 'tv', 'livetv', 'trending', 'explore', 'watchlist'].includes(explicitHash)) {
                window.location.href = 'index.php#' + explicitHash;
            }
            return;
        }

        const hash = window.location.hash.substring(1) || 'home';
        const [route, queryString] = hash.split('?');
        const params = new URLSearchParams(queryString || '');

        // Check if direct movie/tv link: e.g. #movie/550 or #tv/1399
        if (route.startsWith('movie/') || route.startsWith('tv/')) {
            const [type, id] = route.split('/');
            this.switchView('home');
            this.openDetails(id, type);
            return;
        }

        switch (route) {
            case 'home':
                this.switchView('home');
                break;
            case 'movies':
                this.switchView('movies');
                this.loadMoviesView(true);
                break;
            case 'tv':
                this.switchView('tv');
                this.loadTvView(true);
                break;
            case 'livetv':
                this.switchView('livetv');
                this.loadLiveTvView();
                break;
            case 'trending':
                this.switchView('explore');
                this.exploreState.sortBy = 'popularity.desc';
                this.applyExploreFilters(true);
                break;
            case 'explore':
                this.switchView('explore');
                if (params.has('search')) {
                    const q = decodeURIComponent(params.get('search'));
                    this.exploreState.query = q;
                    this.exploreState.with_original_language = '';
                    const searchInput = document.getElementById('globalSearchInput');
                    if (searchInput) searchInput.value = q;
                    const mainTitle = document.getElementById('exploreMainTitle');
                    if (mainTitle) mainTitle.textContent = `Search Results for "${q}"`;
                } else if (params.has('with_original_language')) {
                    const lang = params.get('with_original_language');
                    this.exploreState.with_original_language = lang;
                    this.exploreState.query = '';
                    this.exploreState.genre = '';
                    const mainTitle = document.getElementById('exploreMainTitle');
                    if (mainTitle && lang === 'ko') mainTitle.textContent = `Explore Korean Movies & Dramas`;
                } else if (params.has('genre')) {
                    const g = params.get('genre');
                    this.exploreState.genre = g;
                    this.exploreState.with_original_language = '';
                    const genreSelect = document.getElementById('exploreGenreSelect');
                    if (genreSelect) genreSelect.value = g;
                } else {
                    this.exploreState.with_original_language = '';
                }
                this.applyExploreFilters(true);
                break;
            case 'watchlist':
                this.switchView('watchlist');
                this.renderWatchlistView();
                break;
            default:
                this.switchView('home');
                break;
        }
    },

    switchView(viewName) {
        if (this.currentView === 'livetv' && viewName !== 'livetv') {
            const video = document.getElementById('liveTvVideoPlayer');
            if (video) video.pause();
        }

        this.currentView = viewName;

        // Hide all views, activate target
        document.querySelectorAll('.view-section').forEach(sec => sec.classList.remove('active'));
        const targetView = document.getElementById(`${viewName}View`);
        if (targetView) {
            targetView.classList.add('active');
        }

        // Update nav active states
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.toggle('active', link.dataset.nav === viewName);
        });
        document.querySelectorAll('.mobile-nav-item').forEach(item => {
            item.classList.toggle('active', item.dataset.nav === viewName);
        });

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Update scroll indicators for visible tabs
        if (viewName === 'livetv' || viewName === 'home') {
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 150);
        }
    },

    /**
     * Setup smooth horizontal scrolling and dynamic button states for tab ribbons
     */
    setupTabsScroll(containerId, prevBtnId, nextBtnId, scrollStep = 240) {
        const container = document.getElementById(containerId);
        const prevBtn = document.getElementById(prevBtnId);
        const nextBtn = document.getElementById(nextBtnId);
        if (!container || !prevBtn || !nextBtn) return;

        const updateBtnStates = () => {
            const maxScroll = Math.max(0, container.scrollWidth - container.clientWidth);
            const scrollLeft = container.scrollLeft;

            if (maxScroll <= 4) {
                prevBtn.classList.add('is-disabled');
                nextBtn.classList.add('is-disabled');
                return;
            }

            prevBtn.classList.toggle('is-disabled', scrollLeft <= 3);
            nextBtn.classList.toggle('is-disabled', scrollLeft >= maxScroll - 3);
        };

        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            container.scrollBy({ left: -scrollStep, behavior: 'smooth' });
            setTimeout(updateBtnStates, 320);
        });

        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            container.scrollBy({ left: scrollStep, behavior: 'smooth' });
            setTimeout(updateBtnStates, 320);
        });

        container.addEventListener('scroll', updateBtnStates, { passive: true });
        window.addEventListener('resize', updateBtnStates, { passive: true });

        requestAnimationFrame(updateBtnStates);
        setTimeout(updateBtnStates, 200);
        setTimeout(updateBtnStates, 600);
    },

    /**
     * Hero Slider Engine
     */
    async loadHeroSlider() {
        const slides = await Api.getHeroSlides();
        const skeleton = document.getElementById('heroSkeleton');
        const wrapper = document.getElementById('heroSlidesWrapper');
        const indicators = document.getElementById('heroIndicators');

        if (!slides || slides.length === 0) return;
        this.heroSlides = slides;

        if (skeleton) skeleton.style.display = 'none';
        if (wrapper) wrapper.innerHTML = '';
        if (indicators) indicators.innerHTML = '';

        slides.forEach((slide, index) => {
            const slideEl = document.createElement('div');
            slideEl.className = `hero-slide ${index === 0 ? 'active' : ''}`;
            slideEl.dataset.index = index;

            const releaseYear = Api.formatYear(slide.release_date || slide.first_air_date);
            const rating = slide.vote_average ? Number(slide.vote_average).toFixed(1) : 'N/A';
            const genreNames = (slide.genre_ids || []).slice(0, 3).map(id => this.genresMap.movie[id] || 'Cinema').filter(Boolean);

            slideEl.innerHTML = `
                <img class="hero-backdrop-img" src="${slide.backdrop_url || Api.getImageUrl(slide.backdrop_path, 'original')}" alt="${slide.title || 'Movie backdrop'}" loading="${index === 0 ? 'eager' : 'lazy'}">
                <div class="hero-overlay-gradient"></div>
                <div class="hero-content">
                    <div class="hero-badge-row">
                        <span class="hero-pill trending-pill"><i class="fa-solid fa-fire"></i> #1 Trending</span>
                        <span class="hero-pill rating-pill"><i class="fa-solid fa-star"></i> ${rating} Rating</span>
                        <span class="hero-pill quality-pill"><i class="fa-solid fa-film"></i> 4K Ultra HD</span>
                    </div>
                    <h1 class="hero-title">${slide.title || slide.name}</h1>
                    <div class="hero-meta-row">
                        <div class="hero-meta-item"><i class="fa-solid fa-calendar"></i> ${releaseYear}</div>
                        <div class="hero-genres-list">
                            ${genreNames.map(g => `<span class="hero-genre-tag">${g}</span>`).join('')}
                        </div>
                    </div>
                    <p class="hero-description">${slide.overview || 'Experience the cinematic spectacle of this worldwide trending blockbuster.'}</p>
                    <div class="hero-cta-group">
                        <a href="watch.php?id=${slide.id}&type=movie" class="btn btn-gradient hero-watch-btn">
                            <i class="fa-solid fa-play"></i> <span>Watch<span class="btn-text-extra"> Movie</span></span>
                        </a>
                        <button class="btn btn-secondary hero-trailer-btn" data-key="${slide.trailer_key || ''}" data-title="${(slide.title || slide.name || '').replace(/"/g, '&quot;')}">
                            <i class="fa-solid fa-film"></i> <span><span class="btn-text-extra">Watch </span>Trailer</span>
                        </button>
                        <button class="btn btn-secondary btn-icon-only hero-details-btn" data-id="${slide.id}" data-type="movie" title="More Details" aria-label="More Details">
                            <i class="fa-solid fa-circle-info"></i>
                        </button>
                        <button class="btn btn-secondary btn-icon-only hero-bookmark-btn ${Watchlist.has(slide.id, 'movie') ? 'active' : ''}" data-id="${slide.id}" data-type="movie" title="Save to Watchlist" aria-label="Save to Watchlist">
                            <i class="${Watchlist.has(slide.id, 'movie') ? 'fa-solid fa-check' : 'fa-solid fa-bookmark'}"></i>
                        </button>
                    </div>
                </div>
            `;

            wrapper.appendChild(slideEl);

            // Dot Indicator
            const dot = document.createElement('div');
            dot.className = `hero-indicator-dot ${index === 0 ? 'active' : ''}`;
            dot.addEventListener('click', () => this.goToSlide(index));
            indicators.appendChild(dot);
        });

        // Bind CTA buttons on Hero
        wrapper.querySelectorAll('.hero-trailer-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const trailerKey = btn.dataset.key;
                const title = btn.dataset.title || 'Official Trailer';
                if (trailerKey) {
                    this.openTrailerModal(trailerKey, `${title} — Official Trailer`);
                } else {
                    this.showToast('Trailer not available for this title', 'info');
                }
            });
        });

        wrapper.querySelectorAll('.hero-details-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.openDetails(btn.dataset.id, btn.dataset.type);
            });
        });

        wrapper.querySelectorAll('.hero-bookmark-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = btn.dataset.id;
                const slide = this.heroSlides.find(s => String(s.id) === String(id));
                if (slide) {
                    const res = Watchlist.toggle(slide);
                    if (res.limitReached) {
                        this.showToast(`⚠️ Watchlist full (${res.count}/${res.limit} slots)! Buy +2 space for 50 Coins in Rewards Shop.`, 'info');
                        return;
                    }
                    btn.classList.toggle('active', res.added);
                    btn.innerHTML = res.added ? '<i class="fa-solid fa-check"></i>' : '<i class="fa-solid fa-bookmark"></i>';
                    this.showToast(res.added ? `Added "${slide.title}" to Watchlist` : `Removed "${slide.title}" from Watchlist`, res.added ? 'success' : 'remove');
                }
            });
        });

        // Start Auto Play Timer
        this.startHeroTimer();

        // Pause on Hover
        const heroSection = document.getElementById('heroSection');
        if (heroSection) {
            heroSection.addEventListener('mouseenter', () => this.pauseHeroTimer());
            heroSection.addEventListener('mouseleave', () => this.startHeroTimer());
        }
    },

    goToSlide(index) {
        if (!this.heroSlides || this.heroSlides.length === 0) return;
        this.currentSlideIndex = (index + this.heroSlides.length) % this.heroSlides.length;

        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.hero-indicator-dot');

        slides.forEach((s, idx) => s.classList.toggle('active', idx === this.currentSlideIndex));
        dots.forEach((d, idx) => d.classList.toggle('active', idx === this.currentSlideIndex));

        this.resetHeroProgressBar();
    },

    nextSlide() {
        this.goToSlide(this.currentSlideIndex + 1);
    },

    prevSlide() {
        this.goToSlide(this.currentSlideIndex - 1);
    },

    startHeroTimer() {
        this.pauseHeroTimer();
        const duration = 6000;
        const step = 50;
        let elapsed = 0;
        const progressBar = document.getElementById('heroProgressBar');

        this.heroProgressInterval = setInterval(() => {
            elapsed += step;
            const pct = Math.min(100, (elapsed / duration) * 100);
            if (progressBar) progressBar.style.width = `${pct}%`;

            if (elapsed >= duration) {
                this.nextSlide();
                elapsed = 0;
            }
        }, step);
    },

    pauseHeroTimer() {
        if (this.heroProgressInterval) clearInterval(this.heroProgressInterval);
    },

    resetHeroProgressBar() {
        const progressBar = document.getElementById('heroProgressBar');
        if (progressBar) progressBar.style.width = '0%';
    },

    /**
     * Load Home Rows & Shelves
     */
    async loadHomeShelves() {
        // Shelf 1: Trending Top 10 (Rank Numbered)
        this.loadTrendingShelf();

        // Shelf 2: Now Playing
        this.loadShelfRow('nowPlayingShelfRow', () => Api.getNowPlaying(1), 'movie');

        // Shelf 3: Top Rated
        this.loadShelfRow('topRatedShelfRow', () => Api.getTopRatedMovies(1), 'movie');

        // Shelf 4: Popular TV
        this.loadShelfRow('popularTvShelfRow', () => Api.getPopularTv(1), 'tv');

        // Shelf 5: Upcoming
        this.loadShelfRow('upcomingShelfRow', () => Api.getUpcoming(1), 'movie');

        // Shelf 6: Trending Korean Movies
        this.loadShelfRow('koreanShelfRow', () => Api.getKoreanMovies(1), 'movie');

        // Genre Spotlight
        this.loadSpotlightGenre(28); // Action by default
    },

    async loadTrendingShelf() {
        const data = await Api.getTrending('all', 'day', 1);
        const row = document.getElementById('trendingShelfRow');
        if (!data || !data.results || !row) return;

        row.innerHTML = '';
        const top10 = data.results.slice(0, 10);

        top10.forEach((item, index) => {
            const rankWrapper = document.createElement('div');
            rankWrapper.className = 'rank-card-wrapper';
            rankWrapper.innerHTML = `
                <div class="rank-number">${index + 1}</div>
            `;
            const card = this.createMovieCard(item, { isRanked: true });
            rankWrapper.appendChild(card);
            row.appendChild(rankWrapper);
        });

        this.enableDragToScroll(row);
    },

    async loadShelfRow(rowId, fetcherFn, defaultType = 'movie') {
        const row = document.getElementById(rowId);
        if (!row) return;

        const data = await fetcherFn();
        if (!data || !data.results) return;

        row.innerHTML = '';
        data.results.forEach(item => {
            if (!item.media_type) item.media_type = defaultType;
            const card = this.createMovieCard(item);
            row.appendChild(card);
        });

        this.enableDragToScroll(row);
    },

    /**
     * Enable smooth touch swipe & mouse drag-to-slide on horizontal shelves
     */
    enableDragToScroll(container) {
        if (!container || container.dataset.dragEnabled === 'true') return;
        container.dataset.dragEnabled = 'true';

        let isDown = false;
        let startX = 0;
        let scrollLeft = 0;
        let hasMoved = false;

        container.addEventListener('mousedown', (e) => {
            if (e.target.closest('button') || e.target.closest('.card-bookmark-btn') || e.target.closest('.card-play-btn')) return;
            isDown = true;
            hasMoved = false;
            container.classList.add('dragging');
            container.style.scrollBehavior = 'auto';
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });

        window.addEventListener('mouseup', () => {
            if (isDown) {
                isDown = false;
                container.classList.remove('dragging');
                container.style.scrollBehavior = 'smooth';
                setTimeout(() => { hasMoved = false; }, 60);
            }
        });

        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 1.5;
            if (Math.abs(x - startX) > 6) {
                hasMoved = true;
            }
            container.scrollLeft = scrollLeft - walk;
        });

        // Prevent opening movie details modal if user was performing a drag slide
        container.addEventListener('click', (e) => {
            if (hasMoved) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);
    },

    async loadSpotlightGenre(genreId) {
        const grid = document.getElementById('spotlightGrid');
        if (!grid) return;
        grid.innerHTML = '<div class="details-loading-spinner"><div class="spinner-ring"></div></div>';

        let data = null;
        if (genreId === 'korean') {
            data = await Api.getKoreanMovies(1);
        } else {
            data = await Api.discover({
                type: 'movie',
                with_genres: genreId,
                sort_by: 'popularity.desc',
                page: 1
            });
        }

        if (!data || !data.results) {
            grid.innerHTML = '<p>Unable to load titles for this genre.</p>';
            return;
        }

        grid.innerHTML = '';
        data.results.slice(0, 12).forEach(item => {
            item.media_type = 'movie';
            const card = this.createMovieCard(item);
            grid.appendChild(card);
        });
    },

    /**
     * Card Builder Component
     */
    createMovieCard(item, options = {}) {
        const card = document.createElement('div');
        card.className = 'movie-card';
        card.dataset.id = item.id;
        const type = item.media_type || (item.first_air_date ? 'tv' : 'movie');
        card.dataset.type = type;

        const title = item.title || item.name || 'Untitled';
        const posterUrl = Api.getImageUrl(item.poster_path, 'w500');
        const releaseYear = Api.formatYear(item.release_date || item.first_air_date);
        const rating = item.vote_average ? Number(item.vote_average).toFixed(1) : 'N/A';
        const isSaved = Watchlist.has(item.id, type);

        card.innerHTML = `
            <div class="poster-wrapper">
                <img class="card-poster-img" src="${posterUrl}" alt="${title.replace(/"/g, '&quot;')}" loading="lazy" onerror="this.onerror=null; this.src=Api.getPosterPlaceholder();">
                <div class="card-rating-badge"><i class="fa-solid fa-star"></i> ${rating}</div>
                <div class="card-type-badge">${type === 'tv' ? 'TV Series' : 'Movie'}</div>
                <div class="card-play-overlay">
                    <div class="card-play-btn" onclick="event.stopPropagation(); window.location.href='watch.php?id=${item.id}&type=${type}';" title="Watch Now"><i class="fa-solid fa-play"></i></div>
                </div>
                <button class="card-bookmark-btn ${isSaved ? 'active' : ''}" data-id="${item.id}" data-type="${type}" title="Watchlist" aria-label="Bookmark">
                    <i class="${isSaved ? 'fa-solid fa-check' : 'fa-solid fa-bookmark'}"></i>
                </button>
            </div>
            <div class="card-info">
                <h4 class="card-title" title="${title}">${title}</h4>
                <div class="card-meta-row">
                    <span>${releaseYear}</span>
                    <span><i class="fa-solid fa-film"></i> HD</span>
                </div>
            </div>
        `;

        // Card Click opens Details Modal
        card.addEventListener('click', (e) => {
            if (e.target.closest('.card-bookmark-btn')) return;
            this.openDetails(item.id, type);
        });

        // Bookmark Click
        const bookmarkBtn = card.querySelector('.card-bookmark-btn');
        if (bookmarkBtn) {
            bookmarkBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const res = Watchlist.toggle(item);
                if (res.limitReached) {
                    this.showToast(`⚠️ Watchlist full (${res.count}/${res.limit} slots)! Buy +2 space for 50 Coins in Rewards Shop.`, 'info');
                    return;
                }
                bookmarkBtn.classList.toggle('active', res.added);
                bookmarkBtn.innerHTML = res.added ? '<i class="fa-solid fa-check"></i>' : '<i class="fa-solid fa-bookmark"></i>';
                this.showToast(res.added ? `Added "${title}" to Watchlist` : `Removed "${title}" from Watchlist`, res.added ? 'success' : 'remove');
            });
        }

        return card;
    },

    /**
     * Autocomplete & Live Search
     */
    async handleAutocomplete(query) {
        const dropdown = document.getElementById('searchAutocomplete');
        const list = document.getElementById('autocompleteList');
        const echo = document.getElementById('searchQueryEcho');
        if (!dropdown || !list) return;

        const activeFilterBtn = document.querySelector('.search-type-toggle button.active');
        const searchType = activeFilterBtn ? activeFilterBtn.dataset.searchFilter : 'multi';

        const data = await Api.search(query, searchType, 1);
        if (!data || !data.results || data.results.length === 0) {
            list.innerHTML = `<div style="padding: 1.2rem; text-align: center; color: var(--text-muted);">No results found for "${query}"</div>`;
            dropdown.classList.add('open');
            return;
        }

        list.innerHTML = '';
        if (echo) echo.textContent = query;

        const validResults = (data.results || []).slice(0, 6);

        validResults.forEach(item => {
            const itemEl = document.createElement('div');
            itemEl.className = 'autocomplete-item';

            const title = item.title || item.name || 'Untitled';
            const isPerson = item.media_type === 'person';
            const imgPath = isPerson ? item.profile_path : (item.poster_path || item.backdrop_path);
            const posterUrl = isPerson ? Api.getProfileUrl(imgPath, 'w185') : Api.getImageUrl(imgPath, 'w185');
            const fallbackSrc = isPerson ? Api.getProfilePlaceholder() : Api.getPosterPlaceholder();
            const releaseYear = Api.formatYear(item.release_date || item.first_air_date);
            const rating = item.vote_average ? Number(item.vote_average).toFixed(1) : 'N/A';
            const type = item.media_type || (item.first_air_date ? 'tv' : 'movie');

            itemEl.innerHTML = `
                <img class="autocomplete-poster ${isPerson ? 'autocomplete-person-avatar' : ''}" src="${posterUrl}" alt="${title.replace(/"/g, '&quot;')}" onerror="this.onerror=null; this.src='${fallbackSrc}';">
                <div class="autocomplete-info">
                    <div class="autocomplete-title">${title}</div>
                    <div class="autocomplete-meta">
                        <span class="autocomplete-badge">${type}</span>
                        ${!isPerson && releaseYear !== 'TBA' ? `<span>${releaseYear}</span>` : ''}
                        ${!isPerson && rating !== 'N/A' ? `<span><i class="fa-solid fa-star" style="color: #fbbf24;"></i> ${rating}</span>` : ''}
                    </div>
                </div>
            `;

            itemEl.addEventListener('click', () => {
                this.closeAutocomplete();
                if (type === 'person') {
                    this.openPersonModal(item.id);
                } else {
                    this.openDetails(item.id, type);
                }
            });

            list.appendChild(itemEl);
        });

        dropdown.classList.add('open');
    },

    closeAutocomplete() {
        const dropdown = document.getElementById('searchAutocomplete');
        if (dropdown) dropdown.classList.remove('open');
    },

    /**
     * Explore Page Engine & Filters
     */
    applyExploreFilters(resetPage = true) {
        if (resetPage) this.exploreState.page = 1;

        const genreSelect = document.getElementById('exploreGenreSelect');
        const yearSelect = document.getElementById('exploreYearSelect');
        const ratingSelect = document.getElementById('exploreRatingSelect');
        const sortSelect = document.getElementById('exploreSortSelect');

        if (genreSelect) this.exploreState.genre = genreSelect.value;
        if (yearSelect) this.exploreState.year = yearSelect.value;
        if (ratingSelect) this.exploreState.minRating = ratingSelect.value;
        if (sortSelect) this.exploreState.sortBy = sortSelect.value;

        this.fetchExploreResults(resetPage);
    },

    async fetchExploreResults(isNewSearch = true) {
        const grid = document.getElementById('exploreResultsGrid');
        const countText = document.getElementById('exploreResultsCount');
        const loadMoreWrapper = document.getElementById('exploreLoadMoreWrapper');
        if (!grid) return;

        if (isNewSearch) {
            grid.innerHTML = '<div class="details-loading-spinner"><div class="spinner-ring"></div></div>';
        }

        let data = null;
        if (this.exploreState.query) {
            data = await Api.search(this.exploreState.query, this.exploreState.type === 'tv' ? 'tv' : 'movie', this.exploreState.page);
        } else {
            data = await Api.discover({
                type: this.exploreState.type,
                with_genres: this.exploreState.genre,
                with_original_language: this.exploreState.with_original_language,
                year: this.exploreState.year,
                min_rating: this.exploreState.minRating,
                sort_by: this.exploreState.sortBy,
                page: this.exploreState.page
            });
        }

        if (!data || !data.results || data.results.length === 0) {
            if (isNewSearch) {
                grid.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem;">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 3rem; color: #ff0080; margin-bottom: 1rem;"></i>
                        <h3>No Discoveries Found</h3>
                        <p style="color: var(--text-muted); margin-top: 0.5rem;">Try relaxing your filters or searching for another keyword.</p>
                    </div>
                `;
            }
            if (loadMoreWrapper) loadMoreWrapper.style.display = 'none';
            if (countText) countText.textContent = 'Found 0 titles';
            return;
        }

        if (isNewSearch) grid.innerHTML = '';
        this.exploreState.totalPages = data.total_pages || 1;

        data.results.forEach(item => {
            item.media_type = this.exploreState.type;
            const card = this.createMovieCard(item);
            grid.appendChild(card);
        });

        if (countText) {
            const total = (data.total_results || 0).toLocaleString();
            countText.textContent = `Showing page ${this.exploreState.page} of ${data.total_pages} (${total} total titles)`;
        }

        if (loadMoreWrapper) {
            loadMoreWrapper.style.display = this.exploreState.page < data.total_pages ? 'flex' : 'none';
        }
    },

    exploreByGenre(genreId, genreName) {
        window.location.hash = `#explore?genre=${genreId}`;
    },

    /**
     * Movies Dedicated View
     */
    async loadMoviesView(isNew = true) {
        const grid = document.getElementById('moviesGrid');
        const loadMore = document.getElementById('moviesLoadMoreWrapper');
        if (!grid) return;

        if (isNew) grid.innerHTML = '<div class="details-loading-spinner"><div class="spinner-ring"></div></div>';

        let data = null;
        switch (this.moviesState.category) {
            case 'now_playing': data = await Api.getNowPlaying(this.moviesState.page); break;
            case 'top_rated': data = await Api.getTopRatedMovies(this.moviesState.page); break;
            case 'upcoming': data = await Api.getUpcoming(this.moviesState.page); break;
            default: data = await Api.getPopularMovies(this.moviesState.page); break;
        }

        if (!data || !data.results) return;
        if (isNew) grid.innerHTML = '';

        data.results.forEach(item => {
            item.media_type = 'movie';
            const card = this.createMovieCard(item);
            grid.appendChild(card);
        });

        if (loadMore) {
            loadMore.style.display = this.moviesState.page < data.total_pages ? 'flex' : 'none';
        }
    },

    /**
     * TV Shows Dedicated View
     */
    async loadTvView(isNew = true) {
        const grid = document.getElementById('tvGrid');
        const loadMore = document.getElementById('tvLoadMoreWrapper');
        if (!grid) return;

        if (isNew) grid.innerHTML = '<div class="details-loading-spinner"><div class="spinner-ring"></div></div>';

        let data = null;
        switch (this.tvState.category) {
            case 'top_rated_tv': data = await Api.getTopRatedTv(this.tvState.page); break;
            case 'on_the_air_tv': data = await Api.getOnTheAirTv(this.tvState.page); break;
            default: data = await Api.getPopularTv(this.tvState.page); break;
        }

        if (!data || !data.results) return;
        if (isNew) grid.innerHTML = '';

        data.results.forEach(item => {
            item.media_type = 'tv';
            const card = this.createMovieCard(item);
            grid.appendChild(card);
        });

        if (loadMore) {
            loadMore.style.display = this.tvState.page < data.total_pages ? 'flex' : 'none';
        }
    },

    /**
     * Live TV 24/7 Broadcast View
     */
    async loadLiveTvView(resetPagination = true) {
        const grid = document.getElementById('liveTvChannelsGrid');
        const emptyState = document.getElementById('liveTvEmptyState');
        const loadMore = document.getElementById('liveTvLoadMoreWrapper');
        if (!grid) return;

        if (resetPagination) {
            this.liveTvState.displayedCount = 8;
        }

        if (this.liveTvState.channelsList.length === 0) {
            grid.innerHTML = '<div class="details-loading-spinner"><div class="spinner-ring"></div></div>';
        }

        const channels = await Api.getLiveTvChannels(this.liveTvState.category, this.liveTvState.search);
        this.liveTvState.channelsList = channels || [];

        const allCountEl = document.getElementById('liveTvCountAll');
        if (allCountEl && this.liveTvState.category === 'all' && !this.liveTvState.search) {
            allCountEl.textContent = this.liveTvState.channelsList.length;
        }

        if (!channels || channels.length === 0) {
            grid.innerHTML = '';
            if (emptyState) emptyState.style.display = 'block';
            if (loadMore) loadMore.style.display = 'none';
            return;
        }

        if (emptyState) emptyState.style.display = 'none';

        const visibleChannels = channels.slice(0, this.liveTvState.displayedCount);
        this.renderLiveTvGrid(visibleChannels, false);

        if (loadMore) {
            loadMore.style.display = (this.liveTvState.displayedCount < channels.length) ? 'flex' : 'none';
        }

        // If no channel is currently playing, start playing the first channel
        if (!this.liveTvState.currentChannel && channels.length > 0) {
            this.playLiveChannel(channels[0]);
        }
    },

    loadMoreLiveChannels() {
        const loadMore = document.getElementById('liveTvLoadMoreWrapper');
        const total = this.liveTvState.channelsList.length;
        if (this.liveTvState.displayedCount >= total) {
            if (loadMore) loadMore.style.display = 'none';
            return;
        }

        const prevCount = this.liveTvState.displayedCount;
        this.liveTvState.displayedCount = Math.min(prevCount + this.liveTvState.pageSize, total);
        const nextChannels = this.liveTvState.channelsList.slice(prevCount, this.liveTvState.displayedCount);

        this.renderLiveTvGrid(nextChannels, true);

        if (loadMore) {
            loadMore.style.display = (this.liveTvState.displayedCount < total) ? 'flex' : 'none';
        }
    },

    renderLiveTvGrid(channels, append = false) {
        const grid = document.getElementById('liveTvChannelsGrid');
        if (!grid) return;
        if (!append) {
            grid.innerHTML = '';
        }

        const currentId = this.liveTvState.currentChannel ? this.liveTvState.currentChannel.id : null;

        channels.forEach(ch => {
            const card = document.createElement('div');
            const isActive = currentId === ch.id;
            card.className = `livetv-channel-card ${isActive ? 'is-active-channel' : ''}`;
            card.dataset.channelId = ch.id;

            const categoryLabels = {
                malaysia: '🇲🇾 Malaysia TV',
                movies: 'Movies',
                news: 'News',
                entertainment: 'Entertainment',
                sports: 'Sports',
                music: 'Music',
                documentary: 'Documentary',
                kids: 'Kids'
            };

            const categoryLabel = categoryLabels[ch.category] || ch.category;

            card.innerHTML = `
                <div class="channel-card-top">
                    <div class="channel-card-logo">
                        <img src="${ch.logo}" alt="${ch.name.replace(/"/g, '&quot;')}" loading="lazy" onerror="this.onerror=null; this.src='assets/images/livetv_icon.svg';">
                    </div>
                    <div class="channel-card-status">
                        <span class="live-pill"><span class="live-dot-pulse"></span> LIVE</span>
                        <span class="country-pill">${ch.country}</span>
                    </div>
                </div>
                <div class="channel-card-body">
                    <h3 class="channel-card-name">${ch.name}</h3>
                    <p class="channel-card-desc">${ch.description || ''}</p>
                </div>
                <div class="channel-card-footer">
                    <span class="channel-cat-badge">${categoryLabel}</span>
                    <button class="channel-play-btn" aria-label="Watch ${ch.name.replace(/"/g, '&quot;')}">
                        <i class="fa-solid fa-play"></i> <span>${isActive ? 'Playing' : 'Watch Live'}</span>
                    </button>
                </div>
                ${isActive ? '<div class="now-playing-wave"><span class="bar1"></span><span class="bar2"></span><span class="bar3"></span></div>' : ''}
            `;

            card.addEventListener('click', () => {
                this.playLiveChannel(ch);
                // Scroll up smoothly to player stage if on mobile screens
                const stage = document.getElementById('liveTvStage');
                if (stage && window.innerWidth < 768) {
                    stage.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });

            grid.appendChild(card);
        });
    },

    playLiveChannel(channel) {
        if (!channel || !channel.stream_url) return;
        this.liveTvState.currentChannel = channel;

        // Update stage banner
        const nameEl = document.getElementById('currentChannelName');
        const catEl = document.getElementById('currentChannelCategory');
        const countryEl = document.getElementById('currentChannelCountry');
        const descEl = document.getElementById('currentChannelDesc');
        const logoWrap = document.getElementById('currentChannelLogoWrap');

        if (nameEl) nameEl.textContent = channel.name;
        if (catEl) {
            const categoryLabels = {
                malaysia: '🇲🇾 Malaysia TV',
                movies: 'Movies & Cinema',
                news: 'News 24/7',
                entertainment: 'Entertainment',
                sports: 'Sports & Action',
                music: 'Music & Concerts',
                documentary: 'Documentary & Sci',
                kids: 'Kids & Animation'
            };
            catEl.textContent = categoryLabels[channel.category] || channel.category;
        }
        if (countryEl) countryEl.textContent = `${channel.country} (${channel.language || 'EN'})`;
        if (descEl) descEl.textContent = channel.description || '24/7 Free Live Broadcast';
        if (logoWrap) {
            logoWrap.innerHTML = `<img src="${channel.logo}" alt="${channel.name.replace(/"/g, '&quot;')}" class="current-channel-logo-img" onerror="this.onerror=null; this.outerHTML='<i class=\\'fa-solid fa-tower-broadcast default-tv-icon\\'></i>';">`;
        }

        // Highlight active card in grid
        document.querySelectorAll('.livetv-channel-card').forEach(card => {
            const isActive = card.dataset.channelId === channel.id;
            card.classList.toggle('is-active-channel', isActive);
            const playBtn = card.querySelector('.channel-play-btn span');
            if (playBtn) playBtn.textContent = isActive ? 'Playing' : 'Watch Live';
            
            const existingWave = card.querySelector('.now-playing-wave');
            if (isActive && !existingWave) {
                const wave = document.createElement('div');
                wave.className = 'now-playing-wave';
                wave.innerHTML = '<span class="bar1"></span><span class="bar2"></span><span class="bar3"></span>';
                card.appendChild(wave);
            } else if (!isActive && existingWave) {
                existingWave.remove();
            }
        });

        // Initialize HLS Video Streaming with Backend CORS Proxy
        const video = document.getElementById('liveTvVideoPlayer');
        const overlay = document.getElementById('liveTvPlayerOverlay');
        const overlayText = document.getElementById('liveTvOverlayText');
        if (!video) return;

        if (overlay) {
            overlay.style.display = 'flex';
            if (overlayText) overlayText.innerHTML = `<span class="livetv-connecting-name">Connecting to <strong>${channel.name}</strong>...</span>`;
        }

        if (this.liveTvState.hlsInstance) {
            this.liveTvState.hlsInstance.destroy();
            this.liveTvState.hlsInstance = null;
        }

        // Always route through stream proxy to guarantee CORS and SSL headers
        const streamUrl = `api.php?action=stream_proxy&url=${encodeURIComponent(channel.stream_url)}`;

        video.onplaying = () => {
            if (overlay) overlay.style.display = 'none';
        };

        video.onwaiting = () => {
            if (overlay) {
                overlay.style.display = 'flex';
                if (overlayText) overlayText.innerHTML = `<span>Buffering <strong>${channel.name}</strong>...</span>`;
            }
        };

        let retryCount = 0;
        const maxRetries = 3;

        // Use Hls.js if supported
        if (window.Hls && Hls.isSupported()) {
            const hls = new Hls({
                enableWorker: true,
                lowLatencyMode: true,
                backBufferLength: 30,
                maxBufferLength: 20,
                maxMaxBufferLength: 40,
                liveSyncDurationCount: 3,
                liveMaxLatencyDurationCount: 6,
                fragLoadingTimeOut: 12000,
                manifestLoadingTimeOut: 12000,
                levelLoadingTimeOut: 12000
            });
            this.liveTvState.hlsInstance = hls;

            hls.loadSource(streamUrl);
            hls.attachMedia(video);

            hls.on(Hls.Events.MANIFEST_PARSED, () => {
                video.play().catch(err => {
                    console.log('Unmuted autoplay restricted by browser, retrying muted:', err);
                    video.muted = true;
                    video.play().catch(e => console.warn('Autoplay failed:', e));
                });
            });

            hls.on(Hls.Events.ERROR, (event, data) => {
                if (data.fatal) {
                    console.warn('HLS Live Stream Fatal Error:', data.type, data.details);
                    retryCount++;

                    if (retryCount <= maxRetries) {
                        switch (data.type) {
                            case Hls.ErrorTypes.NETWORK_ERROR:
                                if (overlayText) overlayText.innerHTML = `<span>Reconnecting stream (${retryCount}/${maxRetries})...</span>`;
                                setTimeout(() => hls.startLoad(), 1200);
                                break;
                            case Hls.ErrorTypes.MEDIA_ERROR:
                                if (overlayText) overlayText.innerHTML = `<span>Recovering media playback...</span>`;
                                hls.recoverMediaError();
                                break;
                            default:
                                hls.destroy();
                                this.showNextChannelPrompt(channel);
                                break;
                        }
                    } else {
                        hls.destroy();
                        this.showNextChannelPrompt(channel);
                    }
                }
            });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            // Native Safari / iOS HLS support
            video.src = streamUrl;
            video.addEventListener('loadedmetadata', () => {
                if (overlay) overlay.style.display = 'none';
                video.play().catch(err => {
                    video.muted = true;
                    video.play().catch(() => {});
                });
            }, { once: true });
        } else {
            if (overlay) {
                overlay.style.display = 'flex';
                if (overlayText) overlayText.innerHTML = `<span>Your browser does not support HLS live streams.</span>`;
            }
        }
    },

    showNextChannelPrompt(failedChannel) {
        const overlay = document.getElementById('liveTvPlayerOverlay');
        const overlayText = document.getElementById('liveTvOverlayText');
        if (!overlay || !overlayText) return;

        overlay.style.display = 'flex';
        overlayText.innerHTML = `
            <div class="livetv-fail-box">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div><strong>${failedChannel.name}</strong> is temporarily offline from the broadcaster.</div>
                <button class="btn btn-sm btn-gradient" id="playNextChannelBtn" style="margin-top: 0.6rem;">
                    <i class="fa-solid fa-forward-step"></i> Play Next Channel
                </button>
            </div>
        `;

        const nextBtn = document.getElementById('playNextChannelBtn');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const list = this.liveTvState.channelsList;
                if (!list || list.length === 0) return;
                const idx = list.findIndex(c => c.id === failedChannel.id);
                const nextIdx = (idx + 1) % list.length;
                this.playLiveChannel(list[nextIdx]);
            });
        }
    },

    reloadCurrentLiveStream() {
        if (this.liveTvState.currentChannel) {
            this.showToast(`Reconnecting ${this.liveTvState.currentChannel.name}...`, 'info');
            this.playLiveChannel(this.liveTvState.currentChannel);
        }
    },

    toggleLiveTvFullscreen() {
        const playerWrap = document.querySelector('.livetv-player-container');
        const video = document.getElementById('liveTvVideoPlayer');
        const el = playerWrap || video;
        if (!el) return;

        if (!document.fullscreenElement) {
            if (el.requestFullscreen) {
                el.requestFullscreen();
            } else if (el.webkitRequestFullscreen) {
                el.webkitRequestFullscreen();
            } else if (video && video.webkitEnterFullscreen) {
                video.webkitEnterFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    },

    /**
     * Watchlist Dedicated View
     */
    renderWatchlistView() {
        const grid = document.getElementById('watchlistGrid');
        const emptyState = document.getElementById('watchlistEmptyState');
        if (!grid) return;

        const allItems = Watchlist.getItems();
        let filteredItems = allItems;
        if (this.watchlistFilter === 'movie') {
            filteredItems = allItems.filter(i => (i.media_type || 'movie') === 'movie');
        } else if (this.watchlistFilter === 'tv') {
            filteredItems = allItems.filter(i => i.media_type === 'tv');
        }

        if (filteredItems.length === 0) {
            grid.innerHTML = '';
            if (emptyState) emptyState.style.display = 'block';
            return;
        }

        if (emptyState) emptyState.style.display = 'none';
        grid.innerHTML = '';

        filteredItems.forEach(item => {
            const card = this.createMovieCard(item);
            grid.appendChild(card);
        });
    },

    /**
     * Movie & TV Show Details Modal Renderer
     */
    async openDetails(id, type = 'movie') {
        const overlay = document.getElementById('detailsModalOverlay');
        const body = document.getElementById('detailsModalBody');
        if (!overlay || !body) return;

        body.innerHTML = '<div class="details-loading-spinner"><div class="spinner-ring"></div><span>Loading cinematic details...</span></div>';
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';

        const data = await Api.getDetails(id, type);
        if (!data) {
            body.innerHTML = '<div style="padding: 3rem; text-align: center;"><h3>Failed to load details.</h3></div>';
            return;
        }

        const title = data.title || data.name || 'Untitled';
        const tagline = data.tagline ? `"${data.tagline}"` : '';
        const backdropUrl = Api.getImageUrl(data.backdrop_path, 'original');
        const posterUrl = Api.getImageUrl(data.poster_path, 'w500');
        const releaseDate = Api.formatFullDate(data.release_date || data.first_air_date);
        const releaseYear = Api.formatYear(data.release_date || data.first_air_date);
        const runtime = data.runtime ? Api.formatRuntime(data.runtime) : (data.episode_run_time && data.episode_run_time[0] ? `${data.episode_run_time[0]}m / ep` : 'N/A');
        const rating = data.vote_average ? Number(data.vote_average).toFixed(1) : 'N/A';
        const votes = data.vote_count ? data.vote_count.toLocaleString() : 0;
        const genres = (data.genres || []).map(g => `<span class="meta-chip">${g.name}</span>`).join('');
        const status = data.status || 'Released';
        const budget = data.budget ? `$${(data.budget / 1000000).toFixed(1)}M` : 'N/A';
        const revenue = data.revenue ? `$${(data.revenue / 1000000).toFixed(1)}M` : 'N/A';
        const isSaved = Watchlist.has(data.id, type);
        const trailerKey = data.trailer_key;

        // Cast List (up to 20 members with reliable unknown avatar fallback)
        const rawCast = data.credits && data.credits.cast ? data.credits.cast.slice(0, 20) : [];
        const castItems = rawCast.map(c => {
            const avatarUrl = Api.getProfileUrl(c.profile_path, 'w185');
            const actorName = c.name || 'Unknown Actor';
            const charName = c.character || 'Cast Member';
            return `
                <div class="cast-item" onclick="App.openPersonModal(${c.id})">
                    <div class="cast-img-box">
                        <img src="${avatarUrl}" alt="${actorName.replace(/"/g, '&quot;')}" loading="lazy" onerror="this.onerror=null; this.src=Api.getProfilePlaceholder();">
                    </div>
                    <div class="cast-name" title="${actorName.replace(/"/g, '&quot;')}">${actorName}</div>
                    <div class="cast-role" title="${charName.replace(/"/g, '&quot;')}">${charName}</div>
                </div>
            `;
        }).join('');

        // Companies
        const companies = (data.production_companies || []).slice(0, 4).map(c => `
            <span class="company-badge">${c.name}</span>
        `).join('');

        // Videos List
        const videosList = (data.videos && data.videos.results ? data.videos.results : [])
            .filter(v => v.site === 'YouTube' && ['Trailer', 'Teaser', 'Clip', 'Featurette'].includes(v.type))
            .slice(0, 8);

        // Keywords List
        const rawKeywords = data.keywords ? (data.keywords.keywords || data.keywords.results || []) : [];
        const keywordsList = rawKeywords.slice(0, 10);

        body.innerHTML = `
            <div class="details-hero" style="background-image: url('${backdropUrl}');">
                <div class="details-hero-overlay"></div>
                <div class="details-hero-content">
                    <div class="details-poster-box">
                        <img src="${posterUrl}" alt="${title}">
                    </div>
                    <div class="details-title-info">
                        <h2 class="details-title">${title}</h2>
                        ${tagline ? `<p class="details-tagline">${tagline}</p>` : ''}
                        <div class="details-meta-chips">
                            <span class="meta-chip score-circle-badge"><i class="fa-solid fa-star"></i> ${rating} (${votes} votes)</span>
                            <span class="meta-chip"><i class="fa-solid fa-calendar"></i> ${releaseYear}</span>
                            <span class="meta-chip"><i class="fa-solid fa-clock"></i> ${runtime}</span>
                            <span class="meta-chip">${type === 'tv' ? 'TV Series' : 'Movie'}</span>
                            ${genres}
                        </div>
                        <div class="details-actions-bar">
                            <a href="watch.php?id=${data.id}&type=${type}" class="btn btn-gradient modal-watch-btn">
                                <i class="fa-solid fa-circle-play"></i> Watch ${type === 'tv' ? 'Series' : 'Movie'}
                            </a>
                            ${trailerKey ? `
                                <button class="btn btn-secondary modal-trailer-btn" data-key="${trailerKey}" data-title="${title.replace(/"/g, '&quot;')}">
                                    <i class="fa-solid fa-film"></i> Watch Trailer
                                </button>
                            ` : `
                                <button class="btn btn-secondary modal-trailer-btn" data-key="" data-title="${title.replace(/"/g, '&quot;')}">
                                    <i class="fa-solid fa-magnifying-glass"></i> Find Trailer
                                </button>
                            `}
                            <button class="btn btn-secondary modal-watchlist-btn ${isSaved ? 'active' : ''}" data-id="${data.id}" data-type="${type}">
                                <i class="${isSaved ? 'fa-solid fa-check' : 'fa-solid fa-bookmark'}"></i> ${isSaved ? 'In Watchlist' : 'Add to Watchlist'}
                            </button>
                            <button class="btn btn-secondary modal-share-btn" data-title="${title.replace(/"/g, '&quot;')}" data-id="${data.id}" data-type="${type}" title="Share title">
                                <i class="fa-solid fa-share-nodes"></i> Share
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="details-main-content">
                <div class="details-overview-box">
                    <h3 class="details-section-heading"><i class="fa-solid fa-book-open"></i> Storyline Overview</h3>
                    <p class="details-overview-text">${data.overview || 'No storyline summary available for this title.'}</p>
                </div>

                ${castItems ? `
                    <div class="details-cast-box">
                        <div class="modal-shelf-header">
                            <h3 class="details-section-heading" style="margin-bottom: 0;"><i class="fa-solid fa-users"></i> Top Cast & Crew</h3>
                            <div class="header-actions-shelf">
                                <button class="shelf-nav-btn prev" id="detailsCastPrevBtn" aria-label="Scroll left"><i class="fa-solid fa-chevron-left"></i></button>
                                <button class="shelf-nav-btn next" id="detailsCastNextBtn" aria-label="Scroll right"><i class="fa-solid fa-chevron-right"></i></button>
                            </div>
                        </div>
                        <div class="cast-avatars-row" id="detailsCastRow">${castItems}</div>
                    </div>
                ` : ''}

                ${videosList.length > 0 ? `
                    <div class="details-videos-box">
                        <div class="modal-shelf-header">
                            <h3 class="details-section-heading" style="margin-bottom: 0;"><i class="fa-brands fa-youtube" style="color: #ff0000;"></i> Trailers & Video Clips (${videosList.length})</h3>
                            <div class="header-actions-shelf">
                                <button class="shelf-nav-btn prev" id="detailsVideosPrevBtn" aria-label="Scroll left"><i class="fa-solid fa-chevron-left"></i></button>
                                <button class="shelf-nav-btn next" id="detailsVideosNextBtn" aria-label="Scroll right"><i class="fa-solid fa-chevron-right"></i></button>
                            </div>
                        </div>
                        <div class="details-videos-row" id="detailsVideosRow">
                            ${videosList.map(v => `
                                <div class="video-card-thumb" onclick="App.openTrailerModal('${v.key}', '${title.replace(/'/g, "\\'")} — ${v.name.replace(/'/g, "\\'")}')">
                                    <div class="video-thumb-img-box">
                                        <img src="https://img.youtube.com/vi/${v.key}/mqdefault.jpg" alt="${v.name}" loading="lazy">
                                        <div class="video-play-badge"><i class="fa-solid fa-play"></i></div>
                                    </div>
                                    <div class="video-card-info">
                                        <div class="video-card-type">${v.type}</div>
                                        <div class="video-card-title" title="${v.name}">${v.name}</div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}

                ${keywordsList.length > 0 ? `
                    <div class="details-keywords-box">
                        <h4 class="details-section-heading" style="font-size: 0.92rem; margin-bottom: 0.45rem;"><i class="fa-solid fa-tags"></i> Tags & Keywords</h4>
                        <div class="details-keywords-list">
                            ${keywordsList.map(k => `<span class="keyword-chip">#${k.name}</span>`).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>

            <!-- Similar Titles -->
            ${data.similar && data.similar.results && data.similar.results.length > 0 ? `
                <div class="modal-similar-section">
                    <div class="modal-shelf-header">
                        <h3 class="details-section-heading" style="margin-bottom: 0;"><i class="fa-solid fa-sparkles"></i> More Like This</h3>
                        <div class="header-actions-shelf">
                            <button class="shelf-nav-btn prev" id="detailsSimilarPrevBtn" aria-label="Scroll left"><i class="fa-solid fa-chevron-left"></i></button>
                            <button class="shelf-nav-btn next" id="detailsSimilarNextBtn" aria-label="Scroll right"><i class="fa-solid fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="shelf-row" id="detailsSimilarRow">
                        ${data.similar.results.slice(0, 12).map(sim => `
                            <div class="movie-card" onclick="App.openDetails(${sim.id}, '${type}')">
                                <div class="poster-wrapper">
                                    <img class="card-poster-img" src="${Api.getImageUrl(sim.poster_path, 'w500')}" alt="${sim.title || sim.name}">
                                    <div class="card-rating-badge"><i class="fa-solid fa-star"></i> ${sim.vote_average ? Number(sim.vote_average).toFixed(1) : 'N/A'}</div>
                                </div>
                                <div class="card-info">
                                    <div class="card-title">${sim.title || sim.name}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
        `;

        // Bind inner modal action buttons
        const trailerBtn = body.querySelector('.modal-trailer-btn');
        if (trailerBtn) {
            trailerBtn.addEventListener('click', () => {
                const key = trailerBtn.dataset.key;
                if (key) {
                    this.openTrailerModal(key, `${title} — Official Trailer`);
                } else {
                    window.open(`https://www.youtube.com/results?search_query=${encodeURIComponent(title + ' official trailer')}`, '_blank');
                }
            });
        }

        const watchlistBtn = body.querySelector('.modal-watchlist-btn');
        if (watchlistBtn) {
            watchlistBtn.addEventListener('click', () => {
                const res = Watchlist.toggle(data);
                if (res.limitReached) {
                    this.showToast(`⚠️ Watchlist full (${res.count}/${res.limit} slots)! Buy +2 space for 50 Coins in Rewards Shop.`, 'info');
                    return;
                }
                watchlistBtn.classList.toggle('active', res.added);
                watchlistBtn.innerHTML = res.added ? '<i class="fa-solid fa-check"></i> In Watchlist' : '<i class="fa-solid fa-bookmark"></i> Add to Watchlist';
                this.showToast(res.added ? `Added "${title}" to Watchlist` : `Removed "${title}" from Watchlist`, res.added ? 'success' : 'remove');
            });
        }

        const shareBtn = body.querySelector('.modal-share-btn');
        if (shareBtn) {
            shareBtn.addEventListener('click', () => {
                const shareUrl = `${window.location.origin}${window.location.pathname}#${type}/${id}`;
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(shareUrl);
                    this.showToast('Direct link copied to clipboard!', 'info');
                } else {
                    prompt('Copy link to movie:', shareUrl);
                }
            });
        }

        // Enable left/right buttons and drag on modal cast
        const castRow = body.querySelector('#detailsCastRow');
        const castPrev = body.querySelector('#detailsCastPrevBtn');
        const castNext = body.querySelector('#detailsCastNextBtn');
        if (castRow) {
            if (castPrev) castPrev.addEventListener('click', () => castRow.scrollBy({ left: -castRow.clientWidth * 0.75, behavior: 'smooth' }));
            if (castNext) castNext.addEventListener('click', () => castRow.scrollBy({ left: castRow.clientWidth * 0.75, behavior: 'smooth' }));
            this.enableDragToScroll(castRow);
        }

        // Enable left/right buttons and drag on modal video clips
        const videosRow = body.querySelector('#detailsVideosRow');
        const videosPrev = body.querySelector('#detailsVideosPrevBtn');
        const videosNext = body.querySelector('#detailsVideosNextBtn');
        if (videosRow) {
            if (videosPrev) videosPrev.addEventListener('click', () => videosRow.scrollBy({ left: -videosRow.clientWidth * 0.75, behavior: 'smooth' }));
            if (videosNext) videosNext.addEventListener('click', () => videosRow.scrollBy({ left: videosRow.clientWidth * 0.75, behavior: 'smooth' }));
            this.enableDragToScroll(videosRow);
        }

        // Enable left/right buttons and drag on similar items
        const similarRow = body.querySelector('#detailsSimilarRow');
        const similarPrev = body.querySelector('#detailsSimilarPrevBtn');
        const similarNext = body.querySelector('#detailsSimilarNextBtn');
        if (similarRow) {
            if (similarPrev) similarPrev.addEventListener('click', () => similarRow.scrollBy({ left: -similarRow.clientWidth * 0.75, behavior: 'smooth' }));
            if (similarNext) similarNext.addEventListener('click', () => similarRow.scrollBy({ left: similarRow.clientWidth * 0.75, behavior: 'smooth' }));
            this.enableDragToScroll(similarRow);
        }
    },

    closeDetailsModal() {
        const overlay = document.getElementById('detailsModalOverlay');
        if (overlay) overlay.classList.remove('open');
        document.body.style.overflow = '';
    },

    /**
     * Trailer Theater Player Modal
     */
    openTrailerModal(youtubeKey, title = 'Official Trailer') {
        const overlay = document.getElementById('trailerModalOverlay');
        const container = document.getElementById('videoContainer');
        const titleEl = document.getElementById('trailerModalTitle');
        if (!overlay || !container) return;

        if (titleEl) titleEl.textContent = title;
        container.innerHTML = `
            <iframe src="https://www.youtube-nocookie.com/embed/${youtubeKey}?autoplay=1&rel=0&modestbranding=1" title="${title}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        `;

        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    },

    closeTrailerModal() {
        const overlay = document.getElementById('trailerModalOverlay');
        const container = document.getElementById('videoContainer');
        if (container) container.innerHTML = '';
        if (overlay) overlay.classList.remove('open');
        // If details modal wasn't open, restore scroll
        const detailsOverlay = document.getElementById('detailsModalOverlay');
        if (!detailsOverlay || !detailsOverlay.classList.contains('open')) {
            document.body.style.overflow = '';
        }
    },

    /**
     * Actor / Person Profile Modal
     */
    async openPersonModal(personId) {
        const overlay = document.getElementById('personModalOverlay');
        const body = document.getElementById('personModalBody');
        if (!overlay || !body) return;

        body.innerHTML = '<div class="details-loading-spinner"><div class="spinner-ring"></div></div>';
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';

        const data = await Api.getPerson(personId);
        if (!data) {
            body.innerHTML = '<p>Person profile unavailable.</p>';
            return;
        }

        const photoUrl = Api.getProfileUrl(data.profile_path, 'w500');
        const knownFor = data.known_for_department || 'Acting';
        const birthday = data.birthday ? Api.formatFullDate(data.birthday) : 'N/A';
        const birthplace = data.place_of_birth || 'N/A';
        const bio = data.biography || 'No biography available for this artist.';

        // Filter and deduplicate all cast works with posters
        const rawCredits = (data.combined_credits && data.combined_credits.cast ? data.combined_credits.cast : []);
        rawCredits.sort((a, b) => (b.vote_count || 0) - (a.vote_count || 0));
        const seenIds = new Set();
        const credits = [];
        rawCredits.forEach(c => {
            if (!seenIds.has(c.id) && (c.poster_path || c.backdrop_path)) {
                seenIds.add(c.id);
                credits.push(c);
            }
        });

        body.innerHTML = `
            <div class="person-profile-header">
                <img class="person-avatar-lg" src="${photoUrl}" alt="${data.name}" onerror="this.onerror=null; this.src=Api.getProfilePlaceholder();">
                <div>
                    <h2 class="person-name">${data.name}</h2>
                    <div style="display: flex; gap: 0.6rem; margin-bottom: 0.8rem; flex-wrap: wrap;">
                        <span class="meta-chip">${knownFor}</span>
                        ${birthday !== 'N/A' ? `<span class="meta-chip"><i class="fa-solid fa-cake-candles"></i> ${birthday}</span>` : ''}
                        ${birthplace !== 'N/A' ? `<span class="meta-chip"><i class="fa-solid fa-location-dot"></i> ${birthplace}</span>` : ''}
                    </div>
                    <p class="person-bio">${bio}</p>
                </div>
            </div>

            <div class="person-roles-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.8rem;">
                <h3 class="details-section-heading" style="margin-bottom: 0;"><i class="fa-solid fa-star"></i> Famous Roles & Works (${credits.length})</h3>
                <div class="header-actions-shelf">
                    <button class="shelf-nav-btn prev" id="personRolesPrevBtn" aria-label="Scroll left"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="shelf-nav-btn next" id="personRolesNextBtn" aria-label="Scroll right"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="shelf-row" id="personRolesShelfRow">
                ${credits.map(c => `
                    <div class="movie-card" onclick="App.closePersonModal(); App.openDetails(${c.id}, '${c.media_type || (c.first_air_date ? 'tv' : 'movie')}');">
                        <div class="poster-wrapper">
                            <img class="card-poster-img" src="${Api.getImageUrl(c.poster_path, 'w500')}" alt="${c.title || c.name}" loading="lazy" onerror="this.onerror=null; this.src=Api.getPosterPlaceholder();">
                            <div class="card-rating-badge"><i class="fa-solid fa-star"></i> ${c.vote_average ? Number(c.vote_average).toFixed(1) : 'N/A'}</div>
                            <div class="card-type-badge">${c.media_type === 'tv' || c.first_air_date ? 'TV' : 'Movie'}</div>
                        </div>
                        <div class="card-info">
                            <div class="card-title">${c.title || c.name}</div>
                            <div class="card-meta-row">
                                <span>${Api.formatYear(c.release_date || c.first_air_date)}</span>
                                <span style="max-width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${c.character ? c.character : 'Cast'}</span>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

        // Wire up left and right chevron buttons for person roles
        const personRow = body.querySelector('#personRolesShelfRow');
        const prevBtn = body.querySelector('#personRolesPrevBtn');
        const nextBtn = body.querySelector('#personRolesNextBtn');

        if (personRow && prevBtn && nextBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const scrollAmount = personRow.clientWidth * 0.75;
                personRow.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
            nextBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const scrollAmount = personRow.clientWidth * 0.75;
                personRow.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
            this.enableDragToScroll(personRow);
        }
    },

    closePersonModal() {
        const overlay = document.getElementById('personModalOverlay');
        if (overlay) overlay.classList.remove('open');
        const detailsOverlay = document.getElementById('detailsModalOverlay');
        if (!detailsOverlay || !detailsOverlay.classList.contains('open')) {
            document.body.style.overflow = '';
        }
    },

    closeAllModals() {
        this.closeDetailsModal();
        this.closeTrailerModal();
        this.closePersonModal();
        this.closeDailyDiceModal();
        this.closeCoinPerksModal();
        this.closeSurpriseMysteryModal();
        this.closeAutocomplete();
        const mobileDrawer = document.getElementById('mobileDrawer');
        if (mobileDrawer) mobileDrawer.classList.remove('open');
    },

    /**
     * Surprise Me / Animated Vibrating Mystery Box Modal Controller
     */
    openSurpriseMysteryModal() {
        const overlay = document.getElementById('surpriseMysteryOverlay');
        if (!overlay) {
            return this.handleSurpriseMeFallback();
        }

        overlay.classList.add('open');
        document.body.classList.add('modal-open');
        this.rollSurpriseMovie();
    },

    closeSurpriseMysteryModal() {
        if (this._mysteryInterval) {
            clearInterval(this._mysteryInterval);
            this._mysteryInterval = null;
        }
        if (this._mysteryProgressTimer) {
            clearTimeout(this._mysteryProgressTimer);
            this._mysteryProgressTimer = null;
        }
        const overlay = document.getElementById('surpriseMysteryOverlay');
        if (overlay) overlay.classList.remove('open');
        document.body.classList.remove('modal-open');
    },

    async rollSurpriseMovie() {
        const loadingStage = document.getElementById('mysteryStageLoading');
        const revealStage = document.getElementById('mysteryStageRevealed');
        const chestBox = document.getElementById('mysteryChestBox');
        const statusText = document.getElementById('mysteryStatusText');
        const progressFill = document.getElementById('mysteryProgressFill');
        const movieCard = document.getElementById('revealedMovieCard');
        const watchBtn = document.getElementById('mysteryWatchGemBtn');

        if (!loadingStage || !revealStage || !chestBox) return;

        // Reset stages
        loadingStage.style.display = 'flex';
        revealStage.style.display = 'none';
        chestBox.classList.remove('cracking');
        chestBox.classList.add('vibrating');

        if (progressFill) progressFill.style.width = '0%';

        // Play suspense rolling sound
        this.playDiceSoundEffect('roll');

        // Text Rotator
        const teasers = [
            '<i class="fa-solid fa-dice fa-spin"></i> Shuffling 10,000+ blockbuster films...',
            '<i class="fa-solid fa-shuffle fa-spin"></i> Rolling the cosmic cinema dice...',
            '<i class="fa-solid fa-wand-magic-sparkles"></i> Searching for hidden masterpieces...',
            '<i class="fa-solid fa-film fa-spin"></i> Selecting your destiny gem...',
            '<i class="fa-solid fa-gem fa-bounce"></i> Unlocking mystery cinema vault...'
        ];
        let teaserIdx = 0;
        if (this._mysteryInterval) clearInterval(this._mysteryInterval);
        this._mysteryInterval = setInterval(() => {
            teaserIdx = (teaserIdx + 1) % teasers.length;
            if (statusText) statusText.innerHTML = teasers[teaserIdx];
        }, 260);

        // Progress bar smooth fill
        const startTime = Date.now();
        const duration = 1400; // 1.4s suspense time

        const updateProgress = () => {
            const elapsed = Date.now() - startTime;
            const pct = Math.min(100, Math.floor((elapsed / duration) * 100));
            if (progressFill) progressFill.style.width = `${pct}%`;
            if (elapsed < duration) {
                requestAnimationFrame(updateProgress);
            }
        };
        requestAnimationFrame(updateProgress);

        // Parallel Fetch: Fetch a random movie from top rated or trending or popular
        let fetchedMovie = null;
        try {
            const randomPage = Math.floor(Math.random() * 6) + 1;
            const fetchMode = Math.floor(Math.random() * 3);
            let data = null;

            if (fetchMode === 0) {
                data = await Api.getTopRatedMovies(randomPage);
            } else if (fetchMode === 1) {
                data = await Api.getTrending('movie', 'week', randomPage);
            } else {
                data = await Api.getPopularMovies(randomPage);
            }

            if (data && data.results && data.results.length > 0) {
                const validMovies = data.results.filter(m => m.poster_path && m.title && m.vote_average >= 6.0);
                const pool = validMovies.length > 0 ? validMovies : data.results;
                fetchedMovie = pool[Math.floor(Math.random() * pool.length)];
            }
        } catch (e) {
            console.error('Mystery movie fetch error:', e);
        }

        // Wait until duration completes (minimum 1.4s)
        const elapsed = Date.now() - startTime;
        if (elapsed < duration) {
            await new Promise(r => setTimeout(r, duration - elapsed));
        }

        if (this._mysteryInterval) {
            clearInterval(this._mysteryInterval);
            this._mysteryInterval = null;
        }

        // Fallback movie if network failed
        if (!fetchedMovie) {
            fetchedMovie = {
                id: 157336,
                title: 'Interstellar',
                vote_average: 8.4,
                release_date: '2014-11-05',
                poster_path: '/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                overview: 'The adventures of a group of explorers who make use of a newly discovered wormhole to surpass the limitations on human space travel and conquer the vast distances involved in an interstellar voyage.',
                genre_ids: [12, 18, 878]
            };
        }

        // Trigger Box Crack & Burst
        chestBox.classList.remove('vibrating');
        chestBox.classList.add('cracking');
        this.playDiceSoundEffect('win');
        this.launchConfetti(2800);

        // Wait 380ms for crack burst animation
        await new Promise(r => setTimeout(r, 380));

        loadingStage.style.display = 'none';
        revealStage.style.display = 'flex';

        // Genre Mapping
        const GENRE_MAP = {
            28: 'Action', 12: 'Adventure', 16: 'Animation', 35: 'Comedy', 80: 'Crime',
            99: 'Doc', 18: 'Drama', 10751: 'Family', 14: 'Fantasy', 36: 'History',
            27: 'Horror', 10402: 'Music', 9648: 'Mystery', 10749: 'Romance', 878: 'Sci-Fi',
            10770: 'TV Movie', 53: 'Thriller', 10752: 'War', 37: 'Western'
        };

        const genrePills = (fetchedMovie.genre_ids || [])
            .slice(0, 2)
            .map(gid => `<span class="revealed-genre-pill">${GENRE_MAP[gid] || 'Cinema'}</span>`)
            .join(' ');

        const posterUrl = fetchedMovie.poster_path ? Api.getImageUrl(fetchedMovie.poster_path, 'w500') : Api.getPosterPlaceholder();
        const year = Api.formatYear(fetchedMovie.release_date || fetchedMovie.first_air_date);
        const rating = fetchedMovie.vote_average ? Number(fetchedMovie.vote_average).toFixed(1) : '8.0';
        const title = fetchedMovie.title || fetchedMovie.name || 'Mystery Gem';
        const overview = fetchedMovie.overview || 'An extraordinary cinematic experience waiting to be discovered. Grab your popcorn and enjoy!';

        if (movieCard) {
            movieCard.innerHTML = `
                <div class="revealed-poster-box">
                    <img src="${posterUrl}" alt="${title}" loading="eager" onerror="this.src='${Api.getPosterPlaceholder()}'">
                </div>
                <div class="revealed-info-box">
                    <h4 class="revealed-movie-name" title="${title}">${title}</h4>
                    <div class="revealed-meta-row">
                        <span class="revealed-score-badge"><i class="fa-solid fa-star"></i> ${rating}</span>
                        <span class="revealed-year-tag">${year}</span>
                        ${genrePills}
                    </div>
                    <p class="revealed-overview">${overview}</p>
                </div>
            `;
        }

        if (watchBtn) {
            watchBtn.onclick = () => {
                window.location.href = `watch.php?id=${fetchedMovie.id}&type=movie`;
            };
        }
    },

    async handleSurpriseMeFallback() {
        this.showToast('Rolling the dice for a cinematic gem...', 'info');
        const randomPage = Math.floor(Math.random() * 5) + 1;
        const data = await Api.getTopRatedMovies(randomPage);
        if (data && data.results && data.results.length > 0) {
            const randomIndex = Math.floor(Math.random() * data.results.length);
            const randomMovie = data.results[randomIndex];
            this.openDetails(randomMovie.id, 'movie');
            this.showToast(`✨ Found: ${randomMovie.title}!`, 'success');
        }
    },

    /**
     * Toast Notifications Engine
     */
    showToast(message, type = 'info') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast-pill toast-${type}`;

        let icon = '<i class="fa-solid fa-circle-info"></i>';
        if (type === 'success') icon = '<i class="fa-solid fa-circle-check"></i>';
        if (type === 'remove') icon = '<i class="fa-solid fa-trash-can"></i>';

        toast.innerHTML = `${icon} <span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(15px) scale(0.9)';
            toast.style.transition = 'all 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    },

    /**
     * Daily Coin System & Movie Dice Game Controller
     */
    initCoinSystem() {
        if (typeof CoinSystem === 'undefined') return;

        const updateCoinUI = () => {
            const coins = CoinSystem.getCoins();
            const dailyState = CoinSystem.getDailyState();

            const headerCoin = document.getElementById('headerCoinCount');
            const mobileCoin = document.getElementById('mobileCoinCount');
            const modalCoin = document.getElementById('diceModalCoinCount');
            const perksShopBalance = document.getElementById('perksShopBalance');
            const streakCount = document.getElementById('diceStreakCount');
            const readyDot = document.getElementById('coinDailyReadyDot');
            const mobileSub = document.getElementById('mobileDiceStatusText');
            const rollsLeftEl = document.getElementById('diceRollsLeftCount');

            if (headerCoin) headerCoin.textContent = coins.toLocaleString();
            if (mobileCoin) mobileCoin.textContent = coins.toLocaleString();
            if (modalCoin) modalCoin.textContent = coins.toLocaleString();
            if (perksShopBalance) perksShopBalance.textContent = coins.toLocaleString();
            if (streakCount) streakCount.textContent = dailyState.streak || 0;
            if (rollsLeftEl) rollsLeftEl.textContent = dailyState.rollsLeft !== undefined ? dailyState.rollsLeft : 2;

            const hasRolls = (dailyState.rollsLeft === undefined || dailyState.rollsLeft > 0);
            if (readyDot) {
                readyDot.style.display = hasRolls ? 'block' : 'none';
            }
            if (mobileSub) {
                mobileSub.textContent = hasRolls ? `${dailyState.rollsLeft || 2} Daily Rolls Ready!` : 'Rolls Used Today';
            }
        };

        CoinSystem.subscribe((update) => {
            updateCoinUI();
            if (update.payload && update.payload.type === 'coin_added') {
                const headerCoin = document.getElementById('headerCoinCount');
                const perksShopBalance = document.getElementById('perksShopBalance');
                if (headerCoin) {
                    headerCoin.style.transform = 'scale(1.3)';
                    headerCoin.style.color = '#fff';
                    headerCoin.style.transition = 'all 0.3s ease';
                    setTimeout(() => {
                        headerCoin.style.transform = '';
                        headerCoin.style.color = '';
                    }, 400);
                }
                if (perksShopBalance) {
                    perksShopBalance.style.transform = 'scale(1.25)';
                    perksShopBalance.style.color = '#fff';
                    perksShopBalance.style.transition = 'all 0.3s ease';
                    setTimeout(() => {
                        perksShopBalance.style.transform = '';
                        perksShopBalance.style.color = '';
                    }, 400);
                }
            }
        });

        // Countdown Timer Loop (updates every second)
        setInterval(() => {
            const timerEl = document.getElementById('diceCountdownTimer');
            if (timerEl) {
                const time = CoinSystem.getTimeUntilReset();
                timerEl.textContent = time.formatted;
            }
        }, 1000);

        updateCoinUI();
    },

    openDailyDiceModal() {
        if (typeof CoinSystem === 'undefined') return;
        const overlay = document.getElementById('dailyDiceModalOverlay');
        if (!overlay) return;

        const setup = CoinSystem.getTodaySetup();
        const state = CoinSystem.getDailyState();

        // Update Target Card
        const posterEl = document.getElementById('targetMoviePoster');
        const tagEl = document.getElementById('targetFaceTag');
        const titleEl = document.getElementById('targetMovieTitle');
        const genreEl = document.getElementById('targetMovieGenre');
        const streakEl = document.getElementById('diceStreakCount');
        const modalCoinEl = document.getElementById('diceModalCoinCount');

        if (posterEl) posterEl.src = setup.targetMovie.poster;
        if (tagEl) tagEl.textContent = `Dice Face #${setup.targetMovie.faceNumber} ${setup.targetMovie.icon}`;
        if (titleEl) titleEl.textContent = setup.targetMovie.title;
        if (genreEl) genreEl.textContent = `${setup.targetMovie.genre} (${setup.targetMovie.year})`;
        if (streakEl) streakEl.textContent = state.streak || 0;
        if (modalCoinEl) modalCoinEl.textContent = CoinSystem.getCoins().toLocaleString();

        // Update 6 Faces of 3D Cube
        const cube = document.getElementById('movieDiceCube');
        if (cube) {
            setup.faces.forEach((face, idx) => {
                const faceEl = cube.querySelector(`.face-${['front', 'back', 'right', 'left', 'top', 'bottom'][idx]}`);
                if (faceEl) {
                    faceEl.querySelector('.face-number').textContent = face.faceNumber;
                    faceEl.querySelector('.face-icon').textContent = face.icon;
                    faceEl.querySelector('.face-title').textContent = face.title;
                    faceEl.style.borderColor = face.color;
                }
            });

            // Reset classes
            cube.className = 'dice-cube';
            if (state.playedToday && state.rolledFaceNumber) {
                cube.classList.add(`show-face-${state.rolledFaceNumber}`);
            }
        }

        // Update Button & Banner Status
        const rollBtn = document.getElementById('rollMovieDiceBtn');
        const banner = document.getElementById('diceResultBanner');
        const rollsLeft = state.rollsLeft !== undefined ? state.rollsLeft : 2;

        if (rollsLeft <= 0) {
            if (rollBtn) {
                rollBtn.disabled = true;
                rollBtn.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>All Daily Rolls Used</span>';
            }
            if (banner) {
                banner.style.display = 'block';
                banner.className = 'dice-result-banner played';
                banner.innerHTML = `🎲 You used all daily rolls today! Total won today: <strong>+${state.totalCoinsWonToday || state.coinsWon || 0} Coins</strong>. Next reset is at midnight!`;
            }
        } else {
            if (rollBtn) {
                rollBtn.disabled = false;
                rollBtn.innerHTML = `<i class="fa-solid fa-dice"></i> <span>Roll Movie Dice (${rollsLeft} Left)</span>`;
            }
            if (banner) {
                if (state.rolledFaceNumber) {
                    banner.style.display = 'block';
                } else {
                    banner.style.display = 'none';
                }
            }
        }

        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    },

    closeDailyDiceModal() {
        const overlay = document.getElementById('dailyDiceModalOverlay');
        if (overlay) overlay.classList.remove('open');
        const detailsOverlay = document.getElementById('detailsModalOverlay');
        if (!detailsOverlay || !detailsOverlay.classList.contains('open')) {
            document.body.style.overflow = '';
        }
    },

    async handleRollMovieDice() {
        if (typeof CoinSystem === 'undefined' || this.isDiceRolling) return;

        const rollBtn = document.getElementById('rollMovieDiceBtn');
        const cube = document.getElementById('movieDiceCube');
        const banner = document.getElementById('diceResultBanner');

        const state = CoinSystem.getDailyState();
        if ((state.rollsLeft || 0) <= 0) {
            this.showToast('You used all daily rolls today! Reset is at midnight.', 'info');
            return;
        }

        this.isDiceRolling = true;
        if (rollBtn) {
            rollBtn.disabled = true;
            rollBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Rolling Dice...</span>';
        }

        // Start 3D rolling animation
        if (cube) {
            cube.className = 'dice-cube rolling';
        }

        // Play rolling audio synth effect
        this.playDiceSoundEffect('roll');

        // Roll calculation (50% target match chance, +25 coins on match, +5 coins consolation)
        const result = CoinSystem.rollDailyDice();

        // 2.2 seconds dramatic suspense
        setTimeout(() => {
            if (cube) {
                cube.className = 'dice-cube';
                // Force reflow
                void cube.offsetWidth;
                cube.classList.add(`show-face-${result.rolledFaceNumber}`);
            }

            this.isDiceRolling = false;

            if (banner) {
                banner.style.display = 'block';
                if (result.isMatch) {
                    banner.className = 'dice-result-banner win';
                    banner.innerHTML = `🎉 <strong>JACKPOT MATCH!</strong> You rolled #${result.rolledFaceNumber} <strong>${result.rolledMovie.title}</strong> and won <strong style="color: #fbbf24;">+25 COINS</strong>!`;
                    this.playDiceSoundEffect('win');
                    this.launchConfetti();
                    this.showToast('🎉 +25 Coins Added to your Balance!', 'success');
                } else {
                    banner.className = 'dice-result-banner win';
                    banner.innerHTML = `🍿 <strong>Nice Roll!</strong> You rolled #${result.rolledFaceNumber} <strong>${result.rolledMovie.title}</strong> and earned <strong style="color: #fbbf24;">+5 Bonus Coins</strong>!`;
                    this.playDiceSoundEffect('win');
                    this.showToast(`+5 Bonus Coins for rolling ${result.rolledMovie.title}!`, 'success');
                }
            }

            // Update Roll button based on remaining rolls
            if (rollBtn) {
                if (result.rollsLeft > 0) {
                    setTimeout(() => {
                        rollBtn.disabled = false;
                        rollBtn.innerHTML = `<i class="fa-solid fa-dice"></i> <span>Roll Again (${result.rollsLeft} Left!)</span>`;
                    }, 800);
                } else {
                    rollBtn.disabled = true;
                    rollBtn.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>All Daily Rolls Used</span>';
                }
            }
        }, 2200);
    },

    /**
     * Web Audio API synthesized sound effects (No external audio assets needed!)
     */
    playDiceSoundEffect(type = 'roll') {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();

            if (type === 'roll') {
                for (let i = 0; i < 7; i++) {
                    setTimeout(() => {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'triangle';
                        osc.frequency.setValueAtTime(320 + Math.random() * 280, ctx.currentTime);
                        gain.gain.setValueAtTime(0.08, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.09);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.09);
                    }, i * 260);
                }
            } else if (type === 'win') {
                const notes = [523.25, 659.25, 783.99, 1046.50]; // C5, E5, G5, C6
                notes.forEach((freq, idx) => {
                    setTimeout(() => {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, ctx.currentTime);
                        gain.gain.setValueAtTime(0.18, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.35);
                    }, idx * 110);
                });
            } else if (type === 'loss') {
                [380, 310].forEach((freq, idx) => {
                    setTimeout(() => {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sawtooth';
                        osc.frequency.setValueAtTime(freq, ctx.currentTime);
                        gain.gain.setValueAtTime(0.08, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.25);
                    }, idx * 160);
                });
            }
        } catch (e) {
            // Audio context not allowed before user gesture
        }
    },

    /**
     * Celebratory Canvas Confetti Burst
     * Triggers multi-cannon burst with golden coins, fluttering ribbons, and sparkling stars.
     * Runs for a few seconds (~3.8s) with smooth physics and fade-out, then automatically hides and cleans up.
     */
    launchConfetti(durationMs = 3800) {
        let canvas = document.getElementById('coinConfettiCanvas');
        if (!canvas) {
            canvas = document.createElement('canvas');
            canvas.id = 'coinConfettiCanvas';
            document.body.appendChild(canvas);
        }

        if (this._confettiAnimId) {
            cancelAnimationFrame(this._confettiAnimId);
            this._confettiAnimId = null;
        }

        canvas.style.display = 'block';
        canvas.style.opacity = '1';
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const ctx = canvas.getContext('2d');
        const particles = [];
        const colors = [
            '#fbbf24', '#f59e0b', '#ffd700', // Gold & Amber
            '#ff0080', '#ec4899',            // Neon Rose / Pink
            '#00f2fe', '#38bdf8',            // Cyber Cyan / Sky
            '#10b981', '#34d399',            // Emerald Green
            '#a855f7', '#c084fc',            // Electric Purple
            '#ffffff'                        // Sparkle White
        ];

        const particleCount = Math.min(180, Math.floor(window.innerWidth / 8));

        // Create particles from three celebratory cannon origins: Left, Right, and Center
        for (let i = 0; i < particleCount; i++) {
            let originX, originY, angleRad, speed;
            const originType = Math.random();

            if (originType < 0.4) {
                // Bottom Left Cannon -> shoots up and towards right
                originX = canvas.width * 0.05 + Math.random() * 60;
                originY = canvas.height * 0.95;
                angleRad = (Math.PI / 180) * (290 + Math.random() * 50); // Shooting up-right
                speed = Math.random() * 16 + 13;
            } else if (originType < 0.8) {
                // Bottom Right Cannon -> shoots up and towards left
                originX = canvas.width * 0.95 - Math.random() * 60;
                originY = canvas.height * 0.95;
                angleRad = (Math.PI / 180) * (200 + Math.random() * 50); // Shooting up-left
                speed = Math.random() * 16 + 13;
            } else {
                // Center Starburst -> bursts radially outward
                originX = canvas.width * 0.5 + (Math.random() - 0.5) * 160;
                originY = canvas.height * 0.45 + (Math.random() - 0.5) * 80;
                angleRad = Math.random() * Math.PI * 2;
                speed = Math.random() * 12 + 5;
            }

            const vx = Math.cos(angleRad) * speed;
            const vy = Math.sin(angleRad) * speed;
            const typeRand = Math.random();
            let shape = 'ribbon';
            if (typeRand < 0.28) shape = 'coin';
            else if (typeRand < 0.45) shape = 'star';

            particles.push({
                x: originX,
                y: originY,
                vx: vx,
                vy: vy,
                gravity: Math.random() * 0.16 + 0.26,
                drag: 0.978,
                w: Math.random() * 10 + 6,
                h: Math.random() * 14 + 8,
                shape: shape,
                color: colors[Math.floor(Math.random() * colors.length)],
                rotation: Math.random() * 360,
                rotSpeed: (Math.random() - 0.5) * 14,
                flip: Math.random() * Math.PI * 2,
                flipSpeed: (Math.random() * 0.12 + 0.06) * (Math.random() > 0.5 ? 1 : -1),
                wobblePhase: Math.random() * Math.PI * 2,
                wobbleSpeed: Math.random() * 0.08 + 0.04
            });
        }

        const startTime = performance.now();

        const render = (now) => {
            const elapsed = now - startTime;
            const progress = Math.min(1, elapsed / durationMs);

            // Compute overall fadeout alpha during the last 35% of the duration
            let globalAlpha = 1;
            if (progress > 0.65) {
                globalAlpha = Math.max(0, 1 - ((progress - 0.65) / 0.35));
            }

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            let hasActiveParticles = false;

            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];

                // Physics update
                p.vx *= p.drag;
                p.vy += p.gravity;
                p.vy *= p.drag;

                p.wobblePhase += p.wobbleSpeed;
                p.x += p.vx + Math.sin(p.wobblePhase) * 0.6;
                p.y += p.vy;

                p.rotation += p.rotSpeed;
                p.flip += p.flipSpeed;

                // Check if particle is still on-screen or within bounds
                if (p.y < canvas.height + 40 && globalAlpha > 0.005) {
                    hasActiveParticles = true;

                    ctx.save();
                    ctx.globalAlpha = globalAlpha;
                    ctx.translate(p.x, p.y);
                    ctx.rotate((p.rotation * Math.PI) / 180);

                    // 3D paper flip scale
                    const scaleX = Math.cos(p.flip);
                    ctx.scale(scaleX, 1);

                    if (p.shape === 'coin') {
                        // Draw shiny golden coin disc
                        ctx.fillStyle = '#f59e0b';
                        ctx.beginPath();
                        ctx.arc(0, 0, p.w * 0.6, 0, Math.PI * 2);
                        ctx.fill();

                        ctx.fillStyle = '#fbbf24';
                        ctx.beginPath();
                        ctx.arc(0, 0, p.w * 0.45, 0, Math.PI * 2);
                        ctx.fill();

                        ctx.strokeStyle = '#fff';
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    } else if (p.shape === 'star') {
                        // Draw sparkling 4-point star
                        ctx.fillStyle = p.color;
                        ctx.beginPath();
                        const rOuter = p.w * 0.6;
                        const rInner = rOuter * 0.35;
                        for (let s = 0; s < 8; s++) {
                            const r = s % 2 === 0 ? rOuter : rInner;
                            const a = (s * Math.PI) / 4;
                            const sx = Math.cos(a) * r;
                            const sy = Math.sin(a) * r;
                            if (s === 0) ctx.moveTo(sx, sy);
                            else ctx.lineTo(sx, sy);
                        }
                        ctx.closePath();
                        ctx.fill();
                    } else {
                        // Fluttering rectangular ribbon
                        ctx.fillStyle = p.color;
                        ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                    }

                    ctx.restore();
                }
            }

            if (progress < 1 && hasActiveParticles) {
                this._confettiAnimId = requestAnimationFrame(render);
            } else {
                // Animation complete: clean up and hide canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                canvas.style.display = 'none';
                if (this._confettiAnimId) {
                    cancelAnimationFrame(this._confettiAnimId);
                    this._confettiAnimId = null;
                }
            }
        };

        this._confettiAnimId = requestAnimationFrame(render);
    },

    /**
     * Coin Perks & VIP State Handlers
     */
    initVipState() {
        const isPermanentVip = localStorage.getItem('moviq_vip_active') === 'true';
        const demoUntil = parseInt(localStorage.getItem('moviq_vip_demo_until') || '0', 10);
        const now = Date.now();
        const isDemoVip = (demoUntil > now);

        const isVip = isPermanentVip || isDemoVip;
        const navVip = document.getElementById('navVipBadge');
        const mobileVip = document.getElementById('mobileNavVipBadge');

        if (this._demoTimer) {
            clearTimeout(this._demoTimer);
            this._demoTimer = null;
        }

        if (isVip) {
            document.body.classList.add('vip-active');
            if (navVip) navVip.style.display = 'inline-flex';
            if (mobileVip) mobileVip.style.display = 'inline-flex';

            // If in active demo mode, schedule countdown reversion
            if (!isPermanentVip && isDemoVip) {
                const remainingMs = Math.max(100, demoUntil - now);
                this._demoTimer = setTimeout(() => {
                    localStorage.removeItem('moviq_vip_demo_until');
                    this.initVipState();
                    if (window.App && App.showToast) {
                        this.showToast('VIP Demo ended. Unlock permanently with 50 coins!', 'info');
                    }
                }, remainingMs);
            }
        } else {
            document.body.classList.remove('vip-active');
            if (navVip) navVip.style.display = 'none';
            if (mobileVip) mobileVip.style.display = 'none';
            if (localStorage.getItem('moviq_vip_demo_until')) {
                localStorage.removeItem('moviq_vip_demo_until');
            }
        }
    },

    openCoinPerksModal(defaultTab = 'vip') {
        const overlay = document.getElementById('coinPerksModalOverlay');
        const balanceEl = document.getElementById('perksShopBalance');
        if (!overlay) return;

        if (balanceEl && typeof CoinSystem !== 'undefined') {
            balanceEl.textContent = CoinSystem.getCoins().toLocaleString();
        }

        // Activate specific tab
        document.querySelectorAll('#perksShopTabs .perks-tab-btn').forEach(b => {
            b.classList.toggle('active', b.dataset.ptab === defaultTab);
        });
        document.querySelectorAll('.perk-tab-content').forEach(c => {
            c.classList.toggle('active', c.id === `ptab-${defaultTab}`);
        });

        // Update VIP button state
        const buyVipBtn = document.getElementById('buyVipBtn');
        const isVip = localStorage.getItem('moviq_vip_active') === 'true';
        if (buyVipBtn) {
            if (isVip) {
                buyVipBtn.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>VIP Active & Equipped</span>';
                buyVipBtn.classList.remove('btn-gradient');
                buyVipBtn.classList.add('btn-secondary');
            } else {
                buyVipBtn.innerHTML = '<i class="fa-solid fa-crown"></i> <span>Unlock & Equip VIP (50 Coins)</span>';
                buyVipBtn.classList.add('btn-gradient');
                buyVipBtn.classList.remove('btn-secondary');
            }
        }

        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    },

    closeCoinPerksModal() {
        const overlay = document.getElementById('coinPerksModalOverlay');
        if (overlay) overlay.classList.remove('open');
        const diceOverlay = document.getElementById('dailyDiceModalOverlay');
        const detailsOverlay = document.getElementById('detailsModalOverlay');
        if ((!diceOverlay || !diceOverlay.classList.contains('open')) && (!detailsOverlay || !detailsOverlay.classList.contains('open'))) {
            document.body.style.overflow = '';
        }
    },

    handleBuyVip() {
        if (typeof CoinSystem === 'undefined') return;
        const isVip = localStorage.getItem('moviq_vip_active') === 'true';
        if (isVip) {
            this.showToast('👑 VIP Status is already active on your account!', 'info');
            return;
        }

        const coins = CoinSystem.getCoins();
        if (coins < 50) {
            this.showToast(`Need 50 Coins to unlock VIP (You have ${coins} coins). Roll the daily dice to earn more!`, 'info');
            return;
        }

        const updatedCoins = CoinSystem.addCoins(-50, 'Unlocked VIP Member Badge & Neon Aura');
        localStorage.removeItem('moviq_vip_demo_until');
        localStorage.setItem('moviq_vip_active', 'true');
        this.initVipState();

        const buyVipBtn = document.getElementById('buyVipBtn');
        if (buyVipBtn) {
            buyVipBtn.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>VIP Active & Equipped</span>';
            buyVipBtn.classList.remove('btn-gradient');
            buyVipBtn.classList.add('btn-secondary');
        }

        const balanceEl = document.getElementById('perksShopBalance');
        if (balanceEl) balanceEl.textContent = updatedCoins.toLocaleString();

        this.playDiceSoundEffect('win');
        this.launchConfetti();
        this.showToast('👑 VIP Member Badge & Golden Neon Aura Activated!', 'success');
    },

    handleDemoVip() {
        // Persist a 15-second demo timestamp so navigating to watch.php retains VIP mode!
        const demoDurationMs = 15000;
        const demoExpiresAt = Date.now() + demoDurationMs;
        localStorage.setItem('moviq_vip_demo_until', demoExpiresAt.toString());

        this.initVipState();
        this.playDiceSoundEffect('win');
        this.showToast('👑 15-Second VIP Demo Activated! Browse anywhere (including Watch page) to see Gold Gradient.', 'success');
    },

    handleBuyExtraRoll() {
        if (typeof CoinSystem === 'undefined') return;
        const coins = CoinSystem.getCoins();
        if (coins < 15) {
            this.showToast(`Need 15 Coins to buy an extra roll (You have ${coins} coins).`, 'info');
            return;
        }

        const updatedCoins = CoinSystem.addCoins(-15, 'Purchased +1 Extra Movie Dice Roll');
        const state = CoinSystem.getDailyState();
        state.rollsLeft = (state.rollsLeft || 0) + 1;
        state.playedToday = false;
        localStorage.setItem(CoinSystem.STORAGE_KEY_DICE, JSON.stringify(state));

        const balanceEl = document.getElementById('perksShopBalance');
        if (balanceEl) balanceEl.textContent = updatedCoins.toLocaleString();

        this.playDiceSoundEffect('win');
        this.showToast('🎲 +1 Extra Roll Added! Opening Movie Dice...', 'success');

        this.closeCoinPerksModal();
        setTimeout(() => {
            this.openDailyDiceModal();
        }, 300);
    },

    async handleOpenMysteryBox() {
        if (typeof CoinSystem === 'undefined') return;
        const coins = CoinSystem.getCoins();
        if (coins < 20) {
            this.showToast(`Need 20 Coins to open Mystery Gem Box (You have ${coins} coins).`, 'info');
            return;
        }

        const updatedCoins = CoinSystem.addCoins(-20, 'Opened Mystery Gem Box');
        const balanceEl = document.getElementById('perksShopBalance');
        if (balanceEl) balanceEl.textContent = updatedCoins.toLocaleString();

        this.playDiceSoundEffect('win');
        this.launchConfetti();

        // Pick a top rated cinema masterpiece
        const topGems = [157336, 680, 278, 238, 424, 129, 670, 872585, 496243, 155];
        const randomId = topGems[Math.floor(Math.random() * topGems.length)];

        this.closeCoinPerksModal();
        this.showToast('🎁 Mystery Gem Unlocked! Loading masterpiece...', 'success');
        setTimeout(() => {
            this.openDetails(randomId, 'movie');
        }, 400);
    },

    handleBuyWatchlistSlots() {
        if (typeof CoinSystem === 'undefined' || typeof Watchlist === 'undefined') return;
        const coins = CoinSystem.getCoins();
        const cost = 50;
        if (coins < cost) {
            this.showToast(`Need ${cost} Coins to buy +2 Watchlist Space (You have ${coins} coins). Roll the daily dice to earn more!`, 'info');
            return;
        }

        const updatedCoins = CoinSystem.addCoins(-cost, 'Purchased +2 Watchlist Space');
        const newLimit = Watchlist.expandLimit(2);

        this.playDiceSoundEffect('win');
        this.launchConfetti();
        this.showToast(`🎉 +2 Watchlist Space Unlocked! New Capacity: ${newLimit} items.`, 'success');

        const capacityEl = document.getElementById('shopWatchlistCapacity');
        if (capacityEl) capacityEl.textContent = newLimit;
        const limitCountEl = document.getElementById('watchlistLimitCount');
        if (limitCountEl) limitCountEl.textContent = newLimit;
        const balanceEl = document.getElementById('perksShopBalance');
        if (balanceEl) balanceEl.textContent = updatedCoins.toLocaleString();
    }
};

// Start application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});

window.App = App;
