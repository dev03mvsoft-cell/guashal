<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

$message = '';
$error = '';
$id = $_GET['id'] ?? null;
$data = [
    'name_en' => '',
    'designation_en' => '',
    'image_path' => '',
    'sort_order' => 0
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM team WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch() ?: $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n_en = $_POST['name_en'];
    $d_en = $_POST['designation_en'];
    $img = $_POST['image_path'];
    $sort = $_POST['sort_order'];

    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE team SET name_en=?, designation_en=?, image_path=?, sort_order=? WHERE id=?");
            $stmt->execute([$n_en, $d_en, $img, $sort, $id]);
            header("Location: index.php?msg=Guardian Profile Refined");
            exit;
        } else {
            $stmt = $pdo->prepare("INSERT INTO team (name_en, designation_en, image_path, sort_order) VALUES (?,?,?,?)");
            $stmt->execute([$n_en, $d_en, $img, $sort]);
            header("Location: index.php?msg=New Guardian Onboarded");
            exit;
        }
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardian Editor - Gaushala Admin</title>
    <?php include '../include/head.php'; ?>
    <style>
        .input-premium {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(44, 76, 59, 0.05);
            border-radius: 2rem;
            padding: 1.5rem;
            width: 100%;
            transition: all 0.4s;
            font-weight: 600;
        }

        .input-premium:focus {
            outline: none;
            border-color: #FF6A00;
            background: white;
            box-shadow: 0 20px 40px -15px rgba(255, 106, 0, 0.1);
        }

        .section-label {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            color: rgba(44, 76, 59, 0.3);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .section-label::after {
            content: '';
            height: 1px;
            flex: 1;
            background: rgba(44, 76, 59, 0.05);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col md:flex-row bg-[#faf8f6]">

    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-12 overflow-y-auto">
        <div class="max-w-5xl mx-auto">

            <header class="mb-16">
                <a href="index.php" class="text-nature/30 hover:text-[#FF6A00] transition-colors flex items-center gap-2 mb-8 uppercase text-[12px] font-black tracking-widest">
                    <i class="fas fa-arrow-left"></i> Return to Council
                </a>
                <h1 style="font-family: 'Playfair Display';" class="text-5xl font-bold text-nature"><?= $id ? 'Refine' : 'Onboard' ?> <span class="italic text-[#FF6A00]">Guardian</span></h1>
            </header>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-500 p-8 rounded-[2.5rem] mb-12 border border-red-100 font-bold text-[12px] uppercase tracking-widest leading-loose">
                    <i class="fas fa-shield-alt mr-2"></i> Error: <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="bg-white/60 p-10 md:p-14 rounded-[3.5rem] border border-nature/5 shadow-2xl relative overflow-hidden backdrop-blur-3xl mb-20">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                    <!-- Identity & Role -->
                    <div class="md:col-span-8 space-y-8">
                        <section>
                            <div class="section-label mb-6">Identity Details</div>
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <label class="block text-[12px] font-bold text-nature/40 uppercase tracking-widest ml-4">Full Name</label>
                                    <input type="text" name="name_en" class="input-premium py-4" placeholder="e.g. Shri Rajesh Patel" value="<?= htmlspecialchars($data['name_en']) ?>" required>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[12px] font-bold text-nature/40 uppercase tracking-widest ml-4">Role / Designation</label>
                                    <input type="text" name="designation_en" class="input-premium py-4" placeholder="e.g. President" value="<?= htmlspecialchars($data['designation_en']) ?>" required>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Visuals -->
                    <div class="md:col-span-4 space-y-8">
                        <section>
                            <div class="section-label mb-6">Visuals</div>
                            <div class="space-y-4">
                                <div id="preview_box" class="w-full aspect-square rounded-[2.5rem] bg-nature/5 border border-nature/5 overflow-hidden shadow-inner flex items-center justify-center relative group">
                                    <img id="current_preview" src="<?= $data['image_path'] ?: '#' ?>" class="w-full h-full object-cover <?= $data['image_path'] ? '' : 'hidden' ?>">
                                    <?php if (!$data['image_path']): ?>
                                        <div id="preview_placeholder" class="text-nature/10 text-4xl"><i class="fas fa-user-shield"></i></div>
                                    <?php endif; ?>
                                    <div class="absolute inset-0 bg-nature/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-4">
                                        <button type="button" onclick="openVault()" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-nature shadow-xl hover:bg-gold hover:text-white transition-all"><i class="fas fa-images"></i></button>
                                        <button type="button" onclick="document.getElementById('sacred_upload').click()" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-nature shadow-xl hover:bg-saffron hover:text-white transition-all"><i class="fas fa-cloud-upload-alt"></i></button>
                                    </div>
                                </div>
                                <input type="hidden" name="image_path" id="image_path" value="<?= htmlspecialchars($data['image_path']) ?>">
                                <input type="file" id="sacred_upload" class="hidden" accept="image/*" onchange="handleSacredUpload(this)">

                                <div class="space-y-2">
                                    <label class="block text-[12px] font-bold text-nature/40 uppercase tracking-widest ml-4">Council Order</label>
                                    <input type="number" name="sort_order" class="input-premium py-4" value="<?= htmlspecialchars($data['sort_order']) ?>">
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                <div class="mt-12 pt-10 border-t border-nature/5">
                    <button type="submit" class="w-full bg-nature text-white py-6 rounded-[2rem] font-black uppercase tracking-[0.4em] text-[15px] shadow-2xl hover:bg-gold hover:text-nature hover:scale-[1.01] transition-all duration-700">
                        <?= $id ? 'Refine Guardian Profile' : 'Onboard Visionary Leader' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function openVault() {
            const vaultUrl = '../gallery.php?select=image_path';
            const vaultWindow = window.open(vaultUrl, 'Gallery', 'width=1100,height=800,scrollbars=yes');
            if (!vaultWindow || vaultWindow.closed || typeof vaultWindow.closed == 'undefined') {
                alert('Divine insight: Your browser blocked the Visual Vault. Please allow popups for the sanctuary dashboard.');
            }
        }

        function handleSacredUpload(input) {
            if (!input.files || !input.files[0]) return;

            const formData = new FormData();
            formData.append('image_file', input.files[0]);
            formData.append('action', 'quick_upload');

            // Feedback
            const btn = input.previousElementSibling;
            const originalIcon = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-sync fa-spin text-saffron"></i>';

            fetch('../gallery.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('image_path').value = data.path;
                        updatePreview(data.path);
                    } else {
                        alert('Sacred error: ' + (data.error || 'The upload was interrupted.'));
                    }
                })
                .catch(err => alert('Sacred error: Connection to the vault failed.'))
                .finally(() => {
                    btn.innerHTML = originalIcon;
                });
        }

        function updatePreview(path) {
            const img = document.getElementById('current_preview');
            const placeholder = document.getElementById('preview_placeholder');
            img.src = path;
            img.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        }

        // Live path preview
        document.getElementById('image_path').addEventListener('input', function() {
            updatePreview(this.value);
        });
    </script>
</body>

</html>