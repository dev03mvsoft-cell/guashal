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
    $position_en = $_POST['position_en'];
    $bio_en = $_POST['bio_en'];
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
            $stmt = $pdo->prepare("UPDATE team SET name_en=?, position_en=?, bio_en=?, sort_order=?, image_path=? WHERE id=?");
            $stmt->execute([$name_en, $position_en, $bio_en, $sort_order, $image_path, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO team (name_en, position_en, bio_en, sort_order, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name_en, $position_en, $bio_en, $sort_order, $image_path]);
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
            border: 2px solid #f1f5f9;
            border-radius: 1.25rem;
            padding: 1.25rem 1.5rem;
            width: 100%;
            transition: all 0.4s;
            font-weight: 600;
        }
        .system-input:focus {
            border-color: #c0a50e;
            box-shadow: 0 10px 30px -10px rgba(192, 165, 14, 0.2);
            outline: none;
        }
        .glass-card {
            background: white;
            border-radius: 3rem;
            padding: 3rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.05);
        }
        .label-system {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: #94a3b8;
            margin-bottom: 0.75rem;
            display: block;
            margin-left: 1rem;
        }
    </style>
</head>
<body class="bg-[#fcfdfd] flex">

    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-8 lg:p-20 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            
            <header class="mb-16 flex items-center justify-between">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.4em] text-[10px] mb-2 block">HR Administration</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-5xl font-bold text-nature leading-tight"><?= $id ? 'Edit' : 'Add' ?> <span class="text-gold italic">Member</span></h1>
                </div>
                <a href="index.php" class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-nature/20 hover:text-red-500 hover:rotate-90 transition-all shadow-xl">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </header>

            <form method="POST" enctype="multipart/form-data" class="space-y-12 pb-20">
                
                <!-- STEP 1: MEMBER PORTRAIT -->
                <div class="glass-card text-center" data-aos="fade-up">
                    <div class="relative group w-48 h-48 mx-auto mb-10">
                        <div class="w-full h-full rounded-[3rem] border-4 border-white shadow-2xl overflow-hidden relative bg-slate-50">
                            <img id="img-preview" src="<?= $item ? htmlspecialchars($item['image_path']) : '#' ?>" 
                                 class="w-full h-full object-cover <?= $item && $item['image_path'] ? '' : 'hidden' ?>">
                            <?php if(!$item || !$item['image_path']): ?>
                                <div id="img-placeholder" class="w-full h-full flex items-center justify-center text-slate-200">
                                    <i class="fas fa-user-circle text-6xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Hidden File Input -->
                        <input type="file" name="image_file" id="image_file" class="hidden" accept="image/*" onchange="previewImage(this)">
                        
                        <label for="image_file" class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-nature text-white px-8 py-3 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-2xl hover:bg-gold cursor-pointer transition-all whitespace-nowrap">
                                <i class="fas fa-camera-retro mr-2"></i> Upload Portrait
                        </label>
                        <input type="hidden" name="image_path" id="image_path" value="<?= $item ? htmlspecialchars($item['image_path']) : '' ?>">
                    </div>
                    
                    <div class="max-w-md mx-auto space-y-2">
                        <label class="label-system">Full Name</label>
                        <input type="text" name="name_en" required class="system-input text-center text-2xl" placeholder="e.g. Shri Rajesh Patel" value="<?= $item ? htmlspecialchars($item['name_en']) : '' ?>">
                    </div>
                </div>

                <!-- STEP 2: PROFESSIONAL ROLE -->
                <div class="glass-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-2">
                            <label class="label-system">Official Position</label>
                            <input type="text" name="position_en" required class="system-input" placeholder="e.g. Manager / Coordinator" value="<?= $item ? htmlspecialchars($item['position_en']) : '' ?>">
                        </div>
                        <div class="space-y-2">
                            <label class="label-system">Internal Rank (Sort Order)</label>
                            <input type="number" name="sort_order" required class="system-input" value="<?= $item ? $item['sort_order'] : '0' ?>">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="label-system">Short Biography</label>
                            <textarea name="bio_en" class="system-input h-32" placeholder="Tell the world about their humble service..."><?= $item ? htmlspecialchars($item['bio_en']) : '' ?></textarea>
                        </div>
                        <div class="md:col-span-2 flex items-end">
                            <button type="submit" class="w-full bg-nature text-white py-6 rounded-3xl font-black uppercase tracking-[0.4em] text-sm shadow-2xl hover:bg-gold hover:text-nature transition-all duration-500 transform hover:scale-[1.02]">
                                Save Member Record
                            </button>
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