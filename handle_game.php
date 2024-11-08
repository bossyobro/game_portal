<?php
// handle_game.php
session_start();
require_once 'db.php';
require_once 'auth.php';
require_once 'game_functions.php';

header('Content-Type: application/json');

try {
    // Authenticate user
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        throw new Exception('Unauthorized access', 403);
    }

    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method', 405);
    }

    // Parse JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (!isset($input['game_id']) || !isset($input['score']) || !isset($input['session_id'])) {
        throw new Exception('Missing required parameters');
    }

    $game_id = filter_var($input['game_id'], FILTER_VALIDATE_INT);
    $score = filter_var($input['score'], FILTER_VALIDATE_INT);
    $session_id = filter_var($input['session_id'], FILTER_VALIDATE_INT);

    if ($game_id === false || $score === false || $session_id === false) {
        throw new Exception('Invalid input parameters');
    }

    // Verify game session
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        SELECT * FROM game_sessions 
        WHERE id = ? AND user_id = ? AND game_id = ? AND status = 'active'
    ");
    $stmt->execute([$session_id, $_SESSION['user_id'], $game_id]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        throw new Exception('Invalid game session');
    }

    // Close the game session
    $stmt = $conn->prepare("
        UPDATE game_sessions 
        SET status = 'completed', 
            end_time = NOW(), 
            final_score = ? 
        WHERE id = ?
    ");
    $stmt->execute([$score, $session_id]);

    // Check and award achievements
    $achievements = checkAchievements($_SESSION['user_id'], $game_id, $score);

    // Update player stats
    updatePlayerStats($_SESSION['user_id'], $game_id, $score);

    // Respond with success and potential achievements
    echo json_encode([
        'success' => true, 
        'message' => 'Game session completed',
        'achievements' => $achievements
    ]);

} catch (Exception $e) {
    // Log the error
    error_log('Game session error: ' . $e->getMessage());

    // Send appropriate error response
    http_response_code($e->getCode() ?: 400);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
    exit;
}

// Example achievement checking function (to be implemented)
function checkAchievements($user_id, $game_id, $score) {
    $achievements = [];

    // Example achievement logic
    if ($score > 50) {
        $achievements[] = 'High Scorer';
    }
    if ($score > 100) {
        $achievements[] = 'Master Player';
    }

    return $achievements;
}

// Example player stats update function (to be implemented)
function updatePlayerStats($user_id, $game_id, $score) {
    $conn = getDbConnection();
    
    // Update total games played
    $stmt = $conn->prepare("
        INSERT INTO player_stats (user_id, game_id, total_games_played, total_score) 
        VALUES (?, ?, 1, ?) 
        ON DUPLICATE KEY UPDATE 
        total_games_played = total_games_played + 1, 
        total_score = total_score + ?"
    );
    $stmt->execute([$user_id, $game_id, $score, $score]);
}