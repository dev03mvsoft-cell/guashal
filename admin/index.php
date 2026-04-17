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
        <!-- Header Section -->
        <header class="flex justify-between items-center mb-12 relative z-50">
            <div>
                <p class="text-[14px] uppercase tracking-widest text-gray-600 font-bold mb-2">Welcome Back,</p>
                <h2 style="font-family: 'Playfair Display';" class="text-5xl font-bold text-nature">Core <span class="text-[#FF6A00] italic">Panel</span></h2>
            </div>

            <details class="group relative">
                <summary class="glass flex items-center gap-4 p-3 pl-8 rounded-full pr-3 shadow-sm cursor-pointer list-none hover:shadow-md transition-all border border-transparent hover:border-saffron/20 group-open:shadow-lg">
                    <span class="text-[12px] uppercase font-black tracking-widest text-gray-600 hidden md:block">Administrator Mode</span>
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block border-l-2 border-gray-200 pl-4 py-1">
                            <p class="text-[14px] font-bold text-nature leading-none mb-1 text-right"><?= $_SESSION['admin_name'] ?? 'Admin' ?></p>
                            <p class="text-[11px] uppercase tracking-widest text-saffron font-black text-right"><?= $_SESSION['admin_role'] ?? 'Super Admin' ?></p>
                        </div>
                        <div class="w-12 h-12 bg-nature text-white flex items-center justify-center rounded-full font-bold shadow-inner text-lg">
                            <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-500 group-open:rotate-180 transition-transform ml-1"></i>
                    </div>
                </summary>

                <div class="absolute right-0 top-full mt-3 w-64 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform opacity-0 scale-95 origin-top-right group-open:opacity-100 group-open:scale-100 transition-all duration-200 z-50">
                    <div class="p-5 border-b border-gray-50 bg-gray-50/50">
                        <p class="text-[11px] uppercase tracking-widest text-gray-500 font-black mb-1">Signed in as</p>
                        <p class="text-sm font-bold text-nature truncate"><?= $_SESSION['admin_name'] ?? 'Administrator' ?></p>
                    </div>
                    <div class="p-2">
                        <a href="profile.php" class="flex items-center gap-4 text-[13px] font-bold uppercase tracking-widest text-gray-500 hover:text-nature hover:bg-gray-50 p-4 rounded-xl transition-colors">
                            <i class="fas fa-user-circle w-5 text-lg"></i> Profile Details
                        </a>
                        <a href="logout.php" class="flex items-center gap-4 text-[13px] font-bold uppercase tracking-widest text-red-500 hover:text-red-700 hover:bg-red-50 p-4 rounded-xl transition-colors mt-1 font-black">
                            <i class="fas fa-sign-out-alt w-5 text-lg"></i> Secure Logout
                        </a>
                    </div>
                </div>
            </details>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <div class="glass p-8 rounded-[2.5rem] shadow-sm relative overflow-hidden group hover:shadow-xl transition-all border-b-4 border-saffron">
                <div class="absolute -right-4 -bottom-4 text-9xl opacity-10 group-hover:opacity-20 transition-all group-hover:scale-110"><i class="fas fa-bullhorn text-saffron"></i></div>
                <p class="text-[15px] text-gray-600 font-black uppercase tracking-wider mb-2">Active News</p>
                <h3 class="text-5xl font-bold text-nature mb-2"><?= $announcement_count ?></h3>
                <p class="text-[13px] font-black uppercase tracking-widest text-nature/40 italic">Broadcast Reach: High</p>
            </div>

            <div class="glass p-8 rounded-[2.5rem] shadow-sm relative overflow-hidden group hover:shadow-xl transition-all border-b-4 border-blue-500">
                <div class="absolute -right-4 -bottom-4 text-9xl opacity-10 group-hover:opacity-20 transition-all"><i class="fas fa-calendar-alt text-blue-500"></i></div>
                <p class="text-[15px] text-gray-600 font-black uppercase tracking-wider mb-2">Scheduled Events</p>
                <h3 class="text-5xl font-bold text-nature mb-2"><?= $event_count ?></h3>
                <p class="text-[13px] font-black uppercase tracking-widest text-nature/40 italic">Next: Vedic Festival</p>
            </div>

            <!-- DYNAMIC TRANSPARENCY STAT -->
            <div class="glass p-8 rounded-[2.5rem] shadow-sm relative overflow-hidden group hover:shadow-xl transition-all border-b-4 border-green-500">
                <div class="absolute -right-4 -bottom-4 text-9xl opacity-10 group-hover:opacity-20 transition-all"><i class="fas fa-hand-holding-heart text-green-500"></i></div>
                <p class="text-[15px] text-gray-600 font-black uppercase tracking-wider mb-2">Total Raised</p>
                <h3 class="text-5xl font-bold text-nature mb-2">₹<?= number_format($raised_amount) ?></h3>
                <p class="text-[13px] font-black uppercase tracking-widest text-nature/40 italic">Financial Trust: Verified</p>
            </div>

            <div class="premium-gradient p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden text-white hover:scale-[1.03] transition-all cursor-pointer shadow-saffron/20 border-b-4 border-white/20">
                <div class="absolute -right-4 -bottom-4 text-9xl opacity-20"><i class="fas fa-plus"></i></div>
                <p class="text-[13px] uppercase tracking-widest font-black mb-4 opacity-100">Quick Action</p>
                <p class="text-xl font-black italic leading-tight">COMMIT<br>NEW RECORDS</p>
                <a href="transparency/editor.php" class="absolute inset-0"></a>
            </div>
        </div>

        <!-- Sections Menu -->
        <h3 class="text-[13px] uppercase tracking-[0.2em] text-gray-600 font-black mb-10 flex items-center gap-6">
            AVAILABLE MODULES <div class="h-[2px] bg-gray-100 flex-1"></div>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-10">
            <!-- Transparency Module -->
            <a href="transparency/index.php" class="glass group p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all border border-transparent hover:border-saffron/20 block">
                <div class="w-20 h-20 bg-green-50 text-green-600 rounded-3xl flex items-center justify-center mb-10 group-hover:scale-110 group-hover:bg-green-600 group-hover:text-white transition-all shadow-lg shadow-green-600/5">
                    <i class="fas fa-hand-holding-heart text-3xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-nature mb-4">Financial Transparency</h4>
                <p class="text-[15px] text-gray-600 font-medium leading-relaxed mb-8">Manage institutional expenditures and synchronize your mission goals with the public ledger.</p>
                <div class="flex items-center gap-3 text-[14px] font-black text-green-600 uppercase tracking-widest">Open Ledger <i class="fas fa-arrow-right text-[12px]"></i></div>
            </a>

            <!-- News Module -->
            <a href="announcements.php" class="glass group p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all border border-transparent hover:border-saffron/20 block">
                <div class="w-20 h-20 bg-saffron/5 text-saffron rounded-3xl flex items-center justify-center mb-10 group-hover:scale-110 group-hover:bg-saffron group-hover:text-white transition-all shadow-lg shadow-saffron/5">
                    <i class="fas fa-bullhorn text-3xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-nature mb-4">News Panel</h4>
                <p class="text-[15px] text-gray-600 font-medium leading-relaxed mb-8">Manage the header marquee and recent announcements across the portal.</p>
                <div class="flex items-center gap-3 text-[14px] font-black text-saffron uppercase tracking-widest">Open Module <i class="fas fa-arrow-right text-[12px]"></i></div>
            </a>

            <!-- Events Module -->
            <a href="events/index.php" class="glass group p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all border border-transparent hover:border-saffron/20 block">
                <div class="w-20 h-20 bg-purple-50 text-purple-600 rounded-3xl flex items-center justify-center mb-10 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all shadow-lg shadow-purple-600/5">
                    <i class="fas fa-calendar-alt text-3xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-nature mb-4">Upcoming Events</h4>
                <p class="text-[15px] text-gray-600 font-medium leading-relaxed mb-8">Schedule and manage upcoming festivals, shivirs, and special seva events.</p>
                <div class="flex items-center gap-3 text-[14px] font-black text-purple-600 uppercase tracking-widest">Manage Events <i class="fas fa-arrow-right text-[12px]"></i></div>
            </a>
            
            <a href="testimonials/index.php" class="glass group p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all border border-transparent hover:border-saffron/20 block">
                <div class="w-20 h-20 bg-gold/10 text-gold rounded-3xl flex items-center justify-center mb-10 group-hover:scale-110 group-hover:bg-gold group-hover:text-nature transition-all shadow-lg shadow-gold/5">
                    <i class="fas fa-quote-right text-3xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-nature mb-4">Testimonial Feed</h4>
                <p class="text-[15px] text-gray-600 font-medium leading-relaxed mb-8">Review and manage the voices of devotion from your global community.</p>
                <div class="flex items-center gap-3 text-[14px] font-black text-gold uppercase tracking-widest">Review Voices <i class="fas fa-arrow-right text-[12px]"></i></div>
            </a>

            <a href="team/index.php" class="glass group p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all border border-transparent hover:border-saffron/20 block">
                <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mb-10 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-lg shadow-blue-600/5">
                    <i class="fas fa-users text-3xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-nature mb-4">Management Team</h4>
                <p class="text-[15px] text-gray-600 font-medium leading-relaxed mb-8">Update profile details, designations, and roles of core leadership.</p>
                <div class="flex items-center gap-3 text-[14px] font-black text-blue-600 uppercase tracking-widest">Update Team <i class="fas fa-arrow-right text-[12px]"></i></div>
            </a>

            <a href="founders/index.php" class="glass group p-10 rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all border border-transparent hover:border-saffron/20 block">
                <div class="w-20 h-20 bg-amber-50 text-amber-600 rounded-3xl flex items-center justify-center mb-10 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all shadow-lg shadow-amber-600/5">
                    <i class="fas fa-user-shield text-3xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-nature mb-4">Founders Circle</h4>
                <p class="text-[15px] text-gray-600 font-medium leading-relaxed mb-8">Manage profiles of founding visionaries and their contributions.</p>
                <div class="flex items-center gap-3 text-[14px] font-black text-amber-600 uppercase tracking-widest">Honor Council <i class="fas fa-arrow-right text-[12px]"></i></div>
            </a>
        </div>

        <!-- RECENT DATA SECTION -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 pb-32">
            <!-- Latest Contact Requests -->
            <div class="glass p-10 rounded-[3rem] shadow-xl border border-white/40">
                <div class="flex justify-between items-center mb-10">
                    <h4 class="text-2xl font-bold text-nature flex items-center gap-4">
                        <i class="fas fa-envelope-open text-saffron"></i> New Leads
                    </h4>
                    <a href="contact_requests.php" class="text-[12px] uppercase font-black tracking-widest text-nature/40 hover:text-saffron transition-colors border-b-2 border-transparent hover:border-saffron">View All</a>
                </div>
                <div class="space-y-4">
                    <?php if (empty($recent_contacts)): ?>
                        <p class="text-gray-500 text-[15px] font-medium italic py-10 text-center">No recent contact leads in sanctuary.</p>
                    <?php else: ?>
                        <?php foreach ($recent_contacts as $rc): ?>
                            <div class="flex items-center justify-between p-5 bg-white/50 rounded-2xl border border-gray-100 group hover:bg-white hover:shadow-lg transition-all">
                                <div>
                                    <p class="font-bold text-nature text-[16px] mb-1"><?= htmlspecialchars($rc['name']) ?></p>
                                    <p class="text-[12px] text-gray-600 font-bold uppercase tracking-wider"><?= htmlspecialchars($rc['purpose']) ?></p>
                                </div>
                                <span class="text-[12px] font-black text-gray-400 italic"><?= date('d M Y', strtotime($rc['created_at'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Detailed Donations -->
            <div class="glass p-10 rounded-[3rem] shadow-xl border border-white/40">
                <div class="flex justify-between items-center mb-10">
                    <h4 class="text-2xl font-bold text-nature flex items-center gap-4">
                        <i class="fas fa-hand-holding-heart text-green-600"></i> Offerings
                    </h4>
                    <a href="donations.php" class="text-[12px] uppercase font-black tracking-widest text-nature/40 hover:text-green-600 transition-colors border-b-2 border-transparent hover:border-green-600">Digital Registry</a>
                </div>
                <div class="space-y-4">
                    <?php if (empty($recent_donations)): ?>
                        <p class="text-gray-500 text-[15px] font-medium italic py-10 text-center">No sacred donations recorded in registry.</p>
                    <?php else: ?>
                        <?php foreach ($recent_donations as $rd): ?>
                            <div class="flex items-center justify-between p-5 bg-white/50 rounded-2xl border border-gray-100 group hover:bg-white hover:shadow-lg transition-all">
                                <div>
                                    <p class="font-bold text-nature text-[16px] mb-1"><?= htmlspecialchars($rd['donor_name']) ?></p>
                                    <p class="text-[13px] text-green-600 font-black tracking-widest">₹<?= number_format($rd['amount']) ?></p>
                                </div>
                                <span class="text-[12px] font-black text-gray-400 italic"><?= date('M Y', strtotime($rd['created_at'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

</body>

</html>