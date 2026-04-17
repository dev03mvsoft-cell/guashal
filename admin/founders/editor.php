<?php
require_once '../include/auth.php';
require_once '../include/functions.php';
require_once '../../config/db.php';

$id = $_GET['id'] ?? null;
$error = '';

$data = [
    'name_en' => '',
    'designation_en' => '',
    'company_en' => '',
    'message_en' => '',
    'image_path' => '',
    'type' => 'trustee',
    'sort_order' => 0,
    'contact' => ''
];

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM founders WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) $data = array_merge($data, $row);
    } catch (Exception $e) { $error = $e->getMessage(); }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_en = $_POST['name_en'] ?? '';
    $designation_en = $_POST['designation_en'] ?? '';
    $company_en = $_POST['company_en'] ?? '';
    $message_en = $_POST['message_en'] ?? '';
    $type = $_POST['type'] ?? 'trustee';
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $contact = $_POST['contact'] ?? '';
    $image_path = $_POST['image_path'] ?? $data['image_path']; // Fallback to existing

    // Handle File Upload
    if (!empty($_FILES['image_file']['name'])) {
        $uploaded_path = upload_file($_FILES['image_file'], 'uploads/founders');
        if ($uploaded_path) {
            // Delete old file if replacing
            if (!empty($data['image_path'])) {
                cleanup_file($data['image_path']);
            }
            $image_path = $uploaded_path;
        }
    }

    try {
        if ($id) {
            $set_sql = "name_en=?, designation_en=?, company_en=?, message_en=?, image_path=?, type=?, sort_order=?, contact=?";
            $stmt = $pdo->prepare("UPDATE founders SET $set_sql WHERE id=?");
            $stmt->execute([
                $name_en, $designation_en, $company_en, 
                $message_en, $image_path, $type, 
                $sort_order, $contact, $id
            ]);
        } else {
            $cols = "name_en, designation_en, company_en, message_en, image_path, type, sort_order, contact";
            $stmt = $pdo->prepare("INSERT INTO founders ($cols) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $name_en, $designation_en, $company_en, 
                $message_en, $image_path, $type, 
                $sort_order, $contact
            ]);
        }
        header("Location: index.php?msg=" . ($id ? "Legacy Synchronized" : "Visionary Welcomed"));
        exit;
    } catch (Exception $e) { $error = $e->getMessage(); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>High-Fidelity Visionary Portal</title>
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
        <div class="max-w-4xl mx-auto">
            
            <header class="mb-8 flex items-center justify-between">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.3em] text-[12px] mb-1 block">Council Administration</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature leading-tight"><?= $id ? 'Refine' : 'Add' ?> <span class="text-gold italic">Visionary</span></h1>
                </div>
                <a href="index.php" class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-nature/40 hover:text-red-500 hover:rotate-90 transition-all shadow-lg border border-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </header>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-6 rounded-2xl border border-red-100 font-bold mb-8 text-sm">
                    <i class="fas fa-shield-alt mr-3"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6 pb-20">
                <div class="glass-card" data-aos="fade-up">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        <!-- Col 1: Portrait -->
                        <div class="flex flex-col items-center">
                            <label class="label-system text-center w-full">Portrait</label>
                            <div class="relative group w-24 h-24 mb-3">
                                <div class="w-full h-full rounded-2xl bg-slate-50 border-2 border-white shadow-lg overflow-hidden relative">
                                    <img id="pfp_preview" src="<?= $data['image_path'] ?: '#' ?>" class="w-full h-full object-cover <?= $data['image_path'] ? '' : 'hidden' ?>">
                                    <?php if(!$data['image_path']): ?>
                                        <div id="pfp_placeholder" class="w-full h-full flex items-center justify-center text-slate-200">
                                            <i class="fas fa-user-tie text-3xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <label for="image_file" class="absolute bottom-0 right-0 w-7 h-7 bg-nature text-white rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:scale-110 transition-transform border-2 border-white">
                                    <i class="fas fa-camera text-[10px]"></i>
                                </label>
                                <input type="file" name="image_file" id="image_file" class="hidden" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="image_path" id="image_path" value="<?= htmlspecialchars($data['image_path']) ?>">
                            </div>
                        </div>

                        <!-- Col 2 & 3: Details -->
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
                            <div class="md:col-span-2 space-y-1">
                                <label class="label-system">Visionary Full Name</label>
                                <input type="text" name="name_en" required class="system-input" placeholder="e.g. Shri Rajesh Patel" value="<?= htmlspecialchars($data['name_en']) ?>">
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Designation</label>
                                <input type="text" name="designation_en" class="system-input" placeholder="Director / Founder" value="<?= htmlspecialchars($data['designation_en']) ?>">
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Organization</label>
                                <input type="text" name="company_en" class="system-input" placeholder="e.g. Patel Industries" value="<?= htmlspecialchars($data['company_en']) ?>">
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Role in Council</label>
                                <select name="type" class="system-input">
                                    <option value="trustee" <?= (strcasecmp(trim($data['type'] ?? ''), 'trustee') === 0) ? 'selected' : '' ?>>Executive Trustee</option>
                                    <option value="founder" <?= (strcasecmp(trim($data['type'] ?? ''), 'founder') === 0) ? 'selected' : '' ?>>Founding Member</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Contact Info</label>
                                <input type="text" name="contact" class="system-input" placeholder="+91 XXXX XXX XXX" value="<?= htmlspecialchars($data['contact']) ?>">
                            </div>
                            <div class="md:col-span-2 space-y-1">
                                <label class="label-system">Personal Sacred Message</label>
                                <textarea name="message_en" class="system-input h-24 italic leading-relaxed" placeholder="Enter message..."><?= htmlspecialchars($data['message_en']) ?></textarea>
                            </div>
                            <div class="space-y-1">
                                <label class="label-system">Sort Order</label>
                                <input type="number" name="sort_order" class="system-input" value="<?= $data['sort_order'] ?>">
                                <span class="text-[13px] font-bold uppercase tracking-tight text-nature/60">Visible on Portal</span>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-nature text-white py-4 rounded-2xl font-black uppercase tracking-[0.2em] text-[13px] shadow-xl hover:bg-saffron transition-all duration-300 transform hover:scale-[1.02]">
                                    <?= $id ? 'Update Record' : 'Publish Visionary' ?>
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
            const preview = document.getElementById('pfp_preview');
            const placeholder = document.getElementById('pfp_placeholder');
            
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