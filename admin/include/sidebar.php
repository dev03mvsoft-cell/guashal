<!-- Mobile Header (Visible only on mobile) -->
<div class="md:hidden bg-nature text-white p-4 flex items-center justify-between sticky top-0 z-[60] shadow-lg">
    <div class="flex items-center gap-3">
        <img src="/asset/img/logo/logo.png" alt="Logo" class="w-8 h-8 object-contain bg-white rounded-lg p-1">
        <span style="font-family: 'Playfair Display';" class="text-lg font-bold text-gold">Admin Portal</span>
    </div>
    <button onclick="toggleMobileSidebar()" class="w-10 h-10 flex items-center justify-center bg-white/10 rounded-xl hover:bg-white/20 transition-all">
        <i class="fas fa-bars text-xl"></i>
    </button>
</div>

<!-- Sidebar Overlay (Mobile only) -->
<div id="sidebar-overlay" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[70] hidden transition-opacity duration-300 opacity-0"></div>

<!-- Sidebar (Simplified Institutional Architecture) -->
<aside id="main-sidebar" class="fixed inset-y-0 left-0 w-72 bg-nature text-white p-8 flex-shrink-0 z-[80] transform -translate-x-full transition-transform duration-300 ease-in-out md:relative md:translate-x-0 md:h-screen md:sticky md:top-0 md:overflow-y-auto border-r border-white/5 flex flex-col shadow-2xl sidebar-scroll">

    <style>
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        details summary::-webkit-details-marker {
            display: none;
        }
    </style>

    <div class="mb-8 border-b border-white/5 pb-8 flex items-center gap-4">
        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-lg p-2 flex-shrink-0 shadow-saffron/20">
            <img src="/asset/img/logo/logo.png" alt="Logo" class="w-full h-full object-contain drop-shadow-sm">
        </div>
        <div class="overflow-hidden">
            <h1 style="font-family: 'Playfair Display';" class="text-2xl font-bold text-gold tracking-tight truncate">Admin Portal</h1>
        </div>
    </div>

    <nav class="space-y-2 flex-1 pb-10">
        <?php $current_page = $_SERVER['SCRIPT_NAME']; ?>

        <!-- Dashboard -->
        <a href="/admin/index.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= (strpos($current_page, '/admin/index.php') !== false) ? 'bg-white/10 font-bold text-white shadow-lg' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-home w-5 text-gold/80"></i>
            <span class="text-xs uppercase tracking-widest">Dashboard</span>
        </a>

        <!-- Website Pages -->
        <?php $is_web = strpos($current_page, '/admin/gallery.php') !== false || strpos($current_page, '/admin/events/') !== false; ?>
        <details class="group" <?= $is_web ? 'open' : '' ?>>
            <summary class="flex items-center justify-between p-4 rounded-2xl cursor-pointer list-none transition-all <?= $is_web ? 'bg-white/5 font-bold text-white' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
                <div class="flex items-center gap-4">
                    <i class="fas fa-globe w-5 text-saffron/80"></i>
                    <span class="text-xs uppercase tracking-widest whitespace-nowrap">Website Edits</span>
                </div>
                <i class="fas fa-chevron-down text-[10px] group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="pl-6 pr-4 py-2 ml-6 mt-1 border-l border-white/10 space-y-4">
                <a href="/admin/gallery.php" class="block text-[11px] uppercase tracking-widest <?= strpos($current_page, '/admin/gallery.php') !== false ? 'text-saffron font-bold' : 'text-white/50 hover:text-saffron transition-colors' ?>">Gallery</a>
                <a href="/admin/events/index.php" class="block text-[11px] uppercase tracking-widest <?= strpos($current_page, '/admin/events/') !== false ? 'text-saffron font-bold' : 'text-white/50 hover:text-saffron transition-colors' ?>">Events</a>
            </div>
        </details>

        <!-- Our Info -->
        <?php $is_info = strpos($current_page, '/admin/team/') !== false || strpos($current_page, '/admin/founders/') !== false || strpos($current_page, '/admin/testimonials/') !== false; ?>
        <details class="group" <?= $is_info ? 'open' : '' ?>>
            <summary class="flex items-center justify-between p-4 rounded-2xl cursor-pointer list-none transition-all <?= $is_info ? 'bg-white/5 font-bold text-white' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
                <div class="flex items-center gap-4">
                    <i class="fas fa-info-circle w-5 text-gold/80"></i>
                    <span class="text-xs uppercase tracking-widest whitespace-nowrap">Our Info</span>
                </div>
                <i class="fas fa-chevron-down text-[10px] group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="pl-6 pr-4 py-2 ml-6 mt-1 border-l border-white/10 space-y-4">
                <a href="/admin/team/index.php" class="block text-[11px] uppercase tracking-widest <?= strpos($current_page, '/admin/team/') !== false ? 'text-saffron font-bold' : 'text-white/50 hover:text-saffron transition-colors' ?>">Team</a>
                <a href="/admin/founders/index.php" class="block text-[11px] uppercase tracking-widest <?= strpos($current_page, '/admin/founders/') !== false ? 'text-saffron font-bold' : 'text-white/50 hover:text-saffron transition-colors' ?>">Founders</a>
                <a href="/admin/testimonials/index.php" class="block text-[11px] uppercase tracking-widest <?= strpos($current_page, '/admin/testimonials/') !== false ? 'text-saffron font-bold' : 'text-white/50 hover:text-saffron transition-colors' ?>">Testimonials</a>
            </div>
        </details>

        <!-- Donations -->
        <?php $is_don = strpos($current_page, '/admin/transparency/') !== false || strpos($current_page, '/admin/contributions.php') !== false || strpos($current_page, '/admin/seva/') !== false || strpos($current_page, '/admin/donations.php') !== false; ?>
        <details class="group" <?= $is_don ? 'open' : '' ?>>
            <summary class="flex items-center justify-between p-4 rounded-2xl cursor-pointer list-none transition-all <?= $is_don ? 'bg-white/5 font-bold text-white' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
                <div class="flex items-center gap-4">
                    <i class="fas fa-hand-holding-heart w-5 text-saffron/80"></i>
                    <span class="text-xs uppercase tracking-widest whitespace-nowrap">Donations</span>
                </div>
                <i class="fas fa-chevron-down text-[10px] group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="pl-6 pr-4 py-2 ml-6 mt-1 border-l border-white/10 space-y-4">
                <a href="/admin/donations.php" class="block text-[11px] uppercase tracking-widest <?= strpos($current_page, '/admin/donations.php') !== false ? 'text-saffron font-bold' : 'text-white/50 hover:text-saffron transition-colors' ?>">Donation Logs</a>
                <a href="/admin/transparency/index.php" class="block text-[11px] uppercase tracking-widest <?= strpos($current_page, '/admin/transparency/') !== false ? 'text-saffron font-bold' : 'text-white/50 hover:text-saffron transition-colors' ?>">Transparency</a>
                <a href="/admin/contributions.php" class="block text-[11px] uppercase tracking-widest <?= strpos($current_page, '/admin/contributions.php') !== false ? 'text-saffron font-bold' : 'text-white/50 hover:text-saffron transition-colors' ?>">Contributions</a>
                <a href="/admin/seva/index.php" class="block text-[11px] uppercase tracking-widest <?= strpos($current_page, '/admin/seva/') !== false ? 'text-saffron font-bold' : 'text-white/50 hover:text-saffron transition-colors' ?>">Add Seva</a>
                <a href="/admin/donors/index.php" class="block text-[11px] uppercase tracking-widest <?= strpos($current_page, '/admin/donors/') !== false ? 'text-saffron font-bold' : 'text-white/50 hover:text-saffron transition-colors' ?>">Donate Wall</a>
            </div>
        </details>

        <!-- Standalone -->
        <a href="/admin/announcements.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= (strpos($current_page, '/admin/announcements.php') !== false) ? 'bg-white/10 font-bold text-white' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-bullhorn w-5 text-white/50"></i>
            <span class="text-xs uppercase tracking-widest">Announcements</span>
        </a>

        <a href="/admin/contact_requests.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= (strpos($current_page, '/admin/contact_requests.php') !== false) ? 'bg-white/10 font-bold text-white' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-envelope-open w-5 text-white/50"></i>
            <span class="text-xs uppercase tracking-widest">Contact Leads</span>
        </a>

        <?php if (($_SESSION['admin_role'] ?? '') === 'Super Admin'): ?>
            <a href="/admin/manage_users.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= (strpos($current_page, '/admin/manage_users.php') !== false) ? 'bg-white/10 font-bold text-white' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
                <i class="fas fa-user-lock w-5 text-white/50"></i>
                <span class="text-xs uppercase tracking-widest">Access Mgmt</span>
            </a>
        <?php endif; ?>

        <!-- Portal Actions -->
        <div class="pt-8 border-t border-white/5 mt-8 space-y-2">
            <a href="/" target="_blank" class="flex items-center gap-4 text-white/40 hover:text-white p-3 hover:bg-white/5 rounded-2xl transition-all">
                <i class="fas fa-external-link-alt w-5 text-[12px]"></i>
                <span class="text-[11px] font-black uppercase tracking-widest">Live Website</span>
            </a>

            <a href="/admin/logout.php" class="flex items-center gap-4 text-red-400 p-3 hover:bg-red-500/10 rounded-2xl transition-all mt-2 font-black border border-transparent hover:border-red-900/20 group">
                <i class="fas fa-power-off w-5 text-[12px] group-hover:scale-110 transition-transform"></i>
                <span class="text-[11px] uppercase tracking-widest">Secure Logout</span>
            </a>
        </div>
    </nav>
</aside>

<script>
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('main-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (sidebar.classList.contains('-translate-x-full')) {
            // Open
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.add('opacity-100');
            }, 10);
        } else {
            // Close
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }
    }
</script>

<!-- Global Message Listener -->
<?php if (!empty($message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => notify('success', '<?= addslashes($message) ?>'));
    </script>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => notify('error', '<?= addslashes($error) ?>'));
    </script>
<?php endif; ?>