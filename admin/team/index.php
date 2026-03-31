<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete') {
        $pdo->prepare("DELETE FROM team WHERE id = ?")->execute([$_POST['id']]);
        header("Location: index.php?msg=Member Released From Service");
        exit;
    }

    if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $pdo->prepare("DELETE FROM team WHERE id IN ($placeholders)")->execute($ids);
        header("Location: index.php?msg=" . count($ids) . " Members Released From Service");
        exit;
    }
}

// Fetch all
$items = [];
try {
    $items = $pdo->query("SELECT * FROM team ORDER BY sort_order ASC, created_at DESC")->fetchAll();
} catch (Exception $e) {
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sacred Guardians - Gaushala Admin</title>
    <?php include '../include/head.php'; ?>
    <style>
        .guardian-card {
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .guardian-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 50px 100px -20px rgba(44, 76, 59, 0.15);
            border-color: #FF6A00;
        }

        .image-glow {
            position: absolute;
            inset: -1px;
            background: linear-gradient(to bottom, #FF6A00, transparent);
            opacity: 0;
            transition: opacity 0.5s;
            z-index: 1;
            border-radius: inherit;
        }

        .guardian-card:hover .image-glow {
            opacity: 0.1;
        }
    </style>
</head>

<body class="md:h-screen flex flex-col md:flex-row bg-[#faf8f6] text-nature/80 overflow-hidden">

    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-12 overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">

            <!-- Hero Header -->
            <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-20 gap-8">
                <div>
                    <h1 style="font-family: 'Playfair Display';" class="text-6xl font-bold text-nature mb-4 tracking-tight">Management <span class="italic text-[#FF6A00]">Team</span></h1>
                    <div class="flex items-center gap-6 text-[12px] tracking-[0.4em] font-black text-nature/40 uppercase">
                        <span>Sanctuary Guardians</span>
                        <div class="w-1.5 h-1.5 bg-[#FF6A00] rounded-full animate-pulse"></div>
                        <span class="text-[#FF6A00]"><?= count($items) ?> Divine Souls</span>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected?', 'The selected members will be released from the council.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-[2rem] border border-red-100 shadow-xl shadow-red-500/10 transition-all">
                            <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-8 h-8 rounded-lg flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="fas fa-trash-alt text-[12px]"></i>
                            </button>
                        </div>
                    </form>
                    <a href="editor.php" class="bg-nature text-white px-10 py-5 rounded-[2rem] font-bold text-[15px] shadow-2xl hover:shadow-[#FF6A00]/20 hover:bg-[#FF6A00] transition-all flex items-center gap-4 group">
                        <i class="fas fa-plus-circle text-xl group-hover:rotate-90 transition-transform"></i>
                        <span>Onboard Member</span>
                    </a>
                </div>
            </header>

            <div class="mb-12 flex items-center px-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-6 h-6 rounded-lg border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                    <span class="text-[12px] font-black uppercase tracking-[0.3em] text-nature/20 group-hover:text-nature transition-colors">Select All Guardians</span>
                </label>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="bg-white border-l-8 border-nature p-8 rounded-[2.5rem] mb-16 shadow-2xl flex items-center gap-6 animate-slide-in">
                    <div class="w-12 h-12 bg-nature/5 text-nature rounded-full flex items-center justify-center text-xl shadow-inner italic font-display">G</div>
                    <span class="text-[15px] font-bold tracking-widest leading-loose text-nature/60 italic"><?= htmlspecialchars($_GET['msg']) ?></span>
                </div>
            <?php endif; ?>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-12">
                <?php if (empty($items)): ?>
                    <div class="col-span-full py-40 text-center glass rounded-[4rem] border-2 border-dashed border-nature/10">
                        <i class="fas fa-users-cog text-9xl text-nature/5 mb-10"></i>
                        <h2 class="text-2xl font-display italic text-nature/30 uppercase tracking-[0.2em]">The management council is currently quiet...</h2>
                        <a href="editor.php" class="text-[#FF6A00] font-black uppercase text-[12px] tracking-widest mt-8 inline-block hover:underline">Begin Onboarding <i class="fas fa-arrow-right ml-2 text-[12px]"></i></a>
                    </div>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <div class="guardian-card glass p-8 rounded-[3.5rem] bg-white border border-nature/5 flex flex-col items-center text-center group relative overflow-hidden h-full">
                            <div class="image-glow"></div>

                            <!-- Profile Image Container -->
                            <div class="w-48 h-48 rounded-[3rem] overflow-hidden mb-8 relative z-10 border-4 border-nature/5 shadow-2xl group-hover:scale-105 transition-transform duration-700">
                                <?php if ($item['image_path']): ?>
                                    <img src="<?= htmlspecialchars($item['image_path']) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($item['name_en']) ?>">
                                <?php else: ?>
                                    <div class="w-full h-full bg-nature/5 flex items-center justify-center text-5xl text-nature/10 italic font-display"><?= substr($item['name_en'], 0, 1) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="relative z-10 w-full">
                                <span class="bg-nature/5 text-nature py-1 px-4 rounded-full text-[12px] font-black tracking-widest uppercase mb-4 inline-block"><?= htmlspecialchars($item['designation_en']) ?></span>
                                <h3 class="text-2xl font-bold text-nature mb-2 leading-tight"><?= htmlspecialchars($item['name_en']) ?></h3>
                            </div>

                            <!-- Actions Overlay -->
                            <div class="mt-10 flex gap-4 opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-y-10 group-hover:translate-y-0 relative z-10">
                                <a href="editor.php?id=<?= $item['id'] ?>" class="w-12 h-12 bg-nature text-white rounded-2xl flex items-center justify-center hover:bg-[#FF6A00] transition-all shadow-xl"><i class="fas fa-pen text-sm"></i></a>
                                <form method="POST" class="inline" onsubmit="return confirm('Release this member from the digital council?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="w-12 h-12 bg-white border border-red-100 text-red-500 rounded-2xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl"><i class="fas fa-trash text-sm"></i></button>
                                </form>
                                <!-- Multi Select Checkbox -->
                                <input type="checkbox" name="selected_ids[]" value="<?= $item['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-6 h-6 rounded-lg border-2 border-nature/5 text-saffron focus:ring-saffron cursor-pointer shadow-inner">
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

</body>

</html>