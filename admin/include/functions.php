<?php
/**
 * Universal File Upload & Cleanup System
 */

/**
 * Handle Single File Upload
 * @param array $file_post $_FILES['field_name']
 * @param string $target_dir Directory relative to site root
 * @return string|false Path to saved file on success, false on failure
 */
function upload_file($file_post, $target_dir = 'uploads/') {
    if (!isset($file_post) || $file_post['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Determine the base path – assuming this file is in admin/include/
    $base_path = dirname(dirname(__DIR__)); 
    $full_target_dir = $base_path . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, trim($target_dir, '/')) . DIRECTORY_SEPARATOR;

    if (!file_exists($full_target_dir)) {
        mkdir($full_target_dir, 0777, true);
    }

    // Sanitize and generate filename
    $ext = pathinfo($file_post['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . uniqid() . '.' . $ext;
    $target_file = $full_target_dir . $filename;

    if (move_uploaded_file($file_post['tmp_name'], $target_file)) {
        return '/' . trim($target_dir, '/') . '/' . $filename;
    }

    return false;
}

/**
 * Cleanup File from Disk
 * @param string $file_path Absolute or relative path from site root
 */
function cleanup_file($file_path) {
    if (empty($file_path)) return;

    // Determine base path
    $base_path = dirname(dirname(__DIR__)); 
    
    // Normalize path – remove leading slash if present
    $rel_path = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file_path), DIRECTORY_SEPARATOR);
    $abs_path = $base_path . DIRECTORY_SEPARATOR . $rel_path;
    
    if (file_exists($abs_path) && is_file($abs_path)) {
        @unlink($abs_path);
    }
}
