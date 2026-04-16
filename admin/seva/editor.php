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
            border: 2px solid #f1f5f9;
            border-radius: 1.25rem;
            padding: 1.25rem 1.5rem;
            width: 100%;
            transition: all 0.4s;
            font-weight: 600;
        }
        .system-input:focus {
            border-color: #FF6A00;
            box-shadow: 0 10px 30px -10px rgba(255, 106, 0, 0.2);
            outline: none;
        }
        .glass-card {
            background: white;
            border-radius: 3rem;
            padding: 3rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.05);
        }
        .label-system {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: #94a3b8;
            margin-bottom: 0.75rem;
            display: block;
            margin-left: 1rem;
        }
    </style>
</head>
<body class="bg-[#fbfcff] flex">

    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-8 lg:p-20 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            
            <header class="mb-16 flex items-center justify-between">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.4em] text-[10px] mb-2 block">Mission Configurator</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-5xl font-bold text-nature leading-tight"><?= $id ? 'Refine' : 'Add' ?> <span class="text-saffron italic">Seva</span></h1>
                </div>
                <a href="index.php" class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-nature/20 hover:text-red-500 hover:rotate-90 transition-all shadow-xl">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </header>

            <form method="POST" class="space-y-12 pb-20">
                <input type="hidden" name="action" value="save">

                <!-- STEP 1: ICON IDENTITY -->
                <div class="glass-card text-center" data-aos="fade-up">
                    <div class="w-40 h-40 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-10 border-4 border-white shadow-2xl transition-all duration-700 hover:scale-105">
                        <i id="icon-preview" class="<?= $item ? htmlspecialchars($item['icon_class']) : 'fas fa-heart' ?> text-6xl text-saffron"></i>
                    </div>
                    
                    <div class="max-w-md mx-auto space-y-2">
                        <label class="label-system">Mission Title</label>
                        <input type="text" name="title_en" required class="system-input text-center text-2xl" placeholder="e.g. Roti Seva" value="<?= $item ? htmlspecialchars($item['title_en']) : '' ?>">
                    </div>
                </div>

                <!-- STEP 2: MISSION DETAILS -->
                <div class="glass-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-2">
                            <label class="label-system">Icon Class (FontAwesome)</label>
                            <input type="text" name="icon_class" id="icon-input" required class="system-input" placeholder="fas fa-heart" value="<?= $item ? htmlspecialchars($item['icon_class']) : 'fas fa-heart' ?>">
                        </div>
                        <div class="space-y-2">
                            <label class="label-system">Theme Color</label>
                            <select name="color_class" class="system-input appearance-none">
                                <option value="saffron" <?= ($item && $item['color_class'] == 'saffron') ? 'selected' : '' ?>>Divine Saffron</option>
                                <option value="nature" <?= ($item && $item['color_class'] == 'nature') ? 'selected' : '' ?>>Nature Green</option>
                                <option value="gold" <?= ($item && $item['color_class'] == 'gold') ? 'selected' : '' ?>>Celestial Gold</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="label-system">Mission Narrative</label>
                            <textarea name="description_en" required class="system-input h-40" placeholder="Describe the impact..."><?= $item ? htmlspecialchars($item['description_en']) : '' ?></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="label-system">Display Sequence</label>
                            <input type="number" name="sort_order" required class="system-input" value="<?= $item ? $item['sort_order'] : '0' ?>">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-nature text-white py-6 rounded-3xl font-black uppercase tracking-[0.4em] text-sm shadow-2xl hover:bg-saffron transition-all duration-500 transform hover:scale-[1.02]">
                                Update Registry
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </main>

    <script>
        document.getElementById('icon-input').addEventListener('input', function() {
            document.getElementById('icon-preview').className = this.value + ' text-6xl text-saffron';
        });
    </script>
</body>
</html>