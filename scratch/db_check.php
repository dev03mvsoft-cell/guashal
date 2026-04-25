<?php
$pdo = new PDO('mysql:host=localhost', 'root', '');
$stmt = $pdo->query('SHOW DATABASES');
while($row = $stmt->fetch(PDO::FETCH_NUM)) {
    echo $row[0] . PHP_EOL;
}
