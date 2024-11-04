<?php
// app.php
session_start();
require 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// This file can contain your app logic or routing, if necessary
// Example: Load user data or handle specific requests
try {
    $conn = getDbConnection();
    // Fetch user data or perform other operations
} catch (PDOException $e) {
    // Log the error and redirect
    error_log("Database error: " . $e->getMessage());
    header("Location: error.php");
    exit;
}
?>