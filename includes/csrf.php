<?php
// includes/csrf.php

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $_SESSION['csrf_last_error'] = 'FILE_TOO_LARGE';
        return false;
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_last_error'] = 'SESSION_EXPIRED';
        return false;
    }

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $_SESSION['csrf_last_error'] = 'TOKEN_MISMATCH';
        return false;
    }
    
    return true;
}

function get_csrf_error_message() {
    $error = $_SESSION['csrf_last_error'] ?? 'INVALID_TOKEN';
    unset($_SESSION['csrf_last_error']);
    
    switch ($error) {
        case 'FILE_TOO_LARGE':
            return 'The uploaded file is too large for the server configuration. Please check post_max_size.';
        case 'SESSION_EXPIRED':
            return 'Your session has expired. Please refresh the page and try again.';
        case 'TOKEN_MISMATCH':
            return 'Security token mismatch. This can happen if you have multiple tabs open. Please refresh.';
        default:
            return 'Invalid CSRF token. Please try again.';
    }
}

function csrf_field() {
    $token = generate_csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}
