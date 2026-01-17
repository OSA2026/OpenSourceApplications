<?php
// includes/upload.php

function upload_file($file, $destination_dir, $allowed_extensions = ['jpg', 'jpeg', 'png', 'zip', 'gz', 'tar', 'deb', 'rpm', 'apk']) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload failed with error code ' . $file['error']];
    }

    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_extensions)) {
        return ['success' => false, 'error' => 'Invalid file extension: ' . $ext];
    }

    // Max size: 2GB for releases, 5MB for images
    if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
        $max_size = 5 * 1024 * 1024;
    } else {
        $max_size = 2048 * 1024 * 1024; // 2GB
    }

    if ($file['size'] > $max_size) {
        $size_desc = ($max_size >= 1024 * 1024 * 1024) ? ($max_size / 1024 / 1024 / 1024) . 'GB' : ($max_size / 1024 / 1024) . 'MB';
        return ['success' => false, 'error' => 'File too large. Max allowed: ' . $size_desc];
    }

    $new_filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $target_path = $destination_dir . '/' . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'path' => $new_filename];
    }

    return ['success' => false, 'error' => 'Failed to move uploaded file.'];
}
