<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

$message = '';
$error = '';

// Handle Settings Update (Raised & Goal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_settings') {
        $raised = (int)$_POST['donation_raised'];
        $goal = (int)$_POST['donation_goal'];
        try {
            $stmt = $pdo->prepare("UPDATE transparency_stats SET raised = ?, goal = ? WHERE id = 1");
            $stmt->execute([$raised, $goal]);
            $message = "Mission target synchronized to dedicated ledger!";
        } catch (PDOException $e) {
            $error = "Write Error: " . $e->getMessage();
        }
    }

    if ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        try {
            $pdo->prepare("DELETE FROM transparency_materials WHERE id = ?")->execute([$id]);
            $message = "Expenditure record archived.";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM transparency_materials WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $message = count($ids) . " expenditure records purged successfully!";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}

// Fetch Stats from dedicated table
$stats = null;
try {
    $stats = $pdo->query("SELECT * FROM transparency_stats WHERE id = 1")->fetch();
} catch (Exception $e) {
}
$raised_val = $stats['raised'] ?? 0;
$goal_val = $stats['goal'] ?? 10000000;

// Fetch Materials
$materials = [];
try {
    $materials = $pdo->query("SELECT * FROM transparency_materials ORDER BY sort_order ASC, id ASC")->fetchAll();
} catch (Exception $e) {
    $error = "Module disconnected.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Ledger - Admin Dashboard</title>
    <?php include '../include/head.php'; ?>
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

<body class="bg-[#f8fafc] flex">
    <?php include '../include/sidebar.php'; ?>
    <main class="flex-1 p-6 lg:p-12 overflow-y-auto">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.3em] text-[13px] mb-2 block">Financial Integrity</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature leading-tight">Sacred <span class="text-saffron italic">Ledger</span></h1>
                    <p class="text-nature/40 mt-1 text-[13px] font-medium tracking-wide">Centralized mission accounting and transparency control</p>
                </div>
                <div class="flex items-center gap-4">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected records?', 'The selected entries will be removed from the ledger.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10 transition-all">
                            <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-10 h-10 rounded-xl flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="fas fa-trash-alt text-[12px]"></i>
                            </button>
                        </div>
                    </form>
                    <a href="editor.php" class="bg-saffron text-white px-8 py-4 rounded-xl font-bold flex items-center gap-3 shadow-lg shadow-saffron/20 hover:scale-105 transition-all">
                        <i class="fas fa-file-invoice-dollar text-xs"></i> <span>New Expenditure</span>
                    </a>
                </div>
            </header>

            <?php if ($message): ?>
                <div class="bg-nature/10 text-nature p-4 rounded-xl mb-8 font-bold text-sm border border-nature/20 animate-fade-in">
                    <i class="fas fa-check-circle mr-2"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Fundraising Sync Card -->
            <div class="glass-card mb-10 !py-8">
                <div class="flex items-center gap-3 mb-6">
                    <i class="fas fa-bullseye text-saffron text-lg"></i>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-nature/60">Mission Goal Synchronization</h3>
                </div>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <input type="hidden" name="action" value="update_settings">
                    <div class="space-y-2">
                        <label class="text-[11px] uppercase font-bold tracking-widest text-gray-400 ml-2">Raised To Date (₹)</label>
                        <input type="number" name="donation_raised" value="<?= $raised_val ?>" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-6 py-3 font-medium text-nature outline-none focus:border-saffron focus:ring-4 focus:ring-saffron/5 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] uppercase font-bold tracking-widest text-gray-400 ml-2">Target Goal (₹)</label>
                        <input type="number" name="donation_goal" value="<?= $goal_val ?>" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-6 py-3 font-medium text-nature outline-none focus:border-saffron focus:ring-4 focus:ring-saffron/5 transition-all">
                    </div>
                    <button type="submit" class="bg-nature text-white h-[48px] rounded-xl flex items-center justify-center gap-2 font-bold uppercase text-[12px] tracking-widest hover:bg-black transition-all shadow-lg shadow-nature/20">
                        <i class="fas fa-sync-alt"></i> Update Targets
                    </button>
                </form>
            </div>

            <div class="mb-6 flex items-center px-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                    <span class="text-[12px] font-black uppercase tracking-widest text-nature/40 group-hover:text-nature transition-colors">Select All Chronicles</span>
                </label>
            </div>

            <div class="glass-card !p-0 overflow-hidden shadow-sm">
                <table class="system-table">
                    <thead>
                        <tr>
                            <th class="w-12 h-16 pl-6">#</th>
                            <th>Expenditure Channel</th>
                            <th class="w-48 text-center">Quantities</th>
                            <th class="w-48 text-right">Channel Value</th>
                            <th class="w-32 text-right pr-6">Management</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($materials)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-24">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-nature/5 text-nature/10 rounded-full flex items-center justify-center text-4xl mb-4 italic"><i class="fas fa-file-invoice"></i></div>
                                        <p class="text-nature/30 uppercase font-black tracking-widest text-[11px]">Accounting Archive Empty</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($materials as $m): 
                                $target_v = isset($m['target_val']) ? (int)$m['target_val'] : 10000;
                                $current_v = isset($m['current_val']) ? (int)$m['current_val'] : 0;
                                $perc = ($target_v > 0) ? ($current_v / $target_v) * 100 : 0;
                                $perc = min(100, $perc);
                            ?>
                                <tr class="group">
                                    <td class="pl-6">
                                        <input type="checkbox" name="selected_ids[]" value="<?= $m['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded border-2 border-nature/10 text-saffron focus:ring-saffron cursor-pointer">
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl <?= $m['color_class'] ?: 'bg-nature' ?> flex items-center justify-center text-white/40 text-[10px] font-black shadow-sm">#<?= $m['sort_order'] ?></div>
                                            <div class="flex-1">
                                                <div class="font-medium text-nature text-[16px] leading-tight mb-2"><?= htmlspecialchars($m['name_en']) ?></div>
                                                <div class="flex items-center gap-3">
                                                    <div class="w-32 bg-slate-50 h-1.5 rounded-full overflow-hidden border border-slate-100">
                                                        <div class="<?= $m['color_class'] ?: 'bg-nature' ?> h-full transition-all duration-1000" style="width: <?= $perc ?>%"></div>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-nature/30 uppercase tracking-widest whitespace-nowrap">₹<?= number_format($current_v) ?> / ₹<?= number_format($target_v) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="text-[14px] font-medium text-nature"><?= htmlspecialchars($m['quantity']) ?> <span class="text-[11px] text-nature/30"><?= htmlspecialchars($m['unit_name'] ?? 'Units') ?></span></div>
                                        <div class="text-[11px] font-normal text-nature/40 italic">@ ₹<?= number_format((float)$m['unit_price']) ?></div>
                                    </td>
                                    <td class="text-right">
                                        <div class="text-[16px] font-medium text-nature tracking-tight">₹<?= number_format((float)str_replace(',', '', $m['total_amount'])) ?></div>
                                    </td>
                                    <td class="pr-6 text-right">
                                        <div class="flex justify-end items-center gap-3">
                                            <a href="editor.php?id=<?= $m['id'] ?>" class="w-10 h-10 rounded-xl bg-nature/5 text-nature flex items-center justify-center hover:bg-nature hover:text-white transition-all shadow-sm border border-nature/10">
                                                <i class="fas fa-edit text-[14px]"></i>
                                            </a>
                                            <form method="POST" onsubmit="return confirmAction(event, 'Purge Record?', 'This expenditure entry will be erased.');" class="inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $m['id'] ?>">
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
    </main>
</body>
</html>