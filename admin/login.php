<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// If already logged in, go to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Hardcoded for now if table doesn't exist, or check table
    // For a real app, we check the database. 
    // Let's implement a simple check and provide a way to setup.

    try {
        // Allow login with either username or email
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $user['username'];
            $_SESSION['admin_name'] = $user['full_name'] ?? $user['username'];
            $_SESSION['admin_role'] = $user['role'] ?? 'Editor';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } catch (PDOException $e) {
        $error = "System Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gaushala Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        nature: '#2c4c3b',
                        saffron: '#FF6A00',
                        gold: '#FFD700',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.84);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .bg-pattern {
            background-color: #fdfaf7;
            background-image: url("https://www.transparenttextures.com/patterns/pinstriped-suit.png");
        }
    </style>
</head>

<body class="bg-pattern min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full">
        <!-- Logo/Header -->
        <div class="text-center mb-10">
            <div class="inline-block p-4 bg-white rounded-3xl shadow-2xl mb-6">
                <img src="/asset/img/logo/logo.png" class="w-16 h-16 object-contain" alt="Logo">
            </div>
            <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold text-nature">Admin <span class="text-saffron italic">Panel</span></h1>
            <p class="text-gray-400 text-sm mt-2 uppercase tracking-widest font-bold">Gaushala Management Portal</p>
        </div>

        <!-- Login Card -->
        <div class="glass p-10 rounded-[2.5rem] shadow-2xl border-t-8 border-saffron">
            <h2 class="text-xl font-bold text-nature mb-8 flex items-center gap-3">
                <i class="fas fa-lock text-saffron"></i> Secure Login
            </h2>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-700 p-4 rounded-xl text-xs mb-6 border-l-4 border-red-500 font-bold">
                    <i class="fas fa-exclamation-circle mr-2"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-bold mb-3 px-1">Username</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                        <input type="text" name="username" required
                            class="w-full bg-white border border-gray-100 rounded-2xl py-4 pl-12 pr-4 focus:outline-none focus:ring-4 focus:ring-saffron/10 focus:border-saffron transition-all"
                            placeholder="admin">
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] uppercase tracking-widest text-gray-400 font-bold mb-3 px-1">Password</label>
                    <div class="relative">
                        <i class="fas fa-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                        <input type="password" name="password" required
                            class="w-full bg-white border border-gray-100 rounded-2xl py-4 pl-12 pr-4 focus:outline-none focus:ring-4 focus:ring-saffron/10 focus:border-saffron transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between px-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" class="rounded text-saffron focus:ring-saffron">
                        <span class="text-xs text-gray-400 group-hover:text-nature transition-colors">Remember me</span>
                    </label>
                    <a href="#" class="text-xs text-gray-400 hover:text-saffron transition-colors">Forgot Password?</a>
                </div>

                <button type="submit"
                    class="w-full bg-saffron text-white py-5 rounded-2xl font-bold uppercase tracking-widest text-sm hover:shadow-2xl hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3 shadow-xl shadow-saffron/20">
                    Sign In to Portal <i class="fas fa-arrow-right opacity-50"></i>
                </button>
            </form>
        </div>

        <p class="text-center mt-10 text-gray-400 text-xs">
            &copy; <?= date('Y') ?> Shri Gau Rakshak Seva Samiti. <br>
            All Rights Reserved.
        </p>
    </div>

</body>

</html>