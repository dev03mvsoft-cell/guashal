<!-- Hero Section: The Sacred Guardians -->
<section class="relative h-[60vh] md:h-[70vh] flex items-center justify-center overflow-hidden">
    <!-- Background Image with Ken Burns Effect -->
    <div class="absolute inset-0 z-0">
        <img src="/asset/img/cow/bgofthecow.jpg"
            class="w-full h-full object-cover kenburns-bg" alt="Sacred Guardians">
        <div class="absolute inset-0 bg-gradient-to-b from-nature/60 via-nature/40 "></div>
    </div>

    <!-- Content -->
    <div class="container mx-auto px-6 relative z-10 text-center pt-32 md:pt-40" data-aos="zoom-out">
        <span class="text-gold uppercase tracking-[0.5em] text-sm font-bold mb-4 block drop-shadow-lg" data-lang="team_hero_span">The Custodians of Devotion</span>
        <h1 class="text-5xl md:text-8xl font-display text-white mb-6 leading-tight drop-shadow-2xl" data-lang="team_hero_h1">
            Our <span class="italic text-gold">Sacred</span> Guardians
        </h1>
        <div class="section-divider mx-auto w-24 h-1.5 rounded-full shadow-lg"></div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════ -->
<?php
$team = [];
try {
    $stmt = $pdo->query("SELECT * FROM team ORDER BY sort_order ASC, created_at DESC");
    if ($stmt) $team = $stmt->fetchAll();
} catch (Exception $e) {
}

if (!empty($team)):
?>
    <section id="management" class="py-24 bg-[#fffcf5] relative overflow-hidden border-y border-gold/10">
        <div class="container mx-auto px-6 lg:px-24">
            <div class="flex flex-col lg:flex-row justify-between lg:items-end items-start mb-24 gap-8" data-aos="fade-up">
                <div class="max-w-xl text-left">
                    <span class="text-saffron font-bold tracking-[0.4em] uppercase text-xs mb-4 block" data-lang="team_label">The Guardians</span>
                    <h2 class="text-4xl md:text-5xl font-display text-nature tracking-tight leading-tight" data-lang="team_title">Sacred <span class="italic text-gold">Management</span> Council</h2>
                </div>
                <p class="text-nature/40 italic lg:max-w-sm lg:text-right text-left font-light" data-lang="team_quote">"Dedication and discipline lead the way to divine service."</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-12">
                <?php foreach ($team as $member): ?>
                    <div class="text-center group" data-aos="fade-up">
                        <div class="relative w-full aspect-square rounded-[3.5rem] overflow-hidden mb-8 border-2 border-gold/5 shadow-xl group-hover:border-gold transition-colors duration-500">
                            <?php if ($member['image_path']): ?>
                                <img src="<?= htmlspecialchars($member['image_path']) ?>" alt="<?= htmlspecialchars($member['name_en']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <?php else: ?>
                                <div class="w-full h-full bg-nature/5 flex items-center justify-center text-5xl font-display italic text-nature/10"><?= substr($member['name_en'], 0, 1) ?></div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-nature/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="text-white text-[12px] uppercase font-black tracking-widest border border-white/30 px-6 py-3 rounded-full backdrop-blur-sm" data-lang="team_verified">Verified Member</span>
                            </div>
                        </div>
                        <h4 class="text-2xl font-display text-nature italic mb-1 group-hover:text-gold transition-colors" data-trans="en"><?= htmlspecialchars($member['name_en']) ?></h4>
                        <p class="text-saffron text-[12px] font-black uppercase tracking-[0.3em]" data-trans="en"><?= htmlspecialchars($member['designation_en']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
<section class="py-32 relative overflow-hidden bg-white">
    <!-- Background Accents -->
    <div class="absolute top-0 right-0 w-76 h-76 bg-saffron/5 rounded-full blur-3xl -z-10 translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-76 h-76 bg-gold/5 rounded-full blur-3xl -z-10 -translate-x-1/2 translate-y-1/2"></div>

    <div class="container mx-auto px-6 lg:px-24">
        <div class="relative rounded-[3.5rem] shadow-[0_60px_120px_-30px_rgba(0,0,0,0.5)] overflow-hidden p-10 md:p-20 text-center border border-white/10 group" data-aos="zoom-in">
            <!-- Luxury Card Background Interface -->
            <div class="absolute inset-0 z-0">
                <img src="/asset/img/cow/gushala18.jpg"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-[6s] ease-out" alt="Sacred Mission Card">
                <!-- Multi-layered Scrim for Premium Depth -->
                <div class="absolute inset-0 bg-nature/90 backdrop-blur-[4px] mix-blend-multiply"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-nature via-nature/40 to-saffron/10 opacity-60"></div>
            </div>

            <!-- Card Content Wrapper -->
            <div class="relative z-10 flex flex-col items-center">
                <!-- Spiritual Pulse Badge -->
                <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mb-6 text-gold shadow-[0_0_50px_rgba(255,215,0,0.1)] relative group">
                    <div class="absolute inset-0 bg-gold rounded-full blur-2xl opacity-10 animate-pulse"></div>
                    <i class="fas fa-om text-3xl relative z-10 transition-transform duration-700 group-hover:rotate-[360deg]"></i>
                </div>

                <h2 class="text-4xl md:text-7xl font-display text-white mb-6 leading-[1.1] tracking-tight" data-lang="about_cta_title">
                    Join us in <span class="italic text-gold underline decoration-gold/20 underline-offset-[16px]">Helping Cows</span>
                </h2>

                <p class="text-lg md:text-xl text-white/70 mb-10 max-w-3xl mx-auto leading-relaxed italic font-light font-display" data-lang="about_cta_desc">
                    Your help ensures every cow here gets the best care they need. Whether you visit or donate, you are doing a great thing.
                </p>

                <!-- Divine Horizontal Divider -->
                <div class="w-32 h-px bg-gradient-to-r from-transparent via-gold/40 to-transparent mb-10"></div>

                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center w-full sm:w-auto">
                    <a href="/donate" class="bg-saffron text-white px-12 py-5 rounded-full font-bold text-lg hover:shadow-[0_20px_60px_rgba(255,106,0,0.5)] hover:scale-105 active:scale-95 transition-all duration-700 w-full sm:w-auto uppercase tracking-[0.3em] text-[15px] shadow-2xl" data-lang="about_cta_btn1">
                        Donate Now
                    </a>
                    <a href="/contact" class="bg-white/5 backdrop-blur-md border border-white/20 text-white px-12 py-5 rounded-full font-bold text-lg hover:bg-white hover:text-nature transition-all duration-700 w-full sm:w-auto uppercase tracking-[0.3em] text-[15px] shadow-2xl" data-lang="about_cta_btn2">
                        Come Visit Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>