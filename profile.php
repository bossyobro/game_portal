<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user data from the database
require 'db.php';
$conn = getDbConnection();
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="container">
        <h1>Your Profile</h1>
        <p>Username: <?php echo htmlspecialchars($user['username']); ?></p>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        
        <h2>Your Scores</h2>
        <table>
            <tr>
                <th>Game</th>
                <th>Score</th>
            </tr>
            <?php
            // Fetch user scores
            $stmt = $conn->prepare("SELECT game_name, score FROM scores JOIN games ON scores.game_id = games.id WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            while ($score = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($score['game_name']); ?></td>
                    <td><?php echo htmlspecialchars($score['score']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>
</form>
</body>
</html>