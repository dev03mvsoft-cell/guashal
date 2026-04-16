<?php
// Fetch all donors for the full Hall of Fame
$all_donors = [];
try {
    $all_donors = $pdo->query("SELECT * FROM donors WHERE is_visible = 1 ORDER BY donation_date DESC")->fetchAll();
} catch (Exception $e) {
    $all_donors = [];
}
?>

<!-- Hero Section -->
<section class="relative h-[60vh] md:h-[70vh] flex items-center justify-center overflow-hidden">
    <!-- Background Image with Ken Burns Effect -->
    <div class="absolute inset-0 z-0">
        <img src="/asset/img/cow/bgofthecow.jpg" class="w-full h-full object-cover kenburns-bg" alt="Donors Hall of Fame">
        <div class="absolute inset-0 bg-gradient-to-b from-nature/80 via-nature/40 to-nature/90"></div>
    </div>

    <!-- Content -->
    <div class="container mx-auto px-6 relative z-10 text-center pt-24" data-aos="zoom-out">
        <span class="text-gold uppercase tracking-[0.5em] text-[12px] md:text-sm font-bold mb-4 block drop-shadow-lg" data-lang="donors_hero_label">The Sacred Hall of Fame</span>
        <h1 class="text-4xl md:text-8xl font-display text-white mb-6 leading-tight drop-shadow-2xl" data-lang="donors_hero_title">
            Our <span class="italic text-gold underline decoration-gold/20 underline-offset-[16px]">Noble</span> Donors
        </h1>
        <p class="text-white/70 text-lg md:text-xl font-display italic max-w-2xl mx-auto mb-10" data-lang="donors_hero_desc">
            "Serving the Gaia is serving the Divine." We honor the souls whose compassion sustains our sanctuary.
        </p>
        <div class="section-divider mx-auto w-32 h-1.5 rounded-full bg-gradient-to-r from-saffron to-gold shadow-lg"></div>
    </div>
</section>

<!-- Donors Grid Section -->
<section class="py-24 bg-white relative overflow-hidden">
    <!-- Subtle Pattern Overlay -->
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none">
        <img src="/asset/img/pattern/footer-pattern.png" class="w-full h-full object-cover">
    </div>

    <div class="container mx-auto px-6 lg:px-24 relative z-10">
        
        <!-- Premium Search & Filter Bar -->
        <div class="mb-12" data-aos="fade-up">
            <div class="max-w-3xl mx-auto relative group">
                <div class="absolute inset-y-0 left-8 flex items-center pointer-events-none text-nature/30 group-focus-within:text-saffron transition-colors">
                    <i class="fas fa-search text-xl"></i>
                </div>
                <input type="text" 
                       id="donorSearch" 
                       placeholder="Search noble donors by name or purpose..." 
                       class="w-full bg-white border-2 border-nature/5 focus:border-saffron/30 rounded-[2.5rem] py-8 pl-20 pr-10 text-nature font-display text-xl focus:outline-none shadow-xl shadow-nature/5 transition-all placeholder:text-nature/20 placeholder:italic">
                
                <!-- Search Decoration -->
                <div class="absolute right-6 top-1/2 -translate-y-1/2 flex items-center gap-3">
                    <span class="text-[10px] font-black uppercase tracking-widest text-nature/20 hidden md:block">Instant Filter</span>
                    <div class="w-10 h-10 bg-nature/5 rounded-full flex items-center justify-center text-nature/20">
                        <i class="fas fa-filter text-xs"></i>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($all_donors)): ?>
            <div class="text-center py-20 bg-secondary/30 rounded-[3rem] border-2 border-dashed border-nature/10" data-aos="fade-up">
                <i class="fas fa-heart text-6xl text-nature/10 mb-6 border-gold"></i>
                <h3 class="text-2xl font-display text-nature">Our family is growing</h3>
                <p class="text-nature/40 mt-2 italic">Be the first to join our Sacred Hall of Fame.</p>
                <a href="/donate" class="inline-block mt-8 bg-saffron text-white px-10 py-4 rounded-full font-bold uppercase tracking-widest text-xs hover:shadow-xl transition-all">Support Now</a>
            </div>
        <?php else: ?>
            <!-- Optimized Premium Table-List -->
            <div class="bg-white rounded-[3rem] shadow-2xl shadow-nature/5 border border-nature/5 overflow-hidden">
                <!-- Header -->
                <div class="hidden md:grid grid-cols-12 items-center px-12 py-6 bg-nature/5 border-b border-nature/10 text-nature/60 text-[11px] font-bold uppercase tracking-widest">
                    <div class="col-span-1">Profile</div>
                    <div class="col-span-4 ml-4">Donor Name & Purpose</div>
                    <div class="col-span-3 text-center">Donation Details</div>
                    <div class="col-span-4 text-right pr-4">Total Amount</div>
                </div>

                <!-- Body -->
                <div class="divide-y divide-nature/10" id="donorsList">
                    <?php foreach ($all_donors as $index => $donor): ?>
                        <div class="donor-row group relative grid grid-cols-1 md:grid-cols-12 items-center px-8 py-5 md:px-12 md:py-6 hover:bg-nature/[0.02] transition-colors duration-200" 
                             data-aos="fade-up" 
                             data-aos-delay="<?= ($index % 10) * 30 ?>"
                             data-search="<?= strtolower(htmlspecialchars($donor['name'] . ' ' . $donor['purpose'])) ?>">
                            
                            <!-- Column 1: Profile -->
                            <div class="col-span-1 flex justify-center md:justify-start">
                                <div class="w-12 h-12 rounded-xl bg-nature/5 overflow-hidden ring-1 ring-nature/10">
                                    <img src="/asset/img/donors/<?= $donor['profile_pic'] ?>" 
                                         onerror="this.src='/asset/img/donors/default_donor.png'"
                                         class="w-full h-full object-cover">
                                </div>
                            </div>

                            <!-- Column 2: Name & Purpose -->
                            <div class="col-span-4 md:ml-4 text-center md:text-left">
                                <h4 class="text-lg font-bold text-nature leading-tight">
                                    <?= htmlspecialchars($donor['name'] ?: 'Noble Donor') ?>
                                </h4>
                                <p class="text-nature/60 text-xs mt-1 font-medium">
                                    <?= htmlspecialchars($donor['purpose']) ?>
                                </p>
                            </div>

                            <!-- Column 3: Date & Meta -->
                            <div class="col-span-3 flex flex-col items-center justify-center text-center">
                                <span class="text-nature/80 font-bold text-sm"><?= date('d M, Y', strtotime($donor['donation_date'])) ?></span>
                                <span class="text-[9px] font-bold uppercase tracking-widest text-nature/30 mt-1">Verified Offering</span>
                            </div>

                            <!-- Column 4: Amount -->
                            <div class="col-span-4 text-center md:text-right md:pr-4">
                                <div class="inline-flex items-center gap-2">
                                    <span class="text-[10px] font-black text-saffron uppercase tracking-widest bg-saffron/5 px-2 py-0.5 rounded">INR</span>
                                    <span class="text-2xl font-bold text-nature">
                                        <?= number_format($donor['amount']) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Row Interaction Decoration (The BG Text Effect) -->
                            <div class="absolute right-10 top-1/2 -translate-y-1/2 text-[100px] text-nature/[0.01] pointer-events-none group-hover:text-nature/[0.04] transition-all duration-700 font-bold uppercase select-none hidden lg:block tracking-tighter">
                                GRATITUDE
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Table Footer Decor -->
                <div class="px-12 py-10 bg-nature/[0.03] text-center border-t border-nature/10">
                    <p class="text-nature/40 text-[11px] font-black uppercase tracking-[0.6em]">Sacred Contributions for Perpetual Protection of Gaia</p>
                </div>
            </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('donorSearch');
    const donorRows = document.querySelectorAll('.donor-row');
    const noResults = document.createElement('div');
    
    // Create "No Results" element
    noResults.id = 'noResultsMessage';
    noResults.className = 'hidden py-20 text-center bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200';
    noResults.innerHTML = `
        <div class="flex flex-col items-center">
            <i class="fas fa-search text-4xl text-gray-200 mb-4"></i>
            <p class="text-gray-400 font-display italic">No donors found matching your search...</p>
        </div>
    `;
    
    const donorsContainer = document.querySelector('.divide-y');
    donorsContainer.appendChild(noResults);

    searchInput.addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase().trim();
        let visibleCount = 0;

        donorRows.forEach(row => {
            const searchText = row.getAttribute('data-search');
            if (searchText.includes(term)) {
                row.classList.remove('hidden');
                row.classList.add('grid');
                visibleCount++;
            } else {
                row.classList.remove('grid');
                row.classList.add('hidden');
            }
        });

        // Show/Hide "No Results" message
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    });
});
</script>

<!-- Call to Action -->
<section class="py-32 relative overflow-hidden bg-white">
    <div class="container mx-auto px-6 lg:px-24">
        <div class="relative rounded-[4rem] shadow-premium overflow-hidden p-12 md:p-24 text-center group" data-aos="zoom-in">
            <!-- Background Interaction -->
            <div class="absolute inset-0 z-0">
                <img src="/asset/img/cow/gushala18.jpg" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[8s]" alt="Sacred Mission Card">
                <div class="absolute inset-0 bg-nature/90 mix-blend-multiply opacity-90"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-nature via-nature/40 to-saffron/20 opacity-60"></div>
            </div>

            <div class="relative z-10">
                <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center mb-10 mx-auto text-gold border border-white/20">
                    <i class="fas fa-om text-4xl animate-pulse"></i>
                </div>
                <h2 class="text-4xl md:text-7xl font-display text-white mb-8 leading-tight">
                    Join Our <span class="italic text-gold underline decoration-gold/30 underline-offset-8">Divine Circle</span>
                </h2>
                <p class="text-xl text-white/70 mb-12 max-w-2xl mx-auto italic font-light">
                    Your contribution ensures the dignity and protection of our sacred Gaia. Become a guardian of the divine today.
                </p>
                <a href="/donate" class="bg-saffron text-white px-16 py-6 rounded-full font-bold text-lg hover:shadow-[0_20px_60px_rgba(255,106,0,0.4)] transition-all uppercase tracking-[0.3em] inline-block">
                    Become a Donor
                </a>
            </div>
        </div>
    </div>
</section>
