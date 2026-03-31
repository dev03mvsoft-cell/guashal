<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    // 1. Fetch the last 10 divine contributions
    $stmt = $pdo->query("SELECT donor_name, amount, message, created_at FROM contributions WHERE status = 'active' ORDER BY created_at DESC LIMIT 10");
    if ($stmt) {
        $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($donations);
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    echo json_encode([]); // Fail gracefully by returning an empty blessing list
}
?>
