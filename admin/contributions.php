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
        .system-input {
            background: #fff;
            border: 2px solid #f8fafc;
            border-radius: 1rem;
            padding: 0.75rem 1.25rem;
            width: 100%;
            transition: all 0.4s;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .system-input:focus {
            border-color: #FF6A00;
            box-shadow: 0 10px 30px -10px rgba(255, 106, 0, 0.2);
            outline: none;
        }
        .glass-card {
            background: white;
            border-radius: 2rem;
            padding: 2rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
        }
        .label-system {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #475569;
            margin-bottom: 0.6rem;
            display: block;
            margin-left: 0.25rem;
        }
    </style>
</head>

<body class="bg-[#f8fafc] flex flex-col md:flex-row md:h-screen md:overflow-hidden">
    <?php include 'include/sidebar.php'; ?>
    <main class="flex-1 p-4 lg:p-12 md:overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.3em] text-[13px] mb-2 block">Ticker Feed Configuration</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature leading-tight">Donation <span class="text-saffron italic">Chronicle</span></h1>
                    <p class="text-nature/40 mt-1 text-[13px] font-medium tracking-wide">Recent contributions for the website ticker feed</p>
                </div>
                <div class="flex items-center gap-4">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected records?', 'The selected records will be removed forever.');">
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Form Side -->
                <div class="lg:col-span-1">
                    <div class="glass-card !p-8 border-t-8 border-saffron">
                        <h3 class="text-xl font-bold mb-8 flex items-center gap-3 text-nature">
                            <i class="fas fa-heart text-saffron"></i> <?= $edit_data ? 'Refine' : 'Log' ?> Entry
                        </h3>
                        <form method="POST" class="space-y-5">
                            <input type="hidden" name="action" value="save">
                            <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id'] ?>"><?php endif; ?>

                            <div class="space-y-1">
                                <label class="label-system">Devotee Name</label>
                                <input type="text" name="donor_name" class="system-input" value="<?= $edit_data ? htmlspecialchars($edit_data['donor_name']) : '' ?>" placeholder="Full Name" required>
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Amount (INR)</label>
                                <input type="number" name="amount" class="system-input" value="<?= $edit_data ? $edit_data['amount'] : '' ?>" placeholder="e.g. 5000" required>
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Devotion Location</label>
                                <input type="text" name="location" class="system-input" value="<?= $edit_data ? htmlspecialchars($edit_data['location']) : '' ?>" placeholder="e.g. Mumbai">
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Short Narrative</label>
                                <textarea name="message" class="system-input h-24" placeholder="Brief note..."><?= $edit_data ? htmlspecialchars($edit_data['message']) : '' ?></textarea>
                            </div>
                            <button type="submit" class="w-full bg-nature text-white py-4 rounded-2xl font-black uppercase tracking-[0.2em] text-[13px] shadow-xl hover:bg-saffron transition-all duration-300 transform hover:scale-[1.02]">
                                <?= $edit_data ? 'Update Entry' : 'Publish Ticker' ?>
                            </button>
                            <?php if ($edit_data): ?><a href="contributions.php" class="block text-center text-xs font-bold text-gray-500 mt-4 underline decoration-nature/20 underline-offset-4">Cancel Editing</a><?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- List Side -->
                <div class="lg:col-span-2">
                    <div class="mb-6 flex items-center px-4">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                            <span class="text-[12px] font-black uppercase tracking-widest text-nature/40 group-hover:text-nature transition-colors">Selection Control</span>
                        </label>
                    </div>

                    <div class="space-y-4">
                        <?php if (empty($contributions)): ?>
                            <div class="glass-card !p-20 text-center border-2 border-dashed border-nature/5">
                                <i class="fas fa-hand-holding-heart text-5xl text-nature/10 mb-6"></i>
                                <p class="text-nature/30 font-bold uppercase tracking-widest text-[11px]">Chronicle is silent</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($contributions as $c): ?>
                                <div class="glass-card !p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 group hover:bg-nature/[0.02] transition-all border border-nature/5">
                                    <div class="flex items-center gap-6">
                                        <div class="w-12 h-12 bg-saffron/10 text-saffron rounded-xl flex items-center justify-center font-black relative overflow-hidden shadow-inner text-lg flex-shrink-0">
                                            ₹
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-nature leading-tight mb-1 text-lg"><?= htmlspecialchars($c['donor_name']) ?> <span class="text-nature/40 text-[14px] md:ml-2 italic font-normal block md:inline"><?= htmlspecialchars($c['location']) ?></span></h4>
                                            <p class="text-saffron font-medium text-[16px] tracking-tight">₹<?= number_format($c['amount']) ?> <span class="text-nature/80 italic font-normal md:ml-2 text-[14px] block md:inline">- "<?= htmlspecialchars($c['message'] ?: 'Sacred Seva') ?>"</span></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 w-full md:w-auto justify-end border-t md:border-t-0 pt-4 md:pt-0 border-nature/10 md:opacity-0 md:group-hover:opacity-100 md:translate-x-4 md:group-hover:translate-x-0 transition-all duration-300">
                                        <a href="?edit=<?= $c['id'] ?>" class="w-10 h-10 bg-nature/5 text-nature rounded-xl flex items-center justify-center hover:bg-nature hover:text-white transition-all shadow-sm"><i class="fas fa-edit text-[14px]"></i></a>
                                        <form method="POST" onsubmit="return confirmAction(event, 'Purge Record?', 'This contribution will be removed.');" class="inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <button type="submit" class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm border border-red-100"><i class="fas fa-trash-alt text-[14px]"></i></button>
                                        </form>
                                        <div class="pl-2 border-l border-nature/10 ml-1">
                                            <input type="checkbox" name="selected_ids[]" value="<?= $c['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded border-2 border-nature/10 text-saffron focus:ring-saffron cursor-pointer">
                                        </div>
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