<?php
function startGameSession($user_id, $game_id) {
    $conn = getDbConnection();
    try {
        $stmt = $conn->prepare("INSERT INTO game_sessions (user_id, game_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $game_id]);
        return $conn->lastInsertId();
    } catch (PDOException $e) {
        error_log("Start game session error: " . $e->getMessage());
        return false;
    }
}

function getGameDetails($game_id) {
    $conn = getDbConnection();
    try {
        $stmt = $conn->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->execute([$game_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get game details error: " . $e->getMessage());
        return false;
    }
}

function getTopScores($game_id, $limit = 10) {
    $conn = getDbConnection();
    try {
        $stmt = $conn->prepare("
            SELECT u.username, s.score
            FROM scores s 
            JOIN users u ON s.user_id = u.id 
            WHERE s.game_id = ? 
            ORDER BY s.score DESC 
            LIMIT ?
        ");
        $stmt->execute([$game_id, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get top scores error: " . $e->getMessage());
        return [];
    }
}