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
        .premium-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 1rem;
        }

        .premium-table tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .premium-table tr td {
            background: white;
            padding: 1.5rem 2rem;
            border-top: 1px solid rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }

        .premium-table tr td:first-child {
            border-left: 1px solid rgba(0, 0, 0, 0.03);
            border-radius: 2.5rem 0 0 2.5rem;
        }

        .premium-table tr td:last-child {
            border-right: 1px solid rgba(0, 0, 0, 0.03);
            border-radius: 0 2.5rem 2.5rem 0;
        }

        .premium-table tr:hover {
            transform: scale(1.015);
            box-shadow: 0 20px 40px rgba(44, 76, 59, 0.05);
            z-index: 10;
            position: relative;
        }

        .premium-table tr:hover td {
            border-color: #FF6A00/20;
            background: #fffcf9;
        }

        .stat-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
    </style>
</head>

<body class="md:h-screen bg-[#f9f7f4] flex flex-col md:flex-row shadow-inner overflow-hidden">
    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-16 overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-16 gap-8">
                <div>
                    <h1 style="font-family: 'Playfair Display';" class="text-5xl font-bold text-nature leading-tight">Financial <span class="italic text-saffron">Transparency</span></h1>
                    <p class="text-gray-400 mt-2 text-[12px] tracking-widest uppercase font-black italic">centralized mission accounting control</p>
                </div>
                <div class="flex items-center gap-6">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected records?', 'The selected expenditure entries will be erased from the ledger.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10">
                            <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-8 h-8 rounded-lg flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="fas fa-trash-alt text-[12px]"></i>
                            </button>
                        </div>
                    </form>
                    <a href="editor.php" class="bg-nature text-white px-12 py-5 rounded-3xl font-black uppercase tracking-widest text-[15px] shadow-2xl shadow-nature/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-4">
                        <i class="fa-solid fa-plus text-xs"></i> New Expenditure Entry
                    </a>
                </div>
            </header>

            <div class="mb-12 flex items-center px-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded-lg border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                    <span class="text-[12px] font-black uppercase tracking-widest text-nature/40 group-hover:text-nature transition-colors">Select All Archives</span>
                </label>
            </div>

            <?php if ($message): ?>
                <div class="bg-nature text-white p-6 rounded-[2.5rem] text-[12px] mb-16 flex items-center gap-4 border-l-8 border-saffron font-black uppercase tracking-widest animate-pulse shadow-lg">
                    <i class="fa-solid fa-circle-check text-lg text-saffron"></i> <?= $message ?>
                </div>
            <?php endif; ?>

            <!-- Fundraising Target Tracker Card -->
            <div class="mb-14 glass p-10 md:p-14 rounded-[5rem] border-t-8 border-saffron shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-20 -top-20 w-[400px] h-[400px] bg-saffron/5 rounded-full blur-[80px]"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-4">
                        <i class="fa-solid fa-bullseye text-saffron text-2xl"></i>
                        <h3 class="text-xl font-black uppercase tracking-widest text-nature/40 italic">Fundraising Synchronization</h3>
                    </div>
                    <form method="POST" class="flex flex-col lg:flex-row gap-8 items-end">
                        <input type="hidden" name="action" value="update_settings">
                        <div class="flex-1 w-full space-y-3">
                            <label class="text-[12px] uppercase font-black tracking-widest text-gray-400 ml-4">Raised To Date (₹)</label>
                            <input type="number" name="donation_raised" value="<?= $raised_val ?>" class="w-full bg-white border border-nature/5 rounded-3xl p-6 text-2xl font-bold text-nature outline-none focus:border-saffron focus:ring-4 focus:ring-saffron/10 transition-all text-center placeholder-nature/10">
                        </div>
                        <div class="flex-1 w-full space-y-3">
                            <label class="text-[12px] uppercase font-black tracking-widest text-gray-400 ml-4">Target Mission Goal (₹)</label>
                            <input type="number" name="donation_goal" value="<?= $goal_val ?>" class="w-full bg-white border border-nature/5 rounded-3xl p-6 text-2xl font-bold text-nature outline-none focus:border-saffron focus:ring-4 focus:ring-saffron/10 transition-all text-center placeholder-nature/10">
                        </div>
                        <button type="submit" class="bg-saffron text-white h-[84px] aspect-square rounded-3xl flex items-center justify-center hover:bg-black transition-all shadow-xl shadow-saffron/20 group-hover:scale-105">
                            <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Ledger Entries (Tabular Form) -->
            <div class="flex items-center gap-6 mb-12">
                <h2 class="text-[12px] font-black uppercase tracking-[0.6em] text-nature/30 whitespace-nowrap">Sacred Accounting Ledger</h2>
                <div class="h-px flex-1 bg-nature/[0.04]"></div>
                <span class="text-[12px] font-black uppercase tracking-widest text-gray-300"><?= count($materials) ?> Entries</span>
            </div>

            <?php if (empty($materials)): ?>
                <div class="glass p-32 rounded-[5rem] border-2 border-dashed border-gray-100 text-center flex flex-col items-center justify-center">
                    <i class="fa-solid fa-file-invoice-dollar text-8xl text-gray-100 mb-10"></i>
                    <h3 class="text-2xl font-bold text-nature mb-4 uppercase tracking-widest">Accounting Archive Empty</h3>
                    <p class="text-gray-400 text-[15px] max-w-sm leading-relaxed">Financial transparency records await your first sanctified entry.</p>
                </div>
            <?php else: ?>
                <div class="overflow-visible pb-32 px-4">
                    <table class="premium-table">
                        <thead>
                            <tr class="text-[12px] font-black uppercase tracking-[0.3em] text-nature/30 text-center">
                                <th class="py-4 px-10 text-left">Expenditure Channel</th>
                                <th class="py-4 px-4 text-center">Qty / Price</th>
                                <th class="py-4 px-10 text-right">Channel Value</th>
                                <th class="py-4 px-10 opacity-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $m): ?>
                                <tr class="group">
                                    <td>
                                        <div class="flex items-center gap-6">
                                            <div class="w-12 h-12 rounded-2xl <?= $m['color_class'] ?> flex items-center justify-center text-white/50 text-[12px] font-black shadow-lg <?= $m['color_class'] ?>/10">#<?= $m['sort_order'] ?></div>
                                            <div class="flex-1">
                                                <p class="text-xl font-bold text-nature group-hover:text-saffron transition-colors leading-none mb-3"><?= htmlspecialchars($m['name_en']) ?></p>
                                                <!-- Mini Progress Bar -->
                                                <div class="w-full max-w-[200px] bg-gray-100 h-2 rounded-full overflow-hidden flex">
                                                    <?php
                                                    $target_v = isset($m['target_val']) ? (int)$m['target_val'] : 10000;
                                                    $current_v = isset($m['current_val']) ? (int)$m['current_val'] : 0;
                                                    $perc = ($target_v > 0) ? ($current_v / $target_v) * 100 : 0;
                                                    $perc = min(100, $perc);
                                                    ?>
                                                    <div class="<?= $m['color_class'] ?> h-full transition-all duration-1000" style="width: <?= $perc ?>%"></div>
                                                </div>
                                                <div class="flex justify-between w-full max-w-[200px] mt-2">
                                                    <span class="text-[12px] font-black text-gray-300 uppercase"><?= number_format($current_v) ?> Raised</span>
                                                    <span class="text-[12px] font-black text-gray-300 uppercase"><?= number_format($target_v) ?> Target</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-lg font-bold text-nature/70"><?= htmlspecialchars($m['quantity']) ?> <span class="text-[12px] text-gray-300"><?= htmlspecialchars($m['unit_name'] ?? 'Units') ?></span></p>
                                        <p class="text-[12px] font-black uppercase tracking-widest text-gray-300">@ ₹<?= htmlspecialchars($m['unit_price']) ?> / <?= htmlspecialchars($m['unit_name'] ?? 'Unit') ?></p>
                                    </td>
                                    <td class="text-right">
                                        <p class="text-3xl font-display italic text-nature font-bold tracking-tight"><?= htmlspecialchars($m['total_amount']) ?></p>
                                    </td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-3 translate-x-4 opacity-0 group-hover:opacity-100 transition-all group-hover:translate-x-0">
                                            <a href="editor.php?id=<?= $m['id'] ?>" class="w-14 h-14 rounded-2xl bg-white shadow-premium flex items-center justify-center text-nature hover:bg-nature hover:text-white transition-all border border-gray-50">
                                                <i class="fa-solid fa-pen-nib text-sm"></i>
                                            </a>
                                            <div class="flex items-center gap-3">
                                                <form method="POST" onsubmit="return confirmAction(event, 'Erase Record?', 'This permanent accounting record will be lost.');" class="inline">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                                    <button type="submit" class="w-14 h-14 rounded-2xl bg-white shadow-premium flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white transition-all border border-gray-50">
                                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                                    </button>
                                                </form>
                                                <!-- Multiple Select Checkbox -->
                                                <input type="checkbox" name="selected_ids[]" value="<?= $m['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded-lg border-2 border-nature/5 text-saffron focus:ring-saffron cursor-pointer shadow-inner">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>