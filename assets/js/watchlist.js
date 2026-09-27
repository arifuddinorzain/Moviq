/**
 * Moviq — Watchlist & Library Manager
 * Manages user's saved movies and TV shows in localStorage with reactive updates and capacity limits.
 * Default capacity is 2 slots; additional space (+2 slots) can be purchased with 50 Moviq Coins.
 */

const WATCHLIST_STORAGE_KEY = 'moviq_watchlist_v1';
const WATCHLIST_LIMIT_KEY = 'moviq_watchlist_limit';

const Watchlist = {
    DEFAULT_LIMIT: 2,
    SLOTS_PER_PURCHASE: 2,
    PRICE_PER_EXPANSION: 50,
    listeners: [],

    /**
     * Get user's current maximum watchlist capacity limit (default: 2)
     */
    getLimit() {
        try {
            const stored = localStorage.getItem(WATCHLIST_LIMIT_KEY);
            if (stored === null) return this.DEFAULT_LIMIT;
            const val = parseInt(stored, 10);
            return isNaN(val) || val < this.DEFAULT_LIMIT ? this.DEFAULT_LIMIT : val;
        } catch (e) {
            return this.DEFAULT_LIMIT;
        }
    },

    /**
     * Expand watchlist limit by additional slots (e.g. +2 slots)
     */
    expandLimit(slots = 2) {
        const current = this.getLimit();
        const updated = current + slots;
        try {
            localStorage.setItem(WATCHLIST_LIMIT_KEY, updated.toString());
        } catch (e) {
            console.error('Failed to save watchlist limit:', e);
        }
        this.notify();
        return updated;
    },

    /**
     * Check if watchlist has reached maximum capacity
     */
    isFull() {
        return this.getItems().length >= this.getLimit();
    },

    /**
     * Get all items from localStorage
     */
    getItems() {
        try {
            const raw = localStorage.getItem(WATCHLIST_STORAGE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            console.error('Failed to parse watchlist:', e);
            return [];
        }
    },

    /**
     * Save items array to localStorage
     */
    saveItems(items) {
        try {
            localStorage.setItem(WATCHLIST_STORAGE_KEY, JSON.stringify(items));
            this.notify();
        } catch (e) {
            console.error('Failed to save watchlist:', e);
        }
    },

    /**
     * Check if a specific title is in the watchlist
     */
    has(id, type = 'movie') {
        const items = this.getItems();
        return items.some(item => String(item.id) === String(id) && (item.media_type || 'movie') === type);
    },

    /**
     * Add title to watchlist with capacity check
     */
    add(item) {
        if (!item || !item.id) return { success: false, reason: 'invalid' };
        const items = this.getItems();
        const type = item.media_type || (item.first_air_date ? 'tv' : 'movie');

        if (this.has(item.id, type)) {
            return { success: false, reason: 'exists' };
        }

        const limit = this.getLimit();
        if (items.length >= limit) {
            return { success: false, reason: 'limit_reached', limit, count: items.length };
        }

        const cleanItem = {
            id: item.id,
            title: item.title || item.name || 'Untitled',
            poster_path: item.poster_path || null,
            backdrop_path: item.backdrop_path || null,
            vote_average: item.vote_average || 0,
            release_date: item.release_date || item.first_air_date || '',
            media_type: type,
            overview: item.overview || '',
            added_at: new Date().toISOString()
        };

        items.unshift(cleanItem);
        this.saveItems(items);
        return { success: true, item: cleanItem };
    },

    /**
     * Remove title from watchlist
     */
    remove(id, type = 'movie') {
        const items = this.getItems();
        const filtered = items.filter(item => !(String(item.id) === String(id) && (item.media_type || 'movie') === type));
        if (filtered.length !== items.length) {
            this.saveItems(filtered);
            return true;
        }
        return false;
    },

    /**
     * Toggle title presence in watchlist with limit enforcement
     */
    toggle(item) {
        const type = item.media_type || (item.first_air_date ? 'tv' : 'movie');
        if (this.has(item.id, type)) {
            this.remove(item.id, type);
            return { added: false, item };
        } else {
            const items = this.getItems();
            const limit = this.getLimit();
            if (items.length >= limit) {
                return { added: false, limitReached: true, limit, count: items.length, item };
            }
            const addRes = this.add(item);
            return { added: addRes.success, item, ...addRes };
        }
    },

    /**
     * Clear all items in watchlist
     */
    clear() {
        this.saveItems([]);
    },

    /**
     * Get item counts and capacity details
     */
    getCounts() {
        const items = this.getItems();
        const total = items.length;
        const movies = items.filter(i => (i.media_type || 'movie') === 'movie').length;
        const tv = items.filter(i => i.media_type === 'tv').length;
        const limit = this.getLimit();
        const isFull = total >= limit;
        return { total, movies, tv, limit, isFull };
    },

    /**
     * Subscribe to changes
     */
    subscribe(fn) {
        if (typeof fn === 'function') {
            this.listeners.push(fn);
        }
    },

    /**
     * Notify all listeners of changes
     */
    notify() {
        const counts = this.getCounts();
        const items = this.getItems();
        this.listeners.forEach(fn => {
            try {
                fn({ counts, items });
            } catch (e) {
                console.error('Watchlist listener error:', e);
            }
        });
    }
};

window.Watchlist = Watchlist;
