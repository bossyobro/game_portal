<?php
session_start();
require_once 'auth.php';
require_once 'db.php';
require_once 'game_functions.php';

try {
    checkAuth();

    $game = getGameDetails(1); // Snake game ID = 1
    if (!$game) {
        throw new Exception("Game not found");
    }

    $top_scores = getTopScores(1);
} catch (Exception $e) {
    error_log($e->getMessage());
    die("An error occurred: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Snake Game</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="game-wrapper">
        <h1><?php echo htmlspecialchars($game['name']); ?></h1>
        <p><?php echo htmlspecialchars($game['description']); ?></p>
        
        <div class="snake-game-container">
            <div id="score">Score: 0</div>
            <canvas id="snakeCanvas" width="400" height="400"></canvas>
        </div>

        <div class="game-controls">
            <button onclick="startGame()">Start New Game</button>
            <!-- Button to go back to the dashboard -->
            <button onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
        </div>

        <div class="leaderboard">
            <h2>Top Scores</h2>
            <table>
                <tr>
                    <th>Player</th>
                    <th>Score</th>
                </tr>
                <?php foreach ($top_scores as $score): ?>
                <tr>
                    <td><?php echo htmlspecialchars($score['username']); ?></td>
                    <td><?php echo htmlspecialchars($score['score']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        // Remove the session_id since it's no longer needed
        // window.session_id = <?php echo json_encode($session_id); ?>; // Remove this line
    </script>
    <script src="snake.js"></script>
</body>
</html>