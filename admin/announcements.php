<?php
require_once 'include/auth.php';
require_once '../config/db.php';

$message = '';
$error = '';

// Handle Create or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save') {
        $id = $_POST['id'] ?? null;
        $msg_en = $_POST['message_en'];
        $msg_hi = $_POST['message_hi'];
        $msg_gu = $_POST['message_gu'];

        try {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE announcements SET message_en = ?, message_hi = ?, message_gu = ? WHERE id = ?");
                $stmt->execute([$msg_en, $msg_hi, $msg_gu, $id]);
                $message = "Announcement updated successfully!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO announcements (message_en, message_hi, message_gu) VALUES (?, ?, ?)");
                $stmt->execute([$msg_en, $msg_hi, $msg_gu]);
                $message = "Announcement added successfully!";
            }
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Announcement deleted successfully!";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM announcements WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $message = count($ids) . " announcements purged successfully!";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}

// Fetch all announcements
$announcements = [];
try {
    $stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC");
    if ($stmt) {
        $announcements = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    // Table likely doesn't exist yet
    $error = "Table 'announcements' not found. Please create it first using the SQL Executor.";
}

// Fetch single for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM announcements WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements Manager - Gaushala Admin</title>
    <?php include 'include/head.php'; ?>
    <style>
        .input-round {
            border-radius: 1rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.3s;
            background: white;
        }

        .input-round:focus {
            outline: none;
            border-color: #FF6A00;
            box-shadow: 0 0 0 4px rgba(255, 106, 0, 0.1);
        }
    </style>
</head>

<body class="bg-[#fdfaf7] flex flex-col md:flex-row md:h-screen md:overflow-hidden">

    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 p-4 md:p-12 md:overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12">
                <div class="mb-6 md:mb-0">
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold">Latest <span class="italic text-[#FF6A00]">Announcements</span></h1>
                    <p class="text-gray-400 mt-2 text-[12px] tracking-widest uppercase font-bold">ADMIN DASHBOARD • CORE MANAGEMENT</p>
                </div>

                <div class="flex items-center gap-4">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected?', 'The selected announcements will be deleted forever.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10 transition-all">
                            <span class="text-[14px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-8 h-8 rounded-lg flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="fas fa-trash-alt text-[14px]"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </header>

            <div class="mb-8 flex items-center px-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded-lg border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                    <span class="text-[14px] font-black uppercase tracking-widest text-nature/40 group-hover:text-nature transition-colors">Select All Messages</span>
                </label>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

                <!-- Management Form (Create/Edit) -->
                <div class="lg:col-span-1">
                    <div class="glass p-8 rounded-[2.5rem] shadow-2xl border-t-8 border-[#FF6A00]">
                        <h3 class="text-xl font-bold mb-8 flex items-center gap-3">
                            <i class="fas fa-edit text-[#FF6A00]"></i>
                            <?= $edit_data ? 'Edit' : 'Add New' ?> Announcement
                        </h3>

                        <?php if ($message): ?>
                            <div class="bg-green-50 text-green-700 p-4 rounded-xl text-[12px] mb-6 border-l-4 border-green-500 font-bold"><?= $message ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="bg-red-50 text-red-700 p-4 rounded-xl text-[12px] mb-6 border-l-4 border-red-500 font-bold"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="action" value="save">
                            <?php if ($edit_data): ?>
                                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                            <?php endif; ?>

                            <div>
                                <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-bold mb-3">Announcement (English)</label>
                                <textarea name="message_en" class="input-round h-32 text-[15px]" required><?= $edit_data ? htmlspecialchars($edit_data['message_en']) : '' ?></textarea>
                            </div>
                            <div>
                                <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-bold mb-3">Announcement (Hindi)</label>
                                <textarea name="message_hi" class="input-round h-24 text-[15px]"><?= $edit_data ? htmlspecialchars($edit_data['message_hi']) : '' ?></textarea>
                            </div>
                            <div>
                                <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3 px-1">Announcement (Gujarati)</label>
                                <textarea name="message_gu" class="input-round h-24 text-[15px]"><?= $edit_data ? htmlspecialchars($edit_data['message_gu']) : '' ?></textarea>
                            </div>

                            <button type="submit" class="w-full bg-[#FF6A00] text-white py-4 rounded-2xl font-bold text-[15px] hover:shadow-2xl hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-save opacity-50"></i>
                                <?= $edit_data ? 'Update' : 'Publish' ?> Announcement
                            </button>

                            <?php if ($edit_data): ?>
                                <a href="announcements.php" class="block text-center text-[12px] font-bold text-gray-400 hover:text-nature transition-colors mt-4 italic">Cancel Editing</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- List of Announcements -->
                <div class="lg:col-span-2">
                    <div class="space-y-6">
                        <?php if (empty($announcements)): ?>
                            <div class="glass p-20 rounded-[2.5rem] border-2 border-dashed border-gray-200 text-center">
                                <div class="text-4xl opacity-20 mb-4">🕊️</div>
                                <h4 class="text-gray-400 font-bold uppercase tracking-widest text-[15px]">No announcements found</h4>
                                <p class="text-gray-300 text-[15px] mt-2">Latest news will appear here once you publish them.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($announcements as $item): ?>
                                <div class="glass p-8 rounded-[2rem] shadow-lg border border-white/40 hover:shadow-2xl transition-all group">
                                    <div class="flex flex-col md:flex-row justify-between items-start gap-6">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-4 mb-4">
                                                <span class="text-[14px] font-bold bg-[#FF6A00]/10 text-[#FF6A00] px-3 py-1 rounded-full uppercase tracking-tighter">ID #<?= $item['id'] ?></span>
                                                <span class="text-[14px] font-bold text-gray-300 uppercase italic"><?= date('d M, Y • H:i', strtotime($item['created_at'])) ?></span>
                                            </div>
                                            <p class="text-nature font-bold text-lg mb-4"><?= htmlspecialchars($item['message_en']) ?></p>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="p-3 bg-gray-50 rounded-xl">
                                                    <span class="text-[14px] font-bold uppercase text-gray-300 block mb-1">HINDI</span>
                                                    <p class="text-xl text-gray-500 line-clamp-2"><?= htmlspecialchars($item['message_hi'] ?: 'N/A') ?></p>
                                                </div>
                                                <div class="p-3 bg-gray-50 rounded-xl">
                                                    <span class="text-[14px] font-bold uppercase text-gray-300 block mb-1">GUJARATI</span>
                                                    <p class="text-xl text-gray-500 line-clamp-2"><?= htmlspecialchars($item['message_gu'] ?: 'N/A') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 self-end md:self-start">
                                            <a href="?edit=<?= $item['id'] ?>" class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-nature hover:shadow-sm transition-all" title="Edit">
                                                <i class="fas fa-pen text-sm"></i>
                                            </a>
                                            <form method="POST" onsubmit="return confirmAction(event, 'Archive this?', 'This will remove the announcement.');" class="inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button type="submit" class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-red-500 hover:shadow-sm transition-all" title="Delete">
                                                    <i class="fas fa-trash-alt text-sm"></i>
                                                </button>
                                            </form>

                                            <!-- Bulk Selection Checkbox -->
                                            <div class="ml-2 flex items-center">
                                                <input type="checkbox" name="selected_ids[]" value="<?= $item['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded-lg border-2 border-nature/5 text-saffron focus:ring-saffron cursor-pointer shadow-inner">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
    </main>
</body>

</html>