<?php
require_once 'config/db.php';

try {
    // Add contact column if not exists
    $pdo->exec("ALTER TABLE donors ADD COLUMN contact VARCHAR(20) DEFAULT NULL");
} catch (Exception $e) { /* Column might already exist */ }

try {
    // Add special_date column if not exists (for birthdays/anniversaries)
    $pdo->exec("ALTER TABLE donors ADD COLUMN special_date DATE DEFAULT NULL");
} catch (Exception $e) { /* Column might already exist */ }

echo "Donors Table Synchronized Successfully!";
