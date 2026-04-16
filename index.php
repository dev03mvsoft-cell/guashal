<?php
// --- Real-time Monitoring & Auto-blocking (Fixed) ---
function log_suspicious_activity($reason)
{
    $logfile = __DIR__ . '/suspicious_activity.log';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    $entry = date('Y-m-d H:i:s') . " | IP: $ip | UA: $ua | Reason: $reason\n";
    file_put_contents($logfile, $entry, FILE_APPEND | LOCK_EX);
}

// Block known malicious bots and scanners
if (
    !empty($_SERVER['HTTP_USER_AGENT']) &&
    preg_match('/(sqlmap|nikto|acunetix|wpscan|fuzz|nmap|dirbuster|havij|zaproxy|crawler|bot|spider|curl|wget|python|perl|ruby|java|scan|masscan|hydra|netsparker|owasp)/i', $_SERVER['HTTP_USER_AGENT'])
) {
    log_suspicious_activity('Malicious bot detected');
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied.');
}

// Block suspicious file access attempts
if (
    !empty($_SERVER['REQUEST_URI']) &&
    strpos($_SERVER['REQUEST_URI'], '/admin') !== 0 &&
    preg_match('/\.(php[0-9]?|phtml|phps|bak|swp|orig|save)$/i', $_SERVER['REQUEST_URI'])
) {
    log_suspicious_activity('Suspicious file access');
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied.');
}

// Add more triggers as needed for your environment
// Example: Block requests with suspicious query strings, POST data, etc.

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
    '/donors' => 'donors-hall.php',
];

// Get current URL and strip query strings for routing
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/'; // Adjust if site is in subfolder
$url = strtok(str_replace($base_path, '/', $request_uri), '?');
if ($url !== '/' && substr($url, 0, 1) !== '/') $url = '/' . $url;

include 'include/header.php';
// Simple Router
if (isset($routes[$url])) {
    include 'view/' . $routes[$url];
} else {
    include 'view/home.php'; // Default page
}
include 'include/footer.php';
