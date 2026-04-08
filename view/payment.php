<?php
// Secure redirection or access
if (!isset($_GET['status']) || $_GET['status'] !== 'success') {
    // Optionally redirect back if accessed directly without success
}
?>
<!-- Premium Payment Integration Portal -->
<section class="min-h-screen pt-40 pb-24 lg:pt-48 bg-[#fffcf9] overflow-hidden flex flex-col justify-center">

    <div class="w-full px-6 xl:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch max-w-7xl mx-auto">

            <!-- Box 1: UPI Transmit -->
            <div class="relative group h-full" data-aos="fade-up" data-aos-delay="100">
            <div class="absolute inset-0 bg-gold/10 rounded-[3.5rem] blur-3xl group-hover:bg-saffron/10 transition-all duration-1000"></div>
            <div class="relative glass p-8 xl:p-10 rounded-[3.5rem] border border-white shadow-xl text-center h-full flex flex-col justify-center">
                <h3 class="text-2xl font-bold text-nature mb-6">UPI Transmit</h3>
                <div class="bg-nature/5 p-5 rounded-2xl border border-nature/5">
                    <p class="text-[12px] font-black uppercase tracking-widest text-nature/30 mb-1">Passage ID (UPI)</p>
                    <p class="text-[15px] xl:text-lg font-bold text-saffron tracking-wider select-all break-all">0793065A0168004.BQR@KOTAK</p>
                </div>
            </div>
        </div>

        <!-- Box 2: Direct Transfer Bank Details -->
        <div class="glass p-8 xl:p-10 rounded-[3.5rem] border border-white shadow-xl h-full flex flex-col justify-center" data-aos="fade-up" data-aos-delay="200">
            <h3 class="text-xl font-bold text-nature mb-8 flex items-center gap-3">
                <i class="fas fa-university text-gold"></i> Direct Transfer
            </h3>
            <div class="space-y-6">
                <div class="flex flex-col gap-1 border-b border-gray-100 pb-4">
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Account Guardian (Name)</span>
                    <span class="text-sm xl:text-base font-bold text-nature leading-tight">SHRI GAU RAKSHK SEVA SAMITI</span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Sacred Number (A/C)</span>
                    <span class="text-sm xl:text-base font-bold text-nature">9049164841</span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Vedic Link (IFSC)</span>
                    <span class="text-sm xl:text-base font-bold text-nature">KKBK0003065</span>
                </div>
                <div class="flex flex-col gap-1 border-t border-gray-100 pt-4">
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Bank Sanctuary</span>
                    <span class="text-sm xl:text-base font-bold text-nature leading-tight">KOTAK MAHINDRA BANK, GANDHIDHAM</span>
                </div>
            </div>
        </div>

        <!-- Box 3: Verification (WhatsApp) -->
        <div class="bg-nature p-8 xl:p-10 rounded-[3.5rem] text-white shadow-2xl relative overflow-hidden group h-full flex flex-col justify-center" data-aos="fade-up" data-aos-delay="300">
            <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-1000"></div>
            <div class="relative z-10">
                <h4 class="text-xl font-bold mb-4 flex items-center gap-3"><i class="fas fa-check-double text-gold"></i> Finalize Offering</h4>
                <p class="text-white/60 text-xs xl:text-sm leading-relaxed mb-8 italic">"Once your transfer is complete, please share a screenshot of the confirmation on our WhatsApp for formal acknowledgement and digital receipt generation."</p>
                <a href="https://wa.me/919998581811" target="_blank" class="w-full bg-gold text-nature py-4 rounded-xl font-black uppercase tracking-widest text-[10px] xl:text-[11px] flex items-center justify-center gap-2 hover:bg-white hover:scale-[1.02] transition-all">
                    <i class="fab fa-whatsapp text-lg"></i> Send Screenshot
                </a>
            </div>
        </div>

        <div class="text-center mt-12" data-aos="fade-up" data-aos-delay="400">
            <a href="/" class="text-nature/30 hover:text-saffron transition-colors text-xs font-bold uppercase tracking-widest"><i class="fas fa-arrow-left mr-2"></i> Return to Sanctuary Home</a>
        </div>
    </div>
</section>