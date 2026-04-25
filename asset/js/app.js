// Standard Theme Logic for Gaushala Sanctuary
document.addEventListener('DOMContentLoaded', () => {
    // 1. Smooth Scroll for Navbar
    const nav = document.querySelector('.nav-main');
    const navContainer = document.querySelector('.nav-container');
    const topBar = document.querySelector('.top-announcement');

    function updateNav() {
        if (!nav || !navContainer) return;

        if (window.scrollY > 30) {
            nav.classList.add('shadow-2xl', 'top-0');
            nav.classList.remove('shadow-lg', 'top-[40px]', 'md:top-[45px]');
            navContainer.classList.add('py-1', 'md:py-2');
            navContainer.classList.remove('py-2', 'md:py-3');
            if (topBar) topBar.classList.add('-translate-y-full');
        } else {
            nav.classList.add('shadow-lg', 'top-[40px]', 'md:top-[45px]');
            nav.classList.remove('shadow-2xl', 'top-0');
            navContainer.classList.remove('py-1', 'md:py-2');
            navContainer.classList.add('py-2', 'md:py-3');
            if (topBar) topBar.classList.remove('-translate-y-full');
        }
    }

    window.addEventListener('scroll', updateNav);
    updateNav(); // Immediate check on DOMContentLoaded

    // 2. Mobile Menu Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuOpen = document.querySelector('.menu-open');
    const menuClose = document.querySelector('.menu-close');

    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            menuOpen.classList.toggle('hidden');
            menuClose.classList.toggle('hidden');
        });
    }

    // 3. VR Toggle Logic
    const vrToggle = document.getElementById('vr-toggle');
    const panorama = document.getElementById('panorama');
    const heroContent = document.querySelector('.hero-content');
    const heroDots = document.getElementById('hero-dots');
    const heroOverlay = document.getElementById('hero-overlay');
    const heroGradient = document.getElementById('hero-gradient');

    if (vrToggle) {
        vrToggle.addEventListener('click', () => {
            const isVR = panorama.classList.contains('opacity-0');

            if (isVR) {
                // Enter VR Mode
                panorama.classList.remove('opacity-0', 'pointer-events-none');
                heroContent.classList.add('opacity-0', 'pointer-events-none');
                heroDots.classList.add('opacity-0');
                heroOverlay.classList.add('opacity-0');
                heroGradient.classList.add('opacity-0');
                vrToggle.innerHTML = '<i class="fas fa-times"></i> Exit 360° View';

                // Initialize Pannellum if not already done
                if (!window.viewer) {
                    window.viewer = pannellum.viewer('panorama', {
                        "type": "equirectangular",
                        "panorama": `${window.GA_BASE_URL}/asset/img/cow/ohhh.png`,
                        "autoLoad": true,
                        "showControls": false
                    });
                }

            } else {
                // Exit VR Mode
                panorama.classList.add('opacity-0', 'pointer-events-none');
                heroContent.classList.remove('opacity-0', 'pointer-events-none');
                heroDots.classList.remove('opacity-0');
                heroOverlay.classList.remove('opacity-0');
                heroGradient.classList.remove('opacity-0');
                vrToggle.innerHTML = '<span class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></span> Enter 360° Virtual Reality';
            }
        });
    }

    // 4. Achievement Counters
    const counters = document.querySelectorAll('.counter');
    const speed = 200;

    const animateCounters = () => {
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const inc = target / speed;

            if (count < target) {
                counter.innerText = Math.ceil(count + inc);
                setTimeout(animateCounters, 1);
            } else {
                counter.innerText = target;
            }
        });
    };

    // Trigger counters only when they come into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(c => observer.observe(c));

    // 5. Hero Carousel (Swiper)
    const swiperHero = new Swiper('.swiper-hero', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
    });

    // 5b. Shlok Translation Carousel
    const swiperText = new Swiper('.swiper-text', {
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        allowTouchMove: false,
    });

    // 6. Common Swipers (Vertical Mission Swiper)
    const verticalSwiper = new Swiper('.swiper-vertical', {
        direction: 'vertical',
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        speed: 1500,
        effect: 'slide',
    });

    // 6b. Gallery Swiper (New Horizontal Slider)
    const gallerySwiper = new Swiper('.swiper-gallery', {
        slidesPerView: 'auto',
        spaceBetween: 20,
        loop: true,
        speed: 5000,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        freeMode: true,
        navigation: {
            nextEl: '.gallery-next',
            prevEl: '.gallery-prev',
        },
        breakpoints: {
            1024: {
                spaceBetween: 30,
            }
        }
    });

    // 6c. Testimonials Swiper
    const swiperTestimonials = new Swiper('.swiper-testimonials', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.testi-next',
            prevEl: '.testi-prev',
        },
        breakpoints: {
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 }
        }
    });

    window.allSwipers = { swiperHero, swiperText, verticalSwiper, swiperTestimonials, gallerySwiper };

    // 7. Initialize AOS (CRITICAL: Otherwise data stays hidden)
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 0, // Trigger immediately when entering viewport
            easing: 'ease-out-quad',
            mirror: false
        });

        // Refresh AOS on full window load to account for images
        window.addEventListener('load', () => {
            AOS.refresh();
        });
    } else {
        console.error("AOS is not defined. Please check if the script is loaded.");
        document.querySelectorAll('[data-aos]').forEach(el => el.style.opacity = '1');
    }

    // Initial Language Apply
    applyLanguage();
});

// Global Multi-language System (English, Hindi, Gujarati)
const translations = {
    'en': {
        // Nav & Hero
        'nav_home': 'Home', 'nav_about': 'About', 'nav_contact': 'Contact', 'nav_donate': 'Donate',
        'nav_founders': 'Founders', 'nav_team': 'Our Team', 'nav_gallery': 'Gallery', 'nav_events': 'Events',
        'nav_bulletin': 'Bulletin', 'nav_announcements': 'Announcements', 'nav_our_story': 'Our Story',
        'nav_donors': 'Donate Wall', 'nav_updates': 'Updates',
        'brand_name': 'Shree Gau Rakshak Seva Samiti', 'brand_tagline': 'Panjrapole - Galpadar',
        'header_phone': '+91 99985 81811',
        'hero_span': 'Shree Radhe Radhe ',
        'hero_h1': 'A <span class="italic text-gold">Mother</span> Who Speaks No Words, <br> Yet <span class="text-gold underline decoration-white/20 underline-offset-8">Feeds the World</span> — <br> <span class="text-white opacity-90 italic">Let Our Love Protect Her</span>',
        'shlok_trans_en': '"Experience the spirituality of Gau Seva in its purest form. Join us in preserving the sanctity of our sacred cows through care, protection, and devotion."',
        'explore_mission': 'Explore Mission', 'vr_tour': 'Enter 360° Virtual Reality',
        // Connection & Mission
        'connection_label': 'The Eternal Bond', 'connection_title': 'Why People <span class="italic text-gold">Connect</span> With Us',
        'mission_label': 'The Sacred Impact', 'mission_title1': 'Healing Hearts,', 'mission_title2': 'Protecting Life',
        'mission_quote': '"They shared their life to nourish ours. Now, it’s our turn to protect them. Join us in giving every cow a home filled with love and dignity."',
        'stat_serving': 'Serving with Love & Care!', 'stat_treatment': 'Under Treatment: Fighting for Life!', 'stat_healed': 'Healed & Given a New Chance!',
        'join_cause': 'Join the Cause',
        // Transparency
        'trust_label': 'Trust & Transparency', 'trust_title': 'Where your Donations go?', 'trust_quote': '"Every rupee you contribute is a silent promise of life to our cows."',
        'raised_label': 'Raised', 'raised_out_of': 'raised out of', 'goal_label': 'Goal',
        'table_material': 'Material', 'table_quantity': 'Quantity', 'table_price': 'Price/Unit', 'table_total': 'Total Amount',
        // Reality check
        'reality_label': 'Critical Status Report', 'reality_title': 'State of the Gau Mata', 'reality_subtitle': '(Transition 2000 — 2026)',
        'recent_condition_label': 'Recent Condition', 'status_critical': 'Status: Critical', 'case_title': 'Rescued Patient Case #450',
        'case_desc': 'This mother was rescued from highway collision. Our team is working 24/7 on her recovery.', 'support_life': 'Support Her Life',
        // Seva Options
        'seva_label': 'Divine Opportunities', 'seva_title': 'Gaushala Seva', 'seva_quote': 'Choose your path of service and be the light in their lives.',
        'seva_roti_title': 'First Roti for Cow', 'seva_roti_desc': 'Start your day with the divine blessing of feeding the mother.',
        'seva_adopt_title': 'Life Adoption', 'seva_adopt_desc': 'Provide total care, food, and medicine for one Gau Mata.',
        'seva_nandi_title': 'Nandi Seva', 'seva_nandi_desc': 'Honor the strength and divinity of the sacred Nandi Dev.',
        'seva_treatment_title': 'Medical Seva', 'seva_treatment_desc': 'Fund critical surgeries and life-saving procedures.',
        'seva_shed_title': 'Shed Construction', 'seva_shed_desc': 'Help us build stable, comfortable homes for more rescues.',
        'seva_feed20_title': 'Bhandara Seva', 'seva_feed20_desc': 'A massive act of kindness that ensures no one goes hungry.',
        'seva_calf_title': 'Calf Nurturing', 'seva_calf_desc': 'Nurture the future by supporting an innocent, growing calf.',
        'seva_med_title': 'Medicine Kit', 'seva_med_desc': 'Ensure every rescued soul has access to essential medicine.',
        'seva_fodder_title': 'Green Fodder', 'seva_fodder_desc': 'Provide fresh, nutritious green grass for daily nourishment.',
        'gau_datt_sub': 'Divine Foster Care', 'gau_datt_title': 'Gau Dattak <span class="italic text-gold italic">Seva</span>', 'connect_host': 'Connect to Foster a Cow',
        // Portal
        'donate_offer': 'Offer Your Devotion', 'donate_title': 'Sanctuary <span class="italic text-gold">Registry</span>', 'donate_label': 'Gau Mata Sanrakshan',
        'donate_bank_label': 'Bank Account Details',
        'donate_desc': 'From the harsh streets of the city to the eternal peace of our sanctuary—every recovery is a story written by you.',
        'donors_hero_label': 'The Sacred Donate Wall', 'donors_hero_title': 'Our <span class="italic text-gold underline decoration-gold/20 underline-offset-[16px]">Noble</span> Donors', 'donors_hero_desc': '"Serving the Gaia is serving the Divine." We honor the souls whose compassion sustains our sanctuary.',
        'donors_search_placeholder': 'Search noble donors by name or purpose...', 'instant_filter': 'Instant Filter',
        'donors_empty_title': 'Our family is growing', 'donors_empty_desc': 'Be the first to join our Sacred Donate Wall.',
        'table_profile': 'Profile', 'table_donor_info': 'Donor Name & Purpose', 'table_donation_details': 'Donation Details', 'table_total_amount': 'Total Amount',
        'verified_offering': 'Verified Offering', 'gratitude_bg': 'GRATITUDE', 'donors_footer_text': 'Sacred Contributions for Perpetual Protection of Gaia',
        'donors_cta_title': 'Join Our <span class="italic text-gold underline decoration-gold/30 underline-offset-8">Divine Circle</span>', 'donors_cta_desc': 'Your contribution ensures the dignity and protection of our sacred Gaia. Become a guardian of the divine today.', 'donors_cta_btn': 'Become a Donor',
        'donate_subtitle': 'Divine Service Center', 'form_seva_label': 'Choose Seva', 'form_amount_label': 'Amount (₹)',
        'form_id_label': 'Sacred Identification', 'form_birthday_label': 'Birthday Ritual', 'form_select_opt': 'Select Domain',
        'currency_inr': 'Indian Currency (INR)', 'currency_foreign': 'Non-Indian Currency',
        'form_pincode': 'City Pincode', 'form_submit': 'Perform Divine Sewa',
        'placeholder_pincode': 'Pincode', 'placeholder_amount': '₹ Custom', 'placeholder_phone': 'WhatsApp Number', 'placeholder_ritual': 'Birthday Ritual',
        'donate_verify_label': 'UPI Payment', 'donate_upi_label': 'Direct UPI Passage', 'donate_trust_label': 'Sanctuary Trust', 'donate_tax_label': 'Tax Exemption U/S 80G - Income Tax Act 1961',
        'seva_fallback': 'Divine opportunities are being prepared...',
        'view_all_seva': 'View All Opportunities',
        // Testimonials
        'testimonials_label': 'Voices of Devotion', 'testimonials_title': 'What our Devotees say',
        'testi1_n': 'Rajesh Sharma', 'testi1_r': 'Monthly Donor', 'testi1_t': 'Visiting the Gaushala changed my perspective on life. Seeing the care given to injured cows is truly divine. It\'s a sanctuary of peace.', 'testi1_initial': 'R',
        'testi2_n': 'Priya Patel', 'testi2_r': 'Volunteer', 'testi2_t': 'The transparency here is unmatched. I know exactly how my small contribution helps in building sheds and buying medicines.', 'testi2_initial': 'P',
        'testi3_n': 'Amit Verma', 'testi3_r': 'Visitor', 'testi3_t': 'A truly spiritual experience. The 360° VR tour on the website is amazing, but visiting in person is even better for the soul.', 'testi3_initial': 'A',
        'testi4_n': 'Meera Bai', 'testi4_r': 'Monthly Donor', 'testi4_t': 'The health checkups and ICU services are world-class. It\'s heartening to see such devotion toward these sacred beings.', 'testi4_initial': 'M',
        // Welfare
        'welfare_label': 'Core Mission', 'welfare_title': 'Welfare Beyond Boundaries', 'welfare_quote': '"True service is not just providing food, but restoring the dignity of life itself."',
        'welfare_icu_title': 'Veterinary ICU', 'welfare_icu_desc': 'Immediate surgical intervention for accident victims.',
        'welfare_nutrition_title': 'Organic Nutrition', 'welfare_nutrition_desc': 'Scientifically balanced green fodder and supplements.',
        'welfare_rehab_title': 'Trauma Rehab', 'welfare_rehab_desc': 'Dedicated recovery zones for cattle rescued from abuse.',
        // Table Rows
        'row_shed': 'Shed Construction Material', 'row_medical': 'Medical Kit', 'row_fodder': 'Green Fodder Bundle',
        'row_jaggery': 'Jaggery', 'row_calcium': 'Animal Calcium (10L)', 'row_dry_fodder': 'Dry Fodder Bundle',
        'row_daliya': 'Daliya', 'row_sugar_cane': 'Sugar Cane Bundle',
        // Gallery
        'gallery_label': 'Sacred Sanctuary', 'gallery_title': 'Moments of Devotion',
        // Marquee
        'latest_label': 'Latest News',
        'marquee_msg_1': 'New Medical ICU for our Gau Mata was inaugurated last week — Thank you for your donations!',
        'marquee_msg_2': 'Upcoming Gau Sewa Shivir on 15th April — Volunteer registrations are now open.',
        'marquee_msg_3': 'Donation for annual fodder supply is in progress — Contribute your first roti today.',
        // About Page
        'about_hero_span': 'Established with Devotion',
        'about_hero_h1': 'Our <span class="italic text-gold">Sacred</span> Legacy',
        'about_mission_quote': 'Where every life is sacred, and every heartbeat tells a story of <span class="text-saffron font-bold">compassion</span>. We are the guardians of the divine.',
        'about_mission_label': '— The Gaushala Philosophy',
        'about_years_exp': 'Years of Constant Service',
        'about_journey_label': 'Our History & Divine Mission',
        'about_journey_title': 'A Legacy of <span class="italic text-gold">Compassion</span>, A Calling of Service',
        'about_history_title': 'The Sacred Genesis',
        'about_journey_p1': 'Deeply rooted in the heart of Galpadar, Gandhidham (Reg No. GUJ1517/F/1693KUTCH), our path began as a humble prayer. What started with the rescue of just three native Gir cows has blossomed into a world-class sanctuary, where traditional Vedic wisdom meets modern conservation.',
        'about_mission_title': 'Our Divine Mission',
        'about_journey_p2': 'To ensure that no sacred soul in the Kutch region suffers from the cold touch of neglect. We strive to restore the eternal bond between humanity and the Gau Mata, creating an ecosystem where protection, dignity, and spiritual well-being are the absolute priority.',
        'about_pillar_vedic': 'Vedic Lineage', 'about_pillar_vedic_desc': 'Preserving indigenous Gir, Kankrej & Tharparkar breeds.',
        'about_pillar_sustenance': 'Sustenance', 'about_pillar_sustenance_desc': '15+ Acres of organic fodder & sustainable Panchgavya research.',
        'about_care_title': 'Best Health Care', 'about_care_desc': 'Doctors available 24/7 and healthy natural food.',
        'about_ethical_title': 'Happy Life', 'about_ethical_desc': 'A life of respect and peace for every cow.',
        'about_fac_label': 'Everything We Do', 'about_fac_title': 'Services & <span class="text-gold italic">Daily Work</span>',
        'about_fac_h1_title': 'Our Animal Hospital', 'about_fac_h1_desc': 'Our home has a medical unit with experienced doctors. We have special rooms for sick or hurt cows, making sure they get the best care and natural treatments.',
        'about_fac_h2_title': 'Protecting Desi Cows', 'about_fac_h2_desc': 'We focus on keeping pure Indian cow breeds like Gir and Kankrej. These cows are known for healthy A2 milk and stay strong in our hot weather.',
        'about_fac_h3_title': 'Natural Grass & Farming', 'about_fac_h3_desc': 'We grow natural green grass on 15 acres. cows are fed a healthy mix of dry grass and natural vitamins, without any bad chemicals.',
        'about_fac_h4_title': 'Making Natural Fertilizers', 'about_fac_h4_desc': 'Using cow urine and dung, we make high-quality natural fertilizers. This helps us grow our own food and helps nearby farmers avoid chemicals.',
        'about_fac_h5_title': 'Peaceful Environment', 'about_fac_h5_desc': 'Our day starts with peaceful sounds. We clean the cows, brush them, and let them roam freely in the fields without any stress.',
        'about_fac_h6_title': 'Teaching Others', 'about_fac_h6_desc': 'We invite schools and students to learn about why cows are important for nature. Visitors can see how we turn cow waste into useful things for farming.',
        'about_pillar_title': 'Our <span class="text-gold italic">Foundation</span>',
        'about_pillar_h1_title': 'Natural Wisdom', 'about_pillar_h1_desc': 'Using natural herbal treatments and a peaceful home for the well-being of our cows.',
        'about_pillar_h2_title': 'Helping Nature', 'about_pillar_h2_desc': 'Using nature to make organic fertilizers and making sure nothing goes to waste.',
        'about_pillar_h3_title': 'Serving with Love', 'about_pillar_h3_desc': 'A place where anyone can come and enjoy the joy of helping these animals.',
        'about_stat_cows': 'Total Cows we care for', 'about_stat_acres': 'Open Green Fields', 'about_stat_trees': 'Green Trees', 'about_stat_visitors': 'Monthly Guests',
        'about_cta_title': 'Join us in <span class="italic text-saffron underline decoration-gold/30 underline-offset-8">Helping Cows</span>', 'about_cta_desc': 'Your help ensures every cow here gets the best care they need. Whether you visit or donate, you are doing a great thing.',
        'about_cta_btn1': 'Donate Now', 'about_cta_btn2': 'Come Visit Us',
        // Miscellaneous
        'lang_name': 'English',
        'cta_label': 'Help our Cows', 'cta_title1': 'Once Honoured and Loved,', 'cta_title2': 'Now left on the roads', 'cta_title3': 'Let us bring them home.', 'cta_btn': 'Serve a Cow',
        // Founders Page
        'founders_hero_span': 'The Visionaries of Gaushala', 'founders_hero_h1': 'Our <span class="italic text-gold">Founding</span> Souls',
        'founders_label': 'Sacred Lineage', 'founders_title': 'The Founding Visionaries',
        'founders_quote_main': '"A sanctuary is not built of bricks, but of the silent prayers and unshakeable compassion of those who see divinity in the cow’s eyes. This sacred path of motherhood is eternal."',
        'founders_p1': 'Founded in the heart of Kutch, Shri Gau Rakshak Seva Samiti began with a singular, divine realization by our visionary founders: that the health of our civilization is mirrored in the care we give to the Gau Mata.',
        'heal_title': 'Cow Treatment Seva Initiative',
        'heal_desc': "The cornerstone of Gaushala Naitrutav' vision was the establishment of a specialized medical wing. Today, our 24/7 ICU serves as a silent testament to their commitment to life, healing thousands of injured mother cows every year.",
        'legacy_cont': 'Legacy of Compassion Continued...',
        'founders_cta_title': 'Keep the <span class="italic text-saffron underline decoration-gold/30 underline-offset-8">Vision Alive</span>',
        'founders_cta_desc': 'Support the sanctuary and carry forward the legacy of Gaushala Naitrutav. Every contribution makes a difference.',
        'founders_foundational_spirit': 'Foundational Spirit', 'founders_visionary': 'Chief Visionary',
        'founders_h2': 'Gaushala Naitrutav', 'trustees_h2': 'Our Trustees',
        'member_founder': 'Founding Member', 'member_trustee': 'Trustee Member',
        // Team Page
        'team_hero_span': 'The Custodians of Devotion', 'team_hero_h1': 'Our <span class="italic text-gold">Sacred</span> Guardians',
        'team_label': 'The Guardians', 'team_title': 'Sacred <span class="italic text-gold">Management</span> Council',
        'team_quote': '"Dedication and discipline lead the way to divine service."', 'team_verified': 'Verified Member',
        'team_cta_title': 'Join Our <span class="italic text-saffron underline decoration-gold/30 underline-offset-8">Mission of Love</span>',
        'team_cta_desc': 'Become a volunteer and serve the divine souls. Experience the peace that comes from selfless sewa.',
        // Gallery Page
        'gallery_hero_span': 'A Visual Journey of Devotion', 'gallery_hero_h1': 'Our <span class="italic text-gold">Sacred</span> Chronicles',
        'gallery_section_label': 'The Sanctuary Gallery', 'gallery_section_title': 'Glimpses of <span class="italic text-gold">Divine</span> Peace',
        // Events Page
        'events_hero_span': 'Spiritual Gatherings & Events', 'events_hero_h1': 'Our <span class="italic text-gold">Sacred</span> Calendar',
        'events_section_label': 'Upcoming Events', 'events_section_title': 'Join Our <span class="italic text-gold">Spiritual</span> Shivir',
        // Contact Page
        'contact_hero_span': 'Connect With Us', 'contact_hero_h1': 'Reach Out for <br><span class="italic text-gold block font-light opacity-90">Sewa & Darshan</span>',
        'contact_label': 'Coordinates', 'contact_title': 'Where Every Life is <span class="text-gold italic">Honored</span>',
        'contact_info_label': 'Sanctuary Location', 'contact_phone_label': 'Voice Channel', 'contact_email_label': 'Digital Mail',
        'contact_form_title': 'Send a <span class="italic text-gold">Sacred Message</span>', 'contact_form_name': 'Name or Identity', 'contact_form_email': 'Email Address',
        'contact_form_subject': 'Subject', 'contact_form_message': 'Shared Wisdom (Message)', 'contact_form_submit': 'Transmit Message',
        'placeholder_name': 'Enter your full name', 'placeholder_email': 'Email Address', 'placeholder_subject': 'Subject', 'placeholder_message': 'How can we help?',
        // Footer
        'footer_about_title': 'Gaushala Sanctuary', 'footer_about_desc': 'Transforming lives through Vedic wisdom and compassionate cow protection. Join us in our mission to serve the sacred beings.',
        'footer_quick_links': 'Quick Links', 'footer_contact_us': 'Get In Touch',
        'footer_address': 'Shri Gau Rakshak Seva Samiti,<br>Survey No. 129, Behind Rajvi Resort, <br>Galpadar, Gandhidham - Kutch.',
        'footer_open_daily': 'Open Daily: 6:00 AM – 8:00 PM', 'footer_privacy': 'Privacy Policy', 'footer_terms': 'Terms of Use',
        'footer_rights': 'All Rights Reserved.',
        'faq_help_center': 'Help Center',
        'faq_title': 'Frequently <span class="italic text-gold">Asked</span> Questions',
        'donate_now': 'Donate Now',
        'faq1_q': 'How are donations used at Gaushala?',
        'faq1_a': '100% of your donation goes directly towards the care of our cows — including feed, medicines, veterinary surgeries, shelter construction, and staff salaries.',
        'faq2_q': 'Can I visit the Gaushala and meet the cows?',
        'faq2_a': 'Absolutely! We welcome visitors and devotees. You can visit our sanctuary, participate in Gau Seva, and even feed the cows yourself.',
        'faq3_q': 'How do I adopt a cow or calf for a month?',
        'faq3_a': 'You can select the "Adopt Cow for 1 Month" option from our Seva menu and complete a simple donation form.',
        'faq4_q': 'Is Gaushala registered and tax-exempt?',
        'faq4_a': 'Yes. Our organization is a registered trust and all donations are eligible for tax deduction under Section 80G.',
        'faq5_q': 'What happens to the stray cattle you rescue?',
        'faq5_a': 'Each rescued cow undergoes a full health assessment. They receive emergency treatment and are assigned a permanent space in our sanctuary.'
    },
    'hi': {
        'nav_home': 'मुख्य पृष्ठ', 'nav_about': 'हमारे बारे में', 'nav_contact': 'संपर्क', 'nav_donate': 'दान दें',
        'nav_founders': 'संस्थापक', 'nav_team': 'हमारी टीम', 'nav_gallery': 'गैलरी', 'nav_events': 'कार्यक्रम',
        'nav_bulletin': 'बुलेटिन', 'nav_announcements': 'घोषणाएं', 'nav_our_story': 'हमारी कहानी',
        'nav_donors': 'दान की दीवार', 'nav_updates': 'अपडेट',
        'brand_name': 'श्री गौ रक्षक सेवा समिति', 'brand_tagline': 'पांजरापोल - गलपादर',
        'header_phone': '+91 99985 81811',
        'hero_span': 'पवित्र गौ सेवा धाम',
        'hero_h1': 'एक <span class="italic text-gold">मां</span> जो बोलती नहीं, <br> फिर भी <span class="text-gold underline decoration-white/20 underline-offset-8">दुनिया को पालती है</span> — <br> <span class="text-white opacity-90 italic">आइये प्यार से उनकी रक्षा करें</span>',
        'shlok_trans_hi': 'अर्थ: गायों में सब कुछ बसा है, तीनों लोक बसते हैं, गायों में ही जीवन है और सब कुछ उन्हीं में समाया है।',
        'explore_mission': 'हमारा लक्ष्य देखें', 'vr_tour': '360° घर बैठे देखें',
        'connection_label': 'अनोखा रिश्ता', 'connection_title': 'लोग हमसे क्यों <span class="italic text-gold">जुड़ते</span> हैं',
        'mission_label': 'पवित्र कार्य', 'mission_title1': 'दिलों का जुड़ना,', 'mission_title2': 'जीवन की रक्षा',
        'mission_quote': '"उन्होंने हमें पालने के लिए अपनी पूरी शक्ति दी। अब हमारी बारी है उनकी रक्षा करने की। हम हर गाय को प्यार और सम्मान वाला घर देना चाहते हैं।"',
        'stat_serving': 'प्यार और सेवा के साथ!', 'stat_treatment': 'इलाज जारी: जीवन के लिए संघर्ष!', 'stat_healed': 'स्वस्थ और नया जीवन मिला!',
        'join_the_cause': 'सेवा से जुड़ें',
        'trust_label': 'आपका भरोसा', 'trust_title': 'आपका दान कहाँ खर्च होता है?', 'trust_quote': '"आपका हर एक रुपया हमारी गौ माताओं के लिए जीवन का वादा है।"',
        'raised_label': 'जमा हुआ', 'raised_out_of': 'कुल लक्ष्य में से', 'goal_label': 'लक्ष्य',
        'table_material': 'सामान', 'table_quantity': 'मात्रा', 'table_price': 'कीमत', 'table_total': 'कुल राशि',
        'reality_label': 'जरूरी रिपोर्ट', 'reality_title': 'गौ माता की स्थिति', 'reality_subtitle': '(बदलाव 2000 — 2026)',
        'recent_condition_label': 'अभी की हालत', 'status_critical': 'हालत: गंभीर', 'case_title': 'बचाई गई गौ माता #450',
        'case_desc': 'इस माता को सड़क हादसे से बचाया गया था। हमारी टीम 24/7 उनकी सेवा में लगी है।', 'support_life': 'उनके जीवन में मदद करें',
        'seva_label': 'सेवा के अवसर', 'seva_title': 'गौशाला सेवा', 'seva_quote': 'सेवा का अपना रास्ता चुनें और उनके जीवन में रौशनी बनें।',
        'seva_roti_title': 'गौ ग्रास (पहली रोटी)', 'seva_roti_desc': 'माँ को खिलाकर आशीर्वाद के साथ अपने दिन की शुरुआत करें।',
        'seva_adopt_title': 'जीवन भर की जिम्मेदारी', 'seva_adopt_desc': 'एक गौ माता के रहने, खाने और दवा का पूरा खर्च उठाएं।',
        'seva_nandi_title': 'नंदी सेवा', 'seva_nandi_desc': 'शक्तिशाली नंदी देव की सेवा और सम्मान करें।',
        'seva_treatment_title': 'बीमारों का इलाज', 'seva_treatment_desc': 'जरूरी ऑपरेशन और जान बचाने वाले इलाज में मदद करें।',
        'seva_shed_title': 'घर बनाना (शेड)', 'seva_shed_desc': 'बेसहारा गायों के लिए पक्के और आरामदायक घर बनाने में मदद करें।',
        'seva_feed20_title': 'भंडारा सेवा', 'seva_feed20_desc': 'एक नेक काम जिससे पक्का हो कि कोई भूखा न रहे।',
        'seva_calf_title': 'बछड़ों का पालन', 'seva_calf_desc': 'छोटे और मासूम बछड़ों की सेवा करके उनका भविष्य संवारें।',
        'seva_med_title': 'दवाई की किट', 'seva_med_desc': 'पक्का करें कि हर बीमार गाय को जरूरी दवा मिल सके।',
        'seva_fodder_title': 'हरा चारा', 'seva_fodder_desc': 'गायों के खाने के लिए ताजा और पौष्टिक हरा घास दें।',
        'gau_datt_sub': 'पवित्र गोद सेवा', 'gau_datt_title': 'गौ दत्तक <span class="italic text-gold italic">सेवा</span>', 'connect_host': 'गौ माता गोद लेने के लिए जुड़ें',
        'donate_offer': 'अपनी श्रद्धा अर्पित करें', 'donate_title': 'गौशाला सेवा केंद्र', 'donate_label': 'गौ माता संरक्षण',
        'donate_bank_label': 'बैंक खाते का विवरण',
        'donate_desc': 'शहर की कठिन सड़कों से हमारे अभयारण्य की अनंत शांति तक—हर सुधार आपकी लिखी एक कहानी है।',
        'donors_hero_label': 'पावन दान की दीवार', 'donors_hero_title': 'हमारे <span class="italic text-gold underline decoration-gold/20 underline-offset-[16px]">महान</span> दानवीर', 'donors_hero_desc': '"गौ सेवा ही माधव सेवा है।" हम उन पुण्यात्माओं का सम्मान करते हैं जिनकी करुणा हमारे अभयारण्य को जीवित रखती है।',
        'donors_search_placeholder': 'नाम या उद्देश्य के आधार पर दानदाताओं को खोजें...', 'instant_filter': 'त्वरित फ़िल्टर',
        'donors_empty_title': 'हमारा परिवार बढ़ रहा है', 'donors_empty_desc': 'हमारी पावन दान की दीवार में शामिल होने वाले पहले व्यक्ति बनें।',
        'table_profile': 'प्रोफ़ाइल', 'table_donor_info': 'दाता का नाम और उद्देश्य', 'table_donation_details': 'दान विवरण', 'table_total_amount': 'कुल राशि',
        'about_hero_span': 'श्रद्धा के साथ स्थापित',
        'about_hero_h1': 'हमारी <span class="italic text-gold">पावन</span> विरासत',
        'about_mission_quote': 'जहाँ हर जीवन पवित्र है, और हर धड़कन <span class="text-saffron font-bold">करुणा</span> की कहानी कहती है। हम दिव्य के संरक्षक हैं।',
        'about_mission_label': '— गौशाला दर्शन',
        'about_years_exp': 'लगातार सेवा के वर्ष',
        'about_journey_label': 'हमारा इतिहास और दिव्य मिशन',
        'about_journey_title': '<span class="italic text-gold">करुणा</span> की विरासत, सेवा का आह्वान',
        'about_history_title': 'पावन उत्पत्ति (The Sacred Genesis)',
        'about_journey_p1': 'गलपादर, गांधीधाम (Reg No. GUJ1517/F/1693KUTCH) के हृदय में गहराई से जुड़ी हमारी यात्रा एक विनम्र प्रार्थना के रूप में शुरू हुई। केवल तीन देशी गिर गायों के बचाव के साथ जो शुरू हुआ, वह अब एक विश्व स्तरीय अभयारण्य बन गया है।',
        'about_mission_title': 'हमारा दिव्य मिशन',
        'about_journey_p2': 'यह सुनिश्चित करना कि कच्छ क्षेत्र में कोई भी पवित्र आत्मा उपेक्षा का शिकार न हो। हम मानवता और गौ माता के बीच के शाश्वत बंधन को बहाल करने का प्रयास करते हैं।',
        'about_pillar_vedic': 'वैदिक वंशावली (Vedic Lineage)', 'about_pillar_vedic_desc': 'स्वदेशी गिर, कांकरेज और थारपारकर नस्लों का संरक्षण।',
        'about_pillar_sustenance': 'भरण-पोषण (Sustenance)', 'about_pillar_sustenance_desc': '15+ एकड़ में जैविक चारा और टिकाऊ पंचगव्य अनुसंधान।',
        'about_care_title': 'सबसे अच्छी सेहत', 'about_care_desc': '24/7 डॉक्टर और शुद्ध प्राकृतिक भोजन।',
        'about_ethical_title': 'खुशहाल जीवन', 'about_ethical_desc': 'हर गौ माता के लिए सम्मान और शांति भरा जीवन।',
        'about_fac_label': 'हम जो कुछ भी करते हैं', 'about_fac_title': 'सेवाएं और <span class="text-gold italic">दैनिक कार्य</span>',
        'about_fac_h1_title': 'हमारा पशु अस्पताल', 'about_fac_h1_desc': 'यहाँ अनुभवी डॉक्टरों की एक टीम है। बीमार या चोटिल गायों के लिए विशेष कमरे हैं, जहाँ उनकी आयुर्वेद और प्यार से देखभाल की जाती है।',
        'about_fac_h2_title': 'देसी नस्लों की सुरक्षा', 'about_fac_h2_desc': 'हम गिर और कांकरेज जैसी शुद्ध भारतीय नस्लों को बचाने पर काम करते हैं। ये गायें अपने सेहतमंद A2 दूध और ताकत के लिए जानी जाती हैं।',
        'about_fac_h3_title': 'हरा घास और खेती', 'about_fac_h3_desc': 'हम 15 एकड़ में प्राकृतिक हरा घास उगाते हैं। गायों को बिना किसी केमिकल के, शुद्ध और पौष्टिक आहार दिया जाता है।',
        'about_fac_h4_title': 'प्राकृतिक खाद बनाना', 'about_fac_h4_desc': 'गोबर और गौ-मूत्र से हम अच्छी क्वालिटी की खाद और दवा बनाते हैं। इससे बिना केमिकल की खेती को बढ़ावा मिलता है।',
        'about_fac_h5_title': 'शांत वातावरण', 'about_fac_h5_desc': 'हमारा दिन शांति और प्रार्थना से शुरू होता है। हम गायों को साफ करते हैं और उन्हें खुले मैदान में बिना किसी तनाव के चरने देते हैं।',
        'about_fac_h6_title': 'नई पीढ़ी को सिखाना', 'about_fac_h6_desc': 'हम स्कूलों और बच्चों को यहाँ बुलाते हैं ताकि वे समझ सकें कि गाय प्रकृति के लिए क्यों ज़रूरी हैं।',
        'about_pillar_title': 'हमारी <span class="text-gold italic">बुनियाद</span>',
        'about_pillar_h1_title': 'प्राकृतिक आयुर्वेद', 'about_pillar_h1_desc': 'गायों की भलाई के लिए जड़ी-बूटियों और शांत माहौल का उपयोग करना।',
        'about_pillar_h2_title': 'प्रकृति की मदद', 'about_pillar_h2_desc': 'बिना केमिकल की खेती को बढ़ावा देना और कचरे को कम करना।',
        'about_pillar_h3_title': 'प्यार से सेवा', 'about_pillar_h3_desc': 'एक ऐसी जगह जहाँ कोई भी आकर गौ सेवा का आनंद ले सकता है।',
        'about_stat_cows': 'कुल गौ माताएं', 'about_stat_acres': 'खुले चरागाह', 'about_stat_trees': 'हरे पेड़', 'about_stat_visitors': 'मासिक मेहमान',
        'about_cta_title': 'गौ सेवा में <span class="italic text-saffron underline decoration-gold/30 underline-offset-8">जुड़ें</span>', 'about_cta_desc': 'आपकी मदद यह पक्का करती है कि हर गाय को अच्छी देखभाल मिले। आपका सहयोग बहुत कीमती है।',
        'about_cta_btn1': 'दान दें', 'about_cta_btn2': 'यहाँ घूमने आएं',
        'welfare_label': 'हमारा लक्ष्य', 'welfare_title': 'हर जीव की सेवा', 'welfare_quote': '"सच्ची सेवा सिर्फ खाना देना नहीं, बल्कि जीवन को सम्मान देना है।"',
        'welfare_icu_title': 'गौ अस्पताल (ICU)', 'welfare_icu_desc': 'हादसों में घायल गायों के लिए तुरंत ऑपरेशन की सुविधा।',
        'welfare_nutrition_title': 'शुद्ध आहार', 'welfare_nutrition_desc': 'गायों के लिए खास हरा घास और पौष्टिक आहार।',
        'welfare_rehab_title': 'सेवा और प्यार', 'welfare_rehab_desc': 'बेसहारा और चोटिल गायों के रहने और ठीक होने की जगह।',
        'row_shed': 'घर (शेड) बनाने का सामान', 'row_medical': 'दवाई की किट', 'row_fodder': 'हरा चारा का बंडल',
        'row_jaggery': 'गुड़', 'row_calcium': 'पशु कैल्शियम (10L)', 'row_dry_fodder': 'सूखा घास का बंडल',
        'row_daliya': 'दलिया', 'row_sugar_cane': 'गन्ने का बंडल',
        'gallery_label': 'पवित्र धाम', 'gallery_title': 'श्रद्धा के पल',
        // Marquee
        'latest_label': 'ताजा खबरें',
        'marquee_msg_1': 'हमारी गौ माताओं के लिए नया मेडिकल आईसीयू पिछले हफ्ते शुरू किया गया — आपके दान के लिए धन्यवाद!',
        'marquee_msg_2': '15 अप्रैल को गौ सेवा शिविर — स्वयंसेवक पंजीकरण अब खुला है।',
        'marquee_msg_3': 'सालाना चारा आपूर्ति के लिए दान प्रगति पर है — आज ही अपनी पहली रोटी का योगदान दें।',
        'lang_name': 'हिंदी (Hi)',
        'cta_label': 'गौ सेवा की पुकार', 'cta_title1': 'कभी जहाँ सम्मान मिलता था,', 'cta_title2': 'आज सड़कों पर लावारिस हैं', 'cta_title3': 'आइये उन्हें घर वापस लाएं।', 'cta_btn': 'गौ सेवा करें',
        // Founders Page
        'founders_hero_span': 'गौशाला के मार्गदर्शक', 'founders_hero_h1': 'हमारे <span class="italic text-gold">संस्थापक</span> सदस्य',
        'founders_label': 'पवित्र वंश', 'founders_title': 'गौशाला नेतृत्व और उनके विचार',
        'founders_quote_main': '"एक गौशाला ईंटों से नहीं, बल्कि उन लोगों की मौन प्रार्थनाओं और अडिग करुणा से बनती है जो गाय की आँखों में दिव्यता देखते हैं। मातृत्व का यह पावन पथ अनंत है।"',
        'founders_p1': 'कच्छ के हृदय में स्थित, श्री गौ रक्षक सेवा समिति की शुरुआत हमारे दूरदर्शी संस्थापकों द्वारा एक अद्वितीय, दिव्य बोध के साथ हुई थी: कि हमारी सभ्यता का स्वास्थ्य उसी देखभाल में प्रतिबिंबित होता है जो हम गौ माता को देते हैं।',
        'heal_title': 'गौ चिकित्सा सेवा पहल',
        'heal_desc': 'गौशाला नेतृत्वों की दृष्टि का मुख्य आधार एक विशेष चिकित्सा विंग की स्थापना थी। आज, हमारा 24/7 ICU जीवन के प्रति उनकी प्रतिबद्धता के मूक प्रमाण के रूप में कार्य करता है, जो हर साल हजारों घायल गौ माताओं का उपचार करता है।',
        'legacy_cont': 'करुणा की विरासत जारी है...',
        'founders_cta_title': 'संकल्प को <span class="italic text-saffron underline decoration-gold/30 underline-offset-8">जीवित रखें</span>',
        'founders_cta_desc': 'गौशाला की सेवा का हिस्सा बनें और गौशाला नेतृत्वों के संकल्प को आगे बढ़ाएं।',
        'founders_pillar_label': 'विरासत स्तंभ', 'founders_foundational_spirit': 'संस्थापक भावना', 'founders_visionary': 'मुख्य दूरदर्शी',
        'founders_h2': 'गौशाला नेतृत्व', 'trustees_h2': 'हमारे ट्रस्टी',
        'member_founder': 'संस्थापक सदस्य', 'member_trustee': 'ट्रस्टी सदस्य',
        // Team Page
        'team_hero_span': 'सेवा और समर्पण के रक्षक', 'team_hero_h1': 'हमारे <span class="italic text-gold">सेवादार</span> रक्षक',
        'team_label': 'सेवादार मंडल', 'team_title': 'पवित्र <span class="italic text-gold">संचालन</span> समिति',
        'team_quote': '"समर्पण और अनुशासन ही सच्ची सेवा का मार्ग है।"', 'team_verified': 'सत्यापित सदस्य',
        'team_cta_title': 'प्यार और <span class="italic text-saffron underline decoration-gold/30 underline-offset-8">सेवा से जुड़ें</span>',
        'team_cta_desc': 'स्वयंसेवक बनें और पुण्य का कार्य करें। निस्वार्थ सेवा से मिलने वाली शांति का अनुभव करें।',
        // Gallery Page
        'gallery_hero_span': 'श्रद्धा की झलक', 'gallery_hero_h1': 'हमारे <span class="italic text-gold">पवित्र</span> दृश्य',
        'gallery_section_label': 'गौशाला गैलरी', 'gallery_section_title': 'पवित्र <span class="italic text-gold">शांति</span> ',
        'faq1_q': 'गौशाला में दान का उपयोग कैसे किया जाता है?', 'faq1_a': 'आपका 100% दान सीधे हमारी गायों की देखभाल में जाता है - जिसमें चारा, दवाएं, पशु चिकित्सा सर्जरी, आश्रय निर्माण और कर्मचारियों का वेतन शामिल है।',
        'faq2_q': 'क्या मैं गौशाला जा सकता हूँ और गायों से मिल सकता हूँ?',
        'faq2_a': 'बिल्कुल! हम आगंतुकों और भक्तों का स्वागत करते हैं। आप हमारे अभयारण्य में आ सकते हैं, गौ सेवा में भाग ले सकते हैं, और यहाँ तक कि गायों को खुद खिला भी सकते हैं।',
        'faq3_q': 'मैं एक महीने के लिए गाय या बछड़ा कैसे गोद ले सकता हूँ?',
        'faq3_a': 'आप हमारे सेवा मेनू से "1 महीने के लिए गाय गोद लें" विकल्प चुन सकते हैं और एक सरल दान फॉर्म पूरा कर सकते हैं।',
        'faq4_q': 'क्या गौशाला पंजीकृत और कर-मुक्त है?',
        'faq4_a': 'हाँ। हमारा संगठन एक पंजीकृत ट्रस्ट है और सभी दान धारा 80G के तहत आयकर छूट के लिए पात्र हैं।',
        'faq5_q': 'आपके द्वारा बचाए गए आवारा पशुओं का क्या होता है?',
        'faq5_a': 'प्रत्येक बचाई गई गाय का पूर्ण स्वास्थ्य मूल्यांकन किया जाता है। उन्हें आपातकालीन उपचार मिलता है और हमारे अभयारण्य में एक स्थायी स्थान दिया जाता है।'
    },
    'gu': {
        'nav_home': 'હોમ', 'nav_about': 'અમારા વિશે', 'nav_contact': 'સંપર્ક કરો', 'nav_donate': 'દાન આપો',
        'nav_founders': 'સ્થાપકો', 'nav_team': 'અમારી ટીમ', 'nav_gallery': 'ગેલેરી', 'nav_events': 'કાર્યક્રમો',
        'nav_bulletin': 'બુલેટિન', 'nav_announcements': 'જાહેરાતો', 'nav_our_story': 'અમારી વાર્તા',
        'nav_donors': 'દાનની દીવાલ', 'nav_updates': 'અપડેટ',
        'brand_name': 'શ્રી ગૌ રક્ષક સેવા સમિતિ', 'brand_tagline': 'પાંજરાપોળ - ગળપાદર',
        'header_phone': '+91 99985 81811',
        'hero_span': 'દિવ્ય ગૌ સેવા ધામ',
        'hero_h1': 'એક <span class="italic text-gold">માતા</span> જે એક શબ્દ નથી બોલતી, <br> છતાં <span class="text-gold underline decoration-white/20 underline-offset-8">પૂરા વિશ્વને પોષે છે</span> — <br> <span class="text-white opacity-90 italic">ચાલો આપણો પ્રેમ તેનું રક્ષણ કરે</span>',
        'shlok_trans_gu': 'અર્થ: ગાયોમાં જ બધું સ્થિત છે, ગાયોમાં જ ત્રણેય લોક સ્થિત છે, ગાયોમાં જ જીવ જીવે છે, ગાયોમાં જ બધું સમાયેલું છે.',
        'explore_mission': 'અમારું લક્ષ્ય જુઓ', 'vr_tour': '360° ઘરે બેઠા જુઓ',
        'connection_label': 'અનોખું બંધન', 'connection_title': 'લોકો અમારી સાથે કેમ <span class="italic text-gold">જોડાય</span> છે',
        'mission_label': 'પવિત્ર કાર્ય', 'mission_title1': 'હૃદયનું સ્વસ્થ થવું,', 'mission_title2': 'જીવનનું રક્ષણ',
        'mission_quote': '"તેમણે અમારું પોષણ કરવા માટે પોતાનું જીવન વહેંચ્યું. હવે, તેમનું રક્ષણ કરવાનો અમારો વારો છે. અમે દરેક ગાયને પ્રેમ અને આદર મળે એવું ઘર આપવા માંગીએ છીએ."',
        'stat_serving': 'પ્રેમ અને સંભાળ સાથે સેવા!', 'stat_treatment': 'સારવાર હેઠળ: જીવન માટે સંઘર્ષ!', 'stat_healed': 'સ્વસ્થ અને નવું જીવન મળ્યું!',
        'join_cause': 'સેવા સાથે જોડાઓ',
        'trust_label': 'તમારો વિશ્વાસ', 'trust_title': 'તમારું દાન ક્યાં જાય છે?', 'trust_quote': '"તમારું દરેક યોગદાન અમારી ગાયો માટે જીવનનું વચન છે."',
        'raised_label': 'એકત્રિત', 'raised_out_of': 'કુલ લક્ષ્યમાંથી', 'goal_label': 'લક્ષ્ય',
        'table_material': 'સામગ્રી', 'table_quantity': 'જથ્થો', 'table_price': 'કિંમત/એકમ', 'table_total': 'કુલ રકમ',
        'reality_label': 'મહત્વપૂર્ણ અહેવાલ', 'reality_title': 'ગૌ માતાની સ્થિતિ', 'reality_subtitle': '(બદલાવ 2000 — 2026)',
        'recent_condition_label': 'તાજેતરની સ્થિતિ', 'status_critical': 'સ્થિતિ: ગંભીર', 'case_title': 'બચાવવામાં આવેલ ગૌ માતા #450',
        'case_desc': 'આ માતાને હાઇવે અકસ્માતમાંથી બચાવવામાં આવી હતી. અમારી ટીમ 24/7 સેવામાં છે.', 'support_life': 'તેના જીવનને ટેકો આપો',
        'seva_label': 'સેવાની તકો', 'seva_title': 'ગૌશાળા સેવા', 'seva_quote': 'તમારા સેવાનો રસ્તો પસંદ કરો અને તેમના જીવનમાં પ્રકાશ બનો.',
        'seva_roti_title': 'ગૌ ગ્રાસ (પહેલી રોટલી)', 'seva_roti_desc': 'માતાને જમાડીને આશીર્વાદ સાથે તમારા દિવસની શરૂઆત કરો.',
        'seva_adopt_title': 'જીવન દત્તક લેવું', 'seva_adopt_desc': 'એક ગૌ માતા માટે કુલ સંભાળ, ખોરાક અને દવા પૂરી પાડો.',
        'seva_nandi_title': 'નંદી સેવા', 'seva_nandi_desc': 'પવિત્ર નંદી દેવની શક્તિ અને દિવ્યતાનું સન્માન કરો.',
        'seva_treatment_title': 'તબીબી સેવા', 'seva_treatment_desc': 'ગંભીર સર્જરી અને જીવન બચાવવાના કામમાં મદદ કરો.',
        'seva_shed_title': 'શેડ નિર્માણ', 'seva_shed_desc': 'વધારે ગાયો માટે સ્થિર અને આરામદાયક ઘરો બનાવવામાં અમને મદદ કરો.',
        'seva_feed20_title': 'ભંડારા સેવા', 'seva_feed20_desc': 'એક મોટું સેવા કાર્ય જે સુનિશ્ચિત કરે છે કે કોઈ ભૂખ્યું ન રહે.',
        'seva_calf_title': 'વાછરડાનું જતન', 'seva_calf_desc': 'નિર્દોષ અને નાના વાછરડાને ટેકો આપીને તેમનું ભવિષ્ય સુધારો.',
        'seva_med_title': 'દવા કિટ', 'seva_med_desc': 'ખાતરી કરો કે દરેક બીમાર ગાયને જરૂરી દવા મળે.',
        'seva_fodder_title': 'લીલો ચારો', 'seva_fodder_desc': 'દૈનિક પોષણ માટે તાજું અને પૌષ્ટિક લીલું ઘાસ પૂરું પાડો.',
        'gau_datt_sub': 'દિવ્ય દત્તક સેવા', 'gau_datt_title': 'ગૌ દત્તક <span class="italic text-gold italic">સેવા</span>', 'connect_host': 'ગાય દત્તક લેવા માટે સંપર્ક કરો',
        'donate_offer': 'તમારી ભક્તિ અર્પણ કરો', 'donate_title': 'દાન સેવા કેન્દ્ર', 'donate_label': 'ગૌ માતા સંરક્ષણ',
        'donate_desc': 'શહેરના કઠિન રસ્તાઓથી લઈને અમારા આશ્રમના અનંત શાંતિ સુધી—દરેક સુધારો તમારા દ્વારા લખાયેલી એક વાર્તા છે.',
        'donate_bank_label': 'બેંક ખાતાની વિગતો',
        'donors_hero_label': 'ભવ્ય દાનની દીવાલ', 'donors_hero_title': 'અમારા <span class="italic text-gold underline decoration-gold/20 underline-offset-[16px]">ઉદાર</span> દાતાઓ', 'donors_hero_desc': '"ગૌ સેવા એ જ પ્રભુ સેવા છે." અમે તે આત્માઓનું સન્માન કરીએ છીએ જેમની કરુણા અમારા આશ્રમની રક્ષા કરે છે.',
        'donors_search_placeholder': 'નામ અથવા ઉદ્દેશ્ય દ્વારા દાતાઓની શોધ કરો...', 'instant_filter': 'ફિલ્ટર',
        'donors_empty_title': 'અમારો પરિવાર વધી રહ્યો છે', 'donors_empty_desc': 'અમારી ભવ્ય દાનની દીવાલ પર જોડાનાર પ્રથમ બનો.',
        'table_profile': 'પ્રોફાઇલ', 'table_donor_info': 'દાતાનું નામ અને હેતુ', 'table_donation_details': 'દાનની વિગત', 'table_total_amount': 'કુલ રકમ',
        'verified_offering': 'ચકાસાયેલ અર્પણ', 'gratitude_bg': 'કૃતજ્ઞતા', 'donors_footer_text': 'ગૌ માતાના કાયમી રક્ષણ માટે પવિત્ર યોગદાન',
        'donors_cta_title': 'અમારા <span class="italic text-gold underline decoration-gold/30 underline-offset-8">દિવ્ય મંડળ</span> માં જોડાઓ', 'donors_cta_desc': 'તમારું યોગદાન અમારી પવિત્ર ગૌ માતાની ગરિમા અને સુરક્ષા સુનિશ્ચિત કરે છે. આજે જ દિવ્ય રક્ષક બનો.', 'donors_cta_btn': 'દાતા બનો',
        'donate_subtitle': 'દિવ્ય સેવા કેન્દ્ર', 'form_seva_label': 'સેવા પસંદ કરો', 'form_amount_label': 'રકમ (₹)',
        'form_id_label': 'પવિત્ર ઓળખ', 'form_birthday_label': 'જન્મદિવસ વિધિ', 'form_select_opt': 'ક્ષેત્ર પસંદ કરો',
        'currency_inr': 'ભારતીય રૂપિયો (INR)', 'currency_foreign': 'વિદેશી ચલણ',
        'form_pincode': 'શહેર પિનકોડ', 'form_submit': 'સેવા માટે દાન કરો',
        'placeholder_pincode': 'પિનકોડ', 'placeholder_amount': '₹ રકમ', 'placeholder_phone': 'વોટ્સએપ નંબર', 'placeholder_ritual': 'જન્મદિવસ વિધિ',
        'donate_verify_label': 'ચકાસણી પુરાવો', 'donate_upi_label': 'સીધો UPI માર્ગ', 'donate_trust_label': 'સેવા ટ્રસ્ટ', 'donate_tax_label': 'ટેક્સ મુક્તિ U/S 80G - આવકવેરા અધિનિયમ 1961',
        'seva_fallback': 'દિવ્ય સેવાના અવસરો તૈયાર થઈ રહ્યા છે...',
        'view_all_seva': 'તમામ સેવાઓ જુઓ',
        'testimonials_label': 'લોકોના મંતવ્યો', 'testimonials_title': 'ભક્તો શું કહે છે',
        'testi1_n': 'રાજેશ શર્મા', 'testi1_r': 'માસિક દાતા', 'testi1_t': 'ગૌશાળાની મુલાકાતે જીવન પ્રત્યે મારો દૃષ્ટિકોણ બદલી નાખ્યો. બીમાર ગાયોની સંભાળ અહીં ખૂબ સરસ છે.', 'testi1_initial': 'ર',
        'testi2_n': 'પ્રિયા પટેલ', 'testi2_r': 'સ્વયંસેવક', 'testi2_t': 'અહીંની પારદર્શિતા અજોડ છે. મને ખબર છે કે મારું નાનું દાન પણ યોગ્ય રીતે વપરાય છે.', 'testi2_initial': 'પ્ર',
        'testi3_n': 'અમિત વર્મા', 'testi3_r': 'મુલાકાતી', 'testi3_t': 'એક સાચો આધ્યાત્મિક અનુભવ. વેબસાઇટ પર 360° ટૂર સુંદર છે, પણ રૂબરૂ સેવા કરવાનો આનંદ જુદો જ છે.', 'testi3_initial': 'અ',
        'testi4_n': 'મીરા બાઈ', 'testi4_r': 'માસિક દાતા', 'testi4_t': 'ગૌશાળાની હોસ્પિટલ અને આઇસીયુ સેવાઓ ખૂબ જ સારી છે. ગાયો પ્રત્યેનો આવો પ્રેમ જોઈને આનંદ થાય છે.', 'testi4_initial': 'મ',
        'welfare_label': 'મુખ્ય લક્ષ્ય', 'welfare_title': 'સમસ્ત જીવોની સેવા', 'welfare_quote': '"સાચી સેવા માત્ર ખોરાક આપવો નથી, પણ જીવનને માન-સન્માન આપવું છે."',
        'welfare_icu_title': 'પશુ હોસ્પિટલ (ICU)', 'welfare_icu_desc': 'અકસ્માત પીડિતો માટે તાત્કાલિક ઓપરેશનની સુવિધા.',
        'welfare_nutrition_title': 'શુદ્ધ પોષણ', 'welfare_nutrition_desc': 'ગાયો માટે શુદ્ધ લીલો ચારો અને સંતુલિત ખોરાક.',
        'welfare_rehab_title': 'સેવા અને રક્ષણ', 'welfare_rehab_desc': 'બેઘર અને બીમાર ગાયો માટે રહેવાની અને સાજા થવાની જગ્યા.',
        'row_shed': 'શેડ નિર્માણ સામગ્રી', 'row_medical': 'મેડિકલ કિટ', 'row_fodder': 'લીલા ઘાસનું બંડલ',
        'row_jaggery': 'ગોળ', 'row_calcium': 'પશુ કેલ્શિયમ (10L)', 'row_dry_fodder': 'સૂકા ઘાસનું બંડલ',
        'row_daliya': 'દલિયા', 'row_sugar_cane': 'શેરડીનું બંડલ',
        'gallery_label': 'પવિત્ર ધામ', 'gallery_title': 'શ્રદ્ધાની ક્ષણો',
        // Marquee
        'latest_label': 'નવી ખબર',
        'marquee_msg_1': 'આપણી ગૌ માતાઓ માટે નવું મેડિકલ ICU ગયા અઠવાડિયે શરૂ કરવામાં આવ્યું — તમારા દાન બદલ આભાર!',
        'marquee_msg_2': '15 એપ્રિલે ગૌ સેવા શિબિર — સ્વયંસેવક નોંધણી હવે ચાલુ છે.',
        'marquee_msg_3': 'વાર્ષિક ઘાસચારા માટે દાન પ્રગતિમાં છે — આજે જ તમારી પહેલી રોટીનું યોગદાન આપો.',
        // About Page (Gujarati)
        'about_hero_span': 'ગૌ સેવા અને પ્રેમ થી નિર્મિત',
        'about_hero_h1': 'અમારો <span class="italic text-gold">ઈતિહાસ અને સેવા</span>',
        'about_mission_quote': 'જ્યાં દરેક જીવન માટે પવિત્ર ભાવ છે, and દરેક ધબકાર <span class="text-saffron">પ્રેમ અને દયા</span> ની વાત કહે છે. અમે ગૌ રક્ષકો છીએ.',
        'about_mission_label': '— અમારી વિચારધારા',
        'about_years_exp': 'સતત સેવાના વર્ષો',
        'about_journey_label': 'અમારી સફર',
        'about_journey_title': '<span class="italic text-gold">જૂના મૂલ્યો</span> પર આધારિત, ભવિષ્ય માટે તૈયાર',
        'about_journey_p1': 'ગલપાદર, ગાંધીધામ માં આવેલી અમારી ગૌશાળા (શ્રી ગૌ રક્ષક સેવા સમિતિ) એક નાના આશ્રય થી શરૂ થઈ હતી. ત્રણ બચાવેલી ગાયો થી શરૂ થયેલી આ સફર આજે એક મોટું સુરક્ષિત ઘર બની ગયું છે.',
        'about_journey_p2': 'અમારો હેતુ માત્ર આશરો આપવો જ નથી; અમે કચ્છમાં એવું વાતાવરણ બનાવવા માંગીએ છીએ જ્યાં માણસ અને ગાય હળીમળીને રહે અને કોઈ ગાય રસ્તે રઝળતી કે દુઃખી ના હોય.',
        'about_care_title': 'સારામાં સારી સારવાર', 'about_care_desc': '24/7 ડોક્ટર અને શુદ્ધ પ્રાકૃતિક ખોરાક.',
        'about_ethical_title': 'સુખદ જીવન', 'about_ethical_desc': 'દરેક ગૌ માતા માટે માન-સન્માન અને શાંતિ થી જીવવાની જગ્યા.',
        'about_fac_label': 'અમે જે કરીએ છીએ', 'about_fac_title': 'સેવા અને <span class="text-gold italic">દૈનિક કામ</span>',
        'about_fac_h1_title': 'અમારું ગૌ હોસ્પિટલ', 'about_fac_h1_desc': 'અહીં અનુભવી ડોકટરોની ટીમ છે. બીમાર ગાયો માટે ખાસ હોસ્પિટલ છે જ્યાં તેમને આયુર્વેદિક અને આધુનિક રીતે સારવાર મળે છે.',
        'about_fac_h2_title': 'દેશી ગાયની જાળવણી', 'about_fac_h2_desc': 'અમે ગીર અને કાંકરેજ જેવી શુદ્ધ ભારતીય ઓલાદને બચાવવાનું કામ કરીએ છીએ. આ ગાયો એમના દૂધ અને તાકાત માટે જાણીતી છે.',
        'about_fac_h3_title': 'લીલું ઘાસ અને ઓર્ગેનિક ખેતી', 'about_fac_h3_desc': 'અમે 15 એકરમાં કુદરતી ઘાસ ઉગાડીએ છીએ. ગાયોને કોઈ પણ કેમિકલ વગરનો શુદ્ધ ખોરાક આપવામાં આવે છે.',
        'about_fac_h4_title': 'કુદરતી ખાતર બનાવવું', 'about_fac_h4_desc': 'ગોબર અને ગૌ-મૂત્ર માંથી અમે ઉચ્ચ ક્વોલિટીનું ખાતર બનાવીએ છીએ. આનાથી કેમિકલ વગરની ખેતી ને પ્રોત્સાહન મળે છે.',
        'about_fac_h5_title': 'શાંત વાતાવરણ', 'about_fac_h5_desc': 'અમારો દિવસ પ્રાર્થના થી શરૂ થાય છે. અમે ગાયોને સાફ કરીએ છીએ ને તેમને ખુલ્લા મેદાનમાં શાંતિ થી ચરવા દઈએ છીએ.',
        'about_fac_h6_title': 'નવી પેઢીને શિક્ષણ', 'about_fac_h6_desc': 'અમે શાળાઓ ને અહીં બોલાવીએ છીએ જેથી બાળકો સમજી શકે કે પ્રકૃતિ માટે ગાય કેમ જરૂરી છે.',
        'about_pillar_title': 'અમારી <span class="text-gold italic">મૂળભૂત બાબતો</span>',
        'about_pillar_h1_title': 'કુદરતી આયુર્વેદ', 'about_pillar_h1_desc': 'પશુઓના કલ્યાણ માટે દેશી જડીબુટ્ટી અને શાંત વાતાવરણનો ઉપયોગ કરવો.',
        'about_pillar_h2_title': 'કુદરતની રક્ષા', 'about_pillar_h2_desc': 'ઓર્ગેનિક ખેતીને પ્રોત્સાહન અને કચરાનો યોગ્ય નિકાલ.',
        'about_pillar_h3_title': 'પ્રેમ થી સેવા', 'about_pillar_h3_desc': 'એક એવી જગ્યા જ્યાં કોઈ પણ આવીને ગૌ સેવા નો આનંદ લઇ શકે.',
        'about_stat_cows': 'કુલ ગૌ માતાઓ', 'about_stat_acres': 'ખુલ્લા ગૌચર', 'about_stat_trees': 'લીલા વૃક્ષો', 'about_stat_visitors': 'માસિક મહેમાનો',
        'about_cta_title': 'ગૌ સેવામાં <span class="italic text-saffron underline decoration-gold/30 underline-offset-8">જોડાઓ</span>', 'about_cta_desc': 'તમારી મદદથી ગૌ માતાને શ્રેષ્ઠ સારવાર મળી રહે છે. તમારી નાની સેવા પણ ખૂબ કિંમતી છે.',
        'about_cta_btn1': 'દાન કરો', 'about_cta_btn2': 'મુલાકાત લો',
        'lang_name': 'ગુજરાતી (Gu)',
        'cta_label': 'ગૌ સેવાની પુકાર', 'cta_title1': 'કોઈ સમયે જ્યાં સન્માન મળતું,', 'cta_title2': 'આજે રસ્તાઓ પર નિરાધાર છે', 'cta_title3': 'ચાલો તેમને ઘરે પાછી લાવીએ.', 'cta_btn': 'ગૌ સેવા કરો',
        // Founders Page
        'founders_hero_span': 'ગૌશાળાના માર્ગદર્શકો', 'founders_hero_h1': 'અમારા <span class="italic text-gold">સ્થાપક</span> પુરુષો',
        'founders_label': 'પવિત્ર વંશ', 'founders_title': 'અમારા સ્થાપક અને તેમનો દ્રષ્ટિકોણ',
        'founders_quote_main': '"એક ગૌશાળા માત્ર ઈંટોની દીવાલ નથી, પણ તે લોકોની મૌન પ્રાર્થના અને અડગ કરૂણાથી બનેલી છે જે ગાયની આંખોમાં દિવ્યતા જુએ છે. માતૃત્વનો આ પાવન માર્ગ અનંત છે."',
        'founders_p1': 'કચ્છના હૃદયમાં સ્થિત, શ્રી ગૌ રક્ષક સેવા સમિતિની શરૂઆત અમારા દુરદર્શી સ્થાપકો દ્વારા એક અનોખી, દિવ્ય પ્રાપ્તિ સાથે થઈ હતી: કે આપણી સભ્યતાનું સ્વાસ્થ્ય તે સંભાળમાં દેખાય છે જે આપણે ગૌ માતાને આપીએ છીએ.',
        'heal_title': 'ગૌ ચિકિત્સા સેવા પહેલ',
        'heal_desc': 'ગૌશાળા નેતૃત્વના દ્રષ્ટિકોણનો મુખ્ય પાયો એક વિશેષ તબીબી વિંગની સ્થાપના હતી. આજે, આપણું 24/7 ICU જીવન પ્રત્યેની તેમની પ્રતિબદ્ધતાના મૌન પુરાવા તરીકે કાર્ય કરે છે, જે દર વર્ષે હજારો ઘાયલ ગૌ માતાઓની સારવાર કરે છે.',
        'legacy_cont': 'કરુણાનો વારસો ચાલુ રહ્યો...',
        'founders_cta_title': 'સંકલ્પને <span class="italic text-saffron underline decoration-gold/30 underline-offset-8">જીવંત રાખો</span>',
        'founders_cta_desc': 'ગૌશાળાની સેવાનો ભાગ બનો અને ગૌશાળા નેતૃત્વના સંકલ્પને આગળ ધપાવો.',
        'founders_pillar_label': 'વારસો સ્તંભ', 'founders_foundational_spirit': 'સ્થાપક ભાવના', 'founders_visionary': 'મુખ્ય દુરદર્શી',
        'founders_h2': 'ગૌશાળા નેતૃત્વ', 'trustees_h2': 'અમારા ટ્રસ્ટીઓ',
        'member_founder': 'સ્થાપક સભ્ય', 'member_trustee': 'ટ્રસ્ટી સભ્ય',
        // Team Page
        'team_hero_span': 'સેવા અને સમર્પણના રક્ષકો', 'team_hero_h1': 'અમારા <span class="italic text-gold">સેવાભાવી</span> રક્ષકો',
        'team_label': 'સેવાભાવી મંડળ', 'team_title': 'પવિત્ર <span class="italic text-gold">સંચાલન</span> સમિતિ',
        'team_quote': '"સમર્પણ અને શિસ્ત એ જ સાચી સેવાનો માર્ગ છે."', 'team_verified': 'ચકાસાયેલ સભ્ય',
        'team_cta_title': 'પ્રેમ અને <span class="italic text-saffron underline decoration-gold/30 underline-offset-8">સેવા સાથે જોડાઓ</span>',
        'team_cta_desc': 'સ્વયંસેવક બનો અને પુણ્યનું કાર્ય કરો. નિઃસ્વાર્થ સેવાથી મળતી શાંતિનો અનુભવ કરો.',
        // Gallery Page
        'gallery_hero_span': 'શ્રદ્ધાની ઝલક', 'gallery_hero_h1': 'અમારા <span class="italic text-gold">પવિત્ર</span> દ્રશ્યો',
        'gallery_section_label': 'ગૌશાળા ગેલેરી', 'gallery_section_title': 'પવિત્ર <span class="italic text-gold">શાંતિ</span> ની ઝલક',
        // Events Page
        'events_hero_span': 'આધ્યાત્મિક કાર્યક્રમો', 'events_hero_h1': 'અમારું <span class="italic text-gold">સેવા</span> કેલેન્ડર',
        'events_section_label': 'આવનારા કાર્યક્રમો', 'events_section_title': 'આધ્યાત્મિક <span class="italic text-gold">શિબિરમાં</span> જોડાઓ',
        // Contact Page
        'contact_hero_span': 'અમારી સાથે જોડાઓ', 'contact_hero_h1': 'સેવા અને દર્શન માટે <br><span class="italic text-gold block font-light opacity-90">સંપર્ક કરો</span>',
        'contact_label': 'નિર્દેશાંક', 'contact_title': 'જ્યાં દરેક જીવનનું <span class="text-gold italic">સન્માન</span> છે',
        'contact_info_label': 'ગૌશાળાનું સરનામું', 'contact_phone_label': 'વોઇસ ચેનલ (ફોન)', 'contact_email_label': 'ડિજિટલ મેઇલ (ઈમેલ)',
        'contact_form_title': 'એક <span class="italic text-gold">પવિત્ર સંદેશ</span> મોકલો', 'contact_form_name': 'નામ અથવા ઓળખ', 'contact_form_email': 'ઈમેલ એડ્રેસ',
        'contact_form_subject': 'વિષય', 'contact_form_message': 'સામાજિક જ્ઞાન (સંદેશ)', 'contact_form_submit': 'સંદેશ મોકલો',
        'placeholder_name': 'તમારું પૂરું નામ લખો', 'placeholder_email': 'ઈમેલ એડ્રેસ', 'placeholder_subject': 'વિષય', 'placeholder_message': 'અમે તમારી શું મદદ કરી શકીએ?',
        // Footer
        'footer_about_title': 'ગૌશાળા સેવા ધામ', 'footer_about_desc': 'વૈદિક જ્ઞાન અને કરુણા દ્વારા ગૌ સેવા અને રક્ષણ. આ પવિત્ર અભિયાનનો ભાગ બનો.',
        'footer_quick_links': 'મહત્વપૂર્ણ લિંક્સ', 'footer_contact_us': 'સંપર્ક',
        'footer_address': 'શ્રી ગૌ રક્ષક સેવા સમિતિ,<br>સર્વે નં. ૧૨૯, રાજવી રીસોર્ટ ની પાછળ, <br>ગળપાદર, ગાંધીધામ - કચ્છ.',
        'footer_open_daily': 'દરરોજ ખુલ્લું: સવારે ૬:૦૦ – રાત્રે ૮:૦૦', 'footer_privacy': 'ગોપનીયતા નીતિ', 'footer_terms': 'વપરાશની શરતો',
        'footer_rights': 'તમામ હકો અનામત.',
        'faq_help_center': 'મદદ કેન્દ્ર',
        'faq_title': 'વારંવાર પૂછાતા <span class="italic text-gold">પ્રશ્નો</span>',
        'donate_now': 'દાન આપો',
        'faq1_q': 'ગૌશાળામાં દાનનો ઉપયોગ કેવી રીતે થાય છે?',
        'faq1_a': 'તમારા દાનના 100% સીધા જ અમારી ગાયોની સંભાળમાં વપરાય છે - જેમાં ઘાસચારો, દવાઓ, પશુ ચિકિત્સા સર્જરી, આશ્રય નિર્માણ અને સ્ટાફના પગારનો સમાવેશ થાય છે.',
        'faq2_q': 'શું હું ગૌશાળાની મુલાકાત લઈ શકું અને ગાયોને મળી શકું?',
        'faq2_a': 'ચોક્કસ! અમે મુલાકાતીઓ અને ભક્તોનું સ્વાગત કરીએ છીએ. તમે અમારા આશ્રમની મુલાકાત લઈ શકો છો, ગૌ સેવામાં ભાગ લઈ શકો છો અને ગાયોને જાતે ખવડાવી પણ શકો છો.',
        'faq3_q': 'હું એક મહિના માટે ગાય અથવા વાછરડાને કેવી રીતે દત્તક લઈ શકું?',
        'faq3_a': 'તમે અમારા સેવા મેનૂમાંથી "1 મહિના માટે ગાય દત્તક લો" વિકલ્પ પસંદ કરી શકો છો અને એક સરળ દાન ફોર્મ ભરી શકો છો.',
        'faq4_q': 'શું ગૌશાળા નોંધાયેલ છે અને કરમુક્ત છે?',
        'faq4_a': 'હા. અમારી સંસ્થા એક રજિસ્ટર્ડ ટ્રસ્ટ છે અને તમામ દાન આવકવેરાની કલમ 80G હેઠળ કર મુક્તિ માટે પાત્ર છે.',
        'faq5_q': 'તમે બચાવેલા રઝળતા પશુઓનું શું થાય છે?',
        'faq5_a': 'દરેક બચાવેલ ગાયનું સંપૂર્ણ સ્વાસ્થ્ય પરીક્ષણ કરવામાં આવે છે. તેમને તાત્કાલિક સારવાર આપવામાં આવે છે અને અમારા આશ્રમમાં કાયમી જગ્યા આપવામાં આવે છે.'
    }
};

window.changeLanguage = function (lang) {
    localStorage.setItem('site_lang', lang);
    applyLanguage();
}

function applyLanguage() {
    const lang = localStorage.getItem('site_lang') || 'en';
    const dict = translations[lang];
    if (!dict) return;

    // 1. Dictionary Mapping (data-lang)
    document.querySelectorAll('[data-lang]').forEach(el => {
        const key = el.getAttribute('data-lang');
        if (dict[key]) el.innerHTML = dict[key];
    });

    // 2. Dynamic Fallback (for DB content like announcements)
    if (lang !== 'en' && typeof window.translateAllDynamicContent === 'function') {
        window.translateAllDynamicContent(lang);
    } else if (lang === 'en') {
        // Restore original text for DB content if switched back to EN
        document.querySelectorAll('[data-trans="en"]').forEach(el => {
            if (el.getAttribute('data-origin')) el.innerText = el.getAttribute('data-origin');
        });
    }

    // 3. Update Placeholders
    document.querySelectorAll('[data-lang-placeholder]').forEach(el => {
        const key = el.getAttribute('data-lang-placeholder');
        if (dict[key]) el.setAttribute('placeholder', dict[key]);
    });

    // 4. Toggle Visibility
    const langs = ['en', 'hi', 'gu'];
    langs.forEach(l => {
        document.querySelectorAll(`.lang-${l}`).forEach(el => {
            const isTarget = (l === lang);
            el.classList.toggle('hidden', !isTarget);
            if (isTarget) {
                el.classList.remove('opacity-0');
                el.classList.add('opacity-100');
            } else {
                el.classList.remove('opacity-100');
                el.classList.add('opacity-0');
            }
        });
    });

    // Update Header
    const currentLangLabel = document.getElementById('current-lang');
    if (currentLangLabel) currentLangLabel.textContent = dict['lang_name'] || 'English';

    // TRIGGER SWIPER & AOS UPDATES
    if (window.allSwipers) {
        Object.values(window.allSwipers).forEach(s => {
            if (s && s.el && typeof s.update === 'function') s.update();
        });
    }

    // CRITICAL: Refresh AOS after language layout changes
    if (typeof AOS !== 'undefined') {
        setTimeout(() => {
            AOS.refresh();
        }, 100);
    }
}

// 5. Intelligent Dynamic Translation: Automatic API Integration
// This handles content from the database (Announcements, Events, etc.)
// Using MyMemory API (Free) with LocalStorage Caching to prevent rate limiting.

const TRANS_CACHE_KEY = 'gaushala_dynamic_cache';
let transCacheDict = JSON.parse(localStorage.getItem(TRANS_CACHE_KEY) || '{}');

async function getAutoTranslation(text, targetLang) {
    if (targetLang === 'en') return text;
    const cacheKey = `${targetLang}:${text}`;

    // 1. Check Browser-level Cache (STRIKT Audit)
    if (transCacheDict[cacheKey]) {
        const cachedValue = transCacheDict[cacheKey];
        if (cachedValue.includes('MYMEMORY') || cachedValue.includes('limit reached')) {
            delete transCacheDict[cacheKey];
            localStorage.setItem(TRANS_CACHE_KEY, JSON.stringify(transCacheDict));
        } else {
            return cachedValue;
        }
    }

    // 2. Call Internal Server-side Bridge (MySQL + Google Engine)
    try {
        const response = await fetch(`${window.GA_BASE_URL}/api/translate.php?q=${encodeURIComponent(text)}&lang=${targetLang}`);
        const data = await response.json();

        if (data && data.translatedText) {
            const translatedText = data.translatedText;

            // Sync to Browser Cache
            transCacheDict[cacheKey] = translatedText;
            localStorage.setItem(TRANS_CACHE_KEY, JSON.stringify(transCacheDict));

            return translatedText;
        }
    } catch (e) {
        console.warn("Translation layer busy. Graceful fallback.");
    }
    return text; // Revert to English if all else fails
}

window.translateAllDynamicContent = async function (lang) {
    const dynamicElements = document.querySelectorAll('[data-trans="en"]');

    // Curated Dynamic Dictionary for high-priority UI strings (Prevents API delay for common items)
    const manualRepo = {
        'hi': {
            'Divine update': 'दिव्य समाचार',
            'Featured Moment': 'मुख्य क्षण',
            'Sanctuary Location': 'गौशाला का पता',
            'Archive is Being Restored': 'पुरालेख अपडेट हो रहा है',
            'Acknowledge': 'स्वीकार करें',
            'Visit Bulletin': 'बुलेटिन देखें',
            'Heart of the Sanctuary': 'गौशाला का हृदय',
            'Sacred Whisper': 'पवित्र संदेश',
            'From The Sanctuary': 'गौशाला से',
            'Divine Revelations': 'दिव्य दर्शन',
            'Recent Stories': 'हाल ही की कहानियां',
            'Founding Member': 'संस्थापक सदस्य',
            'Trustee Member': 'ट्रस्टी सदस्य',
            'Gaushala Naitrutav': 'गौशाला नेतृत्व',
            'Our Trustees': 'हमारे ट्रस्टी'
        },
        'gu': {
            'Divine update': 'દિવ્ય સમાચાર',
            'Featured Moment': 'મુખ્ય પળ',
            'Sanctuary Location': 'ગૌશાળાનું સરનામું',
            'Archive is Being Restored': 'પુરાલેખ અપડેટ થઈ રહ્યો છે',
            'Acknowledge': 'સ્વીકારો',
            'Visit Bulletin': 'બુલેટિન જુઓ',
            'Heart of the Sanctuary': 'ગૌશાળાનું હૃદય',
            'Sacred Whisper': 'પવિત્ર સંદેશ',
            'From The Sanctuary': 'ગૌશાળા થી',
            'Divine Revelations': 'દિવ્ય દર્શન',
            'Recent Stories': 'તાજેતરની વાર્તાઓ',
            'Founding Member': 'સ્થાપક સભ્ય',
            'Trustee Member': 'ટ્રસ્ટી સભ્ય',
            'Gaushala Naitrutav': 'ગૌશાળા નેતૃત્વ',
            'Our Trustees': 'અમારા ટ્રસ્ટીઓ'
        }
    };

    // Iterate through all elements marked for dynamic translation
    for (const el of dynamicElements) {
        // Store original English if not present (Strict Audit)
        if (!el.getAttribute('data-origin')) {
            el.setAttribute('data-origin', el.innerHTML.trim());
        }

        const originalText = el.getAttribute('data-origin');

        // 1. Priority: Check manual high-importance mappings
        if (manualRepo[lang] && manualRepo[lang][originalText]) {
            el.innerHTML = manualRepo[lang][originalText];
            continue;
        }

        // 2. Automated: Fetch from Neural API (Private Bridge)
        if (lang !== 'en') {
            el.style.opacity = '0.7';

            // STRIKT THROTTLE: Avoid API Storm
            await new Promise(r => setTimeout(r, 50));

            const result = await getAutoTranslation(originalText, lang);
            el.innerHTML = result;
            el.style.opacity = '1';
        } else {
            // Revert to English
            el.innerHTML = originalText;
        }
    }

    // Final Layout Refresh after all translations
    if (typeof AOS !== 'undefined') AOS.refresh();
}

// Auto-apply language on load is already handled inside the main DOMContentLoaded at the top
