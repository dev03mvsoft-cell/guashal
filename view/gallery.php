<?php
require_once 'config/db.php';

// Fetch Categories & Items
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM gallery");
$categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);

$filter = $_GET['cat'] ?? 'all';
if ($filter !== 'all') {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE category = ? ORDER BY created_at DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
}
$items = $stmt->fetchAll();


?>

<style>
    .gallery-title {
        font-family: 'Playfair Display', serif;
    }

    .gallery-card {
        transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        border-radius: 2.5rem;
        overflow: hidden;
        height: 400px;
        cursor: pointer;
        position: relative;
    }

    .gallery-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 1s ease;
    }

    .gallery-card:hover img {
        transform: scale(1.1);
        filter: brightness(0.6);
    }

    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 3rem;
        background: linear-gradient(0deg, rgba(44, 76, 59, 0.95) 0%, rgba(44, 76, 59, 0) 100%);
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.4s;
        color: white;
    }

    .gallery-card:hover .gallery-overlay {
        opacity: 1;
        transform: translateY(0);
    }

    .filter-tab {
        padding: 0.8rem 2rem;
        border-radius: 100px;
        font-weight: 700;
        transition: all 0.3s;
        border: 1px solid #eee;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 12px;
    }

    .filter-tab.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        box-shadow: 0 15px 30px rgba(255, 106, 0, 0.2);
    }

    /* Modal Styles */
    #galleryModal {
        z-index: 99999;
    }

    .modal-nav-btn {
        backdrop-filter: blur(10px);
    }

    @media (max-width: 768px) {
        .gallery-card {
            height: 300px;
            border-radius: 2rem;
        }
        
        .gallery-overlay {
            padding: 2rem;
        }

        .modal-nav-btn {
            width: 3rem !important;
            height: 3rem !important;
        }
    }
</style>

<!-- Hero Section (Classic Premium) -->
<section class="relative  bg-nature overflow-hidden">
    <div class="absolute inset-0 opacity-5 pointer-events-none">
        <img src="/asset/img/cow/bgofthecow.jpg" class="w-full h-full object-cover">
    </div>
    <div class="container mx-auto px-6 relative z-10 text-center pt-40 pb-24 md:pt-48 md:pb-32">
        <span class="text-gold uppercase tracking-[0.5em] text-sm font-bold mb-4 block drop-shadow-lg" data-lang="gallery_hero_span">A Visual Journey of Devotion</span>
        <h1 class="gallery-title text-5xl md:text-7xl font-bold text-white mb-6" data-aos="fade-up" data-lang="gallery_hero_h1">Our <span class="italic text-gold">Sacred</span> Chronicles</h1>
        <p class="text-white/60 text-lg max-w-2xl mx-auto leading-relaxed" data-aos="fade-up" data-aos-delay="100" data-lang="gallery_section_title">
            Glimpses of <span class="italic text-gold">Divine</span> Peace
        </p>
    </div>
</section>

<!-- Gallery Filter & Grid -->
<section class="py-24 bg-[#fffcf9]">
    <div class="container mx-auto px-6">

        <!-- Classic Filters -->


        <!-- High-End Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($items)): ?>
                <div class="col-span-full py-20 text-center">
                    <i class="fas fa-camera text-gray-100 text-7xl mb-6"></i>
                    <h3 class="text-gray-300 font-bold uppercase tracking-widest">No Memories Indexed Here</h3>
                </div>
            <?php else: ?>
                <?php foreach ($items as $idx => $item): ?>
                    <div class="gallery-card group shadow-2xl shadow-nature/5 border border-white/40"
                        data-aos="fade-up"
                        data-aos-delay="<?= ($idx % 3) * 100 ?>"
                        onclick="openGalleryModal(<?= $idx ?>)">
                        <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="Visual Frame">
                        <div class="gallery-overlay">
                            <span class="text-[12px] uppercase tracking-[0.3em] font-black text-gold mb-3 block opacity-70" data-trans="en"><?= htmlspecialchars($item['category']) ?></span>
                            <h4 class="text-2xl font-bold leading-tight" data-trans="en"><?= htmlspecialchars($item['title_en'] ?: 'Serene Moment') ?></h4>
                            <div class="mt-6 flex items-center justify-between border-t border-white/20 pt-4">
                                <span class="text-[12px] uppercase tracking-widest text-white/50"><?= date('M d, Y', strtotime($item['created_at'])) ?></span>
                                <i class="fas fa-expand text-xs opacity-50"></i>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 🖼️ PREMIUM GALLERY MODAL 🖼️ -->
<div id="galleryModal" class="fixed inset-0 hidden flex items-center justify-center bg-nature/95 backdrop-blur-2xl transition-all duration-500">
    <!-- Close Backdrop -->
    <div class="absolute inset-0 z-0" onclick="closeGalleryModal()"></div>

    <!-- Close Button -->
    <button onclick="closeGalleryModal()" class="absolute top-6 right-6 md:top-10 md:right-10 text-white/50 hover:text-white transition-all z-20 group">
        <div class="w-12 h-12 md:w-16 md:h-16 rounded-full border border-white/10 flex items-center justify-center group-hover:bg-white/10">
            <i class="fas fa-times text-xl md:text-2xl"></i>
        </div>
    </button>
    
    <!-- Navigation Buttons -->
    <button onclick="prevGalleryImage()" class="modal-nav-btn absolute left-4 md:left-10 top-1/2 -translate-y-1/2 w-12 h-12 md:w-20 md:h-20 rounded-full bg-white/5 border border-white/10 hover:bg-gold hover:text-nature transition-all flex items-center justify-center text-white z-20 shadow-2xl">
        <i class="fas fa-chevron-left text-xl"></i>
    </button>
    
    <button onclick="nextGalleryImage()" class="modal-nav-btn absolute right-4 md:right-10 top-1/2 -translate-y-1/2 w-12 h-12 md:w-20 md:h-20 rounded-full bg-white/5 border border-white/10 hover:bg-gold hover:text-nature transition-all flex items-center justify-center text-white z-20 shadow-2xl">
        <i class="fas fa-chevron-right text-xl"></i>
    </button>

    <!-- Image Container -->
    <div class="relative max-w-[95vw] lg:max-w-[85vw] max-h-[75vh] md:max-h-[80vh] z-10 flex flex-col items-center">
        <div class="relative overflow-hidden rounded-3xl shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] border border-white/10">
            <img id="modalImage" src="" class="w-full h-auto max-h-[70vh] object-contain transition-all duration-500 transform scale-100">
        </div>
        
        <!-- Info Overlay -->
        <div class="mt-8 md:mt-12 text-center px-6">
            <span id="modalCategory" class="text-gold uppercase tracking-[0.4em] text-[10px] md:text-xs font-black mb-3 block opacity-60"></span>
            <h3 id="modalTitle" class="text-white text-2xl md:text-4xl font-display italic leading-tight"></h3>
            <div class="w-12 h-1 bg-gold/30 mx-auto mt-6 rounded-full"></div>
        </div>
    </div>
</div>

<script>
    let currentGalleryIdx = 0;
    const galleryItems = <?= json_encode($items) ?>;

    function openGalleryModal(idx) {
        currentGalleryIdx = idx;
        updateModalContent();
        const modal = document.getElementById('galleryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        // Anim entry
        setTimeout(() => {
            document.getElementById('modalImage').classList.add('scale-100');
        }, 50);
    }

    function closeGalleryModal() {
        const modal = document.getElementById('galleryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function updateModalContent() {
        const item = galleryItems[currentGalleryIdx];
        const img = document.getElementById('modalImage');
        const title = document.getElementById('modalTitle');
        const cat = document.getElementById('modalCategory');
        
        // Smooth transition
        img.style.opacity = '0';
        img.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            img.src = item.image_path;
            title.innerText = item.title_en || 'Serene Moment';
            cat.innerText = item.category;
            img.style.opacity = '1';
            img.style.transform = 'scale(1)';
        }, 200);
    }

    function nextGalleryImage() {
        currentGalleryIdx = (currentGalleryIdx + 1) % galleryItems.length;
        updateModalContent();
    }

    function prevGalleryImage() {
        currentGalleryIdx = (currentGalleryIdx - 1 + galleryItems.length) % galleryItems.length;
        updateModalContent();
    }

    // Keyboard support
    document.addEventListener('keydown', (e) => {
        const modal = document.getElementById('galleryModal');
        if (modal.classList.contains('hidden')) return;
        
        if (e.key === 'ArrowRight') nextGalleryImage();
        if (e.key === 'ArrowLeft') prevGalleryImage();
        if (e.key === 'Escape') closeGalleryModal();
    });
</script>