<?php
// includes/flash.php

function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

function display_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        $bg = 'bg-blue-900/50 border-blue-500 text-blue-200';
        if ($msg['type'] === 'success') $bg = 'bg-green-900/50 border-green-500 text-green-200';
        if ($msg['type'] === 'error') $bg = 'bg-red-900/50 border-red-500 text-red-200';
        
        echo '<div class="p-4 mb-4 border rounded-lg ' . $bg . ' backdrop-blur-md animate-fade-in-down">' . htmlspecialchars($msg['message']) . '</div>';
        unset($_SESSION['flash_message']);
    }
}
