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
    <title>Snake Game</title>
    <link rel="stylesheet" href="static/style.css">
    <script src="snake.js" defer></script>
</head>
<body>
    <div class="snake-game-container">
        <canvas id="snakeCanvas" width="400" height="400"></canvas>
    </div>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
