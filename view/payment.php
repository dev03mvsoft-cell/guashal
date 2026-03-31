<?php
// Secure redirection or access
if (!isset($_GET['status']) || $_GET['status'] !== 'success') {
    // Optionally redirect back if accessed directly without success
}
?>
<!-- Premium Payment Integration Portal -->
<section class="min-h-screen pt-36 pb-24 bg-[#fffcf9] overflow-hidden">
    <div class="container mx-auto px-6 max-w-5xl">
        <div class="text-center mb-16" data-aos="fade-down">
            <span class="text-saffron font-black uppercase tracking-[0.4em] text-[12px] mb-4 block">Final Step of Devotion</span>
            <h1 class="text-5xl md:text-7xl font-display text-nature leading-tight">Digital <span class="italic text-gold">Offerings</span></h1>
            <p class="text-nature/40 mt-4 text-lg italic tracking-wide">Syncing your contribution with the sanctuary's mission.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <!-- Left: QR Visualization -->
            <div class="relative group" data-aos="fade-right">
                <div class="absolute inset-0 bg-gold/10 rounded-[4rem] blur-3xl group-hover:bg-saffron/10 transition-all duration-1000"></div>
                <div class="relative glass p-10 md:p-14 rounded-[4rem] border border-white shadow-2xl text-center">
                    <div class="mb-10 relative inline-block">
                         <div class="absolute inset-0 bg-saffron animate-ping opacity-10 rounded-3xl"></div>
                         <img src="/asset/img/donation_qr_mockup.png" class="w-64 h-64 mx-auto rounded-3xl shadow-2xl border-4 border-gold/10 relative z-10" alt="Payment QR">
                    </div>
                    <h3 class="text-2xl font-bold text-nature mb-2">Scan & Sanctify</h3>
                    <p class="text-nature/60 text-sm leading-relaxed mb-8">Scan this sacred code through any BHIM UPI Application (PhonePe, Google Pay, Paytm) to complete your ritual.</p>
                    <div class="bg-nature/5 p-5 rounded-2xl border border-nature/5">
                        <p class="text-[12px] font-black uppercase tracking-widest text-nature/30 mb-1">Passage ID (UPI)</p>
                        <p class="text-xl font-bold text-saffron tracking-wider select-all">goseva.augp@aubank</p>
                    </div>
                </div>
            </div>

            <!-- Right: Bank Details & Verification -->
            <div class="space-y-8" data-aos="fade-left">
                <div class="glass p-10 rounded-[3.5rem] border border-white shadow-xl">
                    <h3 class="text-2xl font-bold text-nature mb-8 flex items-center gap-4">
                        <i class="fas fa-university text-gold"></i> Direct Transfer
                    </h3>
                    <div class="space-y-6">
                        <div class="flex flex-col gap-1 border-b border-gray-100 pb-4">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Account Guardian (Name)</span>
                            <span class="text-lg font-bold text-nature">SREE GAU RAKSHAK SEVA SAMITI</span>
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                           <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Sacred Number (A/C)</span>
                                <span class="text-lg font-bold text-nature">2201213031269389</span>
                           </div>
                           <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Vedic Link (IFSC)</span>
                                <span class="text-lg font-bold text-nature">AUBL0002130</span>
                           </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Bank Sanctuary</span>
                            <span class="text-lg font-bold text-nature">AU SMALL FINANCE BANK, GANDHIDHAM</span>
                        </div>
                    </div>
                </div>

                <div class="bg-nature p-10 rounded-[3.5rem] text-white shadow-2xl relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-1000"></div>
                    <div class="relative z-10">
                        <h4 class="text-xl font-bold mb-4 flex items-center gap-3"><i class="fas fa-check-double text-gold"></i> Finalize Your Offering</h4>
                        <p class="text-white/60 text-sm leading-relaxed mb-8 italic">"Once your transfer is complete, please share a screenshot of the confirmation on our WhatsApp for formal acknowledgement and digital receipt generation."</p>
                        <a href="https://wa.me/919998581811" target="_blank" class="w-full bg-gold text-nature py-4 rounded-2xl font-black uppercase tracking-extra-widest text-[12px] flex items-center justify-center gap-3 hover:bg-white hover:scale-[1.02] transition-all">
                           <i class="fab fa-whatsapp text-lg"></i> Send Confirmation Screenshot
                        </a>
                    </div>
                </div>

                <div class="text-center">
                    <a href="/" class="text-nature/30 hover:text-saffron transition-colors text-xs font-bold uppercase tracking-widest"><i class="fas fa-arrow-left mr-2"></i> Return to Sanctuary Home</a>
                </div>
            </div>
        </div>
    </div>
</section>
