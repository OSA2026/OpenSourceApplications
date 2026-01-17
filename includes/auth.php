<?php
// includes/auth.php

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/public/login.php');
        exit;
    }
}

function require_admin() {
    if (!is_admin()) {
        header('Location: ' . BASE_URL . '/public/login.php');
        exit;
    }
}

function login_user($user) {
    if ($user['status'] === 'suspended') {
        return false;
    }
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    session_regenerate_id(true);
    return true;
}

function logout_user() {
    $_SESSION = [];
    session_destroy();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}

function check_user_status($pdo) {
    if (!is_logged_in()) return;
    
    $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        logout_user();
        header('Location: ' . BASE_URL . '/public/login.php');
        exit;
    }
}

function is_user_suspended($pdo) {
    if (!is_logged_in()) return false;
    
    try {
        $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        return ($user && $user['status'] === 'suspended');
    } catch (Exception $e) {
        return false;
    }
}
