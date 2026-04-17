<?php
require_once '../include/auth.php';
require_once '../include/functions.php';
require_once '../../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$donor = null;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM donors WHERE id = ?");
        $stmt->execute([$id]);
        $donor = $stmt->fetch();
    } catch (Exception $e) {}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $amount = (float)$_POST['amount'];
    $purpose = $_POST['purpose'];
    $donation_date = $_POST['donation_date'];
    $special_date = !empty($_POST['special_date']) ? $_POST['special_date'] : null;
    $contact = $_POST['contact'];
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;
    
    // Image Handling Using Universal Function
    $profile_pic = $donor ? $donor['profile_pic'] : 'default_donor.png';
    
    if (!empty($_FILES['profile_pic']['name'])) {
        $uploaded_path = upload_file($_FILES['profile_pic'], 'asset/img/donors');
        if ($uploaded_path) {
            // Cleanup old file (basename because current DB storage seems to store only filename)
            if ($donor && $donor['profile_pic'] !== 'default_donor.png') {
                cleanup_file('asset/img/donors/' . $donor['profile_pic']);
            }
            $profile_pic = basename($uploaded_path);
        }
    }

    try {
        if ($id > 0) {
            $sql = "UPDATE donors SET name=?, profile_pic=?, donation_date=?, special_date=?, purpose=?, amount=?, is_visible=?, contact=? WHERE id=?";
            $pdo->prepare($sql)->execute([$name, $profile_pic, $donation_date, $special_date, $purpose, $amount, $is_visible, $contact, $id]);
        } else {
            $sql = "INSERT INTO donors (name, profile_pic, donation_date, special_date, purpose, amount, is_visible, contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$name, $profile_pic, $donation_date, $special_date, $purpose, $amount, $is_visible, $contact]);
        }
        header("Location: index.php?msg=Honor Circle Updated");
        exit();
    } catch (Exception $e) { $error = $e->getMessage(); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Offering Portal | High-Fidelity</title>
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
                    <span class="text-saffron font-black uppercase tracking-[0.3em] text-[12px] mb-1 block">Gratitude Registry</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature leading-tight"><?= $id ? 'Edit' : 'New' ?> <span class="text-saffron italic">Offering</span></h1>
                </div>
                <a href="index.php" class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-nature/40 hover:text-red-500 hover:rotate-90 transition-all shadow-lg border border-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </header>

            <form method="POST" enctype="multipart/form-data" class="space-y-6 pb-20">
                
                <div class="glass-card" data-aos="fade-up">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        <!-- Col 1: Portrait & Name -->
                        <div class="flex flex-col items-center">
                            <label class="label-system text-center w-full">Portrait</label>
                            <div class="relative group w-28 h-28 mb-4">
                                <div class="w-full h-full rounded-full border-2 border-slate-100 shadow-md overflow-hidden relative">
                                    <img id="preview-img" src="../../asset/img/donors/<?= $donor ? $donor['profile_pic'] : 'default_donor.png' ?>" 
                                         onerror="this.src='../../asset/img/donors/default_donor.png';"
                                         class="w-full h-full object-cover">
                                </div>
                                <label for="profile_pic" class="absolute bottom-0 right-0 w-8 h-8 bg-saffron text-white rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:scale-110 transition-transform border-2 border-white">
                                    <i class="fas fa-camera text-xs"></i>
                                </label>
                                <input type="file" id="profile_pic" name="profile_pic" class="hidden" accept="image/*" onchange="preview(this)">
                            </div>
                        </div>

                        <!-- Col 2 & 3: Main Fields -->
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            <div class="space-y-2">
                                <label class="label-system">Full Name</label>
                                <input type="text" name="name" required class="system-input" placeholder="Donor Name" value="<?= $donor ? htmlspecialchars($donor['name']) : '' ?>">
                            </div>
                            <div class="space-y-2">
                                 <label class="label-system">WhatsApp Contact</label>
                                 <input type="text" name="contact" class="system-input" placeholder="+91 XXXX XXX XXX" value="<?= $donor ? htmlspecialchars($donor['contact']) : '' ?>">
                            </div>
                            <div class="space-y-2">
                                <label class="label-system">Offering Amount (₹)</label>
                                <input type="number" step="0.01" name="amount" required class="system-input" value="<?= $donor ? $donor['amount'] : '' ?>">
                            </div>
                            <div class="space-y-2">
                                <label class="label-system">Donation Date</label>
                                <input type="date" name="donation_date" required class="system-input" value="<?= $donor ? $donor['donation_date'] : date('Y-m-d') ?>">
                            </div>
                            <div class="space-y-2">
                                <label class="label-system">Special Date (Anniv./B-day)</label>
                                <input type="date" name="special_date" class="system-input" value="<?= $donor ? $donor['special_date'] : '' ?>">
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="label-system">Purpose / Sacred Message</label>
                                <input type="text" name="purpose" required class="system-input" placeholder="e.g. For Green Fodder / In Memory of..." value="<?= $donor ? htmlspecialchars($donor['purpose']) : '' ?>">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="label-system">Public Visibility</label>
                                <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_visible" class="sr-only peer" <?= (!$donor || $donor['is_visible']) ? 'checked' : '' ?>>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-saffron"></div>
                                    </label>
                                    <span class="text-[13px] font-bold uppercase tracking-tight text-nature/60">Visible on Wall</span>
                                </div>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-nature text-white py-4 rounded-2xl font-black uppercase tracking-[0.2em] text-[13px] shadow-xl hover:bg-saffron transition-all duration-300 transform hover:scale-[1.02]">
                                    <?= $id ? 'Update Record' : 'Publish Offering' ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
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