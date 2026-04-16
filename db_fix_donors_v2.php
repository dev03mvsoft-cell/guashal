<?php
require_once 'config/db.php';

echo "<h2>Starting Donors Table Fix...</h2>";

try {
    // Check if columns exist
    $query = $pdo->query("SHOW COLUMNS FROM donors");
    $columns = $query->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('special_date', $columns)) {
        echo "Adding special_date column...<br>";
        $pdo->exec("ALTER TABLE donors ADD COLUMN special_date DATE DEFAULT NULL");
    } else {
        echo "special_date column already exists.<br>";
    }

    if (!in_array('contact', $columns)) {
        echo "Adding contact column...<br>";
        $pdo->exec("ALTER TABLE donors ADD COLUMN contact VARCHAR(20) DEFAULT NULL");
    } else {
        echo "contact column already exists.<br>";
    }

    echo "<h3>Fix Completed Successfully!</h3>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
