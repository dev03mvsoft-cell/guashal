<?php
require_once 'config/db.php';

// Fetch all active announcements
$announcements = [];
try {
    $stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC");
    if ($stmt) {
        $announcements = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $announcements = [];
}
?>

<!-- 🌅 THE DIVINE BULLETIN HERO 🌅 -->
<section class="relative h-[60vh] md:h-[70vh] flex items-center justify-center overflow-hidden bg-nature">
    <!-- Celestial Patterns -->
    <div class="absolute inset-0 opacity-10 pointer-events-none mix-blend-overlay">
        <img src="/asset/img/cow/bgofthecow.jpg" class="w-full h-full object-cover">
    </div>
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-saffron/10 rounded-full blur-[100px] -mr-48 -mt-48"></div>
    <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-gold/5 rounded-full blur-[80px] -ml-24 -mb-24"></div>

    <div class="container mx-auto px-6 relative z-10 text-center pt-40 pb-24 md:pt-48 md:pb-32">
        <span class="text-gold font-black uppercase tracking-[0.6em] text-[12px] mb-6 block" data-aos="fade-down" data-lang="latest_label">Latest News</span>
        <h1 class="text-5xl md:text-8xl font-display font-bold text-white mb-8 leading-none" data-aos="fade-up" data-lang="nav_announcements">
            Announcements
        </h1>
        <p class="text-white/60 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed italic" data-aos="fade-up" data-aos-delay="100" data-trans="en">
            Stay connected with the latest revelations, service updates, and spiritual news from our sacred sanctuary.
        </p>
    </div>
</section>

<!-- 📜 ANNOUNCEMENT REVELATIONS 📜 -->
<section class="relative py-24 bg-secondary overflow-hidden">
    <!-- Decorative Glows -->
    <div class="absolute top-0 translate-y-[-50%] left-1/2 translate-x-[-50%] w-[1000px] h-[600px] bg-nature/5 blur-[120px] pointer-events-none"></div>

    <div class="container mx-auto px-6 relative z-10">
        <?php if (!empty($announcements)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <?php foreach ($announcements as $idx => $item): ?>
                    <div class="group h-full" data-aos="fade-up" data-aos-delay="<?= $idx * 100 ?>">
                        <div class="bg-white/70 backdrop-blur-2xl rounded-[3.5rem] p-10 border border-white shadow-xl hover:-translate-y-4 hover:shadow-2xl transition-all duration-700 h-full flex flex-col relative overflow-hidden group">

                            <!-- Premium Background Texture -->
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-[0.03] pointer-events-none transition-opacity duration-700">
                                <!-- Pattern removed due to missing resource -->
                            </div>

                            <!-- Date Badge -->
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-14 h-14 rounded-2xl bg-saffron/10 flex items-center justify-center text-saffron text-xl shadow-inner shadow-saffron/5">
                                    <i class="fas fa-bullhorn text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-[12px] uppercase font-black text-gray-300 leading-none mb-1">Revealed On</p>
                                    <p class="text-xs font-bold text-nature"><?= date('M d, Y', strtotime($item['created_at'])) ?></p>
                                </div>
                            </div>

                            <!-- Message Content -->
                            <div class="flex-1">
                                <p class="text-gray-500 text-lg leading-relaxed font-sans" data-trans="en"><?= nl2br(htmlspecialchars($item['message_en'])) ?></p>
                            </div>

                            <!-- Decorative Footer Dot -->
                            <div class="mt-10 pt-6 border-t border-gray-50 flex items-center justify-between">
                                <span class="w-2 h-2 bg-gold/40 rounded-full"></span>
                                <span class="text-[12px] font-bold uppercase tracking-[0.3em] text-nature/20">Gaushala Chronicles</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="max-w-4xl mx-auto bg-white/50 backdrop-blur-xl border border-gold/10 p-24 rounded-[4.5rem] text-center shadow-2xl" data-aos="zoom-in">
                <div class="w-24 h-24 rounded-full bg-gold/5 flex items-center justify-center mx-auto mb-10 text-gold/30">
                    <i class="fas fa-dove text-4xl"></i>
                </div>
                <h3 class="text-3xl font-display font-bold text-nature" data-trans="en">Peace & Quiet</h3>
                <p class="text-gray-400 text-sm mt-6 leading-relaxed italic" data-trans="en">
                    The sanctuary is currently in a state of deep reflection. <br>
                    Please return later for new celestial updates.
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
    .font-display {
        font-family: 'Playfair Display', serif;
    }
</style>