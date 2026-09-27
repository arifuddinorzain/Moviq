<!-- Hero Section (Dynamic TMDB Trending Showcase) -->
<section class="hero-section" id="heroSection">
    <div class="hero-slider-container" id="heroSlider">
        <!-- Skeleton Loader for Hero while loading -->
        <div class="hero-skeleton" id="heroSkeleton">
            <div class="hero-skeleton-content">
                <div class="skeleton-pill"></div>
                <div class="skeleton-title"></div>
                <div class="skeleton-meta"></div>
                <div class="skeleton-desc"></div>
                <div class="skeleton-btns"></div>
            </div>
        </div>

        <!-- Slides injected dynamically by JS -->
        <div class="hero-slides-wrapper" id="heroSlidesWrapper"></div>

        <!-- Slider Controls -->
        <div class="hero-controls">
            <button class="hero-nav-btn prev-btn" id="heroPrevBtn" aria-label="Previous Slide">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <div class="hero-indicators" id="heroIndicators">
                <!-- Injected dynamically -->
            </div>
            <button class="hero-nav-btn next-btn" id="heroNextBtn" aria-label="Next Slide">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>

        <!-- Hero Progress Bar -->
        <div class="hero-progress-bar-container">
            <div class="hero-progress-bar" id="heroProgressBar"></div>
        </div>
    </div>
</section>
