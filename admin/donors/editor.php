<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$donor = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM donors WHERE id = ?");
    $stmt->execute([$id]);
    $donor = $stmt->fetch();
}

// Handle Save Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $amount = (float)$_POST['amount'];
    $purpose = $_POST['purpose'];
    $donation_date = $_POST['donation_date'];
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;
    
    // Image Handling
    $profile_pic = $donor ? $donor['profile_pic'] : 'default_donor.png';
    if (!empty($_FILES['profile_pic']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['profile_pic']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_name = 'donor_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $target_dir = "../../asset/img/donors/";
            
            // Create dir if missing
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_dir . $new_name)) {
                // Delete old pic
                if ($donor && $donor['profile_pic'] !== 'default_donor.png') {
                    if (file_exists($target_dir . $donor['profile_pic'])) unlink($target_dir . $donor['profile_pic']);
                }
                $profile_pic = $new_name;
            }
        }
    }

    if ($id > 0) {
        $sql = "UPDATE donors SET name=?, profile_pic=?, donation_date=?, purpose=?, amount=?, is_visible=? WHERE id=?";
        $pdo->prepare($sql)->execute([$name, $profile_pic, $donation_date, $purpose, $amount, $is_visible, $id]);
        $msg = "Donor information updated successfully";
    } else {
        $sql = "INSERT INTO donors (name, profile_pic, donation_date, purpose, amount, is_visible) VALUES (?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$name, $profile_pic, $donation_date, $purpose, $amount, $is_visible]);
        $msg = "New donor added to Hall of Fame";
    }
    
    header("Location: index.php?msg=" . urlencode($msg));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id > 0 ? 'Edit' : 'Add' ?> Donor | Hall of Fame</title>
    <?php include '../include/head.php'; ?>
</head>
<body class="flex">

    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-12">
        <div class="max-w-4xl mx-auto">
            
            <!-- Breadcrumb / Back -->
            <a href="index.php" class="inline-flex items-center gap-2 text-nature/60 hover:text-saffron font-bold text-sm mb-8 transition-colors">
                <i class="fas fa-arrow-left"></i> Back to Hall of Fame
            </a>

            <!-- Card -->
            <div class="bg-white rounded-[3rem] shadow-2xl border border-nature/5 overflow-hidden">
                <div class="bg-nature p-10 text-white relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 mandala-bg scale-150 transform rotate-12"></div>
                    <div class="relative z-10">
                        <h2 style="font-family: 'Playfair Display';" class="text-3xl font-bold mb-2"><?= $id > 0 ? 'Modify Offering' : 'New Offering' ?></h2>
                        <p class="text-white/60">Enter the details of the noble donor to be showcased.</p>
                    </div>
                </div>

                <form method="POST" enctype="multipart/form-data" class="p-10 space-y-10">
                    
                    <!-- Avatar Upload -->
                    <div class="flex flex-col md:flex-row items-center gap-10 pb-10 border-b border-nature/5">
                        <div class="relative group">
                            <div class="w-40 h-40 rounded-full border-4 border-nature/5 overflow-hidden shadow-xl bg-gray-50 flex items-center justify-center">
                                <img id="preview-img" src="../../asset/img/donors/<?= $donor ? $donor['profile_pic'] : 'default_donor.png' ?>" 
                                     onerror="this.src='/asset/img/donors/default_donor.png'"
                                     class="w-full h-full object-cover">
                            </div>
                            <label for="profile_pic" class="absolute bottom-1 right-1 w-12 h-12 bg-saffron text-white rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:scale-110 transition-transform border-4 border-white">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="profile_pic" name="profile_pic" class="hidden" accept="image/*" onchange="preview(this)">
                        </div>
                        <div class="text-center md:text-left">
                            <h4 class="text-xl font-bold text-nature mb-2">Donor Profile Picture</h4>
                            <p class="text-nature/40 text-sm max-w-xs uppercase tracking-widest font-black leading-relaxed">
                                Upload a clear portrait of the donor. Square aspect ratio (1:1) is optimal.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Name -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black uppercase tracking-[0.2em] text-nature/40">Full Name</label>
                            <input type="text" name="name" required value="<?= $donor ? htmlspecialchars($donor['name']) : '' ?>" 
                                   class="w-full bg-nature/[0.02] border border-nature/10 rounded-2xl px-6 py-4 focus:border-saffron focus:ring-4 focus:ring-saffron/5 outline-none transition-all font-bold text-nature">
                        </div>

                        <!-- Amount -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black uppercase tracking-[0.2em] text-nature/40">Donation Amount (₹)</label>
                            <input type="number" step="0.01" name="amount" required value="<?= $donor ? $donor['amount'] : '' ?>" 
                                   class="w-full bg-nature/[0.02] border border-nature/10 rounded-2xl px-6 py-4 focus:border-saffron focus:ring-4 focus:ring-saffron/5 outline-none transition-all font-bold text-nature">
                        </div>

                        <!-- Date -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black uppercase tracking-[0.2em] text-nature/40">Donation Date</label>
                            <input type="date" name="donation_date" required value="<?= $donor ? $donor['donation_date'] : date('Y-m-d') ?>" 
                                   class="w-full bg-nature/[0.02] border border-nature/10 rounded-2xl px-6 py-4 focus:border-saffron focus:ring-4 focus:ring-saffron/5 outline-none transition-all font-bold text-nature">
                        </div>

                        <!-- Visibility -->
                        <div class="space-y-3">
                            <label class="block text-xs font-black uppercase tracking-[0.2em] text-nature/40">Hall of Fame Visibility</label>
                            <div class="flex items-center gap-4 bg-nature/[0.02] border border-nature/10 rounded-2xl px-6 py-4">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_visible" class="sr-only peer" <?= (!$donor || $donor['is_visible']) ? 'checked' : '' ?>>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-saffron"></div>
                                </label>
                                <span class="text-sm font-bold text-nature/60">Visible on Public Hall of Fame</span>
                            </div>
                        </div>

                        <!-- Purpose / Occasion -->
                        <div class="md:col-span-2 space-y-3">
                            <label class="block text-xs font-black uppercase tracking-[0.2em] text-nature/40">Occasion / Message (Title)</label>
                            <input type="text" name="purpose" required value="<?= $donor ? htmlspecialchars($donor['purpose']) : '' ?>" 
                                   placeholder="e.g. In Memory of Late Rama Devi / For Birthday Celebration"
                                   class="w-full bg-nature/[0.02] border border-nature/10 rounded-2xl px-6 py-4 focus:border-saffron focus:ring-4 focus:ring-saffron/5 outline-none transition-all font-bold text-nature">
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-10 flex flex-col md:flex-row gap-6">
                        <button type="submit" class="flex-1 bg-saffron text-white py-5 rounded-2xl font-black uppercase tracking-widest text-sm shadow-xl shadow-saffron/30 hover:shadow-saffron/40 transform hover:-translate-y-1 transition-all">
                            <?= $id > 0 ? 'Save Updates' : 'Publish to Hall of Fame' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function preview(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

</body>
</html>