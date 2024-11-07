<?php
// handle_game.php
session_start();
require_once 'auth.php';
require_once 'db.php';
require_once 'game_functions.php';

checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $session_id = $data['session_id'];
    $game_id = $data['game_id'];
    $score = $data['score'];
    
    // End the game session
    endGameSession($session_id, $score);
    
    // Record the score
    recordScore($_SESSION['user_id'], $game_id, $score);
    
    // Check for achievements
    checkAchievements($_SESSION['user_id'], $game_id, $score);
    
    // Get any new achievements
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        SELECT achievement_name 
        FROM user_achievements 
        WHERE user_id = ? AND game_id = ? 
        AND achieved_at >= NOW() - INTERVAL 5 SECOND
    ");
    $stmt->execute([$_SESSION['user_id'], $game_id]);
    $new_achievements = $stmt ->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode(['success' => true, 'achievements' => $new_achievements]);
    exit;
}
?> 