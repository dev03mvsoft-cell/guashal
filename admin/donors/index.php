<?php
require_once '../include/auth.php';
require_once '../../config/db.php';

// Handle Delete Action
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Fetch image to delete from server
    $stmt = $pdo->prepare("SELECT profile_pic FROM donors WHERE id = ?");
    $stmt->execute([$id]);
    $donor = $stmt->fetch();
    
    if ($donor && $donor['profile_pic'] !== 'default_donor.png') {
        $file_path = "../../asset/img/donors/" . $donor['profile_pic'];
        if (file_exists($file_path)) unlink($file_path);
    }
    
    $pdo->prepare("DELETE FROM donors WHERE id = ?")->execute([$id]);
    header("Location: index.php?msg=Member removed from Hall of Fame");
    exit();
}

$donors = $pdo->query("SELECT * FROM donors ORDER BY donation_date DESC")->fetchAll();
$message = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donors Hall of Fame Management | Admin</title>
    <?php include '../include/head.php'; ?>
</head>
<body class="flex">

    <?php include '../include/sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-12 overflow-x-hidden">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
            <div>
                <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature mb-2">Donors Hall of Fame</h1>
                <p class="text-nature/60 font-medium">Manage and celebrate the compassionate souls who support our cows.</p>
            </div>
            <a href="editor.php" class="bg-saffron text-white px-8 py-4 rounded-xl font-bold flex items-center gap-3 shadow-lg shadow-saffron/20 hover:scale-105 transition-all">
                <i class="fas fa-plus"></i> Add New Donor
            </a>
        </div>

        <!-- Donors List -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            <?php foreach ($donors as $donor): ?>
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-nature/5 hover:border-saffron/30 transition-all duration-500 group relative overflow-hidden">
                    
                    <!-- Status Badge -->
                    <div class="absolute top-6 right-6">
                        <?php if ($donor['is_visible']): ?>
                            <span class="bg-green-100 text-green-700 text-[10px] uppercase font-black px-3 py-1 rounded-full border border-green-200">Public</span>
                        <?php else: ?>
                            <span class="bg-gray-100 text-gray-500 text-[10px] uppercase font-black px-3 py-1 rounded-full border border-gray-200">Private</span>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-6 mb-6">
                        <div class="w-20 h-20 rounded-full border-4 border-nature/5 overflow-hidden shadow-lg flex-shrink-0 group-hover:scale-110 transition-transform">
                            <img src="../../asset/img/donors/<?= $donor['profile_pic'] ?>" 
                                 onerror="this.src='/asset/img/donors/default_donor.png'"
                                 class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-nature line-clamp-1"><?= htmlspecialchars($donor['name']) ?></h3>
                            <p class="text-saffron font-bold text-sm">₹<?= number_format($donor['amount']) ?></p>
                        </div>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase font-black tracking-widest text-nature/30 mb-1">Occasion / Purpose</span>
                            <span class="text-sm text-nature/70 italic line-clamp-2">"<?= htmlspecialchars($donor['purpose']) ?>"</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase font-black tracking-widest text-nature/30 mb-1">Donation Date</span>
                            <span class="text-sm font-bold text-nature/60"><?= date('M d, Y', strtotime($donor['donation_date'])) ?></span>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-6 border-t border-nature/5">
                        <a href="editor.php?id=<?= $donor['id'] ?>" class="flex-1 bg-nature/5 text-nature py-3 rounded-xl font-bold text-sm text-center hover:bg-nature hover:text-white transition-all">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a>
                        <form onsubmit="return confirmAction(event, 'Delete Donor?', 'This record will be removed from the Hall of Fame.')" action="index.php" method="GET" class="flex-1">
                            <input type="hidden" name="delete" value="<?= $donor['id'] ?>">
                            <button type="submit" class="w-full bg-red-50 text-red-600 py-3 rounded-xl font-bold text-sm hover:bg-red-600 hover:text-white transition-all">
                                <i class="fas fa-trash mr-2"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($donors)): ?>
                <div class="col-span-full py-32 text-center bg-nature/5 rounded-[3rem] border-4 border-dashed border-nature/10">
                    <i class="fas fa-heart text-6xl text-nature/10 mb-6"></i>
                    <h3 class="text-2xl font-bold text-nature">The Hall of Fame is Empty</h3>
                    <p class="text-nature/40 mt-2">Start recognizing your noble donors today.</p>
                    <a href="editor.php" class="inline-block mt-8 bg-nature text-white px-8 py-3 rounded-xl font-bold">Add First Donor</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>