<?php
session_start();
if (!isset($_SESSION['user_id'])) {
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
    <script src="snake.js" defer></script> <!-- Link to the existing snake.js file -->
</head>
<body>
    <h1>Snake Game</h1>
    <div class="snake-game-container">
        <canvas id="snakeCanvas" width="400" height="400"></canvas> <!-- The canvas for the snake game -->
    </div>
    <a href="dashboard.php">Back to Dashboard</a> <!-- Link to navigate back to the dashboard -->
</body>
</html>
