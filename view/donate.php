<?php
// Seva Options fetch for the dropdown
$all_sevas = [];
try {
    $all_sevas = $pdo->query("SELECT * FROM seva_options WHERE status = 'active' ORDER BY sort_order ASC")->fetchAll();
} catch (Exception $e) {
    $all_sevas = [];
}
?>

<!-- 🕊️ THE SACRED SPLIT: Emotional Donation Portal 🕊️ -->
<!-- Adjusted pt-24 on mobile and pt-36 on desktop to clear fixed header -->
<section id="donation-portal" class="min-h-screen pt-24 lg:pt-36 bg-white flex flex-col lg:flex-row relative overflow-hidden">



    <!-- LEFT: Emotional Visual Anchor (50%) -->
    <div class="lg:w-[45%] xl:w-[50%] h-[350px] md:h-[450px] lg:h-screen sticky top-0 relative overflow-hidden">

        <img src="/asset/img/sanctuary_peace.png" class="w-full h-full object-cover transform hover:scale-110 transition-all duration-[10s] ease-in-out" alt="Sacred Sanctuary Sanctuary">
        <div class="absolute inset-0 bg-gradient-to-tr from-nature/90 via-nature/40 to-transparent"></div>

        <!-- Text Overlay -->
        <div class="absolute bottom-10 lg:bottom-20 left-6 lg:left-12 right-6 lg:right-12 p-6 lg:p-10 backdrop-blur-xl bg-white/10 rounded-[2rem] lg:rounded-[3rem] border border-white/20" data-aos="fade-up">
            <span class="text-gold uppercase tracking-[0.5em] text-[12px] lg:text-[12px] font-black mb-3 lg:mb-4 block" data-lang="donate_label">Gau Mata Sanrakshan</span>
            <h2 class="text-2xl lg:text-4xl xl:text-6xl font-display text-white italic leading-tight mb-4 lg:mb-6" data-lang="donate_offer">
                Your <span class="text-gold underline decoration-gold/20 underline-offset-8">Compassion</span> Is Their Miracle.
            </h2>
            <p class="text-white/60 text-[15px] lg:text-lg leading-relaxed italic" data-lang="donate_desc">
                From the harsh streets of the city to the eternal peace of our sanctuary—every recovery is a story written by you.
            </p>
        </div>
    </div>

    <!-- RIGHT: Professional Form Portal (55%) -->
    <div class="lg:w-[55%] xl:w-[50%] p-6 md:p-12 lg:p-24 overflow-y-auto flex flex-col justify-center">
        <div class="max-w-3xl mx-auto w-full">

            <header class="mb-12 lg:mb-20">
                <div class="flex items-center gap-4 lg:gap-6 mb-8 lg:mb-10">
                    <a href="/" class="w-12 h-12 lg:w-14 lg:h-14 bg-nature/5 text-nature rounded-full flex items-center justify-center hover:bg-saffron hover:text-white transition-all shadow-sm">
                        <i class="fas fa-home"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-display font-bold text-nature" data-lang="donate_title">Sanctuary <span class="italic text-saffron">Registry</span></h1>
                        <p class="text-gray-400 text-[12px] lg:text-[12px] uppercase font-black tracking-[0.3em] mt-1" data-lang="donate_subtitle">Divine Service Center</p>
                    </div>
                </div>


            </header>

            <div class="mb-10 text-left" data-aos="fade-up">
                <h4 class="text-[12px] lg:text-[12px] font-black uppercase tracking-widest text-nature/30 mb-6 flex items-center justify-center md:justify-start gap-2">Bank Account Details <i class="far fa-copy opacity-40"></i></h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-nature/5 p-6 rounded-2xl text-center md:text-left">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-nature/40 mb-1">Account Name</p>
                        <p class="text-sm font-bold text-nature">SHRI GAU RAKSHK SEVA SAMITI</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-nature/40 mb-1">Account Number</p>
                        <p class="text-sm font-bold text-nature">9049164841</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-nature/40 mb-1">Bank Name</p>
                        <p class="text-sm font-bold text-nature">KOTAK MAHINDRA BANK</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-nature/40 mb-1">IFSC Code & Branch</p>
                        <p class="text-sm font-bold text-nature">KKBK0003065 (GANDHIDHAM)</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 mb-10 text-left" data-aos="fade-up">
                <h4 class="text-[12px] lg:text-[12px] font-black uppercase tracking-widest text-nature/30 mb-4" data-lang="donate_verify_label">UPI Payment</h4>
                <div class="space-y-1 bg-nature/5 p-4 rounded-2xl md:inline-block w-full text-center md:text-left">
                    <p class="text-[10px] font-black uppercase tracking-widest text-nature/40 mb-1" data-lang="donate_upi_label">Direct UPI Passage</p>
                    <p class="text-[14px] lg:text-[14px] text-saffron font-bold">0793065A0168004.BQR@KOTAK <i class="far fa-copy ml-1 opacity-40 hover:opacity-100 cursor-pointer"></i></p>
                    <p class="text-[10px] lg:text-[10px] text-nature/80 font-bold uppercase tracking-widest mt-1">SHRI GAU RAKSHK SEVA SAMITI</p>
                </div>
            </div>

            <form id="donation-form" enctype="multipart/form-data" class="space-y-10 lg:space-y-12">
                <input type="hidden" name="currency" id="selected-currency" value="INR">
                <div class="space-y-8 lg:space-y-10" data-aos="fade-up">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-10">
                        <!-- Select Seva -->
                        <div>
                            <label class="block text-[12px] lg:text-[12px] font-black uppercase tracking-widest text-nature/40 mb-3 ml-1" data-lang="form_seva_label">Choose Seva</label>
                            <div class="relative">
                                <select name="seva_id" class="w-full h-[64px] lg:h-[72px] bg-white border border-gray-100 focus:border-saffron/30 rounded-2xl px-6 text-nature font-bold appearance-none cursor-pointer shadow-sm transition-all text-[15px] lg:text-base">
                                    <option value="" data-lang="form_select_opt">Select Domain</option>
                                    <?php foreach ($all_sevas as $s): ?>
                                        <option value="<?= $s['id'] ?>" data-trans="en"><?= htmlspecialchars($s['title_en']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 text-saffron opacity-40 pointer-events-none"><i class="fas fa-heart text-xs"></i></div>
                            </div>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label class="block text-[12px] lg:text-[12px] font-black uppercase tracking-widest text-nature/40 mb-3 ml-1" data-lang="form_amount_label">Amount (₹)</label>
                            <input type="number" name="amount" id="donation-amount" placeholder="₹ Custom" data-lang-placeholder="placeholder_amount" class="w-full h-[64px] lg:h-[72px] bg-white border border-gray-100 focus:border-saffron/30 rounded-2xl px-6 text-nature font-black text-xl lg:text-2xl shadow-sm" required>
                        </div>
                    </div>

                    <!-- Presets -->
                    <div class="flex flex-wrap gap-2 lg:gap-3">
                        <?php foreach ([11000, 5100, 2101, 1100, 501] as $p): ?>
                            <button type="button" onclick="setDonationAmount(<?= $p ?>)" class="px-4 lg:px-6 py-2.5 lg:py-3 rounded-xl border border-gray-100 bg-white text-[12px] lg:text-[12px] font-black text-nature hover:bg-gold hover:text-nature transition-all">₹ <?= number_format($p) ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-gray-50/80 backdrop-blur-md p-6 lg:p-10 rounded-[2rem] lg:rounded-[3rem] space-y-8 lg:space-y-10 border border-gray-100 shadow-inner" data-aos="fade-up">
                    <div>
                        <label class="block text-[12px] lg:text-[12px] font-black uppercase tracking-widest text-nature/60 mb-6 lg:mb-8 px-4 flex items-center gap-3"><span class="w-2 h-2 bg-saffron rounded-full animate-pulse"></span> <span data-lang="form_id_label">Sacred Identification</span></label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-10">
                            <input type="text" name="donor_name" placeholder="Your Full Name" data-lang-placeholder="placeholder_name" class="bg-white/50 border-b-2 border-gray-200 focus:border-saffron py-4 px-6 rounded-t-xl font-bold text-nature text-base lg:text-lg focus:outline-none transition-all placeholder:text-nature/40" required>
                            <input type="tel" name="phone" placeholder="WhatsApp Number" data-lang-placeholder="placeholder_phone" class="bg-white/50 border-b-2 border-gray-200 focus:border-saffron py-4 px-6 rounded-t-xl font-bold text-nature text-base lg:text-lg focus:outline-none transition-all placeholder:text-nature/40">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-10">
                        <input type="email" name="email" placeholder="Email Address" data-lang-placeholder="placeholder_email" class="bg-white/50 border-b-2 border-gray-200 focus:border-saffron py-4 px-6 rounded-t-xl font-bold text-nature text-base lg:text-lg focus:outline-none transition-all placeholder:text-nature/40">
                        <div class="relative">
                            <input type="date" name="date" class="w-full bg-white/50 border-b-2 border-gray-200 focus:border-saffron py-4 px-6 rounded-t-xl font-bold text-nature text-base lg:text-lg focus:outline-none transition-all">
                            <span class="absolute top-[-10px] right-4 text-[9px] lg:text-[9px] font-black uppercase tracking-widest text-nature bg-white px-2 rounded-full border-2 border-saffron/10 shadow-sm" data-lang="form_birthday_label">Birthday Ritual</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] lg:text-[12px] font-black uppercase tracking-widest text-nature/60 mb-4 px-4 flex items-center gap-2"><i class="fas fa-image opacity-50"></i> Payment Screenshot</label>
                        <input type="file" name="screenshot" accept="image/*" class="w-full bg-white/50 border-b-2 border-gray-200 focus:border-saffron py-3 px-6 rounded-t-xl font-bold text-nature text-base lg:text-lg focus:outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-black file:bg-nature/10 file:text-nature hover:file:bg-nature/20 cursor-pointer">
                    </div>
                    
                    <div class="hidden" style="display:none;">
                        <label>Leave this field empty</label>
                        <input type="text" name="hp_catcher" id="donate_hp_catcher" tabindex="-1" autocomplete="off">
                    </div>
                </div>

        <button type="submit" class="w-full py-6 lg:py-8 rounded-[2rem] lg:rounded-[2.5rem] bg-nature text-white font-display text-2xl lg:text-3xl font-bold shadow-[0_40px_80px_rgba(44,76,59,0.3)] hover:bg-saffron transition-all duration-1000 transform hover:-translate-y-2 flex items-center justify-center gap-4 lg:gap-6 group">
            <span id="submit-text" class="relative italic" data-lang="form_submit">Perform Sacred Ritual</span>
            <i class="fas fa-arrow-right text-base lg:text-lg opacity-40 transform group-hover:translate-x-3 transition-transform"></i>
        </button>


        <div class="pt-12 lg:pt-16 border-t border-gray-50 text-center flex justify-center">
            <div class="space-y-4 inline-block text-left">
                <h4 class="text-[12px] lg:text-[12px] font-black uppercase tracking-widest text-nature/30" data-lang="donate_trust_label">Sanctuary Trust</h4>
                <div class="flex items-center gap-3 bg-nature/5 p-4 rounded-2xl">
                    <i class="fas fa-certificate text-gold"></i>
                    <p class="text-[12px] lg:text-[12px] font-bold text-nature/60 italic leading-snug" data-lang="donate_tax_label">Tax Exemption U/S 80G - Income Tax Act 1961</p>
                </div>
            </div>
        </div>

        </form>
    </div>
    </div>

</section>

<style>
    .slanted-overlay {
        clip-path: polygon(0 0, 100% 0, 95% 100%, 0% 100%);
    }

    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f9f9f9;
    }

    ::-webkit-scrollbar-thumb {
        background: #eee;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #ddd;
    }
</style>

<script>
    function switchCurrency(type) {
        const tabs = document.querySelectorAll('.currency-tab');
        const indianTab = document.getElementById('tab-indian');
        const foreignTab = document.getElementById('tab-foreign');

        tabs.forEach(t => {
            t.classList.remove('bg-saffron', 'text-white', 'shadow-xl', 'shadow-saffron/20', 'font-bold');
            t.classList.add('text-gray-400');
        });

        if (type === 'indian') {
            indianTab.classList.add('bg-saffron', 'text-white', 'shadow-xl', 'shadow-saffron/20', 'font-bold');
            indianTab.classList.remove('text-gray-400');
            document.getElementById('selected-currency').value = 'INR';
        } else {
            foreignTab.classList.add('bg-saffron', 'text-white', 'shadow-xl', 'shadow-saffron/20', 'font-bold');
            foreignTab.classList.remove('text-gray-400');
            document.getElementById('selected-currency').value = 'Foreign';
        }
    }

    function setDonationAmount(amount) {
        const input = document.getElementById('donation-amount');
        if (input) {
            input.value = amount;
            input.focus();
        }
    }
    document.getElementById('donation-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const submitText = document.getElementById('submit-text');

        submitBtn.disabled = true;
        const originalText = submitText.innerText;
        submitText.innerText = "Authenticating...";

        grecaptcha.ready(function() {
            grecaptcha.execute('6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI', {action: 'submit'}).then(function(token) {
                const formData = new FormData(form);
                formData.append('recaptcha_token', token);
                submitText.innerText = "Processing...";

                fetch('./api/donation_handler.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Toast.fire({
                                icon: 'success',
                                title: data.message
                            });

                            // Optimized redirect flow to the payment integration page
                            setTimeout(() => {
                                window.location.href = '/payment?status=success';
                            }, 2000);

                            form.reset();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Toast.fire({
                            icon: 'error',
                            title: 'Sacred connection lost. Please try again.'
                        });
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitText.innerText = originalText;
                    });
            });
        });
    });
</script>
<script src="https://www.google.com/recaptcha/api.js?render=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></script>