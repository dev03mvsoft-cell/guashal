<?php
// Dynamic URL Base Generator for automated environment adaptation
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'];
$current_url = $base_url . $_SERVER['REQUEST_URI'];

// Advanced Multi-City SEO Logic
$seo_valid_locations = ['Gandhidham', 'Bhuj', 'Anjar', 'Mandvi', 'Mundra', 'Adipur', 'Bhachau', 'Nakhatrana', 'Rapar', 'Gujarat', 'Kutch', 'India'];
$seo_location = 'Gandhidham'; // Highest Priority Target Location
$seo_locality = 'Gandhidham'; 

if (isset($_GET['loc']) && !empty($_GET['loc'])) {
    $requested_loc = ucwords(strtolower(trim(preg_replace('/[^a-zA-Z\s]/', '', $_GET['loc']))));
    if (in_array($requested_loc, $seo_valid_locations)) {
        $seo_location = $requested_loc;
        $seo_locality = $requested_loc;
    }
}

// Multi-Language Organization Constants (Dynamic Centralized Control)
$org_en = "Shri Gau Rakshak Seva Samiti";
$org_gu = "શ્રી ગૌ રક્ષક સેવા સમિતિ";
$org_hi = "श्री गौ रक्षक सेवा समिति";
$org_short = "GRSS";

// Dynamic Google Top Search Intents logic for titles
// High-volume keywords in India: Gaushala, Cow Donation, Gau Seva, Cow Shelter, Panjrapole
$seo_title_intent = "Gaushala, Cow Donation & Gau Seva in {$seo_location}";

// If location is specific or the priority target (Gandhidham / Bhuj), include Panjrapole
if($seo_location !== 'Gujarat' && $seo_location !== 'India') {
    $seo_title_intent = "{$seo_location} Gaushala & Panjrapole - Cow Donation";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Security Headers -->
    <?php
    // Set HTTP security headers
    header('X-Frame-Options: SAMEORIGIN'); // Prevent clickjacking
    header('X-Content-Type-Options: nosniff'); // Prevent MIME sniffing
    header('X-XSS-Protection: 1; mode=block'); // XSS filter
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=()');
    header("Content-Security-Policy: default-src 'self' https://cdn.tailwindcss.com https://fonts.googleapis.com https://fonts.gstatic.com https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com https://www.googletagmanager.com; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com; img-src 'self' data: https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com https://www.googletagmanager.com; font-src 'self' https://fonts.gstatic.com https://fonts.googleapis.com data:; connect-src 'self' https://www.googletagmanager.com; frame-ancestors 'self';");
    // Prevent tabnabbing
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-origin');
    // Disable directory listing (should also be set in .htaccess or server config)
    // Prevent information leakage
    header_remove('X-Powered-By');
    // Secure session cookies
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        session_start();
    }
    ?>

    <!-- Development Cache Control: Always fetch fresh code -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title><?= $seo_title_intent ?> | <?= $org_gu ?> | <?= $org_short ?></title>

    <!-- Primary SEO Meta Tags -->
    <meta name="description" content="Support gaushala and donate for cow seva in <?= $seo_location ?>. Help us protect and care for cows with your contribution. <?= $org_en ?> (<?= $org_gu ?> / <?= $org_hi ?>) is dedicated to the care and protection of cows. Donate online easily.">
    <meta name="keywords" content="gaushala donation <?= $seo_location ?>, cow donation <?= $seo_location ?>, gaushala near me, donate for cows <?= $seo_location ?>, gau seva donation online, support gaushala <?= $seo_location ?>, cow shelter donation India, gaushala charity trust <?= $seo_locality ?>, Gaushala in Gujarat, Cow Shelter in Kutch">
    <meta name="author" content="<?= $org_en ?> (<?= $org_gu ?>)">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">

    <!-- Open Graph / Regional SEO -->
    <meta property="og:title" content="<?= $seo_title_intent ?> | <?= $org_short ?>">
    <meta property="og:description" content="<?= $org_en ?> (<?= $org_gu ?>) is a trusted Panjrapole and cow shelter. We provide medical support to cows in need. Start your gau seva journey today.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= $current_url ?>">
    <meta property="og:image" content="<?= $base_url ?>/asset/img/logo/logo.png">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Donate for Gau Seva <?= $seo_location ?> | Cow Shelter in <?= $seo_locality ?>">
    <meta name="twitter:description" content="Support gaushala and donate for cow seva in <?= $seo_location ?>. Help us protect and care for cows with your contribution. Donate online easily.">

    <!-- Local SEO (<?= $seo_locality ?> Target) -->
    <meta name="geo.region" content="IN-GJ" />
    <meta name="geo.placename" content="<?= $seo_locality ?>, Kutch" />

    <!-- Schema.org JSON-LD LocalBusiness -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "NGO",
            "name": "<?= $org_en ?>",
            "alternateName": [
                "<?= $org_gu ?>",
                "<?= $org_hi ?>",
                "<?= $org_short ?> Gaushala"
            ],
            "description": "<?= $org_en ?> (<?= $org_gu ?> / <?= $org_hi ?>) is dedicated to the care and protection of cows in <?= $seo_location ?>. We provide shelter, food and medical support to cows in need. Your donation helps us continue our mission of gau seva and cow protection.",
            "url": "<?= $base_url ?>/",
            "logo": "<?= $base_url ?>/asset/img/logo/logo.png",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "<?= $seo_locality ?>",
                "addressRegion": "Gujarat",
                "postalCode": "370201",
                "addressCountry": "IN"
            },
            "areaServed": [{
                    "@type": "City",
                    "name": "Gandhidham"
                },
                {
                    "@type": "City",
                    "name": "Bhuj"
                },
                {
                    "@type": "City",
                    "name": "Anjar"
                },
                {
                    "@type": "City",
                    "name": "Mandvi"
                },
                {
                    "@type": "AdministrativeArea",
                    "name": "Kutch"
                },
                {
                    "@type": "AdministrativeArea",
                    "name": "Gujarat"
                }
            ],
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+91-9998581811",
                "contactType": "customer service",
                "areaServed": "IN",
                "availableLanguage": ["English", "Hindi", "Gujarati"]
            },
            "sameAs": [
                "https://www.facebook.com/grssgaushala"
            ]
        }
    </script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Pannellum (360 VR Viewer) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>

    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Font Awesome (Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 (Toaster) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/asset/img/logo/logo.png">
    <link rel="apple-touch-icon" href="/asset/img/logo/logo.png">


    <!-- Custom CSS (Cache Busting version) -->
    <link rel="stylesheet" href="/asset/css/style.css?v=<?php echo time(); ?>">


    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'saffron': '#FF6A00',
                        'gold': '#c0a50eff',
                        'nature': '#2c4c3b',
                        'primary': '#FF6A00',
                        'secondary': '#fffcf9',
                        'accent': '#FFD700',
                    },
                    fontFamily: {
                        'sans': ['Outfit', 'sans-serif'],
                        'display': ['Playfair Display', 'serif'],
                    }
                }
            }
        }

        /* --- Language Bridge --- */
        function changeLanguage(lang) {
            localStorage.setItem('site_lang', lang);
            if (window.applyLanguage) {
                window.applyLanguage();
            } else {
                location.reload();
            }
        }

        // Initialize SweetAlert Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    </script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Ensure SweetAlert toasts appear above the fixed navbar */
        .swal2-container {
            z-index: 999999 !important;
        }
    </style>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-2M01098BDS"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-2M01098BDS');
    </script>
</head>

<body class="bg-secondary text-primary font-sans overflow-x-hidden">

    <!-- Premium Announcement Marquee (Top Bar) -->
    <?php
    $marquee_items = [];
    try {
        $stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");
        if ($stmt) {
            $marquee_items = $stmt->fetchAll();
        }
    } catch (Exception $e) {
        $marquee_items = []; // Fallback to static if table doesn't exist
    }
    ?>
    <div class="top-announcement bg-gradient-to-r from-nature via-nature/90 to-nature border-b border-gold/20 py-2 fixed top-0 left-0 w-full z-[10000] overflow-hidden transition-transform duration-500">
        <div class="container mx-auto px-6 flex items-center gap-4">
            <span class="bg-saffron text-white text-[12px] font-bold px-3 py-1 rounded-full uppercase tracking-widest hidden md:block whitespace-nowrap shadow-lg shadow-saffron/20 border border-white/10" data-lang="latest_label">Latest Update</span>
            <div class="flex-1 overflow-hidden relative">
                <div class="flex gap-20 animate-marquee whitespace-nowrap items-center py-1">
                    <?php if (!empty($marquee_items)): ?>
                        <?php foreach ($marquee_items as $item): ?>
                            <p class="text-white/90 text-xs font-bold tracking-wide flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-gold rounded-full animate-pulse shadow-[0_0_10px_#FFD700]"></span>
                                <span data-trans="en"><?= htmlspecialchars($item['message_en']) ?></span>
                            </p>
                        <?php endforeach; ?>
                        <!-- Duplicated for loop -->
                        <?php foreach ($marquee_items as $item): ?>
                            <p class="text-white/90 text-xs font-bold tracking-wide flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-gold rounded-full animate-pulse shadow-[0_0_10px_#FFD700]"></span>
                                <span data-trans="en"><?= htmlspecialchars($item['message_en']) ?></span>
                            </p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback Static Content -->
                        <p class="text-white/90 text-xs font-bold tracking-wide flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-gold rounded-full animate-pulse shadow-[0_0_10px_#FFD700]"></span>
                            <span data-lang="marquee_msg_1">New Medical ICU for our Gau Mata was inaugurated last week — Thank you for your donations!</span>
                        </p>
                        <p class="text-white/90 text-xs font-bold tracking-wide flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-gold rounded-full animate-pulse shadow-[0_0_10px_#FFD700]"></span>
                            <span data-lang="marquee_msg_2">Upcoming Gau Sewa Shivir on 15th April — Volunteer registrations are now open.</span>
                        </p>
                        <p class="text-white/90 text-xs font-bold tracking-wide flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-gold rounded-full animate-pulse shadow-[0_0_10px_#FFD700]"></span>
                            <span data-lang="marquee_msg_3">Donation for annual fodder supply is in progress — Contribute your first roti today.</span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hidden md:flex gap-4 border-l border-white/10 pl-6 text-[12px] items-center">
                <a href="tel:+919998581811" class="text-white/70 hover:text-gold transition-colors font-bold"><i class="fas fa-phone-alt mr-2 text-saffron"></i><span data-lang="header_phone">+91 9998581811 / 9824284733</span></a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="fixed top-[40px] md:top-[45px] left-0 w-full z-[9999] transition-all duration-500 backdrop-blur-md bg-nature/95 shadow-lg border-b border-white/10 nav-main">
        <div class="container mx-auto px-6 py-2 md:py-3 flex justify-between items-center transition-all duration-500 nav-container">
            <a href="/" class="flex items-center group transition-all duration-500 hover:scale-[1.03] active:scale-95">
                <div class="relative">
                    <!-- Premium Wide Glow -->
                    <div class="absolute inset-0 bg-gold/20 rounded-full blur-2xl group-hover:bg-saffron/30 transition-colors duration-700"></div>

                    <!-- Branding Capsule -->
                    <div class="relative bg-gradient-to-r from-white via-white to-[#fff9f2] border-2 border-gold rounded-full flex items-center px-1 py-1 gap-4 shadow-xl h-20 max-w-[90vw] pr-6 sm:pr-[60px] ml-6 sm:ml-10 scale-90 sm:scale-100 origin-left">
                        <!-- Logo Circle POP OUT -->
                        <div class="h-20 w-20 -ml-9 rounded-full flex items-center justify-center flex-shrink-0 bg-white border-2 border-gold shadow-lg p-0">
                            <img src="/asset/img/logo/logo.png" class="h-full w-full object-contain" alt="Logo">
                        </div>
                        <!-- Brand Typography Integrated -->
                        <div class="flex flex-col overflow-hidden">
                            <span class="text-nature font-display text-lg font-bold leading-tight">શ્રી ગૌ રક્ષક સેવા સમિતિ</span>
                            <span class="text-[10px] uppercase tracking-[0.3em] text-saffron font-black">Panjrapole</span>
                        </div>
                    </div>


                </div>
                <ul class="hidden md:flex gap-8 items-center font-medium text-white/90">
                    <li><a href="/" class="hover:text-gold transition-colors text-[15px]" data-lang="nav_home">Home</a></li>
                    <!-- About Dropdown -->
                    <li class="relative group">
                        <a href="/about" class="hover:text-gold transition-all duration-500 flex items-center gap-2 py-4 text-[15px]" data-lang="nav_about">
                            About
                            <svg class="w-2.5 h-2.5 group-hover:rotate-180 transition-transform duration-700 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </a>

                        <!-- High-Fidelity Floating Dropdown -->
                        <div class="absolute top-[100%] left-1/2 -translate-x-1/2 w-56 bg-white/95 backdrop-blur-3xl rounded-[2rem] shadow-[0_40px_100px_-20px_rgba(44,76,59,0.2)] border border-gold/10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-500 transform origin-top z-[10001] scale-95 group-hover:scale-100 overflow-hidden mt-2">
                            <div class="py-4">
                                <a href="/about" class="group/item block px-8 py-3.5 text-[15px] font-bold text-nature hover:bg-gold/5 transition-all flex items-center gap-4">
                                    <span class="w-1.5 h-1.5 rounded-full bg-saffron/30 group-hover/item:bg-saffron group-hover/item:scale-150 transition-all duration-500"></span>
                                    <span class="group-hover/item:translate-x-1 transition-transform" data-lang="nav_our_story">Our Story</span>
                                </a>
                                <a href="/founders" class="group/item block px-8 py-3.5 text-[15px] font-bold text-nature hover:bg-gold/5 transition-all flex items-center gap-4">
                                    <span class="w-1.5 h-1.5 rounded-full bg-saffron/30 group-hover/item:bg-saffron group-hover/item:scale-150 transition-all duration-500"></span>
                                    <span class="group-hover/item:translate-x-1 transition-transform" data-lang="nav_founders">Founders</span>
                                </a>
                                <a href="/team" class="group/item block px-8 py-3.5 text-[15px] font-bold text-nature hover:bg-gold/5 transition-all flex items-center gap-4">
                                    <span class="w-1.5 h-1.5 rounded-full bg-saffron/30 group-hover/item:bg-saffron group-hover/item:scale-150 transition-all duration-500"></span>
                                    <span class="group-hover/item:translate-x-1 transition-transform" data-lang="nav_team">Our Team</span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li><a href="/gallery" class="hover:text-gold transition-colors text-[15px]" data-lang="nav_gallery">Gallery</a></li>
                    <li><a href="/events" class="hover:text-gold transition-colors text-[15px]" data-lang="nav_events">Events</a></li>
                    <li><a href="/announcements" class="hover:text-gold transition-colors text-[15px]" data-lang="nav_announcements">Announcements</a></li>
                    <li><a href="/donors" class="hover:text-gold transition-colors text-[15px]" data-lang="nav_donors">Hall of Fame</a></li>
                    <li><a href="/contact" class="hover:text-gold transition-colors text-[15px]" data-lang="nav_contact">Contact</a></li><!-- Language Switcher Dropdown (Vedic Premium) -->
                    <li class="relative group ml-4">
                        <button class="bg-white/10 backdrop-blur-md border border-white/20 px-5 py-2 rounded-full text-[12px] font-bold uppercase tracking-widest flex items-center gap-2 group-hover:bg-gold group-hover:text-nature transition-all duration-500 shadow-sm shadow-black/20">
                            <span id="current-lang">English</span>
                            <svg class="w-3 h-3 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <!-- Floating Dropdown -->
                        <div class="absolute right-0 mt-3 w-44 bg-white/95 backdrop-blur-2xl rounded-2xl shadow-2xl border border-gold/10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-500 transform origin-top-right py-3 z-[10000] scale-90 group-hover:scale-100">

                            <button onclick="changeLanguage('en')" class="w-full text-left px-6 py-3 text-[15px] font-bold text-nature hover:bg-saffron/10 hover:text-saffron transition-all flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-saffron/20"></span> English
                            </button>
                            <button onclick="changeLanguage('hi')" class="w-full text-left px-6 py-3 text-[15px] font-bold text-nature hover:bg-saffron/10 hover:text-saffron transition-all font-hindi flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-saffron/20"></span> हिंदी (Hindi)
                            </button>
                            <button onclick="changeLanguage('gu')" class="w-full text-left px-6 py-3 text-[15px] font-bold text-nature hover:bg-saffron/10 hover:text-saffron transition-all font-hindi flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-saffron/20"></span> ગુજરાતી (Guj)
                            </button>
                        </div>
                    </li>

                    <li><a href="/donate" class="bg-saffron text-white px-8 py-2 rounded-full hover:bg-gold hover:text-nature transition-all duration-500 shadow-xl shadow-saffron/20 border border-saffron/20 font-bold text-[15px]" data-lang="nav_donate">Donate</a></li>
            </a></li>
            </ul>

            <!-- Mobile Burger Icon -->
            <button id="menu-toggle" class="md:hidden text-white focus:outline-none">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path class="menu-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    <path class="menu-close hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Drawer Menu -->
        <div id="mobile-menu" class="hidden md:hidden absolute top-full left-0 w-full bg-white backdrop-blur-3xl border-t border-gold/10 shadow-2xl overflow-hidden py-10">
            <ul class="flex flex-col gap-8 font-display text-2xl text-nature items-center">
                <li><a href="/" class="mobile-nav-link hover:text-saffron transition-colors" data-lang="nav_home">Home</a></li>
                <li><a href="/about" class="mobile-nav-link hover:text-saffron transition-colors" data-lang="nav_our_story">Our Story</a></li>
                <li><a href="/founders" class="mobile-nav-link hover:text-saffron transition-colors" data-lang="nav_founders">Founders</a></li>
                <li><a href="/team" class="mobile-nav-link hover:text-saffron transition-colors" data-lang="nav_team">Our Team</a></li>
                <li><a href="/gallery" class="mobile-nav-link hover:text-saffron transition-colors" data-lang="nav_gallery">Gallery</a></li>
                <li><a href="/events" class="mobile-nav-link hover:text-saffron transition-colors" data-lang="nav_events">Events</a></li>
                <li><a href="/announcements" class="mobile-nav-link hover:text-saffron transition-colors" data-lang="nav_announcements">Announcements</a></li>
                <li><a href="/donors" class="mobile-nav-link hover:text-saffron transition-colors" data-lang="nav_donors">Hall of Fame</a></li>
                <li><a href="/contact" class="mobile-nav-link hover:text-saffron transition-colors" data-lang="nav_contact">Contact</a></li>

                <!-- Mobile Language Selector -->
                <li class="flex gap-4 mt-4">
                    <button onclick="changeLanguage('en')" class="w-10 h-10 rounded-full border border-gold/20 flex items-center justify-center text-xs font-bold text-nature hover:bg-gold transition-colors">EN</button>
                    <button onclick="changeLanguage('hi')" class="w-10 h-10 rounded-full border border-gold/20 flex items-center justify-center text-xs font-bold text-nature hover:bg-gold transition-colors">HI</button>
                    <button onclick="changeLanguage('gu')" class="w-10 h-10 rounded-full border border-gold/20 flex items-center justify-center text-xs font-bold text-nature hover:bg-gold transition-colors">GU</button>
                </li>

                <li><a href="/donate" class="bg-saffron text-white px-12 py-4 rounded-full shadow-xl shadow-saffron/20 font-bold" data-lang="nav_donate">Donate</a></li>
            </ul>
        </div>
    </nav>

    <main>