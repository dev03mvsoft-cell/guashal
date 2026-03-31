<?php
require_once 'config/db.php';
// Simple Routes Array
$routes = [
    '/' => 'home.php',
    '/about' => 'about.php',
    '/contact' => 'contact.php',
    '/gallery' => 'gallery.php',
    '/events' => 'events.php',
    '/announcements' => 'announcements.php',
    '/team' => 'team.php',
    '/founders' => 'founders.php',
    '/gaushala-seva' => 'gaushala-seva.php',
    '/donate' => 'donate.php',
    '/payment' => 'payment.php',
];

// Get current URL and strip query strings for routing
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/'; // Adjust if site is in subfolder
$url = strtok(str_replace($base_path, '/', $request_uri), '?');
if($url !== '/' && substr($url, 0, 1) !== '/') $url = '/' . $url;

include 'include/header.php';

// Simple Router
if (isset($routes[$url])) {
    include 'view/' . $routes[$url];
} else {
    include 'view/home.php'; // Default page
}

include 'include/footer.php';
?>
