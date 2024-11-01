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
    <title>Tic Tac Toe</title>
    <link rel="stylesheet" href="static/style.css"> <!-- Link to your CSS file -->
    <script src="static/tictactoe.js" defer></script> <!-- Link to your JS file -->
</head>
<body>
    <div class="game-container">
        <h1>Tic Tac Toe</h1>
        <div class="board" id="gameBoard">
            <div class="cell" data-index="0"></div>
            <div class="cell" data-index="1"></div>
            <div class="cell" data-index="2"></div>
            <div class="cell" data-index="3"></div>
            <div class="cell" data-index="4"></div>
            <div class="cell" data-index="5"></div>
            <div class="cell" data-index="6"></div>
            <div class="cell" data-index="7"></div>
            <div class="cell" data-index="8"></div>
        </div>
        <button id="reset">Reset Game</button>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
