<?php
session_start();
require_once 'db.php';
require_once 'auth.php';
require_once 'game_functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        throw new Exception('Unauthorized access', 403);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method', 405);
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['game_id']) || !isset($input['score']) || !isset($input['session_id'])) {
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
        INSERT INTO scores (user_id, game_id, score) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$_SESSION['user_id'], $game_id, $score]);

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