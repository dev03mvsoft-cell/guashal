<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

$message = "";
$error = "";

// Save Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save') {
        $id = $_POST['id'] ?? null;
        $name_en = $_POST['name_en'];
        $qty = $_POST['quantity'];
        $unit_name = $_POST['unit_name'];
        $unit = $_POST['unit_price'];
        $total = $_POST['total_amount'];
        $target_v = $_POST['target_val'] ?: 10000;
        $current_v = $_POST['current_val'] ?: 0;
        $color = $_POST['color_class'];
        $sort = $_POST['sort_order'] ?: 0;

        try {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE transparency_materials SET name_en = ?, quantity = ?, unit_name = ?, unit_price = ?, total_amount = ?, target_val = ?, current_val = ?, color_class = ?, sort_order = ? WHERE id = ?");
                $stmt->execute([$name_en, $qty, $unit_name, $unit, $total, $target_v, $current_v, $color, $sort, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO transparency_materials (name_en, quantity, unit_name, unit_price, total_amount, target_val, current_val, color_class, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name_en, $qty, $unit_name, $unit, $total, $target_v, $current_v, $color, $sort]);
            }
            header("Location: index.php?msg=success");
            exit;
        } catch (PDOException $e) {
            $error = "Critical Error: " . $e->getMessage();
        }
    }
}

// Edit Fetch
$edit_data = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM transparency_materials WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_data = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit_data ? 'Update' : 'Initialize' ?> Expenditure - Admin Dashboard</title>
    <?php include '../include/head.php'; ?>
    <style>
        .system-input {
            background: #fff;
            border: 2px solid #f8fafc;
            border-radius: 1rem;
            padding: 0.6rem 1.25rem;
            width: 100%;
            transition: all 0.4s;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .system-input:focus {
            border-color: #2c4c3b;
            box-shadow: 0 10px 30px -10px rgba(44, 76, 59, 0.1);
            outline: none;
        }
        .glass-card {
            background: white;
            border-radius: 2rem;
            padding: 2.5rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
        }
        .label-system {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #475569;
            margin-bottom: 0.5rem;
            display: block;
            margin-left: 0.25rem;
        }
        .color-dot {
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 1rem;
            border: 2px solid transparent;
        }
        .peer:checked + .color-dot {
            border-color: #2c4c3b;
            transform: scale(1.05);
        }
    </style>
</head>

<body class="bg-[#f8fafc] flex">
    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-12 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            <header class="mb-8 flex justify-between items-center">
                <div>
                    <span class="text-saffron font-bold uppercase tracking-[0.3em] text-[12px] mb-1 block">Ledger Registry</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature"><?= $edit_data ? 'Refine' : 'Add' ?> <span class="italic text-saffron">Expenditure</span></h1>
                </div>
                <a href="index.php" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-nature/40 hover:text-red-500 hover:rotate-90 transition-all shadow-md border border-gray-100">
                    <i class="fas fa-times text-lg"></i>
                </a>
            </header>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-500 p-4 rounded-xl mb-8 font-bold text-xs border border-red-100 shadow-sm"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-6 pb-20">
                <input type="hidden" name="action" value="save">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <?php endif; ?>

                <div class="glass-card" data-aos="fade-up">
                    <div class="space-y-6">
                        <!-- Header Row: Title & Colors -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                            <div class="md:col-span-2 space-y-1">
                                <label class="label-system">Expenditure Title</label>
                                <input type="text" name="name_en" class="system-input" value="<?= $edit_data ? htmlspecialchars($edit_data['name_en']) : '' ?>" placeholder="e.g. Pure Green Fodder" required>
                            </div>
                            <div class="space-y-2">
                                <label class="label-system text-center">Vedic Theme</label>
                                <div class="flex justify-center gap-3">
                                    <?php
                                    $pallatte = [
                                        'bg-saffron' => '#FF6A00',
                                        'bg-nature' => '#2c4c3b',
                                        'bg-gold' => '#FFD700'
                                    ];
                                    foreach ($pallatte as $class => $hex): ?>
                                        <label class="relative block">
                                            <input type="radio" name="color_class" value="<?= $class ?>" class="hidden peer" <?= (!$edit_data && $class == 'bg-saffron') || ($edit_data && $edit_data['color_class'] == $class) ? 'checked' : '' ?>>
                                            <div class="color-dot w-9 h-9 <?= $class ?> flex items-center justify-center p-1 border-2 border-white shadow-sm cursor-pointer">
                                                <i class="fas fa-check text-white text-[9px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Metrics Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-slate-50/50 p-6 rounded-2xl border border-slate-100/50">
                            <div class="space-y-1">
                                <label class="label-system">Quantity</label>
                                <input type="text" name="quantity" class="system-input" value="<?= $edit_data ? htmlspecialchars($edit_data['quantity']) : '' ?>" placeholder="2000" required>
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Unit Type</label>
                                <select name="unit_name" class="system-input">
                                    <?php
                                    $units = ['Units', 'KG', 'Gram', 'Litre', 'SQ. FT', 'System', 'Sets', 'Monthly', 'Annual'];
                                    foreach ($units as $u): ?>
                                        <option value="<?= $u ?>" <?= ($edit_data && $edit_data['unit_name'] == $u) ? 'selected' : '' ?>><?= $u ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Price/Unit (₹)</label>
                                <input type="text" name="unit_price" class="system-input" value="<?= $edit_data ? htmlspecialchars($edit_data['unit_price']) : '' ?>" placeholder="500" required>
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Sort Order</label>
                                <input type="number" name="sort_order" class="system-input" value="<?= $edit_data ? $edit_data['sort_order'] : '0' ?>">
                            </div>
                        </div>

                        <!-- Total & Progress -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1 space-y-1">
                                <label class="label-system">Target Goal</label>
                                <input type="number" name="target_val" class="system-input" value="<?= $edit_data ? ($edit_data['target_val'] ?? 10000) : '10000' ?>">
                            </div>
                            <div class="md:col-span-1 space-y-1">
                                <label class="label-system">Current Progress</label>
                                <input type="number" name="current_val" class="system-input" value="<?= $edit_data ? ($edit_data['current_val'] ?? 0) : '0' ?>">
                            </div>
                            <div class="md:col-span-1 space-y-1">
                                <label class="label-system">Total Amount (Display)</label>
                                <input type="text" name="total_amount" class="system-input font-bold text-nature" value="<?= $edit_data ? htmlspecialchars($edit_data['total_amount']) : '' ?>" placeholder="₹ 1,30,000" required>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full bg-nature text-white py-4 rounded-2xl font-bold uppercase tracking-[0.2em] text-[13px] shadow-lg hover:bg-black transition-all duration-300 transform hover:scale-[1.01]">
                                <i class="fas fa-check-circle mr-2 opacity-50"></i> <?= $edit_data ? 'Update Expenditure' : 'Commit to Ledger' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>
</html>