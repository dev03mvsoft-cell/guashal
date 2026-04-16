<?php
echo "Script started...\n";

require_once __DIR__ . '/config/db.php';

try {
    // 1. Create donors table
    $pdo->exec("CREATE TABLE IF NOT EXISTS donors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        profile_pic VARCHAR(255) DEFAULT 'default_donor.png',
        donation_date DATE NOT NULL,
        purpose VARCHAR(255) NOT NULL,
        amount DECIMAL(15, 2) DEFAULT 0.00,
        is_visible TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Create donor images directory if not exists
    $donor_dir = __DIR__ . '/asset/img/donors';
    if (!file_exists($donor_dir)) {
        mkdir($donor_dir, 0777, true);
    }

    echo "<h1>Database Setup Successful</h1>";
    echo "<p>Table 'donors' created/verified.</p>";
    echo "<p>Directory '/asset/img/donors' created/verified.</p>";
    
    // Insert dummy data if empty
    $check = $pdo->query("SELECT COUNT(*) FROM donors")->fetchColumn();
    if ($check == 0) {
        $stmt = $pdo->prepare("INSERT INTO donors (name, donation_date, purpose, amount) VALUES (?, ?, ?, ?)");
        $donors = [
            ['Rajesh Kumar', date('Y-m-d'), 'Birthday Celebration', 5100.00],
            ['Amit Shah', date('Y-m-d', strtotime('-1 day')), 'Mukshna Seva', 11000.00],
            ['Suresh Patel', date('Y-m-d', strtotime('-3 days')), 'Anniversary Donation', 2100.00]
        ];
        foreach ($donors as $d) {
            $stmt->execute($d);
        }
        echo "<p>Sample donor data inserted.</p>";
    }

} catch (Exception $e) {
    die("Error during setup: " . $e->getMessage());
}
