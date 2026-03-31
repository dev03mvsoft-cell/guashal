<!-- 🌅 CELESTIAL HERO 🌅 -->
<section class="relative h-[60vh] md:h-[70vh] flex items-center justify-center overflow-hidden bg-nature">
    <!-- Divine Patterns -->
    <div class="absolute inset-0 opacity-10 pointer-events-none mix-blend-overlay">
        <img src="/asset/img/cow/bgofthecow.jpg" class="w-full h-full object-cover">
    </div>
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-saffron/10 rounded-full blur-[100px] -mr-48 -mt-48"></div>
    <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-gold/5 rounded-full blur-[80px] -ml-24 -mb-24"></div>

    <div class="container mx-auto px-6 relative z-10 text-center pt-40 pb-24 md:pt-48 md:pb-32">
        <span class="text-gold font-black uppercase tracking-[0.6em] text-[12px] mb-6 block" data-aos="fade-down" data-lang="events_hero_span">Spiritual Gatherings & Events</span>
        <h1 class="text-5xl md:text-8xl font-display font-bold text-white mb-8 leading-none" data-aos="fade-up" data-lang="events_hero_h1">
            Our <span class="italic text-gold">Sacred</span> Calendar
        </h1>
        <p class="text-white/60 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed italic" data-aos="fade-up" data-aos-delay="100" data-lang="events_section_title">
            Join Our <span class="italic text-gold">Spiritual</span> Shivir
        </p>
    </div>
</section>

<!-- 🕊️ MAIN REVELATION 🕊️ -->
<section class="relative py-24 overflow-hidden bg-secondary">
    <div class="container mx-auto px-6 relative z-10">

        <?php
        $events = [];
        try {
            $stmt = $pdo->query("SELECT * FROM events ORDER BY start_date DESC");
            if ($stmt) $events = $stmt->fetchAll();
        } catch (Exception $e) {
            $events = [];
        }

        if (!empty($events)):
            $featured = $events[0];
            $others = array_slice($events, 1);

            $is_past_feat = strtotime($featured['end_date'] ?: $featured['start_date']) < time();
            $is_live_feat = !$is_past_feat && strtotime($featured['start_date']) <= time();
        ?>

            <!-- 🌟 FEATURED SPOTLIGHT 🌟 -->
            <div class="mb-24" data-aos="fade-up">
                <div class="relative bg-white rounded-[4.5rem] overflow-hidden shadow-2xl shadow-nature/10 border border-gold/10 group flex flex-col lg:flex-row lg:h-[650px]">
                    <!-- Image Half (Static/Fixed Height) -->
                    <div class="lg:w-1/3 relative h-[400px] lg:h-full overflow-hidden bg-nature/5">
                        <?php if ($featured['image_path']): ?>
                            <img src="<?= htmlspecialchars($featured['image_path']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-[2s]" alt="Featured Event">
                            <!-- Premium Scrim -->
                            <div class="absolute inset-0 bg-gradient-to-t from-nature/40 via-transparent to-transparent opacity-60"></div>
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center relative">
                                <i class="fas fa-om text-nature/10 text-9xl transform -rotate-12"></i>
                            </div>
                        <?php endif; ?>

                        <div class="absolute top-10 left-10">
                            <div class="bg-nature/60 backdrop-blur-xl text-gold text-[12px] font-black uppercase tracking-widest px-8 py-3 rounded-full border border-gold/40 shadow-2xl flex items-center gap-3">
                                <span class="w-2 h-2 bg-gold rounded-full animate-pulse"></span>
                                Featured Moment
                            </div>
                        </div>
                    </div>

                    <!-- Content Half (Independently Scrollable) -->
                    <div class=" flex flex-col h-full overflow-hidden">
                        <div class="flex-1 p-12 lg:p-20 overflow-y-auto custom-scrollbar">
                            <div class="flex items-center gap-6 mb-8">
                                <div class="bg-saffron/10 text-saffron px-6 py-2.5 rounded-2xl text-[12px] font-black uppercase tracking-widest">
                                    <?= date('M d, Y', strtotime($featured['start_date'])) ?>
                                </div>
                                <?php if ($is_live_feat): ?>
                                    <span class="flex items-center gap-2 text-[12px] font-black uppercase tracking-widest text-green-500 font-black"><span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span> Live Now</span>
                                <?php endif; ?>
                            </div>

                            <h3 class="text-4xl lg:text-5xl font-display font-bold text-nature mb-8 leading-tight" data-trans="en"><?= htmlspecialchars($featured['title']) ?></h3>
                            <div class="prose prose-sm text-gray-400 max-w-none mb-10">
                                <p class="text-lg leading-relaxed italic" data-trans="en"><?= nl2br(htmlspecialchars($featured['description'] ?: 'Join us for a spiritual gathering of peace and service.')) ?></p>
                            </div>

                            <?php if ($featured['organizers']): ?>
                                <div class="mb-6">
                                    <p class="text-[12px] uppercase font-black text-gray-300 leading-none mb-6 tracking-widest">Visionaries behind this event</p>
                                    <div class="flex flex-wrap gap-3">
                                        <?php foreach (explode(', ', $featured['organizers']) as $org): ?>
                                            <span class="bg-saffron/5 text-saffron px-6 py-2.5 rounded-full text-[12px] font-black uppercase tracking-tighter border border-saffron/10 hover:bg-saffron hover:text-white transition-all cursor-default shadow-sm"><?= htmlspecialchars($org) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Sticky Footer Actions -->
                        <div class="p-10 lg:px-20 lg:py-10 border-t border-gray-100 bg-white/50 backdrop-blur-sm mt-auto">
                            <div class="flex flex-wrap items-center gap-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gold/5 flex items-center justify-center text-saffron">
                                        <i class="fas fa-map-marker-alt text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-[15px] uppercase font-black text-gray-300 leading-none mb-1">Venue</p>
                                        <p class="text-xs font-bold text-nature" data-trans="en"><?= htmlspecialchars($featured['location'] ?: 'Gaushala Sanctuary') ?></p>
                                    </div>
                                </div>

                                <a href="/contact" class="bg-nature text-white px-12 py-5 rounded-3xl font-black uppercase tracking-widest text-[15px] hover:bg-saffron hover:shadow-2xl transition-all duration-500 flex items-center gap-3 ml-auto">
                                    <span data-trans="en">Inquire Now</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 📅 EVENTS GRID 📅 -->
            <?php if (!empty($others)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <?php foreach ($others as $idx => $event):
                        $is_past = strtotime($event['end_date'] ?: $event['start_date']) < time();
                    ?>
                        <div onclick="openEventModal(<?= htmlspecialchars(json_encode($event)) ?>)" class="group cursor-pointer" data-aos="fade-up" data-aos-delay="<?= $idx * 100 ?>">
                            <div class="bg-white/60 backdrop-blur-xl rounded-[4rem] p-5 border border-white shadow-xl hover:-translate-y-4 hover:shadow-2xl transition-all duration-700 h-full flex flex-col">
                                <div class="relative h-72 rounded-[3rem] overflow-hidden mb-8">
                                    <?php if ($event['image_path']): ?>
                                        <img src="<?= htmlspecialchars($event['image_path']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-[1.5s]">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-nature/5 flex items-center justify-center"><i class="fas fa-calendar-alt text-nature/10 text-6xl"></i></div>
                                    <?php endif; ?>

                                    <div class="absolute top-5 left-5 bg-white/95 backdrop-blur px-6 py-4 rounded-[2rem] shadow-xl text-center min-w-[70px]">
                                        <p class="text-[12px] uppercase font-black text-gray-400 leading-none mb-1"><?= date('M', strtotime($event['start_date'])) ?></p>
                                        <p class="text-3xl font-display font-bold text-nature leading-none"><?= date('d', strtotime($event['start_date'])) ?></p>
                                    </div>
                                </div>

                                <div class="px-6 pb-8 flex-1 flex flex-col">
                                    <h4 class="text-2xl font-bold text-nature mb-4 line-clamp-1 group-hover:text-saffron transition-colors" data-trans="en"><?= htmlspecialchars($event['title']) ?></h4>
                                    <p class="text-gray-400 text-[15px] leading-relaxed mb-10 line-clamp-2" data-trans="en"><?= htmlspecialchars($event['description'] ?: 'Sacred encounter with divine service...') ?></p>

                                    <div class="mt-auto flex items-center justify-between pt-6 border-t border-gray-50 uppercase text-[15px] font-black tracking-widest text-nature/40 group-hover:text-saffron transition-colors">
                                        <span data-trans="en">Explore Details</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="bg-white/50 backdrop-blur-xl border border-gold/10 p-24 rounded-[4.5rem] text-center shadow-2xl">
                <i class="fas fa-clock text-7xl text-gold/20 mb-10"></i>
                <h3 class="text-2xl font-bold text-nature" data-trans="en">Archive is Being Restored</h3>
                <p class="text-gray-400 text-sm mt-4" data-trans="en">Celestial moments are being recorded. Please return shortly.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- 🖼️ THE DIVINE MODAL 🖼️ -->
<div id="eventModal" class="fixed inset-0 z-[99999] hidden flex items-center justify-center p-6 lg:p-12">
    <!-- Click backdrop to close -->
    <div onclick="closeEventModal()" class="absolute inset-0 bg-nature/90 backdrop-blur-2xl transition-all duration-700"></div>

    <!-- Floating Close Button (Always Visible) -->
    <button onclick="closeEventModal()" class="fixed top-8 right-8 md:top-12 md:right-12 w-14 h-14 md:w-16 md:h-16 rounded-full bg-saffron text-white shadow-2xl hover:scale-110 active:scale-90 transition-all z-[100000] flex items-center justify-center text-xl cursor-pointer border-4 border-white/20">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <div class="relative bg-white w-full max-w-6xl rounded-[3rem] md:rounded-[4.5rem] overflow-hidden shadow-2xl transform transition-all duration-500 scale-95 opacity-0 translate-y-20 z-10" id="modalContainer">
        <div class="flex flex-col lg:flex-row max-h-[85vh]">
            <div class="lg:w-1/2 relative bg-nature overflow-hidden min-h-[400px]">
                <img id="modalImage" src="" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-nature/90 via-transparent to-transparent"></div>
            </div>

            <div class="lg:w-1/2 p-12 lg:p-24 overflow-y-auto">
                <div class="flex items-center gap-6 mb-8">
                    <span id="modalDate" class="bg-saffron text-white px-7 py-3 rounded-2xl text-[12px] font-black uppercase tracking-widest shadow-xl shadow-saffron/20"></span>
                    <span id="modalLocTag" class="text-[12px] font-black uppercase tracking-widest text-gray-400 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-saffron"></i>
                        <span id="modalLocSmall" data-trans="en"></span>
                    </span>
                </div>

                <h3 id="modalTitle" class="text-5xl font-display font-bold text-nature mb-10 leading-tight" data-trans="en"></h3>

                <div class="prose prose-sm text-gray-500 max-w-none mb-12">
                    <p id="modalDesc" class="text-lg leading-relaxed italic" data-trans="en"></p>
                </div>

                <div class="mb-16">
                    <p class="text-[12px] uppercase font-black text-gray-300 leading-none mb-4 tracking-widest">Visionaries behind this event</p>
                    <div id="modalOrg" class="flex flex-wrap gap-3"></div>
                </div>

                <div class="flex flex-wrap items-center gap-10 pt-10 border-t border-gray-100 mt-auto">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gold/5 flex items-center justify-center text-saffron">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <p class="text-[12px] uppercase font-black text-gray-300 leading-none mb-1">Venue</p>
                            <p id="modalLocSmall" class="text-[12px] font-bold text-nature" data-trans="en"></p>
                        </div>
                    </div>
                    <a href="/contact" class="bg-nature text-white px-10 py-5 rounded-3xl font-black uppercase tracking-widest text-[15px] hover:bg-saffron hover:shadow-2xl transition-all duration-500 flex items-center gap-3 ml-auto">
                        <span data-trans="en">Inquire Now</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openEventModal(event) {
        const modal = document.getElementById('eventModal');
        const container = document.getElementById('modalContainer');

        // Fill Content
        document.getElementById('modalTitle').innerText = event.title;
        document.getElementById('modalDesc').innerText = event.description || 'Join us for a spiritual gathering of peace and service.';
        document.getElementById('modalLocSmall').innerText = event.location || 'Gaushala Main Campus';

        // Multi-Organizer Tag Generation
        const orgContainer = document.getElementById('modalOrg');
        orgContainer.innerHTML = '';
        const orgs = (event.organizers || 'Gaushala Sewa Trust').split(', ');
        orgs.forEach(org => {
            const tag = document.createElement('span');
            tag.className = 'bg-saffron/5 text-saffron px-5 py-2 rounded-full text-[12px] font-black uppercase tracking-tighter border border-saffron/10 cursor-default shadow-sm';
            tag.innerText = org;
            orgContainer.appendChild(tag);
        });

        document.getElementById('modalImage').src = event.image_path || '/asset/img/events/default.jpg';
        document.getElementById('modalDate').innerText = new Date(event.start_date).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });

        // Auto-Translate Trigger
        if (localStorage.getItem('site_lang') && localStorage.getItem('site_lang') !== 'en') {
            translateAllDynamicContent(localStorage.getItem('site_lang'));
        }

        // Modal Animation
        modal.classList.remove('hidden');
        setTimeout(() => {
            container.classList.remove('opacity-0', 'scale-95', 'translate-y-20');
            container.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 50);
    }

    function closeEventModal() {
        const modal = document.getElementById('eventModal');
        const container = document.getElementById('modalContainer');

        container.classList.add('opacity-0', 'scale-95', 'translate-y-20');
        container.classList.remove('opacity-100', 'scale-100', 'translate-y-0');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 500);
    }

    // Global Escape Key Listener
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeEventModal();
    });
</script>

<style>
    .font-display {
        font-family: 'Playfair Display', serif;
    }

    #modalContainer::-webkit-scrollbar {
        width: 0px;
    }

    .line-clamp-4 {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #D4AF37;
        border-radius: 10px;
    }
</style>