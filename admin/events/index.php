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
            // First fetch the image path to delete the file
            $stmt = $pdo->prepare("SELECT image_path FROM events WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            
            if ($item && $item['image_path']) {
                $file_path = '../../' . $item['image_path'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Sacred event erased from the holy chronicle.";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        try {
            // Fetch multiple image paths
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("SELECT image_path FROM events WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $files = $stmt->fetchAll();

            foreach ($files as $f) {
                if ($f['image_path']) {
                    $file_path = '../../' . $f['image_path'];
                    if (file_exists($file_path)) unlink($file_path);
                }
            }

            $stmt = $pdo->prepare("DELETE FROM events WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $message = count($ids) . " chronicles purged successfully!";
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
    $error = "The archive is currently unreachable.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sacred Events Registry</title>
    <?php include '../include/head.php'; ?>
    <style>
        .system-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.25rem;
        }
        .system-table th {
            text-align: left;
            padding: 1rem 1rem;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #ffffff;
            background: #2c4c3b;
        }
        .system-table thead tr th:first-child { border-radius: 1.25rem 0 0 1.25rem; }
        .system-table thead tr th:last-child { border-radius: 0 1.25rem 1.25rem 0; }
        
        .system-table td {
            padding: 0.5rem 1rem;
            background: #fff;
            vertical-align: middle;
            transition: all 0.3s;
        }
        .system-table tr:hover td {
            background: #f1f5f9;
        }
        .system-table tr td:first-child { border-radius: 1.25rem 0 0 1.25rem; }
        .system-table tr td:last-child { border-radius: 0 1.25rem 1.25rem 0; }
        
        .glass-card {
            background: white;
            border-radius: 2rem;
            padding: 2rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="bg-[#f8fafc] flex">
    <?php include '../include/sidebar.php'; ?>
    <main class="flex-1 p-6 lg:p-12 overflow-y-auto">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.3em] text-[13px] mb-2 block">Divine Chronicles</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature leading-tight">Master <span class="text-saffron italic">Events</span></h1>
                    <p class="text-nature/40 mt-1 text-[13px] font-medium tracking-wide">Registry of historical and upcoming holy gatherings</p>
                </div>
                <div class="flex items-center gap-4">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected events?', 'The selected gatherings will be removed from history.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10 transition-all">
                            <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-10 h-10 rounded-xl flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="fas fa-trash-alt text-[12px]"></i>
                            </button>
                        </div>
                    </form>
                    <a href="editor.php" class="bg-saffron text-white px-8 py-4 rounded-xl font-bold flex items-center gap-3 shadow-lg shadow-saffron/20 hover:scale-105 transition-all">
                        <i class="fas fa-calendar-plus text-xs"></i> <span>Schedule New</span>
                    </a>
                </div>
            </header>

            <?php if ($message || isset($_GET['msg'])): ?>
                <div class="bg-nature/10 text-nature p-4 rounded-xl mb-8 font-bold text-sm border border-nature/20 animate-fade-in shadow-sm shadow-nature/5">
                    <i class="fas fa-check-circle mr-2"></i> Event registry successfully updated.
                </div>
            <?php endif; ?>

            <div class="mb-6 flex items-center px-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                    <span class="text-[12px] font-black uppercase tracking-widest text-nature/40 group-hover:text-nature transition-colors">Select All Chronicles</span>
                </label>
            </div>

            <div class="glass-card !p-0 overflow-hidden shadow-sm">
                <table class="system-table">
                    <thead>
                        <tr>
                            <th class="w-12 h-16 pl-6">#</th>
                            <th class="w-20">Media</th>
                            <th>Event Details</th>
                            <th>Date & Time</th>
                            <th>Venue Info</th>
                            <th class="w-32 text-right pr-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($events)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-24">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-nature/5 text-nature/10 rounded-full flex items-center justify-center text-4xl mb-4 italic"><i class="fas fa-calendar-alt"></i></div>
                                        <p class="text-nature/30 uppercase font-black tracking-widest text-[11px]">No chronicles recorded yet</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($events as $item): 
                                $is_past = strtotime($item['end_date'] ?: $item['start_date']) < time();
                            ?>
                                <tr class="group">
                                    <td class="pl-6">
                                        <input type="checkbox" name="selected_ids[]" value="<?= $item['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded border-2 border-nature/10 text-saffron focus:ring-saffron cursor-pointer">
                                    </td>
                                    <td>
                                        <div class="w-12 h-12 rounded-xl overflow-hidden shadow-sm border border-nature/5">
                                            <?php if ($item['image_path']): ?>
                                                <img src="../../<?= htmlspecialchars($item['image_path']) ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full bg-nature/5 flex items-center justify-center font-black text-nature/10 text-xs italic"><i class="fas fa-om"></i></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-medium text-nature text-[16px] leading-tight mb-0.5"><?= htmlspecialchars($item['title']) ?></div>
                                        <div class="text-[11px] font-medium uppercase tracking-widest <?= $is_past ? 'text-gray-400' : 'text-saffron' ?>">
                                            <?= $is_past ? 'Concluded' : 'Active' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-[14px] font-medium text-nature/80"><?= date('d M, Y', strtotime($item['start_date'])) ?></div>
                                        <div class="text-[11px] font-normal uppercase text-nature/30 tracking-tighter italic"><?= date('l', strtotime($item['start_date'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2 text-nature/60">
                                            <i class="fas fa-map-marker-alt text-[10px] text-saffron"></i>
                                            <span class="text-[12px] font-medium"><?= htmlspecialchars($item['location'] ?: 'Main Campus') ?></span>
                                        </div>
                                    </td>
                                    <td class="pr-6 text-right">
                                        <div class="flex justify-end items-center gap-3">
                                            <a href="editor.php?id=<?= $item['id'] ?>" class="w-10 h-10 rounded-xl bg-nature/5 text-nature flex items-center justify-center hover:bg-nature hover:text-white transition-all shadow-sm border border-nature/10">
                                                <i class="fas fa-edit text-[14px]"></i>
                                            </a>
                                            <form method="POST" onsubmit="return confirmAction(event, 'Purge Event?', 'This record will be removed from history.');" class="inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button type="submit" class="w-10 h-10 rounded-xl bg-red-100/50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm border border-red-200">
                                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>