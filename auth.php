<?php
// auth.php
function checkAuth() {
    if (!isset($_SESSION['user_id']) || 
        !isset($_SESSION['authenticated']) || 
        $_SESSION['authenticated'] !== true) {
        header("Location: login.php");
        exit;
    }
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $token)) {
        throw new Exception('CSRF token validation failed');
    }
}