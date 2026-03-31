<!-- Sidebar (Simplified Institutional Architecture) -->
<aside class="w-full md:w-72 bg-nature text-white p-8 flex-shrink-0 md:h-screen md:sticky md:top-0 md:overflow-y-auto border-r border-white/5 flex flex-col shadow-2xl">

    <div class="mb-12 border-b border-white/5 pb-8">
        <h1 style="font-family: 'Playfair Display';" class="text-3xl font-bold text-gold tracking-tight">Admin.</h1>
        <p class="text-[12px] uppercase tracking-widest text-white/40 mt-1 font-black"><?= $_SESSION['admin_name'] ?? 'Administrator' ?> • <span class="text-saffron"><?= $_SESSION['admin_role'] ?? 'Super Admin' ?></span></p>
    </div>

    <nav class="space-y-4 flex-1">
        <?php $current_page = $_SERVER['SCRIPT_NAME']; ?>
        <a href="/admin/index.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= (strpos($current_page, '/admin/index.php') !== false) ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-th-large w-5 text-gold/80"></i>
            <span class="text-xs uppercase tracking-[0.1em]">Summary</span>
        </a>
        <a href="/admin/announcements.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= (strpos($current_page, '/admin/announcements.php') !== false) ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-bullhorn w-5 text-saffron/80"></i>
            <span class="text-xs uppercase tracking-[0.1em]">News & Alerts</span>
        </a>
        <a href="/admin/gallery.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= (strpos($current_page, '/admin/gallery.php') !== false) ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-images w-5"></i>
            <span class="text-xs uppercase tracking-[0.1em]">Visual Gallery</span>
        </a>
        <a href="/admin/events/index.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= strpos($current_page, '/admin/events/') !== false ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-calendar-alt w-5"></i>
            <span class="text-xs uppercase tracking-[0.1em]">Chronicle</span>
        </a>
        <a href="/admin/seva/index.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= strpos($current_page, '/admin/seva/') !== false ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-hand-holding-heart w-5 text-saffron"></i>
            <span class="text-xs uppercase tracking-[0.1em]">Seva Options</span>
        </a>
        <a href="/admin/founders/index.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= strpos($current_page, '/admin/founders/') !== false ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-user-shield w-5 text-gold"></i>
            <span class="text-xs uppercase tracking-[0.1em]">The Council</span>
        </a>
        <a href="/admin/team/index.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= strpos($current_page, '/admin/team/') !== false ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-users w-5"></i>
            <span class="text-xs uppercase tracking-[0.1em]">Sanctuary Team</span>
        </a>
        <a href="/admin/testimonials/index.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= strpos($current_page, '/admin/testimonials/') !== false ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-quote-left w-5"></i>
            <span class="text-xs uppercase tracking-[0.1em]">Devotee Voices</span>
        </a>
        <a href="/admin/transparency/index.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= strpos($current_page, '/admin/transparency/') !== false ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-hand-holding-heart w-5 text-saffron"></i>
            <span class="text-xs uppercase tracking-[0.1em]">Transparency</span>
        </a>
        <a href="/admin/contributions.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= (strpos($current_page, '/admin/contributions.php') !== false) ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-heart text-gold w-5"></i>
            <span class="text-xs uppercase tracking-[0.1em]">Blessing Log</span>
        </a>
        <a href="/admin/donations.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= (strpos($current_page, '/admin/donations.php') !== false) ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-hand-holding-usd text-saffron w-5"></i>
            <span class="text-xs uppercase tracking-[0.1em]">Detailed Donations</span>
        </a>
        <a href="/admin/contact_requests.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= (strpos($current_page, '/admin/contact_requests.php') !== false) ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
            <i class="fas fa-envelope-open text-white/50 w-5"></i>
            <span class="text-xs uppercase tracking-[0.1em]">Contact Leads</span>
        </a>

        <?php if (($_SESSION['admin_role'] ?? '') === 'Super Admin'): ?>
            <a href="/admin/manage_users.php" class="flex items-center gap-4 group p-4 rounded-2xl transition-all <?= (strpos($current_page, '/admin/manage_users.php') !== false) ? 'sidebar-active shadow-lg font-bold' : 'text-white/60 hover:text-white hover:bg-white/5' ?>">
                <i class="fas fa-user-lock w-5 text-white/50"></i>
                <span class="text-xs uppercase tracking-[0.1em]">Access Management</span>
            </a>
        <?php endif; ?>

        <div class="pt-8 border-t border-white/5 mt-8 space-y-2">
            <p class="text-[12px] uppercase tracking-widest text-white/20 mb-4 px-3 font-bold">Public Portal</p>
            <a href="/" class="flex items-center gap-4 text-white/40 hover:text-white p-3 hover:bg-white/5 rounded-2xl transition-all">
                <i class="fas fa-external-link-alt w-5 text-[12px]"></i>
                <span class="text-[11px] font-black uppercase tracking-widest">Live Website</span>
            </a>

            <a href="/admin/logout.php" class="flex items-center gap-4 text-red-400 p-4 hover:bg-red-500/10 rounded-2xl transition-all mt-4 font-black border border-transparent hover:border-red-900/20">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span class="text-xs uppercase tracking-widest">Logout</span>
            </a>
        </div>
    </nav>
</aside>

<!-- Global Message Listener: Bridges PHP state to High-Fidelity UI Notifications -->
<?php if (!empty($message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            notify('success', '<?= addslashes($message) ?>');
        });
    </script>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            notify('error', '<?= addslashes($error) ?>');
        });
    </script>
<?php endif; ?>