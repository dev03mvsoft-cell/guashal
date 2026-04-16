<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
echo "Starting Debug Script...\n";

try {
    $db_file = __DIR__ . '/config/db.php';
    if (!file_exists($db_file)) {
        echo "Error: config/db.php not found at $db_file\n";
    } else {
        require_once $db_file;
        echo "Database connection loaded.\n";

        if (isset($pdo)) {
            $stmt = $pdo->query("SELECT id, username, email, login_otp, otp_expiry FROM admin_users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($users)) {
                echo "No users found in admin_users table.\n";
            } else {
                echo "Found " . count($users) . " users:\n";
                foreach ($users as $user) {
                    echo "ID: {$user['id']} | Username: {$user['username']} | Email: {$user['email']} | OTP: {$user['login_otp']} | Expiry: {$user['otp_expiry']}\n";
                }
            }
        } else {
            echo "Error: PDO object not initialized.\n";
        }
    }
} catch (Exception $e) {
    echo "Caught Exception: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "Caught Error: " . $e->getMessage() . "\n";
}

$output = ob_get_clean();
file_put_contents('debug_users.txt', $output);
echo "Debug data written to debug_users.txt\n";
