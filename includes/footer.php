<!-- Footer Component -->
<footer class="site-footer">
    <div class="footer-ambient-glow"></div>
    <div class="footer-container">
        <!-- Top Row: Brand & Links -->
        <div class="footer-grid">
            <div class="footer-brand-col">
                <a href="#home" class="brand-logo">
                    <div class="logo-icon-box">
                        <i class="fa-solid fa-play"></i>
                    </div>
                    <span class="logo-text">MOVI<span class="logo-gradient">Q</span></span>
                    <span class="logo-badge">CINEMA</span>
                </a>
                <p class="footer-desc">
                    Your premier cinematic destination for discovering blockbuster movies, award-winning TV series, streaming high-definition official trailers, and organizing your personal watchlist.
                </p>
                <div class="footer-tech-badges">
                    <span class="tech-badge"><i class="fa-solid fa-bolt"></i> Free Streaming</span>
                    <span class="tech-badge"><i class="fa-solid fa-clapperboard"></i> TMDB </span>
                    <span class="tech-badge"><i class="fa-solid fa-circle-check"></i> 100% Free</span>
                </div>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Discover</h4>
                <ul class="footer-links">
                    <li><a href="#movies" class="footer-nav-link" data-nav="movies"><i class="fa-solid fa-film"></i> Popular Movies</a></li>
                    <li><a href="#tv" class="footer-nav-link" data-nav="tv"><i class="fa-solid fa-tv"></i> TV Series</a></li>
                    <li><a href="#explore" class="footer-nav-link" data-nav="explore"><i class="fa-solid fa-compass"></i> Explore & Filter</a></li>
                    <li><a href="#explore" class="footer-nav-link" data-nav="explore"><i class="fa-solid fa-star"></i> Top Rated</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Genres</h4>
                <ul class="footer-links" id="footerGenreLinks">
                    <li><a href="#explore" onclick="App.exploreByGenre(28, 'Action'); return false;"><i class="fa-solid fa-burst"></i> Action</a></li>
                    <li><a href="#explore" onclick="App.exploreByGenre(878, 'Sci-Fi'); return false;"><i class="fa-solid fa-robot"></i> Sci-Fi</a></li>
                    <li><a href="#explore" onclick="App.exploreByGenre(16, 'Animation'); return false;"><i class="fa-solid fa-wand-magic-sparkles"></i> Animation</a></li>
                    <li><a href="#explore" onclick="App.exploreByGenre(27, 'Horror'); return false;"><i class="fa-solid fa-skull"></i> Horror</a></li>
                    <li><a href="#explore" onclick="App.exploreByGenre(35, 'Comedy'); return false;"><i class="fa-solid fa-face-laugh-beam"></i> Comedy</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Features & Legal</h4>
                <div class="tmdb-attribution-card">
                    <div class="tmdb-logo-row">
                        <span class="tmdb-chip">TMDB</span>
                        <span class="tmdb-caption">API Powered</span>
                    </div>
                    <p class="tmdb-disclaimer">
                        This product uses the TMDB API but is not endorsed or certified by TMDB. All movie data, images, and metadata belong to their respective copyright holders.
                    </p>
                </div>
                <div class="footer-social-links">
                    <a href="https://github.com" target="_blank" rel="noopener" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                    <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="https://themoviedb.org" target="_blank" rel="noopener" aria-label="TMDB"><i class="fa-solid fa-database"></i></a>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="footer-bottom">
            <p class="copyright-text">
                &copy; <?php echo date('Y'); ?> <strong class="brand-text-highlight">Moviq</strong>. Crafted with Arif<i class="fa-solid fa-heart heart-icon"></i> for cinema lovers.
            </p>
            <button class="back-to-top-btn" id="backToTopBtn" aria-label="Back to top">
                <span>Back to Top</span>
                <i class="fa-solid fa-arrow-up"></i>
            </button>
        </div>
    </div>
</footer>

<!-- JavaScript Bundles (with cache-busting versions) -->
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.8/dist/hls.min.js"></script>
<script src="assets/js/api.js?v=<?php echo file_exists(__DIR__ . '/../assets/js/api.js') ? filemtime(__DIR__ . '/../assets/js/api.js') : time(); ?>"></script>
<script src="assets/js/watchlist.js?v=<?php echo file_exists(__DIR__ . '/../assets/js/watchlist.js') ? filemtime(__DIR__ . '/../assets/js/watchlist.js') : time(); ?>"></script>
<script src="assets/js/coinSystem.js?v=<?php echo file_exists(__DIR__ . '/../assets/js/coinSystem.js') ? filemtime(__DIR__ . '/../assets/js/coinSystem.js') : time(); ?>"></script>
<script src="assets/js/app.js?v=<?php echo file_exists(__DIR__ . '/../assets/js/app.js') ? filemtime(__DIR__ . '/../assets/js/app.js') : time(); ?>"></script>
</body>
</html>
