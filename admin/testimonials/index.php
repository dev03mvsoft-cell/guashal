<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

// Handle Delete (One line, clean)
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $pdo->prepare("DELETE FROM testimonials WHERE id = ?")->execute([$_POST['id']]);
    header("Location: index.php?msg=Voice Quietly Arrived");
    exit;
}

// Handle Bulk Delete
if (isset($_POST['action']) && $_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
    $ids = $_POST['selected_ids'];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $pdo->prepare("DELETE FROM testimonials WHERE id IN ($placeholders)")->execute($ids);
    header("Location: index.php?msg=" . count($ids) . " Voices Quietly Arrived");
    exit;
}

// Fetch all
$items = [];
try {
    $items = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC")->fetchAll();
} catch (Exception $e) {
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voices Of Devotion - Gaushala Admin</title>
    <?php include '../include/head.php'; ?>
    <style>
        .testi-card {
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .testi-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 40px 80px -20px rgba(184, 134, 11, 0.15);
            border-color: #B8860B;
        }
    </style>
</head>

<body class="md:h-screen flex flex-col md:flex-row bg-[#fdfaf7] text-nature/80 overflow-hidden">

    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-12 overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">

            <!-- Dual Header -->
            <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-16 gap-8">
                <div>
                    <h1 style="font-family: 'Playfair Display';" class="text-5xl font-bold text-nature mb-4">Voices Of <span class="italic text-gold">Devotees</span></h1>
                    <div class="flex items-center gap-4 text-[12px] tracking-[0.3em] font-black text-nature/30 uppercase">
                        <span>Management Area</span>
                        <span class="w-1 h-1 bg-gold rounded-full"></span>
                        <span class="text-gold"><?= count($items) ?> Total Stories</span>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected?', 'The selected devotee voices will be deleted forever.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10">
                            <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-8 h-8 rounded-lg flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="fas fa-trash-alt text-[12px]"></i>
                            </button>
                        </div>
                    </form>
                    <a href="editor.php" class="bg-nature text-white px-10 py-5 rounded-3xl font-bold text-[15px] shadow-2xl hover:bg-gold hover:text-nature transition-all flex items-center gap-4 group">
                        <span class="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center group-hover:bg-nature/10"><i class="fas fa-plus"></i></span>
                        Publish New Voice
                    </a>
                </div>
            </header>

            <div class="mb-10 flex items-center px-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded-lg border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                    <span class="text-[12px] font-black uppercase tracking-[0.3em] text-nature/40 group-hover:text-nature transition-colors">Select All Chronicles</span>
                </label>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="bg-nature text-white p-6 rounded-3xl mb-12 shadow-xl flex items-center gap-4 animate-bounce">
                    <i class="fas fa-check-circle text-gold"></i>
                    <span class="text-[15px] font-bold tracking-widest uppercase"><?= htmlspecialchars($_GET['msg']) ?></span>
                </div>
            <?php endif; ?>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <?php if (empty($items)): ?>
                    <div class="col-span-full py-40 text-center glass rounded-[4rem] border-2 border-dashed border-gold/10">
                        <i class="fas fa-quote-left text-9xl text-gold/5 mb-8"></i>
                        <h2 class="text-2xl font-display italic text-nature/20">The sanctuary awaits its first echo of devotion...</h2>
                    </div>
                <?php else: ?>
                    <?php foreach ($items as $item):
                        $initial = substr($item['name'], 0, 1);
                    ?>
                        <div class="testi-card glass p-10 rounded-[3.5rem] bg-white/60 border border-gold/5 flex flex-col justify-between group h-full relative overflow-hidden">
                            <!-- Spiritual Pattern Overlay -->
                            <div class="absolute -right-8 -top-8 w-32 h-32 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                                <i class="fa-solid fa-om text-9xl"></i>
                            </div>

                            <div class="relative z-10">
                                <div class="flex gap-1 mb-8">
                                    <?php for ($i = 0; $i < $item['rating']; $i++): ?><i class="fas fa-star text-gold text-[12px]"></i><?php endfor; ?>
                                </div>
                                <p class="text-nature/70 font-display text-lg leading-relaxed italic mb-10 min-h-[120px]">"<?= htmlspecialchars($item['testimonial']) ?>"</p>
                            </div>

                            <div class="flex items-center justify-between border-t border-gold/5 pt-8 relative z-10">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 bg-gradient-to-br from-gold/20 to-saffron/20 rounded-full flex items-center justify-center text-nature font-black text-lg shadow-inner"><?= $initial ?></div>
                                    <div>
                                        <h4 class="font-bold text-base text-nature"><?= htmlspecialchars($item['name']) ?></h4>
                                        <span class="text-[12px] font-black uppercase tracking-widest text-saffron"><?= htmlspecialchars($item['role']) ?></span>
                                    </div>
                                </div>

                                <div class="flex gap-3 translate-x-10 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-500">
                                    <a href="editor.php?id=<?= $item['id'] ?>" class="w-10 h-10 bg-nature text-white rounded-2xl flex items-center justify-center hover:bg-gold transition-colors shadow-lg"><i class="fas fa-pen text-[12px]"></i></a>
                                    <form method="POST" class="inline" onsubmit="return confirmAction(event, 'Silence Voice?', 'This will permanently remove the testimony.');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="w-10 h-10 bg-white border border-red-100 text-red-500 rounded-2xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-lg"><i class="fas fa-trash text-[12px]"></i></button>
                                    </form>
                                    <!-- Multi Select Checkbox -->
                                    <input type="checkbox" name="selected_ids[]" value="<?= $item['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded-lg border-2 border-nature/5 text-saffron focus:ring-saffron cursor-pointer shadow-inner">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

</body>

</html>