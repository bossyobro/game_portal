<?php
// game_functions.php

function startGameSession($user_id, $game_id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO game_sessions (user_id, game_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $game_id]);
    return $conn->lastInsertId();
}

function endGameSession($session_id, $score) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("UPDATE game_sessions SET 
        end_time = CURRENT_TIMESTAMP, 
        duration = TIMESTAMPDIFF(SECOND, start_time, CURRENT_TIMESTAMP),
        final_score = ? 
        WHERE id = ?");
    $stmt->execute([$score, $session_id]);
}

function recordScore($user_id, $game_id, $score) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO scores (user_id, game_id, score) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $game_id, $score]);
    return $conn->lastInsertId();
}

function checkAchievements($user_id, $game_id, $score) {
    $conn = getDbConnection();
    
    // Get existing achievements
    $stmt = $conn->prepare("SELECT achievement_name FROM user_achievements WHERE user_id = ? AND game_id = ?");
    $stmt->execute([$user_id, $game_id]);
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Define achievements
    $achievements = [
        'snake' => [
            ['name' => 'Beginner', 'condition' => $score >= 10],
            ['name' => 'Intermediate', 'condition' => $score >= 50],
            ['name' => 'Expert', 'condition' => $score >= 100],
        ],
        'tictactoe' => [
            ['name' => 'First Win', 'condition' => $score > 0],
            ['name' => 'Win Streak', 'condition' => $score >= 3],
        ]
    ];
    
    $game_type = $game_id == 1 ? 'snake' : 'tictactoe';
    
    foreach ($achievements[$game_type] as $achievement) {
        if ($achievement['condition'] && !in_array($achievement['name'], $existing)) {
            $stmt = $conn->prepare("INSERT INTO user_achievements (user_id, game_id, achievement_name) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $game_id, $achievement['name']]);
        }
    }
}

function getGameDetails($game_id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM games WHERE id = ?");
    $stmt->execute([$game_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getTopScores($game_id, $limit = 10) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        SELECT u.username, s.score, s.created_at 
        FROM scores s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.game_id = ? 
        ORDER BY s.score DESC 
        LIMIT ?
    ");
    $stmt->execute([$game_id, $limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}