<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

$message = '';
$error = '';
$edit_id = $_GET['id'] ?? null;
$data = ['name' => '', 'role' => '', 'testimonial' => '', 'rating' => 5];

// Fetch if editing
if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$edit_id]);
    $data = $stmt->fetch() ?: $data;
}

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $testimonial = $_POST['testimonial'];
    $rating = $_POST['rating'];

    try {
        if ($edit_id) {
            $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, role = ?, testimonial = ?, rating = ? WHERE id = ?");
            $stmt->execute([$name, $role, $testimonial, $rating, $edit_id]);
            header("Location: index.php?msg=Voice Refined Successfully");
            exit;
        } else {
            $stmt = $pdo->prepare("INSERT INTO testimonials (name, role, testimonial, rating) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $role, $testimonial, $rating]);
            header("Location: index.php?msg=Sacred Voice Published");
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
    <title>Voice Editor - Gaushala Admin</title>
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

<body class="bg-[#f8fafc] flex flex-col md:flex-row md:h-screen md:overflow-hidden">
    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-4 md:p-12 md:overflow-y-auto h-full">
        <div class="max-w-4xl mx-auto">

            <header class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 md:gap-0">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.3em] text-[12px] mb-1 block">Devotee Voice</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature leading-tight"><?= $edit_id ? 'Refine' : 'Add' ?> <span class="italic text-gold">Devotion</span></h1>
                </div>
                <a href="index.php" class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-nature/40 hover:text-red-500 hover:rotate-90 transition-all shadow-lg border border-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </header>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-6 rounded-2xl border border-red-100 font-bold mb-8 text-xs">
                    <i class="fas fa-shield-alt mr-3"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6 pb-20">
                <div class="glass-card" data-aos="fade-up">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-1">
                            <label class="label-system">Devotee Name</label>
                            <input type="text" name="name" class="system-input" placeholder="e.g. Rajesh Sharma" value="<?= htmlspecialchars($data['name']) ?>" required>
                        </div>
                        <div class="space-y-1">
                            <label class="label-system">Sacred Role / Tag</label>
                            <input type="text" name="role" class="system-input" placeholder="e.g. Monthly Donor" value="<?= htmlspecialchars($data['role']) ?>" required>
                        </div>
                        <div class="md:col-span-2 space-y-1">
                            <label class="label-system">Spiritual Experience</label>
                            <textarea name="testimonial" class="system-input h-32 italic leading-relaxed" placeholder="Share experience..." required><?= htmlspecialchars($data['testimonial']) ?></textarea>
                        </div>

                        <div class="md:col-span-2 space-y-1">
                            <label class="label-system">Divine Rating</label>
                            <div class="flex gap-2">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="rating" value="<?= $i ?>" class="hidden peer" <?= $data['rating'] == $i ? 'checked' : '' ?>>
                                        <div class="p-3 rounded-2xl border border-slate-100 bg-slate-50 text-center transition-all peer-checked:bg-gold peer-checked:text-nature peer-checked:border-gold hover:border-gold shadow-sm">
                                            <div class="text-[13px] font-black"><?= $i ?> <i class="fas fa-star text-[12px]"></i></div>
                                        </div>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="md:col-span-2 pt-6">
                            <button type="submit" class="w-full bg-nature text-white py-4 rounded-2xl font-black uppercase tracking-[0.2em] text-[13px] shadow-xl hover:bg-gold hover:text-nature transition-all duration-300 transform hover:scale-[1.02]">
                                <?= $edit_id ? 'Update Voice' : 'Publish Devotion' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

</body>

</html>