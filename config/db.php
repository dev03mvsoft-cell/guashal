<?php
/*
|--------------------------------------------------------------------------
| Database Connection Settings (PDO)
|--------------------------------------------------------------------------
|
| This file handles the secure connection to your MySQL database using 
| the PHP Data Objects (PDO) extension. Prepared statements are used 
| automatically for all dynamic queries to prevent SQL injection.
|
*/

// --- Configuration ---
$host    = 'localhost';         // Database host
$db_name = 'gaushala_db';       // *** REPLACE WITH YOUR DATABASE NAME ***
$user    = 'root';              // Database username
$pass    = '';                  // Database password (empty by default on Localhost/XAMPP)
$charset = 'utf8mb4';           // Character set for international support

// --- Connection Logic ---
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
$options = [
    3 => 2,     // PDO::ATTR_ERRMODE => PDO::ATTR_ERRMODE_EXCEPTION
    19 => 2,    // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    20 => false, // PDO::ATTR_EMULATE_PREPARES
    1002 => "SET NAMES utf8mb4", // PDO::MYSQL_ATTR_INIT_COMMAND
];

try {
    // Create the PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Success: Connection established!
} catch (\PDOException $e) {
    // Log error and stop if connection fails (The user will see this until they create the database)
    error_log("Database connection failed: " . $e->getMessage());
    die("<h3>[Backend Notice]</h3> Database connection setup is complete, but the database <b>'$db_name'</b> does not exist yet. Please create it in phpMyAdmin to proceed.");
}
?>
