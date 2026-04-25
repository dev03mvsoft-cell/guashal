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
        } catch (PDOException $e) { $error = $e->getMessage(); }
    }

    if ($_POST['action'] === 'mark_read') {
        $id = $_POST['id'];
        try {
            $stmt = $pdo->prepare("UPDATE contact_requests SET status = 'Read' WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Marked as read.";
        } catch (PDOException $e) { $error = $e->getMessage(); }
    }
}

// Fetch all
$requests = [];
try {
    $stmt = $pdo->query("SELECT * FROM contact_requests ORDER BY created_at DESC");
    if ($stmt) $requests = $stmt->fetchAll();
} catch (PDOException $e) { $error = "Communication archive unreachable."; }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Registry - Gaushala Admin</title>
    <?php include 'include/head.php'; ?>
    <style>
        .system-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.25rem;
        }
        .system-table th {
            text-align: left;
            padding: 1rem 1rem;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #ffffff;
            background: #2c4c3b;
        }
        .system-table thead tr th:first-child { border-radius: 1.25rem 0 0 1.25rem; }
        .system-table thead tr th:last-child { border-radius: 0 1.25rem 1.25rem 0; }
        
        .system-table td {
            padding: 0.5rem 1rem;
            background: #fff;
            vertical-align: middle;
            transition: all 0.3s;
        }
        .system-table tr:hover td {
            background: #f1f5f9;
        }
        .system-table tr td:first-child { border-radius: 1.25rem 0 0 1.25rem; }
        .system-table tr td:last-child { border-radius: 0 1.25rem 1.25rem 0; }
        
        .glass-card {
            background: white;
            border-radius: 2rem;
            padding: 2rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="bg-[#f8fafc] flex flex-col md:flex-row md:h-screen md:overflow-hidden">
    <?php include 'include/sidebar.php'; ?>
    <main class="flex-1 p-4 lg:p-12 md:overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.3em] text-[13px] mb-2 block">Communication Hub</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature leading-tight">Contact <span class="text-saffron italic">Registry</span></h1>
                    <p class="text-nature/40 mt-1 text-[13px] font-medium tracking-wide">Registry of public inquiries and devotee messages</p>
                </div>
                <div class="flex items-center gap-4">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected requests?', 'The entries will be wiped from communication logs.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10 transition-all">
                            <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-10 h-10 rounded-xl flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="fas fa-trash-alt text-[12px]"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </header>

            <?php if ($message): ?>
                <div class="bg-nature/10 text-nature p-4 rounded-xl mb-8 font-bold text-sm border border-nature/20 animate-fade-in">
                    <i class="fas fa-check-circle mr-2"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="glass-card !p-0 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="system-table min-w-[800px] lg:min-w-full">
                    <thead>
                        <tr>
                            <th class="w-12 h-16 pl-6">
                                <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded border-2 border-white/20 text-saffron focus:ring-saffron bg-transparent cursor-pointer">
                            </th>
                            <th class="w-48">Received</th>
                            <th>Devotee Details</th>
                            <th>Inquiry Topic</th>
                            <th class="w-24">Status</th>
                            <th class="w-32 text-right pr-6">Management</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-20 text-nature/20 uppercase text-[12px] font-medium italic">No messages recorded yet</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $r): ?>
                                <tr class="<?= $r['status'] === 'New' ? 'bg-orange-50/20' : '' ?> group">
                                    <td class="pl-6">
                                        <input type="checkbox" name="selected_ids[]" value="<?= $r['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded border-2 border-nature/10 text-saffron focus:ring-saffron cursor-pointer">
                                    </td>
                                    <td>
                                        <div class="text-[14px] text-gray-700 font-medium uppercase tracking-tight leading-none mb-1"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
                                        <div class="text-[12px] text-nature/40 font-normal"><?= date('h:i A', strtotime($r['created_at'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="text-[16px] font-medium text-nature leading-tight mb-1"><?= htmlspecialchars($r['name']) ?></div>
                                        <div class="text-[13px] text-nature/60 lowercase font-normal tracking-tight"><?= htmlspecialchars($r['email']) ?></div>
                                    </td>
                                    <td>
                                        <div class="text-[14px] font-medium text-nature/70 line-clamp-1 italic mb-1"><?= htmlspecialchars($r['subject']) ?></div>
                                        <div class="text-[12px] text-nature/40 line-clamp-1 font-normal"><?= htmlspecialchars($r['message']) ?></div>
                                    </td>
                                    <td>
                                        <span class="px-3 py-1 rounded-full text-[10px] font-medium uppercase tracking-widest <?= $r['status'] == 'Read' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600 animate-pulse' ?>">
                                            <?= htmlspecialchars($r['status']) ?>
                                        </span>
                                    </td>
                                    <td class="pr-6 text-right">
                                        <div class="flex justify-end items-center gap-3">
                                            <?php if ($r['status'] !== 'Read'): ?>
                                                <form method="POST">
                                                    <input type="hidden" name="action" value="mark_read">
                                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                                    <button type="submit" class="w-10 h-10 rounded-xl bg-nature/5 text-nature flex items-center justify-center hover:bg-nature hover:text-white transition-all shadow-sm border border-nature/10" title="Mark Read">
                                                        <i class="fas fa-check text-[14px]"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" onsubmit="return confirmAction(event, 'Purge Record?', 'This request will be removed from history.');" class="inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                                <button type="submit" class="w-10 h-10 rounded-xl bg-red-100/50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm border border-red-200">
                                                    <i class="fas fa-trash-alt text-[14px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
</body>
</html>
