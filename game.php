<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $guess = $_POST['guess'];
    $number = rand(1, 10);
    $score = ($guess == $number) ? 10 : 0;

    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO scores (user_id, score) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $score]);

    $message = $score ? "Correct! You've earned 10 points." : "Wrong! The number was $number.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Game</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="game-container">
        <h2>Guess the Number (1-10)</h2>
        <?php if ($message) echo "<p>$message</p>"; ?>
        <form method="POST">
            <input type="number" name="guess" min="1" max="10" required>
            <button type="submit">Submit Guess</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
