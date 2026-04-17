<?php
session_start();
$_SESSION['test'] = 'working';
echo "Session Status: " . session_status() . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Value: " . ($_SESSION['test'] ?? 'not set') . "\n";
print_r($_SESSION);
?>
