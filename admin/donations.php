<?php
require_once 'include/auth.php';
require_once '../config/db.php';

$message = '';
$error = '';

// Handle Delete/Bulk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM donations WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Donation record removed.";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM donations WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $message = count($ids) . " donations purged successfully!";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}

// Fetch all with Seva titles
$donations = [];
try {
    $stmt = $pdo->query("SELECT d.*, s.title_en as seva_title 
                         FROM donations d 
                         LEFT JOIN seva_options s ON d.seva_id = s.id 
                         ORDER BY d.created_at DESC");
    if ($stmt) $donations = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Table 'donations' not found. Please run setup.php";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sacred Donations - Gaushala Admin</title>
    <?php include 'include/head.php'; ?>
</head>

<body class="md:h-screen flex flex-col md:flex-row bg-[#fdfaf7] overflow-hidden">
    <?php include 'include/sidebar.php'; ?>
    <main class="flex-1 p-6 lg:p-16 overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
                <div>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold">Divine <span class="text-saffron italic">Offerings</span></h1>
                    <p class="text-gray-400 mt-2 text-sm tracking-widest uppercase font-black italic">Detailed donation registry from the portal</p>
                </div>
                <div class="flex items-center gap-4">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected?', 'The selected records will be removed forever.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10 transition-all">
                            <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-8 h-8 rounded-lg flex items-center justify-center hover:scale-110 transition-transform"><i class="fas fa-trash-alt text-[12px]"></i></button>
                        </div>
                    </form>
                </div>
            </header>

            <div class="mb-8 flex items-center px-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded-lg border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                    <span class="text-[12px] font-black uppercase tracking-widest text-nature/40 group-hover:text-nature transition-colors">Select All Offerings</span>
                </label>
            </div>

            <div class="space-y-4">
                <?php if (empty($donations)): ?>
                    <div class="glass p-20 rounded-[3rem] text-center border-2 border-dashed border-gray-100">
                        <i class="fas fa-heart text-5xl text-nature/10 mb-6"></i>
                        <p class="text-gray-300 font-bold uppercase tracking-widest text-xs">No offerings found</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($donations as $d): ?>
                        <div class="glass p-8 rounded-[2.5rem] flex justify-between items-center group hover:shadow-xl transition-all border border-gray-50">
                            <div class="flex items-center gap-8">
                                <div class="w-16 h-16 bg-saffron/10 text-saffron rounded-full flex items-center justify-center font-black text-xl shadow-inner italic">
                                    <?= $d['currency_type'] === 'INR' ? '₹' : '$' ?>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-black text-nature leading-none mb-2">
                                        <?= number_format($d['amount']) ?> <span class="text-gray-300 font-normal italic"><?= $d['currency_type'] ?></span>
                                    </h4>
                                    <p class="text-nature font-bold text-base mb-1 italic"><?= htmlspecialchars($d['donor_name']) ?></p>
                                    <p class="text-xs text-gray-500 font-bold tracking-widest uppercase">
                                        <i class="far fa-calendar-alt mr-2"></i><?= date('M d, Y', strtotime($d['donation_date'] ?: $d['created_at'])) ?> 
                                        <span class="mx-3">•</span> 
                                        <i class="far fa-heart mr-2 text-saffron"></i><?= htmlspecialchars($d['seva_title'] ?: 'General Seva') ?>
                                    </p>
                                    <div class="mt-4 flex gap-4 text-[11px] font-black uppercase tracking-widest text-gray-400">
                                        <span><i class="fas fa-phone-alt mr-2 text-gold"></i><?= htmlspecialchars($d['phone'] ?: 'N/A') ?></span>
                                        <span><i class="fas fa-envelope mr-2 text-gold"></i><?= htmlspecialchars($d['email'] ?: 'N/A') ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <form method="POST" onsubmit="return confirmAction(event, 'Delete donation record?', 'This entry will be permanently removed.');" class="inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                    <button type="submit" class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Delete"><i class="fas fa-trash text-sm"></i></button>
                                </form>
                                <input type="checkbox" name="selected_ids[]" value="<?= $d['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-8 h-8 rounded-2xl border-2 border-nature/5 text-saffron focus:ring-saffron cursor-pointer shadow-inner">
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>
