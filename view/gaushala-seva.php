<?php
// Database connection is managed by the root index.php router


// Fetch ALL Seva Options
$seva_options = [];
try {
    $seva_options = $pdo->query("SELECT * FROM seva_options WHERE status = 'active' ORDER BY sort_order ASC, id ASC")->fetchAll();
} catch (Exception $e) {
    $seva_options = [];
}
?>

<!-- Hero Header -->
<header class="py-24 bg-nature text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[url('/asset/img/pattern/spirit-mandala.png')] bg-repeat bg-center mix-blend-overlay"></div>
    <div class="container mx-auto px-10 md:px-24 relative z-10 text-center">
        <h1 class="text-5xl md:text-7xl font-display italic text-gold mb-6">Gaushala Seva</h1>
        <p class="text-white/60 tracking-[0.4em] uppercase text-[12px] font-black">Sacred Service Registry</p>
    </div>
</header>

<section id="full-seva-grid" class="py-28 relative overflow-hidden">
    <div class="container mx-auto px-10 md:px-24">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            <?php foreach ($seva_options as $index => $seva):
                $color = $seva['color_class'] ?: 'saffron';
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
                <div class="bg-white p-12 rounded-[4rem] border border-nature/5 shadow-2xl hover:shadow-gold/10 transition-all duration-500 group text-center flex flex-col items-center">
                    <div class="w-20 h-20 <?= $bg_icon_class ?> rounded-full flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-500">
                        <i class="<?= $seva['icon_class'] ?> text-3xl <?= $text_icon_class ?>"></i>
                    </div>
                    <h4 class="text-3xl font-display text-nature mb-6 italic"><?= htmlspecialchars($seva['title_en']) ?></h4>
                    <p class="text-nature/50 text-[15px] mb-10 leading-relaxed italic flex-1"><?= htmlspecialchars($seva['description_en']) ?></p>
                    <a href="/donate" class="w-full bg-nature text-white py-5 rounded-3xl font-black uppercase tracking-widest text-[15px] hover:bg-saffron transition-all shadow-xl shadow-nature/10">Contribute Now</a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-28 text-center border-t border-nature/5 pt-20">
            <a href="/" class="text-nature/40 hover:text-saffron text-[12px] font-black uppercase tracking-widest transition-colors flex items-center justify-center gap-4 group">
                <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-2"></i>
                Return To Sanctuary Home
            </a>
        </div>
    </div>
</section>