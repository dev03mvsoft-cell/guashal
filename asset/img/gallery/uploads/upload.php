<?php
// Secure File Upload Handler Example
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $filename = $file['name'];
    $tmp = $file['tmp_name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $mimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmp);
    finfo_close($finfo);
    // Block double extensions
    if (preg_match('/\.(php|phtml|php3|php4|php5|php7|phps)\./i', $filename)) {
        http_response_code(400); exit('Invalid file name.');
    }
    // Validate extension and MIME
    if (!in_array($ext, $allowed) || !in_array($mime, $mimeTypes)) {
        http_response_code(400); exit('Invalid file type.');
    }
    // Scan file content for PHP/webshell signatures
    $content = file_get_contents($tmp);
    if (preg_match('/<\?php|eval\(|base64_decode\(|shell_exec|system\(|passthru\(|`|\$_GET|\$_POST|\$_REQUEST|\$_FILES|\$_SERVER|\$_SESSION|\$_COOKIE/i', $content)) {
        http_response_code(400); exit('Malicious content detected.');
    }
    // Move file
    $newname = uniqid('img_', true) . '.' . $ext;
    $dest = __DIR__ . '/' . $newname;
    if (move_uploaded_file($tmp, $dest)) {
        echo 'Upload successful: ' . htmlspecialchars($newname, ENT_QUOTES, 'UTF-8');
    } else {
        http_response_code(500); exit('Upload failed.');
    }
} else {
    http_response_code(405); exit('Method not allowed.');
}