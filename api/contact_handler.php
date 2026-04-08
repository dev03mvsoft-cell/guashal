<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mailer.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $purpose = trim($_POST['purpose'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Honeypot Check
    if (!empty($_POST['hp_catcher'])) {
        echo json_encode(['success' => false, 'message' => 'Spam activity detected. Request denied.']);
        exit;
    }

    // reCAPTCHA Token Check
    $recaptcha_token = $_POST['recaptcha_token'] ?? '';
    if (empty($recaptcha_token)) {
        echo json_encode(['success' => false, 'message' => 'Security challenge failed. Please refresh the page.']);
        exit;
    }

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO contact_requests (name, email, phone, purpose, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $purpose, $message]);

        // Send Email Notification
        $emailBody = "
            <h2>New Contact Request</h2>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Purpose:</strong> $purpose</p>
            <p><strong>Message:</strong><br>$message</p>
        ";
        sendGaushalaEmail('dev03.mvsoft@gmail.com', 'New Contact Request: ' . $name, $emailBody);

        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
