<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Portal - Your Gateway to Fun</title>
    <link rel="stylesheet" href="static/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="logo">
                <img src="static/Images/Game_Portal_Logo.jpg" alt="Game Portal Logo">
                <span>Game Portal</span>
            </div>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="games.php">Games</a></li>
                <li><a href="leaderboard.php">Leaderboard</a></li>
                <li><a href="about.php">About</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>Welcome to Game Portal</h1>
            <p>Your gateway to endless fun and excitement!</p>
        </section>

        <section class="featured-games">
            <h2>Featured Games</h2>
            <div class="game-grid">
                <div class="game-card">
                    <img src="static/Images/Snake.jpg" alt="Snake Game">
                    <h3>Snake</h3>
                    <p>Classic snake game with a modern twist!</p>
                    <a href="snake.html" class="btn">Play Now</a>
                </div>
                <div class="game-card">
                    <img src="static/Images/TicTacToe.png" alt="Tic Tac Toe Game">
                    <h3>Tic Tac Toe</h3>
                    <p>Challenge your friends or AI in this timeless game!</p>
                    <a href="tictactoe.php" class="btn">Play Now</a>
                </div>
                <!-- Add more game cards as needed -->
            </div>
        </section>

        <section class="about-us">
            <h2>About Game Portal</h2>
            <p>Game Portal is your one-stop destination for online gaming fun. We offer a variety of games to suit all tastes, from classic puzzles to modern multiplayer experiences. Join our community today and start your gaming adventure!</p>
        </section>
    </main>

    <footer>
        <p>&copy; 2024-2025 Game Portal. All rights reserved.</p>
        <div class="social-links">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
    </footer>
</body>
</html>