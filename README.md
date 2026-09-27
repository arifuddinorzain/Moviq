# 🎬 Moviq — The Ultimate Cinematic Movie & TV Hub

A modern, high-performance movie and TV series exploration web application built with **PHP** and **JavaScript**, powered by the **TMDB (The Movie Database) API**.

![Moviq Theme](https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1200&auto=format&fit=crop&q=80)

---

## ✨ Features & Highlights

- **🌙 Premium Dark Theme & Vibrant Neon Gradients**:
  - Deep obsidian black surfaces (`#06080d`), glassmorphism with `backdrop-filter: blur(20px)`, and electric magenta-to-violet (`#7928ca` to `#ff0080`), neon cyan, and amber gold accents.
  - Dynamic ambient background glow orbs with smooth breathing animations.

- **🔥 Dynamic Hero Showcase Slider**:
  - Auto-rotating carousel showcasing the #1 global trending blockbusters with high-resolution backdrops, movie metadata, ratings, and genre tags.
  - Direct *"Watch Trailer"* trigger and animated timer progress bar.

- **🏆 Netflix-Style Top 10 Trending Row**:
  - Giant stylized rank numeral typography (1–10) with glowing hover transitions.

- **🔎 Instant Search with Live Autocomplete**:
  - Real-time debounced search dropdown with movie/TV/actor suggestions, posters, release years, and rating badges.
  - Global keyboard shortcut (`Ctrl + K` or `/`) to quickly search from anywhere.

- **🎭 Comprehensive Discovery & Filtering (Explore View)**:
  - Filter by Media Type (Movies vs TV Shows), Genres, Release Year, and Minimum Rating (8.0+ Masterpiece, 7.0+ Great, etc.).
  - Sort by Popularity, Rating, Release Date, and Box Office Revenue.
  - Grid View & Compact View toggle.

- **🎬 Full Movie & TV Show Details Modal**:
  - High-definition YouTube trailer embeds.
  - Key facts: Budget, Box Office Revenue, Seasons & Episodes, Status, Production companies.
  - Top Cast & Crew avatar row (clickable to view Actor filmography).
  - *"More Like This"* similar titles recommendations.
  - Direct shareable links (e.g. `#movie/550` or `#tv/1399`).

- **🔖 Persistent Watchlist Library**:
  - Add/remove titles with instant `localStorage` persistence.
  - Live header badge counters and synchronized button states across all views.
  - Filter saved items by Movies or TV Series.

- **🎲 Surprise Me / Random Gem**:
  - Quick-pick button to find a random top-rated movie instantly.

- **⚡ Fast PHP Backend & Intelligent Caching**:
  - Built-in transparent file caching system (10–30 min expiry) to minimize API latency and respect TMDB rate limits.

---

## 🚀 How to Run Locally

### 1. Requirements
- **PHP 8.0+** (PHP 8.2 recommended)
- Modern web browser (Chrome, Edge, Firefox, Safari)

### 2. Start the PHP Built-in Server
Open your terminal inside the project directory and run:

```bash
php -S localhost:8000
```

### 3. Open in Browser
Visit **[http://localhost:8000](http://localhost:8000)** in your browser.

---

## 📂 Project Structure

```
movieMe/
├── index.php             # Main entry point & SPA views (Home, Explore, Movies, TV, Watchlist)
├── config.php            # TMDB API configuration, caching engine & helper constants
├── api.php               # REST API gateway proxying TMDB requests with JSON formatting
├── includes/
│   ├── header.php        # HTML head, SEO tags, Google Fonts (Outfit & Plus Jakarta Sans)
│   ├── navbar.php        # Navigation bar, search input, autocomplete dropdown & mobile drawer
│   ├── hero.php          # Dynamic Hero slider skeleton and structure
│   ├── modal.php         # Details modal, trailer player modal, actor modal
│   ├── toast.php         # Floating dynamic toast notification container
│   └── footer.php        # Footer, TMDB attribution, social links & script imports
├── assets/
│   ├── css/
│   │   └── style.css     # Design system, CSS variables, glassmorphism, responsive styles
│   └── js/
│       ├── api.js        # TMDB API client wrapper
│       ├── watchlist.js  # Reactive localStorage watchlist library manager
│       └── app.js        # Master application controller, routing, slider, modals
└── cache/                # Auto-generated cache directory for fast local responses
```

---

## 🔑 TMDB API Attribution

This product uses the TMDB API but is not endorsed or certified by TMDB.
All movie data, images, and metadata are provided by [The Movie Database (TMDB)](https://www.themoviedb.org/).
