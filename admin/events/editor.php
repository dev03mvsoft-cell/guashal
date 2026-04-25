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

<body class="bg-[#fcfdfd] flex flex-col md:flex-row md:h-screen md:overflow-hidden">
    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-4 md:p-12 md:overflow-y-auto h-full">
        <div class="max-w-5xl mx-auto">
            <header class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-0" data-aos="fade-down">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.3em] text-[12px] mb-2 block">Sacred Chronicles</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl md:text-5xl font-bold text-nature leading-tight">
                        <?= $edit_data ? 'Refine' : 'Orchestrate' ?> <span class="italic text-saffron">Event</span>
                    </h1>
                </div>
                <a href="index.php" class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-nature/20 hover:text-red-500 hover:rotate-90 transition-all shadow-xl border border-gray-100 group">
                    <i class="fas fa-times text-xl group-hover:scale-110"></i>
                </a>
            </header>

            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-32">
                <input type="hidden" name="action" value="save">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <input type="hidden" name="existing_image" value="<?= $edit_data['image_path'] ?>">
                <?php endif; ?>

                <!-- Layout Sidebar: Image & Organizers -->
                <div class="space-y-8">
                    <!-- Image Card -->
                    <div class="glass-card bg-white p-8" data-aos="fade-right">
                        <label class="label-system text-center mb-6">Ceremony Visual</label>
                        <div class="relative group mx-auto w-full aspect-square max-w-[240px]">
                            <div class="w-full h-full rounded-[2.5rem] border-4 border-gray-50 shadow-2xl overflow-hidden relative bg-slate-50">
                                <?php if ($edit_data && $edit_data['image_path']): ?>
                                    <img id="preview_img" src="<?= htmlspecialchars($edit_data['image_path']) ?>" class="absolute inset-0 w-full h-full object-cover">
                                <?php else: ?>
                                    <img id="preview_img" src="#" class="absolute inset-0 w-full h-full object-cover hidden">
                                    <div id="drop_text" class="absolute inset-0 flex flex-col items-center justify-center text-slate-200">
                                        <i class="fas fa-calendar-stars text-5xl mb-4"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-nature/20">Awaiting Cover</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <label for="image_file" class="absolute -bottom-4 -right-4 w-12 h-12 bg-nature text-white rounded-2xl flex items-center justify-center cursor-pointer shadow-2xl hover:scale-110 active:scale-95 transition-all border-4 border-white z-10">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" name="image_file" id="image_file" class="hidden" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <p class="text-center text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mt-8 leading-relaxed px-4">
                            Upload a high-fidelity visual to represent this sacred gathering.
                        </p>
                    </div>

                    <!-- Organizers Card -->
                    <div class="glass-card bg-white p-8" data-aos="fade-right" data-aos-delay="100">
                        <label class="label-system mb-4">Event Guardians</label>
                        <div id="organizer-container" class="space-y-3">
                            <?php foreach ($organizer_array as $org): ?>
                                <div class="flex gap-2 group">
                                    <input type="text" name="organizers_list[]" value="<?= htmlspecialchars($org) ?>" 
                                           class="system-input text-sm" placeholder="e.g. Temple Trust">
                                    <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 flex items-center justify-center text-red-100 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                        <i class="fas fa-minus-circle"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" onclick="addOrganizer()" class="mt-6 w-full py-3 border-2 border-dashed border-gray-100 rounded-xl text-gray-300 hover:border-nature/20 hover:text-nature transition-all text-[11px] font-bold uppercase tracking-widest">
                            <i class="fas fa-plus-circle mr-2"></i> Add Host
                        </button>
                    </div>
                </div>

                <!-- Main Details Card -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="glass-card bg-white p-10" data-aos="fade-left">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div class="md:col-span-2 space-y-1">
                                <label class="label-system">Event Revelation Title</label>
                                <input type="text" name="title" class="system-input" value="<?= $edit_data ? htmlspecialchars($edit_data['title']) : '' ?>" placeholder="e.g. Grand Aarti Mahotsav" required>
                            </div>

                            <div class="md:col-span-2 space-y-1">
                                <label class="label-system">Sanctuary Location / Venue</label>
                                <div class="relative">
                                    <i class="fas fa-map-marker-alt absolute left-5 top-1/2 -translate-y-1/2 text-saffron opacity-40"></i>
                                    <input type="text" name="location" class="system-input pl-12" value="<?= $edit_data ? htmlspecialchars($edit_data['location']) : '' ?>" placeholder="e.g. Main Hall, Gaushala Complex" required>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="label-system">Commencement Date</label>
                                <input type="date" name="start_date" class="system-input" value="<?= $edit_data ? htmlspecialchars($edit_data['start_date']) : '' ?>" required>
                            </div>

                            <div class="space-y-1">
                                <label class="label-system">Conclusion (Optional)</label>
                                <input type="date" name="end_date" class="system-input" value="<?= $edit_data ? htmlspecialchars($edit_data['end_date']) : '' ?>">
                            </div>

                            <div class="md:col-span-2 space-y-1">
                                <label class="label-system">Divine Narrative / description</label>
                                <textarea name="description" class="system-input h-64 font-normal text-nature/70 leading-relaxed" placeholder="Chronicle the details of this event..."><?= $edit_data ? htmlspecialchars($edit_data['description']) : '' ?></textarea>
                            </div>
                        </div>

                        <div class="mt-12 flex justify-end">
                            <button type="submit" class="bg-nature text-white px-12 py-5 rounded-[1.25rem] font-bold uppercase tracking-[0.25em] text-[13px] shadow-2xl hover:bg-black transition-all transform hover:-translate-y-1 active:scale-95">
                                <i class="fas fa-check-double mr-3 opacity-30"></i> <?= $edit_data ? 'Update Event' : 'Commit to History' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        function addOrganizer() {
            const container = document.getElementById('organizer-container');
            const div = document.createElement('div');
            div.className = 'flex gap-2 group animate-in fade-in slide-in-from-top-2 duration-300';
            div.innerHTML = `
                <input type="text" name="organizers_list[]" class="system-input text-sm" placeholder="e.g. Temple Trust">
                <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 flex items-center justify-center text-red-500 transition-colors">
                    <i class="fas fa-minus-circle"></i>
                </button>
            `;
            container.appendChild(div);
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('preview_img');
                    const text = document.getElementById('drop_text');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    if (text) text.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>