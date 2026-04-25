<?php
require_once '../include/auth.php';
require_once '../include/functions.php';
require_once '../../config/db.php';

$id = $_GET['id'] ?? null;
$item = null;
$error = '';

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM team WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
    } catch (Exception $e) {}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_en = $_POST['name_en'];
    $designation_en = $_POST['designation_en'];
    $sort_order = (int)$_POST['sort_order'];
    $image_path = $_POST['image_path'] ?: ($item['image_path'] ?? '');

    // Handle File Upload
    if (!empty($_FILES['image_file']['name'])) {
        $uploaded_path = upload_file($_FILES['image_file'], 'uploads/team');
        if ($uploaded_path) {
            // Delete old file if replacing
            if ($item && !empty($item['image_path'])) {
                cleanup_file($item['image_path']);
            }
            $image_path = $uploaded_path;
        }
    }

    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE team SET name_en=?, designation_en=?, sort_order=?, image_path=? WHERE id=?");
            $stmt->execute([$name_en, $designation_en, $sort_order, $image_path, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO team (name_en, designation_en, sort_order, image_path) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name_en, $designation_en, $sort_order, $image_path]);
        }
        header("Location: index.php?msg=Member Updated");
        exit;
    } catch (PDOException $e) { $error = $e->getMessage(); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optimize Team Portal</title>
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
            padding: 2.5rem;
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
<body class="bg-[#f8fafc] flex flex-col md:flex-row md:h-screen md:overflow-hidden">

    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-4 md:p-12 md:overflow-y-auto h-full">
        <div class="max-w-4xl mx-auto">
            
            <header class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-0">
                <div>
                    <span class="text-saffron font-bold uppercase tracking-[0.3em] text-[12px] mb-1 block">HR Administration</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature leading-tight"><?= $id ? 'Edit' : 'Add' ?> <span class="text-saffron italic">Member</span></h1>
                </div>
                <a href="index.php" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-nature/40 hover:text-red-500 hover:rotate-90 transition-all shadow-md border border-gray-100">
                    <i class="fas fa-times text-lg"></i>
                </a>
            </header>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-6 rounded-2xl border border-red-100 font-bold mb-8 text-sm">
                    <i class="fas fa-shield-alt mr-3"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6 pb-20">
                <div class="glass-card" data-aos="fade-up">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <!-- Col 1: Portrait -->
                        <div class="flex flex-col items-center">
                            <label class="label-system text-center w-full">Portrait</label>
                            <div class="relative group w-24 h-24 mb-4">
                                <div class="w-full h-full rounded-2xl border-2 border-slate-100 shadow-lg overflow-hidden relative bg-slate-50">
                                    <img id="img-preview" src="<?= $item ? htmlspecialchars($item['image_path']) : '#' ?>" 
                                         class="w-full h-full object-cover <?= $item && $item['image_path'] ? '' : 'hidden' ?>">
                                    <?php if(!$item || !$item['image_path']): ?>
                                        <div id="img-placeholder" class="w-full h-full flex items-center justify-center text-slate-200">
                                            <i class="fas fa-user-circle text-4xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <label for="image_file" class="absolute bottom-0 right-0 w-8 h-8 bg-nature text-white rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:scale-110 transition-transform border-2 border-white">
                                    <i class="fas fa-camera text-xs"></i>
                                </label>
                                <input type="file" name="image_file" id="image_file" class="hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="image_path" id="image_path" value="<?= $item ? htmlspecialchars($item['image_path']) : '' ?>">
                            </div>
                        </div>

                        <!-- Col 2 & 3: Details -->
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
                            <div class="md:col-span-2 space-y-1">
                                <label class="label-system">Full Name</label>
                                <input type="text" name="name_en" required class="system-input" placeholder="e.g. Shri Rajesh Patel" value="<?= $item ? htmlspecialchars($item['name_en']) : '' ?>">
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Official Designation</label>
                                <input type="text" name="designation_en" required class="system-input" placeholder="Manager / Coordinator" value="<?= $item ? htmlspecialchars($item['designation_en']) : '' ?>">
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Sort Order</label>
                                <input type="number" name="sort_order" required class="system-input" value="<?= $item ? $item['sort_order'] : '0' ?>">
                            </div>
                            <div class="md:col-span-2 pt-4">
                                <button type="submit" class="w-full bg-nature text-white py-4 rounded-2xl font-bold uppercase tracking-[0.2em] text-[13px] shadow-lg hover:bg-black transition-all duration-300 transform hover:scale-[1.01]">
                                    <i class="fas fa-check-circle mr-2 opacity-50"></i> <?= $id ? 'Update Record' : 'Enroll Member' ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('img-preview');
            const placeholder = document.getElementById('img-placeholder');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if(placeholder) placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>