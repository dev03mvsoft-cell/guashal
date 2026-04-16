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
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            border-radius: 1.25rem;
            padding: 1.25rem 1.5rem;
            width: 100%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            color: #1e293b;
        }
        .system-input:focus {
            background: #fff;
            border-color: #c0a50e;
            box-shadow: 0 15px 30px -10px rgba(192, 165, 14, 0.15);
            transform: translateY(-2px);
            outline: none;
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
        .section-glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 3rem;
            padding: 3rem;
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.03);
        }
    </style>
</head>
<body class="bg-[#fcfdfd] min-h-screen flex flex-col md:flex-row">
    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-20 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            
            <header class="mb-16 flex items-center justify-between">
                <div data-aos="fade-right">
                    <span class="text-saffron font-black uppercase tracking-[0.4em] text-[10px] mb-2 block">Council Administration</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-5xl font-bold text-nature leading-tight"><?= $id ? 'Refine' : 'Add' ?> <span class="text-gold italic">Visionary</span></h1>
                </div>
                <a href="index.php" class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-nature/20 hover:text-red-500 hover:rotate-90 transition-all shadow-xl">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </header>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-8 rounded-[2rem] border border-red-100 font-bold mb-12 animate-shake">
                    <i class="fas fa-shield-alt mr-3"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="systematic-flow space-y-12 pb-20">
                
                <!-- STEP 1: VISUAL IDENTITY -->
                <div class="section-glass" data-aos="fade-up">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative group mb-10">
                            <div class="w-56 h-56 rounded-[3.5rem] bg-slate-50 border-4 border-white shadow-2xl overflow-hidden relative group-hover:scale-[1.02] transition-transform duration-500">
                                <img id="pfp_preview" src="<?= $data['image_path'] ?: '#' ?>" class="w-full h-full object-cover <?= $data['image_path'] ? '' : 'hidden' ?>">
                                <?php if(!$data['image_path']): ?>
                                    <div id="pfp_placeholder" class="w-full h-full flex items-center justify-center text-slate-200">
                                        <i class="fas fa-user-tie text-6xl"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Hidden File Input -->
                            <input type="file" name="image_file" id="image_file" class="hidden" accept="image/*" onchange="previewImage(this)">
                            
                            <label for="image_file" class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-nature text-white px-8 py-3 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-2xl hover:bg-gold cursor-pointer transition-all whitespace-nowrap">
                                <i class="fas fa-camera-retro mr-2"></i> Upload Portrait
                            </label>
                            
                            <input type="hidden" name="image_path" id="image_path" value="<?= htmlspecialchars($data['image_path']) ?>">
                        </div>
                        
                        <div class="w-full max-w-sm space-y-2">
                             <label class="label-system">Visionary Full Name</label>
                             <input type="text" name="name_en" required class="system-input text-center text-2xl" placeholder="e.g. Shri Rajesh Patel" value="<?= htmlspecialchars($data['name_en']) ?>">
                        </div>
                    </div>
                </div>

                <!-- STEP 2: PROFESSIONAL STANDING -->
                <div class="section-glass" data-aos="fade-up" data-aos-delay="100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-2">
                            <label class="label-system">Designation</label>
                            <input type="text" name="designation_en" class="system-input" placeholder="e.g. Director / Founder" value="<?= htmlspecialchars($data['designation_en']) ?>">
                        </div>
                        <div class="space-y-2">
                            <label class="label-system">Organization / Company</label>
                            <input type="text" name="company_en" class="system-input" placeholder="e.g. Patel Industries" value="<?= htmlspecialchars($data['company_en']) ?>">
                        </div>
                        <div class="space-y-2">
                            <label class="label-system">Role in Council</label>
                            <div class="relative group/select">
                                <select name="type" class="system-input appearance-none pr-12">
                                    <option value="trustee" <?= (strcasecmp(trim($data['type'] ?? ''), 'trustee') === 0) ? 'selected' : '' ?>>Executive Trustee</option>
                                    <option value="founder" <?= (strcasecmp(trim($data['type'] ?? ''), 'founder') === 0) ? 'selected' : '' ?>>Founding Member</option>
                                </select>
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-nature/20 group-hover/select:text-gold transition-colors">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="label-system">Contact Info</label>
                            <input type="text" name="contact" class="system-input" placeholder="+91 XXXX XXX XXX" value="<?= htmlspecialchars($data['contact']) ?>">
                        </div>
                    </div>
                </div>

                <!-- STEP 3: LEGACY & ORDER -->
                <div class="section-glass" data-aos="fade-up" data-aos-delay="200">
                    <div class="space-y-8">
                        <div class="space-y-2">
                            <label class="label-system">Personal Sacred Message</label>
                            <textarea name="message_en" class="system-input h-48 italic leading-relaxed" placeholder="Enter the visionary's quote or message to the world..."><?= htmlspecialchars($data['message_en']) ?></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-2">
                                <label class="label-system">Sequence (Sort Order)</label>
                                <input type="number" name="sort_order" class="system-input" value="<?= $data['sort_order'] ?>">
                                <p class="text-[10px] text-nature/30 ml-4 italic mt-2">Lower numbers appear first in the council.</p>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-nature text-white py-6 rounded-3xl font-black uppercase tracking-[0.4em] text-sm shadow-2xl hover:bg-gold hover:text-nature transition-all duration-700 hover:scale-[1.02]">
                                    <?= $id ? 'Update' : 'Publish' ?> Visionary
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