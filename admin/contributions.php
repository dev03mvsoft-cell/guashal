<?php
require_once 'include/auth.php';
require_once '../config/db.php';

$message = '';
$error = '';

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM contributions WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Donation record expunged.";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM contributions WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $message = count($ids) . " donations purged successfully!";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'save') {
        $id = $_POST['id'] ?? null;
        $name = $_POST['donor_name'];
        $amount = $_POST['amount'];
        $msg = $_POST['message'];
        $loc = $_POST['location'];

        try {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE contributions SET donor_name = ?, amount = ?, message = ?, location = ? WHERE id = ?");
                $stmt->execute([$name, $amount, $msg, $loc, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO contributions (donor_name, amount, message, location) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $amount, $msg, $loc]);
            }
            $message = "Sacred contribution recorded!";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}

// Fetch all
$contributions = [];
try {
    $stmt = $pdo->query("SELECT * FROM contributions ORDER BY created_at DESC");
    if ($stmt) $contributions = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Table 'contributions' not found.";
}

// Fetch for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM contributions WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Manager - Gaushala Admin</title>
    <?php include 'include/head.php'; ?>
    <style>
        .input-round {
            border-radius: 1rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.3s;
            background: white;
        }
    </style>
</head>

<body class="md:h-screen flex flex-col md:flex-row bg-[#fdfaf7] overflow-hidden">
    <?php include 'include/sidebar.php'; ?>
    <main class="flex-1 p-6 lg:p-16 overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
                <div>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold">Donation <span class="text-saffron italic">Chronicle</span></h1>
                    <p class="text-gray-400 mt-2 text-sm tracking-widest uppercase font-black italic">Recent Contributions for Ticker</p>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Form Side -->
                <div class="lg:col-span-1">
                    <div class="glass p-10 rounded-[3rem] border-t-8 border-saffron shadow-2xl">
                        <h3 class="text-xl font-bold mb-8 flex items-center gap-3">
                            <i class="fas fa-heart text-saffron"></i> <?= $edit_data ? 'Update' : 'Record' ?> Contribution
                        </h3>
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="action" value="save">
                            <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id'] ?>"><?php endif; ?>

                            <div class="space-y-2">
                                <label class="text-[12px] uppercase tracking-widest font-black text-gray-400 px-2">Donor Name</label>
                                <input type="text" name="donor_name" class="input-round" value="<?= $edit_data ? htmlspecialchars($edit_data['donor_name']) : '' ?>" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[12px] uppercase tracking-widest font-black text-gray-400 px-2">Amount (₹)</label>
                                <input type="number" name="amount" class="input-round" value="<?= $edit_data ? $edit_data['amount'] : '' ?>" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[12px] uppercase tracking-widest font-black text-gray-400 px-2">City/Location</label>
                                <input type="text" name="location" class="input-round" value="<?= $edit_data ? htmlspecialchars($edit_data['location']) : '' ?>" placeholder="e.g. Mumbai">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[12px] uppercase tracking-widest font-black text-gray-400 px-2">Message (Optional)</label>
                                <textarea name="message" class="input-round h-24" placeholder="e.g. For food seva"><?= $edit_data ? htmlspecialchars($edit_data['message']) : '' ?></textarea>
                            </div>
                            <button type="submit" class="w-full bg-nature text-white py-4 rounded-2xl font-bold shadow-xl hover:scale-105 transition-all">
                                <?= $edit_data ? 'Update Record' : 'Log Contribution' ?>
                            </button>
                            <?php if ($edit_data): ?><a href="contributions.php" class="block text-center text-xs font-bold text-gray-300 mt-4 underline">Cancel Editing</a><?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- List Side -->
                <div class="lg:col-span-2">
                    <div class="mb-8 flex items-center px-4">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded-lg border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                            <span class="text-[12px] font-black uppercase tracking-widest text-nature/40 group-hover:text-nature transition-colors">Select All Logged Entries</span>
                        </label>
                    </div>

                    <div class="space-y-4">
                        <?php if (empty($contributions)): ?>
                            <div class="glass p-20 rounded-[3rem] text-center border-2 border-dashed border-gray-100">
                                <i class="fas fa-hand-holding-heart text-5xl text-nature/10 mb-6"></i>
                                <p class="text-gray-300 font-bold uppercase tracking-widest text-xs">No contributions found</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($contributions as $c): ?>
                                <div class="glass p-6 rounded-[2rem] flex justify-between items-center group hover:shadow-xl transition-all border border-gray-50">
                                    <div class="flex items-center gap-6">
                                        <div class="w-12 h-12 bg-saffron/10 text-saffron rounded-2xl flex items-center justify-center font-bold relative overflow-hidden shadow-inner">
                                            ₹
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-nature leading-none mb-1"><?= htmlspecialchars($c['donor_name']) ?> <span class="text-gray-400 text-[15px] ml-2"><?= htmlspecialchars($c['location']) ?></span></h4>
                                            <p class="text-saffron font-black text-[15px]">₹<?= number_format($c['amount']) ?> <span class="text-gray-900 italic font-medium ml-2 text-[15px]">- "<?= htmlspecialchars($c['message'] ?: 'Holy Seva') ?>"</span></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 translate-x-10 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all">
                                        <a href="?edit=<?= $c['id'] ?>" class="w-10 h-10 bg-nature/5 text-nature rounded-xl flex items-center justify-center hover:bg-nature hover:text-white transition-colors"><i class="fas fa-edit text-xs"></i></a>
                                        <form method="POST" onsubmit="return confirmAction(event, 'Delete entry?', 'This donor record will be removed.');" class="inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <button type="submit" class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors"><i class="fas fa-trash text-xs"></i></button>
                                        </form>
                                        <input type="checkbox" name="selected_ids[]" value="<?= $c['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded-lg border-2 border-nature/5 text-saffron focus:ring-saffron cursor-pointer shadow-inner ml-2">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>