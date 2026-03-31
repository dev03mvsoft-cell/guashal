<?php
require_once 'include/auth.php';
require_once '../config/db.php';

$message = '';
$error = '';

// Handle Delete/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM contact_requests WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Request removed.";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM contact_requests WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $message = count($ids) . " requests purged successfully!";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'mark_read') {
        $id = $_POST['id'];
        try {
            $stmt = $pdo->prepare("UPDATE contact_requests SET status = 'Read' WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Marked as read.";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}

// Fetch all
$requests = [];
try {
    $stmt = $pdo->query("SELECT * FROM contact_requests ORDER BY created_at DESC");
    if ($stmt) $requests = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Table 'contact_requests' not found. Please run setup.php";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Requests - Gaushala Admin</title>
    <?php include 'include/head.php'; ?>
</head>

<body class="md:h-screen flex flex-col md:flex-row bg-[#fdfaf7] overflow-hidden">
    <?php include 'include/sidebar.php'; ?>
    <main class="flex-1 p-6 lg:p-16 overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
                <div>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold">Contact <span class="text-saffron italic">Requests</span></h1>
                    <p class="text-gray-400 mt-2 text-sm tracking-widest uppercase font-black italic">Manage messages from the portal</p>
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
                    <span class="text-[12px] font-black uppercase tracking-widest text-nature/40 group-hover:text-nature transition-colors">Select All Messages</span>
                </label>
            </div>

            <div class="space-y-4">
                <?php if (empty($requests)): ?>
                    <div class="glass p-20 rounded-[3rem] text-center border-2 border-dashed border-gray-100">
                        <i class="fas fa-envelope-open text-5xl text-nature/10 mb-6"></i>
                        <p class="text-gray-300 font-bold uppercase tracking-widest text-xs">No requests found</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                        <div class="glass p-8 rounded-[2.5rem] flex justify-between items-start group hover:shadow-xl transition-all border border-gray-50 <?= $r['status'] === 'New' ? 'border-l-4 border-l-saffron' : '' ?>">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-4">
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] px-3 py-1 rounded-full <?= $r['status'] === 'New' ? 'bg-saffron text-white' : 'bg-gray-100 text-gray-400' ?>">
                                        <?= $r['status'] ?>
                                    </span>
                                    <span class="text-xs text-gray-400 font-bold italic"><?= date('M d, Y • h:i A', strtotime($r['created_at'])) ?></span>
                                </div>
                                <h4 class="text-xl font-bold text-nature mb-1"><?= htmlspecialchars($r['name']) ?></h4>
                                <p class="text-saffron font-bold text-sm mb-4"><?= htmlspecialchars($r['email']) ?> • <span class="text-gray-500 font-medium"><?= htmlspecialchars($r['purpose']) ?></span></p>
                                <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100/50">
                                    <p class="text-nature/70 text-base leading-relaxed italic">"<?= nl2br(htmlspecialchars($r['message'])) ?>"</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-center gap-3 ml-8">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="w-12 h-12 bg-nature/5 text-nature rounded-2xl flex items-center justify-center hover:bg-nature hover:text-white transition-all shadow-sm" title="Mark as Read"><i class="fas fa-check text-sm"></i></button>
                                </form>
                                <form method="POST" onsubmit="return confirmAction(event, 'Delete request?', 'This message will be permanently removed.');" class="inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Delete"><i class="fas fa-trash text-sm"></i></button>
                                </form>
                                <input type="checkbox" name="selected_ids[]" value="<?= $r['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-6 h-6 rounded-xl border-2 border-nature/5 text-saffron focus:ring-saffron cursor-pointer shadow-inner mt-2">
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>
