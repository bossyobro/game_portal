<?php
// add_score.php
session_start();
require 'db.php';

// Check if the user is logged in and if a score is provided
if (!isset($_SESSION['user_id']) || !isset($_POST['score'])) {
    header("Location: login.php");
    exit;
}

// Retrieve user ID and score
$user_id = $_SESSION['user_id'];
$score = intval($_POST['score']);

// Insert the score into the scores table
try {
    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO scores (user_id, score) VALUES (?, ?)");
    $stmt->execute([$user_id, $score]);
    
    // Respond with a JSON message (useful if called via AJAX)
    echo json_encode(["status" => "success", "message" => "Score added successfully."]);
} catch (PDOException $e) {
    // Log the error and respond with an error message
    error_log("Database error: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Failed to add score."]);
}
?>