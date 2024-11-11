<?php
session_start();
require_once 'auth.php';
require_once 'db.php';
require_once 'game_functions.php';


checkAuth(); 

$game = getGameDetails(2); // Tic Tac Toe game ID = 2

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tic Tac Toe</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="game-wrapper">
        <h1><?php echo htmlspecialchars($game['name']); ?></h1>
        <p><?php echo htmlspecialchars($game['description']); ?></p>
        
        <div class="tictactoe-game-container">
            <div id="tictactoeBoard"></div>
            <div id="result"></div>
        </div>

        <div class="game-controls">
            <button onclick="startNewGame()">Start New Game</button>
        </div>

        <!-- Removed the leaderboard section completely -->
    </div>

    <script>
        // No session_id needed
    </script>
    <script src="tictactoe.js"></script>
</body>
</html>