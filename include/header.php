<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaushala - Modern Sanctuary</title>

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


    <!-- Custom CSS -->
    <link rel="stylesheet" href="/asset/css/style.css">


    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'saffron': '#FF6A00',
                        'gold': '#FFD700',
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

        /* --- Premium Background Translator Engine --- */
        async function changeLanguage(lang) {
            localStorage.setItem('site_lang', lang);
            if (window.applyLanguage) {
                window.applyLanguage();
            } else {
                location.reload();
            }
        }

        async function translateAllDynamicContent(target) {
            const elements = document.querySelectorAll('[data-trans="en"], .translatable');
            for (const el of elements) {
                const originalText = el.getAttribute('data-origin') || el.innerText;
                if (!el.getAttribute('data-origin')) el.setAttribute('data-origin', originalText);
                try {
                    const res = await fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=${target}&dt=t&q=${encodeURIComponent(originalText)}`);
                    const data = await res.json();
                    if (data && data[0]) {
                        el.innerText = data[0].map(x => x[0]).join('');
                        el.style.animation = 'fadeIn 0.5s ease';
                    }
                } catch (e) {
                    console.error("Translation Error", e);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('site_lang') || 'en';
            if (savedLang !== 'en') {
                translateAllDynamicContent(savedLang);
                if (document.getElementById('current-lang')) {
                    const labels = {
                        'en': 'English',
                        'hi': 'Hindi',
                        'gu': 'Gujarati'
                    };
                    document.getElementById('current-lang').innerText = labels[savedLang];
                }
            }
        });

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
    <div class="bg-gradient-to-r from-nature via-nature/90 to-nature border-b border-gold/20 py-2 fixed top-0 left-0 w-full z-[10000] overflow-hidden">
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
                <a href="tel:+919998581811" class="text-white/70 hover:text-gold transition-colors font-bold"><i class="fas fa-phone-alt mr-2 text-saffron"></i>+91 99985 81811</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="fixed top-[41px] left-0 w-full z-[9999] transition-all duration-300 backdrop-blur-md bg-nature/95 shadow-lg border-b border-white/10 nav-main">
        <div class="container mx-auto px-6 py-2 md:py-3 flex justify-between items-center transition-all duration-500 nav-container">
            <a href="/" class="flex items-center group transition-all duration-500 hover:scale-[1.03] active:scale-95">
                <div class="relative">
                    <!-- Premium Wide Glow -->
                    <div class="absolute inset-0 bg-gold/20 rounded-full blur-2xl group-hover:bg-saffron/30 transition-colors duration-700"></div>

                    <!-- Branding Capsule -->
                    <div class="relative bg-gradient-to-r from-white via-white to-[#fff9f2] border-2 border-gold rounded-full flex items-center px-1 py-1 gap-2 md:gap-4 shadow-xl h-12 md:h-20 max-w-fit pr-4 md:pr-10 ml-3 md:ml-12">
                        <!-- Logo Circle POP OUT -->
                        <div class="h-14 w-14 md:h-24 md:w-24 -ml-5 md:-ml-16 rounded-full flex items-center justify-center p-2 flex-shrink-0 bg-white border-2 border-gold shadow-lg z-10 transition-transform duration-500 group-hover:scale-110">
                            <img src="/asset/img/logo/logo.png" class="h-full w-full object-contain" alt="Logo">
                        </div>
                        <!-- Brand Typography Integrated -->
                        <div class="flex flex-col overflow-hidden">
                            <span class="text-nature font-display text-[12px] md:text-xl font-bold leading-tight" data-lang="brand_name">શ્રી ગૌ રક્ષક સેવા સમિતિ</span>
                            <span class="text-[12px] md:text-[12px] uppercase tracking-[0.1em] md:tracking-[0.4em] text-saffron font-black" data-lang="brand_tagline">Panjrapole</span>
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