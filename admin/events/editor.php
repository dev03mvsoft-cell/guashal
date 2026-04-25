<?php
require_once '../include/auth.php';
require_once '../include/functions.php';
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

        // 🛡️ Optimized Image Handling via Universal Functions
        if (!empty($_FILES['image_file']['name'])) {
            $uploaded_path = upload_file($_FILES['image_file'], 'uploads/events');
            if ($uploaded_path) {
                // Delete old file if replacing
                if ($id && !empty($_POST['existing_image'])) {
                     cleanup_file($_POST['existing_image']);
                }
                $image_path = $uploaded_path;
            } else {
                $error = "Failed to process image upload.";
            }
        }

        if (empty($error)) {
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
        .system-input {
            background: #fff;
            border: 2px solid #f8fafc;
            border-radius: 1rem;
            padding: 0.75rem 1.25rem;
            width: 100%;
            transition: all 0.4s;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .system-input:focus {
            border-color: #FF6A00;
            box-shadow: 0 10px 30px -10px rgba(255, 106, 0, 0.2);
            outline: none;
        }
        .glass-card {
            background: white;
            border-radius: 2rem;
            padding: 2rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
        }
        .label-system {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #475569;
            margin-bottom: 0.6rem;
            display: block;
            margin-left: 0.25rem;
        }
    </style>
</head>

<body class="bg-[#f8fafc] flex">
    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-12 overflow-y-auto">
        <div class="max-w-5xl mx-auto">
            <header class="mb-8 flex justify-between items-center">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.3em] text-[12px] mb-1 block">Divine Events</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature"><?= $edit_data ? 'Update' : 'Compose' ?> <span class="italic text-saffron">Revelation</span></h1>
                </div>
                <a href="index.php" class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-nature/40 hover:text-red-500 hover:rotate-90 transition-all shadow-lg border border-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </header>

            <form method="POST" enctype="multipart/form-data" class="space-y-6 pb-20">
                <input type="hidden" name="action" value="save">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <input type="hidden" name="existing_image" value="<?= $edit_data['image_path'] ?>">
                <?php endif; ?>

                <div class="glass-card" data-aos="fade-up">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        <!-- Col 1: Flyer -->
                        <div class="flex flex-col items-center">
                            <label class="label-system text-center w-full">Event Flyer</label>
                            <div class="relative group w-32 h-32 mb-3">
                                <div class="w-full h-full rounded-2xl border-2 border-white shadow-lg overflow-hidden relative bg-slate-50">
                                    <?php if ($edit_data && $edit_data['image_path']): ?>
                                        <img id="preview_img" src="<?= htmlspecialchars($edit_data['image_path']) ?>" class="absolute inset-0 w-full h-full object-cover">
                                    <?php else: ?>
                                        <img id="preview_img" src="#" class="absolute inset-0 w-full h-full object-cover hidden">
                                        <div id="drop_text" class="absolute inset-0 flex flex-col items-center justify-center text-slate-200">
                                            <i class="fas fa-calendar-plus text-3xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <label for="image_file" class="absolute bottom-0 right-0 w-8 h-8 bg-saffron text-white rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:scale-110 transition-transform border-2 border-white">
                                    <i class="fas fa-camera text-xs"></i>
                                </label>
                                <input type="file" name="image_file" id="image_file" class="hidden" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-tight">Moment Cover</p>
                        </div>

                        <!-- Col 2 & 3: Details -->
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
                            <div class="md:col-span-2 space-y-1">
                                <label class="label-system">Event Title</label>
                                <input type="text" name="title" class="system-input" value="<?= $edit_data ? htmlspecialchars($edit_data['title']) : '' ?>" placeholder="e.g. Mahotsav 2026 Celebration" required>
                            </div>

                            <div class="space-y-1">
                                <label class="label-system">Venue Name</label>
                                <input type="text" name="location" class="system-input" value="<?= $edit_data ? htmlspecialchars($edit_data['location']) : '' ?>" placeholder="e.g. Main Sanctuary" required>
                            </div>

                            <div class="space-y-1">
                                <label class="label-system">Start Date</label>
                                <input type="date" name="start_date" class="system-input" value="<?= $edit_data ? htmlspecialchars($edit_data['start_date']) : '' ?>" required>
                            </div>

                            <div class="space-y-1">
                                <label class="label-system">End Date (Optional)</label>
                                <input type="date" name="end_date" class="system-input" value="<?= $edit_data ? htmlspecialchars($edit_data['end_date']) : '' ?>">
                            </div>

                            <div class="md:col-span-2 space-y-1">
                                <label class="label-system">Full Description</label>
                                <textarea name="description" class="system-input h-32" placeholder="Tell the full story of this gathering..."><?= $edit_data ? htmlspecialchars($edit_data['description']) : '' ?></textarea>
                            </div>

                            <div class="md:col-span-2 flex items-end pt-2">
                                <button type="submit" class="w-full bg-saffron text-white py-3 rounded-xl font-black uppercase tracking-[0.2em] text-[11px] shadow-lg hover:scale-[1.02] active:scale-95 transition-all">
                                    <?= $edit_data ? 'Apply Updates' : 'Publish to Chronicle' ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
                <input type="text" name="organizers_list[]" class="system-input" placeholder="Name" required>
                <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 rounded-xl bg-red-50 text-red-400 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all"><i class="fa-solid fa-xmark text-xs"></i></button>
            `;
            container.appendChild(row);
        }
    </script>
</body>

</html>