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
    <title>Dashboard</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <nav>
            <a href="index.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
        <div class="game-container">
            <div class="game">
                <h3>Snake</h3>
                <a href="snake.php"><img src="static/Images/Snake.jpg" alt="Snake Game"></a>
            </div>
 <div class="game">
                <h3>Tic Tac Toe</h3>
                <a href="tic_tac_toe.php"><img src="static/Images/TicTacToe.jpg" alt="Tic Tac Toe Game"></a>
            </div>
            <!-- Add more games here -->
        </div>
    </div>
</body>
</html>