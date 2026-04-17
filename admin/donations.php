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
    <style>
        .glass-card {
            background: white;
            border-radius: 2rem;
            padding: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.03);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05);
        }

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

        .system-table thead tr th:first-child {
            border-radius: 1.25rem 0 0 1.25rem;
        }

        .system-table thead tr th:last-child {
            border-radius: 0 1.25rem 1.25rem 0;
        }

        .system-table td {
            padding: 0.5rem 1rem;
            background: #fff;
            vertical-align: middle;
            transition: all 0.3s;
        }

        .system-table tr:hover td {
            background: #f1f5f9;
        }

        .system-table tr td:first-child {
            border-radius: 1.25rem 0 0 1.25rem;
        }

        .system-table tr td:last-child {
            border-radius: 0 1.25rem 1.25rem 0;
        }

        .currency-badge {
            font-size: 12px;
            font-weight: 800;
            padding: 0.5rem 1rem;
            border-radius: 99px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>

<body class="bg-[#f8fafc] flex">
    <?php include 'include/sidebar.php'; ?>
    <main class="flex-1 p-6 lg:p-12 overflow-y-auto">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.3em] text-[13px] mb-2 block">Ledger Overview</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature leading-tight">Divine <span class="text-saffron italic">Offerings</span></h1>
                    <p class="text-nature/40 mt-1 text-[13px] font-medium tracking-wide">Detailed donation registry from the digital portal</p>
                </div>
                <div class="flex items-center gap-4">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected records?', 'The selected offerings will be removed from history forever.');">
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
                <div class="bg-nature/10 text-nature p-4 rounded-xl mb-6 font-bold text-sm border border-nature/20 animate-fade-in"><?= $message ?></div>
            <?php endif; ?>

            <div class="glass-card" data-aos="fade-up">
                <table class="system-table">
                    <thead>
                        <tr>
                            <th class="w-10">
                                <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded border-gray-300 text-saffron focus:ring-saffron">
                            </th>
                            <th class="w-48">Date</th>
                            <th class="w-64">Devotee Information</th>
                            <th class="w-56">Contribution</th>
                            <th>Mission / Seva</th>
                            <th class="w-32 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($donations)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-20 text-gray-400 uppercase text-[12px] font-black tracking-widest">No offerings found in treasury</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($donations as $d): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_ids[]" value="<?= $d['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded border-gray-300 text-saffron focus:ring-saffron">
                                    </td>
                                    <td>
                                        <div class="text-[14px] text-gray-700 font-medium uppercase tracking-tight"><?= date('d M Y', strtotime($d['donation_date'] ?: $d['created_at'])) ?></div>
                                        <div class="text-[12px] text-gray-500 font-normal"><?= date('h:i A', strtotime($d['created_at'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="text-[16px] font-medium text-nature leading-tight mb-1"><?= htmlspecialchars($d['donor_name']) ?></div>
                                        <div class="flex flex-col gap-0.5">
                                            <div class="text-[13px] text-gray-600 font-normal"><i class="fas fa-envelope mr-2 text-gold/60 text-[12px]"></i><?= htmlspecialchars($d['email'] ?: 'N/A') ?></div>
                                            <div class="text-[13px] text-gray-600 font-normal tracking-widest uppercase"><i class="fas fa-phone-alt mr-2 text-gold/60 text-[12px]"></i><?= htmlspecialchars($d['phone'] ?: 'N/A') ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="text-2xl font-black text-nature"><?= number_format($d['amount']) ?></div>
                                            <span class="currency-badge bg-saffron/10 text-saffron font-black"><?= $d['currency_type'] ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <i class="far fa-heart text-saffron"></i>
                                            <span class="text-[13px] text-nature/80 font-bold uppercase tracking-wide"><?= htmlspecialchars($d['seva_title'] ?: 'General Welfare') ?></span>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <form method="POST" onsubmit="return confirmAction(event, 'Purge Record?', 'This donation entry will be expunged.');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                            <button type="submit" class="w-10 h-10 rounded-xl bg-red-100/50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm border border-red-200">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
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