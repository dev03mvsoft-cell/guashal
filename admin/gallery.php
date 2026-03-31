<?php
require_once 'include/auth.php';
require_once '../config/db.php';

$message = '';
$error = '';

// Handle AJAX Quick Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'quick_upload') {
    header('Content-Type: application/json');
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../asset/img/gallery/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        $new_filename = 'auto_' . time() . '_' . uniqid() . '.' . $file_ext;
        $dest_path = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $dest_path)) {
            $image_path = '/asset/img/gallery/uploads/' . $new_filename;
            // Also index in DB for future use
            $stmt = $pdo->prepare("INSERT INTO gallery (image_path, title_en, category) VALUES (?, ?, ?)");
            $stmt->execute([$image_path, 'Auto Upload', 'General']);
            echo json_encode(['success' => true, 'path' => $image_path]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to move file']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No file or upload error']);
    }
    exit;
}

// Handle Create or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save') {
        $id = $_POST['id'] ?? null;
        $title_en = trim($_POST['title_en'] ?? '');
        $title_hi = trim($_POST['title_hi'] ?? '');
        $title_gu = trim($_POST['title_gu'] ?? '');
        $category = $_POST['category'] ?? 'General';

        $image_path = $_POST['existing_image'] ?? '';

        // Handle File Upload
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../asset/img/gallery/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $file_ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($file_ext, $allowed_exts)) {
                $new_filename = 'gallery_' . time() . '_' . uniqid() . '.' . $file_ext;
                $dest_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $dest_path)) {
                    $image_path = '/asset/img/gallery/uploads/' . $new_filename;
                } else {
                    $error = "Failed to move uploaded file.";
                }
            } else {
                $error = "Invalid file type. Only standard images are allowed.";
            }
        }

        if (empty($image_path) && empty($error)) {
            $error = "Please upload an image or provide a path.";
        }

        if (empty($error)) {
            try {
                if ($id) {
                    $stmt = $pdo->prepare("UPDATE gallery SET image_path = ?, title_en = ?, title_hi = ?, title_gu = ?, category = ? WHERE id = ?");
                    $stmt->execute([$image_path, $title_en, $title_hi, $title_gu, $category, $id]);
                    $message = "Visual updated successfully!";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO gallery (image_path, title_en, title_hi, title_gu, category) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$image_path, $title_en, $title_hi, $title_gu, $category]);
                    $message = "Visual added to the vault!";
                }
            } catch (PDOException $e) {
                $error = $e->getMessage();
            }
        }
    }

    if ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Memory removed from gallery.";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    // Handle Bulk Delete
    if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids']; // array
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM gallery WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $message = count($ids) . " visuals purged from memory.";
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}

// Fetch Items
$items = [];
try {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
    if ($stmt) $items = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Gallery table not found.";
}

// Edit Fetch
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visual Vault - Gaushala Admin</title>
    <?php include 'include/head.php'; ?>
    <style>
        .input-premium {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 1.25rem;
            padding: 1rem 1.25rem;
            width: 100%;
            transition: all 0.3s;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .input-premium:focus {
            outline: none;
            border-color: #FF6A00;
            box-shadow: 0 0 0 4px rgba(255, 106, 0, 0.1);
        }

        .card-gallery {
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .card-gallery:hover {
            transform: translateY(-10px);
        }

        .badge-cat {
            background: rgba(255, 106, 0, 0.1);
            color: #FF6A00;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .file-drop {
            border: 2px dashed rgba(255, 106, 0, 0.2);
            border-radius: 1.5rem;
            padding: 2.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(255, 106, 0, 0.02);
        }

        .file-drop:hover {
            border-color: #FF6A00;
            background: rgba(255, 106, 0, 0.05);
        }
    </style>
</head>

<body class="md:h-screen flex flex-col md:flex-row bg-[#fbf9f6] overflow-hidden">
    <?php include 'include/sidebar.php'; ?>
    <main class="flex-1 p-6 lg:p-16 overflow-y-auto h-full">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-16">
                <div>
                    <h1 style="font-family: 'Playfair Display';" class="text-5xl font-bold text-nature">Visual <span class="italic text-saffron">Vault</span></h1>
                    <p class="text-gray-400 mt-2 text-sm tracking-[0.2em] font-bold">DIGITAL ASSET MANAGEMENT</p>
                </div>

                <div class="flex items-center gap-6 mt-6 lg:mt-0">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected?', 'The selected visuals will be deleted forever.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10">
                            <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-8 h-8 rounded-lg flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="fas fa-trash-alt text-[12px]"></i>
                            </button>
                        </div>
                    </form>

                    <?php if ($edit_data): ?>
                        <a href="gallery.php" class="text-gray-400 hover:text-nature flex items-center gap-2 font-bold text-xs uppercase tracking-widest transition-colors"><i class="fas fa-times"></i> Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </header>

            <div class="mb-10 flex items-center justify-between px-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded-lg border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                    <span class="text-[12px] font-black uppercase tracking-[0.3em] text-nature/40 group-hover:text-nature transition-colors">Select All Chronicles</span>
                </label>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-12">
                <!-- Form Side -->
                <div class="xl:col-span-4">
                    <div class="glass p-10 rounded-[3rem] shadow-2xl border-t-[12px] border-saffron sticky top-8">
                        <h3 class="text-2xl font-bold mb-10 flex items-center gap-4">
                            <i class="fas fa-camera-retro text-saffron"></i> <?= $edit_data ? 'Update' : 'Capture' ?> Space
                        </h3>

                        <?php if ($message): ?><div class="bg-green-50 text-green-700 p-5 rounded-2xl text-xs mb-8 border-l-4 border-green-500 font-bold"><?= $message ?></div><?php endif; ?>
                        <?php if ($error): ?><div class="bg-red-50 text-red-700 p-5 rounded-2xl text-xs mb-8 border-l-4 border-red-500 font-bold"><?= $error ?></div><?php endif; ?>

                        <form method="POST" enctype="multipart/form-data" class="space-y-8">
                            <input type="hidden" name="action" value="save">
                            <?php if ($edit_data): ?>
                                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                                <input type="hidden" name="existing_image" value="<?= $edit_data['image_path'] ?>">
                            <?php endif; ?>

                            <div class="space-y-4">
                                <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3 px-1">Visual Media</label>
                                <label for="image_file" class="file-drop block">
                                    <div id="preview_container" class="<?= $edit_data ? '' : 'hidden' ?> mb-4">
                                        <img id="preview_img" src="<?= $edit_data ? htmlspecialchars($edit_data['image_path']) : '#' ?>" class="h-32 w-full object-cover rounded-2xl shadow-lg border border-white">
                                    </div>
                                    <div id="drop_text">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-saffron opacity-50 mb-3"></i>
                                        <h4 class="text-xs font-bold text-nature mb-1">Upload New Picture</h4>
                                        <p class="text-[9px] text-gray-400 uppercase tracking-tighter">Drag here or click to browse</p>
                                    </div>
                                    <input type="file" name="image_file" id="image_file" class="hidden" accept="image/*" onchange="previewImage(this)">
                                </label>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3 px-1">Category</label>
                                    <select name="category" class="input-premium">
                                        <option value="General" <?= ($edit_data && $edit_data['category'] == 'General') ? 'selected' : '' ?>>General</option>
                                        <option value="Events" <?= ($edit_data && $edit_data['category'] == 'Events') ? 'selected' : '' ?>>Events</option>
                                        <option value="Medical" <?= ($edit_data && $edit_data['category'] == 'Medical') ? 'selected' : '' ?>>Medical</option>
                                        <option value="Gau-Seva" <?= ($edit_data && $edit_data['category'] == 'Gau-Seva') ? 'selected' : '' ?>>Gau-Seva</option>
                                    </select>
                                </div>
                                <div><label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3 px-1">ID Tag</label>
                                    <div class="input-premium bg-gray-50 text-gray-300 text-[12px] font-mono"><?= $edit_data ? '#' . $edit_data['id'] : 'AUTOGEN' ?></div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-3 px-1">Title (English)</label>
                                <input type="text" name="title_en" class="input-premium" value="<?= $edit_data ? htmlspecialchars($edit_data['title_en']) : '' ?>" placeholder="e.g. Morning Feeding Session">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-2 px-1">Hindi Title</label>
                                    <input type="text" name="title_hi" class="input-premium !text-xs" value="<?= $edit_data ? htmlspecialchars($edit_data['title_hi']) : '' ?>">
                                </div>
                                <div><label class="block text-[12px] uppercase tracking-widest text-gray-400 font-black mb-2 px-1">Gujarati Title</label>
                                    <input type="text" name="title_gu" class="input-premium !text-xs" value="<?= $edit_data ? htmlspecialchars($edit_data['title_gu']) : '' ?>">
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-[#2c4c3b] text-white py-5 rounded-2xl font-bold uppercase tracking-widest text-xs hover:bg-[#1e3a2d] hover:shadow-2xl transition-all shadow-xl shadow-nature/20">
                                <i class="fas fa-check-circle mr-2 opacity-50"></i> <?= $edit_data ? 'Update Reflection' : 'Index to Gallery' ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Showcase Side -->
                <div class="xl:col-span-8">
                    <?php if (empty($items)): ?>
                        <div class="glass p-32 rounded-[3.5rem] border-2 border-dashed border-gray-100 text-center">
                            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl">
                                <i class="fas fa-mountain text-gray-200 text-3xl"></i>
                            </div>
                            <h4 class="text-gray-400 font-bold uppercase tracking-[0.2em] text-sm">The vault is empty</h4>
                            <p class="text-gray-300 text-xs mt-3">Start by indexing your first digital memory.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <?php foreach ($items as $item): ?>
                                <div class="glass p-6 rounded-[3rem] shadow-xl card-gallery group bg-white border border-white/40">
                                    <div class="relative overflow-hidden rounded-[2.25rem] mb-6 aspect-video bg-gray-50">
                                        <img src="<?= htmlspecialchars($item['image_path']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                        <div class="absolute top-4 left-4">
                                            <span class="badge-cat bg-white/90 backdrop-blur shadow-sm"><?= htmlspecialchars($item['category']) ?></span>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-start px-2">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-nature text-lg leading-tight group-hover:text-saffron transition-colors"><?= htmlspecialchars($item['title_en'] ?: 'No Descriptive Title') ?></h4>
                                            <p class="text-[15px] text-gray-300 mt-2 uppercase tracking-widest font-black"><?= date('F d, Y', strtotime($item['created_at'])) ?></p>
                                        </div>
                                        <div class="flex gap-3 ml-4">
                                            <?php if (isset($_GET['select'])): ?>
                                                <button type="button" onclick="selectThisImage('<?= htmlspecialchars($item['image_path']) ?>')" class="w-10 h-10 rounded-full bg-[#FF6A00] text-white flex items-center justify-center hover:scale-110 transition-all shadow-lg" title="Select This">
                                                    <i class="fas fa-check text-xs"></i>
                                                </button>
                                            <?php else: ?>
                                                <a href="?edit=<?= $item['id'] ?>" class="w-10 h-10 rounded-full bg-nature/5 flex items-center justify-center text-gray-300 hover:text-nature transition-all" title="Refine">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </a>
                                                <form method="POST" class="inline" onsubmit="return confirmAction(event, 'Delete Memory?', 'This will erase the visual from history.');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="w-10 h-10 rounded-full bg-red-50/50 flex items-center justify-center text-gray-200 hover:text-red-500 transition-all" title="Remove">
                                                        <i class="fas fa-trash-alt text-xs"></i>
                                                    </button>
                                                </form>

                                                <!-- Multi Select Checkbox -->
                                                <div class="ml-2 flex items-center">
                                                    <input type="checkbox" name="selected_ids[]" value="<?= $item['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded-lg border-2 border-nature/5 text-saffron focus:ring-saffron cursor-pointer shadow-inner">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview_img').src = e.target.result;
                    document.getElementById('preview_container').classList.remove('hidden');
                    document.getElementById('drop_text').classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function selectThisImage(path) {
            const targetId = '<?= $_GET['select'] ?? '' ?>';
            if (window.opener && !window.opener.closed) {
                const targetInput = window.opener.document.getElementById(targetId);
                if (targetInput) {
                    targetInput.value = path;
                    window.close();
                } else {
                    alert('Target input field not found in the original window.');
                }
            } else {
                alert('Connection to the editor window was lost.');
            }
        }
    </script>
</body>

</html>