<?php
session_start(); // Start the session to access session variables
require_once 'db.php';
header('Content-Type: application/json');

try {
    // Check if the user is authenticated
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access', 403);
    }

    // Validate the request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method', 405);
    }

    // Get the input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input parameters
    if (!isset($input['game_id']) || !isset($input['score'])) {
        throw new Exception('Missing required parameters');
    }

    $game_id = filter_var($input['game_id'], FILTER_VALIDATE_INT);
    $score = filter_var($input['score'], FILTER_VALIDATE_INT);

    if ($game_id === false || $score === false) {
        throw new Exception('Invalid input parameters');
    }

    $conn = getDbConnection();

    // Use the user ID from the session
    $user_id = $_SESSION['user_id'];

    // Record the score in the database
    $stmt = $conn->prepare("
        INSERT INTO scores (user_id, game_id, score) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
        score = GREATEST(score, ?)
    ");
    $stmt->execute([$user_id, $game_id, $score, $score]);

    echo json_encode([
        'success' => true, 
        'message' => 'Game score recorded successfully'
    ]);

} catch (Exception $e) {
    error_log('Score submission error: ' . $e->getMessage());
    http_response_code($e->getCode() ?: 400);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
    exit;
}