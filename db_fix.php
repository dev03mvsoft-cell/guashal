<?php
/**
 * 🛠️ DATABASE SYNC (CLEAN VERSION) 🛠️
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/db.php';

echo "<body style='font-family: sans-serif; padding: 40px; background: #faf8f6;'>";
echo "<div style='max-width: 800px; margin: auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);'>";
echo "<h1 style='color: #2c4c3b;'>Schema Synchronization</h1>";

try {
    // 1. Check columns
    $res = $pdo->query("SHOW COLUMNS FROM `founders`")->fetchAll(PDO::FETCH_COLUMN);
    
    // 2. Fix 'contact'
    if(!in_array('contact', $res)) {
        $pdo->exec("ALTER TABLE `founders` ADD COLUMN `contact` VARCHAR(50) DEFAULT NULL AFTER `sort_order` ");
        echo "<p style='color: blue;'>[+] Added column: <b>contact</b></p>";
    }

    // 3. Fix 'type' (Founder/Trustee)
    if(!in_array('type', $res)) {
        $pdo->exec("ALTER TABLE `founders` ADD COLUMN `type` VARCHAR(20) DEFAULT 'trustee' AFTER `message_en` ");
        echo "<p style='color: blue;'>[+] Added column: <b>type</b></p>";
    }

    echo "<h2 style='color: green;'>✅ Sync Complete!</h2>";
    echo "<p>Your database now supports 'Role in Council' (Founder/Trustee).</p>";
    echo "<hr><a href='admin/founders/index.php' style='display: inline-block; padding: 10px 20px; background: #2c4c3b; color: white; text-decoration: none; border-radius: 10px;'>Go To Founders Panel</a>";

} catch (Exception $e) {
    echo "<p style='color: red;'><b>Error:</b> " . $e->getMessage() . "</p>";
}

echo "</div></body>";
?>
