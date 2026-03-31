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
        .editor-container {
            background: white;
            border-radius: 4rem;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.05);
        }

        .input-premium {
            background: #f9f9f9;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 1.5rem;
            padding: 1.25rem 1.75rem;
            width: 100%;
            transition: all 0.3s;
            font-size: 1rem;
            font-weight: 600;
            color: #2c4c3b;
        }

        .input-premium:focus {
            outline: none;
            border-color: #FF6A00;
            background: white;
            box-shadow: 0 0 0 5px rgba(255, 106, 0, 0.1);
        }

        .color-card {
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s;
            border-radius: 2rem;
            overflow: hidden;
        }

        .color-card:hover {
            transform: scale(1.05);
        }

        .peer:checked+.color-card {
            border-color: #2c4c3b;
            transform: scale(1.08);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="min-h-screen bg-[#f7f5f2] flex flex-col md:flex-row">
    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-16 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            <header class="mb-16 flex justify-between items-center">
                <div>
                    <a href="index.php" class="text-gray-400 hover:text-nature flex items-center gap-2 font-black text-[12px] uppercase tracking-widest mb-4">
                        <i class="fa-solid fa-arrow-left"></i> Return to Ledger
                    </a>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature"><?= $edit_data ? 'Refine' : 'Add' ?> <span class="italic text-saffron">Expenditure</span></h1>
                    <p class="text-[12px] font-black uppercase text-gray-300 tracking-[0.2em] mt-2">Financial Integrity Protocol • Manual Entry</p>
                </div>
            </header>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-500 p-6 rounded-3xl mb-12 font-bold text-[12px] border-l-4 border-red-500 shadow-sm"><?= $error ?></div>
            <?php endif; ?>

            <div class="editor-container">
                <form method="POST" class="p-12 lg:p-20 space-y-12">
                    <input type="hidden" name="action" value="save">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>

                    <!-- Spectral Classification (Color Selection) -->
                    <div class="space-y-6">
                        <label class="block text-[12px] uppercase tracking-[0.3em] text-gray-400 font-black mb-8 text-center">Vedic Color Classification</label>
                        <div class="grid grid-cols-3 gap-8">
                            <?php
                            $pallatte = [
                                'bg-saffron' => ['hex' => '#FF6A00', 'label' => 'Strategic Charge'],
                                'bg-nature' => ['hex' => '#2c4c3b', 'label' => 'Natural Flow'],
                                'bg-gold' => ['hex' => '#FFD700', 'label' => 'Abundant Value']
                            ];
                            foreach ($pallatte as $class => $meta): ?>
                                <label class="relative block">
                                    <input type="radio" name="color_class" value="<?= $class ?>" class="hidden peer" <?= (!$edit_data && $class == 'bg-saffron') || ($edit_data && $edit_data['color_class'] == $class) ? 'checked' : '' ?>>
                                    <div class="color-card h-24 <?= $class ?> flex flex-col items-center justify-center p-4">
                                        <i class="fas fa-check text-white opacity-0 peer-checked:opacity-100 transition-opacity mb-2"></i>
                                        <p class="text-[12px] font-black uppercase tracking-widest text-white/70"><?= $meta['label'] ?></p>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="md:col-span-2">
                            <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3 px-1">Expenditure Title</label>
                            <input type="text" name="name_en" class="input-premium lg:text-2xl" value="<?= $edit_data ? htmlspecialchars($edit_data['name_en']) : '' ?>" placeholder="e.g. Pure Green Fodder" required>
                        </div>

                        <div class="md:col-span-2 bg-nature/[0.02] p-10 rounded-[3rem] border border-nature/5">
                            <label class="block text-[12px] uppercase tracking-widest text-nature/40 font-black mb-8 text-center italic">Metric Values</label>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div>
                                    <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3">Quantity</label>
                                    <input type="text" name="quantity" class="input-premium text-center" value="<?= $edit_data ? htmlspecialchars($edit_data['quantity']) : '' ?>" placeholder="2000" required>
                                </div>
                                <div>
                                    <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3">Unit Type</label>
                                    <select name="unit_name" class="input-premium appearance-none bg-white">
                                        <?php
                                        $units = ['Units', 'KG', 'Gram', 'Litre', 'SQ. FT', 'System', 'Sets', 'Monthly', 'Annual'];
                                        foreach ($units as $u): ?>
                                            <option value="<?= $u ?>" <?= ($edit_data && $edit_data['unit_name'] == $u) ? 'selected' : '' ?>><?= $u ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3">Price Per Unit (₹)</label>
                                    <input type="text" name="unit_price" class="input-premium text-center" value="<?= $edit_data ? htmlspecialchars($edit_data['unit_price']) : '' ?>" placeholder="500" required>
                                </div>
                                <div>
                                    <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3">Sort Order</label>
                                    <input type="number" name="sort_order" class="input-premium text-center" value="<?= $edit_data ? $edit_data['sort_order'] : '0' ?>">
                                </div>
                                <div class="col-span-4">
                                    <label class="block text-[12px] uppercase tracking-widest text-nature/40 font-black mb-3 border-t border-nature/5 pt-4">Manual Progress Tracking (Target vs Current)</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-2 opacity-50">Target Goal</label>
                                            <input type="number" name="target_val" class="input-premium bg-saffron/5" value="<?= $edit_data ? ($edit_data['target_val'] ?? 10000) : '10000' ?>" placeholder="10000">
                                        </div>
                                        <div>
                                            <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-2 opacity-50">Current Progress</label>
                                            <input type="number" name="current_val" class="input-premium bg-nature/5" value="<?= $edit_data ? ($edit_data['current_val'] ?? 0) : '0' ?>" placeholder="5000">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3 px-1 text-center">Total Channel Value (Display String)</label>
                            <input type="text" name="total_amount" class="input-premium bg-gold/5 border-gold/10 text-center text-3xl font-display italic tracking-tighter" value="<?= $edit_data ? htmlspecialchars($edit_data['total_amount']) : '' ?>" placeholder="₹ 1,300,000" required>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-nature text-white py-6 rounded-[2.5rem] font-black uppercase tracking-widest text-[15px] shadow-2xl hover:bg-black hover:scale-[1.02] active:scale-95 transition-all">
                        <?= $edit_data ? 'Update Financial Record' : 'Commit to Ledger' ?>
                    </button>

                </form>
            </div>
        </div>
    </main>
</body>

</html>