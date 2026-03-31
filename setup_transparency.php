<?php
require_once __DIR__ . '/include/db.php';

try {
    // Create transparency_materials table
    $pdo->exec("CREATE TABLE IF NOT EXISTS transparency_materials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name_en VARCHAR(255) NOT NULL,
        quantity VARCHAR(50) DEFAULT NULL,
        unit_price VARCHAR(50) DEFAULT NULL,
        total_amount VARCHAR(50) DEFAULT NULL,
        color_class VARCHAR(50) DEFAULT 'bg-saffron',
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Insert initial data
    $materials = [
        ['Shed Construction Material', 'शे्ड निर्माण सामग्री', '2,000', '650', '1300000', 'bg-saffron', 1],
        ['Medical Kit', 'मेडिकल किट', '2,971', '640', '1901440', 'bg-nature', 2],
        ['Green Fodder Bundle', 'हरा चारा बंडल', '2,971', '500', '1485500', 'bg-gold', 3],
        ['Jaggery', 'गुड़', '1,981', '600', '1188600', 'bg-saffron', 4],
        ['Animal Calcium (10L)', 'पशु कैल्शियम (10L)', '2,476', '500', '1238000', 'bg-nature', 5],
        ['Dry Fodder Bundle (45Kg)', 'सूखा चारा बंडल (45Kg)', '2,476', '540', '1337040', 'bg-gold', 6],
        ['Daliya', 'दलिया', '1,981', '550', '1089550', 'bg-saffron', 7],
        ['Sugar Cane Bundle', 'गन्ने का बंडल', '1,981', '400', '792400', 'bg-nature', 8],
    ];

    $stmt = $pdo->prepare("INSERT INTO transparency_materials (name_en, name_hi, quantity, unit_price, total_amount, color_class, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($materials as $m) {
        $stmt->execute($m);
    }

    // Create a settings table for Raised and Goal if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT
    )");

    $pdo->exec("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES 
        ('donation_raised', '2458626'),
        ('donation_goal', '10332530')
    ");

    echo "Table 'transparency_materials' created and initialized.";
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
