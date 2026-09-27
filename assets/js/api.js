/**
 * Moviq — TMDB API Client
 * Wraps communication with the PHP backend proxy (api.php)
 */

const TMDB_IMG_URL = 'https://image.tmdb.org/t/p';

const Api = {
    baseUrl: 'api.php',

    /**
     * General fetch request helper with error handling
     */
    async request(action, params = {}) {
        const queryParams = new URLSearchParams({ action, ...params });
        try {
            const response = await fetch(`${this.baseUrl}?${queryParams.toString()}`);
            if (!response.ok) {
                throw new Error(`HTTP Error ${response.status}`);
            }
            const result = await response.json();
            if (!result.success) {
                console.warn(`API Warning [${action}]:`, result.error);
                return null;
            }
            return result.data;
        } catch (error) {
            console.error(`API Fetch Error [${action}]:`, error);
            return null;
        }
    },

    /**
     * Get Hero Slider curated slides
     */
    async getHeroSlides() {
        return await this.request('hero_slides');
    },

    /**
     * Get Trending titles
     */
    async getTrending(type = 'all', time = 'day', page = 1) {
        return await this.request('trending', { type, time, page });
    },

    /**
     * Get Popular Movies
     */
    async getPopularMovies(page = 1) {
        return await this.request('popular_movies', { page });
    },

    /**
     * Get Top Rated Movies
     */
    async getTopRatedMovies(page = 1) {
        return await this.request('top_rated_movies', { page });
    },

    /**
     * Get In Theaters / Now Playing
     */
    async getNowPlaying(page = 1) {
        return await this.request('now_playing', { page });
    },

    /**
     * Get Upcoming Movies
     */
    async getUpcoming(page = 1) {
        return await this.request('upcoming_movies', { page });
    },

    /**
     * Get Trending Korean Movies (K-Cinema)
     */
    async getKoreanMovies(page = 1) {
        return await this.request('korean_movies', { page });
    },

    /**
     * Get Popular TV Shows
     */
    async getPopularTv(page = 1) {
        return await this.request('popular_tv', { page });
    },

    /**
     * Get Top Rated TV Shows
     */
    async getTopRatedTv(page = 1) {
        return await this.request('top_rated_tv', { page });
    },

    /**
     * Get TV Shows currently airing
     */
    async getOnTheAirTv(page = 1) {
        return await this.request('on_the_air_tv', { page });
    },

    /**
     * Get Live TV Channels (IPTV)
     */
    async getLiveTvChannels(category = 'all', search = '') {
        const res = await this.request('livetv_channels', { category, search });
        if (!res) return [];
        if (Array.isArray(res)) return res;
        if (res.channels && Array.isArray(res.channels)) return res.channels;
        return [];
    },

    /**
     * Get all genre definitions
     */
    async getGenres() {
        return await this.request('genres');
    },

    /**
     * Discover titles with custom filters
     */
    async discover(params = {}) {
        return await this.request('discover', params);
    },

    /**
     * Search movies, TV shows, actors
     */
    async search(query, searchType = 'multi', page = 1) {
        return await this.request('search', { query, search_type: searchType, page });
    },

    /**
     * Get full details for a Movie or TV Show
     */
    async getDetails(id, type = 'movie') {
        return await this.request('details', { id, type });
    },

    /**
     * Get Person (Actor/Director) details
     */
    async getPerson(id) {
        return await this.request('person', { id });
    },

    /**
     * Get TV Season details with full episode list
     */
    async getTvSeason(id, season = 1) {
        return await this.request('tv_season', { id, season });
    },

    /**
     * Helper to get full image URL with reliable SVG fallbacks
     */
    getImageUrl(path, size = 'w500') {
        if (!path) return this.getPosterPlaceholder();
        return `${TMDB_IMG_URL}/${size}${path}`;
    },

    getProfileUrl(path, size = 'w185') {
        if (!path) return this.getProfilePlaceholder();
        return `${TMDB_IMG_URL}/${size}${path}`;
    },

    getProfilePlaceholder() {
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200"><defs><linearGradient id="avatarBgGrad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#1e293b"/><stop offset="50%" stop-color="#0f172a"/><stop offset="100%" stop-color="#090d16"/></linearGradient><linearGradient id="avatarSilhouette" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" stop-color="#f8fafc"/><stop offset="100%" stop-color="#94a3b8"/></linearGradient><radialGradient id="avatarNeonGlow" cx="50%" cy="40%" r="60%"><stop offset="0%" stop-color="rgba(255, 0, 128, 0.35)"/><stop offset="60%" stop-color="rgba(124, 58, 237, 0.2)"/><stop offset="100%" stop-color="rgba(0,0,0,0)"/></radialGradient></defs><rect width="200" height="200" rx="100" fill="url(#avatarBgGrad)"/><circle cx="100" cy="100" r="100" fill="url(#avatarNeonGlow)"/><circle cx="100" cy="100" r="97" fill="none" stroke="rgba(255, 255, 255, 0.25)" stroke-width="3"/><circle cx="100" cy="70" r="32" fill="url(#avatarSilhouette)"/><path d="M42 165 C42 120 65 108 100 108 C135 108 158 120 158 165 C158 178 144 182 100 182 C56 182 42 178 42 165 Z" fill="url(#avatarSilhouette)"/><circle cx="148" cy="148" r="24" fill="#ff0080" stroke="#090d16" stroke-width="3"/><path d="M144 140 C144 136 146 134 148 134 C151 134 153 136 153 138 C153 141 149 142.5 148 144.5 L148 148 M148 153 L148 155" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"/></svg>`;
        return "data:image/svg+xml;utf8," + encodeURIComponent(svg);
    },

    getPosterPlaceholder() {
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 450" width="300" height="450"><defs><linearGradient id="pGrad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#1e293b"/><stop offset="50%" stop-color="#0f172a"/><stop offset="100%" stop-color="#020617"/></linearGradient><radialGradient id="pGlow" cx="50%" cy="35%" r="60%"><stop offset="0%" stop-color="rgba(255,0,128,0.25)"/><stop offset="100%" stop-color="rgba(0,0,0,0)"/></radialGradient></defs><rect width="300" height="450" fill="url(#pGrad)"/><rect width="300" height="450" fill="url(#pGlow)"/><rect x="12" y="12" width="276" height="426" rx="12" fill="none" stroke="rgba(255,255,255,0.12)" stroke-width="2"/><circle cx="150" cy="175" r="48" fill="rgba(255,255,255,0.06)" stroke="rgba(255,255,255,0.15)" stroke-width="2"/><path d="M138 150 L172 175 L138 200 Z" fill="#ff0080"/><text x="150" y="260" font-family="system-ui, -apple-system, sans-serif" font-size="18" font-weight="800" fill="#f1f5f9" text-anchor="middle" letter-spacing="3">MOVIQ</text><text x="150" y="288" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#94a3b8" text-anchor="middle">No Poster Available</text></svg>`;
        return "data:image/svg+xml;utf8," + encodeURIComponent(svg);
    },

    /**
     * Format minutes to 1h 45m
     */
    formatRuntime(mins) {
        if (!mins || mins <= 0) return 'N/A';
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        return h > 0 ? (m > 0 ? `${h}h ${m}m` : `${h}h`) : `${m}m`;
    },

    /**
     * Format date string to Year or Full readable date
     */
    formatYear(dateStr) {
        if (!dateStr) return 'TBA';
        return dateStr.substring(0, 4);
    },

    formatFullDate(dateStr) {
        if (!dateStr) return 'TBA';
        const date = new Date(dateStr);
        if (isNaN(date.getTime())) return dateStr;
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }
};

window.Api = Api;
