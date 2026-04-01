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
    <title>Sacred Portal - Gaushala Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        nature: '#2c4c3b',
                        saffron: '#FF6A00',
                        gold: '#c0a50eff',
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #fdfaf7;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 40px 100px -10px rgba(44, 76, 59, 0.2);
        }

        .input-premium {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(192, 165, 14, 0.1);
            transition: all 0.4s ease;
        }

        .input-premium:focus {
            border-color: #FF6A00;
            box-shadow: 0 0 0 4px rgba(255, 106, 0, 0.1);
            background: #ffffff;
        }
    </style>
</head>

<body class="min-h-screen flex text-nature antialiased selection:bg-saffron selection:text-white relative overflow-hidden">

    <!-- Background Layer (Immersive) -->
    <div class="absolute inset-0 z-0">
        <img src="/asset/img/cow/gushala5.jpg" class="w-full h-full object-cover scale-100" alt="Gaushala Background">
        <div class="absolute inset-0 bg-gradient-to-r from-nature/95 via-nature/80 to-saffron/20 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-black/40"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 w-full flex min-h-screen">

        <!-- Left Side: Brand Narrative (Hidden on Mobile) -->
        <div class="hidden lg:flex flex-col justify-center w-[55%] p-20 text-white">
            <span class="text-gold tracking-[0.4em] text-xs font-black uppercase mb-6 flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-saffron animate-pulse shadow-[0_0_10px_#FF6A00]"></span>
                Authorized Personnel Only
            </span>
            <h1 class="font-display text-6xl xl:text-7xl font-bold leading-[1.1] mb-8 drop-shadow-2xl">
                The Sacred <br> <span class="italic text-gold underline decoration-gold/30 underline-offset-8">Sanctuary</span> Portal
            </h1>
            <p class="text-white/80 text-lg leading-relaxed max-w-lg border-l-2 border-gold/40 pl-6 italic mb-12 drop-shadow-md">
                "Manage the well-being of our Gau Mata with devotion, transparency, and care. Every administrative action here translates to a better life for them."
            </p>

            <div class="flex items-center gap-6 mt-auto">
                <div class="w-18 h-18 bg-white/100 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-xl">
                    <img src="/asset/img/logo/logo.png" class="w-16 h-16 object-contain drop-shadow-md" alt="Logo">
                </div>
                <div>
                    <h3 class="font-display font-bold text-2xl tracking-wider drop-shadow-md">શ્રી ગૌ રક્ષક સેવા સમિતિ</h3>
                    <p class="text-[10px] text-gold/80 uppercase tracking-[0.3em] font-bold mt-1">Management System v2.0</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Panel -->
        <div class="w-full lg:w-[45%] flex items-center justify-center p-6 lg:p-12 xl:p-20 relative">

            <!-- Mobile Logo (Visible only on mobile) -->
            <div class="absolute top-8 left-1/2 -translate-x-1/2 lg:hidden flex flex-col items-center z-50">
                <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-xl mb-3">
                    <img src="/asset/img/logo/logo.png" class="w-10 h-10 object-contain" alt="Logo">
                </div>
                <h3 class="font-display font-bold text-white text-2xl drop-shadow-md">Panjrapole Portal</h3>
            </div>

            <!-- Login Glass Card -->
            <div class="glass-panel w-full max-w-[460px] rounded-[2.5rem] p-10 md:p-14 relative overflow-hidden group">
                <!-- Decorative Accent -->
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-saffron to-gold"></div>
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-saffron/5 rounded-full blur-[50px] pointer-events-none group-hover:bg-saffron/10 transition-colors duration-1000"></div>

                <div class="mb-10 text-center relative z-10">
                    <h2 class="font-display text-4xl font-bold text-nature mb-3">Secure <span class="italic text-saffron">Login</span></h2>
                    <p class="text-nature/50 text-[10px] font-bold uppercase tracking-widest">Enter your credentials to access</p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-sm mb-8 border border-red-100 font-bold flex items-start gap-3 shadow-sm animate-[fadeIn_0.3s_ease] relative z-10">
                        <i class="fas fa-exclamation-circle mt-1"></i>
                        <span><?= $error ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6 relative z-10">
                    <!-- Username -->
                    <div class="space-y-2 relative">
                        <label class="block text-[10px] uppercase tracking-[0.2em] font-black text-nature/60 ml-2">Username or Email</label>
                        <div class="relative flex items-center">
                            <i class="fas fa-user absolute left-5 text-nature/30 text-lg pointer-events-none"></i>
                            <input type="text" name="username" required autocomplete="username"
                                class="input-premium w-full rounded-2xl py-4 pl-14 pr-6 text-nature font-bold text-[15px] placeholder:text-nature/20 placeholder:font-medium shadow-sm"
                                placeholder="Admin ID">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-2 relative">
                        <label class="block text-[10px] uppercase tracking-[0.2em] font-black text-nature/60 ml-2">Password</label>
                        <div class="relative flex items-center">
                            <i class="fas fa-lock absolute left-5 text-nature/30 text-lg pointer-events-none"></i>
                            <input type="password" name="password" id="admin_password" required autocomplete="current-password"
                                class="input-premium w-full rounded-2xl py-4 pl-14 pr-12 text-nature font-bold text-[15px] placeholder:text-nature/20 placeholder:font-medium tracking-[0.2em] shadow-sm"
                                placeholder="••••••••">
                            <button type="button" id="toggle_password" class="absolute right-5 text-nature/40 hover:text-saffron transition-colors focus:outline-none">
                                <i class="fas fa-eye" id="eye_icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center gap-3 cursor-pointer group/cb">
                            <div class="relative w-5 h-5 flex items-center justify-center">
                                <input type="checkbox" class="peer appearance-none w-5 h-5 border-2 border-nature/20 rounded-md bg-white checked:bg-saffron checked:border-saffron transition-all shadow-sm">
                                <i class="fas fa-check absolute text-white text-[10px] opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity duration-300"></i>
                            </div>
                            <span class="text-xs font-bold text-nature/60 group-hover/cb:text-nature transition-colors">Remember Session</span>
                        </label>
                        <a href="#" class="text-[11px] font-black text-saffron uppercase tracking-widest hover:text-nature transition-colors">Forgot Pwd?</a>
                    </div>

                    <div class="pt-6">
                        <button type="submit"
                            class="w-full bg-nature text-white py-5 rounded-2xl font-bold uppercase tracking-[0.2em] text-[13px] hover:bg-saffron hover:shadow-2xl hover:shadow-saffron/30 hover:-translate-y-1 active:scale-[0.98] transition-all duration-500 flex items-center justify-center gap-4 group/btn">
                            Access Portal
                            <i class="fas fa-arrow-right opacity-50 group-hover/btn:opacity-100 group-hover/btn:translate-x-2 transition-all duration-500"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-full text-center px-6 pointer-events-none">
                <p class="text-white/40 text-[9px] uppercase tracking-widest font-black">
                    &copy; <?= date('Y') ?> Shri Gau Rakshak Seva Samiti. <br class="lg:hidden">All Rights Reserved.
                </p>
            </div>

        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        document.getElementById('toggle_password').addEventListener('click', function() {
            const passwordInput = document.getElementById('admin_password');
            const eyeIcon = document.getElementById('eye_icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>

</html>