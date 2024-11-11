<?php
session_start();
require_once 'db.php';
require_once 'auth.php';
require_once 'game_functions.php';

header('Content-Type: application/json');

try {
    // Check if the user is authenticated
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        throw new Exception('Unauthorized access', 403);
    }

    // Validate the request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method', 405);
    }

    // Get the input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input parameters
    if (!isset($input ['game_id']) || !isset($input['score']) || !isset($input['session_id'])) {
        throw new Exception('Missing required parameters');
    }

    $game_id = filter_var($input['game_id'], FILTER_VALIDATE_INT);
    $score = filter_var($input['score'], FILTER_VALIDATE_INT);
    $session_id = filter_var($input['session_id'], FILTER_VALIDATE_INT);

    if ($game_id === false || $score === false || $session_id === false) {
        throw new Exception('Invalid input parameters');
    }

    $conn = getDbConnection();

    // Update game session
    $stmt = $conn->prepare("
        UPDATE game_sessions 
        SET end_time = NOW(), 
            final_score = ? 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$score, $session_id, $_SESSION['user_id']]);

    // Record or update score
    $stmt = $conn->prepare("
        INSERT INTO scores (user_id, game_id, score, play_count) 
        VALUES (?, ?, ?, 1) 
        ON DUPLICATE KEY UPDATE 
        score = GREATEST(score, ?), 
        play_count = play_count + 1
    ");
    $stmt->execute([$_SESSION['user_id'], $game_id, $score, $score]);

    echo json_encode([
        'success' => true, 
        'message' => 'Game session completed'
    ]);

} catch (Exception $e) {
    error_log('Game session error: ' . $e->getMessage());
    http_response_code($e->getCode() ?: 400);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
    exit;
}