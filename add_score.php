<?php
// add_score.php
session_start();
require 'db.php';
require 'auth.php';

checkAuth();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        validateCSRFToken($_POST['csrf_token']);
        
        if (!isset($_POST['score']) || !isset($_POST['game_id'])) {
            throw new Exception('Missing required parameters');
        }

        $user_id = $_SESSION['user_id'];
        $score = filter_var($_POST['score'], FILTER_VALIDATE_INT);
        $game_id = filter_var($_POST['game_id'], FILTER_VALIDATE_INT);

        if ($score === false || $game_id === false) {
            throw new Exception('Invalid score or game ID');
        }

        $conn = getDbConnection();
        $stmt = $conn->prepare(
            "INSERT INTO scores (user_id, game_id, score) VALUES (?, ?, ?)"
        );
        $stmt->execute([$user_id, $game_id, $score]);

        echo json_encode(["status" => "success"]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}