<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

$message = '';
$error = '';

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM seva_options WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Seva option removed from the divine list.";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM seva_options WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $message = count($ids) . " seva options purged successfully!";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}

// Fetch all seva options
$sevas = [];
try {
    $stmt = $pdo->query("SELECT * FROM seva_options ORDER BY sort_order ASC, created_at DESC");
    if ($stmt) $sevas = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Seva archive unreachable.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seva Management - Admin Dashboard</title>
    <?php include '../include/head.php'; ?>
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

<body class="bg-[#f8fafc] flex flex-col md:flex-row md:h-screen md:overflow-hidden">
    <?php include '../include/sidebar.php'; ?>
    <main class="flex-1 p-4 lg:p-12 md:overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.3em] text-[13px] mb-2 block">Service Configuration</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature leading-tight">Seva <span class="text-saffron italic">Opportunities</span></h1>
                    <p class="text-nature/40 mt-1 text-[13px] font-medium tracking-wide">Registry of divine service options and holy offerings</p>
                </div>
                <div class="flex items-center gap-4">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected sevas?', 'The selected divine services will be removed forever.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10 transition-all">
                            <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-10 h-10 rounded-xl flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="fas fa-trash-alt text-[12px]"></i>
                            </button>
                        </div>
                    </form>
                    <a href="editor.php" class="bg-saffron text-white px-8 py-4 rounded-xl font-bold flex items-center gap-3 shadow-lg shadow-saffron/20 hover:scale-105 transition-all">
                        <i class="fas fa-hand-holding-heart text-xs"></i> <span>Create Opportunity</span>
                    </a>
                </div>
            </header>

            <?php if ($message || isset($_GET['msg'])): ?>
                <div class="bg-nature/10 text-nature p-4 rounded-xl mb-8 font-bold text-sm border border-nature/20 animate-fade-in shadow-sm shadow-nature/5">
                    <i class="fas fa-check-circle mr-2"></i> Seva archive successfully updated.
                </div>
            <?php endif; ?>

            <div class="mb-6 flex items-center px-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                    <span class="text-[12px] font-black uppercase tracking-widest text-nature/40 group-hover:text-nature transition-colors">Select All Sevas</span>
                </label>
            </div>

            <div class="glass-card !p-0 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="system-table min-w-[800px] lg:min-w-full">
                    <thead>
                        <tr>
                            <th class="w-12 h-16 pl-6">#</th>
                            <th class="w-20">Symbol</th>
                            <th>Seva Details</th>
                            <th>Branding</th>
                            <th class="w-24">Order</th>
                            <th class="w-32 text-right pr-6">Management</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sevas)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-24">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-nature/5 text-nature/10 rounded-full flex items-center justify-center text-4xl mb-4 italic"><i class="fas fa-hands-praying"></i></div>
                                        <p class="text-nature/30 uppercase font-black tracking-widest text-[11px]">No seva opportunities defined yet</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sevas as $item): ?>
                                <tr class="group">
                                    <td class="pl-6">
                                        <input type="checkbox" name="selected_ids[]" value="<?= $item['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded border-2 border-nature/10 text-saffron focus:ring-saffron cursor-pointer">
                                    </td>
                                    <td>
                                        <div class="w-12 h-12 rounded-xl bg-nature/5 flex items-center justify-center text-nature text-xl border border-nature/5 shadow-inner">
                                            <i class="<?= htmlspecialchars($item['icon_class'] ?: 'fas fa-heart') ?>"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-medium text-nature text-[16px] leading-tight mb-0.5"><?= htmlspecialchars($item['title_en']) ?></div>
                                        <div class="text-[12px] text-nature/50 line-clamp-1 italic font-normal"><?= htmlspecialchars($item['description_en'] ?: 'No description provided...') ?></div>
                                    </td>
                                    <td>
                                        <span class="px-4 py-1.5 rounded-full text-[11px] font-medium uppercase tracking-widest bg-saffron/10 text-saffron border border-saffron/20 leading-none">
                                            <?= htmlspecialchars($item['color_class'] ?: 'Classic') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="w-10 h-10 bg-slate-50 border border-slate-100 rounded-lg flex items-center justify-center font-normal text-slate-400 text-xs">
                                            <?= $item['sort_order'] ?>
                                        </div>
                                    </td>
                                    <td class="pr-6 text-right">
                                        <div class="flex justify-end items-center gap-3">
                                            <a href="editor.php?id=<?= $item['id'] ?>" class="w-10 h-10 rounded-xl bg-nature/5 text-nature flex items-center justify-center hover:bg-nature hover:text-white transition-all shadow-sm border border-nature/10">
                                                <i class="fas fa-edit text-[14px]"></i>
                                            </a>
                                            <form method="POST" onsubmit="return confirmAction(event, 'Purge Seva?', 'This will remove the divine service offering.');" class="inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button type="submit" class="w-10 h-10 rounded-xl bg-red-100/50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm border border-red-200">
                                                    <i class="fas fa-trash-alt text-[10px]"></i>
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
</body>

</html>