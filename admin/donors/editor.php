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
            border: 2px solid #f1f5f9;
            border-radius: 1.25rem;
            padding: 1.25rem 1.5rem;
            width: 100%;
            transition: all 0.4s;
            font-weight: 600;
        }
        .system-input:focus {
            border-color: #FF6A00;
            box-shadow: 0 10px 30px -10px rgba(255, 106, 0, 0.2);
            outline: none;
        }
        .glass-card {
            background: white;
            border-radius: 3rem;
            padding: 3rem;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 30px 60px -15px rgba(0,0,0,0.05);
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
<body class="bg-[#f8fafc] flex">

    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-8 lg:p-20 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            
            <header class="mb-16 flex items-center justify-between">
                <div>
                    <span class="text-saffron font-black uppercase tracking-[0.4em] text-[10px] mb-2 block">Gratitude Registry</span>
                    <h1 style="font-family: 'Playfair Display';" class="text-5xl font-bold text-nature leading-tight"><?= $id ? 'Edit' : 'New' ?> <span class="text-saffron italic">Offering</span></h1>
                </div>
                <a href="index.php" class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-nature/20 hover:text-red-500 hover:rotate-90 transition-all shadow-xl">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </header>

            <form method="POST" enctype="multipart/form-data" class="space-y-12 pb-20">
                
                <!-- STEP 1: DONOR PORTRAIT -->
                <div class="glass-card text-center" data-aos="fade-up">
                    <div class="relative group w-48 h-48 mx-auto mb-10">
                        <div class="w-full h-full rounded-full border-4 border-white shadow-2xl overflow-hidden relative">
                            <img id="preview-img" src="../../asset/img/donors/<?= $donor ? $donor['profile_pic'] : 'default_donor.png' ?>" 
                                 onerror="this.src='../../asset/img/donors/default_donor.png';"
                                 class="w-full h-full object-cover">
                        </div>
                        <label for="profile_pic" class="absolute bottom-1 right-1 w-12 h-12 bg-saffron text-white rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:scale-110 transition-transform border-4 border-white">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="profile_pic" name="profile_pic" class="hidden" accept="image/*" onchange="preview(this)">
                    </div>
                    
                    <div class="max-w-md mx-auto space-y-2">
                        <label class="label-system">FullName</label>
                        <input type="text" name="name" required class="system-input text-center text-2xl" placeholder="Noble Donor Name" value="<?= $donor ? htmlspecialchars($donor['name']) : '' ?>">
                    </div>
                </div>

                <!-- STEP 2: OFFERING DETAILS -->
                <div class="glass-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-2">
                             <label class="label-system">Contact Number (WhatsApp)</label>
                             <input type="text" name="contact" class="system-input" placeholder="+91 XXXX XXX XXX" value="<?= $donor ? htmlspecialchars($donor['contact']) : '' ?>">
                        </div>
                        <div class="space-y-2">
                            <label class="label-system">Special Date (Birthday/Anniv.)</label>
                            <input type="date" name="special_date" class="system-input" value="<?= $donor ? $donor['special_date'] : '' ?>">
                            <p class="text-[10px] text-nature/30 mt-2 ml-4 italic">Notifications show 7 & 2 days before this date.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="label-system">Amount (₹)</label>
                            <input type="number" step="0.01" name="amount" required class="system-input" value="<?= $donor ? $donor['amount'] : '' ?>">
                        </div>
                        <div class="space-y-2">
                            <label class="label-system">Donation Date</label>
                            <input type="date" name="donation_date" required class="system-input" value="<?= $donor ? $donor['donation_date'] : date('Y-m-d') ?>">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="label-system">Occasion / Message / Purpose</label>
                            <input type="text" name="purpose" required class="system-input" placeholder="e.g. Gau Datt (Cow Foster Care) or In Memory of..." value="<?= $donor ? htmlspecialchars($donor['purpose']) : '' ?>">
                        </div>
                        <div class="space-y-2">
                            <label class="label-system">Public Visibility</label>
                            <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border-2 border-slate-100">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_visible" class="sr-only peer" <?= (!$donor || $donor['is_visible']) ? 'checked' : '' ?>>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-saffron"></div>
                                </label>
                                <span class="text-xs font-black uppercase text-nature/40">Visible on Website</span>
                            </div>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-nature text-white py-6 rounded-3xl font-black uppercase tracking-[0.4em] text-sm shadow-2xl hover:bg-saffron transition-all duration-500 transform hover:scale-[1.02]">
                                Publish Offering
                            </button>
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