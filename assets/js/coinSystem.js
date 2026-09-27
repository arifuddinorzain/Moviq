/**
 * Moviq — Daily Coin & Movie Dice System
 * Handles coin balance persistence (localStorage), daily dice rolls,
 * target movie generation, roll animations, and event subscriptions.
 */

const CoinSystem = {
    STORAGE_KEY_COINS: 'moviq_coins',
    STORAGE_KEY_DICE: 'moviq_daily_dice_state',
    STORAGE_KEY_HISTORY: 'moviq_coin_history',
    DAILY_ROLLS_PER_DAY: 2,
    COINS_PER_MATCH: 25,
    COINS_CONSOLATION: 5,
    WIN_RATE_PROBABILITY: 0.50, // 50% chance of direct target match on each roll!

    // Predefined curated movie catalog for dice faces (with reliable TMDB backdrop & poster images)
    MOVIE_DICE_POOL: [
        {
            id: 157336,
            title: 'Interstellar',
            genre: 'Sci-Fi / Space',
            icon: '🚀',
            poster: 'https://image.tmdb.org/t/p/w342/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
            year: '2014',
            color: '#00f2fe'
        },
        {
            id: 872585,
            title: 'Oppenheimer',
            genre: 'Drama / History',
            icon: '⚛️',
            poster: 'https://image.tmdb.org/t/p/w342/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
            year: '2023',
            color: '#f59e0b'
        },
        {
            id: 670,
            title: 'Oldboy',
            genre: 'Thriller / K-Cinema',
            icon: '🇰🇷',
            poster: 'https://image.tmdb.org/t/p/w342/pWDtjs568ZfOTMbURQBYuT4Qxka.jpg',
            year: '2003',
            color: '#ec4899'
        },
        {
            id: 569094,
            title: 'Spider-Verse',
            genre: 'Animation / Action',
            icon: '🕷️',
            poster: 'https://image.tmdb.org/t/p/w342/8Vt6mWEReuy4Of61Lnj5Xj704m8.jpg',
            year: '2023',
            color: '#ef4444'
        },
        {
            id: 603,
            title: 'The Matrix',
            genre: 'Sci-Fi / Cyberpunk',
            icon: '🕶️',
            poster: 'https://image.tmdb.org/t/p/w342/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg',
            year: '1999',
            color: '#10b981'
        },
        {
            id: 299534,
            title: 'Endgame',
            genre: 'Action / Superhero',
            icon: '⚡',
            poster: 'https://image.tmdb.org/t/p/w342/or06FN3Dka5tukK1e9sl16pB3iy.jpg',
            year: '2019',
            color: '#a855f7'
        }
    ],

    subscribers: [],

    /**
     * Initialize Coin System (default starting balance: 25 coins)
     */
    init() {
        const stored = localStorage.getItem(this.STORAGE_KEY_COINS);
        if (stored === null || stored === '0') {
            localStorage.setItem(this.STORAGE_KEY_COINS, '25');
        }
        this.notify();
    },

    /**
     * Get current user coins (default 25)
     */
    getCoins() {
        const stored = localStorage.getItem(this.STORAGE_KEY_COINS);
        if (stored === null) {
            localStorage.setItem(this.STORAGE_KEY_COINS, '25');
            return 25;
        }
        const val = parseInt(stored, 10);
        return isNaN(val) ? 25 : val;
    },

    /**
     * Add coins with reason and audit log
     */
    addCoins(amount, reason = 'Daily Movie Dice Match') {
        const current = this.getCoins();
        const updated = Math.max(0, current + amount);
        localStorage.setItem(this.STORAGE_KEY_COINS, updated.toString());

        // Log transaction history
        try {
            const history = JSON.parse(localStorage.getItem(this.STORAGE_KEY_HISTORY) || '[]');
            history.unshift({
                amount,
                reason,
                balance: updated,
                date: new Date().toISOString()
            });
            localStorage.setItem(this.STORAGE_KEY_HISTORY, JSON.stringify(history.slice(0, 30)));
        } catch (e) {
            console.warn('Coin history log error:', e);
        }

        this.notify({ type: 'coin_added', amount, newBalance: updated, reason });
        return updated;
    },

    /**
     * Subscribe to coin & dice state updates
     */
    subscribe(callback) {
        if (typeof callback === 'function') {
            this.subscribers.push(callback);
        }
    },

    notify(payload = {}) {
        const coins = this.getCoins();
        const dailyState = this.getDailyState();
        this.subscribers.forEach(cb => {
            try {
                cb({ coins, dailyState, payload });
            } catch (err) {
                console.error('Coin subscriber error:', err);
            }
        });
    },

    /**
     * Get date key formatted as YYYY-MM-DD in local time
     */
    getTodayDateKey() {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        const d = String(now.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    },

    /**
     * Get today's daily 6-face pool and target face index (seeded per day)
     */
    getTodaySetup() {
        const dateKey = this.getTodayDateKey();
        // Compute simple deterministic hash from date string
        let hash = 0;
        for (let i = 0; i < dateKey.length; i++) {
            hash = ((hash << 5) - hash) + dateKey.charCodeAt(i);
            hash |= 0;
        }
        const absHash = Math.abs(hash);

        // Assign face 1..6 with movies from pool
        const faces = this.MOVIE_DICE_POOL.map((movie, idx) => ({
            ...movie,
            faceNumber: idx + 1,
            faceName: ['Front', 'Back', 'Right', 'Left', 'Top', 'Bottom'][idx]
        }));

        // Determine target face index (1..6) for today
        const targetFaceNumber = (absHash % 6) + 1;
        const targetMovie = faces.find(f => f.faceNumber === targetFaceNumber);

        return {
            dateKey,
            faces,
            targetFaceNumber,
            targetMovie
        };
    },

    /**
     * Get daily dice roll state for user
     */
    getDailyState() {
        const today = this.getTodayDateKey();
        let state = null;
        try {
            state = JSON.parse(localStorage.getItem(this.STORAGE_KEY_DICE));
        } catch (e) {
            state = null;
        }

        if (!state || state.date !== today) {
            // New day or first time
            const setup = this.getTodaySetup();
            state = {
                date: today,
                playedToday: false,
                rollsLeft: this.DAILY_ROLLS_PER_DAY,
                maxRolls: this.DAILY_ROLLS_PER_DAY,
                streak: state && state.streak ? (this.isConsecutiveDay(state.date, today) ? state.streak : 0) : 0,
                targetFaceNumber: setup.targetFaceNumber,
                rolledFaceNumber: null,
                isMatch: null,
                coinsWon: 0,
                totalCoinsWonToday: 0
            };
            localStorage.setItem(this.STORAGE_KEY_DICE, JSON.stringify(state));
        }

        return state;
    },

    /**
     * Check if day2 is exactly 1 day after day1
     */
    isConsecutiveDay(day1, day2) {
        if (!day1 || !day2) return false;
        const d1 = new Date(day1);
        const d2 = new Date(day2);
        const diffTime = d2.getTime() - d1.getTime();
        const diffDays = Math.round(diffTime / (1000 * 3600 * 24));
        return diffDays === 1;
    },

    /**
     * Roll the Movie Dice
     * High win rate (50% direct hit), 2 rolls/day, and +5 consolation prize on every roll!
     * Returns: { rolledFaceNumber, rolledMovie, isMatch, coinsWon, newBalance, rollsLeft, streak }
     */
    rollDailyDice() {
        const state = this.getDailyState();
        if ((state.rollsLeft || 0) <= 0) {
            return {
                alreadyPlayed: true,
                state
            };
        }

        const setup = this.getTodaySetup();
        
        // High win-rate roll algorithm (50% direct target hit chance)
        let rolledFaceNumber;
        const willMatch = Math.random() < this.WIN_RATE_PROBABILITY;
        if (willMatch) {
            rolledFaceNumber = setup.targetFaceNumber;
        } else {
            // Pick from the other 5 faces
            const otherFaces = [1, 2, 3, 4, 5, 6].filter(n => n !== setup.targetFaceNumber);
            rolledFaceNumber = otherFaces[Math.floor(Math.random() * otherFaces.length)];
        }

        const rolledMovie = setup.faces.find(f => f.faceNumber === rolledFaceNumber);
        const isMatch = rolledFaceNumber === setup.targetFaceNumber;

        let coinsWon = 0;
        let newBalance = this.getCoins();

        if (isMatch) {
            coinsWon = this.COINS_PER_MATCH;
            newBalance = this.addCoins(coinsWon, `Daily Movie Dice Match (${setup.targetMovie.title})`);
            state.streak = (state.streak || 0) + 1;
        } else {
            // Consolation prize: +5 coins so every roll rewards the user!
            coinsWon = this.COINS_CONSOLATION;
            newBalance = this.addCoins(coinsWon, `Daily Dice Roll Bonus (${rolledMovie.title})`);
            state.streak = state.streak || 0;
        }

        state.rollsLeft = Math.max(0, (state.rollsLeft || this.DAILY_ROLLS_PER_DAY) - 1);
        state.playedToday = state.rollsLeft <= 0;
        state.rolledFaceNumber = rolledFaceNumber;
        state.isMatch = isMatch;
        state.coinsWon = coinsWon;
        state.totalCoinsWonToday = (state.totalCoinsWonToday || 0) + coinsWon;

        localStorage.setItem(this.STORAGE_KEY_DICE, JSON.stringify(state));
        this.notify({ type: 'dice_rolled', result: { rolledFaceNumber, isMatch, coinsWon, rollsLeft: state.rollsLeft } });

        return {
            alreadyPlayed: false,
            rolledFaceNumber,
            rolledMovie,
            targetMovie: setup.targetMovie,
            isMatch,
            coinsWon,
            newBalance,
            rollsLeft: state.rollsLeft,
            streak: state.streak
        };
    },

    /**
     * Reset today's roll (Developer helper / testing feature)
     */
    resetTodayRollForTesting() {
        localStorage.removeItem(this.STORAGE_KEY_DICE);
        this.notify();
    },

    /**
     * Formats countdown string to next midnight reset
     */
    getTimeUntilReset() {
        const now = new Date();
        const midnight = new Date(now);
        midnight.setHours(24, 0, 0, 0);
        const diffMs = midnight - now;

        const hours = Math.floor(diffMs / (1000 * 60 * 60));
        const mins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
        const secs = Math.floor((diffMs % (1000 * 60)) / 1000);

        return {
            hours: String(hours).padStart(2, '0'),
            mins: String(mins).padStart(2, '0'),
            secs: String(secs).padStart(2, '0'),
            formatted: `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`
        };
    }
};

// Initialize coin system on script load
CoinSystem.init();
