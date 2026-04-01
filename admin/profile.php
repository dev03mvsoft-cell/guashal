<?php
require_once 'include/auth.php';
require_once '../config/db.php';

$current_username = $_SESSION['admin_user'] ?? '';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    try {
        if (!empty($new_password)) {
            if ($new_password !== $confirm_password) {
                throw new Exception("New passwords do not match.");
            }
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET full_name = ?, username = ?, email = ?, password = ? WHERE username = ?");
            $stmt->execute([$full_name, $username, $email, $hash, $current_username]);
        } else {
            $stmt = $pdo->prepare("UPDATE admin_users SET full_name = ?, username = ?, email = ? WHERE username = ?");
            $stmt->execute([$full_name, $username, $email, $current_username]);
        }
        
        // Update session
        $_SESSION['admin_name'] = $full_name;
        $_SESSION['admin_user'] = $username;
        $current_username = $username;

        $message = "Profile updated successfully! Information has been synced.";
    } catch (Exception $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}

// Fetch current details
$admin = ['full_name' => 'Unknown', 'username' => 'Unknown', 'email' => '', 'role' => 'Administrator'];
try {
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$current_username]);
    $fetched = $stmt->fetch();
    if($fetched){
        $admin = $fetched;
    }
} catch (Exception $e) {
    // Database edge cases handling
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Gaushala Admin</title>
    <?php include 'include/head.php'; ?>
</head>
<body class="md:h-screen flex flex-col md:flex-row bg-[#faf8f6] overflow-hidden selection:bg-saffron selection:text-white">
    
    <?php include 'include/sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-12 overflow-y-auto h-full">
        <header class="mb-12">
            <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature mb-2">My <span class="italic text-saffron">Profile</span></h1>
            <p class="text-[12px] uppercase tracking-widest text-gray-400 font-bold">Manage your portal credentials and security</p>
        </header>

        <div class="max-w-4xl mx-auto glass p-10 md:p-14 rounded-[3rem] shadow-xl border border-white/60 relative overflow-hidden bg-white/50">
            <!-- decorative bg -->
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-saffron/5 rounded-full blur-[50px] pointer-events-none"></div>

            <form method="POST" class="space-y-8 relative z-10">
                
                <div class="flex items-center gap-6 mb-8 pb-8 border-b border-gray-200/60">
                    <div class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-nature to-[#1a2d23] text-white flex items-center justify-center text-4xl font-display italic font-bold shadow-2xl shadow-nature/20 border-4 border-white">
                        <?= strtoupper(substr($admin['full_name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div>
                        <h3 class="text-3xl font-display font-bold text-nature mb-1"><?= htmlspecialchars($admin['full_name'] ?? 'Loading...') ?></h3>
                        <p class="text-[11px] uppercase tracking-[0.2em] bg-saffron/10 text-saffron px-3 py-1 rounded-full inline-block font-black mt-1"><?= htmlspecialchars($admin['role'] ?? 'Super Admin') ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[11px] uppercase tracking-widest text-gray-400 font-bold mb-3 pl-1">Full Name</label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>" required 
                               class="w-full bg-white border border-gray-100 rounded-2xl py-4 px-6 text-sm font-bold text-nature focus:outline-none focus:border-saffron focus:ring-4 focus:ring-saffron/10 transition-all shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] uppercase tracking-widest text-gray-400 font-bold mb-3 pl-1">Username (Login ID)</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($admin['username'] ?? '') ?>" required 
                               class="w-full bg-white border border-gray-100 rounded-2xl py-4 px-6 text-sm font-bold text-nature focus:outline-none focus:border-saffron focus:ring-4 focus:ring-saffron/10 transition-all shadow-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] uppercase tracking-widest text-gray-400 font-bold mb-3 pl-1">Contact Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required 
                               class="w-full bg-white border border-gray-100 rounded-2xl py-4 px-6 text-sm font-bold text-nature focus:outline-none focus:border-saffron focus:ring-4 focus:ring-saffron/10 transition-all shadow-sm">
                    </div>
                </div>

                <div class="bg-gradient-to-br from-red-50/50 to-red-50/10 p-8 rounded-[2rem] border border-red-100 mt-10 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-red-100/30 rounded-full blur-3xl -mr-10 -mt-10"></div>
                    <h4 class="text-sm font-bold text-red-500 mb-6 flex items-center gap-3">
                        <i class="fas fa-shield-alt bg-red-100 p-2 rounded-lg text-red-600"></i> Authentication & Security
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[11px] uppercase tracking-widest text-gray-400 font-bold mb-3 pl-1">New Password <span class="text-gray-300 font-normal normal-case tracking-normal">(Leave blank to keep current)</span></label>
                            <input type="password" name="new_password" placeholder="Enter highly secure password" 
                                   class="w-full bg-white border border-gray-100 rounded-2xl py-4 px-6 text-sm font-bold text-nature focus:outline-none focus:border-red-400 focus:ring-4 focus:ring-red-400/10 transition-all shadow-sm placeholder:font-normal placeholder:text-gray-300">
                        </div>
                        <div>
                            <label class="block text-[11px] uppercase tracking-widest text-gray-400 font-bold mb-3 pl-1">Confirm Identity</label>
                            <input type="password" name="confirm_password" placeholder="••••••••" 
                                   class="w-full bg-white border border-gray-100 rounded-2xl py-4 px-6 text-sm font-bold text-nature focus:outline-none focus:border-red-400 focus:ring-4 focus:ring-red-400/10 transition-all shadow-sm placeholder:font-normal placeholder:text-gray-300 tracking-widest">
                        </div>
                    </div>
                </div>

                <div class="pt-8 flex justify-end">
                    <button type="submit" class="bg-nature text-white px-10 py-5 rounded-[2rem] font-bold uppercase tracking-widest text-[12px] hover:bg-saffron hover:scale-105 transition-all flex items-center gap-4 shadow-xl shadow-nature/20 group">
                        Confirm Changes <i class="fas fa-check-circle text-white/50 group-hover:text-white transition-colors text-lg"></i>
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
