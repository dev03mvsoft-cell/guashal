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
        .input-premium {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(184, 134, 11, 0.1);
            border-radius: 1.5rem;
            padding: 1.25rem 1.5rem;
            width: 100%;
            transition: all 0.4s;
        }

        .input-premium:focus {
            outline: none;
            border-color: #B8860B;
            background: white;
            box-shadow: 0 15px 30px -10px rgba(184, 134, 11, 0.1);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col md:flex-row bg-[#fdfaf7]">

    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-12 overflow-y-auto">
        <div class="max-w-4xl mx-auto">

            <header class="mb-12">
                <a href="index.php" class="text-nature/30 hover:text-gold transition-colors flex items-center gap-2 mb-6 uppercase text-[12px] font-black tracking-widest">
                    <i class="fas fa-arrow-left"></i> Back to Voices
                </a>
                <h1 style="font-family: 'Playfair Display';" class="text-5xl font-bold text-nature"><?= $edit_id ? 'Refine' : 'Publish' ?> <span class="italic text-gold">Devotion</span></h1>
            </header>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-6 rounded-3xl mb-8 border border-red-100 font-bold text-[12px] uppercase tracking-widest leading-relaxed">
                    <i class="fas fa-exclamation-triangle mr-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-10 bg-white/40 backdrop-blur-xl p-10 md:p-16 rounded-[4rem] border border-gold/10 shadow-2xl relative overflow-hidden">
                <!-- Decorative Corner -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-gold/5 rounded-full blur-3xl"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="block text-[12px] font-black uppercase tracking-widest text-nature/40 ml-4">Devotee Name</label>
                        <input type="text" name="name" class="input-premium font-bold" placeholder="e.g. Rajesh Sharma" value="<?= htmlspecialchars($data['name']) ?>" required>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[12px] font-black uppercase tracking-widest text-nature/40 ml-4">Sacred Role / Tag</label>
                        <input type="text" name="role" class="input-premium font-bold" placeholder="e.g. Monthly Donor" value="<?= htmlspecialchars($data['role']) ?>" required>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-[12px] font-black uppercase tracking-widest text-nature/40 ml-4">Spiritual Experience (Testimonial)</label>
                    <textarea name="testimonial" class="input-premium h-48 font-display italic text-lg leading-relaxed" placeholder="Share the heartfelt experience..." required><?= htmlspecialchars($data['testimonial']) ?></textarea>
                </div>

                <div class="space-y-4">
                    <label class="block text-[12px] font-black uppercase tracking-widest text-nature/40 ml-4">Divine Rating</label>
                    <div class="flex gap-4">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="rating" value="<?= $i ?>" class="hidden peer" <?= $data['rating'] == $i ? 'checked' : '' ?>>
                                <div class="p-4 rounded-2xl border border-gold/5 bg-white text-center transition-all peer-checked:bg-gold peer-checked:text-nature peer-checked:shadow-lg hover:border-gold">
                                    <div class="text-sm font-black mb-1"><?= $i ?></div>
                                    <div class="text-[12px] uppercase font-black opacity-40">Stars</div>
                                </div>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="pt-8">
                    <button type="submit" class="w-full bg-nature text-white py-6 rounded-3xl font-black uppercase tracking-[0.3em] text-[15px] shadow-2xl hover:bg-gold hover:text-nature transition-all duration-700 transform hover:-translate-y-1">
                        <?= $edit_id ? 'Confirm Refinement' : 'Publish to Sanctuary' ?>
                    </button>
                    <p class="text-center text-[12px] text-nature/30 uppercase tracking-widest mt-8 font-bold">This voice will be mirrored on the public landing page instantly</p>
                </div>
            </form>
        </div>
    </main>

</body>

</html>