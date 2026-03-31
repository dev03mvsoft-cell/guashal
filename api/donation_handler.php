<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mailer.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (int)($_POST['amount'] ?? 0);
    $donor_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $currency = trim($_POST['currency_type'] ?? 'INR');
    $seva_id = (int)($_POST['seva_id'] ?? 0);

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
            <p><strong>Donor Name:</strong> $donor_name</p>
            <p><strong>Amount:</strong> $currency $amount</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
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
