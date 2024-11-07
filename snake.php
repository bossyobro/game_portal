<?php
// snake.php
session_start();
require_once 'auth.php';
require_once 'db.php';
require_once 'game_functions.php';

checkAuth();

$game = getGameDetails(1); // Snake game ID = 1
$session_id = startGameSession($_SESSION['user_id'], 1);
$top_scores = getTopScores(1);

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
        </div>

        <div class="leaderboard">
            <h2>Top Scores</h2>
            <table>
                <tr>
                    <th>Player</th>
                    <th>Score</th>
                    <th>Date</th>
                </tr>
                <?php foreach ($top_scores as $score): ?>
                <tr>
                    <td><?php echo htmlspecialchars($score['username']); ?></td>
                    <td><?php echo htmlspecialchars($score['score']); ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($score['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        const session_id = <?php echo $session_id; ?>;
    </script>
    <script src="snake.js"></script>
</body>
</html>