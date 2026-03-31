<?php
require_once __DIR__ . '/../config/db.php';

try {
    // 1. Create admin_users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        full_name VARCHAR(100),
        email VARCHAR(100),
        role VARCHAR(50) DEFAULT 'Editor',
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Create testimonials table
    $pdo->exec("CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        role VARCHAR(100),
        testimonial TEXT NOT NULL,
        rating INT DEFAULT 5,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Create announcements table
    $pdo->exec("CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        message_en TEXT NOT NULL,
        message_hi TEXT,
        message_gu TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 4. Create gallery table
    $pdo->exec("CREATE TABLE IF NOT EXISTS gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title_en VARCHAR(255),
        title_hi VARCHAR(255),
        title_gu VARCHAR(255),
        category VARCHAR(100),
        image_path VARCHAR(255),
        status VARCHAR(50) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 5. Create Events table
    $pdo->exec("CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        start_date DATE NOT NULL,
        end_date DATE,
        location VARCHAR(255),
        organizers TEXT,
        image_path VARCHAR(255),
        status ENUM('Upcoming', 'Ongoing', 'Past') DEFAULT 'Upcoming',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 6. Create Team table
    $pdo->exec("CREATE TABLE IF NOT EXISTS team (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name_en VARCHAR(255) NOT NULL,
        designation_en VARCHAR(255),
        image_path VARCHAR(255),
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 7. Create Founders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS founders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name_en VARCHAR(255) NOT NULL,
        bio_en TEXT,
        message_en TEXT,
        image_path VARCHAR(255),
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 8. Create Seva Options table
    $pdo->exec("CREATE TABLE IF NOT EXISTS seva_options (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title_en VARCHAR(255) NOT NULL,
        title_hi VARCHAR(255),
        title_gu VARCHAR(255),
        description_en TEXT,
        icon_class VARCHAR(100),
        color_class VARCHAR(100),
        sort_order INT DEFAULT 0,
        status VARCHAR(50) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 9. Create Contributions Table (For Frontend Ticker/Toast)
    $pdo->exec("CREATE TABLE IF NOT EXISTS contributions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        donor_name VARCHAR(100) NOT NULL,
        amount INT NOT NULL,
        message VARCHAR(255),
        location VARCHAR(100),
        status VARCHAR(50) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 10. Create Contact Requests table
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        purpose VARCHAR(100),
        message TEXT,
        status ENUM('New', 'Read', 'Replied') DEFAULT 'New',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Ensure phone exists if table was created earlier
    try {
        $pdo->exec("ALTER TABLE contact_requests ADD COLUMN IF NOT EXISTS phone VARCHAR(20) AFTER email");
    } catch (Exception $e) { /* Column might already exist */ }

    // 11. Create Detailed Donations table
    $pdo->exec("CREATE TABLE IF NOT EXISTS donations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        donor_name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(20),
        amount DECIMAL(10, 2) NOT NULL,
        seva_id INT,
        donation_date DATE,
        currency_type ENUM('INR', 'Foreign') DEFAULT 'INR',
        status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Completed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (seva_id) REFERENCES seva_options(id) ON DELETE SET NULL
    )");

    // 12. Pre-populate Seva Options if empty
    $count = $pdo->query("SELECT COUNT(*) FROM seva_options")->fetchColumn();
    if ($count == 0) {
        $sevas = [
            ['First Roti for Cow', 'गायों के लिए पहली रोटी', 'ગાય માટે પહેલી રોટલી', 'Start your day with the divine blessing of feeding the mother.', 'fas fa-bread-slice', 'saffron'],
            ['Adopt Cow for 1 Month', '1 महीने के लिए गाय गोद लें', '1 મહિના માટે ગાય દત્તક લો', 'Provide total care, food, and medicine for one Gau Mata.', 'fas fa-heart', 'nature'],
            ['Nandi Seva', 'नंदी सेवा', 'નંદી સેવા', 'Honor the strength and divinity of the sacred Nandi Dev.', 'fas fa-crown', 'gold'],
            ['Cow Treatment Seva', 'गौ उपचार सेवा', 'ગૌ સારવાર સેવા', 'Fund critical surgeries and life-saving medical procedures.', 'fas fa-hand-holding-medical', 'red-600'],
            ['Cow Shed Seva', 'गौशाला निर्माण सेवा', 'ગૌશાળા નિર્માણ સેવા', 'Help us build stable, comfortable homes for more rescues.', 'fas fa-house-chimney', 'nature'],
            ['Feed 20 Cows', '20 गायों को भोजन', '20 ગાયોને ભોજન', 'A massive act of kindness that ensures no one goes hungry.', 'fas fa-utensils', 'saffron'],
            ['Adopt Calf for 1 Month', '1 महीने के लिए बछड़ा गोद लें', '1 મહિના માટે વાછરડું દત્તક લો', 'Nurture the future by supporting an innocent, growing calf.', 'fas fa-baby', 'gold'],
            ['Medicines Kit for Cows', 'गायों के लिए मेडिसिन किट', 'ગાયો માટે મેડિસિન કીટ', 'Ensure every rescued soul has access to essential medicine.', 'fas fa-pills', 'red-600'],
            ['Green Fodder Seva', 'हरा चारा सेवा', 'લીલો ચારો સેવા', 'Provide fresh, nutritious green grass for daily nourishment.', 'fas fa-leaf', 'nature']
        ];
        $stmt = $pdo->prepare("INSERT INTO seva_options (title_en, title_hi, title_gu, description_en, icon_class, color_class) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($sevas as $s) $stmt->execute($s);
    }

    // 13. Pre-populate Contributions if empty
    $count_contrib = $pdo->query("SELECT COUNT(*) FROM contributions")->fetchColumn();
    if ($count_contrib == 0) {
        $contribs = [
            ['Rahul Sharma', 500, 'For Gau Mata Seva', 'Delhi'],
            ['Priya Patel', 1100, 'Blessings for cow treatment', 'Mumbai'],
            ['Amit Verma', 300, 'Daily Fodder Contribution', 'Ahmedabad'],
            ['Sanjay Gupta', 2100, 'In memory of late father', 'Pune'],
            ['Anjali Mehta', 501, 'Dedicated to divine Nandi', 'Surat']
        ];
        $stmt_c = $pdo->prepare("INSERT INTO contributions (donor_name, amount, message, location) VALUES (?, ?, ?, ?)");
        foreach ($contribs as $c) $stmt_c->execute($c);
    }

    // 14. Create Default Admin (admin / admin)
    $stmt = $pdo->prepare("INSERT INTO admin_users (username, full_name, email, role, password) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE password = VALUES(password)");
    $stmt->execute([
        'admin',
        'System Admin',
        'admin@gaushala.com',
        'Super Admin',
        password_hash('admin', PASSWORD_DEFAULT)
    ]);

    echo "<div style='font-family: sans-serif; padding: 50px; text-align: center; background: #fdfaf7; min-h-screen;'>
            <h1 style='color: #2c4c3b; font-size: 3rem;'>✅ Setup Successful!</h1>
            <p style='color: #666; font-size: 1.2rem; margin-bottom: 30px;'>Development Environment Initialized. Tables Created & Default User Ready.</p>
            
            <div style='display: inline-block; background: white; padding: 30px; border-radius: 20px; shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: left;'>
                <p style='margin: 5px 0;'><b>Username:</b> <code style='background: #eee; padding: 2px 5px;'>admin</code></p>
                <p style='margin: 5px 0;'><b>Password:</b> <code style='background: #eee; padding: 2px 5px;'>admin</code></p>
            </div>
            <br>
            <a href='index.php' style='background: #FF6A00; color: white; padding: 15px 30px; text-decoration: none; border-radius: 12px; display: inline-block; margin-top: 40px; font-weight: bold;'>Return to Dashboard</a>
          </div>";
} catch (PDOException $e) {
    echo "<div style='padding: 50px; color: red;'><h1>❌ Database Error:</h1> " . $e->getMessage() . "</div>";
}
