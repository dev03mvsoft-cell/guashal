<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

$message = '';
$error = '';

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM seva_options WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Seva option removed from the divine list.";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM seva_options WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $message = count($ids) . " seva options purged successfully!";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}

// Fetch all seva options
$sevas = [];
try {
    $stmt = $pdo->query("SELECT * FROM seva_options ORDER BY sort_order ASC, created_at DESC");
    if ($stmt) $sevas = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Seva archive unreachable.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seva Management - Admin Dashboard</title>
    <?php include '../include/head.php'; ?>
    <style>
        .grid-card {
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 4rem !important;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .grid-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 40px 80px rgba(44, 76, 59, 0.12);
            border-color: #FF6A00/20;
        }

        .icon-box {
            height: 160px;
            overflow: hidden;
            position: relative;
            border-radius: 3rem;
            margin: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff9f2;
            border: 1px solid rgba(255, 106, 0, 0.1);
        }
    </style>
</head>

<body class="md:h-screen bg-[#f9f7f4] flex flex-col md:flex-row overflow-hidden">
    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-16 overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-20 gap-8">
                <div>
                    <h1 style="font-family: 'Playfair Display';" class="text-5xl font-bold text-nature">Seva <span class="italic text-saffron">Opportunities</span></h1>
                    <p class="text-gray-400 mt-3 text-[12px] font-black uppercase tracking-[0.3em]">Divine Service Configuration</p>
                </div>
                <div class="flex items-center gap-6">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected?', 'The selected seva options will be deleted forever.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10">
                            <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-8 h-8 rounded-lg flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="fas fa-trash-alt text-[12px]"></i>
                            </button>
                        </div>
                    </form>
                    <a href="editor.php" class="bg-nature text-white px-12 py-5 rounded-3xl font-black uppercase tracking-widest text-[11px] shadow-2xl shadow-nature/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-4">
                        <i class="fa-solid fa-plus text-xs"></i> Create Seva Option
                    </a>
                </div>
            </header>

            <div class="mb-10 flex items-center px-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded-lg border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                    <span class="text-[12px] font-black uppercase tracking-widest text-nature/40 group-hover:text-nature transition-colors">Select All Sevas</span>
                </label>
            </div>

            <?php if ($message || isset($_GET['msg'])): ?>
                <div class="bg-nature text-white p-6 rounded-[2.5rem] text-xs mb-16 flex items-center gap-4 border-l-8 border-saffron font-black uppercase tracking-widest animate-pulse">
                    <i class="fa-solid fa-circle-check text-lg"></i> Sacred Seva Successfully Updated
                </div>
            <?php endif; ?>

            <?php if (empty($sevas)): ?>
                <div class="glass p-32 rounded-[5rem] border-2 border-dashed border-gray-100 text-center flex flex-col items-center justify-center">
                    <i class="fa-solid fa-hand-holding-heart text-8xl text-gray-100 mb-10"></i>
                    <h3 class="text-2xl font-bold text-nature mb-4 uppercase tracking-widest">No Seva Options Found</h3>
                    <p class="text-gray-400 text-sm max-w-sm leading-relaxed">Initialize the mission by creating your first service opportunity.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10">
                    <?php foreach ($sevas as $item): ?>
                        <div class="grid-card">
                            <div class="icon-box">
                                <div class="flex flex-col items-center justify-center text-saffron">
                                    <i class="<?= htmlspecialchars($item['icon_class'] ?: 'fas fa-heart') ?> text-6xl mb-4"></i>
                                    <span class="text-[12px] font-black uppercase tracking-widest bg-saffron/10 px-4 py-1.5 rounded-full"><?= htmlspecialchars($item['color_class'] ?: 'Saffron') ?></span>
                                </div>
                                <div class="absolute top-6 right-6 bg-white/95 backdrop-blur px-4 py-2 rounded-2xl shadow-sm text-center">
                                    <p class="text-[9px] uppercase font-black text-gray-400">Sort: <?= $item['sort_order'] ?></p>
                                </div>
                            </div>

                            <div class="p-10 pt-4 flex-1 flex flex-col">
                                <h4 class="text-2xl font-bold text-nature mb-4 line-clamp-1 leading-tight"><?= htmlspecialchars($item['title_en']) ?></h4>
                                <p class="text-gray-400 text-md mb-10 line-clamp-2 leading-relaxed italic"><?= htmlspecialchars($item['description_en'] ?: 'No description provided...') ?></p>

                                <div class="mt-auto pt-8 border-t border-gray-50 flex items-center gap-6">
                                    <a href="editor.php?id=<?= $item['id'] ?>" class="flex-1 bg-gray-50 text-nature py-5 rounded-[1.5rem] font-black uppercase tracking-widest text-[15px] hover:bg-saffron hover:text-white hover:shadow-xl transition-all text-center">
                                        Refine Details
                                    </a>
                                    <div class="flex items-center gap-4">
                                        <form method="POST" onsubmit="return confirmAction(event, 'Erase this seva?', 'This will remove the seva forever.');" class="inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="w-14 h-14 rounded-[1.5rem] bg-red-50 text-red-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                        <!-- Multi Select Checkbox -->
                                        <input type="checkbox" name="selected_ids[]" value="<?= $item['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-6 h-6 rounded-lg border-2 border-nature/5 text-saffron focus:ring-saffron cursor-pointer shadow-inner">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>