<?php
// Fetch Donors for Multi-Row Marquee
$small_donors = [];
try {
    $small_donors = $pdo->query("SELECT * FROM donors WHERE is_visible = 1 ORDER BY donation_date DESC LIMIT 50")->fetchAll();
} catch (Exception $e) {
    $small_donors = [];
}

// Fetch latest announcement for popup
$latest_pop = null;
try {
    $stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 1");
    if ($stmt) $latest_pop = $stmt->fetch();
} catch (Exception $e) {
    $latest_pop = null;
}

// Fetch Transparency Data
$transparency_mats = [];
$total_item_raised = 0;
$total_item_goal = 0;

try {
    $transparency_mats = $pdo->query("SELECT * FROM transparency_materials ORDER BY sort_order ASC")->fetchAll();
    foreach ($transparency_mats as $m) {
        $total_item_raised += (float)($m['current_val'] ?? 0);
        $total_item_goal += (float)($m['target_val'] ?? 0);
    }

    // Fetch Stats from dedicated table
    $stats = $pdo->query("SELECT * FROM transparency_stats WHERE id = 1")->fetch();

    // Priority: dashboard sync values (even if 0) > sum of ledger items
    $raised_amount = ($stats !== false) ? (float)$stats['raised'] : $total_item_raised;
    $goal_amount = ($stats !== false && $stats['goal'] > 0) ? (float)$stats['goal'] : ($total_item_goal ?: 100);
} catch (Exception $e) {
    $raised_amount = 0;
    $goal_amount = 100;
}

$progress_percent = ($goal_amount > 0) ? ($raised_amount / $goal_amount) * 100 : 0;
// Limit dasharray to 100
$dash_array_val = min(100, $progress_percent);

// Fetch Seva Options for the donation grid (LIMIT 6 for home)
$seva_options = [];
$has_more_seva = false;
try {
    $total_seva_count = $pdo->query("SELECT COUNT(*) FROM seva_options WHERE status = 'active'")->fetchColumn();
    $has_more_seva = $total_seva_count > 6;
    $seva_options = $pdo->query("SELECT * FROM seva_options WHERE status = 'active' ORDER BY sort_order ASC, id ASC LIMIT 6")->fetchAll();
} catch (Exception $e) {
    $seva_options = [];
}
?>

<!-- 🕊️ DIVINE REVELATION POPUP (Emotional Redesign) 🕊️ -->
<?php if ($latest_pop): ?>
    <div id="announcementModal" class="sacred-modal-engine">
        <!-- Soulful Split Modal -->
        <div class="sacred-modal-inner">

            <!-- Left Half: Emotional Anchor (Image) -->
            <div class="sacred-modal-img-wrap group">
                <img src="/asset/img/cow/soulful_cow_sanctuary.png" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[3s] ease-out" alt="Soulful Gau Mata">
                <!-- Warm Saffron Overlay -->
                <div class="absolute inset-0 bg-gradient-to-tr from-saffron/30 via-transparent to-transparent mix-blend-overlay"></div>
                <!-- Vignette for Depth -->
                <div class="absolute inset-0 shadow-[inset_0_0_100px_rgba(0,0,0,0.2)]"></div>

                <!-- Floating Label -->
                <div class="absolute bottom-4 left-6 md:bottom-8 md:left-8 flex items-center gap-2 md:gap-3 bg-white/10 backdrop-blur-md border border-white/20 px-4 py-1.5 md:px-6 md:py-2.5 rounded-full">
                    <span class="w-1.5 h-1.5 bg-gold rounded-full animate-pulse shadow-[0_0_10px_#FFD700]"></span>
                    <span class="text-[12px] md:text-[12px] font-black uppercase tracking-[0.2em] md:tracking-[0.3em] text-white">Heart of the Sanctuary</span>
                </div>
            </div>

            <!-- Right Half: Sacred Whisper (Content) -->
            <div class="sacred-modal-text-wrap">

                <!-- Decorative Corner Patterns -->
                <div class="absolute top-0 right-0 w-24 h-24 md:w-32 md:h-32 opacity-5 pointer-events-none">
                    <img src="/asset/img/pattern/footer-pattern.png" class="w-full h-full object-contain rotate-90">
                </div>

                <div class="relative z-10 w-full">
                    <span class="text-saffron font-black uppercase tracking-[0.4em] text-[8px] md:text-[12px] mb-4 md:mb-6 block" data-trans="en">Divine update</span>

                    <h2 class="text-2xl md:text-5xl font-display font-bold text-nature mb-6 md:mb-8 leading-[1.1]" data-trans="en">
                        A <span class="italic text-gold underline decoration-gold/30 underline-offset-8">Sacred Whisper</span> <br class="hidden md:block">
                        From The Sanctuary
                    </h2>

                    <div class="prose prose-sm text-gray-400 max-w-none mb-8 md:mb-12 relative">
                        <!-- Compassion Quote Mark -->
                        <div class="absolute -left-6 -top-4 md:-left-8 md:-top-4 text-4xl md:text-6xl text-gold/10 font-serif leading-none">“</div>
                        <p class="text-base md:text-xl leading-relaxed italic border-l-2 border-gold/20 pl-6 md:pl-8" data-trans="en">
                            <?= nl2br(htmlspecialchars($latest_pop['message_en'])) ?>
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 md:gap-8">
                        <button onclick="closeAnnouncement()" class="bg-nature text-white px-6 py-3 md:px-10 md:py-4 rounded-full font-bold shadow-xl shadow-nature/20 hover:bg-gold hover:text-nature transition-all duration-700 uppercase tracking-widest text-[12px] md:text-[12px] border border-white/10" data-trans="en">Acknowledge</button>
                        <a href="/announcements" class="text-saffron font-black uppercase tracking-widest text-[12px] md:text-[12px] hover:text-gold transition-colors flex items-center gap-3 group" data-trans="en">
                            Visit Bulletin
                            <i class="fas fa-arrow-right transform group-hover:translate-x-2 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- Bottom Glow -->
                <div class="absolute bottom-0 right-0 w-32 h-32 md:w-48 md:h-48 bg-saffron/5 rounded-full blur-[100px] pointer-events-none"></div>
            </div>

            <!-- Absolute Close Button (X) -->
            <button onclick="closeAnnouncement()" class="sacred-modal-close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <script>
        function closeAnnouncement() {
            const modal = document.getElementById('announcementModal');
            modal.classList.remove('is-active');
            sessionStorage.setItem('pop_shown', 'true');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('announcementModal');
            if (!sessionStorage.getItem('pop_shown')) {
                setTimeout(() => {
                    modal.classList.add('is-active');
                }, 2000);
            }
        });
    </script>
<?php endif; ?>

<!-- Premium Hero Section with Swiper Background -->
<section class="relative min-h-[calc(100vh-135px)] md:min-h-[calc(100vh-145px)] mt-[135px] md:mt-[145px] flex items-center justify-center text-center overflow-hidden">

    <!-- Virtual Reality (VR) Panorama Layer -->
    <div id="panorama" class="absolute inset-0 z-[5] opacity-0 pointer-events-none transition-opacity duration-1000 bg-black"></div>

    <!-- Swiper Background Slider -->
    <div id="hero-slider" class="absolute inset-0 z-0">

        <div class="swiper swiper-hero w-full h-full">
            <div class="swiper-wrapper">
                <div class="swiper-slide bg-black">
                    <img src="/asset/img/cow/gus.jpeg" alt="Gaushala Cow" class="kenburns-bg opacity-70 object-cover object-center">
                </div>
                <div class="swiper-slide bg-black">
                    <img src="/asset/img/cow/gushala5.jpg" alt="Gaushala Sanctuary" class="kenburns-bg opacity-70 object-cover object-center">
                </div>
                <div class="swiper-slide bg-black">
                    <img src="/asset/img/cow/babycow.jpeg" alt="Gaushala Living" class="kenburns-bg opacity-70 object-cover object-center">
                </div>
                <div class="swiper-slide bg-black">
                    <img src="/asset/img/cow/hlo.jpg" alt="Gau Seva" class="kenburns-bg opacity-70 object-cover object-center">
                </div>
            </div>
        </div>
    </div>

    <!-- Overlays -->
    <div id="hero-overlay" class="absolute inset-0 z-[1] bg-saffron/50 mix-blend-multiply transition-opacity duration-1000"></div>
    <div id="hero-gradient" class="absolute inset-0 z-[2] bg-gradient-to-t from-black/80 via-transparent to-black/40 transition-opacity duration-1000"></div>

    <div class="container mx-auto px-6 relative z-[30] hero-content text-white pt-16 pb-12">
        <span class="text-secondary uppercase tracking-[0.5em] text-[12px] md:text-sm font-bold mb-8 block drop-shadow-md" data-lang="hero_span">
            Shree Radhe Radhe
        </span>

        <!-- <h1 class="text-3xl md:text-6xl leading-[1.05] mb-8 font-display drop-shadow-2xl text-white" data-lang="hero_h1">
            A <span class="italic text-gold">Mother</span> Who Speaks No Words, <br>
            Yet <span class="text-gold underline decoration-white/20 underline-offset-8">Feeds the World</span> — <br>
            <span class="text-white opacity-90 italic">Let Our Love Protect Her</span>
        </h1> -->


        <div class="section-divider mx-auto bg-gradient-to-r from-saffron to-gold w-32 md:w-48 !h-1.5 rounded-full mb-10 shadow-lg"></div>

        <div class="shlok-wrapper mb-0 relative z-20">
            <p class="text-xl md:text-4xl font-bold mb-8 drop-shadow-2xl text-secondary/90 font-hindi leading-relaxed tracking-widest">
                "गौषु सर्वं प्रतिष्ठितं गोषु लोकाः प्रतिष्ठिताः। <br>
                गोषु जीवन्ति जन्तवो गोषु सर्वं प्रतिष्ठितम्॥"
            </p>


            <div class="swiper swiper-text min-h-[120px] max-w-4xl mx-auto px-4 pointer-events-none opacity-100">
                <div class="swiper-wrapper">
                    <div class="swiper-slide flex items-center justify-center py-4">
                        <p class="text-sm md:text-lg text-white/90 italic drop-shadow-md font-hindi font-light max-w-3xl" data-lang="shlok_trans_hi">
                            अर्थ: गायों में ही सब कुछ स्थित है, गायों में ही तीनों लोक स्थित हैं, गायों में ही जीव (प्राणी) जीवित रहते हैं, गायों में ही सब कुछ समाया हुआ है।
                        </p>
                    </div>
                    <div class="swiper-slide flex items-center justify-center py-4">
                        <p class="text-sm md:text-lg text-white/90 italic drop-shadow-md tracking-wider font-light max-w-3xl" data-lang="shlok_trans_en">
                            "Experience the spirituality of Gau Seva in its purest form. Join us in preserving the sanctity of our sacred cows through care, protection, and devotion."
                        </p>
                    </div>
                    <div class="swiper-slide flex items-center justify-center py-4">
                        <p class="text-sm md:text-lg text-white/90 italic drop-shadow-md tracking-wider font-light max-w-3xl" data-lang="shlok_trans_gu">
                            અર્થ: ગાયોમાં જ બધું સ્થિત છે, ગાયોમાં જ ત્રણેય લોક સ્થિત છે, ગાયોમાં જ જીવ (પ્રાણીઓ) જીવે છે, ગાયોમાં જ બધું સમાયેલું છે.
                        </p>
                    </div>

                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-8 justify-center">
            <a href="/about" class="bg-saffron text-white px-12 py-5 rounded-premium shadow-2xl hover:bg-white hover:text-nature font-bold transition-all duration-700 transform hover:-translate-y-2" data-lang="explore_mission">
                Explore Mission
            </a>
            <button id="vr-toggle" class="bg-white/10 backdrop-blur-md border border-white/40 text-white px-12 py-5 rounded-premium font-bold hover:bg-gold hover:text-nature transition-all duration-700 shadow-xl flex items-center gap-3 group" data-lang="vr_tour">
                <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse group-hover:bg-nature transition-colors"></span>
                Enter 360° Virtual Reality
            </button>
        </div>
    </div>
    <div id="hero-dots"></div>
</section>

<!-- Why People Connect Section -->
<section id="connection" class="py-28 overflow-hidden">
    <div class="container mx-auto px-10 md:px-16 lg:px-24">
        <div class="flex flex-col lg:flex-row gap-20 items-center">
            <div class="lg:w-1/2 md:grid grid-cols-2 gap-4 h-[400px] md:h-[600px]" data-aos="fade-right">
                <!-- Main Image: Visible on all screens -->
                <div class="md:row-span-2 overflow-hidden rounded-premium shadow-lg group h-full">
                    <img src="/asset/img/cow/gushala12.jpg" class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110 object-center" alt="Sacred Cow">
                </div>
                <!-- Supporting Images: Hidden on mobile, shown from md onwards -->
                <div class="hidden md:block overflow-hidden rounded-premium shadow-lg group">
                    <img src="/asset/img/cow/gushala20.jpg" class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110 object-center" alt="Love for Mother Cow">
                </div>
                <div class="hidden md:block overflow-hidden rounded-premium shadow-lg group">
                    <img src="/asset/img/cow/gushala5.jpg" class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110 object-center" alt="Vedic Life">
                </div>
            </div>

            <div class="lg:w-1/2" data-aos="fade-left">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <span class="text-saffron uppercase tracking-[0.4em] text-xs font-bold mb-4 block" data-lang="connection_label">The Eternal Bond</span>
                        <h2 class="text-4xl md:text-5xl font-display leading-[1.2]" data-lang="connection_title">Why People <span class="italic text-gold">Connect</span> With Us</h2>
                    </div>
                </div>
                <div class="section-divider mb-10"></div>

                <!-- English Content -->
                <div class="lang-en transition-all duration-300 opacity-100">
                    <p class="text-nature/80 text-lg md:text-xl font-light leading-relaxed italic mb-8 border-l-4 border-gold/40 pl-8">
                        "For thousands of years, the cow has walked silently beside human life — not asking for anything, yet giving everything."
                    </p>
                    <p class="text-nature/70 text-lg leading-relaxed mb-6">
                        In the homes and fields of ancient India, she nourished generations, becoming a living symbol of patience, kindness, and selfless giving. Our ancestors didn’t just see her as an animal, but as Gau Mata — a gentle mother who sustained life without expectation.
                    </p>
                    <p class="text-nature/70 text-lg leading-relaxed mb-6">
                        Even Krishna, in his childhood, chose to live among cows, caring for them with love, showing us that true divinity lies in compassion. This bond is not built on belief alone, but on centuries of shared existence, gratitude, and respect.
                    </p>
                    <p class="text-saffron font-bold text-xl drop-shadow-sm">
                        "Protecting the cow is not about obligation — it is about remembering who stood by us quietly, and choosing, with love, to stand by her today."
                    </p>
                </div>

                <!-- Hindi Content -->
                <div class="lang-hi hidden transition-all duration-300 opacity-0 font-hindi">
                    <p class="text-nature/80 text-xl md:text-2xl font-bold leading-relaxed italic mb-8 border-l-4 border-saffron/40 pl-8">
                        "हजारों वर्षों से गाय मानव जीवन के साथ चुपचाप चलती आई है — बिना कुछ मांगे, सब कुछ देती हुई।"
                    </p>
                    <p class="text-nature/70 text-xl leading-relaxed mb-6">
                        प्राचीन भारत के घरों और खेतों में उसने पीढ़ियों का पालन किया और धैर्य, करुणा और निस्वार्थ भाव का जीवंत प्रतीक बनी। हमारे पूर्वजों ने उसे केवल एक पशु नहीं माना, बल्कि गौ माता के रूप में सम्मान दिया — एक ऐसी माँ, जो बिना किसी अपेक्षा के जीवन का पोषण करती है।
                    </p>
                    <p class="text-nature/70 text-xl leading-relaxed mb-6">
                        स्वयं कृष्ण ने अपने बाल्यकाल में गायों के बीच रहकर उनकी सेवा की, यह सिखाते हुए कि सच्ची दिव्यता करुणा में बसती है। यह संबंध केवल आस्था नहीं, बल्कि सदियों की साझी यात्रा, कृतज्ञता और सम्मान से बना है।
                    </p>
                    <p class="text-saffron font-bold text-2xl drop-shadow-sm">
                        "इसलिए गाय की रक्षा करना कोई कर्तव्य नहीं, बल्कि उस मौन साथ को याद रखना है, औरप्रेम से आज उसके साथ खड़े होने का निर्णय है।"
                    </p>
                </div>

                <!-- Gujarati Content -->
                <div class="lang-gu hidden transition-all duration-300 opacity-0 font-gujarati">
                    <p class="text-nature/80 text-xl md:text-2xl font-bold leading-relaxed italic mb-8 border-l-4 border-gold/40 pl-8">
                        "હજારો વર્ષોથી, ગાય માનવ જીવનની સાથે શાંતિથી ચાલતી આવી છે — કંઈપણ માંગ્યા વિના, બધું જ આપતી."
                    </p>
                    <p class="text-nature/70 text-xl leading-relaxed mb-6">
                        પ્રાચીન ભારતના ઘરો અને ખેતરોમાં, તેણે પેઢીઓને પોષી, ધીરજ, દયા અને નિઃસ્વાર્થ સેવાનું જીવંત પ્રતીક બની. આપણા પૂર્વજોએ તેને માત્ર એક પ્રાણી તરીકે નહીં, પણ ગૌ માતા તરીકે જોઈ — એક સૌમ્ય માતા જેણે અપેક્ષા વિના જીવનને ટકાવી રાખ્યું.
                    </p>
                    <p class="text-nature/70 text-xl leading-relaxed mb-6">
                        કૃષ્ણે પણ, તેમના બાળપણમાં, ગાયોની વચ્ચે રહેવાનું પસંદ કર્યું, પ્રેમથી તેમની સંભાળ રાખી, આપણને બતાવ્યું કે સાચી દિવ્યતા કરુણામાં રહેલી છે. આ બંધન માત્ર માન્યતા પર આધારિત નથી, પરંતુ સદીઓના સહિયારા અસ્તિત્વ, કૃતજ્ઞતા અને સન્માન પર બનેલું છે.
                    </p>
                    <p class="text-saffron font-bold text-2xl drop-shadow-sm">
                        "ગાયનું રક્ષણ કરવું એ ફરજ વિશે નથી — તે યાદ રાખવા વિશે છે કે કોણ આપણી સાથે શાંતિથી ઉભું હતું, અને પ્રેમ સાથે, આજે તેની સાથે ઉભા રહેવાનું પસંદ કરવું."
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Achievements Section -->
<section id="mission" class="py-28 relative overflow-hidden bg-white">
    <div class="container mx-auto px-10 md:px-16 lg:px-24">
        <div class="grid grid-cols-1 lg:grid-cols-10 gap-16 items-center">
            <div class="lg:col-span-4" data-aos="fade-right">
                <span class="text-saffron uppercase tracking-[0.4em] text-xs font-bold mb-6 block" data-lang="mission_label">The Sacred Impact</span>
                <h2 class="text-4xl md:text-7xl font-display mb-8 leading-tight">
                    <span data-lang="mission_title1">Healing Hearts,</span> <br>
                    <span class="italic text-gold italic" data-lang="mission_title2">Protecting Life</span>
                </h2>
                <div class="section-divider mb-10 w-48"></div>
                <p class="text-nature/60 text-lg md:text-23l font-light leading-relaxed mb-12 italic border-l-4 border-gold/40 pl-8" data-lang="mission_quote">
                    "They shared their life to nourish ours. Now, it’s our turn to protect them. Join us in giving every cow a home filled with love and dignity."
                </p>
                <div class="flex gap-6 mt-16">
                    <a href="/contact" class="bg-saffron text-white px-10 py-4 rounded-premium font-bold shadow-xl hover:bg-gold hover:text-nature transition-all" data-lang="join_cause">Join the Cause</a>
                </div>
            </div>

            <div class="lg:col-span-3 flex flex-col gap-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="glass-bg p-8 rounded-premium border-l-4 border-saffron shadow-premium hover:translate-x-4 transition-transform duration-500">
                    <div class="text-4xl md:text-5xl font-display text-saffron flex items-baseline gap-1">
                        <span class="counter" data-target="20000">20,000</span><span class="text-2xl font-bold">+</span>
                    </div>
                    <p class="text-xs font-bold text-nature uppercase tracking-widest mt-3" data-lang="stat_serving">Serving with Love & Care!</p>
                </div>
                <div class="glass-bg p-8 rounded-premium border-l-4 border-nature/50 shadow-premium hover:translate-x-4 transition-transform duration-500">
                    <div class="text-4xl md:text-5xl font-display text-nature flex items-baseline gap-1">
                        <span class="counter" data-target="1500">1,500</span><span class="text-2xl font-bold">+</span>
                    </div>
                    <p class="text-xs font-bold text-nature uppercase tracking-widest mt-3" data-lang="stat_treatment">Under Treatment: Fighting for Life!</p>
                </div>
                <div class="glass-bg p-8 rounded-premium border-l-4 border-gold shadow-premium hover:translate-x-4 transition-transform duration-500">
                    <div class="text-4xl md:text-5xl font-display text-gold flex items-baseline gap-1">
                        <span class="counter" data-target="13000">13,000</span><span class="text-2xl font-bold">+</span>
                    </div>
                    <p class="text-xs font-bold text-nature uppercase tracking-widest mt-3" data-lang="stat_healed">Healed & Given a New Chance!</p>
                </div>
            </div>

            <div class="hidden lg:block lg:col-span-3 h-[600px] relative overflow-hidden rounded-premium shadow-premium" data-aos="fade-left" data-aos-delay="400">
                <div class="swiper swiper-vertical w-full h-full">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide"><img src="/asset/img/cow/gushala16.jpg" class="w-full h-full object-cover object-center" alt="Sewa Highlight 1"></div>
                        <div class="swiper-slide"><img src="/asset/img/cow/gushala12.jpg" class="w-full h-full object-cover object-center" alt="Sewa Highlight 2"></div>
                        <div class="swiper-slide"><img src="/asset/img/cow/gushala3.jpg" class="w-full h-full object-cover object-center" alt="Sewa Highlight 3"></div>
                        <div class="swiper-slide"><img src="/asset/img/cow/gushala20.jpg" class="w-full h-full object-cover object-center" alt="Sewa Highlight 4"></div>
                    </div>
                </div>
                <div class="absolute bottom-6 left-6 z-10">
                    <span class="text-white text-[12px] font-bold uppercase tracking-[0.4em] drop-shadow-md bg-black/30 px-4 py-1 rounded-full backdrop-blur-sm">Chronicles of Healing</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- SACRED SANCTUARY GALLERY: Moments of Devotion -->
<!-- ═══════════════════════════════════════════════════════════ -->
<section id="gallery" class="py-28 relative overflow-hidden bg-white">
    <div class="container mx-auto px-10 md:px-16 lg:px-24">
        <div class="flex flex-col md:flex-row justify-between items-end mb-20 gap-8">
            <div data-aos="fade-right">
                <span class="text-saffron uppercase tracking-[0.5em] text-[12px] font-black mb-4 block" data-lang="gallery_label">Sacred Sanctuary</span>
                <h2 class="text-4xl md:text-6xl font-display leading-tight text-nature" data-lang="gallery_title">Moments of <span class="italic text-gold">Devotion</span></h2>
            </div>
            <div class="flex gap-4 relative z-50" data-aos="fade-left">
                <button class="gallery-prev w-14 h-14 rounded-full border border-gold/20 flex items-center justify-center text-nature hover:bg-gold hover:text-white transition-all duration-500 shadow-sm pointer-events-auto">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="gallery-next w-14 h-14 rounded-full border border-gold/20 flex items-center justify-center text-nature hover:bg-gold hover:text-white transition-all duration-500 shadow-sm pointer-events-auto">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <div class="swiper swiper-gallery overflow-visible">
            <div class="swiper-wrapper">
                <?php
                $gallery_items = [
                    ['img' => 'gushala3.jpg', 'title' => 'Morning Aarti'],
                    ['img' => 'gushala5.jpg', 'title' => 'Healing Hands'],
                    ['img' => 'gushala12.jpg', 'title' => 'Sacred Fodder'],
                    ['img' => 'gushala20.jpg', 'title' => 'Eternal Peace'],
                    ['img' => 'gushala1.jpg', 'title' => 'New Life'],
                    ['img' => 'gushala2.jpg', 'title' => 'Safe Haven'],
                ];
                foreach ($gallery_items as $item): ?>
                    <div class="swiper-slide w-[250px] md:w-[350px] group">
                        <div class="relative h-[300px] md:h-[400px] rounded-[3rem] overflow-hidden shadow-premium">
                            <img src="/asset/img/cow/<?= $item['img'] ?>" class="w-full h-full object-cover transform scale-110 group-hover:scale-100 transition-transform duration-[2000ms]" alt="<?= $item['title'] ?>">
                            <div class="absolute inset-x-0 bottom-0 p-10 bg-gradient-to-t from-black/80 via-black/20 to-transparent translate-y-full group-hover:translate-y-0 transition-transform duration-700">
                                <h4 class="text-white text-2xl font-display italic"><?= $item['title'] ?></h4>
                                <div class="w-12 h-px bg-gold mt-4"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>





<!-- Financial Transparency Section -->

<section id="transparency" class="py-28 overflow-hidden">
    <div class="container mx-auto px-10 md:px-16 lg:px-24">
        <div class="text-center mb-16" data-aos="fade-down">
            <span class="text-saffron uppercase tracking-[0.4em] text-xs font-bold mb-4 block" data-lang="trust_label">Trust & Transparency</span>
            <h2 class="text-4xl md:text-5xl font-display mb-8" data-lang="trust_title">Where your <span class="italic text-gold">Donations</span> go?</h2>
            <p class="text-nature/60 max-w-2xl mx-auto italic" data-lang="trust_quote">"Every rupee you contribute is a silent promise of life to our cows."</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-20 items-center">
            <div class="lg:w-1/3 relative flex flex-col items-center" data-aos="fade-right">
                <div class="relative w-72 h-72 md:w-80 md:h-80 flex items-center justify-center">
                    <svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90">
                        <!-- Background Circle -->
                        <circle cx="50" cy="50" r="40" class="stroke-gold/10 fill-none" stroke-width="8" pathLength="100"></circle>
                        <!-- Progress Circle -->
                        <circle cx="50" cy="50" r="40" class="stroke-saffron fill-none" stroke-width="8" stroke-dasharray="<?= $dash_array_val ?> 100" pathLength="100" stroke-linecap="round"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8">
                        <h4 class="text-sm font-bold uppercase tracking-widest text-nature/40 mb-2" data-lang="raised_label">Raised</h4>
                        <span class="text-3xl font-display text-nature">₹ <?= number_format($raised_amount) ?></span>
                    </div>
                </div>
                <div class="mt-12 text-center">
                    <p class="text-nature font-bold text-xl mb-2">₹ <?= number_format($raised_amount) ?> <span class="text-nature/30 text-base font-light italic" data-lang="raised_out_of">raised out of</span></p>
                    <p class="text-saffron text-2xl font-display">₹ <?= number_format($goal_amount) ?> <span class="text-sm font-bold uppercase tracking-widest" data-lang="goal_label">Goal</span></p>
                </div>
            </div>

            <div class="lg:w-2/3" data-aos="fade-left">
                <div class="glass-bg rounded-premium shadow-premium border border-gold/10 overflow-hidden">
                    <div class="overflow-x-auto">
                        <!-- Desktop Table View -->
                        <table class="w-full text-left hidden sm:table">
                            <thead>
                                <tr class="bg-saffron/5 border-b border-gold/10">
                                    <th class="py-6 px-8 text-[12px] font-bold uppercase tracking-widest text-saffron" data-lang="table_material">Material</th>
                                    <th class="py-6 px-4 text-[12px] font-bold uppercase tracking-widest text-nature/60 text-center" data-lang="table_quantity">Quantity</th>
                                    <th class="py-6 px-4 text-[12px] font-bold uppercase tracking-widest text-nature/60 text-center" data-lang="table_price">Price/Unit</th>
                                    <th class="py-6 px-8 text-[12px] font-bold uppercase tracking-widest text-nature text-right" data-lang="table_total">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gold/5">
                                <?php foreach ($transparency_mats as $m):
                                    $target_v = isset($m['target_val']) ? (int)$m['target_val'] : 10000;
                                    $current_v = isset($m['current_val']) ? (int)$m['current_val'] : 0;
                                    $p_perc = ($target_v > 0) ? ($current_v / $target_v) * 100 : 0;
                                    $p_perc = min(100, $p_perc);
                                ?>
                                    <tr class="hover:bg-gold/5 transition-colors group">
                                        <td class="py-6 px-8">
                                            <div class="flex flex-col gap-2">
                                                <div class="flex items-center gap-3">
                                                    <span class="w-2 h-2 rounded-full <?= $m['color_class'] ?>"></span>
                                                    <span class="text-nature font-bold"><?= htmlspecialchars($m['name_en']) ?></span>
                                                </div>
                                                <!-- Enhanced Progress Bar -->
                                                <div class="w-full max-w-[200px] h-1.5 bg-nature/5 rounded-full overflow-hidden relative">
                                                    <div class="absolute inset-0 <?= $m['color_class'] ?> transform origin-left scale-x-0 group-aos-animate:scale-x-<?= (int)$p_perc ?> transition-transform duration-[1500ms] " style="transition-delay: 1000ms; width: <?= $p_perc ?>%"></div>
                                                    <div class="<?= $m['color_class'] ?> h-full" style="width: 0%; transition: width 2s cubic-bezier(0.2, 0, 0.2, 1); transition-delay: 1s;" data-width="<?= $p_perc ?>%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6 px-4 text-center text-nature/60">
                                            <span class="font-bold"><?= $m['quantity'] ?></span>
                                            <span class="text-[12px] uppercase tracking-tighter"><?= htmlspecialchars($m['unit_name'] ?? 'Units') ?></span>
                                        </td>
                                        <td class="py-6 px-4 text-center text-nature/60"><?= $m['unit_price'] ?></td>
                                        <td class="py-6 px-8 text-right font-bold text-nature"><?= $m['total_amount'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Mobile List View -->
                        <div class="sm:hidden divide-y divide-gold/10 p-4">
                            <?php foreach ($transparency_mats as $m): ?>
                                <div class="py-6 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <span class="w-2 h-2 rounded-full <?= $m['color_class'] ?>"></span>
                                        <span class="text-nature font-bold text-lg"><?= htmlspecialchars($m['name_en']) ?></span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-saffron/5 p-3 rounded-2xl border border-saffron/10">
                                            <p class="text-[12px] uppercase tracking-widest font-bold text-nature/40 mb-1" data-lang="table_quantity">Quantity</p>
                                            <p class="text-nature font-bold"><?= $m['quantity'] ?></p>
                                        </div>
                                        <div class="bg-nature/5 p-3 rounded-2xl border border-nature/10">
                                            <p class="text-[12px] uppercase tracking-widest font-bold text-nature/40 mb-1" data-lang="table_price">Price/Unit</p>
                                            <p class="text-nature font-bold"><?= $m['unit_price'] ?></p>
                                        </div>
                                    </div>
                                    <div class="bg-gold/10 p-4 rounded-2xl border border-gold/20 flex justify-between items-center">
                                        <span class="text-[12px] font-bold uppercase tracking-widest text-nature/60" data-lang="table_total">Total Amount</span>
                                        <span class="text-xl font-display text-nature font-bold"><?= $m['total_amount'] ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</section>

<!-- State of the Gau Mata: Classic Left-Aligned Information Section (2000 - 2026) -->
<section id="reality-check" class="py-24 relative overflow-hidden bg-white">
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none">
        <div class="w-full h-full" style="background-image: url('https://www.transparenttextures.com/patterns/graphy.png');"></div>
    </div>

    <div class="container mx-auto px-10 md:px-16 lg:px-24 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
            <div class="lg:col-span-8">
                <div data-aos="fade-right">
                    <span class="text-red-600 uppercase tracking-[0.4em] text-xs font-bold mb-4 block animate-pulse" data-lang="reality_label">Critical Status Report</span>
                    <h2 class="text-4xl md:text-6xl font-display leading-[1.1] text-nature mb-12" data-lang="reality_title">
                        State of the <span class="italic text-gold italic">Gau Mata</span> <br>
                        <span class="text-nature/40 text-2xl font-light italic" data-lang="reality_subtitle">(Transition 2000 — 2026)</span>
                    </h2>
                </div>

                <div id="narrative-container" class="space-y-10" data-aos="fade-up">
                    <div class="lang-en text-nature/80 text-xl md:text-2xl font-light leading-relaxed">
                        <p class="mb-8">
                            For centuries, the Indian cow has been revered as a silent mother of our civilization—a living temple of compassion. In our ancient roots, she was the heartbeat of every home. However, as our landscapes shifted from green pastures to concrete jungles, her place in our hearts was overshadowed by the rush of modern progress. Today, this displacement has created a silent humanitarian crisis: a staggering <span class="underline decoration-red-500/30 decoration-4 font-bold text-nature italic">5 Million+</span> stray cattle are roaming the roads, surviving not on grass, but on the cold leftovers of an indifferent society.
                        </p>
                        <p class="mb-8 p-8 bg-white/10 backdrop-blur-sm rounded-[2rem] shadow-sm italic border-2 border-saffron">
                            "The reality on our highways is heartbreaking. We are witnessing between <span class="underline decoration-saffron/30 decoration-4 font-bold text-nature italic">20-50</span> daily road deaths due to high-speed accidents and absolute neglect. These are not just numbers; they are lives that once nourished ours. Even the survivors suffer in silence, often carrying over <span class="underline decoration-gold/30 decoration-4 font-bold text-nature italic">25kg+ of toxic plastic</span> in their stomachs, consumed in a desperate search for food amidst urban waste."
                        </p>
                        <p class="mb-8">
                            The transition from being worshipped on thrones to being abandoned on street corners is a deep scar on our cultural identity. <span class="underline decoration-nature/20 decoration-2 font-bold text-nature italic">Lakhs</span> of these sacred souls fall victim to illegal trade and the cruelty of the elements. True respect toward the Gau Mata comes not from words alone, but from the responsibility of protection.
                        </p>
                        <p>
                            Our organization—<span class="text-saffron font-bold text-2xl md:text-3xl italic">શ્રી ગૌ રક્ષક સેવા સમિતિ (પાંજરાપોળ)</span>—stands as a line of defense for those who cannot speak for themselves. We provide more than just shelter; we offer a permanent sanctuary of healing, dignity, and a return to the love they so rightfully deserve.
                        </p>
                    </div>

                    <div class="lang-hi hidden space-y-10 font-indic text-nature/80 text-2xl md:text-3xl font-medium leading-[1.8]">
                        <p>सदियों से भारतीय गाय हमारी सभ्यता की मौन साक्षी और करुणा का जीता-जागता स्वरूप रही है। आज लगभग <span class="underline decoration-red-500/30 decoration-4 font-bold text-nature">50 लाख+</span> गौ माता सड़कों पर बेसहारा हैं।</p>
                        <p class="p-8 bg-white/10 backdrop-blur-sm rounded-[2rem] shadow-sm italic border-2 border-saffron">प्रतिदिन <span class="underline decoration-saffron/30 decoration-4 font-bold text-nature">20-50</span> गौ माताओं की मृत्यु देख रहे हैं, और उनके पेट में <span class="underline decoration-gold/30 decoration-4 font-bold text-nature">25 किलो+</span> ज़हरीला प्लास्टिक मिलता है।</p>
                        <p><span class="text-saffron font-bold text-3xl">શ્રી ગૌ રક્ષક સેવા સમિતિ (પાંજરાપોળ)</span> इन असहाय गायों के लिए एक सुरक्षा कवच के रूप में कार्य कर रही है।</p>
                    </div>

                    <div class="lang-gu hidden space-y-10 font-indic text-nature/80 text-2xl md:text-3xl font-medium leading-[1.8]">
                        <p>સદીઓથી ભારતીય ગાય આપણી સંસ્કૃતિની અવિભાજ્ય કડી રહી છે. આજે <span class="underline decoration-red-500/30 decoration-4 font-bold text-nature">50 લાખ+</span> ગાયો રસ્તાઓ પર રઝળે છે.</p>
                        <p class="p-8 bg-white/10 backdrop-blur-sm rounded-[2rem] shadow-sm italic border-2 border-saffron">દરરોજ <span class="underline decoration-saffron/30 decoration-4 font-bold text-nature">20-50</span> ગાયો અકસ્માતે મોતને ભેટે છે અને તેમના પેટમાં <span class="underline decoration-gold/30 decoration-4 font-bold text-nature">25 કિલો+</span> પ્લાસ્ટિક મળે છે.</p>
                        <p>શ્રી ગૌ રક્ષક સેવા સમિતિ (પાંજરાપોળ) ગૌ રક્ષણ માટે કટિબદ્ધ છે.</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4" data-aos="fade-left">
                <div class="sticky top-32 space-y-8">
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl border border-nature/5">
                        <h3 class="text-2xl font-display mb-8 text-nature italic flex items-center gap-3"><span class="w-3 h-3 bg-red-600 rounded-full animate-ping"></span><span data-lang="recent_condition_label">Recent Condition</span></h3>
                        <div class="relative group rounded-3xl overflow-hidden mb-8 h-72 shadow-xl shadow-nature/10">
                            <img src="/asset/img/cow/gushala16.jpg" class="w-full h-full object-cover transform scale-105 group-hover:scale-110 transition-transform duration-1000">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-80"></div>
                            <div class="absolute bottom-6 left-6 right-6">
                                <span class="text-white text-[12px] font-bold uppercase tracking-widest bg-red-600 px-4 py-1.5 rounded-full mb-3 inline-block" data-lang="status_critical">Status: Critical</span>
                                <h4 class="text-white text-xl font-display italic" data-lang="case_title">Rescued Patient Case #450</h4>
                            </div>
                        </div>
                        <p class="text-nature/60 text-[15px] leading-relaxed mb-10 italic" data-lang="case_desc">This mother was rescued from highway collision. Our team is working 24/7 on her recovery. Your support makes this possible.</p>
                        <a href="/donate" class="bg-saffron text-white w-full py-5 rounded-2xl font-bold uppercase tracking-widest text-[15px] hover:bg-nature transition-all shadow-xl block text-center" data-lang="support_life">Support Her Life</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Donors Hall of Fame: High-Fidelity Multi-Row Marquee -->
<section id="noble-donors" class="py-24 relative overflow-hidden bg-[#fdfaf7] border-y border-nature/5">
    <div class="container mx-auto px-6 relative z-10 text-center mb-16">
        <div data-aos="fade-up">
            <span class="text-saffron uppercase tracking-[0.6em] text-[10px] md:text-xs font-black mb-4 block">Hall of Fame</span>
            <h2 class="text-5xl md:text-7xl font-display text-nature mb-6">Our Noble <span class="italic text-gold underline decoration-gold/20 underline-offset-8">Donors</span></h2>
            <div class="flex items-center justify-center gap-4 mt-12 bg-nature/5 w-fit mx-auto p-1.5 rounded-full border border-nature/10">
                <button onclick="filterDonors('recent')" id="btn-recent-marquee" class="px-8 py-3 rounded-full bg-nature text-white font-bold text-xs uppercase tracking-widest transition-all shadow-lg shadow-nature/20 active-pill">Recent</button>

            </div>
        </div>
    </div>

    <!-- Marquee Rows Wrapper -->
    <div class="relative w-full overflow-hidden donors-hall-wrapper py-10" style="mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);">

        <div class="space-y-12">
            <!-- Row 1: Moving Left -->
            <div class="flex marquee-row-left gap-8 animate-marquee-home whitespace-nowrap">
                <?php
                $row1 = array_slice($small_donors, 0, 15);
                for ($i = 0; $i < 2; $i++): foreach ($row1 as $d): ?>
                        <div class="donor-card-mini flex items-center gap-5 bg-white/80 backdrop-blur-md px-8 py-5 rounded-[2.5rem] min-w-[320px] shadow-sm border border-nature/5 hover:border-saffron/20 hover:shadow-xl transition-all duration-500 group">
                            <div class="w-14 h-14 rounded-full bg-[#f1f5e9] flex items-center justify-center font-bold text-nature shadow-inner overflow-hidden flex-shrink-0 border-2 border-white group-hover:scale-110 transition-transform">
                                <?php if ($d['profile_pic'] && $d['profile_pic'] !== 'default_donor.png'): ?>
                                    <img src="/asset/img/donors/<?= $d['profile_pic'] ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?= strtoupper(substr($d['name'], 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            <div class="text-left">
                                <h4 class="text-[16px] font-bold text-nature/80"><?= htmlspecialchars($d['name']) ?></h4>
                                <p class="text-[13px] text-nature/40 font-medium mt-1">Donated <span class="text-saffron font-bold tracking-wider">₹<?= number_format($d['amount']) ?></span></p>
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <span class="w-1 h-1 bg-gold rounded-full"></span>
                                    <span class="text-[10px] text-nature/30 uppercase tracking-widest font-black italic">about <?= rand(1, 24) ?> hrs ago</span>
                                </div>
                            </div>
                        </div>
                <?php endforeach;
                endfor; ?>
            </div>

            <!-- Row 2: Moving Right -->
            <div class="flex marquee-row-right gap-8 animate-marquee-home-reverse whitespace-nowrap" style="margin-left: -500px;">
                <?php
                $row2 = array_slice($small_donors, 15, 15);
                for ($i = 0; $i < 2; $i++): foreach ($row2 as $d): ?>
                        <div class="donor-card-mini flex items-center gap-5 bg-white/80 backdrop-blur-md px-8 py-5 rounded-[2.5rem] min-w-[320px] shadow-sm border border-nature/5 hover:border-saffron/20 hover:shadow-xl transition-all duration-500 group">
                            <div class="w-14 h-14 rounded-full bg-[#f1f5e9] flex items-center justify-center font-bold text-nature shadow-inner overflow-hidden flex-shrink-0 border-2 border-white group-hover:scale-110 transition-transform">
                                <?php if ($d['profile_pic'] && $d['profile_pic'] !== 'default_donor.png'): ?>
                                    <img src="/asset/img/donors/<?= $d['profile_pic'] ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?= strtoupper(substr($d['name'], 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            <div class="text-left">
                                <h4 class="text-[16px] font-bold text-nature/80"><?= htmlspecialchars($d['name']) ?></h4>
                                <p class="text-[13px] text-nature/40 font-medium mt-1">Donated <span class="text-saffron font-bold tracking-wider">₹<?= number_format($d['amount']) ?></span></p>
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <span class="w-1 h-1 bg-gold rounded-full"></span>
                                    <span class="text-[10px] text-nature/30 uppercase tracking-widest font-black italic">about <?= rand(1, 24) ?> hrs ago</span>
                                </div>
                            </div>
                        </div>
                <?php endforeach;
                endfor; ?>
            </div>

            <!-- Row 3: Moving Left -->
            <div class="flex marquee-row-left gap-8 animate-marquee-home whitespace-nowrap">
                <?php
                $row3 = array_slice($small_donors, 30, 20);
                for ($i = 0; $i < 2; $i++): foreach ($row3 as $d): ?>
                        <div class="donor-card-mini flex items-center gap-5 bg-white/80 backdrop-blur-md px-8 py-5 rounded-[2.5rem] min-w-[320px] shadow-sm border border-nature/5 hover:border-saffron/20 hover:shadow-xl transition-all duration-500 group">
                            <div class="w-14 h-14 rounded-full bg-[#f1f5e9] flex items-center justify-center font-bold text-nature shadow-inner overflow-hidden flex-shrink-0 border-2 border-white group-hover:scale-110 transition-transform">
                                <?php if ($d['profile_pic'] && $d['profile_pic'] !== 'default_donor.png'): ?>
                                    <img src="/asset/img/donors/<?= $d['profile_pic'] ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?= strtoupper(substr($d['name'], 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            <div class="text-left">
                                <h4 class="text-[16px] font-bold text-nature/80"><?= htmlspecialchars($d['name']) ?></h4>
                                <p class="text-[13px] text-nature/40 font-medium mt-1">Donated <span class="text-saffron font-bold tracking-wider">₹<?= number_format($d['amount']) ?></span></p>
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <span class="w-1 h-1 bg-gold rounded-full"></span>
                                    <span class="text-[10px] text-nature/30 uppercase tracking-widest font-black italic">about <?= rand(1, 24) ?> hrs ago</span>
                                </div>
                            </div>
                        </div>
                <?php endforeach;
                endfor; ?>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 text-center mt-1" data-aos="fade-up">
        <a href="/donors" class="inline-flex items-center gap-4 px-12 py-5 border-2 border-nature/10 rounded-2xl font-black uppercase tracking-[0.4em] text-[12px] text-nature hover:bg-nature hover:text-white hover:border-nature transition-all duration-500 shadow-xl shadow-nature/5">
            View More Devotees
        </a>
    </div>
</section>

<style>
    .animate-marquee-home {
        display: inline-flex;
        animation: marquee-scroll 80s linear infinite;
        min-width: 100%;
    }

    .animate-marquee-home-reverse {
        display: inline-flex;
        animation: marquee-scroll-reverse 80s linear infinite;
        min-width: 100%;
    }

    @keyframes marquee-scroll {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(-50%);
        }
    }

    @keyframes marquee-scroll-reverse {
        from {
            transform: translateX(-50%);
        }

        to {
            transform: translateX(0);
        }
    }

    .marquee-row-left:hover,
    .marquee-row-right:hover {
        animation-play-state: paused;
    }

    .donors-hall-wrapper {
        -webkit-mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
        mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
    }
</style>

<!-- Gaushala Seva: Saffron Themed Donation Grid -->
<section id="seva-options" class="py-28 relative overflow-hidden bg-white">
    <!-- Saffron Layered BG -->
    <div class="absolute inset-0 bg-saffron/[0.03] z-[1]"></div>
    <div class="absolute inset-0 pointer-events-none z-50 opacity-20 select-none">
        <img src="/asset/img/cow/overlay.png" class="w-full h-full object-cover mix-blend-multiply" alt="Section Texture Overlay">
    </div>

    <div class="container mx-auto px-10 md:px-16 lg:px-24 relative z-20">
        <div class="text-center max-w-3xl mx-auto mb-20" data-aos="fade-down">
            <span class="text-saffron uppercase tracking-[0.5em] text-[12px] font-black mb-4 block" data-lang="seva_label">Divine Opportunities</span>
            <h2 class="text-4xl md:text-6xl font-display leading-tight text-nature mb-6" data-lang="seva_title">Gaushala <span class="italic text-gold">Seva</span></h2>
            <p class="text-nature/50 italic text-lg" data-lang="seva_quote">Choose your path of service and be the light in their lives.</p>
            <div class="section-divider mx-auto mt-8"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($seva_options)): ?>
                <!-- Fallback if DB is empty -->
                <div class="col-span-full py-20 text-center glass rounded-[3rem] opacity-50">
                    <p class="italic text-nature/40 font-display text-xl">Divine opportunities are being prepared...</p>
                </div>
            <?php else: ?>
                <?php foreach ($seva_options as $index => $seva):
                    // Determine which language to show initially
                    $lang = $_SESSION['lang'] ?? 'en';
                    $title = $seva['title_' . $lang] ?: $seva['title_en'];
                    $desc = $seva['description_' . $lang] ?: $seva['description_en'];
                    $icon = $seva['icon_class'] ?: 'fas fa-heart';
                    $color = $seva['color_class'] ?: 'saffron';

                    // Specific color adjustments for the UI
                    $bg_icon_class = "bg-$color/10";
                    $text_icon_class = "text-$color";
                    if ($color == 'nature') {
                        $bg_icon_class = "bg-nature/5";
                        $text_icon_class = "text-nature";
                    }
                    if ($color == 'gold') {
                        $bg_icon_class = "bg-gold/5";
                        $text_icon_class = "text-gold";
                    }
                    if (strpos($color, 'red') !== false) {
                        $bg_icon_class = "bg-red-50";
                        $text_icon_class = "text-red-600";
                    }
                ?>
                    <div class="bg-[#fff9f2] p-10 rounded-[2.5rem] border border-saffron/20 shadow-xl hover:shadow-2xl hover:bg-saffron transition-all duration-500 group text-center" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="w-20 h-20 <?= $bg_icon_class ?> rounded-full flex items-center justify-center mx-auto mb-8 group-hover:bg-white/20 group-hover:text-white transition-colors duration-500">
                            <i class="<?= $icon ?> text-3xl <?= $text_icon_class ?> group-hover:text-white"></i>
                        </div>
                        <h4 class="text-2xl font-display text-nature mb-4 italic group-hover:text-white transition-colors">
                            <span class="lang-en"><?= htmlspecialchars($seva['title_en'] ?? 'Sacred Seva') ?></span>
                            <span class="lang-hi hidden"><?= htmlspecialchars(($seva['title_hi'] ?? null) ?: ($seva['title_en'] ?? 'पवित्र सेवा')) ?></span>
                            <span class="lang-gu hidden"><?= htmlspecialchars(($seva['title_gu'] ?? null) ?: ($seva['title_en'] ?? 'પવિત્ર સેવા')) ?></span>
                        </h4>
                        <p class="text-nature/60 text-[15px] mb-8 leading-relaxed group-hover:text-white/90 transition-colors">
                            <span class="lang-en"><?= htmlspecialchars($seva['description_en'] ?? 'No description available.') ?></span>
                            <span class="lang-hi hidden"><?= htmlspecialchars(($seva['description_hi'] ?? null) ?: ($seva['description_en'] ?? 'विवरण उपलब्ध नहीं है।')) ?></span>
                            <span class="lang-gu hidden"><?= htmlspecialchars(($seva['description_gu'] ?? null) ?: ($seva['description_en'] ?? 'વર્ણન ઉપલબ્ધ નથી.')) ?></span>
                        </p>
                        <a href="/donate" class="bg-saffron/10 text-saffron px-10 py-4 rounded-full font-bold uppercase tracking-widest text-[15px] group-hover:bg-white group-hover:text-saffron transition-all shadow-sm block" data-lang="donate_now">Donate Now</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($has_more_seva): ?>
            <!-- View All Button (Visible only if > 6) -->
            <div class="mt-20 text-center" data-aos="fade-up">
                <a href="/gaushala-seva" class="inline-flex items-center gap-6 group bg-nature text-white px-16 py-6 rounded-full font-black uppercase tracking-[0.3em] text-[15px] shadow-2xl shadow-nature/30 hover:bg-gold hover:text-nature transition-all duration-700">
                    <span data-lang="view_all_seva">View All Opportunities</span>
                    <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center group-hover:bg-nature/10 transition-colors">
                        <i class="fas fa-arrow-right transition-transform group-hover:translate-x-2"></i>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- PREMIUM SPIRITUAL DONATION CENTER: The Ultimate Sewa Portal -->
<!-- ═══════════════════════════════════════════════════════════ -->
<section id="donation-portal" class="py-28 relative overflow-hidden bg-[#fffaf5]">
    <!-- Decorative Divine Elements -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-saffron/5 rounded-full blur-[120px] -z-10 translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-gold/5 rounded-full blur-[100px] -z-10 -translate-x-1/2 translate-y-1/2"></div>

    <div class="container mx-auto px-10 md:px-16 lg:px-24">
        <div class="text-center mb-16" data-aos="fade-down">
            <span class="text-saffron uppercase tracking-[0.6em] text-[12px] font-black mb-4 block" data-lang="donate_offer">Offer Your Devotion</span>
            <h2 class="text-4xl md:text-6xl font-display text-nature mb-6 italic leading-tight" data-lang="donate_title">
                Spiritual <span class="text-gold">Donation</span> Center
            </h2>
            <div class="section-divider mx-auto"></div>
        </div>



        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12" data-aos="fade-up">
            <!-- Left Side: Form (7 cols) -->
            <div class="lg:col-span-7">
                <!-- Using bg-white/80 instead of mystery glass-bg for guaranteed visibility -->
                <div class="bg-white/80 backdrop-blur-xl p-8 md:p-12 rounded-[3.5rem] shadow-premium border border-white/50 relative overflow-hidden h-full">
                    <div class="absolute inset-0 -z-10 group/bg overflow-hidden opacity-20">
                        <img src="/asset/img/cow/bg2.jpg" class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-[3000ms]" alt="Sacred Cow Background">
                        <div class="absolute inset-0 bg-gradient-to-br from-saffron/[0.1] to-nature/[0.05]"></div>
                    </div>

                    <form id="home-donation-form" enctype="multipart/form-data" class="space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Select Seva -->
                            <div>
                                <label class="block text-[12px] font-black uppercase tracking-widest text-nature mb-3 ml-2">Select Seva Path</label>
                                <div class="relative">
                                    <select class="w-full h-[72px] bg-white border-2 border-gold/10 focus:border-saffron/40 rounded-2xl px-6 text-nature font-bold focus:outline-none transition-all appearance-none cursor-pointer shadow-sm pr-12">
                                        <option value="">Select Your Seva</option>
                                        <?php
                                        // Fetching all sevas for the dropdown
                                        $all_sevas = $pdo->query("SELECT * FROM seva_options WHERE status = 'active' ORDER BY sort_order ASC")->fetchAll();
                                        foreach ($all_sevas as $s): ?>
                                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['title_en']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-nature/30">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div>
                                <label class="block text-[12px] font-black uppercase tracking-widest text-nature mb-3 ml-2">Amount (₹) <i class="fas fa-question-circle ml-1 opacity-40"></i></label>
                                <input type="number" id="donation-amount" placeholder="Custom Amount" class="w-full h-[72px] bg-white border-2 border-gold/10 focus:border-saffron/40 rounded-2xl px-6 text-nature font-bold text-lg focus:outline-none transition-all shadow-sm">
                            </div>
                        </div>

                        <!-- Amount Presets -->
                        <div class="flex flex-wrap gap-3">
                            <?php $presets = [10000, 5100, 3100, 2100, 1100, 501];
                            foreach ($presets as $p): ?>
                                <button type="button" onclick="setDonationAmount(<?= $p ?>)" class="px-6 py-3 rounded-xl border border-gold/20 text-[12px] font-black bg-white/50 text-nature hover:bg-saffron hover:text-white hover:border-saffron transition-all duration-300">
                                    ₹ <?= number_format($p) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- Personal Details Wrapper -->
                        <div class="space-y-8 bg-white/40 p-8 md:p-10 rounded-[2.5rem] border border-white shadow-xl backdrop-blur-md">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <!-- Full Name -->
                                <div>
                                    <label class="block text-[12px] font-black uppercase tracking-widest text-nature/60 mb-3">Full Name</label>
                                    <input type="text" placeholder="Your Full Name" class="w-full bg-transparent border-b-2 border-nature/10 focus:border-saffron py-3 focus:outline-none transition-all placeholder:text-nature/30 font-bold text-nature">
                                </div>
                                <!-- WhatsApp Number -->
                                <div>
                                    <label class="block text-[12px] font-black uppercase tracking-widest text-nature/60 mb-3">WhatsApp Number</label>
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center gap-2  px-4 py-3">
                                            <img src="https://flagcdn.com/w20/in.png" class="w-5 object-contain" alt="India">
                                            <span class="text-sm font-black text-nature">+91</span>
                                        </div>
                                        <input type="tel" placeholder="Mobile Number" class="flex-grow bg-transparent border-b-2 border-nature/10 focus:border-saffron py-3 focus:outline-none transition-all placeholder:text-nature/30 font-bold text-nature">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <!-- Email Address -->
                                <div>
                                    <label class="block text-[12px] font-black uppercase tracking-widest text-nature/60 mb-3">Email Address</label>
                                    <input type="email" placeholder="Your Email" class="w-full bg-transparent border-b-2 border-nature/10 focus:border-saffron py-3 focus:outline-none transition-all placeholder:text-nature/30 font-bold text-nature">
                                </div>
                                <!-- DOB -->
                                <div>
                                    <label class="block text-[12px] font-black uppercase tracking-widest text-nature/60 mb-3">Date of Birth <span class="opacity-50 italic">(Optional)</span></label>
                                    <input type="date" class="w-full bg-transparent border-b-2 border-nature/10 focus:border-saffron py-3 focus:outline-none transition-all text-nature font-bold">
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row gap-10">
                                <!-- Pincode -->
                                <div class="w-full md:max-w-[200px]">
                                    <label class="block text-[12px] font-black uppercase tracking-widest text-nature/60 mb-3" data-lang="form_pincode">City Pincode</label>
                                    <input type="text" placeholder="Pincode" class="w-full bg-transparent border-b-2 border-nature/10 focus:border-saffron py-3 focus:outline-none transition-all placeholder:text-nature/30 font-bold tracking-widest text-nature" data-lang-placeholder="placeholder_pincode">
                                </div>
                                <!-- Screenshot -->
                                <div class="w-full flex-grow">
                                    <label class="block text-[12px] font-black uppercase tracking-widest text-nature/60 mb-3 flex items-center gap-2"><i class="fas fa-image opacity-50"></i> Payment Screenshot</label>
                                    <input type="file" name="screenshot" accept="image/*" class="w-full bg-transparent border-b-2 border-nature/10 focus:border-saffron py-2 focus:outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-nature/5 file:text-nature hover:file:bg-nature/10 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <button class="w-full py-6 rounded-2xl bg-saffron text-white font-display text-2xl font-bold shadow-2xl hover:bg-nature transition-all duration-700 transform hover:-translate-y-2 relative group overflow-hidden">
                            <span class="relative z-10 italic font-display" data-lang="form_submit">Perform Divine Sewa</span>
                            <div class="absolute inset-x-0 bottom-0 h-0 bg-white/20 group-hover:h-full transition-all duration-500"></div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Side: Info & QR (5 cols) -->
            <div class="lg:col-span-5 flex flex-col gap-6">
                <!-- Payment Details Container -->
                <div id="payment-details-box" class="bg-saffron/5 p-8 md:p-12 rounded-[3.5rem] border border-saffron/10 relative overflow-hidden flex flex-col">
                    <div>
                        <div class="flex flex-col gap-8 mb-8">
                            <!-- Bank Transfer Details -->
                            <div class="space-y-6">
                                <h4 class="text-15 font-black uppercase tracking-[0.3em] text-nature/40 flex items-center gap-2">For Bank Transfer <i class="far fa-copy text-[12px] opacity-40"></i></h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-nature/5 p-6 rounded-2xl text-left">
                                    <div>
                                        <p class="text-[10px] uppercase tracking-widest font-black text-nature/30 mb-1">Account Name</p>
                                        <p class="text-sm font-bold text-nature">SHRI GAU RAKSHK SEVA SAMITI</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-widest font-black text-nature/30 mb-1">Account Number</p>
                                        <p class="text-sm font-bold text-nature">9049164841</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-widest font-black text-nature/30 mb-1">Bank Name</p>
                                        <p class="text-sm font-bold text-nature">KOTAK MAHINDRA BANK</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-widest font-black text-nature/30 mb-1">IFSC Code & Branch</p>
                                        <p class="text-sm font-bold text-nature">KKBK0003065 (GANDHIDHAM)</p>
                                    </div>
                                </div>
                            </div>

                            <!-- UPI Section -->
                            <div class="text-left">
                                <h4 class="text-xs font-black uppercase tracking-[0.3em] text-nature/40 mb-4">UPI Payment</h4>
                                <div class="space-y-1 bg-nature/5 p-4 rounded-2xl w-full">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-nature/40 mb-1">Direct UPI Passage</p>
                                    <p class="text-[14px] lg:text-[14px] text-saffron font-bold">0793065A0168004.BQR@KOTAK <i class="far fa-copy ml-1 opacity-40 hover:opacity-100 cursor-pointer"></i></p>
                                    <p class="text-[10px] lg:text-[10px] text-nature/80 font-bold uppercase tracking-widest mt-1">SHRI GAU RAKSHK SEVA SAMITI</p>
                                </div>
                            </div>
                        </div>

                        <p class="text-center text-[12px] font-italic text-nature/40">(Kindly send us a screenshot for your seva entry)</p>
                    </div>

                    <!-- Footer Support -->
                    <div class="mt-8 pt-6 border-t border-saffron/10">
                        <div class="flex flex-col md:flex-row md:items-center gap-6 mb-8">
                            <a href="tel:+919998581811" class="flex items-center gap-2 text-xs font-bold text-nature/60 hover:text-saffron transition-colors">
                                <i class="fab fa-whatsapp text-lg text-[#25D366]"></i> +91 9998581811 / 9824284733
                            </a>
                            <a href="mailto:sewa@gaushala.org" class="flex items-center gap-2 text-xs font-bold text-nature/60 hover:text-saffron transition-colors">
                                <i class="far fa-envelope text-lg text-gold"></i> sewa@gaushala.org
                            </a>
                        </div>

                        <div class="flex gap-4 items-center opacity-40 grayscale hover:grayscale-0 transition-all">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/24/Paytm_Logo_%28standalone%29.svg" class="h-4 object-contain" alt="Paytm">
                            <i class="fab fa-google-pay text-2xl text-nature"></i>
                            <i class="fab fa-apple-pay text-2xl text-nature"></i>
                            <i class="fab fa-cc-visa text-2xl text-nature"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function switchCurrency(type) {
        const tabIndian = document.getElementById('tab-indian');
        const tabForeign = document.getElementById('tab-foreign');
        const paymentBox = document.getElementById('payment-details-box');

        if (type === 'indian') {
            tabIndian.classList.add('bg-saffron', 'text-white', 'shadow-xl');
            tabIndian.classList.remove('text-nature/40');
            tabForeign.classList.remove('bg-saffron', 'text-white', 'shadow-xl');
            tabForeign.classList.add('text-nature/40');
            paymentBox.classList.remove('opacity-50', 'pointer-events-none');
        } else {
            tabForeign.classList.add('bg-saffron', 'text-white', 'shadow-xl');
            tabForeign.classList.remove('text-nature/40');
            tabIndian.classList.remove('bg-saffron', 'text-white', 'shadow-xl');
            tabIndian.classList.add('text-nature/40');
            // Example behavior: hide QR for foreign?
        }
    }
</script>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- TESTIMONIALS SECTION: Voices of Devotion -->
<!-- ═══════════════════════════════════════════════════════════ -->
<section id="testimonials" class="py-32 relative overflow-hidden bg-[#fffcf9]">
    <div class="absolute inset-0 bg-nature/[0.02] -z-10"></div>
    <div class="absolute top-20 left-10 w-64 h-64 bg-saffron/5 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-gold/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s"></div>

    <div class="container mx-auto px-10 md:px-16 lg:px-24 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-20 gap-8">
            <div data-aos="fade-right">
                <span class="text-saffron uppercase tracking-[0.5em] text-[12px] font-black mb-4 block" data-lang="testimonials_label">Voices of Devotion</span>
                <h2 class="text-4xl md:text-6xl font-display leading-tight text-nature" data-lang="testimonials_title">What our <span class="italic text-gold">Devotees</span> say</h2>
            </div>
            <div class="flex gap-4 relative z-50" data-aos="fade-left">
                <button class="testi-prev w-14 h-14 rounded-full border border-gold/20 flex items-center justify-center text-nature hover:bg-gold hover:text-white transition-all duration-500 shadow-sm pointer-events-auto">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="testi-next w-14 h-14 rounded-full border border-gold/20 flex items-center justify-center text-nature hover:bg-gold hover:text-white transition-all duration-500 shadow-sm pointer-events-auto">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <div class="swiper swiper-testimonials overflow-visible">
            <div class="swiper-wrapper">
                <?php
                // Fetch dynamic testimonials
                $testimonials = [];
                try {
                    $stmt = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC LIMIT 6");
                    if ($stmt) $testimonials = $stmt->fetchAll();
                } catch (Exception $e) {
                    $testimonials = [];
                }

                // High-End Fallback if No DB entries
                if (empty($testimonials)) {
                    $testimonials = [
                        ['id' => 'f1', 'name' => 'Rajesh Sharma', 'role' => 'Monthly Donor', 'testimonial' => "Visiting the Gaushala changed my perspective on life. Seeing the care given to injured cows is truly divine. It's a sanctuary of peace.", 'rating' => 5],
                        ['id' => 'f2', 'name' => 'Priya Patel', 'role' => 'Volunteer', 'testimonial' => "The transparency here is unmatched. I know exactly how my small contribution helps in building sheds and buying medicines.", 'rating' => 5],
                        ['id' => 'f3', 'name' => 'Amit Verma', 'role' => 'Visitor', 'testimonial' => "A truly spiritual experience. The 360° VR tour on the website is amazing, but visiting in person is even better for the soul.", 'rating' => 5]
                    ];
                }

                foreach ($testimonials as $t):
                    $initial = substr($t['name'], 0, 1);
                ?>
                    <div class="swiper-slide h-auto">
                        <div class="glass-bg !bg-white/60 p-10 md:p-12 rounded-[3rem] border border-gold/10 shadow-premium h-full flex flex-col justify-between hover:translate-y-[-10px] transition-transform duration-500">
                            <div>
                                <div class="flex gap-1 mb-8">
                                    <?php for ($i = 0; $i < ($t['rating'] ?? 5); $i++): ?>
                                        <i class="fas fa-star text-gold text-xs"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-nature/80 text-lg md:text-xl font-light italic leading-relaxed mb-10">"<?= htmlspecialchars($t['testimonial']) ?>"</p>
                            </div>
                            <div class="flex items-center gap-6 border-t border-gold/5 pt-8">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-gold/20 to-saffron/20 flex items-center justify-center text-nature font-bold text-xl uppercase"><?= $initial ?></div>
                                <div>
                                    <h4 class="text-nature font-bold text-lg"><?= htmlspecialchars($t['name']) ?></h4>
                                    <p class="text-saffron text-xs font-bold uppercase tracking-widest"><?= htmlspecialchars($t['role']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- GAUSHALA WELFARE SERVICES: Documentary Style -->
<!-- ═══════════════════════════════════════════════════════════ -->
<section id="welfare-services" class="py-28 relative overflow-hidden bg-white">
    <div class="container mx-auto px-10 md:px-16 lg:px-24 relative z-10">
        <div class="flex flex-col lg:flex-row justify-between items-end mb-20 gap-8" data-aos="fade-up">
            <div class="max-w-2xl">
                <span class="text-saffron uppercase tracking-[0.5em] text-[12px] font-black mb-4 block" data-lang="welfare_label">Core Mission</span>
                <h2 class="text-4xl md:text-6xl font-display leading-[1.1] text-nature" data-lang="welfare_title">
                    Welfare <span class="italic text-gold">Beyond</span> <br>
                    Boundaries
                </h2>
            </div>
            <p class="text-nature/40 italic lg:max-w-sm text-right font-light" data-lang="welfare_quote">"True service is not just providing food, but restoring the dignity of life itself."</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            <?php
            $welfare_items = [
                ['icon' => 'fa-stethoscope', 'key' => 'welfare_icu', 'title' => 'Veterinary ICU', 'desc' => 'Immediate surgical intervention for accident victims and plastic removal.'],
                ['icon' => 'fa-wheat-awn', 'key' => 'welfare_nutrition', 'title' => 'Organic Nutrition', 'desc' => 'Scientifically balanced green fodder and mineral-rich supplements.'],
                ['icon' => 'fa-shield-heart', 'key' => 'welfare_rehab', 'title' => 'Trauma Rehab', 'desc' => 'Dedicated recovery zones for cattle rescued from illegal trade and abuse.'],
            ];
            foreach ($welfare_items as $idx => $item): ?>
                <div class="relative group" data-aos="fade-up" data-aos-delay="<?= $idx * 100 ?>">
                    <div class="mb-8 flex items-center gap-6">
                        <div class="w-14 h-14 bg-saffron/5 group-hover:bg-saffron group-hover:text-white rounded-2xl flex items-center justify-center text-saffron transition-all duration-500 shadow-sm">
                            <i class="fas <?= $item['icon'] ?> text-xl"></i>
                        </div>
                        <div class="h-px bg-gold/20 flex-grow"></div>
                    </div>
                    <h4 class="text-2xl font-display text-nature mb-4 italic" data-lang="<?= $item['key'] ?>_title"><?= $item['title'] ?></h4>
                    <p class="text-nature/60 text-base leading-relaxed font-light" data-lang="<?= $item['key'] ?>_desc"><?= $item['desc'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- HALL OF HONOR: REDESIGNED PREMIUM TICKER -->
<!-- ═══════════════════════════════════════════════════════════ -->
<section id="contributors" class="py-24 relative bg-[#fffaf5] overflow-hidden border-y border-gold/10">
    <!-- Subtle Background Mandala Icon -->
    <div class="absolute inset-0 opacity-[0.1] pointer-events-none flex items-center justify-center overflow-hidden">
        <i class="fa-solid fa-om text-[25rem] text-gold"></i>
    </div>

    <div class="container mx-auto px-10 md:px-16 lg:px-24 mb-16 text-center" data-aos="fade-up">
        <span class="text-saffron uppercase tracking-[0.6em] text-[12px] font-black mb-4 block">Eternal Gratitude</span>
        <h2 class="text-4xl md:text-5xl font-display text-nature italic">
            Hall of <span class="text-gold">Honor</span>
        </h2>
        <div class="section-divider mx-auto mt-6"></div>
        <p class="text-nature/30 text-[12px] uppercase tracking-[0.4em] font-bold mt-8">Respected Contributors & Sacred Blessings to These Noble Souls</p>
    </div>

    <!-- Masked Infinite Marquee with Side-to-Side Fading -->
    <div class="relative w-full overflow-hidden py-12" style="mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);">
        <div class="flex animate-marquee whitespace-nowrap gap-10 items-center">
            <?php
            $names = [
                'Shri Rameshbhai Patel (Ahmedabad)',
                'Smt. Kokilaben Shah (Surat)',
                'Shri Dineshbhai Mehta (Rajkot)',
                'Smt. Savitaben Desai (Vadodara)',
                'Shri Bhaveshbhai Joshi (Gandhinagar)',
                'Smt. Ushaben Trivedi (Junagadh)',
                'Shri Kiritbhai Gadhvi',
                'Smt. Hansaben Prajapati',
                'Shri Nileshbhai Rathod'
            ];
            // Doubling for seamless infinite loop
            $ticker_list = array_merge($names, $names, $names, $names);
            foreach ($ticker_list as $idx => $name): ?>
                <div class="flex items-center gap-6 group">
                    <!-- Seperator Divine Icon -->
                    <div class="text-gold/30 text-xs group-hover:text-saffron transition-colors">
                        <i class="fa-solid fa-spa"></i>
                    </div>
                    <!-- Premium Gratitude Badge with Hover State -->
                    <div class="glass-bg !bg-white/60 px-8 py-4 rounded-full border border-gold/10 shadow-sm hover:shadow-xl hover:border-saffron/30 transition-all duration-500 transform hover:-translate-y-1">
                        <span class="text-nature/70 font-display text-lg italic tracking-wide font-medium"><?= $name ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════ -->
<!-- FAQ: Kept Original Design -->
<!-- ═══════════════════════════════════════════════════════════ -->
<section id="faq" class="py-28 relative overflow-hidden bg-white border-t border-nature/5">
    <div class="container mx-auto px-10 md:px-16 lg:px-24 relative z-10">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-20" data-aos="fade-down">
                <span class="text-saffron uppercase tracking-[0.5em] text-[12px] font-black mb-4 block">Help Center</span>
                <h2 class="text-4xl md:text-6xl font-display leading-tight text-nature mb-6">
                    Frequently <span class="italic text-gold">Asked</span> Questions
                </h2>
                <div class="section-divider mx-auto mt-8"></div>
            </div>
            <div class="space-y-4" id="faq-container">
                <?php
                $faq_list = [
                    ['k' => 'faq1', 'q' => 'How are donations used at Gaushala?', 'a' => '100% of your donation goes directly towards the care of our cows — including feed, medicines, veterinary surgeries, shelter construction, and staff salaries.'],
                    ['k' => 'faq2', 'q' => 'Can I visit the Gaushala and meet the cows?', 'a' => 'Absolutely! We welcome visitors and devotees. You can visit our sanctuary, participate in Gau Seva, and even feed the cows yourself.'],
                    ['k' => 'faq3', 'q' => 'How do I adopt a cow or calf for a month?', 'a' => 'You can select the "Adopt Cow for 1 Month" option from our Seva menu and complete a simple donation form.'],
                    ['k' => 'faq4', 'q' => 'Is Gaushala registered and tax-exempt?', 'a' => 'Yes. Our organization is a registered trust and all donations are eligible for tax deduction under Section 80G.'],
                    ['k' => 'faq5', 'q' => 'What happens to the stray cattle you rescue?', 'a' => 'Each rescued cow undergoes a full health assessment. They receive emergency treatment and are assigned a permanent space in our sanctuary.'],
                ];
                foreach ($faq_list as $fi => $faq): ?>
                    <div class="faq-item bg-[#fff9f2] rounded-[1.5rem] border border-saffron/10 overflow-hidden shadow-sm hover:shadow-md transition-shadow" data-aos="fade-up">
                        <button onclick="toggleFaq(this)" class="w-full flex justify-between items-center p-8 text-left group">
                            <span class="text-lg font-display text-nature italic group-hover:text-saffron transition-colors pr-4" data-lang="<?= $faq['k'] ?>_q"><?= $faq['q'] ?></span>
                            <span class="faq-icon flex-shrink-0 w-10 h-10 bg-saffron/10 rounded-full flex items-center justify-center text-saffron transition-all duration-300">
                                <i class="fas fa-plus text-sm"></i>
                            </span>
                        </button>
                        <div class="faq-answer hidden px-8 pb-8">
                            <div class="border-t border-saffron/10 pt-6">
                                <p class="text-nature/70 leading-relaxed font-light" data-lang="<?= $faq['k'] ?>_a"><?= $faq['a'] ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<!-- Cinematic Section Removed by User Request -->
<div style="    background: #050505;
    overflow: clip;
    --gutter: 2rem;
    position: relative;
    z-index: 60;">
    <section class="py-20 relative overflow-hidden bg-saffron/20">

        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gold/5 blur-[1px] rounded-full -z-10 overflow-hidden">
            <img src="/asset/img/cow/bgcow.png" class="w-full h-full object-cover object-center mix-blend-multiply opacity-40" alt="Cow Background Overlay">
        </div>

        <!-- English -->
        <div class="lang-en flex flex-col items-center">
            <h2 class="text-zinc-100 text-6xl md:text-9xl font-display italic text-center drop-shadow-2xl tracking-tighter leading-[0.95] mb-20 animate-fade-in">
                Healing <span class="text-gold brightness-110">Scars</span>, <br>
                Restoring <span class="text-saffron italic opacity-90">Dignity</span>.
            </h2>
            <div class="h-px w-48 bg-gradient-to-r from-transparent via-gold to-transparent opacity-30 mb-12"></div>
            <p class="text-zinc-400 text-lg md:text-2xl max-w-5xl mx-auto italic px-8 text-center leading-relaxed font-light tracking-wide">
                "From the cold neglect of city streets to the warm embrace of our sanctuary—every recovery is a miracle. <br class="hidden md:block"> Your compassion is the heartbeat of these chronicles."
            </p>
        </div>

        <!-- Hindi -->
        <div class="lang-hi hidden flex flex-col items-center">
            <h2 class="text-zinc-100 text-6xl md:text-9xl font-display italic text-center drop-shadow-2xl tracking-tighter leading-[0.95] mb-20 animate-fade-in">
                घावों को <span class="text-gold brightness-110">भरना</span>, <br>
                गरिमा <span class="text-saffron italic opacity-90">लौटाना</span>।
            </h2>
            <div class="h-px w-48 bg-gradient-to-r from-transparent via-gold to-transparent opacity-30 mb-12"></div>
            <p class="text-zinc-400 text-lg md:text-2xl max-w-5xl mx-auto italic px-8 text-center leading-relaxed font-light tracking-wide">
                "शहर की सड़कों की उपेक्षा से लेकर हमारे अभयारण्य की गर्मजोशी तक—हर रिकवरी एक चमत्कार है। <br class="hidden md:block"> आपकी करुणा इन गाथाओं की धड़कन है।"
            </p>
        </div>

        <!-- Gujarati -->
        <div class="lang-gu hidden flex flex-col items-center">
            <h2 class="text-zinc-100 text-6xl md:text-9xl font-display italic text-center drop-shadow-2xl tracking-tighter leading-[0.95] mb-20 animate-fade-in">
                ઘાને <span class="text-gold brightness-110">રુઝવવા</span>, <br>
                ગૌરવ <span class="text-saffron italic opacity-90">પરત લાવવું</span>.
            </h2>
            <div class="h-px w-48 bg-gradient-to-r from-transparent via-gold to-transparent opacity-30 mb-12"></div>
            <p class="text-zinc-400 text-lg md:text-2xl max-w-5xl mx-auto italic px-8 text-center leading-relaxed font-light tracking-wide">
                "શહેરની શેરીઓની ઉપેક્ષાથી લઈને અમારા અભયારણ્યની હૂંફ સુધી—દરેક રિકવરી એક ચમત્કાર છે. <br class="hidden md:block"> તમારી કરુણા આ ગાથાઓની ધબકાર છે."
            </p>
        </div>
    </section>
</div>


</main>
</div>


<script>
    function toggleFaq(btn) {
        const item = btn.closest('.faq-item');
        const answer = item.querySelector('.faq-answer');
        const icon = item.querySelector('.faq-icon i');
        const isOpen = !answer.classList.contains('hidden');
        document.querySelectorAll('.faq-item .faq-answer').forEach(a => a.classList.add('hidden'));
        document.querySelectorAll('.faq-item .faq-icon').forEach(i => {
            i.classList.remove('bg-saffron', 'text-white');
            i.classList.add('bg-saffron/10', 'text-saffron');
        });
        document.querySelectorAll('.faq-item .faq-icon i').forEach(i => {
            i.classList.remove('fa-minus');
            i.classList.add('fa-plus');
        });
        if (!isOpen) {
            answer.classList.remove('hidden');
            btn.querySelector('.faq-icon').classList.remove('bg-saffron/10', 'text-saffron');
            btn.querySelector('.faq-icon').classList.add('bg-saffron', 'text-white');
            icon.classList.remove('fa-plus');
            icon.classList.add('fa-minus');
        }
    }

    // Progress Bar Ritual Animation
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            document.querySelectorAll('[data-width]').forEach(bar => {
                bar.style.width = bar.getAttribute('data-width');
            });
        }, 1000);
    });

    function setDonationAmount(amount) {
        const input = document.getElementById('donation-amount');
        if (input) {
            input.value = amount;
            input.focus();
        }
    }
</script>