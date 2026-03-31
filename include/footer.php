</main>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- PREMIUM FOOTER -->
<!-- ═══════════════════════════════════════════════════════════ -->
<footer class="relative overflow-hidden pt-10 pb-10" style="background: linear-gradient(135deg, #1a0f00 0%, #2d1a00 50%, #1a0a00 100%);">

    <!-- Ambient Glows -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-saffron/10 rounded-full blur-[150px] pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 w-80 h-80 bg-gold/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute inset-0 opacity-[0.04] pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/shattered.png');"></div>

    <div class="container mx-auto px-6 relative z-10">
        <!-- Top Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-16 mb-12 pb-12 border-b border-white/10">


            <!-- Col 1: Brand -->
            <div class="lg:col-span-1">
                <div class="flex items-center gap-6 mb-12">
                    <div class="relative">
                        <!-- Premium Wide Glow -->
                        <div class="absolute inset-0 bg-gold/10 rounded-full blur-2xl"></div>

                        <!-- Branding Capsule Footer-Lite: Fully Responsive -->
                        <div class="relative bg-gradient-to-r from-white via-white to-[#fff9f2] border-2 border-gold rounded-full flex items-center px-1 py-1 md:px-2 md:py-2 gap-3 md:gap-6 shadow-2xl h-16 md:h-28 max-w-fit pr-6 md:pr-12">
                            <!-- Logo Circle Inside -->
                            <div class="h-14 w-14 md:h-24 md:w-24 rounded-full bg-white flex items-center justify-center p-1 flex-shrink-0 border border-gold/20 shadow-lg">
                                <img src="/asset/img/logo/logo.png" class="h-full w-full object-contain" alt="Logo">
                            </div>
                            <!-- Brand Typography Integrated -->
                            <div class="flex flex-col overflow-hidden">
                                <span class="text-nature font-display text-[12px] md:text-2xl font-bold leading-tight md:mb-1" data-lang="brand_name">શ્રી ગૌ રક્ષક સેવા સમિતિ</span>
                                <span class="text-[12px] md:text-[12px] uppercase tracking-[0.1em] md:tracking-[0.4em] text-saffron font-black" data-lang="brand_tagline">Panjrapole</span>
                            </div>
                        </div>

                    </div>
                </div>




                <div class="w-16 h-1 bg-gradient-to-r from-saffron to-gold rounded-full mb-8"></div>
                <p class="text-white/50 text-[15px] leading-relaxed font-light mb-8" data-lang="footer_about_desc">
                    Transforming lives through Vedic wisdom and compassionate cow protection. Join us in our mission to serve the sacred beings.
                </p>
                <p class="text-saffron text-[15px] font-display italic border-l-2 border-saffron/40 pl-4">
                    "Gavi sarvasya mangalam"<br>
                    <span class="text-white/40 text-[12px] not-italic">— The cow brings auspiciousness to all.</span>
                </p>
            </div>

            <!-- Col 2: Quick Links -->
            <div>
                <h3 class="text-white font-bold uppercase tracking-[0.4em] text-xs mb-8" data-lang="footer_quick_links">Quick Links</h3>
                <ul class="space-y-4">
                    <?php
                    $links = [
                        ['Home', '/'],
                        ['Our Story', '/about'],
                        ['Founding Visionaries', '/founders'],
                        ['Management Council', '/team'],
                        ['Gallery Showcase', '/gallery'],
                        ['Spiritual Events', '/events'],
                        ['Latest Announcements', '/announcements'],
                        ['Contact Seva', '/contact'],
                    ];
                    foreach ($links as $link): ?>
                        <li>
                            <a href="<?= $link[1] ?>" class="text-white/50 text-[15px] hover:text-gold transition-colors duration-300 flex items-center gap-2 group">
                                <span class="w-4 h-px bg-saffron/30 group-hover:w-6 group-hover:bg-gold transition-all duration-300"></span>
                                <?= $link[0] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Col 3: Contact -->
            <div>
                <h3 class="text-white font-bold uppercase tracking-[0.4em] text-xs mb-8" data-lang="footer_contact_us">Get In Touch</h3>
                <div class="space-y-6">
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 bg-saffron/10 rounded-xl flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-map-marker-alt text-saffron text-sm"></i>
                        </div>
                        <div>
                            <p class="text-white/80 text-[15px] leading-relaxed" data-lang="footer_address">Shri Gau Rakshak Seva Samiti,<br>સર્વે નં. ૧૨૯, રાજવી રીસોર્ટ ની પાછળ, <br>ગળપાદર, ગાંધીધામ - કચ્છ.</p>
                        </div>

                    </div>
                    <div class="flex gap-4 items-center">
                        <div class="w-10 h-10 bg-saffron/10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-saffron text-sm"></i>
                        </div>
                        <a href="tel:+919998581811" class="text-white/70 text-[15px] hover:text-gold transition-colors">+91 99985 81811</a>

                    </div>
                    <div class="flex gap-4 items-center">
                        <div class="w-10 h-10 bg-saffron/10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-saffron text-sm"></i>
                        </div>
                        <a href="mailto:seva@gaushala.org" class="text-white/70 text-[15px] hover:text-gold transition-colors">seva@gaushala.org</a>
                    </div>
                    <div class="flex gap-4 items-center">
                        <div class="w-10 h-10 bg-saffron/10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-saffron text-sm"></i>
                        </div>
                        <p class="text-white/70 text-[15px]">Open Daily: 6:00 AM – 8:00 PM</p>
                    </div>
                </div>
            </div>

            <!-- Col 4: Seva CTA + Social -->
            <div>
                <h3 class="text-white font-bold uppercase tracking-[0.4em] text-xs mb-8" data-lang="footer_social">Stay Connected</h3>
                <div class="flex gap-3 mb-10">
                    <a href="#" class="w-11 h-11 bg-white/5 border border-white/10 rounded-full flex items-center justify-center text-white/50 hover:bg-saffron hover:text-white hover:border-saffron transition-all duration-300" title="Facebook">
                        <i class="fab fa-facebook-f text-sm"></i>
                    </a>
                    <a href="#" class="w-11 h-11 bg-white/5 border border-white/10 rounded-full flex items-center justify-center text-white/50 hover:bg-[#E1306C] hover:text-white hover:border-[#E1306C] transition-all duration-300" title="Instagram">
                        <i class="fab fa-instagram text-sm"></i>
                    </a>
                    <a href="#" class="w-11 h-11 bg-white/5 border border-white/10 rounded-full flex items-center justify-center text-white/50 hover:bg-[#25D366] hover:text-white hover:border-[#25D366] transition-all duration-300" title="WhatsApp">
                        <i class="fab fa-whatsapp text-sm"></i>
                    </a>
                    <a href="#" class="w-11 h-11 bg-white/5 border border-white/10 rounded-full flex items-center justify-center text-white/50 hover:bg-[#FF0000] hover:text-white hover:border-[#FF0000] transition-all duration-300" title="YouTube">
                        <i class="fab fa-youtube text-sm"></i>
                    </a>
                </div>

                <p class="text-white/40 text-[12px] uppercase tracking-wider leading-relaxed mb-6 font-bold">
                    REG.NO.: GUJ1517/F/1693KUTCH (PAN NO. AABTG4428K) <br>
                    DONATION IS EXEMPT U/S 80G(5) OF IT ACT. 1961 <br>
                    VIDE REG.NO. AABTG4428KF20241 <br>
                    VALID W.E.F. 06/04/2024. VALID FROM AY 2024-25 TO 2026-27
                </p>

                <a href="/donate" class="bg-gradient-to-r from-saffron to-gold text-white px-8 py-4 rounded-2xl font-bold text-[15px] uppercase tracking-widest shadow-xl hover:scale-105 transition-all duration-500 inline-block w-full text-center" data-lang="nav_donate">
                    <i class="fas fa-hand-holding-heart mr-2"></i> Donate Now
                </a>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-white/30 text-[15px]">
                &copy; <?php echo date('Y'); ?> <span class="text-gold/60" data-lang="brand_name">શ્રી ગૌ રક્ષક સેવા સમિતિ</span>. <span data-lang="footer_rights">All rights reserved.</span>
            </p>
            <div class="flex gap-6 text-white/30 text-xs">
                <a href="/privacy" class="hover:text-gold transition-colors">Privacy Policy</a>
                <span>·</span>
                <a href="/terms" class="hover:text-gold transition-colors">Terms of Use</a>
                <span>·</span>
                <a href="/contact" class="hover:text-gold transition-colors">Get in Touch</a>
            </div>
            <div class="text-right">
                <p class="text-white/20 text-[12px] italic mb-1">Crafted with reverence &amp; devotion</p>
                <p class="text-white/40 text-[12px] font-bold tracking-widest uppercase opacity-60">Developed by <span class="text-gold/50">BCS Group</span></p>
            </div>
        </div>
    </div>
</footer>

<!-- GSAP Animation Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- AOS (Animate on Scroll) JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Initialize Main Scripts -->
<script src="/asset/js/app.js"></script>
<script src="/asset/js/donation-toaster.js"></script>




</body>

</html>