<?php require_once 'config/db.php'; ?>
<!-- Hero Section: The Founding Visionaries -->
<section class="relative h-[60vh] md:h-[70vh] flex items-center justify-center overflow-hidden">
    <!-- Background Image with Ken Burns Effect -->
    <div class="absolute inset-0 z-0">
        <img src="/asset/img/cow/bgofthecow.jpg"
            class="w-full h-full object-cover kenburns-bg" alt="Founding Visionaries">
        <div class="absolute inset-0 bg-gradient-to-b from-nature/60 via-nature/40 "></div>
    </div>

    <!-- Content -->
    <div class="container mx-auto px-6 relative z-10 text-center pt-32 md:pt-40" data-aos="zoom-out">
        <span class="text-gold uppercase tracking-[0.5em] text-sm font-bold mb-4 block drop-shadow-lg" data-lang="founders_hero_span">The Visionaries of Gaushala</span>
        <h1 class="text-5xl md:text-8xl font-display text-white mb-6 leading-tight drop-shadow-2xl" data-lang="founders_hero_h1">
            Our <span class="italic text-gold">Founding</span> Souls
        </h1>
        <div class="section-divider mx-auto w-24 h-1.5 rounded-full shadow-lg"></div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- FOUNDERS SECTION: The Visionaries -->
<!-- ═══════════════════════════════════════════════════════════ -->
<?php
$founders = [];
try {
    $stmt = $pdo->query("SELECT * FROM founders ORDER BY sort_order ASC");
    if ($stmt) $founders = $stmt->fetchAll();
} catch (Exception $e) {
}

if (!empty($founders)):
?>
    <!-- 🏛️ THE SPIRITUAL ARCHITECTS 🏛️ -->
    <section id="founders-list" class="py-24 bg-white relative">
        <div class="container mx-auto px-6 lg:px-24">
            <div class="text-center mb-20" data-aos="fade-down">
                <span class="text-saffron font-bold uppercase tracking-[0.4em] text-xs mb-4 block" data-lang="founders_label">Sacred Lineage</span>
                <h2 class="text-4xl md:text-5xl font-display text-nature" data-lang="founders_title">Founding Visionaries</h2>
                <div class="section-divider mx-auto mt-6 w-20 h-1 bg-gold rounded-full"></div>
            </div>

            <div class="space-y-16">
                <?php foreach ($founders as $index => $f): ?>
                    <div class="bg-secondary/30 p-8 md:p-12 rounded-[3rem] border border-nature/5 shadow-xl transition-all duration-500 hover:shadow-2xl hover:bg-white group" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="flex flex-col md:flex-row items-center gap-10 md:gap-16">

                            <!-- Circle Image -->
                            <div class="w-48 h-48 md:w-64 md:h-64 flex-shrink-0 relative">
                                <div class="absolute inset-0 bg-gold/10 rounded-full blur-2xl group-hover:bg-saffron/20 transition-all duration-700"></div>
                                <div class="w-full h-full rounded-full overflow-hidden border-4 border-white shadow-2xl relative z-10">
                                    <img src="<?= htmlspecialchars($f['image_path']) ?>"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                        alt="<?= htmlspecialchars($f['name_en']) ?>">
                                </div>
                                <!-- Decorative Badge -->
                                <div class="absolute -bottom-2 right-4 z-20 bg-saffron text-white w-10 h-10 rounded-full flex items-center justify-center shadow-lg border-2 border-white">
                                    <i class="fas fa-certificate text-xs animate-pulse"></i>
                                </div>
                            </div>

                            <!-- Information Content -->
                            <div class="flex-1 text-center md:text-left">
                                <span class="text-saffron font-black uppercase tracking-widest text-[12px] mb-2 block" data-lang="founders_visionary">Chief Visionary</span>
                                <h3 class="text-3xl md:text-4xl font-display text-nature mb-6 italic" data-trans="en"><?= htmlspecialchars($f['name_en']) ?></h3>

                                <div class="w-16 h-1 bg-gold/30 rounded-full mb-8 mx-auto md:mx-0"></div>

                                <blockquote class="text-nature/60 text-lg italic leading-relaxed mb-8 relative">
                                    <i class="fas fa-quote-left text-gold/10 text-6xl absolute -top-10 -left-6"></i>
                                    <span data-trans="en">"<?= htmlspecialchars($f['message_en'] ?: ($f['bio_en'] ? substr($f['bio_en'], 0, 100) . '...' : 'Compassion is the heart of our mission.')) ?>"</span>
                                </blockquote>

                                <div class="text-nature/50 text-12 leading-relaxed max-w-2xl font-light" data-trans="en">
                                    <?= nl2br(htmlspecialchars($f['bio_en'])) ?>
                                </div>

                                <div class="mt-10 flex items-center gap-4 justify-center md:justify-start">
                                    <span class="px-4 py-1.5 bg-nature/5 text-nature/40 rounded-full text-[12px] font-black uppercase tracking-widest border border-nature/5" data-lang="founders_pillar_label">Legacy Pillar</span>
                                    <span class="w-2 h-2 bg-gold rounded-full animate-pulse"></span>
                                    <span class="text-gold text-[12px] font-bold uppercase tracking-widest" data-lang="founders_foundational_spirit">Foundational Spirit</span>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Call to Action -->
<section class="py-32 relative overflow-hidden bg-secondary">
    <div class="container mx-auto px-6 lg:px-24">
        <div class="relative glass-bg rounded-premium shadow-2xl overflow-hidden p-12 md:p-24 text-center border-t-4 border-gold" data-aos="zoom-in">
            <h2 class="text-4xl md:text-6xl font-display text-nature mb-8" data-lang="founders_cta_title">Keep the <span class="italic text-saffron underline decoration-gold/30 underline-offset-8">Vision Alive</span></h2>
            <p class="text-xl text-nature/60 mb-12 max-w-2xl mx-auto leading-relaxed" data-lang="founders_cta_desc">
                Support the sanctuary and carry forward the legacy of our founders. Every contribution makes a difference.
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                <a href="/donate" class="bg-saffron text-white px-12 py-5 rounded-full font-bold text-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 w-full sm:w-auto">Support Our Vision</a>
                <a href="/contact" class="border-2 border-nature text-nature px-12 py-5 rounded-full font-bold text-lg hover:bg-nature hover:text-white transition-all duration-300 w-full sm:w-auto">Get Involved</a>
            </div>
        </div>
    </div>
</section>