<?php
require_once 'include/auth.php';
require_once '../config/db.php';

// Only Super Admin can access this page
if (($_SESSION['admin_role'] ?? '') !== 'Super Admin') {
    echo "<h1>Access Denied</h1><p>You do not have permission to access user management.</p><a href='index.php'>Back to Dashboard</a>";
    exit;
}

$message = '';
$error = '';

// Handle CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save') {
        $id = $_POST['id'] ?? null;
        $username = trim($_POST['username']);
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $role = $_POST['role'];
        $password = $_POST['password'] ?? '';

        try {
            if ($id) {
                // Update
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, full_name = ?, email = ?, role = ?, password = ? WHERE id = ?");
                    $stmt->execute([$username, $full_name, $email, $role, $hashed_password, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, full_name = ?, email = ?, role = ? WHERE id = ?");
                    $stmt->execute([$username, $full_name, $email, $role, $id]);
                }
                $message = "User updated successfully!";
            } else {
                // Create
                if (empty($password)) throw new Exception("Password is required for new users.");
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO admin_users (username, full_name, email, role, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $full_name, $email, $role, $hashed_password]);
                $message = "New user created successfully!";
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        try {
            $stmt_check = $pdo->prepare("SELECT username FROM admin_users WHERE id = ?");
            $stmt_check->execute([$id]);
            $u = $stmt_check->fetch();
            if ($u && $u['username'] === $_SESSION['admin_user']) {
                $error = "Self-preservation active: You cannot delete your own account!";
            } else {
                $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
                $stmt->execute([$id]);
                $message = "User removed from path successfully!";
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt_check = $pdo->prepare("SELECT username FROM admin_users WHERE id IN ($placeholders)");
            $stmt_check->execute($ids);
            $targeted = $stmt_check->fetchAll(PDO::FETCH_COLUMN);

            if (in_array($_SESSION['admin_user'], $targeted)) {
                $error = "Security Violation: Bulk purge blocked because it includes your own account.";
            } else {
                $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id IN ($placeholders)");
                $stmt->execute($ids);
                $message = count($ids) . " administrators removed successfully!";
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Fetch all users
$users = [];
try {
    $stmt = $pdo->query("SELECT * FROM admin_users ORDER BY id ASC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Users table not found. Please run setup.php first.";
}

// Fetch for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Access - Gaushala Admin</title>
    <?php include 'include/head.php'; ?>
    <style>
        .input-round {
            border-radius: 1rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.3s;
            background: white;
        }

        .input-round:focus {
            outline: none;
            border-color: #FF6A00;
            box-shadow: 0 0 0 4px rgba(255, 106, 0, 0.1);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col md:flex-row bg-[#fdfaf7]">
    <?php include 'include/sidebar.php'; ?>
    <main class="flex-1 p-6 md:p-12 overflow-y-auto">
        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12">
                <div class="mb-6 md:mb-0">
                    <h1 style="font-family: 'Playfair Display';" class="text-4xl font-bold">Access <span class="text-saffron italic">Management</span></h1>
                    <p class="text-gray-400 mt-2 text-[12px] tracking-widest uppercase font-bold">ADMIN ROLES & PORTAL PERMISSIONS</p>
                </div>
                <div class="flex items-center gap-6">
                    <form id="bulk-form" method="POST" onsubmit="return confirmAction(event, 'Purge selected users?', 'This will permanently revoke their access to the portal.');">
                        <input type="hidden" name="action" value="bulk_delete">
                        <div id="bulk-delete-btn" style="display: none;" class="items-center gap-4 bg-red-50 text-red-600 px-6 py-3 rounded-2xl animate-fade-in border border-red-100 shadow-xl shadow-red-500/10">
                            <span class="text-[12px] font-black uppercase tracking-widest">Selected: <span id="selected-count">0</span></span>
                            <button type="submit" class="bg-red-600 text-white w-8 h-8 rounded-lg flex items-center justify-center hover:scale-110 transition-transform">
                                <i class="fas fa-trash-alt text-[12px]"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </header>

            <div class="mb-10 flex items-center px-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" onchange="toggleSelectAll(this, 'multi-select-item')" class="w-5 h-5 rounded-lg border-2 border-nature/10 text-saffron focus:ring-saffron transition-all cursor-pointer">
                    <span class="text-[12px] font-black uppercase tracking-widest text-nature/20 group-hover:text-nature transition-colors">Select All Administrators</span>
                </label>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <div class="lg:col-span-1">
                    <div class="glass p-8 rounded-[2.5rem] shadow-2xl border-t-8 border-saffron">
                        <h3 class="text-xl font-bold mb-8 flex items-center gap-3">
                            <i class="fas fa-user-shield text-saffron"></i> <?= $edit_data ? 'Edit' : 'Create' ?> User
                        </h3>

                        <?php if ($message): ?><div class="bg-green-50 text-green-700 p-4 rounded-xl text-[12px] mb-6 font-bold"><?= $message ?></div><?php endif; ?>
                        <?php if ($error): ?><div class="bg-red-50 text-red-700 p-4 rounded-xl text-[12px] mb-6 font-bold"><?= $error ?></div><?php endif; ?>

                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="action" value="save">
                            <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id'] ?>"><?php endif; ?>

                            <div>
                                <label class="block text-[12px] font-bold uppercase text-gray-400 mb-2">Username</label>
                                <input type="text" name="username" class="input-round" value="<?= $edit_data ? htmlspecialchars($edit_data['username']) : '' ?>" required>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold uppercase text-gray-400 mb-2">Full Name</label>
                                <input type="text" name="full_name" class="input-round" value="<?= $edit_data ? htmlspecialchars($edit_data['full_name']) : '' ?>" required>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold uppercase text-gray-400 mb-2">Email Address</label>
                                <input type="email" name="email" class="input-round" value="<?= $edit_data ? htmlspecialchars($edit_data['email']) : '' ?>" required>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold uppercase text-gray-400 mb-2">Role</label>
                                <select name="role" class="input-round">
                                    <option value="Super Admin" <?= ($edit_data && $edit_data['role'] == 'Super Admin') ? 'selected' : '' ?>>Super Admin</option>
                                    <option value="Editor" <?= ($edit_data && $edit_data['role'] == 'Editor') ? 'selected' : '' ?>>Editor</option>
                                    <option value="Viewer" <?= ($edit_data && $edit_data['role'] == 'Viewer') ? 'selected' : '' ?>>Viewer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-bold uppercase text-gray-400 mb-2"><?= $edit_data ? 'New Password (Optional)' : 'Security Password' ?></label>
                                <input type="password" name="password" class="input-round" <?= $edit_data ? '' : 'required' ?>>
                            </div>

                            <button type="submit" class="w-full bg-[#2c4c3b] text-white py-4 rounded-2xl font-bold text-[15px] hover:shadow-xl transition-all">Save Account</button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="glass overflow-hidden rounded-[2rem] shadow-xl">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-8 py-5 text-[12px] font-bold uppercase text-gray-400">User Details</th>
                                    <th class="px-8 py-5 text-[12px] font-bold uppercase text-gray-400">Access Level</th>
                                    <th class="px-8 py-5 text-[12px] font-bold uppercase text-gray-400">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach ($users as $u): ?>
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 bg-nature/5 text-nature rounded-full flex items-center justify-center font-bold">
                                                    <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-nature"><?= htmlspecialchars($u['full_name']) ?></p>
                                                    <p class="text-[12px] text-gray-400"><?= htmlspecialchars($u['email']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="px-3 py-1 rounded-full text-[12px] font-bold uppercase tracking-widest <?= $u['role'] == 'Super Admin' ? 'bg-saffron text-white shadow-saffron/20' : 'bg-gold/10 text-gold' ?> shadow-lg">
                                                <?= $u['role'] ?>
                                            </span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <a href="?edit=<?= $u['id'] ?>" class="text-gray-300 hover:text-nature transition-colors"><i class="fas fa-edit"></i></a>
                                                <form method="POST" class="inline" onsubmit="return confirmAction(event, 'Revoke Access?', 'This user will no longer be able to enter the portal.');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors"><i class="fas fa-trash"></i></button>
                                                </form>
                                                <!-- Multi Select Checkbox -->
                                                <input type="checkbox" name="selected_ids[]" value="<?= $u['id'] ?>" form="bulk-form" onchange="updateBulkButtonVisibility()" class="multi-select-item w-5 h-5 rounded-lg border-2 border-nature/5 text-saffron focus:ring-saffron cursor-pointer shadow-inner">
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>