<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

$message = '';
$error = '';

$item = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM seva_options WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $item = $stmt->fetch();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save') {
        $id = $_POST['id'] ?? null;
        $title_en = trim($_POST['title_en']);
        $description_en = trim($_POST['description_en']);
        $icon_class = trim($_POST['icon_class']);
        $color_class = trim($_POST['color_class']);
        $sort_order = (int)$_POST['sort_order'];
        $status = $_POST['status'] ?? 'active';

        try {
            if ($id) {
                // Update
                $stmt = $pdo->prepare("UPDATE seva_options SET title_en = ?, description_en = ?, icon_class = ?, color_class = ?, sort_order = ?, status = ? WHERE id = ?");
                $stmt->execute([$title_en, $description_en, $icon_class, $color_class, $sort_order, $status, $id]);
            } else {
                // Create
                $stmt = $pdo->prepare("INSERT INTO seva_options (title_en, description_en, icon_class, color_class, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title_en, $description_en, $icon_class, $color_class, $sort_order, $status]);
            }
            header("Location: index.php?msg=success");
            exit;
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configure Seva - Admin Dashboard</title>
    <?php include '../include/head.php'; ?>
    <style>
        .input-premium {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 1.5rem;
            padding: 1.25rem 1.5rem;
            width: 100%;
            transition: all 0.3s;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
            color: #2c4c3b;
            font-weight: 500;
        }

        .input-premium:focus {
            outline: none;
            border-color: #FF6A00;
            box-shadow: 0 0 0 4px rgba(255, 106, 0, 0.1);
        }
    </style>
</head>

<body class="md:h-screen bg-[#f9f7f4] flex flex-col md:flex-row overflow-hidden">
    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-20 overflow-y-auto h-full">
        <div class="max-w-4xl mx-auto">
            <header class="flex justify-between items-center mb-16">
                <div>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature"><?= $item ? 'Refine' : 'Divine' ?> <span class="italic text-saffron">Seva</span></h1>
                    <p class="text-gray-400 mt-2 text-[12px] font-black uppercase tracking-[0.3em]">Configure Mission Opportunity</p>
                </div>
                <a href="index.php" class="text-gray-400 hover:text-nature text-[12px] font-black uppercase tracking-widest flex items-center gap-3">
                    <i class="fas fa-arrow-left"></i> Return to Registry
                </a>
            </header>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-700 p-8 rounded-[2rem] text-[12px] mb-12 border-l-8 border-red-500 font-bold"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-12 bg-white p-12 rounded-[4rem] shadow-2xl shadow-nature/5 border border-white/40">
                <input type="hidden" name="action" value="save">
                <?php if ($item): ?><input type="hidden" name="id" value="<?= $item['id'] ?>"><?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                    <div class="md:col-span-8">
                        <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-4 px-1">Display Title (English)</label>
                        <input type="text" name="title_en" class="input-premium text-lg" value="<?= $item ? htmlspecialchars($item['title_en']) : '' ?>" required placeholder="e.g. Roti Seva">
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-4 px-1">Sequence Position</label>
                        <input type="number" name="sort_order" class="input-premium" value="<?= $item ? $item['sort_order'] : '0' ?>" required>
                    </div>

                    <div class="md:col-span-12">
                        <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-4 px-1">Sacred Narrative (English)</label>
                        <textarea name="description_en" class="input-premium h-40" required placeholder="Describe the impact of this seva..."><?= $item ? htmlspecialchars($item['description_en']) : '' ?></textarea>
                    </div>

                    <div class="md:col-span-6">
                        <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-4 px-1">Icon Identity (FontAwesome)</label>
                        <input type="text" name="icon_class" class="input-premium" value="<?= $item ? htmlspecialchars($item['icon_class']) : 'fas fa-heart' ?>" required placeholder="fas fa-bread-slice">
                    </div>
                    <div class="md:col-span-6">
                        <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-4 px-1">Aesthetic Theme</label>
                        <select name="color_class" class="input-premium">
                            <option value="saffron" <?= ($item && $item['color_class'] == 'saffron') ? 'selected' : '' ?>>Divine Saffron</option>
                            <option value="nature" <?= ($item && $item['color_class'] == 'nature') ? 'selected' : '' ?>>Nature Green</option>
                            <option value="gold" <?= ($item && $item['color_class'] == 'gold') ? 'selected' : '' ?>>Celestial Gold</option>
                            <option value="red-600" <?= ($item && $item['color_class'] == 'red-600') ? 'selected' : '' ?>>Urgent Crimson</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-6 pt-10 border-t border-gray-50 mt-12">
                    <button type="submit" class="flex-1 bg-nature text-white py-6 rounded-3xl font-black uppercase tracking-widest text-[15px] shadow-2xl shadow-nature/20 hover:scale-[1.02] active:scale-95 transition-all">
                        <?= $item ? 'Update Mission Registry' : 'Index to Divine Registry' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>