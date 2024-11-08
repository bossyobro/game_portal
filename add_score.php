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

    // Optional: Add additional score validation
    if ($score < 0 || $score > 10000) { // Example validation
        throw new Exception('Score out of valid range');
    }

    // Optional: Rate limiting (basic implementation)
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        SELECT COUNT(*) as score_count 
        FROM scores 
        WHERE user_id = ? AND game_id = ? AND created_at > NOW() - INTERVAL 5 MINUTE
    ");
    $stmt->execute([$_SESSION['user_id'], $game_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['score_count'] >= 10) { // Limit 10 scores per 5 minutes
        throw new Exception('Too many score submissions');
    }

    // Insert score
    $stmt = $conn->prepare(
        "INSERT INTO scores (user_id, game_id, score, created_at) VALUES (?, ?, ?, NOW())"
    );
    $stmt->execute([$_SESSION['user_id'], $game_id, $score]);

    // Optional: Update user's best score
    $stmt = $conn->prepare("
        INSERT INTO user_best_scores (user_id, game_id, best_score) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
        best_score = GREATEST(best_score, ?)"
    );
    $stmt->execute([$_SESSION['user_id'], $game_id, $score, $score]);

    // Respond with success
    echo json_encode([
        'status' => 'success', 
        'message' => 'Score recorded successfully'
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