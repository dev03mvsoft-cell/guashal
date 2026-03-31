<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

$message = "";
$error = "";

// Save Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save') {
        $id = $_POST['id'] ?? null;
        $title = $_POST['title'];
        $description = $_POST['description'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'] ?: null;
        $location = $_POST['location'];

        // Handle Multiple Organizers
        $org_list = array_filter($_POST['organizers_list'] ?? [], 'trim');
        $organizers = implode(', ', $org_list);

        $image_path = $_POST['existing_image'] ?? "";

        // Image Handling
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
            $target_dir = "../../asset/img/events/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $file_ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $new_name = "event_" . time() . "." . $file_ext;
            $target_path = $target_dir . $new_name;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_path)) {
                $image_path = "/asset/img/events/" . $new_name;
            }
        }

        try {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, start_date = ?, end_date = ?, location = ?, organizers = ?, image_path = ? WHERE id = ?");
                $stmt->execute([$title, $description, $start_date, $end_date, $location, $organizers, $image_path, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO events (title, description, start_date, end_date, location, organizers, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $start_date, $end_date, $location, $organizers, $image_path]);
            }
            header("Location: index.php?msg=success");
            exit;
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Edit Fetch
$edit_data = null;
$organizer_array = [''];
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_data = $stmt->fetch();
    if ($edit_data && $edit_data['organizers']) {
        $organizer_array = explode(', ', $edit_data['organizers']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit_data ? 'Edit' : 'Create' ?> Event - Gaushala Admin</title>
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
            font-size: 0.9rem;
        }

        .input-premium:focus {
            outline: none;
            border-color: #FF6A00;
            background: white;
            box-shadow: 0 0 0 5px rgba(255, 106, 0, 0.1);
        }

        .file-drop {
            border: 2px dashed rgba(255, 106, 0, 0.2);
            border-radius: 50%;
            width: 200px;
            height: 200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-drop:hover {
            border-color: #FF6A00;
            background: rgba(255, 106, 0, 0.05);
        }

        .organizer-row {
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="min-h-screen bg-[#f7f5f2] flex flex-col md:flex-row">
    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-16 overflow-y-auto">
        <div class="max-w-5xl mx-auto">
            <header class="mb-16 flex justify-between items-center">
                <div>
                    <a href="index.php" class="text-gray-400 hover:text-nature flex items-center gap-2 font-black text-[12px] uppercase tracking-widest mb-4">
                        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature"><?= $edit_data ? 'Update' : 'Compose' ?> <span class="italic text-saffron">Revelation</span></h1>
                </div>
            </header>

            <div class="editor-container">
                <form method="POST" enctype="multipart/form-data" class="p-12 lg:p-20 space-y-12">
                    <input type="hidden" name="action" value="save">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                        <input type="hidden" name="existing_image" value="<?= $edit_data['image_path'] ?>">
                    <?php endif; ?>

                    <!-- Image Section -->
                    <div class="text-center">
                        <label for="image_file" class="file-drop group relative">
                            <?php if ($edit_data && $edit_data['image_path']): ?>
                                <img id="preview_img" src="<?= htmlspecialchars($edit_data['image_path']) ?>" class="absolute inset-0 w-full h-full object-cover rounded-full">
                            <?php else: ?>
                                <img id="preview_img" src="#" class="absolute inset-0 w-full h-full object-cover rounded-full hidden">
                            <?php endif; ?>
                            <div id="drop_text" class="relative z-10 <?= $edit_data && $edit_data['image_path'] ? 'opacity-0 group-hover:opacity-100' : '' ?> transition-opacity">
                                <i class="fas fa-camera text-3xl text-saffron opacity-50 mb-2"></i>
                                <p class="text-[9px] font-black uppercase text-gray-400">Set Flyer</p>
                            </div>
                            <input type="file" name="image_file" id="image_file" class="hidden" accept="image/*" onchange="previewImage(this)">
                        </label>
                        <p class="text-[9px] text-gray-300 mt-4 uppercase tracking-widest font-black">Divine Moment Cover</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="md:col-span-2">
                            <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3 px-1">Event Title</label>
                            <input type="text" name="title" class="input-premium font-bold text-lg" value="<?= $edit_data ? htmlspecialchars($edit_data['title']) : '' ?>" placeholder="e.g. Mahotsav 2026 Celebration" required>
                        </div>

                        <div>
                            <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3 px-1">Venue Name</label>
                            <input type="text" name="location" class="input-premium" value="<?= $edit_data ? htmlspecialchars($edit_data['location']) : '' ?>" placeholder="e.g. Main Sanctuary" required>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-3 px-1">
                                <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black">Council of Organizers</label>
                                <button type="button" onclick="addOrganizer()" class="text-[9px] font-black uppercase text-saffron hover:underline">+ Add Member</button>
                            </div>
                            <div id="organizer_list" class="space-y-3">
                                <?php foreach ($organizer_array as $org): ?>
                                    <div class="organizer-row flex gap-2">
                                        <input type="text" name="organizers_list[]" class="input-premium" value="<?= htmlspecialchars($org) ?>" placeholder="Name or Trust" required>
                                        <button type="button" onclick="this.parentElement.remove()" class="w-14 h-14 rounded-2xl bg-red-50 text-red-400 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all"><i class="fa-solid fa-xmark text-xs"></i></button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3 px-1">Sacred Start Date</label>
                            <input type="date" name="start_date" class="input-premium" value="<?= $edit_data ? htmlspecialchars($edit_data['start_date']) : '' ?>" required>
                        </div>

                        <div>
                            <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3 px-1">End Date (Optional)</label>
                            <input type="date" name="end_date" class="input-premium" value="<?= $edit_data ? htmlspecialchars($edit_data['end_date']) : '' ?>">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3 px-1">Full Divine Chronicle (Description)</label>
                            <textarea name="description" class="input-premium h-64 text-sm" placeholder="Tell the full story of this sacred gathering..."><?= $edit_data ? htmlspecialchars($edit_data['description']) : '' ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-saffron text-white py-6 rounded-[2rem] font-black uppercase tracking-widest shadow-2xl shadow-saffron/30 hover:scale-[1.02] active:scale-95 transition-all">
                        <?= $edit_data ? 'Apply Updates' : 'Publish to Chronicle' ?>
                    </button>

                    <?php if ($edit_data): ?>
                        <a href="?delete=<?= $edit_data['id'] ?>" onclick="return confirm('Erase this event from history?');" class="block text-center text-[12px] font-black uppercase text-red-500 hover:text-red-700 transition-colors">Emergency Delete</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </main>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview_img').src = e.target.result;
                    document.getElementById('preview_img').classList.remove('hidden');
                    document.getElementById('drop_text').classList.add('opacity-0');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function addOrganizer() {
            const container = document.getElementById('organizer_list');
            const row = document.createElement('div');
            row.className = 'organizer-row flex gap-2';
            row.innerHTML = `
                <input type="text" name="organizers_list[]" class="input-premium" placeholder="Name or Trust" required>
                <button type="button" onclick="this.parentElement.remove()" class="w-14 h-14 rounded-2xl bg-red-50 text-red-400 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all"><i class="fa-solid fa-xmark text-xs"></i></button>
            `;
            container.appendChild(row);
        }
    </script>
</body>

</html>