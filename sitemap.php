<?php
header("Content-Type: application/xml; charset=utf-8");

// Detect protocol and host
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base_url = $protocol . "://" . $host;

// Define priority routes with SEO weight
$routes = [
    '/' => ['priority' => '1.0', 'changefreq' => 'daily'],
    '/about' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    '/gallery' => ['priority' => '0.8', 'changefreq' => 'weekly'],
    '/events' => ['priority' => '0.7', 'changefreq' => 'weekly'],
    '/announcements' => ['priority' => '0.7', 'changefreq' => 'daily'],
    '/donors' => ['priority' => '0.6', 'changefreq' => 'weekly'],
    '/founders' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/team' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/contact' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/donate' => ['priority' => '0.9', 'changefreq' => 'weekly'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <?php foreach ($routes as $path => $config): ?>
    <url>
        <loc><?php echo $base_url . $path; ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq><?php echo $config['changefreq']; ?></changefreq>
        <priority><?php echo $config['priority']; ?></priority>
    </url>
    <?php endforeach; ?>
</urlset>
