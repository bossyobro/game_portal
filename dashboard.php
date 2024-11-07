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
    <title>Dashboard</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
            <nav class="dashboard-nav">
                <a href="index.php">Home</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <div class="game-container">
            <h3>Featured Games</h3>
            <div class="game-grid">
                <div class="game-card">
                    <h4>Snake</h4>
                    <a href="snake.html"><img src="static/Images/Snake.jpg" alt="Snake Game"></a>
                </div>
                <div class="game-card">
                    <h4>Tic Tac Toe</h4>
                    <a href="tictactoe.php"><img src="static/Images/TicTacToe.png" alt="Tic Tac Toe Game"></a>
                </div>
                <!-- Add more games here -->
            </div>
        </div>
    </div>
</body>
</html>