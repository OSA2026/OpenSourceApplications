<?php
// includes/config.php

define('DB_HOST', 'localhost');
define('DB_NAME', 'osa_studio');
define('DB_USER', 'root');
define('DB_PASS', '');

define('BASE_URL', 'http://localhost/osaapp');
define('SITE_NAME', 'Open Source Applications');

// Session security
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
