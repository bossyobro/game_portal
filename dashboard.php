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
        <div class="game-container">
            <div class="game">
                <h3>Snake</h3>
                <a href="snake.php"><img src="static/snake.png" alt="Snake Game"></a>
            </div>
            <div class="game">
                <h3>Tic Tac Toe</h3>
                <a href="tictactoe.php"><img src="static/tictactoe.png" alt="Tic Tac Toe Game"></a>
            </div>
            <!-- Add more games here -->
        </div>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
