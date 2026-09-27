<?php
/**
 * Moviq — Live TV & IPTV Broadcasting Engine
 * Curated from verified 24/7 public streams in the iptv-org/iptv community catalog
 */

if (!defined('CACHE_DIR')) {
    define('CACHE_DIR', __DIR__ . '/../cache');
}

/**
 * Get list of curated, verified 24/7 Live TV channels
 *
 * @param string $category Filter by category (all, movies, news, sports, music, documentary, entertainment, kids/animation)
 * @param string $search Optional search keyword
 * @return array
 */
function get_iptv_channels($category = 'all', $search = '') {
    $channels = [
        // ==========================================
        // 0. MALAYSIA LIVE TV CHANNELS (SALURAN MALAYSIA)
        // ==========================================
        [
            'id' => 'my-tv1',
            'name' => 'TV1 Malaysia HD',
            'category' => 'malaysia',
            'category_name' => 'Malaysia TV',
            'country' => 'MY',
            'country_name' => 'Malaysia',
            'flag' => '🇲🇾',
            'logo' => 'https://i.imgur.com/wSyPmiY.png',
            'stream_url' => 'https://d25tgymtnqzu8s.cloudfront.net/smil:tv1/playlist.m3u8?id=1',
            'quality' => '1080p',
            'current_show' => 'Berita Perdana, Bicara Naratif & Rancangan Perdana',
            'description' => 'Saluran televisyen pertama dan utama Malaysia menyiarkan berita rasmi, hal ehwal semasa dan rancangan maklumat terkini.'
        ],
        [
            'id' => 'my-tv2',
            'name' => 'TV2 Malaysia HD',
            'category' => 'malaysia',
            'category_name' => 'Malaysia TV',
            'country' => 'MY',
            'country_name' => 'Malaysia',
            'flag' => '🇲🇾',
            'logo' => 'https://i.imgur.com/LGEdeyJ.png',
            'stream_url' => 'https://d25tgymtnqzu8s.cloudfront.net/smil:tv2/playlist.m3u8?id=2',
            'quality' => '1080p',
            'current_show' => 'Dunia Hiburan, Filem Blockbuster & Drama Famili',
            'description' => 'Saluran hiburan keluarga berbilang bahasa menyajikan filem Hollywood, Asia, drama tempatan, dan hiburan variasi.'
        ],
        [
            'id' => 'my-berita-rtm',
            'name' => 'Berita RTM HD',
            'category' => 'malaysia',
            'category_name' => 'Malaysia TV',
            'country' => 'MY',
            'country_name' => 'Malaysia',
            'flag' => '🇲🇾',
            'logo' => 'https://i.imgur.com/1Xzxsgl.png',
            'stream_url' => 'https://d25tgymtnqzu8s.cloudfront.net/smil:berita/playlist.m3u8?id=5',
            'quality' => '1080p',
            'current_show' => 'Berita Perdana, Berita Semasa & Analisis Terkini 24/7',
            'description' => 'Saluran berita 24 jam rasmi Radio Televisyen Malaysia (RTM) menyiarkan liputan langsung dan berita mutakhir.'
        ],
        [
            'id' => 'my-sukan-rtm',
            'name' => 'Sukan RTM HD',
            'category' => 'malaysia',
            'category_name' => 'Malaysia TV',
            'country' => 'MY',
            'country_name' => 'Malaysia',
            'flag' => '🇲🇾',
            'logo' => 'https://i.imgur.com/1pYYSu2.png',
            'stream_url' => 'https://d25tgymtnqzu8s.cloudfront.net/smil:sukan/playlist.m3u8?id=4',
            'quality' => '1080p',
            'current_show' => 'Liputan Sukan Langsung, Liga Tempatan & Sukan Antarabangsa',
            'description' => 'Saluran sukan percuma terulung Malaysia menyiarkan acara sukan antarabangsa, kejohanan tempatan dan ulasan sukan.'
        ],
        [
            'id' => 'my-tv-okey',
            'name' => 'TV Okey HD',
            'category' => 'malaysia',
            'category_name' => 'Malaysia TV',
            'country' => 'MY',
            'country_name' => 'Malaysia',
            'flag' => '🇲🇾',
            'logo' => 'https://i.imgur.com/1Xzxsgl.png',
            'stream_url' => 'https://d25tgymtnqzu8s.cloudfront.net/smil:okey/playlist.m3u8?id=3',
            'quality' => '1080p',
            'current_show' => 'Rancangan Variasi, Budaya Borneo & Drama Tempatan',
            'description' => 'Saluran hiburan dan gaya hidup segar RTM membawakan seni budaya Sabah, Sarawak, drama kontemporari dan muzik.'
        ],
        [
            'id' => 'my-tv6',
            'name' => 'TV6 Malaysia Klasik',
            'category' => 'malaysia',
            'category_name' => 'Malaysia TV',
            'country' => 'MY',
            'country_name' => 'Malaysia',
            'flag' => '🇲🇾',
            'logo' => 'https://i.imgur.com/bK8UH9D.png',
            'stream_url' => 'https://d25tgymtnqzu8s.cloudfront.net/smil:tv6/playlist.m3u8?id=6',
            'quality' => '1080p',
            'current_show' => 'Filem Klasik Melayu, Retro Drama & Komedi Lagenda',
            'description' => 'Menyiarkan khazanah filem Melayu klasik terbaik, drama retro kegemilangan zaman, dan hiburan nostalgia tanah air.'
        ],
        [
            'id' => 'my-tvs',
            'name' => 'TVS HD (Sarawak)',
            'category' => 'malaysia',
            'category_name' => 'Malaysia TV',
            'country' => 'MY',
            'country_name' => 'Malaysia',
            'flag' => '🇲🇾',
            'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/92/TVS_Sarawak.jpg/960px-TVS_Sarawak.jpg',
            'stream_url' => 'https://api.dani-dev.co.za/v1/stream/mytv/live/tvs',
            'quality' => '1080p',
            'current_show' => 'Utama TVS, Cerita Borneo & Dokumentari Sarawak',
            'description' => 'Saluran televisyen pertama milik Sarawak yang menyiarkan berita serantau, budaya, dan hiburan beridentitikan Borneo.'
        ],
        [
            'id' => 'my-dewan-rakyat',
            'name' => 'Parlimen Dewan Rakyat',
            'category' => 'malaysia',
            'category_name' => 'Malaysia TV',
            'country' => 'MY',
            'country_name' => 'Malaysia',
            'flag' => '🇲🇾',
            'logo' => 'https://i.postimg.cc/mgrVsTMY/header.png',
            'stream_url' => 'https://d25tgymtnqzu8s.cloudfront.net/smil:rakyat/playlist.m3u8?id=7',
            'quality' => '1080p',
            'current_show' => 'Persidangan Dewan Rakyat & Perbahasan Rang Undang-Undang',
            'description' => 'Liputan langsung rasmi sesi perbahasan dan sidang Parlimen Dewan Rakyat Malaysia.'
        ],
        [
            'id' => 'my-dewan-negara',
            'name' => 'Parlimen Dewan Negara',
            'category' => 'malaysia',
            'category_name' => 'Malaysia TV',
            'country' => 'MY',
            'country_name' => 'Malaysia',
            'flag' => '🇲🇾',
            'logo' => 'https://i.postimg.cc/mgrVsTMY/header.png',
            'stream_url' => 'https://d25tgymtnqzu8s.cloudfront.net/smil:negara/playlist.m3u8?id=8',
            'quality' => '1080p',
            'current_show' => 'Persidangan Dewan Negara & Prosiding Rasmi Senator',
            'description' => 'Liputan langsung sesi persidangan dan perbahasan Dewan Negara Parlimen Malaysia.'
        ],

        // ==========================================
        // 1. MOVIES & CINEMA CHANNELS
        // ==========================================
        [
            'id' => 'cinema-4ever',
            'name' => '4ever Cinema HD',
            'category' => 'movies',
            'category_name' => 'Movies & Cinema',
            'country' => 'US',
            'country_name' => 'United States',
            'flag' => '🇺🇸',
            'logo' => 'https://i.imgur.com/vFOgVbG.png',
            'stream_url' => 'http://stream.mcquack.net/258/index.m3u8',
            'quality' => '1080p',
            'current_show' => 'Feature Films, Hollywood Classics & Indie Blockbusters',
            'description' => 'Continuous streaming of award-winning feature films and cinema hits.'
        ],
        [
            'id' => 'action-50cent',
            'name' => '50 Cent Action Cinema',
            'category' => 'movies',
            'category_name' => 'Movies & Cinema',
            'country' => 'US',
            'country_name' => 'United States',
            'flag' => '🇺🇸',
            'logo' => 'https://provider-static.plex.tv/epg/cms/production/bcfb9977-809f-49ae-acc7-430f2c6ffb26/50CentAction_Logo_1500x1000_DarkBG_-_Chris_Connors.png',
            'stream_url' => 'https://jmp2.uk/plu-68487fb3f212bedacf5a53e3.m3u8',
            'quality' => '1080p',
            'current_show' => 'High-Octane Action, Thrillers & Martial Arts',
            'description' => 'Explosive blockbusters, martial arts cinema, and non-stop thrillers.'
        ],
        [
            'id' => '70s-cinema',
            'name' => '70s Golden Cinema',
            'category' => 'movies',
            'category_name' => 'Movies & Cinema',
            'country' => 'US',
            'country_name' => 'Vintage Cinema',
            'flag' => '🎬',
            'logo' => 'https://i.imgur.com/mgXeEE4.png',
            'stream_url' => 'https://jmp2.uk/plu-5f4d878d3d19b30007d2e782.m3u8',
            'quality' => '720p',
            'current_show' => 'Golden Age 1970s Hollywood Classics',
            'description' => 'Iconic vintage movies, cult classics, and 70s cinema masterpieces.'
        ],
        [
            'id' => '80s-rewind',
            'name' => '80s Rewind Movies',
            'category' => 'movies',
            'category_name' => 'Movies & Cinema',
            'country' => 'US',
            'country_name' => '80s Hits',
            'flag' => '📼',
            'logo' => 'https://i.imgur.com/nkEeYfI.png',
            'stream_url' => 'https://jmp2.uk/plu-5ca525b650be2571e3943c63.m3u8',
            'quality' => '720p',
            'current_show' => 'Retro 1980s Pop Culture & Comedy Films',
            'description' => 'The greatest 80s comedy, sci-fi, and action cinema favorites.'
        ],
        [
            'id' => 'replay-00s',
            'name' => '00s Movie Replay',
            'category' => 'movies',
            'category_name' => 'Movies & Cinema',
            'country' => 'US',
            'country_name' => '2000s Hits',
            'flag' => '🎞️',
            'logo' => 'https://images.pluto.tv/channels/62ba60f059624e000781c436/colorLogoPNG.png',
            'stream_url' => 'https://jmp2.uk/plu-62ba60f059624e000781c436.m3u8',
            'quality' => '720p',
            'current_show' => 'Blockbuster Movies from the 2000s Era',
            'description' => 'Relive the most exciting films from the turn of the millennium.'
        ],
        [
            'id' => '30a-classic-movies',
            'name' => '30A Classic Movies',
            'category' => 'movies',
            'category_name' => 'Movies & Cinema',
            'country' => 'US',
            'country_name' => 'United States',
            'flag' => '🇺🇸',
            'logo' => 'https://i.imgur.com/Lv53nh4.png',
            'stream_url' => 'https://30a-tv.com/feeds/pzaz/30atvmovies.m3u8',
            'quality' => '720p',
            'current_show' => 'Public Domain & Classic Western Masterpieces',
            'description' => 'Legendary classic dramas, film noir, and vintage adventures.'
        ],

        // ==========================================
        // 2. NEWS 24/7 CHANNELS
        // ==========================================
        [
            'id' => 'bloomberg-tv',
            'name' => 'Bloomberg Television',
            'category' => 'news',
            'category_name' => 'News 24/7',
            'country' => 'US',
            'country_name' => 'Global Finance',
            'flag' => '🌐',
            'logo' => 'https://raw.githubusercontent.com/iptv-org/api/master/data/logos/BloombergTV.png',
            'stream_url' => 'https://bloomberg.com/media-manifest/streams/us.m3u8',
            'quality' => '1080p',
            'current_show' => 'Global Markets & Financial News Live',
            'description' => 'World business leaders, stock markets, and breaking financial updates.'
        ],
        [
            'id' => '2gb-news',
            'name' => '2GB News HD',
            'category' => 'news',
            'category_name' => 'News 24/7',
            'country' => 'AU',
            'country_name' => 'Australia',
            'flag' => '🇦🇺',
            'logo' => 'https://i.ibb.co/jwM8DFG/2GB-1.png',
            'stream_url' => 'https://2gblive.akamaized.net/hls/live/2033805/2GB/index.m3u8',
            'quality' => '1080p',
            'current_show' => 'Live Breaking News & Political Debates',
            'description' => '24/7 live television coverage of global news and special reports.'
        ],
        [
            'id' => '3aw-news',
            'name' => '3AW News 24/7',
            'category' => 'news',
            'category_name' => 'News 24/7',
            'country' => 'AU',
            'country_name' => 'Australia',
            'flag' => '🇦🇺',
            'logo' => 'https://i.imgur.com/Z4MdB0S.png',
            'stream_url' => 'https://3awlive.akamaized.net/hls/live/2032295/3AW/index.m3u8',
            'quality' => '1080p',
            'current_show' => '24-Hour International Headline Broadcast',
            'description' => 'Real-time breaking world events, interviews, and deep-dive journalism.'
        ],
        [
            'id' => '3cat-info',
            'name' => '3Cat World News Live',
            'category' => 'news',
            'category_name' => 'News 24/7',
            'country' => 'EU',
            'country_name' => 'Europe',
            'flag' => '🇪🇺',
            'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/69/3CatInfo_logo.svg/960px-3CatInfo_logo.svg.png',
            'stream_url' => 'https://directes-tv-int.3catdirectes.cat/live-origin/canal324-hls/master.m3u8',
            'quality' => '1080p',
            'current_show' => 'European & Global Breaking News',
            'description' => 'Live news direct from European broadcast studios in high definition.'
        ],
        [
            'id' => '6pr-news',
            'name' => '6PR News Live',
            'category' => 'news',
            'category_name' => 'News 24/7',
            'country' => 'AU',
            'country_name' => 'Australia',
            'flag' => '🇦🇺',
            'logo' => 'https://i.imgur.com/Q9iCxg1.png',
            'stream_url' => 'https://6prlive.akamaized.net/hls/live/2033806/6PR/index.m3u8',
            'quality' => '1080p',
            'current_show' => 'Pacific & World Live Bulletins',
            'description' => 'Live journalism, global headlines, and continuous live bulletins.'
        ],
        [
            'id' => 'al-arabiya',
            'name' => 'Al Arabiya News Network',
            'category' => 'news',
            'category_name' => 'News 24/7',
            'country' => 'AE',
            'country_name' => 'Middle East',
            'flag' => '🌐',
            'logo' => 'https://i.imgur.com/Hoc3cfO.png',
            'stream_url' => 'https://live.alarabiya.net/alarabiapublish/aaprograms.smil/playlist.m3u8',
            'quality' => '1080p',
            'current_show' => 'Global Documentaries & International Reports',
            'description' => 'Award-winning international investigative reports and news.'
        ],

        // ==========================================
        // 3. SPORTS & ACTION CHANNELS
        // ==========================================
        [
            'id' => 'redbull-tv',
            'name' => 'Red Bull TV Live',
            'category' => 'sports',
            'category_name' => 'Sports & Action',
            'country' => 'AT',
            'country_name' => 'Extreme Sports',
            'flag' => '⚡',
            'logo' => 'https://raw.githubusercontent.com/iptv-org/api/master/data/logos/RedBullTV.png',
            'stream_url' => 'https://rbmn-live.akamaized.net/hls/live/590964/BoRB-AT/master.m3u8',
            'quality' => '1080p',
            'current_show' => 'Formula 1, Extreme Motocross, Surfing & Cliff Diving',
            'description' => 'The undisputed home of extreme action sports and live competitions.'
        ],
        [
            'id' => '30a-golf',
            'name' => '30A Golf Kingdom',
            'category' => 'sports',
            'category_name' => 'Sports & Action',
            'country' => 'US',
            'country_name' => 'Golf Network',
            'flag' => '⛳',
            'logo' => 'https://i.imgur.com/Lv53nh4.png',
            'stream_url' => 'https://30a-tv.com/feeds/vidaa/golf.m3u8',
            'quality' => '720p',
            'current_show' => 'Championship Golf Tours & Pro Masterclasses',
            'description' => 'PGA highlights, golf course tours, and legendary tournament matches.'
        ],
        [
            'id' => 'aspor-sports',
            'name' => 'A Spor HD Live',
            'category' => 'sports',
            'category_name' => 'Sports & Action',
            'country' => 'TR',
            'country_name' => 'World Sports',
            'flag' => '⚽',
            'logo' => 'https://i.imgur.com/ZhkZzLf.png',
            'stream_url' => 'https://rnttwmjcin.turknet.ercdn.net/lcpmvefbyo/aspor/aspor.m3u8',
            'quality' => '1080p',
            'current_show' => 'Football, Basketball & World League Highlights',
            'description' => '24/7 sports coverage, live football discussions, and highlights.'
        ],
        [
            'id' => 'aci-sport',
            'name' => 'ACI Motorsport TV',
            'category' => 'sports',
            'category_name' => 'Sports & Action',
            'country' => 'IT',
            'country_name' => 'Motorsport',
            'flag' => '🏎️',
            'logo' => 'https://i.imgur.com/U8cHMOt.png',
            'stream_url' => 'https://webstream.multistream.it/memfs/e2cb3629-c1a2-495b-b43a-9eb386f04ed8.m3u8',
            'quality' => '1080p',
            'current_show' => 'GT Racing, Rally Championships & Supercars',
            'description' => 'Adrenaline-fueled GT racing, rally events, and circuit championships.'
        ],
        [
            'id' => 'ado-sports',
            'name' => 'ADO Extreme Sports',
            'category' => 'sports',
            'category_name' => 'Sports & Action',
            'country' => 'FR',
            'country_name' => 'Action Sports',
            'flag' => '🏄',
            'logo' => 'https://i.imgur.com/pxFamLr.png',
            'stream_url' => 'https://strhls.streamakaci.tv/ortb/ortb2-multi/playlist.m3u8',
            'quality' => '720p',
            'current_show' => 'Skateboarding, BMX & Freestyle Athletics',
            'description' => 'High-energy youth action sports, urban athletics, and competitions.'
        ],

        // ==========================================
        // 4. MUSIC & CONCERTS CHANNELS
        // ==========================================
        [
            'id' => '1hd-music',
            'name' => '1HD Music Television',
            'category' => 'music',
            'category_name' => 'Music & Concerts',
            'country' => 'EU',
            'country_name' => 'Music Hits',
            'flag' => '🎧',
            'logo' => 'https://i.imgur.com/4Ww7CsR.png',
            'stream_url' => 'http://176.118.197.101/1HD/index.m3u8',
            'quality' => '1080p',
            'current_show' => 'Top 40 Billboard Hits & Official Music Videos',
            'description' => 'Non-stop top 40 pop, hip-hop, electronic, and chart-topping music videos.'
        ],
        [
            'id' => '4music-hits',
            'name' => '4 Music Hits',
            'category' => 'music',
            'category_name' => 'Music & Concerts',
            'country' => 'UK',
            'country_name' => 'United Kingdom',
            'flag' => '🇬🇧',
            'logo' => 'https://raw.githubusercontent.com/iptv-org/api/master/data/logos/4Music.png',
            'stream_url' => 'https://musichls.persiana.live/hls/stream.m3u8',
            'quality' => '1080p',
            'current_show' => 'Global Pop Anthems & Live Studio Sessions',
            'description' => 'The hottest music videos and chart countdowns 24 hours a day.'
        ],
        [
            'id' => '7radiovisione',
            'name' => '7 RadioVisione HD',
            'category' => 'music',
            'category_name' => 'Music & Concerts',
            'country' => 'IT',
            'country_name' => 'Radio TV',
            'flag' => '📻',
            'logo' => 'https://radio7note.com/img/favicon/android-icon-192x192.png',
            'stream_url' => 'https://stream10.xdevel.com/video1s976543-1932/stream/playlist.m3u8',
            'quality' => '720p',
            'current_show' => 'Live Visual Radio & Electronic Dance Music',
            'description' => 'Visual radio with live DJ sets, music videos, and studio guests.'
        ],
        [
            'id' => '4kurd-music',
            'name' => '4Kurd World Music',
            'category' => 'music',
            'category_name' => 'Music & Concerts',
            'country' => 'INT',
            'country_name' => 'World Beats',
            'flag' => '🎸',
            'logo' => 'https://www.aparatchi.com/images/chanells-logo/4kurd.svg',
            'stream_url' => 'https://4kuhls.persiana.live/hls/stream.m3u8',
            'quality' => '1080p',
            'current_show' => 'World Music Festivals & Concert Performances',
            'description' => 'Dynamic multicultural acoustic, pop, and live festival performances.'
        ],
        [
            'id' => '3abn-praise',
            'name' => '3ABN Music Praise',
            'category' => 'music',
            'category_name' => 'Music & Concerts',
            'country' => 'US',
            'country_name' => 'Acoustic & Choral',
            'flag' => '🎼',
            'logo' => 'https://i.imgur.com/iBcqT8L.png',
            'stream_url' => 'https://3abn.bozztv.com/3abn1/PraiseHim/smil:PraiseHim.smil/playlist.m3u8',
            'quality' => '1080p',
            'current_show' => 'Orchestral, Gospel & Choral Music Concerts',
            'description' => 'Soothing acoustic, symphony orchestra, and vocal choir concerts.'
        ],

        // ==========================================
        // 5. DOCUMENTARY & SCIENCE CHANNELS
        // ==========================================
        [
            'id' => 'adventure-earth',
            'name' => 'Adventure Earth HD',
            'category' => 'documentary',
            'category_name' => 'Documentary & Science',
            'country' => 'US',
            'country_name' => 'Nature & Wildlife',
            'flag' => '🌍',
            'logo' => 'https://d3b6luslimvglo.cloudfront.net/images/79/rlaxximages/channels-rescaled/icon-white/adventureearth_white.png',
            'stream_url' => 'https://a57e9c69976649b582a8d7604c00e69a.mediatailor.us-east-1.amazonaws.com/v1/master/44f73ba4d03e9607dcd9bebdcb8494d86964f1d8/RlaxxTV-eu_AdventureEarth/playlist.m3u8',
            'quality' => '1080p',
            'current_show' => 'Wild Planet, Ocean Depths & Earth Expeditions',
            'description' => 'Breathtaking 4K expeditions across untouched nature and wildlife.'
        ],
        [
            'id' => '365days-history',
            'name' => '365 Days World History',
            'category' => 'documentary',
            'category_name' => 'Documentary & Science',
            'country' => 'EU',
            'country_name' => 'History',
            'flag' => '🏛️',
            'logo' => 'https://i.imgur.com/NfnAiTR.png',
            'stream_url' => 'http://stream.mcquack.net/70/index.m3u8',
            'quality' => '576p',
            'current_show' => 'Ancient Civilizations & Historical Battles',
            'description' => 'Discover ancient empires, historical discoveries, and human achievements.'
        ],
        [
            'id' => '30a-loomer',
            'name' => '30A Discovery TV',
            'category' => 'documentary',
            'category_name' => 'Documentary & Science',
            'country' => 'US',
            'country_name' => 'Discovery',
            'flag' => '🔭',
            'logo' => 'https://i.imgur.com/Lv53nh4.png',
            'stream_url' => 'https://30a-tv.com/loomer.m3u8',
            'quality' => '720p',
            'current_show' => 'Scientific Breakthroughs & Innovation',
            'description' => 'Fascinating modern science, technology breakthroughs, and innovations.'
        ],
        [
            'id' => 'archivos-forenses',
            'name' => 'Archivos Forenses Crime',
            'category' => 'documentary',
            'category_name' => 'Documentary & Science',
            'country' => 'US',
            'country_name' => 'True Crime',
            'flag' => '🔍',
            'logo' => 'https://i.imgur.com/Pxrl9Ug.png',
            'stream_url' => 'https://jmp2.uk/plu-5f984f4a09e92d0007d74647.m3u8',
            'quality' => '720p',
            'current_show' => 'Forensic Files & True Crime Investigations',
            'description' => 'Real forensic science solving famous cold cases and mysteries.'
        ],

        // ==========================================
        // 6. ENTERTAINMENT CHANNELS
        // ==========================================
        [
            'id' => '4u-entertainment',
            'name' => '4U Global TV',
            'category' => 'entertainment',
            'category_name' => 'Entertainment',
            'country' => 'INT',
            'country_name' => 'Entertainment',
            'flag' => '🍿',
            'logo' => 'https://i.imgur.com/PexhKwp.png',
            'stream_url' => 'https://hls.4utv.live/hls/stream.m3u8',
            'quality' => '720p',
            'current_show' => 'Celebrity Gossip, Comedy Shows & Drama Series',
            'description' => 'Top-rated entertainment variety shows, comedy specials, and celebrity news.'
        ],
        [
            'id' => '30a-hollywood',
            'name' => '30A Hollywood Review',
            'category' => 'entertainment',
            'category_name' => 'Entertainment',
            'country' => 'US',
            'country_name' => 'Hollywood',
            'flag' => '🌟',
            'logo' => 'https://images.axios.com/JiG3RuYBwpU_WZFeU6HHjt03FAU=/111x0:1191x1080/320x320/2023/11/09/1699558891265.jpg',
            'stream_url' => 'https://30a-tv.com/gh.m3u8',
            'quality' => '720p',
            'current_show' => 'Movie Premieres, Red Carpets & Director Interviews',
            'description' => 'Behind-the-scenes Hollywood access, red carpet interviews, and film previews.'
        ],
        [
            'id' => '1kzn-tv',
            'name' => '1KZN Television',
            'category' => 'entertainment',
            'category_name' => 'Entertainment',
            'country' => 'ZA',
            'country_name' => 'Drama & Variety',
            'flag' => '🎭',
            'logo' => 'https://admango.cdn.mangomolo.com/analytics/uploads/188/6544bebaae.jpg',
            'stream_url' => 'https://cdn.freevisiontv.co.za/sttv/smil:1kzn.stream.smil/playlist.m3u8',
            'quality' => '576p',
            'current_show' => 'Drama Series, Comedy Sketches & Lifestyle',
            'description' => 'Gripping television drama, local comedies, and lifestyle entertainment.'
        ],
        [
            'id' => 'incinta-16',
            'name' => '16 Anni Reality TV',
            'category' => 'entertainment',
            'category_name' => 'Entertainment',
            'country' => 'EU',
            'country_name' => 'Reality Series',
            'flag' => '✨',
            'logo' => 'https://images.pluto.tv/channels/60940a07d88ba90007b9cb71/colorLogoPNG.png',
            'stream_url' => 'https://jmp2.uk/plu-60940a07d88ba90007b9cb71.m3u8',
            'quality' => '720p',
            'current_show' => 'Hit Reality Television & Lifestyle Stories',
            'description' => 'The most talked-about reality television series and personal stories.'
        ],

        // ==========================================
        // 7. ANIMATION & KIDS CHANNELS
        // ==========================================
        [
            'id' => 'cartoon-classics',
            'name' => 'Cartoon Classics HD',
            'category' => 'animation',
            'category_name' => 'Animation & Kids',
            'country' => 'US',
            'country_name' => 'Cartoons',
            'flag' => '🎨',
            'logo' => 'https://images-3.rakuten.tv/storage/global-live-channel/translation/artwork/3227c06e-333b-4b1b-b657-3e3ab99ebd06-width200-quality90.jpeg',
            'stream_url' => 'https://daiconnect.com/live/hls/tvup/rk-cartoonclassics/578f4b7eb725168349ec0af81b21d388/index.m3u8',
            'quality' => '1080p',
            'current_show' => 'Popeye, Looney Legends, Betty Boop & Classic Cartoons',
            'description' => 'Timeless cartoon classics loved by families and kids of all ages.'
        ],
        [
            'id' => 'adn-animation',
            'name' => 'ADN Anime & Animation TV',
            'category' => 'animation',
            'category_name' => 'Animation & Kids',
            'country' => 'FR',
            'country_name' => 'Anime Network',
            'flag' => '⚡',
            'logo' => 'https://i.imgur.com/HQZQyWt.png',
            'stream_url' => 'https://d3b73b34o7cvkq.cloudfront.net/v1/master/3722c60a815c199d9c0ef36c5b73da68a62b09d1/cc-gz2sgqzp076kf/adn.m3u8',
            'quality' => '720p',
            'current_show' => 'Popular Anime Series & Action Adventures',
            'description' => 'Top Japanese animation, fantasy adventures, and anime series.'
        ],
        [
            'id' => 'anime-hidive',
            'name' => 'Anime x HiDIVE',
            'category' => 'animation',
            'category_name' => 'Animation & Kids',
            'country' => 'US',
            'country_name' => 'Anime Hits',
            'flag' => '⚔️',
            'logo' => 'https://i.imgur.com/v0BlxCa.png',
            'stream_url' => 'https://jmp2.uk/plu-6793eaa4bc03978b9bc63db1.m3u8',
            'quality' => '1080p',
            'current_show' => 'Shonen, Isekai & Action Anime 24/7',
            'description' => '24-hour streaming of trending Japanese anime and animated epics.'
        ],
        [
            'id' => 'avatar-tv',
            'name' => 'Avatar Animated Universe',
            'category' => 'animation',
            'category_name' => 'Animation & Kids',
            'country' => 'US',
            'country_name' => 'Animation',
            'flag' => '🔥',
            'logo' => 'https://images.pluto.tv/channels/656df599c0fc8800089c75ab/colorLogoPNG.png',
            'stream_url' => 'https://jmp2.uk/plu-656df599c0fc8800089c75ab.m3u8',
            'quality' => '720p',
            'current_show' => 'Avatar: The Last Airbender & Legend of Korra',
            'description' => 'All-day streaming of epic animated adventures and heroes.'
        ],
        [
            'id' => '3abn-kids',
            'name' => '3ABN Kids TV',
            'category' => 'animation',
            'category_name' => 'Animation & Kids',
            'country' => 'US',
            'country_name' => 'Educational & Fun',
            'flag' => '🧸',
            'logo' => 'https://i.imgur.com/z3npqO1.png',
            'stream_url' => 'https://3abn.bozztv.com/3abn2/Kids_live/smil:Kids_live.smil/playlist.m3u8',
            'quality' => '1080p',
            'current_show' => 'Puppet Adventures, Stories & Sing-Alongs',
            'description' => 'Safe, positive, and educational cartoons and storybook adventures for kids.'
        ]
    ];

    // Filter by category
    if ($category !== 'all' && !empty($category)) {
        $cat = ($category === 'kids' || $category === 'animation') ? ['kids', 'animation'] : [$category];
        $channels = array_values(array_filter($channels, function($c) use ($cat) {
            return in_array($c['category'], $cat);
        }));
    }

    // Filter by search query
    if (!empty($search)) {
        $q = mb_strtolower(trim($search));
        $channels = array_values(array_filter($channels, function($c) use ($q) {
            return (
                str_contains(mb_strtolower($c['name']), $q) ||
                str_contains(mb_strtolower($c['category_name']), $q) ||
                str_contains(mb_strtolower($c['country_name']), $q) ||
                str_contains(mb_strtolower($c['current_show']), $q)
            );
        }));
    }

    return $channels;
}
