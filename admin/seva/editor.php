<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

$id = $_GET['id'] ?? null;
$item = null;
$error = '';

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM seva_options WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
    } catch (Exception $e) {}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $title_en = trim($_POST['title_en']);
    $description_en = trim($_POST['description_en']);
    $icon_class = trim($_POST['icon_class']);
    $color_class = trim($_POST['color_class']);
    $sort_order = (int)$_POST['sort_order'];
    $status = $_POST['status'] ?? 'active';

    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE seva_options SET title_en=?, description_en=?, icon_class=?, color_class=?, sort_order=?, status=? WHERE id=?");
            $stmt->execute([$title_en, $description_en, $icon_class, $color_class, $sort_order, $status, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO seva_options (title_en, description_en, icon_class, color_class, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title_en, $description_en, $icon_class, $color_class, $sort_order, $status]);
        }
        header("Location: index.php?msg=Mission Optimized");
        exit;
    } catch (PDOException $e) { $error = $e->getMessage(); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optimize Seva Mission</title>
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
        .icon-suggestion {
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .icon-suggestion:hover {
            background: #2c4c3b;
            color: white;
            border-radius: 0.5rem;
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-[#f8fafc] flex">

    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-12 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            
            <header class="mb-8 flex items-center justify-between">
                <div>
                    <span class="text-saffron font-bold uppercase tracking-[0.3em] text-[12px] mb-1 block">Mission Configurator</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature leading-tight"><?= $id ? 'Refine' : 'Add' ?> <span class="text-saffron italic">Seva</span></h1>
                </div>
                <a href="index.php" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-nature/40 hover:text-red-500 hover:rotate-90 transition-all shadow-md border border-gray-100">
                    <i class="fas fa-times text-lg"></i>
                </a>
            </header>

            <form method="POST" class="space-y-6 pb-20">
                <input type="hidden" name="action" value="save">

                <div class="glass-card" data-aos="fade-up">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        <!-- Col 1: Icon Preview -->
                        <div class="flex flex-col items-center">
                            <label class="label-system text-center w-full">Mission Icon</label>
                            <div class="w-24 h-24 bg-slate-50 rounded-3xl flex items-center justify-center mb-4 border-2 border-white shadow-lg overflow-hidden relative group">
                                <div class="absolute inset-0 bg-nature/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <i id="icon-preview" class="<?= $item ? htmlspecialchars($item['icon_class']) : 'fas fa-heart' ?> text-4xl text-saffron transition-all group-hover:scale-110"></i>
                            </div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] text-center px-4 leading-relaxed">Divine Symbol of Service</div>
                        </div>

                        <!-- Col 2 & 3: Details -->
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
                            <div class="md:col-span-2 space-y-1">
                                <label class="label-system">Mission Title</label>
                                <input type="text" name="title_en" required class="system-input" placeholder="e.g. Roti Seva" value="<?= $item ? htmlspecialchars($item['title_en']) : '' ?>">
                            </div>
                            
                            <div class="space-y-1">
                                <label class="label-system">Icon Class (FontAwesome)</label>
                                <input type="text" name="icon_class" id="icon-input" required class="system-input" placeholder="fas fa-heart" value="<?= $item ? htmlspecialchars($item['icon_class']) : 'fas fa-heart' ?>">
                                
                                <div class="pt-3">
                                    <label class="text-[10px] font-bold text-nature/40 uppercase tracking-widest block mb-2">Suggested Symbols:</label>
                                    <div class="flex gap-2 flex-wrap">
                                        <?php 
                                        $suggestions = ['fas fa-heart', 'fas fa-leaf', 'fas fa-hand-holding-heart', 'fas fa-cow', 'fas fa-tint', 'fas fa-wheat-awn', 'fas fa-hands-holding-circle', 'fas fa-seedling'];
                                        foreach($suggestions as $icon): ?>
                                            <div onclick="setIcon('<?= $icon ?>')" class="icon-suggestion w-8 h-8 flex items-center justify-center bg-slate-50 rounded-lg text-nature/30 hover:text-white text-xs border border-slate-100">
                                                <i class="<?= $icon ?>"></i>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="label-system">Theme Color</label>
                                <select name="color_class" class="system-input">
                                    <option value="saffron" <?= ($item && $item['color_class'] == 'saffron') ? 'selected' : '' ?>>Divine Saffron</option>
                                    <option value="nature" <?= ($item && $item['color_class'] == 'nature') ? 'selected' : '' ?>>Nature Green</option>
                                    <option value="gold" <?= ($item && $item['color_class'] == 'gold') ? 'selected' : '' ?>>Celestial Gold</option>
                                </select>
                            </div>

                            <div class="md:col-span-2 space-y-1">
                                <label class="label-system">Mission Narrative</label>
                                <textarea name="description_en" required class="system-input h-24 font-normal text-nature/70" placeholder="Describe the impact..."><?= $item ? htmlspecialchars($item['description_en']) : '' ?></textarea>
                            </div>

                            <div class="space-y-1">
                                <label class="label-system">Display Sequence</label>
                                <input type="number" name="sort_order" required class="system-input" value="<?= $item ? $item['sort_order'] : '0' ?>">
                            </div>

                            <div class="flex items-end pt-4">
                                <button type="submit" class="w-full bg-nature text-white py-4 rounded-xl font-bold uppercase tracking-[0.2em] text-[12px] shadow-lg hover:bg-black transition-all duration-300 transform hover:scale-[1.01]">
                                    <i class="fas fa-check-circle mr-2 opacity-50"></i> <?= $id ? 'Update Mission' : 'Launch Seva' ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

    <script>
        const iconInput = document.getElementById('icon-input');
        const iconPreview = document.getElementById('icon-preview');

        function setIcon(iconClass) {
            iconInput.value = iconClass;
            updatePreview(iconClass);
        }

        function updatePreview(val) {
            iconPreview.className = val + ' text-4xl text-saffron transition-all group-hover:scale-110';
        }

        iconInput.addEventListener('input', function() {
            updatePreview(this.value);
        });
    </script>
</body>
</html>