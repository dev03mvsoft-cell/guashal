<?php require_once 'config/db.php';

// Fetch leadership
$founder_items = [];
$trustee_items = [];

try {
    $stmt = $pdo->query("SELECT * FROM founders ORDER BY sort_order ASC, id ASC");
    $all = $stmt->fetchAll();
    foreach ($all as $f) {
        $role = trim(strtolower($f['type'] ?? 'trustee'));
        if ($role == 'founder') {
            $founder_items[] = $f;
        } else {
            $trustee_items[] = $f;
        }
    }
} catch (Exception $e) {
}
?>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- COMPACT FOUNDERS PAGE (UI Match) -->
<!-- ═══════════════════════════════════════════════════════════ -->
<section class="pt-48 pb-32 bg-white">
    <div class="container mx-auto px-6">

        <!-- SECTION 1: FOUNDERS -->
        <?php if (!empty($founder_items)): ?>
            <div class="mb-32 text-center">
                <h2 style="font-family: 'Playfair Display', serif;" class="text-3xl md:text-4xl font-bold text-nature uppercase tracking-widest mb-16">Our Founders</h2>

                <div class="flex flex-wrap justify-center gap-10">
                    <?php foreach ($founder_items as $f): ?>
                        <div class="w-64 bg-white rounded-2xl shadow-[0_5px_20px_rgba(0,0,0,0.08)] overflow-hidden border border-gray-100 group transition-all hover:-translate-y-2" data-aos="fade-up">
                            <!-- Image Frame -->
                            <div class="w-full aspect-square overflow-hidden bg-gray-50 border-b border-gray-100">
                                <img src="<?= htmlspecialchars($f['image_path'] ?: '/asset/img/donors/default_donor.png') ?>"
                                    class="w-full h-full object-cover">
                            </div>
                            <!-- Simple Content -->
                            <div class="p-6 text-center">
                                <h3 class="text-nature font-black text-[16px] uppercase tracking-wider mb-2 leading-tight">
                                    <?= htmlspecialchars($f['name_en']) ?>
                                </h3>
                                <p class="text-[#c0a50e] font-bold text-[10px] uppercase tracking-[0.2em]">
                                    <?= htmlspecialchars($f['designation_en'] ?: 'Founding Member') ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- SECTION 2: TRUSTEES -->
        <?php if (!empty($trustee_items)): ?>
            <div class="text-center">
                <h2 style="font-family: 'Playfair Display', serif;" class="text-3xl md:text-4xl font-bold text-nature uppercase tracking-widest mb-16">Our Trustees</h2>

                <div class="flex flex-wrap justify-center gap-10">
                    <?php foreach ($trustee_items as $t): ?>
                        <div class="w-64 bg-white rounded-2xl shadow-[0_5px_20px_rgba(0,0,0,0.08)] overflow-hidden border border-gray-100 group transition-all hover:-translate-y-2" data-aos="fade-up">
                            <div class="w-full aspect-square overflow-hidden bg-gray-50 border-b border-gray-100">
                                <img src="<?= htmlspecialchars($t['image_path'] ?: '/asset/img/donors/default_donor.png') ?>"
                                    class="w-full h-full object-cover">
                            </div>
                            <div class="p-6 text-center">
                                <h4 class="text-nature font-black text-[16px] uppercase tracking-wider mb-2 leading-tight">
                                    <?= htmlspecialchars($t['name_en']) ?>
                                </h4>
                                <p class="text-[#c0a50e] font-bold text-[10px] uppercase tracking-[0.2em]">
                                    <?= htmlspecialchars($t['designation_en'] ?: 'Trustee Member') ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>
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