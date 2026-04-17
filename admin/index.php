<?php
// Security checks are handled globally in root index.php

// Secure session settings before session_start
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    $is_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? '1' : '0';
    ini_set('session.cookie_secure', $is_https);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

// Security headers for admin dashboard
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=()');
header("Content-Security-Policy: default-src 'self' https://cdn.tailwindcss.com https://fonts.googleapis.com https://fonts.gstatic.com https://cdnjs.cloudflare.com; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; img-src 'self' data: https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://fonts.googleapis.com https://cdnjs.cloudflare.com data:; connect-src 'self'; frame-ancestors 'self';");
header('Cross-Origin-Opener-Policy: same-origin');
header('Cross-Origin-Resource-Policy: same-origin');
header_remove('X-Powered-By');

require_once 'include/auth.php';
require_once '../config/db.php';

// Fetch quick stats
$announcement_count = 0;
$event_count = 0;
$testimonial_count = 0;
$raised_amount = 0;
try {
    $announcement_count = $pdo->query("SELECT count(*) FROM announcements")->fetchColumn();
    $event_count = $pdo->query("SELECT count(*) FROM events")->fetchColumn();
    $testimonial_count = $pdo->query("SELECT count(*) FROM testimonials")->fetchColumn();

    // Dynamic Transparency Stat
    $stats_sum = $pdo->query("SELECT raised FROM transparency_stats WHERE id = 1")->fetchColumn();
    if ($stats_sum !== false) {
        $raised_amount = $stats_sum;
    } else {
        $raised_amount = $pdo->query("SELECT SUM(current_val) FROM transparency_materials")->fetchColumn() ?: 0;
    }

    // Fetch latest requests & donations
    $recent_contacts = $pdo->query("SELECT * FROM contact_requests ORDER BY created_at DESC LIMIT 5")->fetchAll();
    $recent_donations = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 5")->fetchAll();
} catch (Exception $e) {
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gaushala Admin</title>
    <?php include 'include/head.php'; ?>
</head>

<body class="md:h-screen flex flex-col md:flex-row bg-[#faf8f6] overflow-hidden">

    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 p-6 md:p-12 overflow-y-auto h-full">

        <!-- Header Section -->
        <header class="flex justify-between items-center mb-12 relative z-50">
            <div>
                <p class="text-[12px] uppercase tracking-widest text-gray-400 font-bold mb-2">Welcome Back,</p>
                <h2 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature">Core <span class="text-[#FF6A00] italic">Panel</span></h2>
            </div>

            <details class="group relative">
                <summary class="glass flex items-center gap-4 p-2 pl-6 rounded-full pr-2 shadow-sm cursor-pointer list-none hover:shadow-md transition-all border border-transparent hover:border-saffron/20 group-open:shadow-lg">
                    <span class="text-[10px] uppercase font-black tracking-widest text-gray-400 hidden md:block">Administrator Mode</span>
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block border-l border-gray-200 pl-4 py-1">
                            <p class="text-[12px] font-bold text-nature leading-none"><?= $_SESSION['admin_name'] ?? 'Admin' ?></p>
                            <p class="text-[9px] uppercase tracking-widest text-saffron font-black mt-1"><?= $_SESSION['admin_role'] ?? 'Super Admin' ?></p>
                        </div>
                        <div class="w-10 h-10 bg-nature text-white flex items-center justify-center rounded-full font-bold shadow-inner">
                            <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                        </div>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 group-open:rotate-180 transition-transform ml-1"></i>
                    </div>
                </summary>

                <div class="absolute right-0 top-full mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform opacity-0 scale-95 origin-top-right group-open:opacity-100 group-open:scale-100 transition-all duration-200 z-50">
                    <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                        <p class="text-[9px] uppercase tracking-widest text-gray-400 font-bold mb-1">Signed in as</p>
                        <p class="text-xs font-bold text-nature truncate"><?= $_SESSION['admin_name'] ?? 'Administrator' ?></p>
                    </div>
                    <div class="p-2">
                        <a href="profile.php" class="flex items-center gap-3 text-[11px] font-bold uppercase tracking-widest text-gray-400 hover:text-nature hover:bg-gray-50 p-3 rounded-xl transition-colors">
                            <i class="fas fa-user-circle w-4"></i> Profile Details
                        </a>
                        <a href="logout.php" class="flex items-center gap-3 text-[11px] font-bold uppercase tracking-widest text-red-500 hover:text-red-600 hover:bg-red-50 p-3 rounded-xl transition-colors mt-1">
                            <i class="fas fa-sign-out-alt w-4"></i> Secure Logout
                        </a>
                    </div>
                </div>
            </details>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <div class="glass p-8 rounded-[2rem] shadow-sm relative overflow-hidden group hover:shadow-xl transition-all border-b-4 border-saffron">
                <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 group-hover:opacity-10 transition-all group-hover:scale-110"><i class="fas fa-bullhorn"></i></div>
                <p class="text-sm text-gray-400 font-bold mb-1">Active News</p>
                <h3 class="text-4xl font-bold text-nature"><?= $announcement_count ?></h3>
                <p class="text-[12px] font-black uppercase tracking-widest text-nature/30 mt-2 italic">Broadcast Reach: High</p>
            </div>

            <div class="glass p-8 rounded-[2rem] shadow-sm relative overflow-hidden group hover:shadow-xl transition-all border-b-4 border-blue-400">
                <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 group-hover:opacity-10 transition-all"><i class="fas fa-calendar-alt"></i></div>
                <p class="text-sm text-gray-400 font-bold mb-1">Scheduled Events</p>
                <h3 class="text-4xl font-bold text-nature"><?= $event_count ?></h3>
                <p class="text-[12px] font-black uppercase tracking-widest text-nature/30 mt-2 italic">Next: Vedic Festival</p>
            </div>

            <!-- DYNAMIC TRANSPARENCY STAT -->
            <div class="glass p-8 rounded-[2rem] shadow-sm relative overflow-hidden group hover:shadow-xl transition-all border-b-4 border-green-500">
                <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 group-hover:opacity-10 transition-all"><i class="fas fa-hand-holding-heart"></i></div>
                <p class="text-sm text-gray-400 font-bold mb-1">Total Raised</p>
                <h3 class="text-4xl font-bold text-nature">₹<?= number_format($raised_amount) ?></h3>
                <p class="text-[12px] font-black uppercase tracking-widest text-nature/30 mt-2 italic">Financial Trust: Verified</p>
            </div>

            <div class="premium-gradient p-8 rounded-[2rem] shadow-xl relative overflow-hidden text-white hover:scale-[1.03] transition-all cursor-pointer shadow-saffron/20 border-b-4 border-white/20">
                <div class="absolute -right-4 -bottom-4 text-8xl opacity-20"><i class="fas fa-plus"></i></div>
                <p class="text-[12px] uppercase tracking-widest font-bold mb-4 opacity-80">Quick Action</p>
                <p class="text-lg font-bold italic">Commit<br>New Records</p>
                <a href="transparency/editor.php" class="absolute inset-0"></a>
            </div>
        </div>

        <!-- Sections Menu -->
        <h3 class="text-[12px] uppercase tracking-widest text-gray-400 font-bold mb-8 flex items-center gap-4">
            Available Modules <div class="h-[1px] bg-gray-200 flex-1"></div>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
            <!-- Transparency Module -->
            <a href="transparency/index.php" class="glass group p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all border border-transparent hover:border-saffron/20 block">
                <div class="w-16 h-16 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-10 group-hover:scale-110 group-hover:bg-green-600 group-hover:text-white transition-all shadow-lg shadow-green-600/5">
                    <i class="fas fa-hand-holding-heart text-2xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-nature mb-4">Financial Transparency</h4>
                <p class="text-[15px] text-gray-400 leading-relaxed mb-6">Manage institutional expenditures and synchronize your mission goals with the public ledger.</p>
                <div class="flex items-center gap-2 text-[15px] font-bold text-green-600 uppercase tracking-widest">Open Ledger <i class="fas fa-arrow-right text-[12px]"></i></div>
            </a>

            <!-- News Module -->
            <a href="announcements.php" class="glass group p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all border border-transparent hover:border-saffron/20 block">
                <div class="w-16 h-16 bg-saffron/5 text-saffron rounded-2xl flex items-center justify-center mb-10 group-hover:scale-110 group-hover:bg-saffron group-hover:text-white transition-all shadow-lg shadow-saffron/5">
                    <i class="fas fa-bullhorn text-2xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-nature mb-4">News Panel</h4>
                <p class="text-[15px] text-gray-400 leading-relaxed mb-6">Manage the header marquee and recent announcements across the portal.</p>
                <div class="flex items-center gap-2 text-[15px] font-bold text-saffron uppercase tracking-widest">Open Module <i class="fas fa-arrow-right text-[12px]"></i></div>
            </a>

            <!-- Events Module -->
            <a href="events/index.php" class="glass group p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all border border-transparent hover:border-saffron/20 block">
                <div class="w-16 h-16 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center mb-10 group-hover:scale-110 group-hover:bg-purple-500 group-hover:text-white transition-all shadow-lg shadow-purple-500/5">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-nature mb-4">Upcoming Events</h4>
                <p class="text-[15px] text-gray-400 leading-relaxed mb-6">Schedule and manage upcoming festivals, shivirs, and special seva events.</p>
                <div class="flex items-center gap-2 text-[15px] font-bold text-purple-500 uppercase tracking-widest">Manage Events <i class="fas fa-arrow-right text-[12px]"></i></div>
            </a>

            <!-- Testimonials Module -->
            <a href="testimonials/index.php" class="glass group p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all border border-transparent hover:border-saffron/20 block">
                <div class="w-16 h-16 bg-gold/10 text-gold rounded-2xl flex items-center justify-center mb-10 group-hover:scale-110 group-hover:bg-gold group-hover:text-nature transition-all shadow-lg shadow-gold/5">
                    <i class="fas fa-quote-right text-2xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-nature mb-4">Testimonial Feed</h4>
                <p class="text-[15px] text-gray-400 leading-relaxed mb-6">Review and manage the voices of devotion from your global community and supporters.</p>
                <div class="flex items-center gap-2 text-[15px] font-bold text-gold uppercase tracking-widest">Review Voices <i class="fas fa-arrow-right text-[12px]"></i></div>
            </a>
            <!-- Management Team -->
            <a href="team/index.php" class="glass group p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all border border-transparent hover:border-saffron/20 block">
                <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-10 group-hover:scale-110 group-hover:bg-blue-500 group-hover:text-white transition-all shadow-lg shadow-blue-500/5">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-nature mb-4">Management Team</h4>
                <p class="text-[15px] text-gray-400 leading-relaxed mb-6">Update profile details, designations, and roles of the core leadership members.</p>
                <div class="flex items-center gap-2 text-[15px] font-bold text-blue-500 uppercase tracking-widest">Update Team <i class="fas fa-arrow-right text-[12px]"></i></div>
            </a>

            <!-- Founders Module -->
            <a href="founders/index.php" class="glass group p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all border border-transparent hover:border-saffron/20 block">
                <div class="w-16 h-16 bg-gold/10 text-gold rounded-2xl flex items-center justify-center mb-10 group-hover:scale-110 group-hover:bg-gold group-hover:text-nature transition-all shadow-lg shadow-gold/5">
                    <i class="fas fa-user-shield text-2xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-nature mb-4">Founders Circle</h4>
                <p class="text-[15px] text-gray-400 leading-relaxed mb-6">Manage profiles of the founding visionaries and their spiritual contributions.</p>
                <div class="flex items-center gap-2 text-[15px] font-bold text-gold uppercase tracking-widest">Honor Council <i class="fas fa-arrow-right text-[12px]"></i></div>
            </a>
        </div>

        <!-- RECENT DATA SECTION -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 pb-32">
            <!-- Latest Contact Requests -->
            <div class="glass p-10 rounded-[3rem] shadow-xl border border-white/40">
                <div class="flex justify-between items-center mb-10">
                    <h4 class="text-xl font-bold text-nature flex items-center gap-3">
                        <i class="fas fa-envelope-open text-saffron"></i> Latest Leads
                    </h4>
                    <a href="contact_requests.php" class="text-[10px] uppercase font-black tracking-widest text-nature/30 hover:text-saffron transition-colors">View All</a>
                </div>
                <div class="space-y-4">
                    <?php if (empty($recent_contacts)): ?>
                        <p class="text-gray-400 text-sm italic py-10 text-center">No recent contact leads.</p>
                    <?php else: ?>
                        <?php foreach ($recent_contacts as $rc): ?>
                            <div class="flex items-center justify-between p-4 bg-white/50 rounded-2xl border border-gray-50 group hover:bg-white transition-all">
                                <div>
                                    <p class="font-bold text-nature text-sm"><?= htmlspecialchars($rc['name']) ?></p>
                                    <p class="text-[11px] text-gray-400 font-medium"><?= htmlspecialchars($rc['purpose']) ?></p>
                                </div>
                                <span class="text-[10px] font-bold text-gray-300 italic"><?= date('d M', strtotime($rc['created_at'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Detailed Donations -->
            <div class="glass p-10 rounded-[3rem] shadow-xl border border-white/40">
                <div class="flex justify-between items-center mb-10">
                    <h4 class="text-xl font-bold text-nature flex items-center gap-3">
                        <i class="fas fa-hand-holding-heart text-green-500"></i> New Offerings
                    </h4>
                    <a href="donations.php" class="text-[10px] uppercase font-black tracking-widest text-nature/30 hover:text-green-600 transition-colors">Digital Registry</a>
                </div>
                <div class="space-y-4">
                    <?php if (empty($recent_donations)): ?>
                        <p class="text-gray-400 text-sm italic py-10 text-center">No sacred donations recorded.</p>
                    <?php else: ?>
                        <?php foreach ($recent_donations as $rd): ?>
                            <div class="flex items-center justify-between p-4 bg-white/50 rounded-2xl border border-gray-50 group hover:bg-white transition-all">
                                <div>
                                    <p class="font-bold text-nature text-sm"><?= htmlspecialchars($rd['donor_name']) ?></p>
                                    <p class="text-[11px] text-green-600 font-bold tracking-widest">₹<?= number_format($rd['amount']) ?></p>
                                </div>
                                <span class="text-[10px] font-bold text-gray-300 italic"><?= date('d M', strtotime($rd['created_at'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

</body>

</html>