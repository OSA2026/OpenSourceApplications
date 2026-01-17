<?php
// includes/validators.php

function validate_required($value) {
    return !empty(trim($value));
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

function sanitize_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
