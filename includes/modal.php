<!-- Movie / TV Show Details Modal -->
<div class="modal-overlay" id="detailsModalOverlay" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="modal-container details-modal-container" id="detailsModalContainer">
        <!-- Close Button -->
        <button class="modal-close-btn" id="detailsModalCloseBtn" aria-label="Close modal">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Dynamic Content Body -->
        <div class="details-modal-body" id="detailsModalBody">
            <!-- Populated dynamically via JS -->
            <div class="details-loading-spinner">
                <div class="spinner-ring"></div>
                <span>Loading cinematic details...</span>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Video / Trailer Player Modal -->
<div class="modal-overlay trailer-modal-overlay" id="trailerModalOverlay" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="modal-container trailer-modal-container">
        <div class="trailer-modal-header">
            <div class="trailer-title-info">
                <i class="fa-brands fa-youtube youtube-icon"></i>
                <h3 id="trailerModalTitle">Official Trailer</h3>
            </div>
            <button class="modal-close-btn" id="trailerModalCloseBtn" aria-label="Close trailer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="trailer-player-wrapper">
            <div class="video-responsive-container" id="videoContainer">
                <!-- YouTube iframe will be inserted here -->
            </div>
        </div>
        <div class="trailer-alternate-list" id="trailerAlternateList">
            <!-- Alternative clips / trailers tabs -->
        </div>
    </div>
</div>

<!-- Person / Actor Profile Modal -->
<div class="modal-overlay person-modal-overlay" id="personModalOverlay" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="modal-container person-modal-container">
        <button class="modal-close-btn" id="personModalCloseBtn" aria-label="Close profile">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="person-modal-body" id="personModalBody">
            <!-- Dynamic Actor Profile -->
        </div>
    </div>
</div>

<!-- Daily Movie Dice & Coin Quest Modal -->
<div class="modal-overlay dice-modal-overlay" id="dailyDiceModalOverlay" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="modal-container dice-modal-container" id="dailyDiceModalContainer">
        <!-- Close Button -->
        <button class="modal-close-btn" id="dailyDiceModalCloseBtn" aria-label="Close modal">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Modal Header -->
        <div class="dice-modal-header">
            <div class="dice-modal-title-row">
                <span class="dice-badge-icon">🎲</span>
                <div>
                    <h2 class="dice-modal-title">Daily Movie Dice Quest</h2>
                    <p class="dice-modal-subtitle">Roll the 3D Movie Dice (2 free rolls daily!). Match today's target for <strong class="coin-highlight">+25 Coins</strong>, or get <strong class="coin-highlight">+5 Coins</strong> on any roll!</p>
                </div>
            </div>
            
            <!-- User Status Pill Bar -->
            <div class="dice-user-stats-bar">
                <div class="dice-stat-chip coin-chip">
                    <span class="chip-label">Balance:</span>
                    <strong class="chip-val"><span class="coin-icon">🪙</span> <span id="diceModalCoinCount">25</span> Coins</strong>
                </div>
                <div class="dice-stat-chip rolls-chip">
                    <span class="chip-label">Rolls Left:</span>
                    <strong class="chip-val"><span id="diceRollsLeftCount">2</span> / 2 🎲</strong>
                </div>
                <div class="dice-stat-chip streak-chip">
                    <span class="chip-label">Daily Streak:</span>
                    <strong class="chip-val"><span id="diceStreakCount">0</span> Days 🔥</strong>
                </div>
                <div class="dice-stat-chip timer-chip">
                    <span class="chip-label">Reset:</span>
                    <strong class="chip-val" id="diceCountdownTimer">--:--:--</strong>
                </div>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="dice-modal-body">
            <!-- Left/Top: Target Movie Bounty Card -->
            <div class="dice-target-card" id="diceTargetCard">
                <div class="target-card-badge">
                    <i class="fa-solid fa-crosshairs"></i> TODAY'S TARGET TO MATCH
                </div>
                <div class="target-card-content">
                    <img class="target-movie-poster" id="targetMoviePoster" src="https://image.tmdb.org/t/p/w342/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg" alt="Target Movie">
                    <div class="target-movie-info">
                        <span class="target-face-tag" id="targetFaceTag">Dice Face #1 🚀</span>
                        <h3 class="target-movie-title" id="targetMovieTitle">Interstellar</h3>
                        <p class="target-movie-genre" id="targetMovieGenre">Sci-Fi / Space</p>
                        <div class="target-bounty-badge">
                            <span class="coin-icon">🪙</span>
                            <span>Bounty: <strong>+25 Coins</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3D Dice Arena -->
            <div class="dice-arena-wrapper">
                <div class="dice-arena-pedestal">
                    <div class="pedestal-glow"></div>
                    <div class="dice-scene" id="diceScene">
                        <div class="dice-cube" id="movieDiceCube">
                            <!-- 6 Faces rendered with dynamic styling -->
                            <div class="dice-face face-front" data-face="1">
                                <div class="face-content">
                                    <div class="face-number">1</div>
                                    <div class="face-icon">🚀</div>
                                    <div class="face-title">Interstellar</div>
                                </div>
                            </div>
                            <div class="dice-face face-back" data-face="2">
                                <div class="face-content">
                                    <div class="face-number">2</div>
                                    <div class="face-icon">⚛️</div>
                                    <div class="face-title">Oppenheimer</div>
                                </div>
                            </div>
                            <div class="dice-face face-right" data-face="3">
                                <div class="face-content">
                                    <div class="face-number">3</div>
                                    <div class="face-icon">🇰🇷</div>
                                    <div class="face-title">Oldboy</div>
                                </div>
                            </div>
                            <div class="dice-face face-left" data-face="4">
                                <div class="face-content">
                                    <div class="face-number">4</div>
                                    <div class="face-icon">🕷️</div>
                                    <div class="face-title">Spider-Verse</div>
                                </div>
                            </div>
                            <div class="dice-face face-top" data-face="5">
                                <div class="face-content">
                                    <div class="face-number">5</div>
                                    <div class="face-icon">🕶️</div>
                                    <div class="face-title">The Matrix</div>
                                </div>
                            </div>
                            <div class="dice-face face-bottom" data-face="6">
                                <div class="face-content">
                                    <div class="face-number">6</div>
                                    <div class="face-icon">⚡</div>
                                    <div class="face-title">Endgame</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dice Action & Results Banner -->
                <div class="dice-action-container">
                    <button class="btn btn-gradient btn-lg roll-dice-btn" id="rollMovieDiceBtn">
                        <i class="fa-solid fa-dice"></i> <span>Roll Daily Movie Dice</span>
                    </button>
                    <div class="dice-result-banner" id="diceResultBanner" style="display: none;">
                        <!-- Dynamic Result Details -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer: Coin Perks -->
        <div class="dice-modal-footer">
            <h4 class="perks-title"><i class="fa-solid fa-gem"></i> What Can You Do With Moviq Coins? <span class="perks-hint-tag">Tap to Preview & Redeem</span></h4>
            <div class="perks-grid">
                <button class="perk-item perk-btn" id="perkVipBtn" title="Click to preview VIP Badge">
                    <span class="perk-icon">👑</span>
                    <span class="perk-text">VIP Member Badge & Neon Aura</span>
                    <span class="perk-action-chip">Preview</span>
                </button>
                <button class="perk-item perk-btn" id="perkLuckyBtn" title="Click to buy extra rolls">
                    <span class="perk-icon">✨</span>
                    <span class="perk-text">Lucky Roll Bonus & Mystery Perks</span>
                    <span class="perk-action-chip">Redeem</span>
                </button>
                <button class="perk-item perk-btn" id="perkStreakBtn" title="Click to view Cinema Ranks">
                    <span class="perk-icon">🍿</span>
                    <span class="perk-text">Daily Cinema Fan Streaks</span>
                    <span class="perk-action-chip">Ranks</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Coin Perks & Rewards Shop Modal -->
<div class="modal-overlay perks-modal-overlay" id="coinPerksModalOverlay" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="modal-container perks-modal-container" id="coinPerksModalContainer">
        <!-- Close Button -->
        <button class="modal-close-btn" id="coinPerksModalCloseBtn" aria-label="Close modal">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Header -->
        <div class="perks-modal-header">
            <div class="perks-header-left">
                <span class="shop-badge"><i class="fa-solid fa-gem"></i> Coin Rewards Shop</span>
                <h2 class="perks-modal-title">Use Your Moviq Coins</h2>
                <p class="perks-modal-sub">Spend earned coins on VIP perks, instant extra rolls, and cinema ranks.</p>
            </div>
            <div class="perks-balance-badge">
                <span class="coin-icon">🪙</span>
                <span class="balance-text"><strong id="perksShopBalance">25</strong> Coins</span>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="perks-tabs-bar" id="perksShopTabs">
            <button class="perks-tab-btn active" data-ptab="vip"><i class="fa-solid fa-crown"></i> 1. VIP Badge & Aura</button>
            <button class="perks-tab-btn" data-ptab="rolls"><i class="fa-solid fa-dice"></i> 2. Extra Rolls & Perks</button>
            <button class="perks-tab-btn" data-ptab="ranks"><i class="fa-solid fa-medal"></i> 3. Fan Ranks & Streaks</button>
        </div>

        <!-- Tab 1: VIP Member Badge & Neon Aura -->
        <div class="perk-tab-content active" id="ptab-vip">
            <div class="perk-showcase-grid">
                <div class="perk-preview-card">
                    <div class="preview-card-header">
                        <span class="preview-tag">LIVE PREVIEW</span>
                    </div>
                    <!-- Live Logo Preview with VIP Badge -->
                    <div class="vip-live-preview-box" id="vipLivePreviewBox">
                        <div class="brand-logo" style="margin-bottom: 0.8rem; justify-content: center;">
                            <div class="logo-icon-box"><i class="fa-solid fa-play"></i></div>
                            <span class="logo-text">MOVI<span class="logo-gradient">Q</span></span>
                            <span class="vip-crown-badge"><i class="fa-solid fa-crown"></i> VIP</span>
                        </div>
                        <div class="vip-aura-demo-card">
                            <i class="fa-solid fa-film" style="color: #fbbf24; font-size: 1.6rem; margin-bottom: 0.3rem;"></i>
                            <span style="font-weight: 700; color: #fbbf24;">Cyberpunk Golden Neon Aura Active</span>
                            <div class="vip-btn-preview-demo" style="margin-top: 0.8rem;">
                                <div class="btn btn-gradient btn-sm" style="pointer-events: none;">
                                    <i class="fa-solid fa-play"></i> Watch Now (Gold VIP)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="perk-details-card">
                    <h3 class="perk-details-title">👑 VIP Member Badge & Gold Aura</h3>
                    <ul class="perk-features-list">
                        <li><i class="fa-solid fa-check"></i> <strong>Animated Gold Gradient Buttons</strong> across the entire website with neon glow.</li>
                        <li><i class="fa-solid fa-check"></i> Glowing <strong>VIP Crown Badge</strong> in top navbar and mobile menu.</li>
                        <li><i class="fa-solid fa-check"></i> <strong>Cyberpunk Neon Aura</strong> glowing border on cinema player & hero banner.</li>
                        <li><i class="fa-solid fa-check"></i> Exclusive VIP recognition in Watchlist & Cinema Fan Ranks.</li>
                    </ul>
                    <div class="perk-buy-action">
                        <div class="perk-price-tag">
                            <span class="coin-icon">🪙</span> <strong>50 Coins</strong>
                        </div>
                        <button class="btn btn-gradient" id="buyVipBtn">
                            <i class="fa-solid fa-crown"></i> <span>Unlock & Equip VIP</span>
                        </button>
                        <button class="btn btn-secondary" id="demoVipBtn" title="Test preview for 10 seconds">
                            <i class="fa-solid fa-eye"></i> <span>10s Trial Demo</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Extra Rolls & Mystery Perks -->
        <div class="perk-tab-content" id="ptab-rolls">
            <div class="perks-cards-row">
                <div class="perk-item-shop-card">
                    <div class="shop-card-icon">🎲</div>
                    <h4 class="shop-card-title">+1 Instant Dice Roll</h4>
                    <p class="shop-card-desc">Ran out of daily rolls? Buy an instant extra roll right now to match today's target for +25 coins!</p>
                    <div class="shop-card-footer">
                        <span class="shop-price"><span class="coin-icon">🪙</span> 15 Coins</span>
                        <button class="btn btn-gradient btn-sm" id="buyExtraRollBtn">
                            <i class="fa-solid fa-cart-shopping"></i> Buy Roll
                        </button>
                    </div>
                </div>

                <div class="perk-item-shop-card">
                    <div class="shop-card-icon">🎁</div>
                    <h4 class="shop-card-title">Mystery Gem Box</h4>
                    <p class="shop-card-desc">Unlock a secret handpicked 9.0+ rated masterpiece movie with instant direct stream link!</p>
                    <div class="shop-card-footer">
                        <span class="shop-price"><span class="coin-icon">🪙</span> 20 Coins</span>
                        <button class="btn btn-gradient btn-sm" id="openMysteryBoxBtn">
                            <i class="fa-solid fa-box-open"></i> Open Box
                        </button>
                    </div>
                </div>

                <div class="perk-item-shop-card">
                    <div class="shop-card-icon">📑</div>
                    <h4 class="shop-card-title">+2 Watchlist Space</h4>
                    <p class="shop-card-desc">Expand personal Watchlist capacity by +2 slots (Current: <strong id="shopWatchlistCapacity" style="color: #fbbf24;">2</strong> slots). Store more films!</p>
                    <div class="shop-card-footer">
                        <span class="shop-price"><span class="coin-icon">🪙</span> 50 Coins</span>
                        <button class="btn btn-gradient btn-sm" id="buyWatchlistSlotsBtn">
                            <i class="fa-solid fa-folder-plus"></i> Buy Space
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Fan Ranks & Streaks -->
        <div class="perk-tab-content" id="ptab-ranks">
            <div class="ranks-tier-list">
                <div class="rank-tier-item current">
                    <div class="rank-badge-icon">🍿</div>
                    <div class="rank-tier-info">
                        <div class="rank-name">Popcorn Fan <span class="badge-unlocked">UNLOCKED</span></div>
                        <div class="rank-req">Starting rank (0+ streak)</div>
                    </div>
                </div>
                <div class="rank-tier-item">
                    <div class="rank-badge-icon">🎬</div>
                    <div class="rank-tier-info">
                        <div class="rank-name">Cinephile Buff</div>
                        <div class="rank-req">Reach 3-Day Daily Streak or 50 Coins</div>
                    </div>
                </div>
                <div class="rank-tier-item">
                    <div class="rank-badge-icon">🌟</div>
                    <div class="rank-tier-info">
                        <div class="rank-name">Hollywood Insider</div>
                        <div class="rank-req">Reach 7-Day Daily Streak or 100 Coins</div>
                    </div>
                </div>
                <div class="rank-tier-item">
                    <div class="rank-badge-icon">🏆</div>
                    <div class="rank-tier-info">
                        <div class="rank-name">Grandmaster Director</div>
                        <div class="rank-req">Reach 14-Day Daily Streak or 200 Coins</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simple & Responsive Custom Alert / Confirm Dialog -->
<div class="modal-overlay simple-alert-overlay" id="simpleAlertOverlay" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="simple-alert-box" id="simpleAlertBox">
        <button class="simple-alert-close" id="simpleAlertCloseBtn" aria-label="Close dialog">
            <i class="fa-solid fa-xmark"></i>
        </button>
        
        <div class="simple-alert-icon danger" id="simpleAlertIconWrapper">
            <i class="fa-solid fa-trash-can" id="simpleAlertIcon"></i>
        </div>

        <h3 class="simple-alert-title" id="simpleAlertTitle">Clear Watchlist?</h3>
        <p class="simple-alert-msg" id="simpleAlertMsg">Are you sure you want to remove all saved movies and TV shows from your watchlist?</p>

        <div class="simple-alert-actions" id="simpleAlertActions">
            <button type="button" class="btn-alert-cancel" id="simpleAlertCancelBtn">Cancel</button>
            <button type="button" class="btn-alert-confirm danger" id="simpleAlertConfirmBtn">Clear All</button>
        </div>
    </div>
</div>

<!-- Animated Vibrating Mystery Box & Random Movie Surprise Modal -->
<div class="modal-overlay mystery-modal-overlay" id="surpriseMysteryOverlay" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="mystery-modal-container" id="surpriseMysteryContainer">
        <!-- Close Button -->
        <button class="modal-close-btn mystery-close-btn" id="surpriseMysteryCloseBtn" aria-label="Close modal">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- STAGE 1: VIBRATING MYSTERY BOX ANIMATION -->
        <div class="mystery-stage-loading" id="mysteryStageLoading">
            <div class="mystery-badge-pill">
                <i class="fa-solid fa-dice"></i> <span>Moviq Mystery Gem</span>
            </div>
            <h3 class="mystery-stage-title">Rolling The Cinema Dice...</h3>
            <p class="mystery-stage-subtitle">Summoning a surprise masterpiece for you</p>

            <div class="mystery-box-scene">
                <div class="mystery-neon-aura"></div>
                <div class="mystery-sparks-orbit">
                    <span class="sparkle-item sp1">✨</span>
                    <span class="sparkle-item sp2">🎲</span>
                    <span class="sparkle-item sp3">⭐</span>
                    <span class="sparkle-item sp4">🎬</span>
                    <span class="sparkle-item sp5">⚡</span>
                </div>

                <!-- 3D Vibrating Gift Chest Box -->
                <div class="mystery-chest-box vibrating" id="mysteryChestBox">
                    <div class="chest-lid" id="chestLid">
                        <div class="chest-lid-ribbon">🎀</div>
                        <div class="chest-lid-glow"></div>
                    </div>
                    <div class="chest-body">
                        <div class="chest-emblem">
                            <i class="fa-solid fa-dice-d20"></i>
                        </div>
                        <div class="chest-cross-ribbon horizontal"></div>
                        <div class="chest-cross-ribbon vertical"></div>
                    </div>
                </div>
            </div>

            <!-- Suspenseful Rolling Teaser Text & Progress Bar -->
            <div class="mystery-suspense-bar">
                <div class="suspense-text-rotator" id="mysteryStatusText">
                    <i class="fa-solid fa-shuffle fa-spin"></i> Shuffling 10,000+ top rated movies...
                </div>
                <div class="suspense-progress-track">
                    <div class="suspense-progress-fill" id="mysteryProgressFill"></div>
                </div>
            </div>
        </div>

        <!-- STAGE 2: REVEALED RANDOM MOVIE CARD -->
        <div class="mystery-stage-revealed" id="mysteryStageRevealed" style="display: none;">
            <div class="revealed-header">
                <span class="revealed-badge">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> <span>Cinema Gem Found!</span>
                </span>
                <h3 class="revealed-title">Surprise Recommendation</h3>
            </div>

            <div class="revealed-movie-card" id="revealedMovieCard">
                <!-- Injected dynamically by JS -->
            </div>

            <div class="revealed-actions-row">
                <button class="btn btn-secondary btn-reroll" id="mysteryRerollBtn">
                    <i class="fa-solid fa-rotate"></i> <span>Roll Another</span>
                </button>
                <button class="btn btn-gradient btn-watch-gem" id="mysteryWatchGemBtn">
                    <i class="fa-solid fa-play"></i> <span>Watch Movie Now</span>
                </button>
            </div>
        </div>
    </div>
</div>

