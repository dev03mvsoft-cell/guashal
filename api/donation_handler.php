<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mailer.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (int)($_POST['amount'] ?? 0);
    $donor_name = trim($_POST['donor_name'] ?? $_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $currency = trim($_POST['currency'] ?? $_POST['currency_type'] ?? 'INR');
    $seva_id = (int)($_POST['seva_id'] ?? 0);
    // Input validation
    if (!preg_match('/^[a-zA-Z\s.]{2,100}$/', $donor_name)) {
        echo json_encode(['success' => false, 'message' => 'Invalid donor name.']); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']); exit;
    }
    if (!preg_match('/^[0-9]{10,15}$/', $phone) && !empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number.']); exit;
    }
    if (!preg_match('/^[A-Z]{3,5}$/', $currency)) {
        echo json_encode(['success' => false, 'message' => 'Invalid currency.']); exit;
    }

    // Honeypot Check
    if (!empty($_POST['hp_catcher'])) {
        echo json_encode(['success' => false, 'message' => 'Spam activity detected. Request denied.']);
        exit;
    }

    // reCAPTCHA Token Check
    $recaptcha_token = $_POST['recaptcha_token'] ?? '';
    // Bypass for localhost development
    $host = strtok($_SERVER['HTTP_HOST'] ?? 'localhost', ':');
    $is_localhost = ($host === 'localhost' || $host === '127.0.0.1');

    if (empty($recaptcha_token) && !$is_localhost) {
        echo json_encode(['success' => false, 'message' => 'Security challenge failed. Please refresh the page.']);
        exit;
    }

    if ($amount <= 0 || empty($donor_name) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required donation details.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO donations (amount, donor_name, email, phone, currency_type, seva_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$amount, $donor_name, $email, $phone, $currency, $seva_id]);

        // Send Email Notification
        $emailBody = "
            <h2>New Sacred Donation</h2>
            <p><strong>Donor Name:</strong> " . htmlspecialchars($donor_name, ENT_QUOTES, 'UTF-8') . "</p>
            <p><strong>Amount:</strong> " . htmlspecialchars($currency, ENT_QUOTES, 'UTF-8') . " $amount</p>
            <p><strong>Email:</strong> " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</p>
            <p><strong>Phone:</strong> " . htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') . "</p>
            <p><strong>Seva ID:</strong> $seva_id</p>
            <p><strong>Status:</strong> Pending Verification</p>
        ";
        sendGaushalaEmail('dev03.mvsoft@gmail.com', 'New Sacred Donation: ' . $donor_name, $emailBody);

        echo json_encode(['success' => true, 'message' => 'Your sacred donation has been recorded. Thank you for your support.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
