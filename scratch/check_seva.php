<?php
require_once __DIR__ . '/config/db.php';
$stmt = $pdo->query("DESCRIBE seva_options");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt = $pdo->query("SELECT * FROM seva_options LIMIT 5");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
