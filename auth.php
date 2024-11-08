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
