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
    <canvas id="snakeCanvas" width="400" height="400" style="border:1px solid black;"></canvas>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
