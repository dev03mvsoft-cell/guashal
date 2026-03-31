<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

$message = '';
$error = '';

// Handle Delete (now inside Dashboard)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Event expunged successfully!";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM events WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $message = count($ids) . " events expunged successfully!";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}

// Fetch all events
$events = [];
try {
    $stmt = $pdo->query("SELECT * FROM events ORDER BY start_date DESC");
    if ($stmt) $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Events archive unreachable.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Divine Chronicle - Admin Dashboard</title>
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

        .image-box {
            aspect-ratio: 16/10;
            overflow: hidden;
            position: relative;
            border-radius: 3rem;
            margin: 1rem;
        }
    </style>
</head>

<body class="md:h-screen bg-[#f9f7f4] flex flex-col md:flex-row overflow-hidden">
    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-16 overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-20 gap-8">
                <div>
                    <h1 style="font-family: 'Playfair Display';" class="text-5xl font-bold text-nature">Divine <span class="italic text-saffron">Chronicle</span></h1>
                    <p class="text-gray-400 mt-3 text-[12px] font-black uppercase tracking-[0.3em]">Guardian Mission Control</p>
                </div>
                <div class="flex items-center gap-6">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected events?', 'The selected gatherings will be removed from history forever.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10">
                            <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-8 h-8 rounded-lg flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="fas fa-trash-alt text-[12px]"></i>
                            </button>
                        </div>
                    </form>
                    <a href="editor.php" class="bg-nature text-white px-12 py-5 rounded-3xl font-black uppercase tracking-widest text-[11px] shadow-2xl shadow-nature/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-4">
                        <i class="fa-solid fa-plus text-xs"></i> Schedule New Event
                    </a>
                </div>
            </header>

            <div class="mb-10 flex items-center px-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded-lg border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                    <span class="text-[12px] font-black uppercase tracking-widest text-nature/40 group-hover:text-nature transition-colors">Select All Chronicles</span>
                </label>
            </div>

            <?php if ($message || isset($_GET['msg'])): ?>
                <div class="bg-nature text-white p-6 rounded-[2.5rem] text-xs mb-16 flex items-center gap-4 border-l-8 border-saffron font-black uppercase tracking-widest animate-pulse">
                    <i class="fa-solid fa-circle-check text-lg"></i> Sacred Update Successfully Synchronized
                </div>
            <?php endif; ?>

            <?php if (empty($events)): ?>
                <div class="glass p-32 rounded-[5rem] border-2 border-dashed border-gray-100 text-center flex flex-col items-center justify-center">
                    <i class="fa-solid fa-calendar-alt text-8xl text-gray-100 mb-10"></i>
                    <h3 class="text-2xl font-bold text-nature mb-4 uppercase tracking-widest">Chronicle is Empty</h3>
                    <p class="text-gray-400 text-sm max-w-sm leading-relaxed">No holy gatherings have been recorded in history yet. Start your journey today.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10">
                    <?php foreach ($events as $item):
                        $is_past = strtotime($item['end_date'] ?: $item['start_date']) < time();
                    ?>
                        <div class="grid-card">
                            <div class="image-box">
                                <?php if ($item['image_path']): ?>
                                    <img src="<?= htmlspecialchars($item['image_path']) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full bg-nature/5 flex items-center justify-center text-nature/10 text-6xl italic"><i class="fas fa-om"></i></div>
                                <?php endif; ?>

                                <div class="absolute top-6 left-6 bg-white/95 backdrop-blur px-6 py-4 rounded-3xl shadow-xl text-center min-w-[75px]">
                                    <p class="text-[12px] uppercase font-black text-gray-400 mb-1"><?= date('M', strtotime($item['start_date'])) ?></p>
                                    <p class="text-3xl font-bold text-nature leading-none"><?= date('d', strtotime($item['start_date'])) ?></p>
                                </div>
                            </div>

                            <div class="p-10 pt-4 flex-1 flex flex-col">
                                <h4 class="text-2xl font-bold text-nature mb-4 line-clamp-1 leading-tight"><?= htmlspecialchars($item['title']) ?></h4>
                                <div class="flex items-center gap-2 mb-6 text-gray-400">
                                    <i class="fa-solid fa-map-pin text-[12px] text-saffron"></i>
                                    <span class="text-[11px] font-black uppercase tracking-widest"><?= htmlspecialchars($item['location'] ?: 'Main Campus') ?></span>
                                </div>

                                <p class="text-gray-400 text-md mb-10 line-clamp-2 leading-relaxed italic"><?= htmlspecialchars($item['description'] ?: 'Sacred details awaiting record...') ?></p>

                                <div class="mt-auto pt-8 border-t border-gray-50 flex items-center gap-6">
                                    <a href="editor.php?id=<?= $item['id'] ?>" class="flex-1 bg-gray-50 text-nature py-5 rounded-[1.5rem] font-black uppercase tracking-widest text-[12px] hover:bg-saffron hover:text-white hover:shadow-xl transition-all text-center">
                                        Refine Event
                                    </a>
                                    <div class="flex items-center gap-4">
                                        <form method="POST" onsubmit="return confirmAction(event, 'Erase from history?', 'This event record will be lost.');" class="inline">
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