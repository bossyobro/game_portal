<?php
// add_score.php
session_start();
require_once 'db.php';
require_once 'auth.php';

header('Content-Type: application/json');

try {
    // Authenticate user
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        throw new Exception('Unauthorized access', 403);
    }

    // Validate request method
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception('Invalid request method', 405);
    }

    // Validate input
    $score = filter_input(INPUT_POST, 'score', FILTER_VALIDATE_INT);
    $game_id = filter_input(INPUT_POST, 'game_id', FILTER_VALIDATE_INT);

    if ($score === false || $game_id === false) {
        throw new Exception('Invalid score or game ID');
    }

    // Get database connection
    $conn = getDbConnection();

    // Insert or update score with play count tracking
    $stmt = $conn->prepare(
        "INSERT INTO scores (user_id, game_id, score, play_count, created_at) 
        VALUES (?, ?, ?, 1, NOW()) 
        ON DUPLICATE KEY UPDATE 
        play_count = play_count + 1, 
        score = GREATEST(score, ?)"
    );
    $stmt->execute([$_SESSION['user_id'], $game_id, $score, $score]);


    $stmt->execute([$_SESSION['user_id'], $game_id, $score, $score]);

    // Respond with success
    echo json_encode([
        'status' => 'success', 
        'message' => 'Score recorded successfully',
        'play_count' => true // Indicates successful play count increment
    ]);

} catch (Exception $e) {
    // Log the error
    error_log('Score submission error: ' . $e->getMessage());

    // Send appropriate error response
    http_response_code($e->getCode() ?: 400);
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
    exit;
}