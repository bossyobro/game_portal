<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['authenticated'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake Game</title>
    <link rel="stylesheet" href="static/style.css">
    <script src="snake.js" defer></script> 
</head>
<body>
    <div class="game-wrapper">
        <h1>Snake Game</h1>
        <div class="snake-game-container">
            <canvas id="snakeCanvas" width="400" height="400"></canvas>
            <div id="score">Score: 0</div>
        </div>
        <div class="game-controls">
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>